<?php

namespace App\Filament\Resources\BPNTS\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\BPNT;
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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Section;

class BPNTSTable
{
    public static function getTabs(): array
    {
        $user = auth()->user();

        $baseQuery = fn() => BPNT::whereHas('dtks', function ($q) use ($user) {
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
                TextColumn::make('kepala_keluarga')
                    ->label('Kepala Keluarga')
                    ->state(
                        fn(BPNT $record): string =>
                        collect($record->dtks->anggota_keluarga ?? [])
                            ->firstWhere('status_hubungan', 'Kepala Keluarga')['nama'] ?? 'Tidak Terdata'
                    )
                    ->searchable(
                        query: fn($query, string $search) =>
                        $query->whereHas('dtks', fn($q) => $q->where('anggota_keluarga', 'like', "%{$search}%"))
                    )
                    ->sortable(
                        query: fn($query, string $direction) =>
                        $query->orderBy('dtks_id', $direction)
                    )
                    ->description(
                        fn(BPNT $record): string =>
                        $record->dtks
                            ? 'NIK: ' . (collect($record->dtks->anggota_keluarga ?? [])->firstWhere('status_hubungan', 'Kepala Keluarga')['nik'] ?? '-') . ' | No. KK: ' . $record->dtks->no_kk
                            : ''
                    ),

                TextColumn::make('no_kartu_kks')
                    ->label('No. KKS')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Verifikasi')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'ditinjau' => 'warning',
                        'diterima' => 'success',
                        'ditolak'  => 'danger',
                        default    => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'ditinjau' => 'heroicon-m-clock',
                        'diterima' => 'heroicon-m-check-circle',
                        'ditolak'  => 'heroicon-m-x-circle',
                        default    => 'heroicon-m-question-mark-circle',
                    })
                    ->searchable(),

                TextColumn::make('status_pangan')
                    ->label('Status Sembako')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'terima' => 'success',
                        'tidak'  => 'danger',
                        default  => 'gray',
                    }),

                TextColumn::make('catatan_surveyor')
                    ->label('Catatan')
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),
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
                        ->label(fn($record) => empty($record->status_pangan) ? 'Tambah Data' : 'Edit Data')
                        ->icon(fn($record) => empty($record->status_pangan) ? 'heroicon-m-plus-circle' : 'heroicon-m-pencil-square')
                        ->color(fn($record) => empty($record->status_pangan) ? 'success' : 'gray'),

                    Action::make('lanjut_survey')
                        ->label('Lanjut ke Survey')
                        ->icon('heroicon-m-arrow-right-circle')
                        ->color('primary')
                        ->modalSubmitAction(fn($action) => $action->color('warning'))
                        ->modalHeading('Instruksi Peninjauan')
                        ->form([
                            Textarea::make('alasan_tinjauan_kembali')->label('⚠️ Alasan Dikembalikan (Riwayat Tinjauan Ulang)')->disabled()->visible(fn($record) => filled($record->alasan_tinjauan_kembali)),
                            Textarea::make('catatan_peninjau')->label('Catatan untuk Surveyor')->placeholder('Misal: Tolong cek kelayakan KKS dan penerimaan sembako...')->required(),
                        ])
                        ->fillForm(fn($record) => ['alasan_tinjauan_kembali' => $record->alasan_tinjauan_kembali, 'catatan_peninjau' => $record->catatan_peninjau])
                        ->visible(function ($record) {
                            $user = auth()->user();
                            return ($user->hasRole('super_admin') || $user->can('LanjutSurveyBpnt'))
                                && $record->status === 'ditinjau' && !empty($record->status_pangan);
                        })
                        ->action(function (array $data, BPNT $record): void {
                            $record->update(['status' => 'diproses', 'catatan_peninjau' => $data['catatan_peninjau']]);
                            Notification::make()->title('Diteruskan ke Tim Survey')->success()->send();
                        }),

                    Action::make('lihat_catatan')
                        ->label('Catatan Peninjau')
                        ->icon('heroicon-m-document-text')
                        ->color('info')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup')
                        ->form([
                            Textarea::make('alasan_tinjauan_kembali')->label('⚠️ Alasan Dikembalikan')->disabled()->visible(fn($record) => filled($record->alasan_tinjauan_kembali)),
                            Textarea::make('catatan_peninjau')->label('Instruksi dari Peninjau Administrasi')->disabled(),
                        ])
                        ->visible(function ($record) {
                            $user = auth()->user();
                            return ($user->hasRole('super_admin') || $user->can('LihatCatatanBpnt'))
                                && $record->status === 'diproses' && $record->catatan_peninjau;
                        })
                        ->fillForm(fn($record) => ['alasan_tinjauan_kembali' => $record->alasan_tinjauan_kembali, 'catatan_peninjau' => $record->catatan_peninjau]),

                    Action::make('input_survey')
                        ->label('Input Hasil Survey')
                        ->icon('heroicon-m-clipboard-document-check')
                        ->color('warning')
                        ->modalHeading('Form Hasil Survey BPNT')
                        ->modalWidth('lg')
                        ->form([
                            Textarea::make('alasan_tinjauan_kembali')->label('⚠️ Alasan Minta Disurvey Ulang')->disabled()->columnSpanFull()->visible(fn($record) => filled($record->alasan_tinjauan_kembali)),
                            ToggleButtons::make('status')->label('Keputusan Akhir')->options(['diterima' => 'Diterima (Layak BPNT)', 'ditolak' => 'Ditolak (Tidak Layak)'])->colors(['diterima' => 'success', 'ditolak' => 'danger'])->icons(['diterima' => 'heroicon-m-check-circle', 'ditolak' => 'heroicon-m-x-circle'])->inline()->required(),
                            Textarea::make('catatan_surveyor')->label('Catatan Hasil Survey Lapangan/Berkas')->required(),
                        ])
                        ->fillForm(fn($record) => ['alasan_tinjauan_kembali' => $record->alasan_tinjauan_kembali, 'catatan_surveyor' => $record->catatan_surveyor])
                        ->visible(function ($record) {
                            $user = auth()->user();
                            return ($user->hasRole('super_admin') || $user->can('InputSurveyBpnt'))
                                && $record->status === 'diproses';
                        })
                        ->action(function (array $data, BPNT $record): void {
                            $record->update(['status' => $data['status'], 'catatan_surveyor' => $data['catatan_surveyor'], 'alasan_tinjauan_kembali' => null]);
                            Notification::make()->title('Data Survey Tersimpan')->success()->send();
                        }),

                    Action::make('tinjau_kembali')
                        ->label('Tinjau Kembali')
                        ->icon('heroicon-m-arrow-path-rounded-square')
                        ->color('secondary')
                        ->modalSubmitAction(fn($action) => $action->color('warning')->label('Simpan Status Baru'))
                        ->modalHeading('Tinjau Ulang Keputusan')
                        ->modalWidth('3xl')
                        ->form([
                            Grid::make(2)->schema([Textarea::make('catatan_peninjau')->label('Catatan Peninjau Awal')->disabled(), Textarea::make('catatan_surveyor')->label('Catatan Lapangan Sebelumnya')->disabled(),]),
                            ToggleButtons::make('status')->label('Ubah Status Keputusan')->options(['ditinjau' => 'Kembalikan ke Awal (Ditinjau Ulang)', 'diterima' => 'Ubah jadi Diterima', 'ditolak' => 'Ubah jadi Ditolak'])->colors(['ditinjau' => 'warning', 'diterima' => 'success', 'ditolak' => 'danger'])->inline()->required(),
                            Textarea::make('alasan_tinjauan_kembali')->label('Alasan Tinjauan Kembali')->placeholder('Jelaskan kenapa statusnya diubah...')->required()->columnSpanFull(),
                        ])
                        ->fillForm(fn($record) => ['catatan_peninjau' => $record->catatan_peninjau, 'catatan_surveyor' => $record->catatan_surveyor, 'status' => $record->status, 'alasan_tinjauan_kembali' => $record->alasan_tinjauan_kembali])
                        ->visible(function ($record) {
                            $user = auth()->user();
                            return ($user->hasRole('super_admin') || $user->can('TinjauKembaliBpnt'))
                                && in_array($record->status, ['diterima', 'ditolak']);
                        })
                        ->action(function (array $data, BPNT $record): void {
                            $record->update(['status' => $data['status'], 'alasan_tinjauan_kembali' => $data['alasan_tinjauan_kembali']]);
                            Notification::make()->title('Status Diperbarui')->success()->send();
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
