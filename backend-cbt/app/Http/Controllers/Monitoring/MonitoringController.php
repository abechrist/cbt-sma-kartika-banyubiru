<?php

namespace App\Http\Controllers\Monitoring;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\ExamSession;

class MonitoringController extends Controller
{
    public function index()
    {
        $sessions = ExamSession::whereIn('status', ['open', 'in_progress'])
            ->with(['exam', 'attempts.user'])
            ->get();

        return view('monitoring.index', compact('sessions'));
    }

    public function session(ExamSession $session)
    {
        $session->load(['exam', 'attempts.user', 'attempts.answers', 'attempts.activityLogs']);

        $participants = $session->attempts->map(function ($attempt) {
            $isOnline = $attempt->last_seen_at !== null
                && $attempt->last_seen_at->greaterThanOrEqualTo(now()->subSeconds(60));

            return [
                'attempt_id' => $attempt->id,
                'user_id' => $attempt->user_id,
                'student_name' => $attempt->user->name,
                'status' => $attempt->status,
                'progress' => $attempt->getProgressPercentage(),
                'answers_count' => $attempt->answers->count(),
                'started_at' => $attempt->started_at?->toIso8601String(),
                'submitted_at' => $attempt->submitted_at?->toIso8601String(),
                'last_seen_at' => $attempt->last_seen_at?->toIso8601String(),
                'online' => $isOnline && $attempt->status === 'in_progress',
                'suspicious_flags' => $attempt->suspicious_flags,
                'time_remaining' => $attempt->getTimeRemaining(),
            ];
        });

        $activityLogs = $session->attempts->flatMap(fn ($attempt) => $attempt->activityLogs)
            ->sortByDesc('created_at')
            ->take(100);

        $reverbConfig = [
            'appKey' => config('broadcasting.connections.reverb.key'),
            'host' => config('reverb.servers.reverb.hostname', '127.0.0.1'),
            'port' => (int) config('reverb.servers.reverb.port', 8080),
            'scheme' => config('reverb.servers.reverb.scheme', 'http'),
        ];

        // Pusher config untuk production broadcast (pakai service Pusher).
        $pusherKey = config('broadcasting.connections.pusher.key');

        return view('monitoring.session', compact(
            'session',
            'participants',
            'activityLogs',
            'reverbConfig',
            'pusherKey'
        ));
    }

    public function resetAttempt(ExamAttempt $attempt)
    {
        $attempt->update([
            'status' => 'not_started',
            'started_at' => null,
            'ended_at' => null,
            'submitted_at' => null,
            'current_question_order' => 0,
            'is_resumed' => false,
        ]);

        $attempt->answers()->delete();

        return back()->with('success', 'Percobaan ujian berhasil di-reset.');
    }
}
