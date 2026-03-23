<?php

namespace App\Filament\Resources\DTKS\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Grid;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;

class DTKSForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('status')
                    ->default('ditinjau'),

                Tabs::make('Formulir DTKS')
                    ->tabs([
                        Tab::make('Kartu Keluarga')
                            ->icon('heroicon-o-identification')
                            ->schema([

                                Section::make('Informasi Kartu Keluarga')
                                    ->schema([
                                        TextInput::make('no_kk')
                                            ->label('Nomor Kartu Keluarga')
                                            ->required()
                                            ->maxLength(16),
                                    ])->columns(2),

                                Section::make('Alamat Lengkap Sesuai Karta Keluarga')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('alamat')
                                            ->label('Alamat')
                                            ->required()
                                            ->columnSpanFull(),
                                        TextInput::make('rt')
                                            ->label('RT')
                                            ->required()
                                            ->columnSpan(1),
                                        TextInput::make('rw')
                                            ->label('RW')
                                            ->required()
                                            ->columnSpan(1),
                                        Select::make('provinsi')
                                            ->options(['Kalimantan Tengah' => 'Kalimantan Tengah'])
                                            ->default('Kalimantan Tengah')
                                            ->selectablePlaceholder(false)
                                            ->required(),

                                        Select::make('kabupaten')
                                            ->options(['Kapuas' => 'Kapuas'])
                                            ->default('Kapuas')
                                            ->selectablePlaceholder(false)
                                            ->required(),

                                        Select::make('kecamatan')
                                            ->label('Kecamatan')
                                            ->options([
                                                'Selat' => 'Selat',
                                                'Kapuas Barat' => 'Kapuas Barat',
                                                'Kapuas Hilir' => 'Kapuas Hilir',
                                                'Kapuas Timur' => 'Kapuas Timur',
                                                'Kapuas Murung' => 'Kapuas Murung',
                                                'Kapuas Kuala' => 'Kapuas Kuala',
                                                'Mantangai' => 'Mantangai',
                                                'Timpah' => 'Timpah',
                                                'Kapuas Tengah' => 'Kapuas Tengah',
                                                'Kapuas Hulu' => 'Kapuas Hulu',
                                                'Tamban Catur' => 'Tamban Catur',
                                                'Basarang' => 'Basarang',
                                                'Pasak Talawang' => 'Pasak Talawang',
                                                'Mandau Talawang' => 'Mandau Talawang',
                                                'Dadahup' => 'Dadahup',
                                                'Bataguh' => 'Bataguh',
                                                'Tebing Tinggi' => 'Tebing Tinggi',
                                            ])
                                            ->reactive()
                                            ->required(),

                                        Select::make('kelurahan')
                                            ->label('Kelurahan / Desa')
                                            ->searchable()
                                            ->placeholder(fn(callable $get) => $get('kecamatan') ? 'Pilih Desa/Kelurahan' : 'Pilih Kecamatan dulu')
                                            ->options(function (callable $get) {
                                                $kecamatan = $get('kecamatan');

                                                $dataDesa = [
                                                    'Basarang' => ['Basarang', 'Basarang Jaya', 'Basungkai', 'Batu Nindan', 'Batuah', 'Bungai Jaya', 'Lunuk Ramba', 'Maluen', 'Naning', 'Panarung', 'Pangkalan Rekan', 'Pangkalan Sari', 'Tambun Raya', 'Tarung Manuah'],
                                                    'Bataguh' => ['Bamban Raya', 'Bangun Harjo', 'Budi Mufakat', 'Pulau Kupang', 'Pulau Mambulau', 'Sei Jangkit', 'Sei Lunuk', 'Tamban Luar', 'Terusan Baguntan Raya', 'Terusan Karya', 'Terusan Makmur', 'Terusan Mulya', 'Terusan Raya', 'Terusan Raya Barat', 'Terusan Raya Hulu'],
                                                    'Dadahup' => ['Bentuk Jaya', 'Bina Jaya', 'Dadahup', 'Dadahup Raya', 'Harapan Baru', 'Kahuripan Permai', 'Manuntung', 'Menteng Karya', 'Petak Batuah', 'Sumber Agung', 'Sumber Alaska', 'Tambak Bajai', 'Tanjung Harapan'],
                                                    'Kapuas Barat' => ['Mandomai', 'Anjir Kalampan', 'Basuta Raya', 'Maju Bersama', 'Pantai', 'Penda Katapi', 'Saka Mangkahai', 'Saka Tamiang', 'Sei Dusun', 'Sei Kayu', 'Sei Pitung', 'Teluk Hiri'],
                                                    'Kapuas Hilir' => ['Barimba', 'Dahirang', 'Hampatung', 'Mambulau', 'Sei Pasah', 'Bakungin', 'Saka Batur', 'Sei Asam'],
                                                    'Kapuas Hulu' => ['Barunang II', 'Bulau Ngandung', 'Dirung Koram', 'Hurung Tabengan', 'Hurung Tampang', 'Jakatan Pari', 'Katanjung', 'Mampai Jaya', 'Rahung Bungai', 'Sei Hanyu', 'Supang', 'Tangirang', 'Tumbang Puroh', 'Tumbang Sirat'],
                                                    'Kapuas Kuala' => ['Baranggau', 'Batanjung', 'Cemara Labat', 'Lupak Dalam', 'Lupak Timur', 'Palampai', 'Pematang', 'Sei Bakut', 'Sei Teras', 'Simpang Bunga', 'Tanjung', 'Tamban Baru Selatan', 'Tamban Lupak', 'Wargo Mulyo'],
                                                    'Kapuas Murung' => ['Palingkau Baru', 'Palingkau Lama', 'Belawang', 'Bina Karya', 'Bina Mekar', 'Bina Sejahtera', 'Bumi Rahayu', 'Karya Bersama', 'Mampai', 'Manggala Permai', 'Muara Dadahup', 'Palingkau Asri', 'Palingkau Jaya', 'Palingkau Sejahtera', 'Rawa Subur', 'Saka Binjai', 'Suka Mukti', 'Suka Reja', 'Sumber Mulya', 'Tajepan', 'Talekung Punei'],
                                                    'Kapuas Tengah' => ['Bajuh', 'Barunang', 'Buhut Jaya', 'Hurung Pukung', 'Karukus', 'Kayu Bulan', 'Kota Baru', 'Manis', 'Marapit', 'Masaran', 'Penda Muntei', 'Pujon', 'Tapen'],
                                                    'Kapuas Timur' => ['Anjir Mambulau Barat', 'Anjir Mambulau Tengah', 'Anjir Mambulau Timur', 'Anjir Serapat Barat', 'Anjir Serapat Baru', 'Anjir Serapat Tengah', 'Anjir Serapat Timur'],
                                                    'Mandau Talawang' => ['Karetau Manta\'a', 'Lawang Tamang', 'Masaha', 'Sei Pinang', 'Tanjung Rendan', 'Tumbang Bukoi', 'Tumbang Manyarung', 'Tumbang Tihis'],
                                                    'Mantangai' => ['Mantangai Hilir', 'Mantangai Tengah', 'Mantangai Hulu', 'Danau Rawah', 'Harapan Jaya', 'Kaladan Jaya', 'Katimpun', 'Lapai Jaya', 'Lamunti', 'Mantangai Permai', 'Mekar Jaya', 'Sari Makmur', 'Sei Dusun', 'Suka Maju', 'Tumbang Muroi'],
                                                    'Pasak Talawang' => ['Balai Banjang', 'Dandang', 'Hurung Kampin', 'Jangkang', 'Kaburan', 'Sei Ringin', 'Tumbang Diring', 'Tumbang Tukun'],
                                                    'Pulau Petak' => ['Anjir Palambang', 'Bunga Mawar', 'Handiwung', 'Narahan', 'Palangkai', 'Saka Lagun', 'Sei Tatas', 'Sei Tatas Hilir', 'Teluk Palinget'],
                                                    'Selat' => ['Selat Dalam', 'Selat Hilir', 'Selat Hulu', 'Selat Tengah', 'Selat Utara', 'Selat Barat', 'Murung Keramat', 'Panamas', 'Pulau Telo', 'Pulau Telo Baru'],
                                                    'Tamban Catur' => ['Bandar Raya', 'Sido Mulyo', 'Sidorejo', 'Tamban Baru Mekar', 'Tamban Baru Tengah', 'Tamban Baru Timur', 'Warna Sari'],
                                                    'Timpah' => ['Aruk', 'Batapah', 'Danau Pantau', 'Lawang Kajang', 'Lungkuh Layang', 'Petak Puti', 'Timpah', 'Tumbang Randang', 'Saka Kajang'],
                                                ];

                                                if (empty($kecamatan) || !isset($dataDesa[$kecamatan])) {
                                                    return [];
                                                }

                                                return collect($dataDesa[$kecamatan])->combine($dataDesa[$kecamatan])->toArray();
                                            })
                                            ->required(),
                                    ])->columns(2),
                                Toggle::make('is_survey_lokasi')
                                    ->label('Tambah Titik Koordinat')
                                    ->helperText('Aktifkan jika ingin menentukan lokasi rumah melalui peta (Survey)')
                                    ->reactive()
                                    ->dehydrated(false),

                                Grid::make(1)
                                    ->schema([
                                        Map::make('location')
                                            ->label('Pilih Lokasi Rumah')
                                            ->columnSpanFull()
                                            ->defaultLocation(latitude: -3.0068, longitude: 114.3816)
                                            ->dehydrated(false)
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                $set('latitude', $state['lat']);
                                                $set('longitude', $state['lng']);
                                            })
                                            ->afterStateHydrated(function ($state, $record, callable $set) {
                                                if ($record && $record->latitude && $record->longitude) {
                                                    $set('location', ['lat' => $record->latitude, 'lng' => $record->longitude]);
                                                    $set('is_survey_lokasi', true);
                                                }
                                            }),

                                        Grid::make(2)->schema([
                                            TextInput::make('latitude')
                                                ->label('Latitude')
                                                ->placeholder('Otomatis dari peta...')
                                                ->readonly()
                                                ->nullable(),

                                            TextInput::make('longitude')
                                                ->label('Longitude')
                                                ->placeholder('Otomatis dari peta...')
                                                ->readonly()
                                                ->nullable(),
                                        ]),
                                    ])
                                    ->visible(fn(callable $get) => $get('is_survey_lokasi')),
                            ]),

                        Tab::make('Anggota Keluarga')
                            ->icon('heroicon-o-users')
                            ->schema([
                                Section::make('Keterangan Sosial Ekonomi')
                                    ->schema([
                                        Repeater::make('anggota_keluarga')
                                            ->schema([
                                                Section::make('')
                                                    ->columns(2)
                                                    ->schema([
                                                        TextInput::make('nik')
                                                            ->label('NIK')
                                                            ->required()
                                                            ->maxLength(16),
                                                        TextInput::make('nama')->required(),
                                                        Select::make('jenis_kelamin')
                                                            ->options([
                                                                'L' => 'Laki-laki',
                                                                'P' => 'Perempuan'
                                                            ])
                                                            ->required(),
                                                        DatePicker::make('tanggal_lahir')->required(),
                                                        TextInput::make('tempat_lahir')->required(),
                                                        Select::make('status_hubungan')
                                                            ->label('Status Dalam Keluarga')
                                                            ->options([
                                                                'Kepala Keluarga' => 'Kepala Keluarga',
                                                                'Anggota Keluarga' => 'Anggota Keluarga',
                                                            ]),
                                                        Select::make('agama')
                                                            ->label('Agama')
                                                            ->options([
                                                                'Islam' => 'Islam',
                                                                'Kristen' => 'Kristen',
                                                                'Katolik' => 'Katolik',
                                                                'Hindu' => 'Hindu',
                                                                'Buddha' => 'Buddha',
                                                                'Konghucu' => 'Konghucu',
                                                            ])
                                                            ->required(),
                                                        Select::make('pendidikan')->options([
                                                            'SD Sederajat' => 'SD Sederajat',
                                                            'SMP Sederajat' => 'SMP Sederajat',
                                                            'SMA / SLTA Sederajat' => 'SMA / SLTA Sederajat',
                                                            'Akademik / D-III / Sarjana Muda' => 'Akademik / D-III / Sarjana Muda',
                                                            'D-IV / S-1' => 'D-IV / S-1',
                                                            'S-2' => 'S-2',
                                                            'S-3' => 'S-3',
                                                            'Lain-Lainnya' => 'Lain-Lainnya',
                                                        ])->required(),
                                                        Select::make('pekerjaan')->options([
                                                            'Belum / Tidak Bekerja' => 'Belum / Tidak Bekerja',
                                                            'Mengurus Rumah Tangga' => 'Mengurus Rumah Tangga',
                                                            'PNS' => 'PNS',
                                                            'TNI' => 'TNI',
                                                            'POLRI' => 'POLRI',
                                                            'Pensiunan' => 'Pensiunan',
                                                            'Pelajar / Mahasiswa' => 'Pelajar / Mahasiswa',
                                                            'Petani / Pekebun' => 'Petani / Pekebun',
                                                            'BUMN' => 'BUMN',
                                                            'Pedagang' => 'Pedagang',
                                                            'Peternak' => 'Peternak',
                                                            'Karyawan Swasta' => 'Karyawan Swasta',
                                                            'Buruh' => 'Buruh',
                                                            'Wiraswasta' => 'Wiraswasta',
                                                            'Lain-Lainnya' => 'Lain-Lainnya',
                                                        ])->required(),
                                                        FileUpload::make('file_ktp')
                                                            ->label('Foto KTP *jika sudah berusia 17 tahun ke atas')
                                                            ->disk('public')
                                                            ->directory('data-ktp'),
                                                    ]),
                                            ])->columnSpanFull()
                                            ->default([['type' => 'anggota_keluarga'],])
                                            ->addAction(
                                                fn($action) => $action
                                                    ->label('Tambah Anggota Keluarga')
                                                    ->color('warning')
                                            ),
                                    ]),
                            ]),

                        Tab::make('Ekonomi & Aset')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Section::make('Detail Pekerjaan (Kepala Keluarga / Tulang Punggung)')
                                    ->description('Jelaskan spesifikasi pekerjaan utama rumah tangga ini.')
                                    ->schema([
                                        TextInput::make('detail_pekerjaan')
                                            ->label('Spesifikasi Pekerjaan')
                                            ->placeholder('Misal: Penjual pentol keliling, Kuli angkut pasar, dll')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('nama_tempat_kerja')
                                            ->label('Nama Tempat Kerja / Perusahaan')
                                            ->placeholder('Misal: PT. Sawit Makmur, Pasar Beringin, dll (Boleh kosong jika serabutan)')
                                            ->maxLength(255),
                                    ])->columns(2),

                                Section::make('Kondisi Keuangan Rumah Tangga')
                                    ->description('Data penghasilan dan pengeluaran rata-rata per bulan.')
                                    ->schema([
                                        TextInput::make('penghasilan_per_bulan')
                                            ->label('Penghasilan Utama (Per Bulan)')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(0)
                                            ->required(),

                                        TextInput::make('penghasilan_lainnya')
                                            ->label('Penghasilan Tambahan (Per Bulan)')
                                            ->helperText('Misal: Hasil kebun, kiriman anak, dll')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(0),

                                        TextInput::make('pengeluaran_per_bulan')
                                            ->label('Pengeluaran Rata-rata (Per Bulan)')
                                            ->helperText('Perkiraan biaya makan, listrik, sekolah, dan sewa rumah.')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(0)
                                            ->required(),

                                        TextInput::make('jumlah_tanggungan')
                                            ->label('Jumlah Tanggungan')
                                            ->helperText('Jumlah orang yang menjadi tanggungan keluarga.')
                                            ->numeric()
                                            ->default(0)
                                            ->required(),

                                        Toggle::make('ada_lansia_disabilitas')
                                            ->label('Ada Lansia atau Penyandang Disabilitas?')
                                            ->onIcon('heroicon-m-check')
                                            ->offIcon('heroicon-m-x-mark'),
                                    ])->columns(2),

                                Section::make('Kriteria Fasilitas Rumah')
                                    ->schema([
                                        Select::make('status_kepemilikan_rumah')
                                            ->label('Status Kepemilikan Rumah')
                                            ->options([
                                                'Milik Sendiri' => 'Milik Sendiri',
                                                'Sewa / Kontrak' => 'Sewa / Kontrak',
                                                'Bebas Sewa (Numpang)' => 'Bebas Sewa (Menumpang Keluarga/Orang Lain)',
                                                'Dinas / Perusahaan' => 'Rumah Dinas / Perusahaan',
                                            ])
                                            ->required(),

                                        Select::make('daya_listrik')
                                            ->label('Daya Listrik Terpasang')
                                            ->options([
                                                'Non-PLN' => 'Tidak Ada Listrik',
                                                '450' => '450 VA',
                                                '900' => '900 VA',
                                                '1300' => '1300 VA',
                                                '2200' => '2200 VA atau lebih',
                                            ])->required(),

                                        Select::make('jenis_lantai')
                                            ->label('Jenis Lantai Terluas')
                                            ->options([
                                                'Tanah' => 'Tanah',
                                                'Bambu' => 'Bambu / Kayu Murah',
                                                'Semen' => 'Semen / Bata Merah',
                                                'Keramik' => 'Keramik / Granit',
                                            ])->required(),

                                        Select::make('sumber_air')
                                            ->label('Sumber Air Minum Utama')
                                            ->options([
                                                'Sungai' => 'Sungai / Danau',
                                                'Sumur' => 'Sumur Terbuka',
                                                'PDAM' => 'PDAM / Air Kemasan',
                                            ])->required(),

                                        TextInput::make('aset_lainnya')
                                            ->label('Kepemilikan Aset Berharga')
                                            ->placeholder('Misal: 1 Sepeda Motor, 2 Ekor Sapi, 1 Hektar Sawah (Kosongkan jika tidak ada)')
                                            ->columnSpanFull(),
                                    ])->columns(2),
                            ]),

                        Tab::make('Lampiran')
                            ->icon('heroicon-o-paper-clip')
                            ->schema([
                                Section::make('Upload Dokumen')
                                    ->schema([
                                        FileUpload::make('file_kk')->label('Kartu Keluarga')->directory('data-kk')->disk('public'),
                                        FileUpload::make('file_sktm')->label('Surat Keterangan Tidak Mampu')->directory('data-sktm')->disk('public'),
                                    ])->columns(3),

                                Section::make('Foto Kondisi Rumah')
                                    ->schema([
                                        FileUpload::make('foto_rumah_depan')->label('Foto Depan Rumah')->image()->directory('rumah')->disk('public'),
                                        FileUpload::make('foto_rumah_tamu')->label('Foto Ruang Tamu')->image()->directory('rumah')->disk('public'),
                                        FileUpload::make('foto_rumah_dapur')->label('Foto Dapur')->image()->directory('rumah')->disk('public'),
                                    ])->columns(3),
                            ]),
                        Tab::make('Verifikasi Survey')
                            ->icon('heroicon-o-check-badge')
                            ->visible(function () {
                                /** @var \App\Models\User $user */
                                $user = auth()->user();
                                return $user->hasRole('super_admin') || $user->hasRole('admin');
                            })
                            ->schema([
                                Section::make('Hasil Survey Lapangan')
                                    ->schema([
                                        ToggleButtons::make('status')
                                            ->label('Status Kelayakan Bantuan')
                                            ->options([
                                                'ditinjau' => 'Ditinjau',
                                                'diproses' => 'Sedang Disurvey',
                                                'diterima' => 'Diterima',
                                                'ditolak' => 'Ditolak',
                                            ])
                                            ->colors([
                                                'ditinjau' => 'warning',
                                                'diproses' => 'info',
                                                'diterima' => 'success',
                                                'ditolak' => 'danger',
                                            ])
                                            ->icons([
                                                'ditinjau' => 'heroicon-m-clock',
                                                'diproses' => 'heroicon-m-arrow-path',
                                                'diterima' => 'heroicon-m-check-circle',
                                                'ditolak' => 'heroicon-m-x-circle',
                                            ])
                                            ->inline()
                                            ->default('ditinjau')
                                            ->required(),
                                        Textarea::make('catatan_surveyor')
                                            ->label('Catatan Admin/Surveyor')
                                            ->placeholder('Contoh: Rumah sudah permanen, tidak layak dapat bantuan'),
                                    ])
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
