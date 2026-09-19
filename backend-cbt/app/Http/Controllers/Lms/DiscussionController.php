<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseDiscussion;
use App\Models\DiscussionReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    public function store(Request $request, $courseId)
    {
        $user = Auth::user();
        $course = Course::findOrFail($courseId);

        if ($user->isSiswa() && $course->class_id !== $user->class_id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        CourseDiscussion::create([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'is_pinned' => false,
        ]);

        return redirect()->route('lms.courses.show', $course->id)->with('success', 'Topik diskusi berhasil diterbitkan.');
    }

    public function reply(Request $request, $courseId, $discussionId)
    {
        $user = Auth::user();
        $course = Course::findOrFail($courseId);

        if ($user->isSiswa() && $course->class_id !== $user->class_id) {
            abort(403);
        }

        $discussion = CourseDiscussion::where('course_id', $course->id)->findOrFail($discussionId);

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        DiscussionReply::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'content' => $validated['content'],
        ]);

        return redirect()->route('lms.courses.show', $course->id)->with('success', 'Tanggapan diskusi berhasil dikirim.');
    }
}
