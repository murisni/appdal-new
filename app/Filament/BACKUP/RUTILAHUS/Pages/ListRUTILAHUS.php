<?php

namespace App\Filament\Resources\RUTILAHUS\Pages;

use App\Filament\Resources\RUTILAHUS\RUTILAHUResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\RUTILAHUS\Tables\RUTILAHUSTable;

class ListRUTILAHUS extends ListRecords
{
    protected static string $resource = RUTILAHUResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->color('danger')
                ->label('TAMBAH RUTILAHU'),
        ];
    }


    public function getTabs(): array
    {
        return RUTILAHUSTable::getTabs();
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'ditinjau';
    }
}
