<?php

namespace App\Filament\Resources\DTKS\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Schemas\Components\Grid;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\ToggleButtons;
use Filament\Actions\ActionGroup;
use App\Models\DTKS;
use App\Models\PKH;
use App\Models\BPNT;
use App\Models\PBIJK;
use App\Models\ATENSI;
use App\Models\RUTILAHU;


class DTKSTable
{
    public static function getTabs(): array
    {
        $user = auth()->user();

        $baseQuery = fn() => DTKS::when(
            $user->hasRole('user'),
            fn($q) => $q->where('kecamatan', $user->kecamatan)
        );

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
                TextColumn::make('skor_prioritas')
                    ->label('Skor & Desil')
                    ->state(fn($record) => $record->hitungSkorLengkap() . ' Poin')
                    ->description(fn($record) => $record->estimasi_desil)
                    ->color(fn($record) => match (true) {
                        $record->hitungSkorLengkap() >= 70 => 'danger',
                        $record->hitungSkorLengkap() >= 40 => 'warning',
                        default => 'success',
                    })
                    ->sortable(),

                TextColumn::make('rekomendasi')
                    ->label('Rekomendasi Bantuan')
                    ->getStateUsing(fn($record) => $record->getRekomendasiBantuan())
                    ->badge()
                    ->color(function (string $state, $record): string {
                        return match ($state) {
                            'PKH'    => $record->pkh()->exists()    ? 'success' : 'info',
                            'BPNT'   => $record->bpnt()->exists()   ? 'success' : 'info',
                            'PBI-JK' => $record->pbijk()->exists()  ? 'success' : 'info',
                            'ATENSI' => $record->atensi()->exists() ? 'success' : 'info',
                            default  => 'info',
                        };
                    })
                    ->wrap(),

                TextColumn::make('no_kk')
                    ->label('No. KK')
                    ->searchable()
                    ->copyable()
                    ->sortable(),

                TextColumn::make('kepala_keluarga')
                    ->label('Kepala Keluarga')
                    ->state(function ($record) {
                        $anggota = $record->anggota_keluarga ?? [];
                        $kepala = collect($anggota)->firstWhere('status_hubungan', 'Kepala Keluarga');
                        return $kepala['nama'] ?? 'Tidak Terdata';
                    })
                    ->searchable(query: function ($query, string $search) {
                        return $query->where('anggota_keluarga', 'like', "%{$search}%");
                    }),

                TextColumn::make('kecamatan')
                    ->sortable(),

                TextColumn::make('kelurahan')
                    ->label('Desa/Lurah')
                    ->sortable(),

                TextColumn::make('latitude')
                    ->label('Lokasi')
                    ->formatStateUsing(fn($state) => $state ? '📍 Terpetakan' : '❌ Belum Survey')
                    ->color(fn($state) => $state ? 'success' : 'danger'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'ditinjau' => 'gray',
                        'diproses' => 'warning',
                        'diterima' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'ditinjau' => 'heroicon-m-clock',
                        'diproses' => 'heroicon-m-arrow-path',
                        'diterima' => 'heroicon-m-check-circle',
                        'ditolak' => 'heroicon-m-x-circle',
                        default => 'heroicon-m-question-mark-circle',
                    }),

                TextColumn::make('created_at')
                    ->label('Tgl Daftar')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // TextColumn::make('catatan_surveyor')
                //     ->label('Catatan')
                //     ->placeholder('Tidak ada catatan')
                //     ->limit(50)
            ])
            ->filters([
                //
            ])

            ->recordActions([
                Action::make('lanjut_survey')
                    ->label('Lanjut ke Survey')
                    ->icon('heroicon-m-arrow-right-circle')
                    ->color('primary')
                    ->modalSubmitAction(fn($action) => $action->color('warning'))
                    ->visible(function ($record) {
                        /** @var \App\Models\User $user */
                        $user = auth()->user();
                        return ($user->hasRole('super_admin') || $user->can('LanjutSurveyDtks')) && $record->status === 'ditinjau';
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
                    ->action(function (array $data, DTKS $record): void {
                        $record->update([
                            'status' => 'diproses',
                            'catatan_peninjau' => $data['catatan_peninjau'],
                        ]);
                        Notification::make()->title('Diteruskan ke Tim Survey')->success()->send();
                    }),

                // =========================================================
                // 2. TOMBOL SAAT STATUS: SEDANG DISURVEY (BACA CATATAN)
                // =========================================================
                Action::make('lihat_catatan')
                    ->label('Catatan Peninjau')
                    ->icon('heroicon-m-document-text')
                    ->color('info')
                    ->visible(function ($record) {
                        /** @var \App\Models\User $user */
                        $user = auth()->user();
                        return ($user->hasRole('super_admin') || $user->can('LihatCatatanDtks')) && $record->status === 'diproses' && $record->catatan_peninjau;
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

                // =========================================================
                // 3. TOMBOL SAAT STATUS: SEDANG DISURVEY (INPUT HASIL)
                // =========================================================
                Action::make('input_survey')
                    ->label('Input Hasil Survey')
                    ->icon('heroicon-m-map-pin')
                    ->color('warning')
                    ->modalHeading('Form Hasil Survey Lapangan')
                    ->modalWidth('3xl')
                    ->visible(function ($record) {
                        /** @var \App\Models\User $user */
                        $user = auth()->user();
                        return ($user->hasRole('super_admin') || $user->can('InputSurveyDtks')) && $record->status === 'diproses';
                    })
                    ->form([
                        Textarea::make('alasan_tinjauan_kembali')
                            ->label('⚠️ Alasan Minta Disurvey Ulang')
                            ->disabled()
                            ->columnSpanFull()
                            ->visible(fn($record) => filled($record->alasan_tinjauan_kembali)),

                        Grid::make(2)->schema([
                            Map::make('location')
                                ->label('Titik Lokasi Rumah')
                                ->columnSpanFull()
                                ->defaultLocation(latitude: -3.0068, longitude: 114.3816)
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $set('latitude', $state['lat']);
                                    $set('longitude', $state['lng']);
                                })
                                ->afterStateHydrated(function ($state, $record, callable $set) {
                                    if ($record && $record->latitude && $record->longitude) {
                                        $set('location', ['lat' => $record->latitude, 'lng' => $record->longitude]);
                                    }
                                })
                                ->dehydrated(false),
                            TextInput::make('latitude')->readonly(),
                            TextInput::make('longitude')->readonly(),
                        ]),
                        Grid::make(3)->schema([
                            FileUpload::make('foto_rumah_depan')->image()->directory('rumah'),
                            FileUpload::make('foto_rumah_tamu')->image()->directory('rumah'),
                            FileUpload::make('foto_rumah_dapur')->image()->directory('rumah'),
                        ]),
                        ToggleButtons::make('status')
                            ->label('Keputusan Akhir')
                            ->options([
                                'diterima' => 'Diterima (Layak)',
                                'ditolak' => 'Ditolak (Tidak Layak)',
                            ])
                            ->colors(['diterima' => 'success', 'ditolak' => 'danger'])
                            ->icons(['diterima' => 'heroicon-m-check-circle', 'ditolak' => 'heroicon-m-x-circle'])
                            ->inline()
                            ->required(),
                        Textarea::make('catatan_surveyor')->label('Catatan Hasil Survey')->required(),
                    ])
                    ->fillForm(fn($record) => [
                        'alasan_tinjauan_kembali' => $record->alasan_tinjauan_kembali,
                        'latitude' => $record->latitude,
                        'longitude' => $record->longitude,
                        'foto_rumah_depan' => $record->foto_rumah_depan,
                        'foto_rumah_tamu' => $record->foto_rumah_tamu,
                        'foto_rumah_dapur' => $record->foto_rumah_dapur,
                        'catatan_surveyor' => $record->catatan_surveyor,
                    ])
                    ->action(function (array $data, DTKS $record): void {
                        $record->update([
                            'latitude' => $data['latitude'],
                            'longitude' => $data['longitude'],
                            'foto_rumah_depan' => $data['foto_rumah_depan'],
                            'foto_rumah_tamu' => $data['foto_rumah_tamu'] ?? $record->foto_rumah_tamu,
                            'foto_rumah_dapur' => $data['foto_rumah_dapur'] ?? $record->foto_rumah_dapur,
                            'status' => $data['status'],
                            'catatan_surveyor' => $data['catatan_surveyor'],
                            // Kita kosongkan alasannya karena proses ulang sudah selesai
                            'alasan_tinjauan_kembali' => null,
                            'verified_at' => now(),
                        ]);
                        Notification::make()->title('Data Survey Tersimpan')->success()->send();
                    }),

                // =========================================================
                // 4. TOMBOL SAAT STATUS: DITERIMA / DITOLAK
                // =========================================================
                Action::make('tinjau_kembali')
                    ->label('Tinjau Kembali')
                    ->icon('heroicon-m-arrow-path-rounded-square')
                    ->color('secondary')
                    ->modalSubmitAction(fn($action) => $action->color('warning')->label('Simpan Status Baru'))
                    ->modalHeading('Tinjau Ulang Keputusan')
                    ->modalWidth('3xl')
                    ->visible(function ($record) {
                        /** @var \App\Models\User $user */
                        $user = auth()->user();
                        return ($user->hasRole('super_admin') || $user->can('TinjauKembaliDtks')) && in_array($record->status, ['diterima', 'ditolak']);
                    })
                    ->form([
                        Grid::make(2)->schema([
                            Textarea::make('catatan_peninjau')->label('Catatan Peninjau Awal')->disabled(),
                            Textarea::make('catatan_surveyor')->label('Catatan Lapangan Sebelumnya')->disabled(),
                        ]),

                        ToggleButtons::make('status')
                            ->label('Ubah Status Keputusan')
                            ->options([
                                'ditinjau' => 'Kembalikan ke Awal (Ditinjau Ulang)', // Opsi baru
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
                    ->action(function (array $data, DTKS $record): void {
                        $record->update([
                            'status' => $data['status'],
                            'alasan_tinjauan_kembali' => $data['alasan_tinjauan_kembali'],
                        ]);
                        Notification::make()->title('Status Diperbarui')->success()->send();
                    }),

                ActionGroup::make([
                    // ── PKH ──────────────────────────────────────────────
                    Action::make('ajukan_pkh')
                        ->label(
                            fn($record) => $record->pkh()->exists()
                                ? '✓ PKH (Sudah Diajukan)'
                                : 'Ajukan PKH'
                        )
                        ->icon(
                            fn($record) => $record->pkh()->exists()
                                ? 'heroicon-m-check-badge'
                                : 'heroicon-m-plus-circle'
                        )
                        ->color(fn($record) => $record->pkh()->exists() ? 'gray' : 'success')
                        ->badge(fn($record) => $record->pkh()->exists() ? 'Diajukan' : null)
                        ->badgeColor('success')
                        ->disabled(fn($record) => $record->pkh()->exists())
                        ->visible(fn($record) => in_array('PKH', $record->getRekomendasiBantuan()))
                        ->action(function ($record) {
                            $sd = 0;
                            $smp = 0;
                            $sma = 0;
                            $balita = false;
                            $lansia = false;

                            foreach ($record->anggota_keluarga ?? [] as $anggota) {
                                if (!empty($anggota['tanggal_lahir'])) {
                                    try {
                                        $umur = \Carbon\Carbon::parse($anggota['tanggal_lahir'])->age;
                                        if ($umur <= 6)                    $balita = true;
                                        elseif ($umur >= 7  && $umur <= 12) $sd++;
                                        elseif ($umur >= 13 && $umur <= 15) $smp++;
                                        elseif ($umur >= 16 && $umur <= 18) $sma++;
                                        elseif ($umur >= 60)                $lansia = true;
                                    } catch (\Exception $e) {
                                    }
                                }
                            }

                            $record->pkh()->create([
                                'status'           => 'ditinjau',
                                'jumlah_sd'        => $sd,
                                'jumlah_smp'       => $smp,
                                'jumlah_sma'       => $sma,
                                'anak_usia_dini'   => $balita,
                                'lanjut_usia'      => ($lansia || $record->ada_lansia_disabilitas),
                                'disabilitas_berat' => $record->ada_lansia_disabilitas,
                            ]);

                            Notification::make()
                                ->title('Berhasil Masuk Antrian PKH')
                                ->body('Data komponen PKH telah disinkronkan otomatis.')
                                ->success()
                                ->send();
                        }),

                    // ── BPNT ─────────────────────────────────────────────
                    Action::make('ajukan_bpnt')
                        ->label(
                            fn($record) => $record->bpnt()->exists()
                                ? '✓ BPNT (Sudah Diajukan)'
                                : 'Ajukan BPNT'
                        )
                        ->icon(
                            fn($record) => $record->bpnt()->exists()
                                ? 'heroicon-m-check-badge'
                                : 'heroicon-m-shopping-cart'
                        )
                        ->color(fn($record) => $record->bpnt()->exists() ? 'gray' : 'success')
                        ->badge(fn($record) => $record->bpnt()->exists() ? 'Diajukan' : null)
                        ->badgeColor('success')
                        ->disabled(fn($record) => $record->bpnt()->exists())
                        ->visible(fn($record) => in_array('BPNT', $record->getRekomendasiBantuan()))
                        ->action(function ($record) {
                            $record->bpnt()->create(['status' => 'ditinjau']);
                            Notification::make()
                                ->title('Berhasil Masuk ke Daftar Tunggu BPNT')
                                ->success()
                                ->send();
                        }),

                    // ── PBI-JK ───────────────────────────────────────────
                    Action::make('ajukan_pbijk')
                        ->label(
                            fn($record) => $record->pbijk()->exists()
                                ? '✓ PBI-JK (Sudah Diajukan)'
                                : 'Ajukan PBI-JK'
                        )
                        ->icon(
                            fn($record) => $record->pbijk()->exists()
                                ? 'heroicon-m-check-badge'
                                : 'heroicon-m-heart'
                        )
                        ->color(fn($record) => $record->pbijk()->exists() ? 'gray' : 'success')
                        ->badge(fn($record) => $record->pbijk()->exists() ? 'Diajukan' : null)
                        ->badgeColor('success')
                        ->disabled(fn($record) => $record->pbijk()->exists())
                        ->visible(fn($record) => in_array('PBI-JK', $record->getRekomendasiBantuan()))
                        ->action(function ($record) {
                            $record->pbijk()->create(['status' => 'ditinjau']);
                            Notification::make()
                                ->title('Berhasil Masuk ke Daftar Tunggu PBI-JK')
                                ->success()
                                ->send();
                        }),

                    // ── ATENSI ───────────────────────────────────────────
                    Action::make('ajukan_atensi')
                        ->label(
                            fn($record) => $record->atensi()->exists()
                                ? '✓ ATENSI (Sudah Diajukan)'
                                : 'Ajukan ATENSI'
                        )
                        ->icon(
                            fn($record) => $record->atensi()->exists()
                                ? 'heroicon-m-check-badge'
                                : 'heroicon-m-users'
                        )
                        ->color(fn($record) => $record->atensi()->exists() ? 'gray' : 'success')
                        ->badge(fn($record) => $record->atensi()->exists() ? 'Diajukan' : null)
                        ->badgeColor('success')
                        ->disabled(fn($record) => $record->atensi()->exists())
                        ->visible(fn($record) => in_array('ATENSI', $record->getRekomendasiBantuan()))
                        ->action(function ($record) {
                            $record->atensi()->create(['status' => 'ditinjau']);
                            Notification::make()
                                ->title('Berhasil Masuk ke Daftar Tunggu ATENSI')
                                ->success()
                                ->send();
                        }),
                ])
                    ->label('Ajukan Bantuan')
                    ->icon('heroicon-m-paper-airplane')
                    ->button()
                    ->color('primary')
                    ->visible(function ($record) {
                        /** @var \App\Models\User $user */
                        $user = auth()->user();
                        return ($user->hasRole('super_admin') || $user->can('AjukanBantuanDtks'))
                            && $record->status === 'diterima';
                    }),

                ActionGroup::make([
                    EditAction::make(),
                    ViewAction::make(),
                    DeleteAction::make(),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
