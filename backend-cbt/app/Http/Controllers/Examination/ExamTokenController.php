<?php

namespace App\Http\Controllers\Examination;

use App\Http\Controllers\Controller;
use App\Http\Requests\Examination\StoreExamTokenRequest;
use App\Models\ExamSession;
use App\Models\ExamToken;
use Illuminate\Http\Request;

class ExamTokenController extends Controller
{
    public function index(Request $request)
    {
        $query = ExamToken::with(['session.exam']);

        if ($request->filled('session_id')) {
            $query->where('exam_session_id', $request->session_id);
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('token', 'like', "%{$search}%");
        }

        $tokens = $query->latest()->paginate(15);
        $sessions = ExamSession::with('exam')->get();

        return view('tokens.index', compact('tokens', 'sessions'));
    }

    public function create()
    {
        $sessions = ExamSession::with('exam')->where('status', 'open')->get();

        return view('tokens.create', compact('sessions'));
    }

    public function store(StoreExamTokenRequest $request)
    {
        ExamToken::create($request->validated());

        return redirect()->route('tokens.index')->with('success', 'Token berhasil ditambahkan.');
    }

    public function show(ExamToken $token)
    {
        $token->load(['session.exam']);

        return view('tokens.show', compact('token'));
    }

    public function edit(ExamToken $token)
    {
        return view('tokens.edit', compact('token'));
    }

    public function update(StoreExamTokenRequest $request, ExamToken $token)
    {
        $token->update($request->validated());

        return redirect()->route('tokens.index')->with('success', 'Token berhasil diperbarui.');
    }

    public function destroy(ExamToken $token)
    {
        $token->delete();

        return redirect()->route('tokens.index')->with('success', 'Token berhasil dihapus.');
    }

    public function validate(ValidateTokenRequest $request)
    {
        $token = $request->input('token');
        $examToken = ExamToken::with(['session.exam'])
            ->where('token', $token)
            ->first();

        if (! $examToken) {
            return response()->json(['valid' => false, 'message' => 'Token tidak valid.']);
        }

        if (! $examToken->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => $examToken->is_active ? 'Token kedaluwarsa.' : 'Token tidak aktif.',
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Token valid.',
            'exam' => $examToken->session->exam,
            'session' => $examToken->session,
        ]);
    }
}
