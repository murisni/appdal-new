<?php

namespace App\Filament\Resources\RUTILAHUS\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use App\Models\DTKS;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Textarea;

class RUTILAHUForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Formulir Bedah Rumah (RUTILAHU)')
                    ->tabs([
                        // TAB 1: DATA PEMILIK RUMAH
                        Tab::make('Pemilik Rumah')
                            ->icon('heroicon-m-home-modern')
                            ->schema([
                                Select::make('dtks_id')
                                    ->label('Pilih Keluarga (No. KK DTKS)')
                                    ->relationship(
                                        name: 'dtks',
                                        titleAttribute: 'no_kk',
                                        modifyQueryUsing: fn(Builder $query) => $query->where('status', 'diterima')
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('Diambil dari data DTKS yang layak. Foto kondisi rumah sebelum direnovasi sudah ada di tabel DTKS.'),
                            ]),

                        // TAB 2: SYARAT & REALISASI
                        Tab::make('Realisasi & Syarat')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                Select::make('status_kepemilikan_tanah')
                                    ->label('Status Kepemilikan Tanah')
                                    ->options([
                                        'milik_sendiri' => 'Milik Sendiri (SHM / SKT)',
                                        'warisan' => 'Harta Warisan',
                                        'numpang' => 'Numpang Karang / Sewa (Tidak Layak)',
                                    ])
                                    ->required()
                                    ->helperText('Syarat mutlak bedah rumah adalah tanah milik sendiri.'),

                                TextInput::make('anggaran_disetujui')
                                    ->label('Anggaran Disetujui (Rp)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->placeholder('Misal: 20000000'),

                                FileUpload::make('foto_sesudah_renovasi')
                                    ->label('Foto Rumah Setelah Renovasi')
                                    ->directory('rutilahu_fotos')
                                    ->image()
                                    ->imageEditor()
                                    ->columnSpanFull(),
                            ])->columns(2),

                        // TAB 3: VERIFIKASI LAPANGAN
                        Tab::make('Verifikasi Lapangan')
                            ->icon('heroicon-m-check-badge')
                            ->schema([
                                ToggleButtons::make('status')
                                    ->label('Status Kelayakan RUTILAHU')
                                    ->options([
                                        'ditinjau' => 'Survei Lapangan',
                                        'diterima' => 'Disetujui / Direnovasi',
                                        'ditolak'  => 'Ditolak',
                                    ])
                                    ->colors([
                                        'ditinjau' => 'warning',
                                        'diterima' => 'success',
                                        'ditolak'  => 'danger',
                                    ])
                                    ->icons([
                                        'ditinjau' => 'heroicon-m-map-pin',
                                        'diterima' => 'heroicon-m-check-circle',
                                        'ditolak'  => 'heroicon-m-x-circle',
                                    ])
                                    ->inline()
                                    ->default('ditinjau')
                                    ->required(),

                                Textarea::make('catatan_surveyor')
                                    ->label('Catatan Tim Bedah Rumah')
                                    ->placeholder('Misal: Atap rumah rubuh, dinding bilik bambu, tanah memiliki SKT desa...')
                                    ->columnSpanFull()
                                    ->rows(4),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
