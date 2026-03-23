<?php

namespace App\Filament\Resources\PKHS\Pages;

use App\Filament\Resources\PKHS\PKHResource;
use App\Filament\Resources\PKHS\Tables\PKHSTable;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPKHS extends ListRecords
{
    protected static string $resource = PKHResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->color('danger')
                ->label('TAMBAH PKH'),
        ];
    }


    public function getTabs(): array
    {
        return PKHSTable::getTabs();
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'ditinjau';
    }
}
