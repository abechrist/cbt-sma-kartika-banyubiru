<?php

namespace App\Http\Controllers\Result;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;

class ItemAnalysisController extends Controller
{
    public function index(Request $request)
    {
        $exams = Exam::with(['subject', 'sessions'])->withCount('questions')->orderBy('name')->get();

        return view('item_analysis.index', compact('exams'));
    }

    public function show(Request $request, Exam $exam)
    {
        $questionFilter = $request->input('question_type');
        $subjectFilter = $request->input('subject_id');

        $sessions = $exam->sessions()->withCount('attempts')->get();
        $attempts = ExamAttempt::whereIn('exam_session_id', $sessions->pluck('id'))
            ->where('status', 'submitted')
            ->with('answers')
            ->with('result')
            ->get();

        $totalAttempts = $attempts->count();
        $analysis = [];

        $questions = $exam->questions;
        if ($questionFilter) {
            $questions = $questions->where('type', $questionFilter);
        }

        // Sort attempts by total score (highest = top group)
        $ranked = $attempts->sortByDesc(fn ($a) => (float) optional($a->result)->percentage)->values();
        $n = $ranked->count();
        $upperCount = max(1, (int) round($n * 0.27));
        $lowerCount = max(1, (int) round($n * 0.27));
        $upperGroup = $ranked->take($upperCount)->pluck('id')->flip();
        $lowerGroup = $n ? $ranked->slice(-$lowerCount)->pluck('id')->flip() : collect();

        foreach ($questions as $question) {
            $isAuto = $question->isAutoGradable();
            $correct = 0;
            $incorrect = 0;
            $unanswered = 0;
            $totalScoreGiven = 0.0;
            $upperCorrect = 0;
            $lowerCorrectCount = 0;

            // answer distribution per option
            $optionDist = $question->options->mapWithKeys(fn ($o) => [(string) $o->id => 0]);
            $optionDist['unanswered'] = 0;

            foreach ($attempts as $attempt) {
                $answer = $attempt->answers->firstWhere('question_id', $question->id);

                if (! $answer) {
                    $unanswered++;
                    $optionDist->put('unanswered', $optionDist->get('unanswered') + 1);

                    continue;
                }

                if ($isAuto) {
                    $isRight = $answer->isCorrect();
                    if ($isRight) {
                        $correct++;
                        $upperCorrect += $upperGroup->has($attempt->id) ? 1 : 0;
                        $lowerCorrectCount += $lowerGroup->has($attempt->id) ? 1 : 0;
                    } else {
                        $incorrect++;
                    }
                    foreach ((array) $answer->selected_options as $sel) {
                        if ($optionDist->has((string) $sel)) {
                            $optionDist->put((string) $sel, $optionDist->get((string) $sel) + 1);
                        }
                    }
                } else {
                    $sc = (float) $answer->score;
                    $totalScoreGiven += $sc;
                    if ($sc > 0) {
                        $upperCorrect += $upperGroup->has($attempt->id) ? 1 : 0;
                        $lowerCorrectCount += $lowerGroup->has($attempt->id) ? 1 : 0;
                    }
                }
            }

            if ($isAuto) {
                $difficulty = $totalAttempts ? $correct / $totalAttempts : 0;
            } else {
                $qScore = (float) $question->score;
                $difficulty = ($totalAttempts && $qScore > 0) ? $totalScoreGiven / ($totalAttempts * $qScore) : 0;
            }

            $discrimination = 0;
            if ($upperGroup->count() && $lowerGroup->count()) {
                $discrimination = ($upperCorrect / $upperGroup->count()) - ($lowerCorrectCount / $lowerGroup->count());
            }

            $analysis[] = [
                'question' => $question,
                'auto_gradable' => $isAuto,
                'total_answers' => $correct + $incorrect,
                'correct' => $correct,
                'incorrect' => $incorrect,
                'unanswered' => $unanswered,
                'difficulty' => round($difficulty, 3),
                'discrimination' => round($discrimination, 3),
                'option_dist' => $optionDist,
                'max_score' => $question->score,
            ];
        }

        return view('item_analysis.show', compact(
            'exam', 'totalAttempts', 'analysis', 'questionFilter', 'subjectFilter',
        ));
    }
}
