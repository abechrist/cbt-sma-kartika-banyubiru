<?php

namespace App\Filament\Widgets;

use App\Models\ExamSession;
use Filament\Widgets;

class SessionsChart extends Widgets\ChartWidget
{
    protected static ?string $heading = 'Status Sesi Ujian';

    protected function getData(): array
    {
        $statuses = [
            'scheduled' => 'Terjadwal',
            'open' => 'Dibuka',
            'in_progress' => 'Sedang Berlangsung',
            'completed' => 'Selesai',
            'finished' => 'Final',
            'cancelled' => 'Dibatalkan',
        ];

        $labels = [];
        $data = [];
        foreach ($statuses as $status => $label) {
            $count = ExamSession::where('status', $status)->count();
            if ($count > 0) {
                $labels[] = $label;
                $data[] = $count;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Sesi',
                    'data' => $data,
                    'backgroundColor' => [
                        '#f59e0b',
                        '#3b82f6',
                        '#10b981',
                        '#6b7280',
                        '#ef4444',
                        '#ec4899',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
