<?php

namespace App\Http\Requests\Examination;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:300'],
            'randomize_questions' => ['boolean'],
            'randomize_options' => ['boolean'],
            'allow_back' => ['boolean'],
            'show_result_after' => ['boolean'],
            'question_ids' => ['nullable', 'array'],
            'question_ids.*' => ['exists:questions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'duration_minutes.integer' => 'Durasi harus berupa angka.',
            'duration_minutes.min' => 'Durasi minimal 1 menit.',
            'duration_minutes.max' => 'Durasi maksimal 300 menit.',
            'question_ids.*.exists' => 'Soal yang dipilih tidak valid.',
        ];
    }
}
