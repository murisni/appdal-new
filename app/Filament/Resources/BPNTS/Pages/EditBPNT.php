<?php

namespace App\Filament\Resources\BPNTS\Pages;

use App\Filament\Resources\BPNTS\BPNTResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBPNT extends EditRecord
{
    protected static string $resource = BPNTResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
