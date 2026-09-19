<?php

namespace App\Http\Controllers\Api\Question;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\StoreQuestionRequest;
use App\Http\Requests\Api\UpdateQuestionRequest;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of questions.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Question::with(['subject', 'options', 'questionBank']);

        if ($request->has('search')) {
            $query->where('content', 'like', "%{$request->search}%");
        }

        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->has('question_bank_id')) {
            $query->where('question_bank_id', $request->question_bank_id);
        }

        $questions = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($questions);
    }

    /**
     * Store a newly created question.
     */
    public function store(StoreQuestionRequest $request): JsonResponse
    {
        $question = Question::create($request->validated());

        // Create options if provided
        if ($request->has('options')) {
            foreach ($request->options as $option) {
                $question->options()->create($option);
            }
        }

        return $this->createdResponse($question->load('options'), 'Question created successfully');
    }

    /**
     * Display the specified question.
     */
    public function show(Question $question): JsonResponse
    {
        $question->load(['subject', 'options', 'questionBank', 'exams']);

        return $this->successResponse($question);
    }

    /**
     * Update the specified question.
     */
    public function update(UpdateQuestionRequest $request, Question $question): JsonResponse
    {
        $question->update($request->validated());

        // Update options if provided
        if ($request->has('options')) {
            // Delete existing options
            $question->options()->delete();

            // Create new options
            foreach ($request->options as $option) {
                $question->options()->create($option);
            }
        }

        return $this->successResponse($question->load('options'), 'Question updated successfully');
    }

    /**
     * Remove the specified question.
     */
    public function destroy(Question $question): JsonResponse
    {
        $question->delete();

        return $this->noContentResponse('Question deleted successfully');
    }

    /**
     * Import questions from file.
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        // TODO: Implement import logic

        return $this->successResponse(null, 'Questions imported successfully');
    }

    /**
     * Export questions to file.
     */
    public function export(Request $request): JsonResponse
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'format' => 'sometimes|in:xlsx,csv',
        ]);

        // TODO: Implement export logic

        return $this->successResponse(null, 'Export ready');
    }
}
