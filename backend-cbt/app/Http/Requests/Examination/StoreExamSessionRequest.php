<?php

namespace App\Http\Requests\Examination;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exam_id' => ['required', 'exists:exams,id'],
            'name' => ['required', 'string', 'max:255'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'room' => ['nullable', 'string', 'max:100'],
            'max_participants' => ['required', 'integer', 'min:1', 'max:100'],
            'token_prefix' => ['nullable', 'string', 'max:10'],
            'instructions' => ['nullable', 'string'],
            'allow_resume' => ['boolean'],
            'auto_submit_on_timeout' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'exam_id.required' => 'Ujian harus dipilih.',
            'exam_id.exists' => 'Ujian tidak valid.',
            'name.required' => 'Nama sesi harus diisi.',
            'start_at.required' => 'Waktu mulai harus diisi.',
            'end_at.required' => 'Waktu selesai harus diisi.',
            'end_at.after' => 'Waktu selesai harus setelah waktu mulai.',
            'max_participants.required' => 'Kapasitas maksimal harus diisi.',
            'max_participants.integer' => 'Kapasitas harus berupa angka.',
        ];
    }
}
