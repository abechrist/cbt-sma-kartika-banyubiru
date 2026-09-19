<?php

namespace App\Http\Controllers\Api\Examination;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\StoreExamSessionRequest;
use App\Http\Requests\Api\UpdateExamSessionRequest;
use App\Models\ExamSession;
use App\Models\ExamToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExamSessionController extends Controller
{
    /**
     * Display a listing of exam sessions.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ExamSession::with(['exam', 'createdBy']);

        if ($request->has('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $sessions = $query->orderBy('scheduled_start', 'desc')->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($sessions);
    }

    /**
     * Store a newly created exam session.
     */
    public function store(StoreExamSessionRequest $request): JsonResponse
    {
        $session = ExamSession::create($request->validated());

        return $this->createdResponse($session, 'Exam session created successfully');
    }

    /**
     * Display the specified exam session.
     */
    public function show(ExamSession $session): JsonResponse
    {
        $session->load(['exam', 'createdBy', 'tokens', 'attempts']);

        return $this->successResponse($session);
    }

    /**
     * Update the specified exam session.
     */
    public function update(UpdateExamSessionRequest $request, ExamSession $session): JsonResponse
    {
        $session->update($request->validated());

        return $this->successResponse($session, 'Exam session updated successfully');
    }

    /**
     * Remove the specified exam session.
     */
    public function destroy(ExamSession $session): JsonResponse
    {
        $session->delete();

        return $this->noContentResponse('Exam session deleted successfully');
    }

    /**
     * Open an exam session.
     */
    public function open(ExamSession $session): JsonResponse
    {
        $session->update(['status' => 'active']);

        return $this->successResponse($session, 'Exam session opened');
    }

    /**
     * Close an exam session.
     */
    public function close(ExamSession $session): JsonResponse
    {
        $session->update(['status' => 'closed']);

        return $this->successResponse($session, 'Exam session closed');
    }

    /**
     * Generate tokens for an exam session.
     */
    public function generateTokens(ExamSession $session, Request $request): JsonResponse
    {
        $count = $request->get('count', 10);
        $tokens = [];

        for ($i = 0; $i < $count; $i++) {
            $tokens[] = ExamToken::create([
                'session_id' => $session->id,
                'token' => strtoupper(Str::random(6)),
                'expires_at' => $session->scheduled_end,
            ]);
        }

        return $this->createdResponse($tokens, 'Tokens generated successfully');
    }

    /**
     * Print tokens for an exam session.
     */
    public function printTokens(ExamSession $session): JsonResponse
    {
        $tokens = ExamToken::where('session_id', $session->id)->get();

        return $this->successResponse([
            'session' => $session,
            'tokens' => $tokens,
        ]);
    }
}
