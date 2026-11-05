<?php

namespace App\Http\Controllers\Curriculum;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\LearningMaterial;
use App\Models\Question;
use App\Models\Role;
use App\Models\Rpp;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CurriculumMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $teacherRoleIds = Role::whereIn('name', [
            User::ROLE_GURU,
            User::ROLE_KOORDINATOR_KURIKULUM,
        ])->pluck('id');

        // Ambil seluruh guru pengampu (termasuk guru yang menjabat Koordinator Kurikulum)
        $teachersQuery = User::with([
            'role',
            'subjects',
            'teachingCourses.studentClass',
            'teachingCourses.subject',
            'teachingCourses.materials',
            'teachingCourses.assignments',
            'rpps.subject',
            'rpps.classGroup',
        ])
            ->withCount([
                'teachingCourses',
                'rpps',
                'createdQuestions',
                'createdExams',
            ])
            ->whereIn('role_id', $teacherRoleIds)
            ->where(function ($q) {
                // Tampilkan seluruh guru mata pelajaran (atau akun koordinator yang mengampu mapel/kelas)
                $q->where('email', '!=', 'wakakur@kartika.sch.id')
                    ->orWhereHas('teachingCourses')
                    ->orWhereHas('subjects');
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $teachersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('subject_id')) {
            $subjectId = (int) $request->subject_id;
            $teachersQuery->where(function ($q) use ($subjectId) {
                $q->whereHas('subjects', fn ($sq) => $sq->where('subjects.id', $subjectId))
                    ->orWhereHas('teachingCourses', fn ($cq) => $cq->where('subject_id', $subjectId));
            });
        }

        if ($request->filled('grade')) {
            $grade = (int) $request->grade;
            $teachersQuery->whereHas('teachingCourses.studentClass', fn ($cq) => $cq->where('grade', $grade));
        }

        $teachersCollection = $teachersQuery->orderBy('name')->get()->map(function (User $teacher) {
            $courses = $teacher->teachingCourses;
            $coursesCount = $courses->count();
            $activeCoursesCount = $courses->filter(
                fn ($c) => $c->materials->isNotEmpty() || $c->assignments->isNotEmpty()
            )->count();
            $materialsCount = $courses->sum(fn ($c) => $c->materials->count());
            $assignmentsCount = $courses->sum(fn ($c) => $c->assignments->count());

            $rppsCount = $teacher->rpps_count;
            $approvedRppsCount = $teacher->rpps->where('review_status', 'approved')->count();
            $revisionRppsCount = $teacher->rpps->where('review_status', 'revision_needed')->count();
            $pendingRppsCount = $teacher->rpps->where('review_status', 'pending')->count();

            $questionsCount = $teacher->created_questions_count;
            $examsCount = $teacher->created_exams_count;

            if ($rppsCount > 0 && $materialsCount > 0) {
                $complianceStatus = 'lengkap';
            } elseif ($rppsCount > 0 || $materialsCount > 0 || $questionsCount > 0 || $examsCount > 0) {
                $complianceStatus = 'sebagian';
            } else {
                $complianceStatus = 'belum';
            }

            // Ringkasan kelas yang diampu
            $classNames = $courses->map(fn ($c) => $c->studentClass?->name)
                ->filter()
                ->unique()
                ->values();

            $teacher->metrics = [
                'courses_count' => $coursesCount,
                'active_courses_count' => $activeCoursesCount,
                'course_coverage_pct' => $coursesCount > 0 ? (int) round(($activeCoursesCount / $coursesCount) * 100) : 0,
                'materials_count' => $materialsCount,
                'assignments_count' => $assignmentsCount,
                'rpps_count' => $rppsCount,
                'approved_rpps_count' => $approvedRppsCount,
                'revision_rpps_count' => $revisionRppsCount,
                'pending_rpps_count' => $pendingRppsCount,
                'questions_count' => $questionsCount,
                'exams_count' => $examsCount,
                'compliance_status' => $complianceStatus,
                'class_names' => $classNames,
            ];

            return $teacher;
        });

        if ($request->filled('compliance')) {
            $compliance = $request->compliance;
            $teachersCollection = $teachersCollection
                ->filter(fn (User $t) => $t->metrics['compliance_status'] === $compliance)
                ->values();
        }

        // Statistik Global Kurikulum Sekolah
        $totalTeachers = User::whereIn('role_id', $teacherRoleIds)
            ->where('email', '!=', 'wakakur@kartika.sch.id')
            ->count();

        $totalCourses = Course::where('is_active', true)->count();
        $coursesWithMaterials = Course::where('is_active', true)
            ->where(function ($q) {
                $q->whereHas('materials')->orWhereHas('assignments');
            })->count();
        $lmsActivityRate = $totalCourses > 0 ? (int) round(($coursesWithMaterials / $totalCourses) * 100) : 0;

        $totalRpps = Rpp::count();
        $integratedRpps = Rpp::where('status', 'integrated')->count();
        $approvedRpps = Rpp::where('review_status', 'approved')->count();
        $revisionRpps = Rpp::where('review_status', 'revision_needed')->count();
        $pendingReviewRpps = Rpp::where('review_status', 'pending')->count();
        $teachersWithRpp = Rpp::distinct('teacher_id')->count('teacher_id');
        $rppTeacherRate = $totalTeachers > 0 ? min(100, (int) round(($teachersWithRpp / $totalTeachers) * 100)) : 0;

        $totalMaterials = LearningMaterial::count();
        $totalAssignments = Assignment::count();
        $totalQuestions = Question::count();
        $totalExams = Exam::count();
        $pendingEssayGrading = ExamResult::where('grading_status', 'partial')->count();

        // Ringkasan Keterlaksanaan per Tingkat Kelas (X, XI, XII)
        $gradeSummaries = collect([
            10 => 'Kelas X (Fase E)',
            11 => 'Kelas XI (Fase F)',
            12 => 'Kelas XII (Fase F)',
        ])->map(function ($label, $grade) {
            $classIds = StudentClass::where('grade', $grade)->pluck('id');
            $coursesCount = Course::whereIn('class_id', $classIds)->count();
            $activeCoursesCount = Course::whereIn('class_id', $classIds)
                ->where(function ($q) {
                    $q->whereHas('materials')->orWhereHas('assignments');
                })->count();
            $rppsCount = Rpp::whereIn('class_id', $classIds)->count();
            $materialsCount = LearningMaterial::whereHas('course', fn ($q) => $q->whereIn('class_id', $classIds))->count();

            return [
                'grade' => $grade,
                'label' => $label,
                'classes_count' => $classIds->count(),
                'courses_count' => $coursesCount,
                'active_courses_count' => $activeCoursesCount,
                'coverage_pct' => $coursesCount > 0 ? (int) round(($activeCoursesCount / $coursesCount) * 100) : 0,
                'rpps_count' => $rppsCount,
                'materials_count' => $materialsCount,
            ];
        });

        // Daftar RPP terbaru untuk di-supervisi
        $recentRpps = Rpp::with(['subject', 'classGroup', 'teacher', 'reviewer'])
            ->withCount(['materials', 'assessments'])
            ->latest()
            ->limit(8)
            ->get();

        // Daftar Koordinator Kurikulum aktif & opsi guru untuk penetapan oleh Admin
        $coordinators = User::with(['role', 'subjects'])
            ->whereHas('role', fn ($q) => $q->where('name', User::ROLE_KOORDINATOR_KURIKULUM))
            ->orderBy('name')
            ->get();

        $allTeachersForAssign = User::with(['role', 'subjects'])
            ->whereIn('role_id', $teacherRoleIds)
            ->where('email', '!=', 'wakakur@kartika.sch.id')
            ->orderBy('name')
            ->get();

        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        return view('curriculum.index', compact(
            'teachersCollection',
            'totalTeachers',
            'totalCourses',
            'coursesWithMaterials',
            'lmsActivityRate',
            'totalRpps',
            'integratedRpps',
            'approvedRpps',
            'revisionRpps',
            'pendingReviewRpps',
            'teachersWithRpp',
            'rppTeacherRate',
            'totalMaterials',
            'totalAssignments',
            'totalQuestions',
            'totalExams',
            'pendingEssayGrading',
            'gradeSummaries',
            'recentRpps',
            'coordinators',
            'allTeachersForAssign',
            'subjects',
        ));
    }

    public function showTeacher(User $teacher)
    {
        $teacher->load([
            'role',
            'subjects',
            'teachingCourses' => fn ($q) => $q->with([
                'subject',
                'studentClass',
                'materials',
                'assignments.submissions',
                'discussions',
            ])->orderBy('class_id'),
            'rpps' => fn ($q) => $q->with([
                'subject',
                'classGroup',
                'reviewer',
            ])->withCount(['materials', 'assessments'])->latest(),
            'createdExams' => fn ($q) => $q->with('subject')->withCount('questions')->latest(),
        ]);

        $questionsBySubject = Question::where('created_by', $teacher->id)
            ->with('subject')
            ->selectRaw('subject_id, count(*) as total')
            ->groupBy('subject_id')
            ->get();

        $totalMaterials = $teacher->teachingCourses->sum(fn ($c) => $c->materials->count());
        $totalAssignments = $teacher->teachingCourses->sum(fn ($c) => $c->assignments->count());
        $activeCourses = $teacher->teachingCourses->filter(
            fn ($c) => $c->materials->isNotEmpty() || $c->assignments->isNotEmpty()
        )->count();

        return view('curriculum.teacher-show', compact(
            'teacher',
            'questionsBySubject',
            'totalMaterials',
            'totalAssignments',
            'activeCourses',
        ));
    }

    public function reviewRpp(Request $request, Rpp $rpp)
    {
        $validated = $request->validate([
            'review_status' => 'required|in:pending,approved,revision_needed',
            'review_notes' => 'nullable|string|max:2000',
        ]);

        $rpp->update([
            'review_status' => $validated['review_status'],
            'review_notes' => $validated['review_notes'] ?? null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $statusLabel = match ($validated['review_status']) {
            'approved' => 'Disetujui',
            'revision_needed' => 'Perlu Revisi',
            default => 'Menunggu Review',
        };

        return back()->with('success', "Status supervisi RPP \"{$rpp->topic}\" berhasil diperbarui menjadi: {$statusLabel}.");
    }

    public function assignCoordinator(Request $request)
    {
        $user = Auth::user();
        if (! $user->isSuperAdmin() && ! $user->isAdmin()) {
            abort(403, 'Hanya Administrator yang dapat menetapkan Koordinator Kurikulum.');
        }

        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'action' => 'required|in:assign,revoke',
        ]);

        $targetTeacher = User::findOrFail($validated['teacher_id']);

        $koordinatorRole = Role::firstOrCreate(
            ['name' => User::ROLE_KOORDINATOR_KURIKULUM],
            ['description' => 'Guru Koordinator Kurikulum - Mengajar serta memantau perencanaan & pelaksanaan kurikulum guru']
        );

        $guruRole = Role::firstOrCreate(
            ['name' => User::ROLE_GURU],
            ['description' => 'Guru Mata Pelajaran']
        );

        if ($validated['action'] === 'assign') {
            $targetTeacher->update(['role_id' => $koordinatorRole->id]);
            $msg = "{$targetTeacher->name} berhasil ditetapkan sebagai Guru Koordinator Kurikulum (dengan tetap mempertahankan seluruh kelas & mata pelajaran yang diampu).";
        } else {
            $targetTeacher->update(['role_id' => $guruRole->id]);
            $msg = "Jabatan Koordinator Kurikulum untuk {$targetTeacher->name} telah dikembalikan menjadi Guru Pengampu.";
        }

        return back()->with('success', $msg);
    }
}
