<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveAnswerRequest extends FormRequest
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
            'attempt_id' => 'required|exists:exam_attempts,id',
            'question_id' => 'required|exists:questions,id',
            'answer_text' => 'nullable|string',
            'selected_options' => 'nullable|array',
            'selected_options.*' => 'string',
        ];
    }
}
