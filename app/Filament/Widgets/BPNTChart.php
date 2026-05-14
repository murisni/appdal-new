<?php

namespace App\Filament\Widgets;

use App\Models\BPNT;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class BPNTChart extends ChartWidget
{
    protected ?string $heading = 'Sebaran Status BPNT';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = '1';
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = BPNT::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total',
                    'data' => [
                        $data['ditinjau'] ?? 0,
                        $data['diproses'] ?? 0,
                        $data['diterima'] ?? 0,
                        $data['ditolak'] ?? 0,
                    ],
                    'backgroundColor' => ['#94a3b8', '#f59e0b', '#10b981', '#ef4444'],
                ],
            ],
            'labels' => ['Ditinjau', 'Diproses', 'Diterima', 'Ditolak'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
