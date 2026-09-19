<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Api\Controller;
use App\Models\Course;
use App\Models\CourseDiscussion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    /**
     * Display a listing of discussions for a course.
     */
    public function index(Request $request, Course $course): JsonResponse
    {
        $discussions = $course->discussions()
            ->with(['user', 'replies'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($discussions);
    }

    /**
     * Store a newly created discussion.
     */
    public function store(Request $request, Course $course): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $discussion = $course->discussions()->create([
            ...$request->all(),
            'user_id' => auth()->id(),
        ]);

        return $this->createdResponse($discussion->load('user'), 'Discussion created successfully');
    }

    /**
     * Display the specified discussion.
     */
    public function show(Course $course, CourseDiscussion $discussion): JsonResponse
    {
        $discussion->load(['user', 'replies.user']);

        return $this->successResponse($discussion);
    }

    /**
     * Update the specified discussion.
     */
    public function update(Request $request, Course $course, CourseDiscussion $discussion): JsonResponse
    {
        if ($discussion->user_id !== auth()->id()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $discussion->update($request->all());

        return $this->successResponse($discussion, 'Discussion updated successfully');
    }

    /**
     * Remove the specified discussion.
     */
    public function destroy(Course $course, CourseDiscussion $discussion): JsonResponse
    {
        if ($discussion->user_id !== auth()->id()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $discussion->delete();

        return $this->noContentResponse('Discussion deleted successfully');
    }

    /**
     * Add reply to discussion.
     */
    public function reply(Request $request, Course $course, CourseDiscussion $discussion): JsonResponse
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $reply = $discussion->replies()->create([
            ...$request->all(),
            'user_id' => auth()->id(),
        ]);

        return $this->createdResponse($reply->load('user'), 'Reply added successfully');
    }
}
