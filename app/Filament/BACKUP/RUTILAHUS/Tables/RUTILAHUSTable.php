<?php

namespace App\Filament\Resources\RUTILAHUS\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\RUTILAHU;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Actions\DeleteAction;

class RUTILAHUSTable
{
    public static function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua Data')
                ->icon('heroicon-m-list-bullet'),

            'ditinjau' => Tab::make('Ditinjau')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'ditinjau'))
                ->badge(fn() => RUTILAHU::where('status', 'ditinjau')->count())
                ->badgeColor('gray')
                ->icon('heroicon-m-clock'),

            'diproses' => Tab::make('Sedang Disurvey')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'diproses'))
                ->badge(fn() => RUTILAHU::where('status', 'diproses')->count())
                ->badgeColor('warning')
                ->icon('heroicon-m-arrow-path'),

            'diterima' => Tab::make('Diterima')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'diterima'))
                ->badge(fn() => RUTILAHU::where('status', 'diterima')->count())
                ->badgeColor('success')
                ->icon('heroicon-m-check-circle'),

            'ditolak' => Tab::make('Ditolak')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'ditolak'))
                ->badge(fn() => RUTILAHU::where('status', 'ditolak')->count())
                ->badgeColor('danger')
                ->icon('heroicon-m-x-circle'),
        ];
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('dtks.nama')->label('Nama Pemilik')->searchable(),
                TextColumn::make('status_kepemilikan_tanah')->label('Tanah'),
                TextColumn::make('anggaran_disetujui')
                    ->label('Anggaran')
                    ->money('IDR'),
                ImageColumn::make('foto_sesudah_renovasi')
                    ->label('Hasil Bedah'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->label(fn($record) => empty($record->status_kepemilikan_tanah) ? 'Tambah Data' : 'Edit Data')
                    ->icon(fn($record) => empty($record->status_kepemilikan_tanah) ? 'heroicon-m-plus-circle' : 'heroicon-m-pencil-square')
                    ->color(fn($record) => empty($record->status_kepemilikan_tanah) ? 'success' : 'gray'),

                Action::make('lanjut_survey')
                    ->label('Lanjut ke Survey')
                    ->icon('heroicon-m-arrow-right-circle')
                    ->color('primary')
                    ->modalSubmitAction(fn($action) => $action->color('warning'))
                    ->visible(fn($record) => $record->status === 'ditinjau' && !empty($record->status_kepemilikan_tanah))
                    ->form([
                        Textarea::make('alasan_tinjauan_kembali')->label('⚠️ Alasan Dikembalikan')->disabled()->visible(fn($record) => filled($record->alasan_tinjauan_kembali)),
                        Textarea::make('catatan_peninjau')->label('Catatan untuk Surveyor')->placeholder('Cek material rumah dan status tanah...')->required(),
                    ])
                    ->fillForm(fn($record) => ['alasan_tinjauan_kembali' => $record->alasan_tinjauan_kembali, 'catatan_peninjau' => $record->catatan_peninjau])
                    ->action(function (array $data, RUTILAHU $record): void {
                        $record->update(['status' => 'diproses', 'catatan_peninjau' => $data['catatan_peninjau']]);
                        Notification::make()->title('Diteruskan ke Tim Survey')->success()->send();
                    }),

                Action::make('lihat_catatan')
                    ->label('Catatan Peninjau')
                    ->icon('heroicon-m-document-text')
                    ->color('info')
                    ->visible(fn($record) => $record->status === 'diproses' && $record->catatan_peninjau)
                    ->modalSubmitAction(false)
                    ->form([
                        Textarea::make('catatan_peninjau')->label('Instruksi dari Peninjau Administrasi')->disabled(),
                    ])
                    ->fillForm(fn($record) => ['catatan_peninjau' => $record->catatan_peninjau]),

                Action::make('input_survey')
                    ->label('Input Hasil Survey')
                    ->icon('heroicon-m-clipboard-document-check')
                    ->color('warning')
                    ->modalWidth('lg')
                    ->visible(fn($record) => $record->status === 'diproses')
                    ->form([
                        Textarea::make('alasan_tinjauan_kembali')->label('⚠️ Alasan Minta Disurvey Ulang')->disabled()->visible(fn($record) => filled($record->alasan_tinjauan_kembali)),
                        ToggleButtons::make('status')
                            ->label('Keputusan Akhir')
                            ->options(['diterima' => 'Diterima (Layak Bedah Rumah)', 'ditolak' => 'Ditolak (Tidak Layak)'])
                            ->colors(['diterima' => 'success', 'ditolak' => 'danger'])->inline()->required(),
                        Textarea::make('catatan_surveyor')->label('Catatan Hasil Survey Lapangan')->required(),
                    ])
                    ->fillForm(fn($record) => ['alasan_tinjauan_kembali' => $record->alasan_tinjauan_kembali, 'catatan_surveyor' => $record->catatan_surveyor])
                    ->action(function (array $data, RUTILAHU $record): void {
                        $record->update(['status' => $data['status'], 'catatan_surveyor' => $data['catatan_surveyor'], 'alasan_tinjauan_kembali' => null]);
                        Notification::make()->title('Data Survey Tersimpan')->success()->send();
                    }),

                Action::make('tinjau_kembali')
                    ->label('Tinjau Kembali')
                    ->icon('heroicon-m-arrow-path-rounded-square')
                    ->color('secondary')
                    ->visible(fn($record) => in_array($record->status, ['diterima', 'ditolak']))
                    ->form([
                        Grid::make(2)->schema([
                            Textarea::make('catatan_peninjau')->label('Catatan Peninjau Awal')->disabled(),
                            Textarea::make('catatan_surveyor')->label('Catatan Lapangan Sebelumnya')->disabled(),
                        ]),
                        ToggleButtons::make('status')
                            ->options(['ditinjau' => 'Kembalikan ke Awal (Ditinjau Ulang)', 'diterima' => 'Ubah jadi Diterima', 'ditolak'  => 'Ubah jadi Ditolak'])
                            ->colors(['ditinjau' => 'warning', 'diterima' => 'success', 'ditolak' => 'danger'])->inline()->required(),
                        Textarea::make('alasan_tinjauan_kembali')->label('Alasan Tinjauan Kembali')->required()->columnSpanFull(),
                    ])
                    ->fillForm(fn($record) => ['catatan_peninjau' => $record->catatan_peninjau, 'catatan_surveyor' => $record->catatan_surveyor, 'status' => $record->status, 'alasan_tinjauan_kembali' => $record->alasan_tinjauan_kembali])
                    ->action(function (array $data, RUTILAHU $record): void {
                        $record->update(['status' => $data['status'], 'alasan_tinjauan_kembali' => $data['alasan_tinjauan_kembali']]);
                        Notification::make()->title('Status Diperbarui')->success()->send();
                    }),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
