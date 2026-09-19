<?php

namespace App\Events;

use App\Models\ActivityLog;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class ActivityLogged implements ShouldBroadcast
{
    use SerializesModels;

    public ActivityLog $log;

    public function __construct(ActivityLog $log)
    {
        $this->log = $log;
    }

    public function broadcastOn(): array
    {
        $session = $this->log->attempt?->exam_session_id;
        if ($session === null) {
            return [];
        }

        return [new PrivateChannel('monitoring.'.$session)];
    }

    public function broadcastAs(): string
    {
        return 'activity.logged';
    }

    public function broadcastWith(): array
    {
        return [
            'attempt_id' => $this->log->exam_attempt_id,
            'user_id' => $this->log->user_id,
            'student_name' => $this->log->attempt?->user?->name,
            'session_id' => $this->log->attempt?->exam_session_id,
            'action' => $this->log->action,
            'description' => $this->log->description,
            'suspicious' => $this->log->metadata['suspicious'] ?? false,
            'created_at' => $this->log->created_at?->toIso8601String(),
        ];
    }
}
