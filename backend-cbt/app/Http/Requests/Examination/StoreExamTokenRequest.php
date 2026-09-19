<?php

namespace App\Http\Requests\Examination;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exam_session_id' => ['required', 'exists:exam_sessions,id'],
            'expires_at' => ['required', 'date', 'after:now'],
            'is_single_use' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'exam_session_id.required' => 'Sesi ujian harus dipilih.',
            'exam_session_id.exists' => 'Sesi ujian tidak valid.',
            'expires_at.required' => 'Waktu kedaluwarsa harus diisi.',
            'expires_at.after' => 'Waktu kedaluwarsa harus di masa depan.',
        ];
    }
}
