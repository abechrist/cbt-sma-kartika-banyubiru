<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'sometimes|exists:subjects,id',
            'duration_minutes' => 'sometimes|integer|min:1',
            'total_questions' => 'sometimes|integer|min:1',
            'passing_score' => 'sometimes|numeric|min:0|max:100',
            'shuffle_questions' => 'sometimes|boolean',
            'show_results' => 'sometimes|boolean',
            'status' => 'sometimes|in:draft,published,archived',
        ];
    }
}
