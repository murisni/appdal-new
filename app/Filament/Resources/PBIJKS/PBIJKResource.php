<?php

namespace App\Filament\Resources\PBIJKS;

use App\Filament\Resources\PBIJKS\Pages\CreatePBIJK;
use App\Filament\Resources\PBIJKS\Pages\EditPBIJK;
use App\Filament\Resources\PBIJKS\Pages\ListPBIJKS;
use App\Filament\Resources\PBIJKS\Schemas\PBIJKForm;
use App\Filament\Resources\PBIJKS\Tables\PBIJKSTable;
use App\Models\PBIJK;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PBIJKResource extends Resource
{
    protected static ?string $model = PBIJK::class;

    protected static ?string $slug = 'pbijk';

    protected static ?string $navigationLabel = 'PBIJK';

    protected static ?string $pluralModelLabel = 'Penerima Bantuan Iuran Jaminan Kesehatan';

    protected static string | UnitEnum | null $navigationGroup = 'Data Bantuan';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    public static function form(Schema $schema): Schema
    {
        return PBIJKForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PBIJKSTable::configure($table);
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
            'index' => ListPBIJKS::route('/'),
            'create' => CreatePBIJK::route('/create'),
            'edit' => EditPBIJK::route('/{record}/edit'),
        ];
    }
}
