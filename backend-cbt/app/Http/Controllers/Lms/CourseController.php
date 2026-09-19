<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isGuru()) {
            $courses = Course::where('teacher_id', $user->id)
                ->with(['subject', 'studentClass', 'materials', 'assignments'])
                ->latest()
                ->get();
        } elseif ($user->isSiswa()) {
            $courses = Course::where('class_id', $user->class_id)
                ->where('is_active', true)
                ->with(['subject', 'teacher', 'materials', 'assignments'])
                ->get();
        } else {
            // Super Admin, Admin, Kepala Sekolah, Wali Kelas
            $courses = Course::with(['subject', 'studentClass', 'teacher', 'materials', 'assignments'])
                ->latest()
                ->get();
        }

        $classes = StudentClass::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $teachers = User::whereHas('role', fn ($q) => $q->where('name', User::ROLE_GURU))->get();

        return view('lms.courses.index', compact('courses', 'classes', 'subjects', 'teachers'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $course = Course::with([
            'subject',
            'studentClass.students',
            'teacher',
            'materials.progresses',
            'assignments.submissions',
            'discussions.user.role',
            'discussions.replies.user.role',
        ])->findOrFail($id);

        // Otorisasi: Siswa hanya dapat melihat kelasnya sendiri
        if ($user->isSiswa() && $course->class_id !== $user->class_id) {
            abort(403, 'Anda tidak terdaftar dalam kelas mata pelajaran ini.');
        }

        // Hitung statistik untuk Siswa
        $completedMaterialsCount = 0;
        if ($user->isSiswa()) {
            $completedMaterialsCount = $course->materials->filter(fn ($m) => $m->isCompletedBy($user->id))->count();
        }

        return view('lms.courses.show', compact('course', 'completedMaterialsCount'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (! $user->isGuru() && ! $user->isSuperAdmin() && ! $user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'academic_year' => 'required|string|max:20',
            'semester' => 'required|in:ganjil,genap',
            'description' => 'nullable|string',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        $teacherId = $user->isGuru() ? $user->id : ($validated['teacher_id'] ?? $user->id);

        Course::create([
            'subject_id' => $validated['subject_id'],
            'class_id' => $validated['class_id'],
            'teacher_id' => $teacherId,
            'academic_year' => $validated['academic_year'],
            'semester' => $validated['semester'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('lms.courses.index')->with('success', 'Kelas pembelajaran berhasil dibuka.');
    }
}
