<?php

namespace App\Http\Controllers\Api\Kiosk;

use App\Http\Controllers\Api\Controller;
use App\Models\ExamSession;
use Illuminate\Http\JsonResponse;

class KioskController extends Controller
{
    /**
     * Launch kiosk mode.
     */
    public function launch(): JsonResponse
    {
        $activeSessions = ExamSession::with(['exam.subject'])
            ->where('status', 'open')
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->get();

        return $this->successResponse([
            'active_sessions' => $activeSessions,
            'timestamp' => now()->toISOString(),
        ]);
    }
}
