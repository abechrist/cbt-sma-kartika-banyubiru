<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    protected $fillable = ['exam_attempt_id', 'question_id', 'answer_text', 'selected_options', 'is_flagged', 'score', 'graded_by', 'graded_at', 'notes', 'answered_at'];

    protected function casts(): array
    {
        return [
            'selected_options' => 'array',
            'is_flagged' => 'boolean',
            'score' => 'decimal:2',
            'graded_by' => 'integer',
            'graded_at' => 'datetime',
            'answered_at' => 'datetime',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function isCorrect(): bool
    {
        $question = $this->question;
        if (! $question || ! $question->isAutoGradable()) {
            return false;
        }

        if ($question->type === Question::TYPE_ISIAN_SINGKAT) {
            $correctText = $question->options()->where('is_correct', true)->value('option_text');

            return $correctText !== null
                && strtolower(trim((string) $this->answer_text)) === strtolower(trim((string) $correctText));
        }

        if ($question->type === Question::TYPE_BENAR_SALAH) {
            $correctText = $question->options()->where('is_correct', true)->value('option_text');

            return $correctText !== null
                && strtolower(trim((string) $this->answer_text)) === strtolower(trim((string) $correctText));
        }

        if ($question->type === Question::TYPE_MENJODOHKAN) {
            $selected = (array) $this->selected_options;
            if (empty($selected)) {
                return false;
            }

            $expected = $question->options()
                ->whereNotNull('correct_match')
                ->get()
                ->mapWithKeys(fn ($opt) => [(string) $opt->id => (string) $opt->correct_match]);

            if ($expected->isEmpty()) {
                return false;
            }

            foreach ($expected as $optionId => $match) {
                if (! array_key_exists($optionId, $selected) || (string) $selected[$optionId] !== $match) {
                    return false;
                }
            }

            return true;
        }

        // Option IDs are persisted as strings (SaveAnswerRequest casts selected_options.* to string),
        // so compare them type-agnostically against the correct option IDs.
        $correctIds = $question->options()
            ->where('is_correct', true)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();
        $selected = array_map(fn ($id) => (string) $id, (array) $this->selected_options);

        sort($correctIds);
        sort($selected);

        return $correctIds === $selected;
    }
}
