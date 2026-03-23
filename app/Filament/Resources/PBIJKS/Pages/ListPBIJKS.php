<?php

namespace App\Filament\Resources\PBIJKS\Pages;

use App\Filament\Resources\PBIJKS\PBIJKResource;
use App\Filament\Resources\PBIJKS\Tables\PBIJKSTable;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPBIJKS extends ListRecords
{
    protected static string $resource = PBIJKResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->color('danger')
                ->label('TAMBAH PBIJK'),
        ];
    }

    public function getTabs(): array
    {
        return PBIJKSTable::getTabs();
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'ditinjau';
    }
}
