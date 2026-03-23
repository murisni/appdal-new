<?php

namespace App\Filament\Resources\RUTILAHUS;

use App\Filament\Resources\RUTILAHUS\Pages\CreateRUTILAHU;
use App\Filament\Resources\RUTILAHUS\Pages\EditRUTILAHU;
use App\Filament\Resources\RUTILAHUS\Pages\ListRUTILAHUS;
use App\Filament\Resources\RUTILAHUS\Schemas\RUTILAHUForm;
use App\Filament\Resources\RUTILAHUS\Tables\RUTILAHUSTable;
use App\Models\RUTILAHU;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RUTILAHUResource extends Resource
{
    protected static ?string $model = RUTILAHU::class;

    protected static ?string $slug = 'rutilahu';

    protected static ?string $navigationLabel = 'RUTILAHU';

    protected static ?string $pluralModelLabel = 'Rumah Tidak Layak Huni';

    protected static string | UnitEnum | null $navigationGroup = 'Data Bantuan';

    protected static ?int $navigationSort = 5;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHomeModern;

    public static function form(Schema $schema): Schema
    {
        return RUTILAHUForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RUTILAHUSTable::configure($table);
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
            'index' => ListRUTILAHUS::route('/'),
            'create' => CreateRUTILAHU::route('/create'),
            'edit' => EditRUTILAHU::route('/{record}/edit'),
        ];
    }
}
