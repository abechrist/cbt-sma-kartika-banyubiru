<?php

namespace App\Http\Controllers\Examination;

use App\Http\Controllers\Controller;
use App\Http\Requests\Examination\StoreExamRequest;
use App\Http\Requests\Examination\UpdateExamRequest;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::with(['subject', 'creator']);

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Guru hanya bisa melihat ujian yang dibuatnya atau dari mapel yang diampu
        if (Auth::user()->isGuru()) {
            $teacherSubjects = Auth::user()->subjects->pluck('id')->toArray();
            $query->where(function ($q) use ($teacherSubjects) {
                $q->where('created_by', Auth::id())
                    ->orWhereIn('subject_id', $teacherSubjects);
            });
        }

        $exams = $query->latest()->paginate(15);
        $subjects = Subject::where('is_active', true)->get();
        $statuses = [
            'draft' => 'Draft',
            'published' => 'Diterbitkan',
            'active' => 'Aktif',
            'archived' => 'Diarsipkan',
        ];

        return view('exams.index', compact('exams', 'subjects', 'statuses'));
    }

    public function create()
    {
        $subjects = Subject::where('is_active', true)->get();
        $questions = Question::where('is_active', true)->get();

        return view('exams.create', compact('subjects', 'questions'));
    }

    public function store(StoreExamRequest $request)
    {
        $validated = $request->validated();
        $questionIds = $validated['question_ids'] ?? [];
        unset($validated['question_ids']);

        $validated['created_by'] = Auth::id();
        $exam = Exam::create($validated);

        if (! empty($questionIds)) {
            foreach ($questionIds as $index => $questionId) {
                $exam->questions()->attach($questionId, [
                    'order_in_exam' => $index + 1,
                    'score' => 1,
                ]);
            }
        }

        return redirect()->route('exams.index')->with('success', 'Ujian berhasil ditambahkan.');
    }

    public function show(Exam $exam)
    {
        $exam->load(['subject', 'creator', 'questions' => function ($q) {
            $q->with('options');
        }, 'sessions']);
        $sessions = $exam->sessions;

        return view('exams.show', compact('exam', 'sessions'));
    }

    public function edit(Exam $exam)
    {
        $this->authorize('update', $exam);

        $subjects = Subject::where('is_active', true)->get();
        $questions = Question::where('is_active', true)->get();
        $exam->load('questions');

        return view('exams.edit', compact('exam', 'subjects', 'questions'));
    }

    public function update(UpdateExamRequest $request, Exam $exam)
    {
        $this->authorize('update', $exam);

        $validated = $request->validated();
        $questionIds = $validated['question_ids'] ?? null;
        unset($validated['question_ids']);

        $exam->update($validated);

        if ($questionIds !== null) {
            $exam->questions()->detach();
            foreach ($questionIds as $index => $questionId) {
                $exam->questions()->attach($questionId, [
                    'order_in_exam' => $index + 1,
                    'score' => 1,
                ]);
            }
        }

        return redirect()->route('exams.index')->with('success', 'Ujian berhasil diperbarui.');
    }

    public function destroy(Exam $exam)
    {
        $this->authorize('delete', $exam);
        $exam->delete();

        return redirect()->route('exams.index')->with('success', 'Ujian berhasil dihapus.');
    }

    public function publish(Exam $exam)
    {
        $this->authorize('publish', $exam);

        if ($exam->questions()->count() === 0) {
            return back()->with('error', 'Ujian harus memiliki minimal 1 soal sebelum diterbitkan.');
        }

        $exam->update(['status' => 'published']);

        return redirect()->route('exams.show', $exam)->with('success', 'Ujian berhasil diterbitkan.');
    }
}
