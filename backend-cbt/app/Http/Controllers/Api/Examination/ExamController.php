<?php

namespace App\Http\Controllers\Api\Examination;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\StoreExamRequest;
use App\Http\Requests\Api\UpdateExamRequest;
use App\Models\Exam;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Display a listing of exams.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Exam::with(['subject', 'createdBy']);

        if ($request->has('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $exams = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($exams);
    }

    /**
     * Store a newly created exam.
     */
    public function store(StoreExamRequest $request): JsonResponse
    {
        $exam = Exam::create($request->validated());

        return $this->createdResponse($exam, 'Exam created successfully');
    }

    /**
     * Display the specified exam.
     */
    public function show(Exam $exam): JsonResponse
    {
        $exam->load(['subject', 'createdBy', 'questions', 'sessions']);

        return $this->successResponse($exam);
    }

    /**
     * Update the specified exam.
     */
    public function update(UpdateExamRequest $request, Exam $exam): JsonResponse
    {
        $exam->update($request->validated());

        return $this->successResponse($exam, 'Exam updated successfully');
    }

    /**
     * Remove the specified exam.
     */
    public function destroy(Exam $exam): JsonResponse
    {
        $exam->delete();

        return $this->noContentResponse('Exam deleted successfully');
    }

    /**
     * Publish the specified exam.
     */
    public function publish(Exam $exam): JsonResponse
    {
        $exam->update(['status' => 'published']);

        return $this->successResponse($exam, 'Exam published successfully');
    }
}
