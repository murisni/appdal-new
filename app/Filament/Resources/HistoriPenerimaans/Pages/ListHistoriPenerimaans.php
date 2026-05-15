<?php

namespace App\Filament\Resources\HistoriPenerimaans\Pages;

use App\Filament\Resources\HistoriPenerimaans\HistoriPenerimaanResource;
use App\Models\HistoriPenerimaan;
use App\Models\DTKS;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class ListHistoriPenerimaans extends ListRecords
{
    protected static string $resource = HistoriPenerimaanResource::class;

    public ?string $activeTab = 'semua';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('tambah_histori')
                ->label(fn() => match ($this->activeTab) {
                    'pkh'    => 'TAMBAH HISTORI PKH',
                    'bpnt'   => 'TAMBAH HISTORI BPNT',
                    'pbijk'  => 'TAMBAH HISTORI PBI-JK',
                    'atensi' => 'TAMBAH HISTORI ATENSI',
                    default  => 'TAMBAH HISTORI',
                })
                ->color('primary')
                ->modalHeading(fn() => match ($this->activeTab) {
                    'pkh'    => 'Tambah Histori PKH',
                    'bpnt'   => 'Tambah Histori BPNT',
                    'pbijk'  => 'Tambah Histori PBI-JK',
                    'atensi' => 'Tambah Histori ATENSI',
                    default  => 'Tambah Histori Penerimaan',
                })
                ->modalWidth('3xl')
                ->fillForm(fn() => [
                    'program' => match ($this->activeTab) {
                        'pkh'    => 'PKH',
                        'bpnt'   => 'BPNT',
                        'pbijk'  => 'PBI-JK',
                        'atensi' => 'ATENSI',
                        default  => null,
                    },
                ])
                ->form([
                    Section::make('Data KPM')
                        ->schema([
                            Select::make('dtks_id')
                                ->label('No. KK / Nama KPM')
                                ->options(function () {
                                    return DTKS::where('status', 'diterima')
                                        ->get()
                                        ->mapWithKeys(function ($dtks) {
                                            $anggota = $dtks->anggota_keluarga ?? [];
                                            $kepala  = collect($anggota)
                                                ->firstWhere('status_hubungan', 'Kepala Keluarga');
                                            $nama = $kepala['nama'] ?? 'Tidak Terdata';
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
                                ->required(),

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
                                ->placeholder('Contoh: Januari 2026')
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
                                ->nullable()
                                ->columnSpanFull(),
                        ]),
                ])
                ->action(function (array $data): void {
                    HistoriPenerimaan::create($data);

                    Notification::make()
                        ->title('Histori Penerimaan Tersimpan')
                        ->body('Data histori berhasil ditambahkan.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua Program')
                ->icon('heroicon-m-list-bullet'),

            'pkh' => Tab::make('PKH')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('program', 'PKH'))
                ->badge(fn() => HistoriPenerimaan::where('program', 'PKH')->count())
                ->badgeColor('success')
                ->icon('heroicon-m-users'),

            'bpnt' => Tab::make('BPNT')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('program', 'BPNT'))
                ->badge(fn() => HistoriPenerimaan::where('program', 'BPNT')->count())
                ->badgeColor('info')
                ->icon('heroicon-m-shopping-cart'),

            'pbijk' => Tab::make('PBI-JK')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('program', 'PBI-JK'))
                ->badge(fn() => HistoriPenerimaan::where('program', 'PBI-JK')->count())
                ->badgeColor('warning')
                ->icon('heroicon-m-heart'),

            'atensi' => Tab::make('ATENSI')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('program', 'ATENSI'))
                ->badge(fn() => HistoriPenerimaan::where('program', 'ATENSI')->count())
                ->badgeColor('danger')
                ->icon('heroicon-m-user-group'),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'semua';
    }
}
