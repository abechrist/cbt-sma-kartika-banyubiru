<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\StoreSubjectRequest;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with('teacher');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $subjects = $query->orderBy('name')->paginate(15);

        return view('subjects.index', compact('subjects'));
    }

    public function create()
    {
        $teachers = User::whereHas('role', fn ($q) => $q->where('name', User::ROLE_GURU))
            ->where('is_active', true)
            ->get();

        return view('subjects.create', compact('teachers'));
    }

    public function store(StoreSubjectRequest $request)
    {
        Subject::create($request->validated());

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function show(Subject $subject)
    {
        $subject->load('teacher');

        return view('subjects.show', compact('subject'));
    }

    public function edit(Subject $subject)
    {
        $teachers = User::whereHas('role', fn ($q) => $q->where('name', User::ROLE_GURU))
            ->where('is_active', true)
            ->get();

        return view('subjects.edit', compact('subject', 'teachers'));
    }

    public function update(StoreSubjectRequest $request, Subject $subject)
    {
        $subject->update($request->validated());

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
