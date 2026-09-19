<?php

namespace App\Http\Requests\Question;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_id' => ['required', 'exists:subjects,id'],
            'class_id' => ['nullable', 'exists:classes,id'],
            'type' => ['required', 'in:pg,pg_kompleks,benar_salah,menjodohkan,isian_singkat,esai'],
            'question_text' => ['required', 'string'],
            'image_path' => ['nullable', 'string'],
            'audio_path' => ['nullable', 'string'],
            'video_path' => ['nullable', 'string'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'score' => ['required', 'numeric', 'min:0'],
            'competency_code' => ['nullable', 'string', 'max:50'],
            'options' => ['required_if:type,pg,pg_kompleks,menjodohkan', 'array'],
            'options.*.label' => ['required', 'string'],
            'options.*.option_text' => ['required', 'string'],
            'options.*.is_correct' => ['sometimes', 'boolean'],
            'options.*.correct_match' => ['nullable', 'string', 'max:255'],
            'options.*.sort_order' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'subject_id.required' => 'Mata pelajaran harus dipilih.',
            'subject_id.exists' => 'Mata pelajaran tidak valid.',
            'type.required' => 'Tipe soal harus dipilih.',
            'type.in' => 'Tipe soal tidak valid.',
            'question_text.required' => 'Teks soal harus diisi.',
            'difficulty.required' => 'Tingkat kesulitan harus dipilih.',
            'difficulty.in' => 'Tingkat kesulitan tidak valid.',
            'score.required' => 'Bobot nilai harus diisi.',
            'score.numeric' => 'Bobot nilai harus berupa angka.',
            'options.required_if' => 'Opsi jawaban harus diisi untuk soal pilihan ganda.',
        ];
    }
}
