<?php

namespace App\Filament\Student\Pages;

use App\Models\ExamAttempt;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ExamHistory extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Riwayat Ujian';

    protected static ?string $title = 'Riwayat Ujian';

    protected static string $view = 'filament.student.pages.exam-history';

    protected function getViewData(): array
    {
        return [
            'attempts' => ExamAttempt::with(['session.exam', 'result'])
                ->where('user_id', Auth::id())
                ->whereIn('status', ['submitted', 'auto_submitted'])
                ->orderBy('submitted_at', 'desc')
                ->get(),
        ];
    }
}
