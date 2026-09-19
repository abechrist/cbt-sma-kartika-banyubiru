<?php

namespace App\Filament\Widgets;

use App\Models\Question;
use Filament\Widgets;

class QuestionsChart extends Widgets\ChartWidget
{
    protected static ?string $heading = 'Distribusi Jenis Soal';

    protected function getData(): array
    {
        $types = [
            'pg' => 'Pilihan Ganda',
            'pg_kompleks' => 'Pilihan Ganda Kompleks',
            'benar_salah' => 'Benar Salah',
            'menjodohkan' => 'Menjodohkan',
            'isian_singkat' => 'Isian Singkat',
            'esai' => 'Esai',
        ];

        $labels = [];
        $data = [];
        foreach ($types as $type => $label) {
            $count = Question::where('type', $type)->where('is_active', true)->count();
            if ($count > 0) {
                $labels[] = $label;
                $data[] = $count;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Soal',
                    'data' => $data,
                    'backgroundColor' => [
                        '#3b82f6',
                        '#f59e0b',
                        '#10b981',
                        '#8b5cf6',
                        '#06b6d4',
                        '#ef4444',
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
