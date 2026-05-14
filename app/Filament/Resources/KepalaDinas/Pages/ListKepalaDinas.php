<?php

namespace App\Filament\Resources\KepalaDinas\Pages;

use App\Filament\Resources\KepalaDinas\KepalaDinasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKepalaDinas extends ListRecords
{
    protected static string $resource = KepalaDinasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
