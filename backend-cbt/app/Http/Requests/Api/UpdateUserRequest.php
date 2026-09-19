<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user')?->id;

        return [
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'password' => 'sometimes|string|min:8',
            'role' => 'sometimes|in:siswa,guru,admin,proktor,kepala_sekolah,wali_kelas',
            'nisn' => ['sometimes', 'nullable', 'string', Rule::unique('users', 'nisn')->ignore($userId)],
            'nip' => ['sometimes', 'nullable', 'string', Rule::unique('users', 'nip')->ignore($userId)],
            'class_id' => 'sometimes|nullable|exists:student_classes,id',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
