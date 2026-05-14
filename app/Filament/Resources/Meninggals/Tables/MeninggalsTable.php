<?php

namespace App\Filament\Resources\Meninggals\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Table;
use App\Models\Meninggal;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;

class MeninggalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('dtks.no_kk')
                    ->label('No. KK')
                    ->searchable()
                    ->sortable()
                    ->description(
                        fn(Meninggal $record): string =>
                        $record->dtks
                            ? 'Kec. ' . $record->dtks->kecamatan . ' — ' . $record->dtks->kelurahan
                            : ''
                    ),

                TextColumn::make('nama_almarhum')
                    ->label('Nama Almarhum/ah')
                    ->searchable()
                    ->description(
                        fn(Meninggal $record): string =>
                        'NIK: ' . ($record->nik_almarhum ?? '-') .
                            ' | ' . ($record->status_hubungan ?? '-')
                    ),

                TextColumn::make('tanggal_meninggal')
                    ->label('Tanggal Meninggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('nama_pengganti')
                    ->label('Ahli Waris / Pengganti')
                    ->default('Tidak Ada Pengganti')
                    ->description(
                        fn(Meninggal $record): string =>
                        $record->nama_pengganti
                            ? 'NIK: ' . ($record->nik_pengganti ?? '-') .
                            ' (' . ($record->hubungan_pengganti ?? '-') . ')'
                            : ''
                    ),

                TextColumn::make('program_terdampak')
                    ->label('Program Terdampak')
                    ->badge()
                    ->color('warning')
                    ->separator(','),

                ImageColumn::make('bukti_meninggal')
                    ->label('Bukti')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl(url('/images/no-image.png')),

                TextColumn::make('created_at')
                    ->label('Tgl Dicatat')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('kecamatan')
                    ->label('Kecamatan')
                    ->relationship('dtks', 'kecamatan')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('tanggal_meninggal', 'desc')
            ->emptyStateHeading('Belum ada data meninggal')
            ->emptyStateDescription('Data KPM yang meninggal akan muncul di sini setelah dilaporkan.')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
