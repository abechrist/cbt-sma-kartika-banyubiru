<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use App\Http\Requests\Question\StoreQuestionRequest;
use App\Http\Requests\Question\UpdateQuestionRequest;
use App\Models\Question;
use App\Models\StudentClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with(['subject', 'class', 'creator']);

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('question_text', 'like', "%{$search}%");
        }

        if (Auth::user()->isGuru()) {
            $teacherSubjects = Auth::user()->subjects->pluck('id')->toArray();
            $query->where(function ($q) use ($teacherSubjects) {
                $q->where('created_by', Auth::id())
                    ->orWhereIn('subject_id', $teacherSubjects);
            });
        }

        $questions = $query->latest()->paginate(15);

        $subjects = Subject::where('is_active', true)->get();
        $classes = StudentClass::where('is_active', true)->get();

        $types = [
            'pg' => 'Pilihan Ganda',
            'pg_kompleks' => 'Pilihan Ganda Kompleks',
            'benar_salah' => 'Benar/Salah',
            'menjodohkan' => 'Menjodohkan',
            'isian_singkat' => 'Isian Singkat',
            'esai' => 'Esai',
        ];

        return view('questions.index', compact('questions', 'subjects', 'classes', 'types'));
    }

    public function create()
    {
        $subjects = Subject::where('is_active', true)->get();
        $classes = StudentClass::where('is_active', true)->get();
        $types = [
            'pg' => 'Pilihan Ganda',
            'pg_kompleks' => 'Pilihan Ganda Kompleks',
            'benar_salah' => 'Benar/Salah',
            'menjodohkan' => 'Menjodohkan',
            'isian_singkat' => 'Isian Singkat',
            'esai' => 'Esai',
        ];
        $difficulties = [
            'easy' => 'Mudah',
            'medium' => 'Sedang',
            'hard' => 'Sulit',
        ];

        return view('questions.create', compact('subjects', 'classes', 'types', 'difficulties'));
    }

    public function store(StoreQuestionRequest $request)
    {
        $validated = $request->validated();
        $options = $validated['options'] ?? [];
        unset($validated['options']);

        $validated['created_by'] = Auth::id();
        $question = Question::create($validated);

        foreach ($options as $index => $option) {
            $option['question_id'] = $question->id;
            $option['sort_order'] = $option['sort_order'] ?? $index;
            $option['is_correct'] = (int) ($option['is_correct'] ?? 0);
            $option['correct_match'] = $option['correct_match'] ?? null;
            $option['question_id'] = $question->id;
            $question->options()->create($option);
        }

        Cache::forget('active_classes');
        Cache::forget('active_subjects');

        return redirect()->route('questions.index')->with('success', 'Soal berhasil ditambahkan.');
    }

    public function show(Question $question)
    {
        $question->load(['subject', 'class', 'creator', 'options']);

        return view('questions.show', compact('question'));
    }

    public function edit(Question $question)
    {
        $this->authorize('update', $question);

        $subjects = Subject::where('is_active', true)->get();
        $classes = StudentClass::where('is_active', true)->get();
        $types = [
            'pg' => 'Pilihan Ganda',
            'pg_kompleks' => 'Pilihan Ganda Kompleks',
            'benar_salah' => 'Benar/Salah',
            'menjodohkan' => 'Menjodohkan',
            'isian_singkat' => 'Isian Singkat',
            'esai' => 'Esai',
        ];
        $difficulties = [
            'easy' => 'Mudah',
            'medium' => 'Sedang',
            'hard' => 'Sulit',
        ];

        $question->load('options');

        return view('questions.edit', compact('question', 'subjects', 'classes', 'types', 'difficulties'));
    }

    public function update(UpdateQuestionRequest $request, Question $question)
    {
        $this->authorize('update', $question);

        $validated = $request->validated();
        $options = $validated['options'] ?? [];
        unset($validated['options']);

        $question->update($validated);

        if ($request->has('options')) {
            $question->options()->delete();
            foreach ($options as $index => $option) {
                $option['question_id'] = $question->id;
                $option['sort_order'] = $option['sort_order'] ?? $index;
                $option['is_correct'] = (int) ($option['is_correct'] ?? 0);
                $option['correct_match'] = $option['correct_match'] ?? null;
                $option['question_id'] = $question->id;
                $question->options()->create($option);
            }
        }

        Cache::forget('active_classes');
        Cache::forget('active_subjects');

        return redirect()->route('questions.index')->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Question $question)
    {
        $this->authorize('delete', $question);
        $question->delete();

        Cache::forget('active_classes');
        Cache::forget('active_subjects');

        return redirect()->route('questions.index')->with('success', 'Soal berhasil dihapus.');
    }
}
