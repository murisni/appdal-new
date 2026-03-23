<?php

namespace App\Filament\Resources\PKHS\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PKH;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Dotswan\MapPicker\Fields\Map;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class PKHSTable
{
    public static function getTabs(): array
    {
        $user = auth()->user();

        // Buat base query sesuai role
        $baseQuery = fn() => PKH::whereHas('dtks', function ($q) use ($user) {
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
                        fn(PKH $record): string =>
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
                        fn(PKH $record): string =>
                        $record->dtks
                            ? 'NIK: ' . (collect($record->dtks->anggota_keluarga ?? [])->firstWhere('status_hubungan', 'Kepala Keluarga')['nik'] ?? '-') . ' | No. KK: ' . $record->dtks->no_kk
                            : ''
                    ),

                IconColumn::make('ibu_hamil')
                    ->label('Hamil')
                    ->boolean()
                    ->alignCenter(),

                IconColumn::make('anak_usia_dini')
                    ->label('Balita')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('jumlah_sd')
                    ->label('SD')
                    ->numeric()
                    ->alignCenter(),

                TextColumn::make('jumlah_smp')
                    ->label('SMP')
                    ->numeric()
                    ->alignCenter(),

                TextColumn::make('jumlah_sma')
                    ->label('SMA')
                    ->numeric()
                    ->alignCenter(),

                TextColumn::make('status_penerima')
                    ->label('Kepesertaan')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'aktif' => 'success',
                        'graduasi' => 'info',
                        'belum aktif' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->filters([
                TernaryFilter::make('status_penerimaan')
                    ->label('Histori Penerima')
                    ->placeholder('Semua Data')
                    ->trueLabel('Sudah Diterima (Ada Histori)')
                    ->falseLabel('Belum Diterima Sama Sekali')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('histori_penerimaan')
                            ->where('histori_penerimaan', '!=', '[]'),
                        false: fn(Builder $query) => $query->whereNull('histori_penerimaan')
                            ->orWhere('histori_penerimaan', '[]'),
                        blank: fn(Builder $query) => $query,
                    )
                    ->native(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->label(fn($record) => empty($record->status_penerima) ? 'Tambah Data' : 'Edit Data')
                        ->icon(fn($record) => empty($record->status_penerima) ? 'heroicon-m-plus-circle' : 'heroicon-m-pencil-square')
                        ->color(fn($record) => empty($record->status_penerima) ? 'success' : 'gray'),

                    Action::make('lanjut_survey')
                        ->label('Lanjut ke Survey')
                        ->icon('heroicon-m-arrow-right-circle')
                        ->color('primary')
                        ->modalSubmitAction(fn($action) => $action->color('warning'))

                        ->visible(function ($record) {
                            $user = auth()->user();
                            return ($user->hasRole('super_admin') || $user->can('LanjutSurveyPkh'))
                                && $record->status === 'ditinjau'
                                && !empty($record->status_penerima);
                        })

                        ->modalHeading('Instruksi Peninjauan')
                        ->form([
                            Textarea::make('alasan_tinjauan_kembali')
                                ->label('⚠️ Alasan Dikembalikan (Riwayat Tinjauan Ulang)')
                                ->disabled()
                                ->visible(fn($record) => filled($record->alasan_tinjauan_kembali)),

                            Textarea::make('catatan_peninjau')
                                ->label('Catatan untuk Surveyor')
                                ->placeholder('Misal: Tolong cek detail kepemilikan aset motornya...')
                                ->required(),
                        ])
                        ->fillForm(fn($record) => [
                            'alasan_tinjauan_kembali' => $record->alasan_tinjauan_kembali,
                            'catatan_peninjau' => $record->catatan_peninjau,
                        ])
                        ->action(function (array $data, PKH $record): void {
                            $record->update([
                                'status' => 'diproses',
                                'catatan_peninjau' => $data['catatan_peninjau'],
                            ]);
                            Notification::make()->title('Diteruskan ke Tim Survey')->success()->send();
                        }),

                    Action::make('lihat_catatan')
                        ->label('Catatan Peninjau')
                        ->icon('heroicon-m-document-text')
                        ->color('info')
                        ->visible(function ($record) {
                            $user = auth()->user();
                            return ($user->hasRole('super_admin') || $user->can('LihatCatatanPkh'))
                                && $record->status === 'diproses'
                                && $record->catatan_peninjau;
                        })
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup')
                        ->form([
                            Textarea::make('alasan_tinjauan_kembali')
                                ->label('⚠️ Alasan Dikembalikan (Riwayat Tinjauan Ulang)')
                                ->disabled()
                                ->visible(fn($record) => filled($record->alasan_tinjauan_kembali)),

                            Textarea::make('catatan_peninjau')
                                ->label('Instruksi dari Peninjau Administrasi')
                                ->disabled(),
                        ])
                        ->fillForm(fn($record) => [
                            'alasan_tinjauan_kembali' => $record->alasan_tinjauan_kembali,
                            'catatan_peninjau' => $record->catatan_peninjau,
                        ]),

                    Action::make('input_survey')
                        ->label('Input Hasil Survey')
                        ->icon('heroicon-m-clipboard-document-check')
                        ->color('warning')
                        ->modalHeading('Form Hasil Survey PKH')
                        ->modalWidth('lg')
                        ->visible(function ($record) {
                            $user = auth()->user();
                            return ($user->hasRole('super_admin') || $user->can('InputSurveyPkh'))
                                && $record->status === 'diproses';
                        })->form([
                            Textarea::make('alasan_tinjauan_kembali')
                                ->label('⚠️ Alasan Minta Disurvey Ulang')
                                ->disabled()
                                ->columnSpanFull()
                                ->visible(fn($record) => filled($record->alasan_tinjauan_kembali)),

                            ToggleButtons::make('status')
                                ->label('Keputusan Akhir')
                                ->options([
                                    'diterima' => 'Diterima (Layak PKH)',
                                    'ditolak' => 'Ditolak (Tidak Layak)',
                                ])
                                ->colors(['diterima' => 'success', 'ditolak' => 'danger'])
                                ->icons(['diterima' => 'heroicon-m-check-circle', 'ditolak' => 'heroicon-m-x-circle'])
                                ->inline()
                                ->required(),

                            Textarea::make('catatan_surveyor')
                                ->label('Catatan Hasil Survey Lapangan/Berkas')
                                ->required(),
                        ])
                        ->fillForm(fn($record) => [
                            'alasan_tinjauan_kembali' => $record->alasan_tinjauan_kembali,
                            'catatan_surveyor' => $record->catatan_surveyor,
                        ])
                        ->action(function (array $data, PKH $record): void {
                            $record->update([
                                'status' => $data['status'],
                                'catatan_surveyor' => $data['catatan_surveyor'],
                                'alasan_tinjauan_kembali' => null, // Kosongkan alasan karena sudah disurvey
                            ]);
                            Notification::make()->title('Data Survey Tersimpan')->success()->send();
                        }),

                    Action::make('tinjau_kembali')
                        ->label('Tinjau Kembali')
                        ->icon('heroicon-m-arrow-path-rounded-square')
                        ->color('secondary')
                        ->modalSubmitAction(fn($action) => $action->color('warning')->label('Simpan Status Baru'))
                        ->modalHeading('Tinjau Ulang Keputusan')
                        ->modalWidth('3xl')
                        ->visible(function ($record) {
                            $user = auth()->user();
                            return ($user->hasRole('super_admin') || $user->can('TinjauKembaliPkh'))
                                && in_array($record->status, ['diterima', 'ditolak']);
                        })->form([
                            Grid::make(2)->schema([
                                Textarea::make('catatan_peninjau')->label('Catatan Peninjau Awal')->disabled(),
                                Textarea::make('catatan_surveyor')->label('Catatan Lapangan Sebelumnya')->disabled(),
                            ]),

                            ToggleButtons::make('status')
                                ->label('Ubah Status Keputusan')
                                ->options([
                                    'ditinjau' => 'Kembalikan ke Awal (Ditinjau Ulang)',
                                    'diterima' => 'Ubah jadi Diterima',
                                    'ditolak'  => 'Ubah jadi Ditolak',
                                ])
                                ->colors([
                                    'ditinjau' => 'warning',
                                    'diterima' => 'success',
                                    'ditolak' => 'danger'
                                ])
                                ->inline()
                                ->required(),

                            Textarea::make('alasan_tinjauan_kembali')
                                ->label('Alasan Tinjauan Kembali')
                                ->placeholder('Jelaskan kenapa statusnya diubah atau kenapa minta disurvey ulang...')
                                ->required()
                                ->columnSpanFull(),
                        ])
                        ->fillForm(fn($record) => [
                            'catatan_peninjau' => $record->catatan_peninjau,
                            'catatan_surveyor' => $record->catatan_surveyor,
                            'status' => $record->status,
                            'alasan_tinjauan_kembali' => $record->alasan_tinjauan_kembali,
                        ])
                        ->action(function (array $data, PKH $record): void {
                            $record->update([
                                'status' => $data['status'],
                                'alasan_tinjauan_kembali' => $data['alasan_tinjauan_kembali'],
                            ]);
                            Notification::make()->title('Status Diperbarui')->success()->send();
                        }),
                    DeleteAction::make(),
                ]),


            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
