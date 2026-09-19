<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamResult;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data = $this->getDashboardData($user);

        return view('dashboard', $data);
    }

    private function getDashboardData(User $user): array
    {
        $role = $user->role?->name ?? '';

        return $this->computeDashboardData($user, $role);
    }

    private function computeDashboardData(User $user, string $role): array
    {
        $data = ['user' => $user, 'role' => $role];

        switch ($role) {
            case User::ROLE_SUPER_ADMIN:
            case User::ROLE_ADMIN:
                $data['totalUsers'] = User::count();
                $data['totalStudents'] = User::whereHas('role', fn ($q) => $q->where('name', User::ROLE_SISWA))->count();
                $data['totalTeachers'] = User::whereHas('role', fn ($q) => $q->where('name', User::ROLE_GURU))->count();
                $data['totalClasses'] = StudentClass::count();
                $data['totalSubjects'] = Subject::count();
                $data['totalQuestions'] = Question::count();
                $data['totalExams'] = Exam::count();
                $data['activeSessions'] = ExamSession::whereIn('status', [ExamSession::STATUS_OPEN, ExamSession::STATUS_IN_PROGRESS])->count();
                $data['recentSessions'] = ExamSession::with('exam')->latest()->limit(5)->get();
                break;

            case User::ROLE_GURU:
                $data['myQuestions'] = Question::where('created_by', $user->id)->count();
                $data['myExams'] = Exam::where('created_by', $user->id)->count();
                $data['mySessions'] = ExamSession::whereHas('exam', fn ($q) => $q->where('created_by', $user->id))->latest()->limit(5)->get();
                $data['pendingGrading'] = ExamResult::where('grading_status', 'partial')
                    ->whereHas('attempt.session.exam', fn ($q) => $q->where('created_by', $user->id))
                    ->count();
                break;

            case User::ROLE_KEPALA_SEKOLAH:
                $data['totalExams'] = Exam::count();
                $data['totalStudents'] = User::whereHas('role', fn ($q) => $q->where('name', User::ROLE_SISWA))->count();
                $data['totalClasses'] = StudentClass::count();
                $data['totalSessions'] = ExamSession::count();
                $data['recentSessions'] = ExamSession::with('exam')->latest()->limit(5)->get();
                $data['sessionStatuses'] = ExamSession::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
                $data['participants'] = ExamAttempt::count();
                $data['avgPercentage'] = (float) ExamResult::avg('percentage');
                $data['gradeDistribution'] = ExamResult::selectRaw(
                    'CASE
                         WHEN percentage >= 90 THEN "A"
                         WHEN percentage >= 80 THEN "B"
                         WHEN percentage >= 70 THEN "C"
                         WHEN percentage >= 60 THEN "D"
                         ELSE "E"
                     END as band, count(*) as total'
                )->groupBy('band')->pluck('total', 'band');
                $data['resultsCompleted'] = ExamResult::count();
                $data['studentScoreStats'] = ExamAttempt::with('result')
                    ->whereHas('result')
                    ->with('user')->get()->groupBy('user.class_id')
                    ->map(fn ($attempts) => (int) round($attempts->avg(fn ($a) => $a->result->percentage)))
                    ->sortByDesc(fn ($avg) => $avg);
                break;

            case User::ROLE_SISWA:
                $data['availableSessions'] = ExamSession::where('status', ExamSession::STATUS_OPEN)
                    ->where('start_at', '<=', now())
                    ->where('end_at', '>=', now())
                    ->with(['exam.subject'])
                    ->get();
                $data['myAttempts'] = ExamAttempt::where('user_id', $user->id)
                    ->with(['session.exam.subject', 'result'])
                    ->latest()
                    ->limit(5)
                    ->get();
                break;

            case User::ROLE_PROKTOR:
                $data['todaySessions'] = ExamSession::whereDate('start_at', today())->get();
                break;

            case User::ROLE_WALI_KELAS:
                $class = $user->class;
                $data['myClass'] = $class;
                $data['classStudents'] = $class ? User::where('class_id', $class->id)->get() : collect();
                $data['classAttempts'] = $class
                    ? ExamAttempt::whereHas('user', fn ($q) => $q->where('class_id', $class->id))->with(['result', 'user'])->get()
                    : collect();
                $data['classAverage'] = $class ? round($data['classAttempts']->filter(fn ($a) => $a->result)->avg(fn ($a) => $a->result->percentage)) : null;
                $data['classParticipants'] = $data['classAttempts']->count();
                $data['classSchedule'] = $class ? ExamSession::with('exam')->latest()->limit(5)->get() : collect();
                break;
        }

        return $data;
    }
}
