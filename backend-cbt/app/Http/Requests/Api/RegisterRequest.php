<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'sometimes|in:siswa,guru,admin',
            'nisn' => 'required_if:role,siswa|nullable|string|unique:users,nisn',
            'nip' => 'required_if:role,guru|nullable|string|unique:users,nip',
            'class_id' => 'required_if:role,siswa|nullable|exists:student_classes,id',
        ];
    }
}
