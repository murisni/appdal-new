<?php

namespace App\Filament\Resources\Meninggals\Pages;

use App\Filament\Resources\Meninggals\MeninggalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMeninggals extends ListRecords
{
    protected static string $resource = MeninggalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make()
            //     ->color('danger')
            //     ->label('LAPOR MENINGGAL'),
        ];
    }
}
