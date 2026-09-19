<?php

namespace App\Filament\Student\Pages;

use App\Events\AttemptHeartbeat;
use App\Events\AttemptStatusUpdated;
use App\Models\ActivityLog;
use App\Models\ExamAttempt;
use App\Services\ActivityLogger;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TakeExam extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationLabel = 'Mengikuti Ujian';

    protected static string $view = 'filament.student.pages.take-exam';

    public ExamAttempt $attempt;

    public array $questions = [];

    public int $currentIndex = 0;

    public ?string $answerText = null;

    public array $selectedOptions = [];

    public bool $isFlagged = false;

    public int $timeRemaining = 0;

    protected function getViewData(): array
    {
        $this->updateTimeRemaining();

        $attempt = $this->attempt;
        $attempt->load(['session.exam.questions.options']);

        $exam = $attempt->session->exam;
        $questions = $attempt->session->exam->questions;

        if ($exam->randomize_questions) {
            $questions = $questions->shuffle()->values();
        }

        foreach ($questions as $index => $question) {
            if ($exam->randomize_options) {
                $question->options = $question->options->shuffle()->values();
            }
            $question->number = $index + 1;
        }

        $attempt->load('answers');

        $this->currentIndex = min($this->currentIndex, count($questions) - 1);
        $currentQuestion = $questions[$this->currentIndex] ?? null;

        if ($currentQuestion) {
            $existingAnswer = $attempt->answers->where('question_id', $currentQuestion->id)->first();
            $this->answerText = $existingAnswer?->answer_text;
            $this->selectedOptions = (array) ($existingAnswer?->selected_options ?? []);
            $this->isFlagged = $existingAnswer?->is_flagged ?? false;
        }

        $this->timeRemaining = $attempt->getTimeRemaining();

        return [
            'attempt' => $attempt,
            'questions' => $questions,
            'currentQuestion' => $currentQuestion,
            'answeredCount' => $attempt->answers->count(),
            'totalQuestions' => count($questions),
        ];
    }

    public function mount(ExamAttempt $attempt): void
    {
        $this->attempt = $attempt;

        if ($attempt->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke ujian ini.');
        }

        if ($attempt->status === 'not_started') {
            redirect()->route('student.exam.token');
        }

        if (in_array($attempt->status, ['submitted', 'auto_submitted', 'expired'])) {
            redirect()->route('student.exam.result', $attempt);
        }

        $this->attempt->load(['session.exam.questions.options', 'answers']);
    }

    public function goToQuestion(int $index): void
    {
        $this->saveCurrentAnswer();
        $this->currentIndex = $index;
        $this->refreshCurrentQuestion();
    }

    public function nextQuestion(): void
    {
        $totalQuestions = count($this->attempt->session->exam->questions);
        if ($this->currentIndex < $totalQuestions - 1) {
            $this->saveCurrentAnswer();
            $this->currentIndex++;
            $this->refreshCurrentQuestion();
        }
    }

    public function previousQuestion(): void
    {
        if ($this->currentIndex > 0) {
            $this->saveCurrentAnswer();
            $this->currentIndex--;
            $this->refreshCurrentQuestion();
        }
    }

    public function saveCurrentAnswer(): void
    {
        $attempt = $this->attempt;
        $questions = $this->attempt->session->exam->questions;
        $currentQuestion = $questions[$this->currentIndex] ?? null;

        if (! $currentQuestion) {
            return;
        }

        $answer = $attempt->answers()->updateOrCreate(
            ['question_id' => $currentQuestion->id],
            [
                'answer_text' => $this->answerText,
                'selected_options' => $this->selectedOptions,
                'is_flagged' => $this->isFlagged,
                'answered_at' => now(),
            ]
        );

        $attempt->update(['last_seen_at' => now()]);
        broadcast(new AttemptStatusUpdated($attempt));
    }

    public function toggleFlag(): void
    {
        $this->isFlagged = ! $this->isFlagged;
        $this->saveCurrentAnswer();
    }

    public function submitExam(): void
    {
        $attempt = $this->attempt;

        if ($attempt->user_id !== Auth::id()) {
            return;
        }

        if (in_array($attempt->status, ['submitted', 'auto_submitted', 'cancelled'])) {
            redirect()->route('student.exam.result', $attempt);
        }

        $this->saveCurrentAnswer();

        DB::transaction(function () use ($attempt) {
            $attempt->update([
                'status' => 'submitted',
                'submitted_at' => now(),
                'ended_at' => now(),
            ]);

            $this->gradeExam($attempt);
            ActivityLogger::log(ActivityLog::ACTION_EXAM_SUBMITTED, 'Ujian dikumpulkan.', $attempt);
        });

        redirect()->route('student.exam.result', $attempt);
    }

    protected function refreshCurrentQuestion(): void
    {
        $questions = $this->attempt->session->exam->questions;
        $currentQuestion = $questions[$this->currentIndex] ?? null;

        if ($currentQuestion) {
            $existingAnswer = $this->attempt->answers->where('question_id', $currentQuestion->id)->first();
            $this->answerText = $existingAnswer?->answer_text;
            $this->selectedOptions = (array) ($existingAnswer?->selected_options ?? []);
            $this->isFlagged = $existingAnswer?->is_flagged ?? false;
        }
    }

    private function gradeExam(ExamAttempt $attempt): void
    {
        $exam = $attempt->session->exam;
        $totalScore = 0;
        $maxScore = 0;
        $correctCount = 0;
        $incorrectCount = 0;
        $unansweredCount = 0;
        $pendingEsaiCount = 0;

        foreach ($exam->questions as $question) {
            $maxScore += $question->pivot->score ?? $question->score;

            $answer = $attempt->answers()->where('question_id', $question->id)->first();

            if (! $answer || ($answer->answer_text === null && empty($answer->selected_options))) {
                $unansweredCount++;

                continue;
            }

            if ($question->isAutoGradable()) {
                $isCorrect = $answer->isCorrect();
                $score = $isCorrect ? ($question->pivot->score ?? $question->score) : 0;

                $answer->update(['score' => $score]);

                if ($isCorrect) {
                    $correctCount++;
                    $totalScore += $score;
                } else {
                    $incorrectCount++;
                }
            } else {
                if (($answer->score ?? null) !== null) {
                    $totalScore += $answer->score;
                } else {
                    $pendingEsaiCount++;
                }
            }
        }

        $percentage = $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0;

        $result = $attempt->result()->updateOrCreate(
            ['exam_attempt_id' => $attempt->id],
            [
                'total_score' => $totalScore,
                'max_possible_score' => $maxScore,
                'percentage' => $percentage,
                'correct_count' => $correctCount,
                'incorrect_count' => $incorrectCount,
                'unanswered_count' => $unansweredCount + $pendingEsaiCount,
                'grading_status' => $pendingEsaiCount > 0 ? 'partial' : 'completed',
            ]
        );
    }

    public function updateTimeRemaining(): void
    {
        $this->attempt->refresh();
        $this->timeRemaining = $this->attempt->getTimeRemaining();
    }

    public function heartbeat(): void
    {
        $this->updateTimeRemaining();
        $this->attempt->update(['last_seen_at' => now()]);
        broadcast(new AttemptHeartbeat($this->attempt));

        if ($this->timeRemaining <= 0 && $this->attempt->status === 'in_progress') {
            $this->autoSubmit();
        }
    }

    public function autoSubmit(): void
    {
        $attempt = $this->attempt;

        if (in_array($attempt->status, ['submitted', 'auto_submitted', 'cancelled'])) {
            return;
        }

        DB::transaction(function () use ($attempt) {
            $attempt->update([
                'status' => 'auto_submitted',
                'submitted_at' => now(),
                'ended_at' => now(),
            ]);

            $this->gradeExam($attempt);
            ActivityLogger::log(ActivityLog::ACTION_AUTO_SUBMITTED, 'Ujian dikumpulkan otomatis karena waktu habis.', $attempt);
        });

        redirect()->route('student.exam.result', $attempt);
    }

    public function getAnsweredQuestionsProperty(): array
    {
        return $this->attempt->answers->pluck('question_id')->toArray();
    }

    public function getFlaggedQuestionsProperty(): array
    {
        return $this->attempt->answers->where('is_flagged', true)->pluck('question_id')->toArray();
    }
}
