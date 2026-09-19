<?php

namespace App\Http\Controllers\Api\Academic;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\StoreSubjectRequest;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of subjects.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Subject::query();

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $subjects = $query->orderBy('name')->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($subjects);
    }

    /**
     * Store a newly created subject.
     */
    public function store(StoreSubjectRequest $request): JsonResponse
    {
        $subject = Subject::create($request->validated());

        return $this->createdResponse($subject, 'Subject created successfully');
    }

    /**
     * Display the specified subject.
     */
    public function show(Subject $subject): JsonResponse
    {
        $subject->load(['questions', 'exams']);

        return $this->successResponse($subject);
    }

    /**
     * Update the specified subject.
     */
    public function update(StoreSubjectRequest $request, Subject $subject): JsonResponse
    {
        $subject->update($request->validated());

        return $this->successResponse($subject, 'Subject updated successfully');
    }

    /**
     * Remove the specified subject.
     */
    public function destroy(Subject $subject): JsonResponse
    {
        $subject->delete();

        return $this->noContentResponse('Subject deleted successfully');
    }
}
