<?php

namespace App\Filament\Resources\BPNTS;

use App\Filament\Resources\BPNTS\Pages\CreateBPNT;
use App\Filament\Resources\BPNTS\Pages\EditBPNT;
use App\Filament\Resources\BPNTS\Pages\ListBPNTS;
use App\Filament\Resources\BPNTS\Schemas\BPNTForm;
use App\Filament\Resources\BPNTS\Tables\BPNTSTable;
use App\Models\BPNT;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BPNTResource extends Resource
{
    protected static ?string $model = BPNT::class;

    protected static ?string $slug = 'bpnt';

    protected static ?string $navigationLabel = 'BPNT';

    protected static ?string $pluralModelLabel = 'Bantuan Pangan Non Tunai';

    protected static string | UnitEnum | null $navigationGroup = 'Data Bantuan';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    public static function form(Schema $schema): Schema
    {
        return BPNTForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BPNTSTable::configure($table);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        /** @var \App\Models\User|null $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        if ($user && $user->hasRole('user')) {
            $query->whereHas('dtks', fn($q) => $q->where('kecamatan', $user->kecamatan));
        }
        return $query;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBPNTS::route('/'),
            'create' => CreateBPNT::route('/create'),
            'edit' => EditBPNT::route('/{record}/edit'),
        ];
    }
}
