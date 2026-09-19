<?php

namespace App\Http\Controllers\Api\Examination;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\StoreExamTokenRequest;
use App\Models\ExamToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamTokenController extends Controller
{
    /**
     * Display a listing of exam tokens.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ExamToken::with(['session.exam']);

        if ($request->has('session_id')) {
            $query->where('session_id', $request->session_id);
        }

        if ($request->has('is_used')) {
            $query->where('is_used', $request->boolean('is_used'));
        }

        $tokens = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($tokens);
    }

    /**
     * Store a newly created exam token.
     */
    public function store(StoreExamTokenRequest $request): JsonResponse
    {
        $token = ExamToken::create($request->validated());

        return $this->createdResponse($token, 'Exam token created successfully');
    }

    /**
     * Display the specified exam token.
     */
    public function show(ExamToken $token): JsonResponse
    {
        $token->load(['session.exam', 'usedBy']);

        return $this->successResponse($token);
    }

    /**
     * Remove the specified exam token.
     */
    public function destroy(ExamToken $token): JsonResponse
    {
        $token->delete();

        return $this->noContentResponse('Exam token deleted successfully');
    }

    /**
     * Validate an exam token.
     */
    public function validate(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'session_id' => 'required|exists:exam_sessions,id',
        ]);

        $token = ExamToken::where('token', $request->token)
            ->where('session_id', $request->session_id)
            ->first();

        if (! $token) {
            return $this->errorResponse('Invalid token', 404);
        }

        if ($token->is_used) {
            return $this->errorResponse('Token already used', 422);
        }

        if ($token->expires_at && $token->expires_at->isPast()) {
            return $this->errorResponse('Token expired', 422);
        }

        return $this->successResponse([
            'valid' => true,
            'session' => $token->session,
        ], 'Token is valid');
    }
}
