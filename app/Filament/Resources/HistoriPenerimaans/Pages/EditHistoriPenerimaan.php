<?php

namespace App\Filament\Resources\HistoriPenerimaans\Pages;

use App\Filament\Resources\HistoriPenerimaans\HistoriPenerimaanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHistoriPenerimaan extends EditRecord
{
    protected static string $resource = HistoriPenerimaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
