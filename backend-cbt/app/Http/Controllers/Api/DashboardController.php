<?php

namespace App\Http\Controllers\Api;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display dashboard based on user role.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $data = match ($user->role?->name) {
            'super_admin', 'admin' => $this->adminDashboard(),
            'guru' => $this->teacherDashboard(),
            'siswa' => $this->studentDashboard(),
            'proktor' => $this->proctorDashboard(),
            'kepala_sekolah', 'wali_kelas' => $this->leaderDashboard(),
            default => [],
        };

        return $this->successResponse($data);
    }

    /**
     * Admin dashboard data.
     */
    private function adminDashboard(): array
    {
        return [
            'total_users' => User::count(),
            'total_students' => User::whereHas('role', fn ($q) => $q->where('name', 'siswa'))->count(),
            'total_teachers' => User::whereHas('role', fn ($q) => $q->where('name', 'guru'))->count(),
            'total_subjects' => Subject::count(),
            'total_questions' => Question::count(),
            'total_exams' => Exam::count(),
            'active_sessions' => ExamSession::where('status', 'open')->count(),
            'total_attempts' => ExamAttempt::count(),
            'tka' => [
                'subjects' => Subject::where('code', 'like', 'TKA-%')->get(['id', 'name', 'code']),
                'exams' => Exam::whereHas('subject', fn ($q) => $q->where('code', 'like', 'TKA-%'))->count(),
                'questions' => Question::whereHas('subject', fn ($q) => $q->where('code', 'like', 'TKA-%'))->count(),
            ],
        ];
    }

    /**
     * Teacher dashboard data.
     */
    private function teacherDashboard(): array
    {
        $userId = Auth::id();

        return [
            'my_exams' => Exam::where('created_by', $userId)->count(),
            'my_questions' => Question::where('created_by', $userId)->count(),
            'pending_grading' => ExamAttempt::whereHas('exam', function ($q) use ($userId) {
                $q->where('created_by', $userId);
            })->where('status', 'completed')->count(),
        ];
    }

    /**
     * Student dashboard data.
     */
    private function studentDashboard(): array
    {
        $userId = Auth::id();

        return [
            'available_exams' => Exam::whereHas('sessions', function ($q) {
                $q->where('status', 'open');
            })->count(),
            'my_attempts' => ExamAttempt::where('user_id', $userId)->count(),
            'completed_exams' => ExamAttempt::where('user_id', $userId)
                ->where('status', 'submitted')
                ->count(),
            'active_sessions' => ExamSession::where('status', 'open')
                ->where('start_at', '<=', now())
                ->where('end_at', '>=', now())
                ->with(['exam.subject'])
                ->get()
                ->map(fn ($session) => [
                    'id' => $session->id,
                    'exam' => $session->exam,
                    'is_tka' => $session->exam?->isTka(),
                    'tka_subtest' => $session->exam?->tkaSubtest(),
                ]),
            'tka_subjects' => Subject::where('code', 'like', 'TKA-%')
                ->get(['id', 'name', 'code']),
            'tka_exams' => Exam::whereHas('subject', fn ($q) => $q->where('code', 'like', 'TKA-%'))
                ->count(),
            'results' => ExamAttempt::where('user_id', $userId)
                ->where('status', 'submitted')
                ->with(['session.exam', 'result'])
                ->latest()
                ->take(10)
                ->get()
                ->map(fn ($a) => [
                    'id' => $a->id,
                    'exam_title' => $a->session?->exam?->name,
                    'score' => $a->result?->total_score,
                    'max_score' => $a->result?->max_possible_score,
                    'percentage' => $a->result?->percentage,
                    'submitted_at' => $a->submitted_at,
                ]),
        ];
    }

    /**
     * Proctor dashboard data.
     */
    private function proctorDashboard(): array
    {
        return [
            'active_sessions' => ExamSession::where('status', 'open')->count(),
            'in_progress_attempts' => ExamAttempt::where('status', 'in_progress')->count(),
            'completed_today' => ExamAttempt::where('status', 'submitted')
                ->whereDate('submitted_at', today())
                ->count(),
        ];
    }

    /**
     * Leader dashboard data.
     */
    private function leaderDashboard(): array
    {
        return [
            'total_students' => User::where('role', 'siswa')->count(),
            'total_exams' => Exam::count(),
            'recent_results' => ExamAttempt::where('status', 'submitted')
                ->with(['user', 'session.exam', 'result'])
                ->latest()
                ->limit(10)
                ->get(),
            'average_score' => ExamAttempt::where('status', 'submitted')
                ->whereHas('result')
                ->get()
                ->avg(fn ($a) => $a->result?->percentage),
        ];
    }
}
