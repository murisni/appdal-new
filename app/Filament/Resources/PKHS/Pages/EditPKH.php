<?php

namespace App\Filament\Resources\PKHS\Pages;

use App\Filament\Resources\PKHS\PKHResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPKH extends EditRecord
{
    protected static string $resource = PKHResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
