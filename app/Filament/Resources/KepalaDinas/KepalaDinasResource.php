<?php

namespace App\Filament\Resources\KepalaDinas;

use App\Filament\Resources\KepalaDinas\Pages\CreateKepalaDinas;
use App\Filament\Resources\KepalaDinas\Pages\EditKepalaDinas;
use App\Filament\Resources\KepalaDinas\Pages\ListKepalaDinas;
use App\Filament\Resources\KepalaDinas\Schemas\KepalaDinasForm;
use App\Filament\Resources\KepalaDinas\Tables\KepalaDinasTable;
use App\Models\KepalaDinas;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KepalaDinasResource extends Resource
{
    protected static ?string $model = KepalaDinas::class;

    protected static ?string $slug = 'kepaladinas';

    protected static ?string $navigationLabel = 'Kepala Dinas';

    protected static ?string $pluralModelLabel = 'KEPALA DINAS';

    protected static string | UnitEnum | null $navigationGroup = 'DATA';

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    public static function form(Schema $schema): Schema
    {
        return KepalaDinasForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KepalaDinasTable::configure($table);
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
            'index' => ListKepalaDinas::route('/'),
            'create' => CreateKepalaDinas::route('/create'),
            'edit' => EditKepalaDinas::route('/{record}/edit'),
        ];
    }
}
