<?php

namespace App\Filament\Student\Pages;

use App\Models\ExamAttempt;
use App\Models\ExamSession;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class StudentDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Dashboard Siswa';

    protected static string $view = 'filament.student.pages.dashboard';

    protected function getViewData(): array
    {
        $user = Auth::user();

        // Get upcoming exam sessions
        $upcomingSessions = ExamSession::with(['exam'])
            ->where('status', 'scheduled')
            ->where('start_at', '>', now())
            ->orderBy('start_at')
            ->limit(5)
            ->get();

        // Get active exam sessions (open or in progress)
        $activeSessions = ExamSession::with(['exam'])
            ->whereIn('status', ['open', 'in_progress'])
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->orderBy('start_at')
            ->get();

        // Get completed exams for this student
        $completedExams = ExamAttempt::with(['session.exam', 'result'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['submitted', 'auto_submitted'])
            ->orderBy('submitted_at', 'desc')
            ->limit(5)
            ->get();

        return [
            'user' => $user,
            'upcomingSessions' => $upcomingSessions,
            'activeSessions' => $activeSessions,
            'completedExams' => $completedExams,
        ];
    }
}
