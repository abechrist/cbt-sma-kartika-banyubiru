<?php

namespace App\Http\Controllers\Grading;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamResult;
use App\Models\Question;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradingController extends Controller
{
    public function index(Request $request)
    {
        $query = ExamResult::where('grading_status', 'partial')
            ->with(['attempt.user', 'attempt.session.exam.subject']);

        if ($request->filled('exam_id')) {
            $query->whereHas('attempt.session', fn ($q) => $q->where('exam_id', $request->exam_id));
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('attempt.user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $results = $query->latest()->paginate(15)->withQueryString();
        $exams = Exam::all();

        return view('grading.index', compact('results', 'exams'));
    }

    public function grade(ExamResult $result)
    {
        $this->authorizeGrade($result->attempt);

        $result->load([
            'attempt.user.class',
            'attempt.session.exam.subject',
            'attempt.answers.question.options',
        ]);

        $answers = $result->attempt->answers
            ->filter(fn ($a) => $a->question && $a->question->type === Question::TYPE_ESAI);

        return view('grading.grade', compact('result', 'answers'));
    }

    public function update(Request $request, ExamResult $result)
    {
        $this->authorizeGrade($result->attempt);

        $validated = $request->validate([
            'scores' => ['required', 'array'],
            'scores.*' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($request, $result, $validated) {
            $maxScore = (float) $result->max_possible_score;

            foreach ($validated['scores'] as $answerId => $score) {
                $answer = Answer::with('question')->find($answerId);

                if (! $answer || ! $answer->question || $answer->question->type !== Question::TYPE_ESAI) {
                    continue;
                }

                if ($answer->exam_attempt_id !== $result->exam_attempt_id) {
                    continue;
                }

                $score = min((float) $score, (float) $answer->question->score);
                $answer->update([
                    'score' => $score,
                    'graded_by' => Auth::id(),
                    'graded_at' => now(),
                    'notes' => $request->notes[$answerId] ?? null,
                ]);
            }

            $this->recomputeResult($result, $maxScore);
            ActivityLogger::log(ActivityLog::ACTION_MANUAL_GRADED, 'Nilai esai dikoreksi manual.', $result->attempt);
        });

        return redirect()->route('grading.show', $result)->with('success', 'Nilai esai berhasil dikoreksi.');
    }

    public function show(ExamResult $result)
    {
        $this->authorizeView($result->attempt);
        $result->load(['attempt.user.class', 'attempt.session.exam.subject']);

        return view('grading.show', compact('result'));
    }

    public function regrade(ExamResult $result)
    {
        $this->authorizeGrade($result->attempt);
        $attempt = $result->attempt;

        DB::transaction(function () use ($attempt, $result) {
            $maxScore = 0;
            $totalScore = 0;
            $correct = 0;
            $incorrect = 0;
            $unanswered = 0;
            $pendingEsai = 0;

            foreach ($attempt->session->exam->questions as $question) {
                $maxScore += $question->pivot->score ?? $question->score;
                $answer = $attempt->answers()->where('question_id', $question->id)->first();

                if (! $answer) {
                    $unanswered++;

                    continue;
                }

                if ($question->isAutoGradable()) {
                    if ($answer->isCorrect()) {
                        $score = $question->pivot->score ?? $question->score;
                        $answer->update(['score' => $score]);
                        $correct++;
                        $totalScore += $score;
                    } else {
                        $answer->update(['score' => 0]);
                        $incorrect++;
                    }
                } else {
                    if (isset($answer->score)) {
                        $totalScore += $answer->score;
                    } else {
                        $pendingEsai++;
                    }
                }
            }

            $percentage = $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0;

            $result->update([
                'total_score' => $totalScore,
                'max_possible_score' => $maxScore,
                'percentage' => $percentage,
                'correct_count' => $correct,
                'incorrect_count' => $incorrect,
                'unanswered_count' => $unanswered + $pendingEsai,
                'grading_status' => $pendingEsai > 0 ? 'partial' : 'completed',
                'graded_at' => $pendingEsai === 0 ? now() : null,
            ]);

            ActivityLogger::log(ActivityLog::ACTION_MANUAL_GRADED, 'Nilai ulang dihitung ulang.', $attempt);
        });

        return redirect()->route('grading.show', $result)->with('success', 'Nilai dihitung ulang.');
    }

    private function recomputeResult(ExamResult $result, float $maxScore): void
    {
        $attempt = $result->attempt;
        $totalScore = 0;
        $correct = 0;
        $incorrect = 0;
        $unanswered = 0;
        $pendingEsai = 0;

        foreach ($attempt->session->exam->questions as $question) {
            $answer = $attempt->answers()->where('question_id', $question->id)->first();

            if (! $answer) {
                $unanswered++;

                continue;
            }

            if ($question->isAutoGradable()) {
                if ($answer->isCorrect()) {
                    $score = (float) ($question->pivot->score ?? $question->score);
                    $correct++;
                    $totalScore += $score;
                } elseif ($answer->selected_options || $answer->answer_text) {
                    $incorrect++;
                } else {
                    $unanswered++;
                }
            } else {
                if (isset($answer->score) && $answer->score !== null) {
                    $totalScore += (float) $answer->score;
                    if ((float) $answer->score > 0) {
                        $correct++;
                    }
                } else {
                    $pendingEsai++;
                }
            }
        }

        $percentage = $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0;

        $result->update([
            'total_score' => $totalScore,
            'max_possible_score' => $maxScore,
            'percentage' => $percentage,
            'correct_count' => $correct,
            'incorrect_count' => $incorrect,
            'unanswered_count' => $unanswered + $pendingEsai,
            'grading_status' => $pendingEsai > 0 ? 'partial' : 'completed',
            'graded_at' => $pendingEsai === 0 ? now() : null,
        ]);
    }

    private function authorizeGrade(ExamAttempt $attempt): void
    {
        $user = Auth::user();
        if ($user->isGuru() && $attempt->session->exam->created_by !== $user->id) {
            abort(403, 'Anda bukan pembuat ujian ini.');
        }
        if (! in_array($user->role?->name, [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN, User::ROLE_GURU])) {
            abort(403);
        }
    }

    private function authorizeView(ExamAttempt $attempt): void
    {
        $user = Auth::user();
        if (! in_array($user->role?->name, [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN, User::ROLE_GURU, User::ROLE_KEPALA_SEKOLAH, User::ROLE_WALI_KELAS])) {
            abort(403);
        }
    }
}
