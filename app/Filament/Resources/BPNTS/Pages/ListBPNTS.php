<?php

namespace App\Filament\Resources\BPNTS\Pages;

use App\Filament\Resources\BPNTS\BPNTResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\BPNTS\Tables\BPNTSTable;

class ListBPNTS extends ListRecords
{
    protected static string $resource = BPNTResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->color('danger')
                ->label('TAMBAH BPNT'),
        ];
    }

    public function getTabs(): array
    {
        return BPNTSTable::getTabs();
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'ditinjau';
    }
}
