<?php

namespace App\Filament\Student\Pages;

use App\Models\ExamAttempt;
use App\Models\ExamResult as ExamResultModel;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ExamResult extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Hasil Ujian';

    protected static string $view = 'filament.student.pages.exam-result';

    public ExamAttempt $attempt;

    public ExamResultModel $result;

    protected function getViewData(): array
    {
        $attempt = $this->attempt;
        $attempt->load(['session.exam', 'result']);
        $result = $attempt->result;

        return [
            'attempt' => $attempt,
            'result' => $result,
            'hasPendingEssay' => $result->grading_status === 'partial',
        ];
    }

    public function mount(ExamAttempt $attempt): void
    {
        $this->attempt = $attempt;

        if ($attempt->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke hasil ujian ini.');
        }

        // If exam hasn't been submitted yet, redirect back
        if (! in_array($attempt->status, ['submitted', 'auto_submitted'])) {
            redirect()->route('student.exam.take', $attempt);
        }

        $this->result = $attempt->result;

        // If no result exists yet, create a placeholder (shouldn't happen in practice)
        if (! $this->result) {
            $this->result = ExamResultModel::create([
                'exam_attempt_id' => $attempt->id,
                'total_score' => 0,
                'max_possible_score' => 0,
                'percentage' => 0,
                'correct_count' => 0,
                'incorrect_count' => 0,
                'unanswered_count' => 0,
                'grading_status' => 'pending',
            ]);
        }
    }
}
