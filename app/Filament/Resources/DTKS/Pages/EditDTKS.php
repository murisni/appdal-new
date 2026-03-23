<?php

namespace App\Filament\Resources\DTKS\Pages;

use App\Filament\Resources\DTKS\DTKSResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDTKS extends EditRecord
{
    protected static string $resource = DTKSResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
