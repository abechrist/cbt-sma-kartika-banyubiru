<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
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
            'content' => 'sometimes|string',
            'type' => 'sometimes|in:multiple_choice,true_false,essay,fill_blank',
            'difficulty' => 'sometimes|in:easy,medium,hard',
            'subject_id' => 'sometimes|exists:subjects,id',
            'question_bank_id' => 'nullable|exists:question_banks,id',
            'score' => 'sometimes|numeric|min:0',
            'explanation' => 'nullable|string',
            'options' => 'sometimes|array|min:2',
            'options.*.content' => 'required|string',
            'options.*.is_correct' => 'required|boolean',
            'options.*.order' => 'sometimes|integer',
        ];
    }
}
