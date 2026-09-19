<?php

namespace App\Http\Requests\Examination;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExamSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exam_id' => ['nullable', 'exists:exams,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after:start_at'],
            'room' => ['nullable', 'string', 'max:100'],
            'max_participants' => ['nullable', 'integer', 'min:1', 'max:100'],
            'token_prefix' => ['nullable', 'string', 'max:10'],
            'instructions' => ['nullable', 'string'],
            'allow_resume' => ['boolean'],
            'auto_submit_on_timeout' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'exam_id.exists' => 'Ujian tidak valid.',
            'end_at.after' => 'Waktu selesai harus setelah waktu mulai.',
            'max_participants.integer' => 'Kapasitas harus berupa angka.',
        ];
    }
}
