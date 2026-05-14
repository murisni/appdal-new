<?php

namespace App\Filament\Pages;

class Dashboard extends \Filament\Pages\Dashboard
{
    /**
     * Mengatur jumlah kolom widget pada halaman Dashboard menjadi 3
     */

    public function getColumns(): int | array
    {
        return 4;
    }
}
