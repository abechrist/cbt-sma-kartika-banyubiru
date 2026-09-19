<?php

namespace App\Http\Requests\Examination;

use Illuminate\Foundation\Http\FormRequest;

class SaveAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exam_attempt_id' => ['required', 'exists:exam_attempts,id'],
            'question_id' => ['required', 'exists:questions,id'],
            'answer_text' => ['nullable', 'string'],
            'selected_options' => ['nullable', 'array'],
            'selected_options.*' => ['integer'],
            'is_flagged' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'exam_attempt_id.required' => 'Percobaan ujian tidak valid.',
            'exam_attempt_id.exists' => 'Percobaan ujian tidak ditemukan.',
            'question_id.required' => 'Soal harus dipilih.',
            'question_id.exists' => 'Soal tidak valid.',
        ];
    }
}
