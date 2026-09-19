<?php

namespace App\Http\Controllers\Api\Result;

use App\Http\Controllers\Api\Controller;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    /**
     * Display results listing.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ExamResult::with(['attempt.user', 'attempt.exam']);

        if ($request->has('exam_id')) {
            $query->whereHas('attempt', function ($q) use ($request) {
                $q->where('exam_id', $request->exam_id);
            });
        }

        if ($request->has('student_id')) {
            $query->whereHas('attempt', function ($q) use ($request) {
                $q->where('user_id', $request->student_id);
            });
        }

        $results = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($results);
    }

    /**
     * Get results by exam.
     */
    public function byExam(string $examId): JsonResponse
    {
        $exam = Exam::with(['subject'])->findOrFail($examId);

        $results = ExamResult::whereHas('attempt', function ($q) use ($examId) {
            $q->where('exam_id', $examId);
        })
            ->with(['attempt.user'])
            ->get();

        $stats = [
            'total_participants' => $results->count(),
            'average_score' => $results->avg('score'),
            'highest_score' => $results->max('score'),
            'lowest_score' => $results->min('score'),
            'pass_rate' => $results->where('score', '>=', 75)->count() / max($results->count(), 1) * 100,
        ];

        return $this->successResponse([
            'exam' => $exam,
            'results' => $results,
            'stats' => $stats,
        ]);
    }

    /**
     * Get results by class.
     */
    public function byClass(string $classId): JsonResponse
    {
        $class = StudentClass::with(['users'])->findOrFail($classId);

        $results = ExamResult::whereHas('attempt.user', function ($q) use ($classId) {
            $q->where('class_id', $classId);
        })
            ->with(['attempt.user', 'attempt.exam'])
            ->get();

        $stats = [
            'total_students' => $class->users()->where('role', 'siswa')->count(),
            'average_score' => $results->avg('score'),
            'highest_score' => $results->max('score'),
            'lowest_score' => $results->min('score'),
        ];

        return $this->successResponse([
            'class' => $class,
            'results' => $results,
            'stats' => $stats,
        ]);
    }

    /**
     * Get results by student.
     */
    public function byStudent(string $studentId): JsonResponse
    {
        $student = User::findOrFail($studentId);

        $results = ExamResult::whereHas('attempt', function ($q) use ($studentId) {
            $q->where('user_id', $studentId);
        })
            ->with(['attempt.exam'])
            ->get();

        $stats = [
            'total_exams' => $results->count(),
            'average_score' => $results->avg('score'),
            'highest_score' => $results->max('score'),
            'lowest_score' => $results->min('score'),
        ];

        return $this->successResponse([
            'student' => $student,
            'results' => $results,
            'stats' => $stats,
        ]);
    }

    /**
     * Get recap of scores.
     */
    public function rekap(Request $request): JsonResponse
    {
        $query = ExamResult::with(['attempt.user', 'attempt.exam']);

        if ($request->has('subject_id')) {
            $query->whereHas('attempt.exam', function ($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            });
        }

        $results = $query->get();

        $rekap = $results->groupBy('attempt.user.class_id')
            ->map(function ($classResults, $classId) {
                return [
                    'class_id' => $classId,
                    'students' => $classResults->groupBy('attempt.user_id')
                        ->map(function ($studentResults) {
                            return [
                                'student' => $studentResults->first()->attempt->user,
                                'scores' => $studentResults->pluck('score'),
                                'average' => $studentResults->avg('score'),
                            ];
                        }),
                ];
            });

        return $this->successResponse($rekap);
    }

    /**
     * Generate report.
     */
    public function laporan(Request $request): JsonResponse
    {
        $query = ExamResult::with(['attempt.user', 'attempt.exam', 'attempt.exam.subject']);

        if ($request->has('date_from')) {
            $query->whereHas('attempt', function ($q) use ($request) {
                $q->where('submitted_at', '>=', $request->date_from);
            });
        }

        if ($request->has('date_to')) {
            $query->whereHas('attempt', function ($q) use ($request) {
                $q->where('submitted_at', '<=', $request->date_to);
            });
        }

        $results = $query->get();

        $laporan = [
            'summary' => [
                'total_exams' => $results->pluck('attempt.exam_id')->unique()->count(),
                'total_participants' => $results->pluck('attempt.user_id')->unique()->count(),
                'average_score' => $results->avg('score'),
            ],
            'by_subject' => $results->groupBy('attempt.exam.subject.name')
                ->map(function ($subjectResults, $subjectName) {
                    return [
                        'subject' => $subjectName,
                        'count' => $subjectResults->count(),
                        'average' => $subjectResults->avg('score'),
                    ];
                }),
        ];

        return $this->successResponse($laporan);
    }
}
