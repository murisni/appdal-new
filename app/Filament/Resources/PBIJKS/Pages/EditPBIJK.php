<?php

namespace App\Filament\Resources\PBIJKS\Pages;

use App\Filament\Resources\PBIJKS\PBIJKResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPBIJK extends EditRecord
{
    protected static string $resource = PBIJKResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
