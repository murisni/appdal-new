<?php

namespace App\Filament\Resources\Meninggals\Pages;

use App\Filament\Resources\Meninggals\MeninggalResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMeninggal extends EditRecord
{
    protected static string $resource = MeninggalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
