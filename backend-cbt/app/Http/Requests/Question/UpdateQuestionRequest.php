<?php

namespace App\Http\Requests\Question;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'class_id' => ['nullable', 'exists:classes,id'],
            'type' => ['nullable', 'in:pg,pg_kompleks,benar_salah,menjodohkan,isian_singkat,esai'],
            'question_text' => ['nullable', 'string'],
            'image_path' => ['nullable', 'string'],
            'audio_path' => ['nullable', 'string'],
            'video_path' => ['nullable', 'string'],
            'difficulty' => ['nullable', 'in:easy,medium,hard'],
            'score' => ['nullable', 'numeric', 'min:0'],
            'competency_code' => ['nullable', 'string', 'max:50'],
            'options' => ['nullable', 'array'],
            'options.*.label' => ['required_with:options', 'string'],
            'options.*.option_text' => ['required_with:options', 'string'],
            'options.*.is_correct' => ['sometimes', 'boolean'],
            'options.*.correct_match' => ['nullable', 'string', 'max:255'],
            'options.*.sort_order' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'subject_id.exists' => 'Mata pelajaran tidak valid.',
            'type.in' => 'Tipe soal tidak valid.',
            'difficulty.in' => 'Tingkat kesulitan tidak valid.',
            'score.numeric' => 'Bobot nilai harus berupa angka.',
        ];
    }
}
