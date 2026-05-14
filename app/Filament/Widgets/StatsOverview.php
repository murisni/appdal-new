<?php

namespace App\Filament\Widgets;

use App\Models\DTKS;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalKK = DTKS::count();

        $allAnggota = DTKS::pluck('anggota_keluarga');
        $males = 0;
        $females = 0;

        foreach ($allAnggota as $anggota) {
            $data = is_array($anggota) ? $anggota : json_decode($anggota, true);
            if (is_array($data)) {
                foreach ($data as $person) {
                    $jk = $person['jenis_kelamin'] ?? '';
                    if ($jk === 'L') $males++;
                    if ($jk === 'P') $females++;
                }
            }
        }

        return [
            Stat::make('Total Kartu Keluarga', $totalKK . ' KK')
                ->description('Total KPM Terdaftar')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('primary')
                ->chart([7, 3, 5, 2, 10, 3, 12]),
            Stat::make('Total Laki-Laki', $males . ' Jiwa')
                ->description('Anggota Keluarga L')
                ->descriptionIcon('heroicon-m-user')
                ->color('info')
                ->chart([3, 5, 2, 10, 5, 8, 15]),
            Stat::make('Total Perempuan', $females . ' Jiwa')
                ->description('Anggota Keluarga P')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning')
                ->chart([10, 2, 8, 3, 12, 4, 10]),
        ];
    }
}
