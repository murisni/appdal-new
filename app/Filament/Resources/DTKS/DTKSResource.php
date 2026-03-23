<?php

namespace App\Filament\Resources\DTKS;

use App\Filament\Resources\DTKS\Pages\CreateDTKS;
use App\Filament\Resources\DTKS\Pages\EditDTKS;
use App\Filament\Resources\DTKS\Pages\ListDTKS;
use App\Filament\Resources\DTKS\Schemas\DTKSForm;
use App\Filament\Resources\DTKS\Tables\DTKSTable;
use App\Models\DTKS;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DTKSResource extends Resource
{
    protected static ?string $model = DTKS::class;

    protected static ?string $slug = 'dtks';

    protected static ?string $navigationLabel = 'DTKS';

    protected static ?string $pluralModelLabel = 'Data Terpadu Kesejahteraan Sosial';

    protected static string | UnitEnum | null $navigationGroup = 'DATA';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    public static function form(Schema $schema): Schema
    {
        return DTKSForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DTKSTable::configure($table);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        /** @var \App\Models\User|null $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        if ($user && $user->hasRole('user')) {
            $query->where('kecamatan', $user->kecamatan);
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
            'index' => ListDTKS::route('/'),
            'create' => CreateDTKS::route('/create'),
            'edit' => EditDTKS::route('/{record}/edit'),
        ];
    }
}
