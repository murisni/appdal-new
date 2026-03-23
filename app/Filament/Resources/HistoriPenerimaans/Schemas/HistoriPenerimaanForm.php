<?php

namespace App\Filament\Resources\HistoriPenerimaans\Schemas;

use Filament\Schemas\Schema;
use App\Models\DTKS;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;


class HistoriPenerimaanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data KPM')
                    ->schema([
                        Select::make('dtks_id')
                            ->label('No. KK / Nama KPM')
                            ->options(function () {
                                return DTKS::where('status', 'diterima')
                                    ->get()
                                    ->mapWithKeys(function ($dtks) {
                                        $anggota = $dtks->anggota_keluarga ?? [];
                                        $kepala  = collect($anggota)->firstWhere('status_hubungan', 'Kepala Keluarga');
                                        $nama    = $kepala['nama'] ?? 'Tidak Terdata';
                                        return [$dtks->id => $dtks->no_kk . ' — ' . $nama];
                                    });
                            })
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),

                        Select::make('program')
                            ->label('Program Bantuan')
                            ->options([
                                'PKH'    => 'PKH (Program Keluarga Harapan)',
                                'BPNT'   => 'BPNT (Bantuan Pangan Non Tunai)',
                                'PBI-JK' => 'PBI-JK (Penerima Bantuan Iuran Jaminan Kesehatan)',
                                'ATENSI' => 'ATENSI (Asistensi Rehabilitasi Sosial)',
                            ])
                            ->required()
                            ->live(),

                        Select::make('status_penerimaan')
                            ->label('Status Penerimaan')
                            ->options([
                                'diterima' => 'Diterima',
                                'tidak'    => 'Tidak Diterima',
                            ])
                            ->default('diterima')
                            ->required(),
                    ])->columns(2),

                Section::make('Detail Penerimaan')
                    ->schema([
                        DatePicker::make('tanggal_terima')
                            ->label('Tanggal Terima')
                            ->default(now())
                            ->required(),

                        TextInput::make('periode_bantuan')
                            ->label('Periode Bantuan')
                            ->placeholder('Contoh: Januari 2026, Triwulan I 2026')
                            ->required(),

                        TextInput::make('nominal_bantuan')
                            ->label('Nominal Bantuan (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->nullable(),

                        TextInput::make('lokasi_penyerahan')
                            ->label('Lokasi Penyerahan')
                            ->placeholder('Contoh: Kantor Desa Selat Dalam')
                            ->nullable(),

                        TextInput::make('petugas_penyerah')
                            ->label('Petugas Penyerah')
                            ->placeholder('Nama petugas yang menyerahkan bantuan')
                            ->nullable(),
                    ])->columns(2),

                Section::make('Dokumentasi')
                    ->schema([
                        FileUpload::make('foto_bukti')
                            ->label('Foto Bukti Penerimaan')
                            ->image()
                            ->directory('bukti-penyerahan')
                            ->nullable()
                            ->columnSpanFull(),

                        Textarea::make('catatan_penerimaan')
                            ->label('Catatan')
                            ->placeholder('Keterangan tambahan jika ada...')
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
