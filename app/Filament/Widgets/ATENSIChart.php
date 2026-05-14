<?php

namespace App\Filament\Widgets;

use App\Models\ATENSI;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ATENSIChart extends ChartWidget
{
    protected ?string $heading = 'Tren Penerima ATENSI';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = '1';
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = ATENSI::select(
            DB::raw('count(*) as total'),
            DB::raw("DATE_FORMAT(created_at, '%M') as month")
        )
            ->groupBy('month')
            ->orderByRaw('MIN(created_at) ASC')
            ->pluck('total', 'month')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Penerima Baru',
                    'data' => array_values($data),
                    'fill' => 'start',
                    'borderColor' => '#8b5cf6',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
