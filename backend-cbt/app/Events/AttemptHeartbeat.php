<?php

namespace App\Events;

use App\Models\ExamAttempt;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class AttemptHeartbeat implements ShouldBroadcast
{
    use SerializesModels;

    public ExamAttempt $attempt;

    public bool $afterCommit = true;

    public function __construct(ExamAttempt $attempt)
    {
        $this->attempt = $attempt;
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('monitoring.'.$this->attempt->exam_session_id)];
    }

    public function broadcastAs(): string
    {
        return 'attempts.heartbeat';
    }

    public function broadcastWith(): array
    {
        $attempt = $this->attempt;

        return [
            'attempt_id' => $attempt->id,
            'user_id' => $attempt->user_id,
            'student_name' => $attempt->user?->name,
            'session_id' => $attempt->exam_session_id,
            'status' => $attempt->status,
            'last_seen_at' => $attempt->last_seen_at?->toIso8601String(),
            'time_remaining' => $attempt->getTimeRemaining(),
        ];
    }
}
