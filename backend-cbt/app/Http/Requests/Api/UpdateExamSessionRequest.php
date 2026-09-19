<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateExamSessionRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'scheduled_start' => 'sometimes|date',
            'scheduled_end' => 'sometimes|date|after:scheduled_start',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:scheduled,active,completed,cancelled',
        ];
    }
}
