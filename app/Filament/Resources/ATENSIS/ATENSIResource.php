<?php

namespace App\Filament\Resources\ATENSIS;

use App\Filament\Resources\ATENSIS\Pages\CreateATENSI;
use App\Filament\Resources\ATENSIS\Pages\EditATENSI;
use App\Filament\Resources\ATENSIS\Pages\ListATENSIS;
use App\Filament\Resources\ATENSIS\Schemas\ATENSIForm;
use App\Filament\Resources\ATENSIS\Tables\ATENSISTable;
use App\Models\ATENSI;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ATENSIResource extends Resource
{
    protected static ?string $model = ATENSI::class;

    protected static ?string $slug = 'atensi';

    protected static ?string $navigationLabel = 'ATENSI';

    protected static ?string $pluralModelLabel = 'Asistensi Rehabilitasi Sosial';

    protected static string | UnitEnum | null $navigationGroup = 'Data Bantuan';

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    public static function form(Schema $schema): Schema
    {
        return ATENSIForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ATENSISTable::configure($table);
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
            'index' => ListATENSIS::route('/'),
            'create' => CreateATENSI::route('/create'),
            'edit' => EditATENSI::route('/{record}/edit'),
        ];
    }
}
