<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LearningMaterial;
use App\Models\MaterialProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LearningMaterialController extends Controller
{
    public function show($courseId, $materialId)
    {
        $user = Auth::user();
        $course = Course::with(['subject', 'studentClass', 'teacher'])->findOrFail($courseId);

        if ($user->isSiswa() && $course->class_id !== $user->class_id) {
            abort(403);
        }

        $material = LearningMaterial::where('course_id', $course->id)->findOrFail($materialId);

        // Jika siswa membuka materi, catat progres jika belum
        $isCompleted = false;
        if ($user->isSiswa()) {
            $progress = MaterialProgress::firstOrCreate([
                'material_id' => $material->id,
                'user_id' => $user->id,
            ]);
            $isCompleted = $progress->completed_at !== null;
        }

        return view('lms.materials.show', compact('course', 'material', 'isCompleted'));
    }

    public function store(Request $request, $courseId)
    {
        $user = Auth::user();
        $course = Course::findOrFail($courseId);

        if ($user->isGuru() && $course->teacher_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'chapter' => 'nullable|string|max:100',
            'type' => 'required|in:file,video,article',
            'video_url' => 'nullable|url',
            'content_text' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,ppt,pptx,doc,docx,zip|max:20480',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('materials', 'public');
        }

        LearningMaterial::create([
            'course_id' => $course->id,
            'title' => $validated['title'],
            'chapter' => $validated['chapter'] ?? null,
            'type' => $validated['type'],
            'file_path' => $filePath,
            'video_url' => $validated['video_url'] ?? null,
            'content_text' => $validated['content_text'] ?? null,
            'is_published' => true,
            'created_by' => $user->id,
        ]);

        return redirect()->route('lms.courses.show', $course->id)->with('success', 'Materi pembelajaran berhasil ditambahkan.');
    }

    public function toggleComplete(Request $request, $courseId, $materialId)
    {
        $user = Auth::user();
        if (! $user->isSiswa()) {
            abort(403);
        }

        $progress = MaterialProgress::firstOrCreate([
            'material_id' => $materialId,
            'user_id' => $user->id,
        ]);

        if ($progress->completed_at) {
            $progress->update(['completed_at' => null]);
            $msg = 'Materi ditandai belum selesai.';
        } else {
            $progress->update(['completed_at' => now()]);
            $msg = 'Selamat! Materi ditandai telah selesai dipelajari.';
        }

        return back()->with('success', $msg);
    }

    public function destroy($courseId, $materialId)
    {
        $user = Auth::user();
        $course = Course::findOrFail($courseId);
        if ($user->isGuru() && $course->teacher_id !== $user->id && ! $user->isAdmin() && ! $user->isSuperAdmin()) {
            abort(403);
        }

        $material = LearningMaterial::where('course_id', $course->id)->findOrFail($materialId);
        if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }
        $material->delete();

        return redirect()->route('lms.courses.show', $course->id)->with('success', 'Materi berhasil dihapus.');
    }
}
