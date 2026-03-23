<?php

namespace App\Filament\Resources\Meninggals;

use App\Filament\Resources\Meninggals\Pages\CreateMeninggal;
use App\Filament\Resources\Meninggals\Pages\EditMeninggal;
use App\Filament\Resources\Meninggals\Pages\ListMeninggals;
use App\Filament\Resources\Meninggals\Schemas\MeninggalForm;
use App\Filament\Resources\Meninggals\Tables\MeninggalsTable;
use App\Models\Meninggal;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MeninggalResource extends Resource
{
    protected static ?string $model = Meninggal::class;

    protected static ?string $slug = 'meninggal';

    protected static ?string $navigationLabel = 'Meninggal';

    protected static ?string $pluralModelLabel = 'Meninggal';

    protected static string | UnitEnum | null $navigationGroup = 'DATA';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserMinus;


    public static function form(Schema $schema): Schema
    {
        return MeninggalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MeninggalsTable::configure($table);
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
            'index' => ListMeninggals::route('/'),
            'create' => CreateMeninggal::route('/create'),
            'edit' => EditMeninggal::route('/{record}/edit'),
        ];
    }
}
