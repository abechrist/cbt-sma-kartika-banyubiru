<?php

namespace App\Http\Controllers\Api;

use App\Models\Exam;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemAnalysisController extends Controller
{
    /**
     * Display item analysis listing.
     */
    public function index(Request $request): JsonResponse
    {
        $exams = Exam::with(['subject'])
            ->whereHas('questions')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($exams);
    }

    /**
     * Show item analysis for specific exam.
     */
    public function show(Exam $exam): JsonResponse
    {
        $questions = $exam->questions()->with(['options', 'answers'])->get();

        $analysis = $questions->map(function ($question) {
            $answers = $question->answers;
            $totalAnswers = $answers->count();

            if ($totalAnswers === 0) {
                return [
                    'question_id' => $question->id,
                    'content' => $question->content,
                    'difficulty_index' => 0,
                    'discrimination_index' => 0,
                    'total_answers' => 0,
                ];
            }

            // Calculate difficulty index (p-value)
            $correctAnswers = $answers->where('is_correct', true)->count();
            $difficultyIndex = $correctAnswers / $totalAnswers;

            // Calculate discrimination index (simplified)
            $upperGroup = $answers->take(intceil($totalAnswers * 0.27));
            $lowerGroup = $answers->take(-intceil($totalAnswers * 0.27));

            $upperCorrect = $upperGroup->where('is_correct', true)->count();
            $lowerCorrect = $lowerGroup->where('is_correct', true)->count();

            $upperCount = max($upperGroup->count(), 1);
            $lowerCount = max($lowerGroup->count(), 1);

            $discriminationIndex = ($upperCorrect / $upperCount) - ($lowerCorrect / $lowerCount);

            return [
                'question_id' => $question->id,
                'content' => $question->content,
                'type' => $question->type,
                'difficulty_index' => round($difficultyIndex, 3),
                'discrimination_index' => round($discriminationIndex, 3),
                'difficulty_level' => $this->getDifficultyLevel($difficultyIndex),
                'discrimination_level' => $this->getDiscriminationLevel($discriminationIndex),
                'total_answers' => $totalAnswers,
                'correct_answers' => $correctAnswers,
            ];
        });

        return $this->successResponse([
            'exam' => $exam->load('subject'),
            'questions' => $analysis,
            'summary' => [
                'average_difficulty' => $analysis->avg('difficulty_index'),
                'average_discrimination' => $analysis->avg('discrimination_index'),
                'good_questions' => $analysis->where('discrimination_level', 'Good')->count(),
                'revisable_questions' => $analysis->whereIn('discrimination_level', ['Fair', 'Poor'])->count(),
            ],
        ]);
    }

    /**
     * Get difficulty level label.
     */
    private function getDifficultyLevel(float $index): string
    {
        return match (true) {
            $index >= 0.7 => 'Easy',
            $index >= 0.4 => 'Medium',
            default => 'Hard',
        };
    }

    /**
     * Get discrimination level label.
     */
    private function getDiscriminationLevel(float $index): string
    {
        return match (true) {
            $index >= 0.4 => 'Good',
            $index >= 0.2 => 'Fair',
            default => 'Poor',
        };
    }
}
