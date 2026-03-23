<?php

namespace App\Filament\Resources\PKHS;

use App\Filament\Resources\PKHS\Pages\CreatePKH;
use App\Filament\Resources\PKHS\Pages\EditPKH;
use App\Filament\Resources\PKHS\Pages\ListPKHS;
use App\Filament\Resources\PKHS\Schemas\PKHForm;
use App\Filament\Resources\PKHS\Tables\PKHSTable;
use App\Models\PKH;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PKHResource extends Resource
{
    protected static ?string $model = PKH::class;

    protected static ?string $slug = 'pkh';

    protected static ?string $navigationLabel = 'PKH';

    protected static ?string $pluralModelLabel = 'Program Keluarga Harapan';

    protected static string | UnitEnum | null $navigationGroup = 'Data Bantuan';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    public static function form(Schema $schema): Schema
    {
        return PKHForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PKHSTable::configure($table);
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
            'index' => ListPKHS::route('/'),
            'create' => CreatePKH::route('/create'),
            'edit' => EditPKH::route('/{record}/edit'),
        ];
    }
}
