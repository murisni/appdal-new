<?php

namespace App\Filament\Widgets;

use App\Models\PBIJK;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PBIJKChart extends ChartWidget
{
    protected ?string $heading = 'Proporsi Status PBI-JK';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = '1';
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = PBIJK::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Status',
                    'data' => [
                        $data['ditinjau'] ?? 0,
                        $data['diproses'] ?? 0,
                        $data['diterima'] ?? 0,
                        $data['ditolak'] ?? 0,
                    ],
                    'backgroundColor' => ['#cbd5e1', '#fbbf24', '#4ade80', '#f87171'],
                ],
            ],
            'labels' => ['Ditinjau', 'Diproses', 'Diterima', 'Ditolak'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
