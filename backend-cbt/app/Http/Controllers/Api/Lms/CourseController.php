<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Api\Controller;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Course::with(['subject', 'teacher', 'materials', 'assignments']);

        if ($request->has('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($courses);
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $course = Course::create([
            ...$request->all(),
            'teacher_id' => auth()->id(),
        ]);

        return $this->createdResponse($course, 'Course created successfully');
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course): JsonResponse
    {
        $course->load(['subject', 'teacher', 'materials', 'assignments', 'discussions']);

        return $this->successResponse($course);
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, Course $course): JsonResponse
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'subject_id' => 'sometimes|exists:subjects,id',
        ]);

        $course->update($request->all());

        return $this->successResponse($course, 'Course updated successfully');
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Course $course): JsonResponse
    {
        $course->delete();

        return $this->noContentResponse('Course deleted successfully');
    }
}
