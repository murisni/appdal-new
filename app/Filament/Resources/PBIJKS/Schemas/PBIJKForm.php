<?php

namespace App\Filament\Resources\PBIJKS\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use App\Models\DTKS;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Textarea;

class PBIJKForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Formulir PBI-JK')
                    ->tabs([
                        Tab::make('Data Peserta')
                            ->icon('heroicon-m-user')
                            ->schema([
                                Select::make('nama_penerima')
                                    ->label('Pilih Anggota Keluarga')
                                    ->options(function (Get $get) {
                                        $dtksId = $get('dtks_id');
                                        if (!$dtksId) return [];

                                        $dtks = DTKS::find($dtksId);
                                        if (!$dtks || empty($dtks->anggota_keluarga)) return [];

                                        // Pastikan formatnya diubah menjadi array jika terbaca sebagai string
                                        $anggotaKeluarga = is_string($dtks->anggota_keluarga)
                                            ? json_decode($dtks->anggota_keluarga, true)
                                            : $dtks->anggota_keluarga;

                                        // Jika gagal decode / tetap bukan array, kembalikan kosong
                                        if (!is_array($anggotaKeluarga)) return [];

                                        $options = [];
                                        foreach ($anggotaKeluarga as $anggota) {
                                            if (!empty($anggota['nama'])) {
                                                $options[$anggota['nama']] = $anggota['nama']; // Key dan Label sama
                                            }
                                        }

                                        return $options;
                                    })
                                    ->searchable()
                                    ->required()
                                    ->helperText('Pilih spesifik individu yang mendapatkan KIS/PBI-JK.'),

                                TextInput::make('nomor_bpjs')
                                    ->label('Nomor Kartu BPJS (KIS)')
                                    ->numeric()
                                    ->maxLength(13),

                                TextInput::make('faskes_tingkat_1')
                                    ->label('Faskes Tingkat 1 (Puskesmas/Klinik)'),
                            ])->columns(2),

                        Tab::make('Verifikasi PBI-JK')
                            ->icon('heroicon-m-check-badge')
                            ->schema([
                                ToggleButtons::make('status')
                                    ->label('Status PBI-JK')
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

                                Textarea::make('catatan_surveyor')
                                    ->label('Catatan Surveyor')
                                    ->placeholder('Misal: Kuota BPJS APBD penuh, atau NIK tidak valid di Dukcapil...')
                                    ->columnSpanFull()
                                    ->rows(3),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
