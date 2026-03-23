<?php

namespace App\Filament\Resources\ATENSIS\Pages;

use App\Filament\Resources\ATENSIS\ATENSIResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditATENSI extends EditRecord
{
    protected static string $resource = ATENSIResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
