<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email,'.$userId],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id' => ['nullable', 'exists:roles,id'],
            'nisn' => ['nullable', 'string', 'max:20', 'unique:users,nisn,'.$userId],
            'nip' => ['nullable', 'string', 'max:20', 'unique:users,nip,'.$userId],
            'class_id' => ['nullable', 'exists:classes,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
            'gender' => ['nullable', 'in:L,P'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email sudah digunakan.',
            'email.email' => 'Format email tidak valid.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role_id.exists' => 'Peran tidak valid.',
            'nisn.unique' => 'NISN sudah terdaftar.',
            'nip.unique' => 'NIP sudah terdaftar.',
        ];
    }
}
