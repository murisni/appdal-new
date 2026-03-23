<?php

namespace App\Filament\Resources\RUTILAHUS\Pages;

use App\Filament\Resources\RUTILAHUS\RUTILAHUResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRUTILAHU extends EditRecord
{
    protected static string $resource = RUTILAHUResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
