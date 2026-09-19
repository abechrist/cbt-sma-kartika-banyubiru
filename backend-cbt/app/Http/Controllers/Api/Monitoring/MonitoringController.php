<?php

namespace App\Http\Controllers\Api\Monitoring;

use App\Http\Controllers\Api\Controller;
use App\Models\ExamAttempt;
use App\Models\ExamSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    /**
     * Display monitoring dashboard.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ExamSession::with(['exam', 'attempts.user']);

        if ($request->has('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $sessions = $query->orderBy('scheduled_start', 'desc')->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($sessions);
    }

    /**
     * Show session monitoring details.
     */
    public function session(string $sessionId): JsonResponse
    {
        $session = ExamSession::with(['exam', 'attempts.user', 'attempts.answers'])
            ->findOrFail($sessionId);

        $stats = [
            'total_participants' => $session->attempts->count(),
            'in_progress' => $session->attempts->where('status', 'in_progress')->count(),
            'completed' => $session->attempts->where('status', 'completed')->count(),
            'not_started' => $session->exam->eligibleStudents()->count() - $session->attempts->count(),
        ];

        return $this->successResponse([
            'session' => $session,
            'stats' => $stats,
            'attempts' => $session->attempts,
        ]);
    }

    /**
     * Reset a student's attempt.
     */
    public function resetAttempt(Request $request): JsonResponse
    {
        $request->validate([
            'attempt_id' => 'required|exists:exam_attempts,id',
            'reason' => 'required|string',
        ]);

        $attempt = ExamAttempt::findOrFail($request->attempt_id);

        // Delete associated answers
        $attempt->answers()->delete();

        // Reset attempt status
        $attempt->update([
            'status' => 'pending',
            'started_at' => null,
            'submitted_at' => null,
            'last_activity_at' => null,
        ]);

        // Delete result if exists
        $attempt->result()->delete();

        return $this->successResponse([
            'attempt' => $attempt->fresh(),
        ], 'Attempt reset successfully');
    }

    /**
     * Get real-time stats.
     */
    public function stats(Request $request): JsonResponse
    {
        $stats = [
            'active_sessions' => ExamSession::where('status', 'active')->count(),
            'total_attempts_today' => ExamAttempt::whereDate('created_at', today())->count(),
            'in_progress' => ExamAttempt::where('status', 'in_progress')->count(),
            'completed_today' => ExamAttempt::where('status', 'completed')
                ->whereDate('submitted_at', today())
                ->count(),
        ];

        return $this->successResponse($stats);
    }
}
