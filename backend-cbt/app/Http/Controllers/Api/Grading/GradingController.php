<?php

namespace App\Http\Controllers\Api\Grading;

use App\Http\Controllers\Api\Controller;
use App\Models\Answer;
use App\Models\ExamAttempt;
use App\Models\ExamResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradingController extends Controller
{
    /**
     * Display grading list.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ExamAttempt::with(['user', 'exam', 'result'])
            ->where('status', 'completed');

        if ($request->has('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        if ($request->has('needs_grading')) {
            $query->whereHas('answers', function ($q) {
                $q->whereNull('score');
            });
        }

        $attempts = $query->orderBy('submitted_at', 'desc')->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($attempts);
    }

    /**
     * Show attempt for grading.
     */
    public function show(string $attemptId): JsonResponse
    {
        $attempt = ExamAttempt::with(['user', 'exam', 'answers.question', 'answers.selectedOption', 'result'])
            ->findOrFail($attemptId);

        return $this->successResponse($attempt);
    }

    /**
     * Grade an answer.
     */
    public function grade(Request $request, string $attemptId): JsonResponse
    {
        $request->validate([
            'answer_id' => 'required|exists:answers,id',
            'score' => 'required|numeric|min:0',
            'feedback' => 'nullable|string',
        ]);

        $answer = Answer::where('id', $request->answer_id)
            ->where('attempt_id', $attemptId)
            ->first();

        if (! $answer) {
            return $this->errorResponse('Answer not found', 404);
        }

        $answer->update([
            'score' => $request->score,
            'feedback' => $request->feedback,
            'graded_by' => Auth::id(),
            'graded_at' => now(),
        ]);

        return $this->successResponse($answer, 'Answer graded successfully');
    }

    /**
     * Finalize grading for an attempt.
     */
    public function finalize(string $attemptId): JsonResponse
    {
        $attempt = ExamAttempt::findOrFail($attemptId);

        // Calculate total score from manually graded answers
        $totalScore = Answer::where('attempt_id', $attemptId)
            ->sum('score');

        $maxScore = $attempt->exam->questions()->sum('score');

        $percentage = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;

        // Update or create result
        ExamResult::updateOrCreate(
            ['attempt_id' => $attemptId],
            [
                'score' => round($percentage, 2),
                'manual_score' => $totalScore,
                'auto_graded' => false,
                'graded_by' => Auth::id(),
                'graded_at' => now(),
            ]
        );

        $attempt->update(['status' => 'graded']);

        return $this->successResponse([
            'attempt' => $attempt->fresh(),
            'result' => $attempt->result,
        ], 'Grading finalized');
    }
}
