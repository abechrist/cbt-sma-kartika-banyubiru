<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\StoreClassRequest;
use App\Http\Requests\Academic\UpdateClassRequest;
use App\Models\StudentClass;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentClass::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('grade', 'like', "%{$search}%");
        }

        $classes = $query->orderBy('name')->paginate(15);

        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        return view('classes.create');
    }

    public function store(StoreClassRequest $request)
    {
        StudentClass::create($request->validated());

        return redirect()->route('classes.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(StudentClass $class)
    {
        $class->load(['students' => function ($q) {
            $q->with(['user' => function ($uq) {
                $uq->select('id', 'name', 'nisn');
            }]);
        }]);

        return view('classes.show', compact('class'));
    }

    public function edit(StudentClass $class)
    {
        return view('classes.edit', compact('class'));
    }

    public function update(UpdateClassRequest $request, StudentClass $class)
    {
        $class->update($request->validated());

        return redirect()->route('classes.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(StudentClass $class)
    {
        if ($class->students()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus kelas yang masih memiliki siswa.');
        }
        $class->delete();

        return redirect()->route('classes.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
