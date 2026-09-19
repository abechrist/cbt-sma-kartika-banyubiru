<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\ExamSession;
use Illuminate\Http\Request;

class KioskController extends Controller
{
    public function launch(Request $request)
    {
        $sessions = ExamSession::with(['exam'])
            ->whereIn('status', ['open', 'in_progress'])
            ->orderByDesc('id')
            ->get();

        $takeBaseUrl = route('exam.token');

        return view('kiosk.launch', compact('sessions', 'takeBaseUrl'));
    }

    public function show(Request $request, ExamAttempt $attempt)
    {
        $takeUrl = route('exam.take', $attempt).'?kiosk=1';

        return view('kiosk.show', compact('attempt', 'takeUrl'));
    }
}
