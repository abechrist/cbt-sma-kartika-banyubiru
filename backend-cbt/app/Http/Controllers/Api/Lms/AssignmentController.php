<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Api\Controller;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments for a course.
     */
    public function index(Request $request, Course $course): JsonResponse
    {
        $assignments = $course->assignments()
            ->with(['submissions' => function ($q) {
                $q->where('user_id', auth()->id());
            }])
            ->orderBy('due_date', 'asc')
            ->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($assignments);
    }

    /**
     * Store a newly created assignment.
     */
    public function store(Request $request, Course $course): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'max_score' => 'required|integer|min:0',
            'allowed_file_types' => 'sometimes|array',
        ]);

        $assignment = $course->assignments()->create($request->all());

        return $this->createdResponse($assignment, 'Assignment created successfully');
    }

    /**
     * Display the specified assignment.
     */
    public function show(Course $course, Assignment $assignment): JsonResponse
    {
        $assignment->load(['submissions' => function ($q) {
            $q->where('user_id', auth()->id());
        }]);

        return $this->successResponse($assignment);
    }

    /**
     * Update the specified assignment.
     */
    public function update(Request $request, Course $course, Assignment $assignment): JsonResponse
    {
        $assignment->update($request->all());

        return $this->successResponse($assignment, 'Assignment updated successfully');
    }

    /**
     * Remove the specified assignment.
     */
    public function destroy(Course $course, Assignment $assignment): JsonResponse
    {
        $assignment->delete();

        return $this->noContentResponse('Assignment deleted successfully');
    }

    /**
     * Submit assignment.
     */
    public function submit(Request $request, Course $course, Assignment $assignment): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'notes' => 'sometimes|string',
        ]);

        $path = $request->file->store('assignments/'.$assignment->id, 'public');

        $submission = $assignment->submissions()->create([
            'user_id' => auth()->id(),
            'file_path' => $path,
            'notes' => $request->notes,
            'status' => 'submitted',
        ]);

        return $this->createdResponse($submission, 'Assignment submitted successfully');
    }
}
