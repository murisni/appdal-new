<?php

namespace App\Filament\Resources\DTKS\Pages;

use App\Filament\Resources\DTKS\DTKSResource;
use App\Filament\Resources\DTKS\Tables\DTKSTable;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

use App\Models\DTKS;
use App\Models\Meninggal;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Notifications\Notification;

class ListDTKS extends ListRecords
{
    protected static string $resource = DTKSResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->color('danger')
                ->label('TAMBAH DTKS'),

            Action::make('lapor_meninggal')
                ->label('Lapor Meninggal')
                ->icon('heroicon-o-user-minus')
                ->color('gray')
                ->modalHeading('Form Laporan Meninggal Dunia')
                ->modalWidth('3xl')
                ->modalSubmitActionLabel('Simpan Data')
                ->form([
                    Section::make('Cari Data KPM')
                        ->description('Pilih No. KK dari data DTKS yang sudah terdaftar.')
                        ->schema([
                            Select::make('dtks_id')
                                ->label('No. KK / Nama KPM')
                                ->hint(function () {
                                    $count = DTKS::where('status', 'diterima')
                                        ->with('meninggal')
                                        ->get()
                                        ->filter(function ($dtks) {
                                            $nikSudahMeninggal = $dtks->meninggal
                                                ->pluck('nik_almarhum')
                                                ->toArray();
                                            return collect($dtks->anggota_keluarga ?? [])
                                                ->filter(fn($a) => !in_array($a['nik'] ?? '', $nikSudahMeninggal))
                                                ->isNotEmpty();
                                        })
                                        ->count();
                                    return $count . ' KK tersedia';
                                })
                                ->hintColor('success')
                                ->hintIcon('heroicon-m-check-circle')
                                ->options(function () {
                                    return DTKS::where('status', 'diterima')
                                        ->with('meninggal')
                                        ->get()
                                        ->filter(function ($dtks) {
                                            $nikSudahMeninggal = $dtks->meninggal
                                                ->pluck('nik_almarhum')
                                                ->toArray();
                                            return collect($dtks->anggota_keluarga ?? [])
                                                ->filter(fn($a) => !in_array($a['nik'] ?? '', $nikSudahMeninggal))
                                                ->isNotEmpty();
                                        })
                                        ->mapWithKeys(function ($dtks) {
                                            $anggota = $dtks->anggota_keluarga ?? [];
                                            $kepala  = collect($anggota)->firstWhere('status_hubungan', 'Kepala Keluarga');
                                            $nama    = $kepala['nama'] ?? 'Tidak Terdata';
                                            return [$dtks->id => $dtks->no_kk . ' — ' . $nama];
                                        });
                                })
                                ->searchable()
                                ->live()
                                ->required()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $set('nik_almarhum', null);
                                    $set('nik_pengganti', null);
                                    $set('hubungan_pengganti', null);
                                }),
                        ]),

                    Section::make('Data Almarhum/Almarhumah')
                        ->schema([
                            Select::make('nik_almarhum')
                                ->label('Pilih Anggota yang Meninggal')
                                ->options(function (Get $get) {
                                    $dtksId = $get('dtks_id');
                                    if (!$dtksId) return [];

                                    $dtks = DTKS::find($dtksId);
                                    if (!$dtks) return [];

                                    $nikSudahMeninggal = $dtks->meninggal()
                                        ->pluck('nik_almarhum')
                                        ->toArray();

                                    $options = [];
                                    foreach ($dtks->anggota_keluarga ?? [] as $a) {
                                        if (!empty($a['nik']) && !in_array($a['nik'], $nikSudahMeninggal)) {
                                            $options[$a['nik']] = $a['nama'] .
                                                ' (' . ($a['status_hubungan'] ?? 'Anggota') . ')';
                                        }
                                    }
                                    return $options;
                                })
                                ->live()
                                ->required(),
                        ]),

                    Section::make('Keterangan Meninggal')
                        ->schema([
                            DatePicker::make('tanggal_meninggal')
                                ->label('Tanggal Meninggal')
                                ->required(),

                            FileUpload::make('bukti_meninggal')
                                ->label('Surat Keterangan Meninggal')
                                ->image()
                                ->directory('bukti-meninggal'),

                            Textarea::make('catatan')
                                ->label('Catatan Tambahan')
                                ->placeholder('Keterangan tambahan jika ada...')
                                ->columnSpanFull(),
                        ])->columns(2),

                    Section::make('Peralihan Bantuan')
                        ->description('Isi bagian ini jika almarhum adalah Kepala Keluarga dan bantuan perlu dialihkan ke ahli waris.')
                        ->schema([
                            Toggle::make('ada_pengganti')
                                ->label('Alihkan Bantuan ke Ahli Waris?')
                                ->live()
                                ->columnSpanFull(),

                            Select::make('nik_pengganti')
                                ->label('Pilih Ahli Waris')
                                ->options(function (Get $get) {
                                    $dtksId      = $get('dtks_id');
                                    $nikAlmarhum = $get('nik_almarhum');
                                    if (!$dtksId) return [];

                                    $dtks = DTKS::find($dtksId);
                                    if (!$dtks) return [];

                                    $nikSudahMeninggal = $dtks->meninggal()
                                        ->pluck('nik_almarhum')
                                        ->toArray();

                                    if ($nikAlmarhum) {
                                        $nikSudahMeninggal[] = $nikAlmarhum;
                                    }

                                    $options = [];
                                    foreach ($dtks->anggota_keluarga ?? [] as $a) {
                                        if (!empty($a['nik']) && !in_array($a['nik'], $nikSudahMeninggal)) {
                                            $options[$a['nik']] = $a['nama'] . ' (NIK: ' . $a['nik'] . ')';
                                        }
                                    }
                                    return $options;
                                })
                                ->live()
                                ->visible(fn(Get $get) => $get('ada_pengganti'))
                                ->required(fn(Get $get) => $get('ada_pengganti')),

                            Select::make('hubungan_pengganti')
                                ->label('Hubungan dengan Almarhum')
                                ->options([
                                    'Istri'            => 'Istri',
                                    'Suami'            => 'Suami',
                                    'Anak'             => 'Anak',
                                    'Menantu'          => 'Menantu',
                                    'Cucu'             => 'Cucu',
                                    'Keluarga Lainnya' => 'Keluarga Lainnya',
                                ])
                                ->visible(fn(Get $get) => $get('ada_pengganti'))
                                ->required(fn(Get $get) => $get('ada_pengganti')),
                        ])->columns(2),
                ])
                ->action(function (array $data): void {
                    $dtks = DTKS::find($data['dtks_id']);
                    if (!$dtks) return;

                    $anggota = $dtks->anggota_keluarga ?? [];

                    \Illuminate\Support\Facades\Log::info('DEBUG MENINGGAL', [
                        'nik_almarhum' => $data['nik_almarhum'],
                        'anggota_count' => count($anggota),
                        'anggota_niks' => collect($anggota)->pluck('nik')->toArray(),
                        'is_array' => is_array($anggota),
                    ]);

                    $namaAlmarhum   = '';
                    $statusHubungan = '';
                    foreach ($anggota as $a) {
                        if ((string)($a['nik'] ?? '') === (string)$data['nik_almarhum']) {
                            $namaAlmarhum   = $a['nama'] ?? '';
                            $statusHubungan = $a['status_hubungan'] ?? 'Anggota Keluarga';
                            break;
                        }
                    }

                    $namaPengganti = '';
                    if (!empty($data['nik_pengganti'])) {
                        foreach ($anggota as $a) {
                            if ((string)($a['nik'] ?? '') === (string)$data['nik_pengganti']) {
                                $namaPengganti = $a['nama'] ?? '';
                                break;
                            }
                        }
                    }

                    $programTerdampak = [];
                    if ($dtks->pkh()->exists())    $programTerdampak[] = 'PKH';
                    if ($dtks->bpnt()->exists())   $programTerdampak[] = 'BPNT';
                    if ($dtks->pbijk()->exists())  $programTerdampak[] = 'PBI-JK';
                    if ($dtks->atensi()->exists()) $programTerdampak[] = 'ATENSI';

                    Meninggal::create([
                        'dtks_id'            => $dtks->id,
                        'nama_almarhum'      => $namaAlmarhum,
                        'nik_almarhum'       => $data['nik_almarhum'],
                        'status_hubungan'    => $statusHubungan,
                        'tanggal_meninggal'  => $data['tanggal_meninggal'],
                        'bukti_meninggal'    => $data['bukti_meninggal'] ?? null,
                        'catatan'            => $data['catatan'] ?? null,
                        'nama_pengganti'     => $namaPengganti ?: null,
                        'nik_pengganti'      => $data['nik_pengganti'] ?? null,
                        'hubungan_pengganti' => $data['hubungan_pengganti'] ?? null,
                        'program_terdampak'  => $programTerdampak,
                    ]);

                    $nikSudahMeninggal = $dtks->meninggal()
                        ->pluck('nik_almarhum')
                        ->toArray();
                    $masihAdaHidup = collect($anggota)
                        ->filter(fn($a) => !in_array($a['nik'] ?? '', $nikSudahMeninggal))
                        ->isNotEmpty();

                    $updateDtks = [
                        'status_kpm'        => $masihAdaHidup ? 'Aktif' : 'Meninggal',
                        'tanggal_meninggal' => $data['tanggal_meninggal'],
                        'bukti_meninggal'   => $data['bukti_meninggal'] ?? null,
                    ];
                    if (!empty($data['ada_pengganti'])) {
                        $updateDtks['nama_pengganti']     = $namaPengganti;
                        $updateDtks['nik_pengganti']      = $data['nik_pengganti'];
                        $updateDtks['hubungan_pengganti'] = $data['hubungan_pengganti'];
                    }
                    $dtks->update($updateDtks);

                    $syncData = [
                        'status_kpm' => $masihAdaHidup ? 'Aktif' : 'Meninggal',
                    ];

                    if ($dtks->pkh()->exists())    $dtks->pkh->update($syncData);
                    if ($dtks->bpnt()->exists())   $dtks->bpnt->update($syncData);
                    if ($dtks->pbijk()->exists())  $dtks->pbijk->update($syncData);
                    if ($dtks->atensi()->exists()) $dtks->atensi->update($syncData);

                    Notification::make()
                        ->title('Data Meninggal Tersimpan')
                        ->body('Data berhasil dicatat dan disinkronkan ke semua program bantuan.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getTabs(): array
    {
        return DTKSTable::getTabs();
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'ditinjau';
    }
}
