<?php

namespace App\Filament\Widgets;

use App\Models\PKH;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PKHChart extends ChartWidget
{
    protected ?string $heading = 'Statistik Program PKH';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = '1';
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = PKH::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah KPM',
                    'data' => [
                        $data['ditinjau'] ?? 0,
                        $data['diproses'] ?? 0,
                        $data['diterima'] ?? 0,
                        $data['ditolak'] ?? 0,
                    ],
                    'backgroundColor' => ['#94a3b8', '#f59e0b', '#22c55e', '#ef4444'],
                ],
            ],
            'labels' => ['Ditinjau', 'Diproses', 'Diterima', 'Ditolak'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
