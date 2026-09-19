<?php

namespace App\Filament\Widgets;

use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\User;
use Filament\Widgets;

class StatsOverview extends Widgets\StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Widgets\StatsOverviewWidget\Stat::make('Total Pengguna', $this->getTotalUsers())
                ->description('Semua pengguna aktif')
                ->icon('heroicon-o-users')
                ->color('primary'),
            Widgets\StatsOverviewWidget\Stat::make('Total Soal', $this->getTotalQuestions())
                ->description('Soal aktif dalam bank soal')
                ->icon('heroicon-o-question-mark-circle')
                ->color('success'),
            Widgets\StatsOverviewWidget\Stat::make('Total Ujian', $this->getTotalExams())
                ->description('Semua ujian yang tersedia')
                ->icon('heroicon-o-document-text')
                ->color('info'),
            Widgets\StatsOverviewWidget\Stat::make('Total Sesi Ujian', $this->getTotalSessions())
                ->description('Semua sesi ujian yang tercatat')
                ->icon('heroicon-o-calendar-days')
                ->color('warning'),
        ];
    }

    protected function getTotalUsers(): int
    {
        return User::count();
    }

    protected function getTotalQuestions(): int
    {
        return Question::where('is_active', true)->count();
    }

    protected function getTotalExams(): int
    {
        return Exam::count();
    }

    protected function getTotalSessions(): int
    {
        return ExamSession::count();
    }
}
