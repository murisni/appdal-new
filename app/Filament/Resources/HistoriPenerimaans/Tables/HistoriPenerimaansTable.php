<?php

namespace App\Filament\Resources\HistoriPenerimaans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use App\Models\HistoriPenerimaan;
use Filament\Actions\DeleteAction;
use Filament\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class HistoriPenerimaansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('dtks.no_kk')
                    ->label('No. KK / KPM')
                    ->searchable()
                    ->sortable()
                    ->description(
                        fn(HistoriPenerimaan $record): string =>
                        $record->dtks
                            ? 'Kec. ' . $record->dtks->kecamatan . ' — ' . $record->dtks->kelurahan
                            : ''
                    ),

                TextColumn::make('program')
                    ->label('Program')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'PKH'    => 'success',
                        'BPNT'   => 'info',
                        'PBI-JK' => 'warning',
                        'ATENSI' => 'danger',
                        default  => 'gray',
                    })
                    ->searchable(),

                TextColumn::make('tanggal_terima')
                    ->label('Tanggal Terima')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('periode_bantuan')
                    ->label('Periode')
                    ->searchable(),

                TextColumn::make('nominal_bantuan')
                    ->label('Nominal')
                    ->money('IDR')
                    ->default('-'),

                TextColumn::make('petugas_penyerah')
                    ->label('Petugas')
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('lokasi_penyerahan')
                    ->label('Lokasi')
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status_penerimaan')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'diterima' => 'success',
                        'tidak'    => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'diterima' => 'Diterima',
                        'tidak'    => 'Tidak Diterima',
                        default    => '-',
                    }),

                ImageColumn::make('foto_bukti')
                    ->label('Foto Bukti')
                    ->circular()
                    ->disk('public'),

                TextColumn::make('catatan_penerimaan')
                    ->label('Catatan')
                    ->limit(30)
                    ->placeholder('Tidak ada catatan')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Tgl Input')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('program')
                    ->label('Program Bantuan')
                    ->options([
                        'PKH'    => 'PKH',
                        'BPNT'   => 'BPNT',
                        'PBI-JK' => 'PBI-JK',
                        'ATENSI' => 'ATENSI',
                    ]),

                SelectFilter::make('status_penerimaan')
                    ->label('Status')
                    ->options([
                        'diterima' => 'Diterima',
                        'tidak'    => 'Tidak Diterima',
                    ]),

                SelectFilter::make('kecamatan')
                    ->label('Kecamatan')
                    ->relationship('dtks', 'kecamatan')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ])
            ])
            ->defaultSort('tanggal_terima', 'desc')
            ->emptyStateHeading('Belum ada histori penerimaan')
            ->emptyStateDescription('Tambahkan data histori penerimaan bantuan menggunakan tombol di atas.')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
