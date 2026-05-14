<?php

namespace App\Filament\Resources\KepalaDinas\Pages;

use App\Filament\Resources\KepalaDinas\KepalaDinasResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKepalaDinas extends EditRecord
{
    protected static string $resource = KepalaDinasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
