<?php

namespace App\Http\Controllers\Api\Question;

use App\Http\Controllers\Api\Controller;
use App\Models\QuestionBank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionBankController extends Controller
{
    /**
     * Display a listing of question banks.
     */
    public function index(Request $request): JsonResponse
    {
        $query = QuestionBank::with(['subject', 'questions']);

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $banks = $query->orderBy('name')->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($banks);
    }

    /**
     * Store a newly created question bank.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $bank = QuestionBank::create($request->all());

        return $this->createdResponse($bank, 'Question bank created successfully');
    }

    /**
     * Display the specified question bank.
     */
    public function show(QuestionBank $questionBank): JsonResponse
    {
        $questionBank->load(['subject', 'questions.options']);

        return $this->successResponse($questionBank);
    }

    /**
     * Update the specified question bank.
     */
    public function update(Request $request, QuestionBank $questionBank): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'sometimes|exists:subjects,id',
        ]);

        $questionBank->update($request->all());

        return $this->successResponse($questionBank, 'Question bank updated successfully');
    }

    /**
     * Remove the specified question bank.
     */
    public function destroy(QuestionBank $questionBank): JsonResponse
    {
        $questionBank->delete();

        return $this->noContentResponse('Question bank deleted successfully');
    }
}
