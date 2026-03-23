<?php

namespace App\Filament\Resources\ATENSIS\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\ATENSI;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Section;

class ATENSISTable
{
    public static function getTabs(): array
    {
        $user = auth()->user();

        $baseQuery = fn() => ATENSI::whereHas('dtks', function ($q) use ($user) {
            if ($user->hasRole('user')) {
                $q->where('kecamatan', $user->kecamatan);
            }
        });

        return [
            'semua' => Tab::make('Semua Data')
                ->icon('heroicon-m-list-bullet'),

            'ditinjau' => Tab::make('Ditinjau')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'ditinjau'))
                ->badge(fn() => $baseQuery()->where('status', 'ditinjau')->count())
                ->badgeColor('gray')
                ->icon('heroicon-m-clock'),

            'diproses' => Tab::make('Sedang Disurvey')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'diproses'))
                ->badge(fn() => $baseQuery()->where('status', 'diproses')->count())
                ->badgeColor('warning')
                ->icon('heroicon-m-arrow-path'),

            'diterima' => Tab::make('Diterima')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'diterima'))
                ->badge(fn() => $baseQuery()->where('status', 'diterima')->count())
                ->badgeColor('success')
                ->icon('heroicon-m-check-circle'),

            'ditolak' => Tab::make('Ditolak')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'ditolak'))
                ->badge(fn() => $baseQuery()->where('status', 'ditolak')->count())
                ->badgeColor('danger')
                ->icon('heroicon-m-x-circle'),
        ];
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_penerima')
                    ->label('Nama Penerima')
                    ->searchable()
                    ->sortable()
                    ->description(
                        fn(ATENSI $record): string =>
                        $record->dtks
                            ? 'NIK: ' . (collect($record->dtks->anggota_keluarga ?? [])
                                ->firstWhere('status_hubungan', 'Kepala Keluarga')['nik'] ?? '-') . ' | No. KK: ' . $record->dtks->no_kk
                            : ''
                    ),

                TextColumn::make('kategori')
                    ->badge()
                    ->color('info'),

                TextColumn::make('jenis_bantuan_diterima')
                    ->label('Bantuan')
                    ->limit(50),
            ])
            ->filters([
                TernaryFilter::make('status_penerimaan')
                    ->label('Histori Penerima')
                    ->placeholder('Semua Data')
                    ->trueLabel('Sudah Diterima (Ada Histori)')
                    ->falseLabel('Belum Diterima Sama Sekali')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('histori_penerimaan')->where('histori_penerimaan', '!=', '[]'),
                        false: fn(Builder $query) => $query->whereNull('histori_penerimaan')->orWhere('histori_penerimaan', '[]'),
                        blank: fn(Builder $query) => $query,
                    )->native(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->label(fn($record) => empty($record->kategori) ? 'Tambah Data' : 'Edit Data')
                        ->icon(fn($record) => empty($record->kategori) ? 'heroicon-m-plus-circle' : 'heroicon-m-pencil-square')
                        ->color(fn($record) => empty($record->kategori) ? 'success' : 'gray'),

                    Action::make('lanjut_survey')
                        ->label('Lanjut ke Survey')
                        ->icon('heroicon-m-arrow-right-circle')
                        ->color('primary')
                        ->modalSubmitAction(fn($action) => $action->color('warning'))
                        ->form([
                            Textarea::make('alasan_tinjauan_kembali')->label('⚠️ Alasan Dikembalikan')->disabled()->visible(fn($record) => filled($record->alasan_tinjauan_kembali)),
                            Textarea::make('catatan_peninjau')->label('Catatan untuk Surveyor')->placeholder('Cek kelayakan disabilitas...')->required(),
                        ])
                        ->visible(function ($record) {
                            $user = auth()->user();
                            return ($user->hasRole('super_admin') || $user->can('LanjutSurveyAtensi'))
                                && $record->status === 'ditinjau' && !empty($record->kategori);
                        })
                        ->fillForm(fn($record) => ['alasan_tinjauan_kembali' => $record->alasan_tinjauan_kembali, 'catatan_peninjau' => $record->catatan_peninjau])
                        ->action(function (array $data, ATENSI $record): void {
                            $record->update(['status' => 'diproses', 'catatan_peninjau' => $data['catatan_peninjau']]);
                            Notification::make()->title('Diteruskan ke Tim Survey')->success()->send();
                        }),

                    Action::make('lihat_catatan')
                        ->label('Catatan Peninjau')
                        ->icon('heroicon-m-document-text')
                        ->color('info')
                        ->modalSubmitAction(false)
                        ->form([
                            Textarea::make('catatan_peninjau')->label('Instruksi dari Peninjau Administrasi')->disabled(),
                        ])
                        ->fillForm(fn($record) => ['catatan_peninjau' => $record->catatan_peninjau])
                        ->visible(function ($record) {
                            $user = auth()->user();
                            return ($user->hasRole('super_admin') || $user->can('LihatCatatanAtensi'))
                                && $record->status === 'diproses' && $record->catatan_peninjau;
                        }),

                    Action::make('input_survey')
                        ->label('Input Hasil Survey')
                        ->icon('heroicon-m-clipboard-document-check')
                        ->color('warning')
                        ->modalWidth('lg')
                        ->form([
                            Textarea::make('alasan_tinjauan_kembali')->label('⚠️ Alasan Minta Disurvey Ulang')->disabled()->visible(fn($record) => filled($record->alasan_tinjauan_kembali)),
                            ToggleButtons::make('status')->label('Keputusan Akhir')->options(['diterima' => 'Diterima (Layak ATENSI)', 'ditolak' => 'Ditolak (Tidak Layak)'])->colors(['diterima' => 'success', 'ditolak' => 'danger'])->inline()->required(),
                            Textarea::make('catatan_surveyor')->label('Catatan Hasil Survey Lapangan')->required(),
                        ])
                        ->fillForm(fn($record) => ['alasan_tinjauan_kembali' => $record->alasan_tinjauan_kembali, 'catatan_surveyor' => $record->catatan_surveyor])
                        ->action(function (array $data, ATENSI $record): void {
                            $record->update(['status' => $data['status'], 'catatan_surveyor' => $data['catatan_surveyor'], 'alasan_tinjauan_kembali' => null]);
                            Notification::make()->title('Data Survey Tersimpan')->success()->send();
                        })
                        ->visible(function ($record) {
                            $user = auth()->user();
                            return ($user->hasRole('super_admin') || $user->can('InputSurveyAtensi'))
                                && $record->status === 'diproses';
                        }),

                    Action::make('tinjau_kembali')
                        ->label('Tinjau Kembali')
                        ->icon('heroicon-m-arrow-path-rounded-square')
                        ->color('secondary')
                        ->form([
                            Grid::make(2)->schema([Textarea::make('catatan_peninjau')->label('Catatan Peninjau Awal')->disabled(), Textarea::make('catatan_surveyor')->label('Catatan Lapangan Sebelumnya')->disabled(),]),
                            ToggleButtons::make('status')->options(['ditinjau' => 'Kembalikan ke Awal (Ditinjau Ulang)', 'diterima' => 'Ubah jadi Diterima', 'ditolak' => 'Ubah jadi Ditolak'])->colors(['ditinjau' => 'warning', 'diterima' => 'success', 'ditolak' => 'danger'])->inline()->required(),
                            Textarea::make('alasan_tinjauan_kembali')->label('Alasan Tinjauan Kembali')->required()->columnSpanFull(),
                        ])
                        ->fillForm(fn($record) => ['catatan_peninjau' => $record->catatan_peninjau, 'catatan_surveyor' => $record->catatan_surveyor, 'status' => $record->status, 'alasan_tinjauan_kembali' => $record->alasan_tinjauan_kembali])
                        ->action(function (array $data, ATENSI $record): void {
                            $record->update(['status' => $data['status'], 'alasan_tinjauan_kembali' => $data['alasan_tinjauan_kembali']]);
                            Notification::make()->title('Status Diperbarui')->success()->send();
                        })
                        ->visible(function ($record) {
                            $user = auth()->user();
                            return ($user->hasRole('super_admin') || $user->can('TinjauKembaliAtensi'))
                                && in_array($record->status, ['diterima', 'ditolak']);
                        }),


                    DeleteAction::make(),
                ])->label('Aksi')->icon('heroicon-m-ellipsis-vertical')->tooltip('Klik untuk melihat menu'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
