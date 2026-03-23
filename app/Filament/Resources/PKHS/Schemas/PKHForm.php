<?php

namespace App\Filament\Resources\PKHS\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Textarea;
use App\Models\DTKS;
use Carbon\Carbon;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class PKHForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Form PKH')
                    ->tabs([
                        Tab::make('Data KPM')
                            ->icon('heroicon-m-users')
                            ->schema([
                                Select::make('dtks_id')
                                    ->label('Pilih Keluarga (No. KK / Nama DTKS)')
                                    ->relationship(
                                        name: 'dtks',
                                        titleAttribute: 'no_kk',
                                        modifyQueryUsing: fn(Builder $query) => $query->where('status', 'diterima')
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live() // Wajib ada untuk memicu update instan
                                    // PERUBAHAN 2: Hapus '?string' pada $state agar bisa membaca ID Angka
                                    ->afterStateUpdated(function (Set $set, $state) {

                                        // Jika dikosongkan, kembalikan ke nilai awal
                                        if (!$state) {
                                            $set('jumlah_sd', 0);
                                            $set('jumlah_smp', 0);
                                            $set('jumlah_sma', 0);
                                            $set('anak_usia_dini', false);
                                            $set('lanjut_usia', false);
                                            $set('disabilitas_berat', false);
                                            return;
                                        }

                                        try {
                                            $dtks = DTKS::find($state);

                                            if ($dtks) {
                                                $sd = 0;
                                                $smp = 0;
                                                $sma = 0;
                                                $balita = false;
                                                $lansia = false;

                                                $anggotaKeluarga = $dtks->anggota_keluarga ?? [];

                                                if (is_array($anggotaKeluarga) && count($anggotaKeluarga) > 0) {
                                                    foreach ($anggotaKeluarga as $anggota) {
                                                        // Pastikan field tanggal_lahir ada dan tidak kosong
                                                        if (!empty($anggota['tanggal_lahir'])) {
                                                            try {
                                                                $umur = Carbon::parse($anggota['tanggal_lahir'])->age;

                                                                if ($umur <= 6) $balita = true;
                                                                elseif ($umur >= 7 && $umur <= 12) $sd++;
                                                                elseif ($umur >= 13 && $umur <= 15) $smp++;
                                                                elseif ($umur >= 16 && $umur <= 18) $sma++;
                                                                elseif ($umur >= 60) $lansia = true;
                                                            } catch (\Exception $e) {
                                                                // Abaikan jika format tanggal salah agar form tidak crash
                                                                continue;
                                                            }
                                                        }
                                                    }
                                                }

                                                // Inject data ke form (Paksa update form)
                                                $set('jumlah_sd', $sd);
                                                $set('jumlah_smp', $smp);
                                                $set('jumlah_sma', $sma);
                                                $set('anak_usia_dini', $balita);
                                                $set('lanjut_usia', (bool) ($lansia || $dtks->ada_lansia_disabilitas));
                                                $set('disabilitas_berat', (bool) $dtks->ada_lansia_disabilitas);

                                                // PERUBAHAN 3: Tampilkan Notifikasi Pop-up Berhasil
                                                Notification::make()
                                                    ->title('Deteksi Keluarga Berhasil!')
                                                    ->body("Sistem melacak: $sd anak SD, $smp anak SMP, $sma anak SMA. Silakan cek Tab Kriteria PKH.")
                                                    ->success()
                                                    ->send();
                                            }
                                        } catch (\Exception $e) {
                                            // Mencatat error ke log Laravel jika ada yang salah
                                            Log::error('Error saat auto-fill PKH: ' . $e->getMessage());
                                        }
                                    })
                                    ->helperText('Hanya menampilkan data DTKS yang statusnya Diterima / Layak.'),
                            ]),
                        Tab::make('Kriteria PKH')
                            ->icon('heroicon-m-clipboard-document-list')
                            ->schema([
                                TextInput::make('jumlah_sd')->numeric()->default(0)->label('Jumlah Anak SD')->columnSpan(2),
                                TextInput::make('jumlah_smp')->numeric()->default(0)->label('Jumlah Anak SMP')->columnSpan(2),
                                TextInput::make('jumlah_sma')->numeric()->default(0)->label('Jumlah Anak SMA')->columnSpan(2),
                                Toggle::make('ibu_hamil')->label('Ibu Hamil')->columnSpan(1),
                                Toggle::make('anak_usia_dini')->label('Anak Usia Dini (0-6 thn)')->columnSpan(1),
                                Toggle::make('disabilitas_berat')->label('Disabilitas Berat')->columnSpan(1),
                                Toggle::make('lanjut_usia')->label('Lanjut Usia (60+)')->columnSpan(1),
                            ])->columns(2),

                        Tab::make('Verifikasi Survey')
                            ->icon('heroicon-m-check-badge')
                            ->schema([
                                ToggleButtons::make('status')
                                    ->label('Status Kelayakan PKH')
                                    ->options([
                                        'ditinjau' => 'Ditinjau',
                                        'diterima' => 'Diterima',
                                        'ditolak'  => 'Ditolak',
                                    ])
                                    ->colors([
                                        'ditinjau' => 'warning',
                                        'diterima' => 'success',
                                        'ditolak'  => 'danger',
                                    ])
                                    ->icons([
                                        'ditinjau' => 'heroicon-m-clock',
                                        'diterima' => 'heroicon-m-check-circle',
                                        'ditolak'  => 'heroicon-m-x-circle',
                                    ])
                                    ->inline()
                                    ->default('ditinjau')
                                    ->required(),

                                Select::make('status_penerima')
                                    ->label('Status Kepesertaan PKH')
                                    ->options([
                                        'belum aktif' => 'Belum Aktif (Proses)',
                                        'aktif' => 'Aktif Menerima Bantuan',
                                        'graduasi' => 'Graduasi (Sudah Mampu / Komponen Habis)',
                                    ])
                                    ->default('belum aktif')
                                    ->required()
                                    ->native(false),

                                Textarea::make('catatan_surveyor')
                                    ->label('Catatan Surveyor / Alasan')
                                    ->placeholder('Masukkan alasan hasil pengamatan di lapangan terkait komponen...')
                                    ->columnSpanFull()
                                    ->rows(3),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
