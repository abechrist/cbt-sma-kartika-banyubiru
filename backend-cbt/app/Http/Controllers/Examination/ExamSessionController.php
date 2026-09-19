<?php

namespace App\Http\Controllers\Examination;

use App\Http\Controllers\Controller;
use App\Http\Requests\Examination\StoreExamSessionRequest;
use App\Http\Requests\Examination\UpdateExamSessionRequest;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\ExamToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExamSessionController extends Controller
{
    public function index(Request $request)
    {
        $query = ExamSession::with(['exam.subject']);

        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sessions = $query->latest()->paginate(15);
        $exams = Exam::where('status', 'published')->get();
        $statuses = [
            'scheduled' => 'Terjadwal',
            'open' => 'Dibuka',
            'in_progress' => 'Sedang Berlangsung',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        return view('sessions.index', compact('sessions', 'exams', 'statuses'));
    }

    public function create()
    {
        $exams = Exam::where('status', 'published')->get();

        return view('sessions.create', compact('exams'));
    }

    public function store(StoreExamSessionRequest $request)
    {
        $validated = $request->validated();
        $session = ExamSession::create($validated);

        return redirect()->route('sessions.show', $session)->with('success', 'Sesi ujian berhasil ditambahkan.');
    }

    public function show(ExamSession $session)
    {
        $session->load(['exam.subject', 'tokens', 'attempts.user']);

        return view('sessions.show', compact('session'));
    }

    public function edit(ExamSession $session)
    {
        $exams = Exam::where('status', 'published')->get();

        return view('sessions.edit', compact('session', 'exams'));
    }

    public function update(UpdateExamSessionRequest $request, ExamSession $session)
    {
        $session->update($request->validated());

        return redirect()->route('sessions.show', $session)->with('success', 'Sesi ujian berhasil diperbarui.');
    }

    public function destroy(ExamSession $session)
    {
        if ($session->attempts()->where('status', '!=', 'not_started')->exists()) {
            return back()->with('error', 'Tidak dapat menghapus sesi yang sudah dimulai.');
        }
        $session->delete();

        return redirect()->route('sessions.index')->with('success', 'Sesi ujian berhasil dihapus.');
    }

    public function open(ExamSession $session)
    {
        if ($session->status === 'completed') {
            return back()->with('error', 'Sesi sudah selesai.');
        }
        $session->update(['status' => 'open']);

        return redirect()->route('sessions.show', $session)->with('success', 'Sesi ujian dibuka untuk peserta.');
    }

    public function close(ExamSession $session)
    {
        $session->update(['status' => 'completed']);

        return redirect()->route('sessions.show', $session)->with('success', 'Sesi ujian ditutup.');
    }

    public function generateTokens(Request $request, ExamSession $session)
    {
        $request->validate([
            'count' => ['required', 'integer', 'min:1', 'max:100'],
            'expires_at' => ['required', 'date', 'after:now'],
        ]);

        $count = $request->input('count', $session->max_participants);
        $expiresAt = $request->input('expires_at');

        $prefix = $session->token_prefix ?: 'EXAM';
        $tokens = [];

        for ($i = 0; $i < $count; $i++) {
            $token = strtoupper($prefix.'-'.Str::random(8));
            $tokens[] = [
                'exam_session_id' => $session->id,
                'token' => $token,
                'expires_at' => $expiresAt,
                'is_active' => true,
                'is_single_use' => true,
            ];
        }

        ExamToken::insert($tokens);

        return redirect()->route('sessions.show', $session)->with('success', "$count token berhasil dibuat.");
    }

    public function printTokens(ExamSession $session)
    {
        $tokens = $session->tokens()->where('is_active', true)->get();

        return view('sessions.tokens', compact('session', 'tokens'));
    }
}
