<?php

namespace App\Filament\Resources\HistoriPenerimaans;

use App\Filament\Resources\HistoriPenerimaans\Pages\CreateHistoriPenerimaan;
use App\Filament\Resources\HistoriPenerimaans\Pages\EditHistoriPenerimaan;
use App\Filament\Resources\HistoriPenerimaans\Pages\ListHistoriPenerimaans;
use App\Filament\Resources\HistoriPenerimaans\Schemas\HistoriPenerimaanForm;
use App\Filament\Resources\HistoriPenerimaans\Tables\HistoriPenerimaansTable;
use App\Models\HistoriPenerimaan;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HistoriPenerimaanResource extends Resource
{
    protected static ?string $model = HistoriPenerimaan::class;

    protected static ?string $slug = 'histori';

    protected static ?string $navigationLabel = 'Histori Penerimaan';

    protected static ?string $pluralModelLabel = 'Histori Penerimaan';

    protected static string | UnitEnum | null $navigationGroup = 'DATA';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsVertical;

    public static function form(Schema $schema): Schema
    {
        return HistoriPenerimaanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HistoriPenerimaansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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

    public static function getPages(): array
    {
        return [
            'index' => ListHistoriPenerimaans::route('/'),
            'create' => CreateHistoriPenerimaan::route('/create'),
            'edit' => EditHistoriPenerimaan::route('/{record}/edit'),
        ];
    }
}
