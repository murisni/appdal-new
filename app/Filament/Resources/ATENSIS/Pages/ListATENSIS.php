<?php

namespace App\Filament\Resources\ATENSIS\Pages;

use App\Filament\Resources\ATENSIS\ATENSIResource;
use App\Filament\Resources\ATENSIS\Tables\ATENSISTable;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListATENSIS extends ListRecords
{
    protected static string $resource = ATENSIResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->color('danger')
                ->label('TAMBAH ATENSIS'),
        ];
    }

    public function getTabs(): array
    {
        return ATENSISTable::getTabs();
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'ditinjau';
    }
}
