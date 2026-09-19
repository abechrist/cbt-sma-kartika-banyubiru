<?php

namespace App\Jobs;

use App\Events\AttemptStatusUpdated;
use App\Models\ExamAttempt;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

/**
 * Recomputes and reconciles an ExamResult in the background.
 *
 * The synchronous submit() already computes the result immediately so the
 * student sees it right away; this job runs afterward (on the database queue
 * in production, synchronously in tests) to reconcile any answers that were
 * graded by a manual/auto path, mark the attempt fully completed, and refresh
 * the realtime monitoring channel.
 */
class FinalizeExamResult implements ShouldQueue
{
    use Queueable;

    public int $attemptId;

    public function __construct(int $attemptId)
    {
        $this->attemptId = $attemptId;
    }

    public function handle(): void
    {
        $attempt = ExamAttempt::with(['session.exam', 'user', 'result'])->find($this->attemptId);

        if (! $attempt) {
            return;
        }

        DB::transaction(function () use ($attempt) {
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

            if ($attempt->status !== 'submitted' && $attempt->status !== 'auto_submitted') {
                $attempt->update([
                    'status' => 'submitted',
                    'submitted_at' => now(),
                    'ended_at' => now(),
                ]);
            }

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
        });

        $attempt->refresh();
        $attempt->load('user');
        broadcast(new AttemptStatusUpdated($attempt));
    }
}
