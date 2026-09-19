<?php

namespace App\Http\Controllers\Api\Examination;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\SaveAnswerRequest;
use App\Http\Requests\Api\SubmitExamRequest;
use App\Http\Requests\Api\ValidateTokenRequest;
use App\Jobs\FinalizeExamResult;
use App\Models\ExamAttempt;
use App\Models\ExamToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentExamController extends Controller
{
    /**
     * Validate an exam token (by token text + session id).
     */
    public function validateToken(ValidateTokenRequest $request): JsonResponse
    {
        $examToken = ExamToken::with(['session.exam'])
            ->where('token', $request->token)
            ->where('exam_session_id', $request->session_id)
            ->first();

        if (! $examToken) {
            return $this->errorResponse('Token tidak dikenal.', 404);
        }

        if (! $examToken->isValid()) {
            return $this->errorResponse('Token ujian tidak aktif atau kedaluwarsa.', 422);
        }

        $session = $examToken->session;

        if (! $session->isAccessible()) {
            return $this->errorResponse('Sesi ujian belum dibuka atau telah ditutup.', 422);
        }

        return $this->successResponse([
            'valid' => true,
            'token_id' => $examToken->id,
            'session' => [
                'id' => $session->id,
                'exam_id' => $session->exam_id,
                'exam_title' => $session->exam->name,
                'duration_minutes' => $session->exam->duration_minutes,
                'start_at' => $session->start_at,
                'end_at' => $session->end_at,
            ],
        ], 'Token valid.');
    }

    /**
     * Start (or resume) an exam attempt using a validated token.
     */
    public function startExam(Request $request): JsonResponse
    {
        $request->validate([
            'token_id' => 'required|exists:exam_tokens,id',
            'session_id' => 'required|exists:exam_sessions,id',
        ]);

        $user = Auth::user();
        $examToken = ExamToken::with('session.exam')->findOrFail($request->token_id);

        if ($examToken->exam_session_id != $request->session_id) {
            return $this->errorResponse('Token tidak berlaku untuk sesi ini.', 422);
        }

        if (! $examToken->isValid()) {
            return $this->errorResponse('Token tidak valid atau kedaluwarsa.', 422);
        }

        $session = $examToken->session;

        $attempt = DB::transaction(function () use ($user, $session, $examToken) {
            $attempt = ExamAttempt::firstOrCreate(
                [
                    'exam_session_id' => $session->id,
                    'user_id' => $user->id,
                ],
                [
                    'status' => ExamAttempt::STATUS_IN_PROGRESS,
                    'started_at' => now(),
                    'ended_at' => $session->end_at,
                    'last_seen_at' => now(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]
            );

            if ($attempt->wasRecentlyCreated || $attempt->status === ExamAttempt::STATUS_NOT_STARTED) {
                $attempt->update([
                    'status' => ExamAttempt::STATUS_IN_PROGRESS,
                    'started_at' => now(),
                    'ended_at' => $session->end_at,
                    'last_seen_at' => now(),
                    'is_resumed' => false,
                ]);
            } elseif ($attempt->status === ExamAttempt::STATUS_IN_PROGRESS) {
                $attempt->update([
                    'ended_at' => $session->end_at,
                    'last_seen_at' => now(),
                    'is_resumed' => true,
                ]);
            }

            $examToken->markUsed();

            return $attempt;
        });

        // Load questions (with randomized order/options if configured).
        $questions = $session->exam->questions()->with('options')->get();

        if ($session->exam->randomize_questions) {
            $questions = $questions->shuffle()->values();
        }

        if ($session->exam->randomize_options) {
            $questions = $questions->map(function ($question) {
                $question->options = $question->options->shuffle()->values();

                return $question;
            });
        }

        $answers = $attempt->answers()->get();

        return $this->successResponse([
            'attempt' => $attempt->fresh(),
            'questions' => $questions,
            'answers' => $answers,
            'duration_minutes' => $session->exam->duration_minutes,
            'ends_at' => $attempt->ended_at?->toIso8601String(),
            'time_remaining' => $attempt->getTimeRemaining(),
        ], 'Ujian dimulai.');
    }

    /**
     * Save an answer for a question.
     */
    public function saveAnswer(SaveAnswerRequest $request): JsonResponse
    {
        $user = Auth::user();
        $attempt = ExamAttempt::findOrFail($request->attempt_id);

        if ($attempt->user_id !== $user->id) {
            return $this->errorResponse('Tidak memiliki akses.', 403);
        }

        if ($attempt->status !== ExamAttempt::STATUS_IN_PROGRESS) {
            return $this->errorResponse('Ujian tidak dalam status berlangsung.', 403);
        }

        $attempt->answers()->updateOrCreate(
            ['question_id' => $request->question_id],
            [
                'answer_text' => $request->answer_text,
                'selected_options' => $request->selected_options ?? [],
                'is_flagged' => $request->boolean('is_flagged'),
                'answered_at' => now(),
            ]
        );

        $attempt->update(['last_seen_at' => now()]);

        return $this->successResponse([
            'time_remaining' => $attempt->getTimeRemaining(),
        ], 'Jawaban disimpan.');
    }

    /**
     * Heartbeat — updates last_seen and returns server-authoritative timer.
     */
    public function heartbeat(Request $request): JsonResponse
    {
        $request->validate([
            'attempt_id' => 'required|exists:exam_attempts,id',
        ]);

        $user = Auth::user();
        $attempt = ExamAttempt::findOrFail($request->attempt_id);

        if ($attempt->user_id !== $user->id) {
            return $this->errorResponse('Tidak memiliki akses.', 403);
        }

        if ($attempt->status === ExamAttempt::STATUS_IN_PROGRESS) {
            $attempt->update(['last_seen_at' => now()]);
        }

        return $this->successResponse([
            'server_time' => now()->toIso8601String(),
            'time_remaining' => $attempt->getTimeRemaining(),
            'status' => $attempt->status,
        ]);
    }

    /**
     * Get attempt status (attempt + answers).
     */
    public function getAttemptStatus(Request $request, string $attemptId): JsonResponse
    {
        $user = Auth::user();

        $attempt = ExamAttempt::where('id', $attemptId)
            ->where('user_id', $user->id)
            ->with(['session.exam'])
            ->first();

        if (! $attempt) {
            return $this->errorResponse('Attempt tidak ditemukan.', 404);
        }

        $questions = $attempt->session->exam->questions()->with('options')->get();
        $answers = $attempt->answers()->get();

        return $this->successResponse([
            'attempt' => $attempt,
            'questions' => $questions,
            'answers' => $answers,
            'total_questions' => $questions->count(),
            'answered_questions' => $answers->count(),
            'time_remaining' => $attempt->getTimeRemaining(),
            'status' => $attempt->status,
        ]);
    }

    /**
     * Submit (finalize) an exam attempt.
     */
    public function submitExam(SubmitExamRequest $request): JsonResponse
    {
        $user = Auth::user();
        $attempt = ExamAttempt::findOrFail($request->attempt_id);

        if ($attempt->user_id !== $user->id) {
            return $this->errorResponse('Tidak memiliki akses.', 403);
        }

        if ($attempt->status === ExamAttempt::STATUS_CANCELLED) {
            return $this->errorResponse('Ujian telah dibatalkan.', 403);
        }

        DB::transaction(function () use ($attempt) {
            $attempt->update([
                'status' => ExamAttempt::STATUS_SUBMITTED,
                'submitted_at' => now(),
                'ended_at' => now(),
            ]);

            // Compute result synchronously so the student sees it right away.
            $this->calculateResult($attempt);
        });

        $attempt->refresh();
        $attempt->load(['result']);

        // Reconcile in the background (same as web flow).
        FinalizeExamResult::dispatch($attempt->id);

        return $this->successResponse([
            'attempt' => $attempt->fresh(['result']),
            'result' => $attempt->result,
        ], 'Ujian dikumpulkan.');
    }

    /**
     * Get exam result.
     */
    public function getResult(Request $request, string $attemptId): JsonResponse
    {
        $user = Auth::user();

        $attempt = ExamAttempt::where('id', $attemptId)
            ->where('user_id', $user->id)
            ->with(['result', 'session.exam'])
            ->first();

        if (! $attempt) {
            return $this->errorResponse('Attempt tidak ditemukan.', 404);
        }

        if (! $attempt->isCompleted()) {
            return $this->errorResponse('Ujian belum selesai.', 422);
        }

        return $this->successResponse([
            'attempt' => $attempt,
            'result' => $attempt->result,
            'answers' => $attempt->answers()->with('question.options')->get(),
            'exam' => $attempt->session->exam,
        ], 'Hasil ujian.');
    }

    /**
     * Compute and persist the exam result (created + submitted attempt).
     * Mirrors FinalizeExamResult for synchronous feedback.
     */
    protected function calculateResult(ExamAttempt $attempt): void
    {
        $exam = $attempt->session->exam;

        $totalScore = 0;
        $maxScore = 0;
        $correctCount = 0;
        $incorrectCount = 0;
        $unansweredCount = 0;
        $pendingEsaiCount = 0;

        foreach ($exam->questions as $question) {
            $maxScore += $question->pivot->score ?? $question->score;

            $answer = $attempt->answers()->where('question_id', $question->id)->first();

            if (! $answer) {
                $unansweredCount++;

                continue;
            }

            if ($question->isAutoGradable()) {
                $isCorrect = $answer->isCorrect();
                $score = $isCorrect ? ($question->pivot->score ?? $question->score) : 0;

                $answer->update(['score' => $score]);

                if ($isCorrect) {
                    $correctCount++;
                    $totalScore += $score;
                } else {
                    $incorrectCount++;
                }
            } else {
                if (($answer->score ?? null) !== null) {
                    $totalScore += $answer->score;
                } else {
                    $pendingEsaiCount++;
                }
            }
        }

        $percentage = $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0;

        $attempt->result()->updateOrCreate(
            ['exam_attempt_id' => $attempt->id],
            [
                'total_score' => $totalScore,
                'max_possible_score' => $maxScore,
                'percentage' => $percentage,
                'correct_count' => $correctCount,
                'incorrect_count' => $incorrectCount,
                'unanswered_count' => $unansweredCount + $pendingEsaiCount,
                'grading_status' => $pendingEsaiCount > 0 ? 'partial' : 'completed',
            ]
        );
    }
}
