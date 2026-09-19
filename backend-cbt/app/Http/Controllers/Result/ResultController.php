<?php

namespace App\Http\Controllers\Result;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamResult;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $query = ExamResult::with(['attempt.user', 'attempt.session.exam.subject']);

        if ($request->filled('exam_id')) {
            $query->whereHas('attempt.session', fn ($q) => $q->where('exam_id', $request->exam_id));
        }
        if ($request->filled('class_id')) {
            $query->whereHas('attempt.user', fn ($q) => $q->where('class_id', $request->class_id));
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('attempt.user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $results = $query->latest()->paginate(15);
        $exams = Exam::all();
        $classes = StudentClass::all();

        return view('results.index', compact('results', 'exams', 'classes'));
    }

    public function show(ExamResult $result)
    {
        $result->load(['attempt.user.class', 'attempt.session.exam.subject', 'attempt.answers.question.options']);

        return view('results.show', compact('result'));
    }

    public function byExam(Exam $exam)
    {
        $sessions = $exam->sessions()->with('attempts.user', 'attempts.result')->get();
        $allResults = ExamResult::whereHas('attempt.session', fn ($q) => $q->where('exam_id', $exam->id))
            ->with('attempt.user', 'attempt.session')
            ->get();

        $stats = [
            'total_participants' => $allResults->count(),
            'average_score' => $allResults->avg('percentage') ?? 0,
            'highest_score' => $allResults->max('percentage') ?? 0,
            'lowest_score' => $allResults->min('percentage') ?? 0,
            'passing_count' => $allResults->where('percentage', '>=', 75)->count(),
        ];

        return view('results.by-exam', compact('exam', 'sessions', 'allResults', 'stats'));
    }

    public function byClass(StudentClass $class)
    {
        $students = User::where('class_id', $class->id)->whereHas('role', fn ($q) => $q->where('name', User::ROLE_SISWA))->get();

        $results = ExamResult::whereHas('attempt.user', fn ($q) => $q->where('class_id', $class->id))
            ->with(['attempt.user', 'attempt.session.exam'])
            ->get();

        return view('results.by-class', compact('class', 'students', 'results'));
    }

    public function byStudent(User $student)
    {
        if (! Auth::user()->canAccessExam()) {
            abort(403);
        }

        if (Auth::user()->isSiswa() && $student->id !== Auth::id()) {
            abort(403);
        }

        $attempts = ExamAttempt::where('user_id', $student->id)
            ->with(['session.exam.subject', 'result'])
            ->latest()
            ->get();

        return view('results.by-student', compact('student', 'attempts'));
    }
}
