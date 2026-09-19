<?php

namespace App\Filament\Widgets;

use App\Models\Exam;
use Filament\Widgets;

class ExamsByStatusChart extends Widgets\ChartWidget
{
    protected static ?string $heading = 'Status Ujian';

    protected function getData(): array
    {
        $statuses = [
            'draft' => 'Draft',
            'published' => 'Diterbitkan',
            'active' => 'Aktif',
            'archived' => 'Diarsipkan',
        ];

        $labels = [];
        $data = [];
        foreach ($statuses as $status => $label) {
            $count = Exam::where('status', $status)->count();
            if ($count > 0) {
                $labels[] = $label;
                $data[] = $count;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Ujian',
                    'data' => $data,
                    'backgroundColor' => [
                        '#6b7280',
                        '#3b82f6',
                        '#10b981',
                        '#ef4444',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
