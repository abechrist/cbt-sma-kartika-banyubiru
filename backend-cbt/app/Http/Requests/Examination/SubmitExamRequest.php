<?php

namespace App\Http\Requests\Examination;

use App\Models\ExamAttempt;
use Illuminate\Foundation\Http\FormRequest;

class SubmitExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('exam_attempt_id')) {
            $attempt = $this->route('attempt');
            $this->merge([
                'exam_attempt_id' => $attempt instanceof ExamAttempt ? $attempt->id : $attempt,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'exam_attempt_id' => ['required', 'exists:exam_attempts,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'exam_attempt_id.required' => 'Percobaan ujian tidak valid.',
            'exam_attempt_id.exists' => 'Percobaan ujian tidak ditemukan.',
        ];
    }
}
