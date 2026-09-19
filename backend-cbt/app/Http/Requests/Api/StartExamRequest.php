<?php

namespace App\Http\Requests\Api;

use App\Models\ExamToken;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StartExamRequest extends FormRequest
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
            'session_id' => 'required|exists:exam_sessions,id',
            'token_id' => 'required|exists:exam_tokens,id',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  Validator  $validator
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $token = ExamToken::find($this->token_id);

            if ($token && $token->is_used) {
                $validator->errors()->add('token_id', 'Token has already been used.');
            }

            if ($token && $token->expires_at && $token->expires_at->isPast()) {
                $validator->errors()->add('token_id', 'Token has expired.');
            }
        });
    }
}
