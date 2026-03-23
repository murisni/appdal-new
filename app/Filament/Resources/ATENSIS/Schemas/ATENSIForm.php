<?php

namespace App\Filament\Resources\ATENSIS\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use App\Models\DTKS;
use Filament\Schemas\Components\Utilities\Get;

class ATENSIForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Formulir ATENSI')
                    ->tabs([
                        // TAB 1: IDENTITAS PENERIMA KHUSUS
                        Tab::make('Penerima')
                            ->icon('heroicon-o-user')
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
                                    ->live()
                                    ->afterStateUpdated(fn(callable $set) => $set('nama_penerima', null))
                                    ->required(),

                                Select::make('nama_penerima')
                                    ->label('Pilih Anggota Keluarga')
                                    ->options(function (Get $get) {
                                        $dtksId = $get('dtks_id');
                                        if (!$dtksId) return [];

                                        $dtks = DTKS::find($dtksId);
                                        if (!$dtks || empty($dtks->anggota_keluarga)) return [];

                                        return collect($dtks->anggota_keluarga)
                                            ->pluck('nama', 'nama')
                                            ->toArray();
                                    })
                                    ->searchable()
                                    ->required()
                                    ->helperText('Pilih siapa yang sedang membutuhkan asesmen ATENSI.'),

                                Select::make('kategori')
                                    ->label('Kategori Kasus')
                                    ->options([
                                        'anak' => 'Anak (Yatim Piatu/Terlantar)',
                                        'lansia' => 'Lanjut Usia',
                                        'disabilitas' => 'Penyandang Disabilitas',
                                        'korban_bencana' => 'Korban Bencana/Kekerasan',
                                    ])
                                    ->required(),
                            ])->columns(2),

                        // TAB 2: DETAIL BANTUAN
                        Tab::make('Detail Bantuan')
                            ->icon('heroicon-o-gift')
                            ->schema([
                                Textarea::make('jenis_bantuan_diterima')
                                    ->label('Deskripsi Bantuan (Misal: Kursi Roda, Sembako Nutrisi)')
                                    ->required()
                                    ->rows(3)
                                    ->columnSpanFull(),
                                TextInput::make('nominal_bantuan')
                                    ->label('Estimasi Nilai Bantuan (Rp)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->helperText('Masukkan harga/nilai barang yang diberikan (opsional)'),
                            ]),

                        // TAB 3: VERIFIKASI ASESMEN
                        Tab::make('Verifikasi Asesmen')
                            ->icon('heroicon-m-check-badge')
                            ->schema([
                                ToggleButtons::make('status')
                                    ->label('Status Bantuan')
                                    ->options([
                                        'ditinjau' => 'Asesmen/Ditinjau',
                                        'diterima' => 'Disetujui/Diterima',
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
                                    ->label('Catatan Pekerja Sosial')
                                    ->placeholder('Masukkan hasil asesmen kondisi fisik / sosial penerima...')
                                    ->columnSpanFull()
                                    ->rows(4),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
