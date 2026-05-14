<?php

namespace App\Filament\Resources\KepalaDinas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class KepalaDinasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pangkat_golongan')
                    ->label('Golongan')
                    ->searchable(),
                TextColumn::make('periode_jabatan')
                    ->label('Periode')
                    ->searchable(),
                TextColumn::make('status_pejabat')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Definitif' => 'success',
                        'Plt' => 'warning',
                        'Plh' => 'info',
                        default => 'gray',
                    }),
                ImageColumn::make('foto_ttd')
                    ->label('Tanda Tangan')
                    ->disk('public'),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
