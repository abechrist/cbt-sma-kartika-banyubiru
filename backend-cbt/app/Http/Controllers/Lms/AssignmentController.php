<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function show($courseId, $assignmentId)
    {
        $user = Auth::user();
        $course = Course::with(['subject', 'studentClass', 'teacher'])->findOrFail($courseId);

        if ($user->isSiswa() && $course->class_id !== $user->class_id) {
            abort(403);
        }

        $assignment = Assignment::with(['submissions.user'])->where('course_id', $course->id)->findOrFail($assignmentId);
        $mySubmission = $user->isSiswa() ? $assignment->submissionForUser($user->id) : null;

        return view('lms.assignments.show', compact('course', 'assignment', 'mySubmission'));
    }

    public function store(Request $request, $courseId)
    {
        $user = Auth::user();
        $course = Course::findOrFail($courseId);

        if ($user->isGuru() && $course->teacher_id !== $user->id && ! $user->isAdmin() && ! $user->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'due_date' => 'nullable|date',
            'max_score' => 'required|integer|min:1|max:100',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,zip|max:20480',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('assignments', 'public');
        }

        Assignment::create([
            'course_id' => $course->id,
            'title' => $validated['title'],
            'instructions' => $validated['instructions'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'max_score' => $validated['max_score'],
            'file_attachment' => $attachmentPath,
            'is_published' => true,
            'created_by' => $user->id,
        ]);

        return redirect()->route('lms.courses.show', $course->id)->with('success', 'Tugas baru berhasil dibuat.');
    }

    public function submit(Request $request, $courseId, $assignmentId)
    {
        $user = Auth::user();
        if (! $user->isSiswa()) {
            abort(403);
        }

        $course = Course::findOrFail($courseId);
        if ($course->class_id !== $user->class_id) {
            abort(403);
        }

        $assignment = Assignment::where('course_id', $course->id)->findOrFail($assignmentId);

        $request->validate([
            'notes' => 'nullable|string|max:1000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,zip|max:20480',
        ]);

        if (! $request->hasFile('file') && empty($request->notes)) {
            return back()->withErrors(['file' => 'Silakan unggah berkas tugas atau sertakan catatan pengerjaan.']);
        }

        $submission = AssignmentSubmission::firstOrNew([
            'assignment_id' => $assignment->id,
            'user_id' => $user->id,
        ]);

        if ($request->hasFile('file')) {
            if ($submission->file_path && Storage::disk('public')->exists($submission->file_path)) {
                Storage::disk('public')->delete($submission->file_path);
            }
            $submission->file_path = $request->file('file')->store('submissions', 'public');
        }

        $isLate = $assignment->due_date && now()->isAfter($assignment->due_date);

        $submission->notes = $request->notes;
        $submission->submitted_at = now();
        $submission->status = $isLate ? 'late' : 'submitted';
        $submission->save();

        return redirect()->route('lms.assignments.show', [$course->id, $assignment->id])
            ->with('success', 'Tugas Anda berhasil dikumpulkan.');
    }

    public function grade(Request $request, $courseId, $assignmentId, $submissionId)
    {
        $user = Auth::user();
        $course = Course::findOrFail($courseId);

        if ($user->isGuru() && $course->teacher_id !== $user->id && ! $user->isAdmin() && ! $user->isSuperAdmin()) {
            abort(403);
        }

        $assignment = Assignment::where('course_id', $course->id)->findOrFail($assignmentId);
        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)->findOrFail($submissionId);

        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:'.$assignment->max_score,
            'feedback' => 'nullable|string|max:1000',
        ]);

        $submission->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'] ?? null,
            'graded_by' => $user->id,
            'graded_at' => now(),
            'status' => 'graded',
        ]);

        return back()->with('success', "Nilai tugas {$submission->user->name} berhasil disimpan.");
    }
}
