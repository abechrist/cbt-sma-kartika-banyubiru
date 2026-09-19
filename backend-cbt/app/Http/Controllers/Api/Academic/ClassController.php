<?php

namespace App\Http\Controllers\Api\Academic;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\StoreClassRequest;
use App\Http\Requests\Api\UpdateClassRequest;
use App\Models\StudentClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    /**
     * Display a listing of classes.
     */
    public function index(Request $request): JsonResponse
    {
        $query = StudentClass::query();

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->has('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }

        $classes = $query->orderBy('name')->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($classes);
    }

    /**
     * Store a newly created class.
     */
    public function store(StoreClassRequest $request): JsonResponse
    {
        $class = StudentClass::create($request->validated());

        return $this->createdResponse($class, 'Class created successfully');
    }

    /**
     * Display the specified class.
     */
    public function show(StudentClass $studentClass): JsonResponse
    {
        $studentClass->load(['users', 'subjects']);

        return $this->successResponse($studentClass);
    }

    /**
     * Update the specified class.
     */
    public function update(UpdateClassRequest $request, StudentClass $studentClass): JsonResponse
    {
        $studentClass->update($request->validated());

        return $this->successResponse($studentClass, 'Class updated successfully');
    }

    /**
     * Remove the specified class.
     */
    public function destroy(StudentClass $studentClass): JsonResponse
    {
        $studentClass->delete();

        return $this->noContentResponse('Class deleted successfully');
    }
}
