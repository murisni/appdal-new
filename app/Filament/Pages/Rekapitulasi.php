<?php

namespace App\Filament\Pages;

use App\Models\DTKS;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class Rekapitulasi extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected string $view = 'filament.pages.rekapitulasi';
    protected static ?string $navigationLabel = 'Rekapitulasi Bantuan';
    protected static ?string $pluralModelLabel = 'Rekapitulasi';
    protected static string | UnitEnum | null $navigationGroup = 'Rekapitulasi';
    protected static ?int $navigationSort = 10;
    protected static ?string $title = 'Rekapitulasi Data Penerima Bantuan';

    public $tipe_laporan = 'bulanan';
    public $tanggal_mulai;
    public $tanggal_sampai;
    public $bulan;
    public $triwulan = '1';
    public $tahun;
    public $program = 'semua';
    public $status_verifikasi = 'semua';

    public $listTahun = [];

    public function mount(): void
    {
        $this->tanggal_mulai = date('Y-m-d');
        $this->tanggal_sampai = date('Y-m-d');
        $this->bulan = date('m');
        $this->tahun = date('Y');

        $currentYear = date('Y');
        $years = range(2024, $currentYear + 1);
        $this->listTahun = array_combine($years, $years);
    }

    public function getFilteredQuery(): Builder
    {
        $query = DTKS::query()->with(['pkh', 'bpnt', 'pbijk', 'atensi']);

        if ($this->tipe_laporan === 'harian' && $this->tanggal_mulai && $this->tanggal_sampai) {
            $sampai = Carbon::parse($this->tanggal_sampai)->endOfDay();
            $query->whereBetween('created_at', [$this->tanggal_mulai, $sampai]);
        } elseif ($this->tipe_laporan === 'bulanan' && $this->bulan && $this->tahun) {
            $query->whereMonth('created_at', $this->bulan)->whereYear('created_at', $this->tahun);
        } elseif ($this->tipe_laporan === 'triwulan' && $this->triwulan && $this->tahun) {
            $months = match ($this->triwulan) {
                '1' => [1, 2, 3],
                '2' => [4, 5, 6],
                '3' => [7, 8, 9],
                '4' => [10, 11, 12],
                default => [1, 2, 3],
            };
            $query->whereIn(\DB::raw('MONTH(created_at)'), $months)->whereYear('created_at', $this->tahun);
        } elseif ($this->tipe_laporan === 'tahunan' && $this->tahun) {
            $query->whereYear('created_at', $this->tahun);
        }

        if ($this->program !== 'semua') {
            $relasi = $this->program;
            $query->whereHas($relasi, function ($q) {
                if ($this->status_verifikasi !== 'semua') {
                    $q->where('status', $this->status_verifikasi);
                }
            });
        }

        return $query->latest();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => $this->getFilteredQuery())
            ->columns([
                TextColumn::make('no_kk')->label('No. KK')->searchable(),
                TextColumn::make('nik')->label('NIK')->searchable()->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('nama_kpm')
                    ->label('Nama Kepala Keluarga')
                    ->getStateUsing(function ($record) {
                        $anggota = is_string($record->anggota_keluarga) ? json_decode($record->anggota_keluarga, true) : $record->anggota_keluarga;
                        if (is_array($anggota)) {
                            foreach ($anggota as $a) {
                                if (isset($a['status_hubungan']) && strtolower($a['status_hubungan']) === 'kepala keluarga') {
                                    return $a['nama'];
                                }
                            }
                            return $anggota[0]['nama'] ?? '-';
                        }
                        return '-';
                    })
                    ->description(fn($record) => $record->status_kpm === 'Meninggal' ? 'Wafat (Pengganti: ' . ($record->nama_pengganti ?? '-') . ')' : 'Aktif')
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->where('anggota_keluarga', 'like', "%{$search}%");
                    }),

                TextColumn::make('alamat')->label('Alamat')->limit(30)->visible(fn() => $this->program === 'semua'),
                TextColumn::make('pkh.status')->label('PKH')->badge()->color(fn($state) => match ($state) {
                    'diterima' => 'success',
                    'ditolak' => 'danger',
                    'diproses' => 'warning',
                    default => 'gray'
                })->default('-')->visible(fn() => $this->program === 'semua'),
                TextColumn::make('bpnt.status')->label('BPNT')->badge()->color(fn($state) => match ($state) {
                    'diterima' => 'success',
                    'ditolak' => 'danger',
                    'diproses' => 'warning',
                    default => 'gray'
                })->default('-')->visible(fn() => $this->program === 'semua'),
                TextColumn::make('pbijk.status')->label('PBI-JK')->badge()->color(fn($state) => match ($state) {
                    'diterima' => 'success',
                    'ditolak' => 'danger',
                    'diproses' => 'warning',
                    default => 'gray'
                })->default('-')->visible(fn() => $this->program === 'semua'),
                TextColumn::make('atensi.status')->label('ATENSI')->badge()->color(fn($state) => match ($state) {
                    'diterima' => 'success',
                    'ditolak' => 'danger',
                    'diproses' => 'warning',
                    default => 'gray'
                })->default('-')->visible(fn() => $this->program === 'semua'),

                IconColumn::make('pkh.ibu_hamil')->label('Bumil')->boolean()->visible(fn() => $this->program === 'pkh'),
                IconColumn::make('pkh.anak_usia_dini')->label('Balita')->boolean()->visible(fn() => $this->program === 'pkh'),
                TextColumn::make('pkh.jumlah_sd')->label('SD')->numeric()->visible(fn() => $this->program === 'pkh'),
                TextColumn::make('pkh.jumlah_smp')->label('SMP')->numeric()->visible(fn() => $this->program === 'pkh'),
                TextColumn::make('pkh.jumlah_sma')->label('SMA')->numeric()->visible(fn() => $this->program === 'pkh'),
                TextColumn::make('pkh.catatan_surveyor')->label('Catatan Surveyor')->color('danger')->wrap()->visible(fn() => $this->program === 'pkh' && $this->status_verifikasi === 'ditolak'),

                TextColumn::make('bpnt.no_kartu_kks')->label('No. KKS')->visible(fn() => $this->program === 'bpnt'),
                TextColumn::make('bpnt.status_pangan')->label('Status Sembako')->badge()->color(fn($state) => match ($state) {
                    'terima' => 'success',
                    'tidak' => 'danger',
                    default => 'gray'
                })->formatStateUsing(fn($state) => match ($state) {
                    'terima' => 'Menerima',
                    'tidak' => 'Tidak / Kosong',
                    default => '-'
                })->visible(fn() => $this->program === 'bpnt'),
                TextColumn::make('bpnt.catatan_surveyor')->label('Catatan Surveyor')->color('danger')->wrap()->visible(fn() => $this->program === 'bpnt' && $this->status_verifikasi === 'ditolak'),

                TextColumn::make('pbijk.nomor_bpjs')->label('No. BPJS')->visible(fn() => $this->program === 'pbijk'),
                TextColumn::make('pbijk.faskes_tingkat_1')->label('Faskes Tkt.1')->visible(fn() => $this->program === 'pbijk'),
                TextColumn::make('pbijk.catatan_surveyor')->label('Catatan Surveyor')->color('danger')->wrap()->visible(fn() => $this->program === 'pbijk' && $this->status_verifikasi === 'ditolak'),

                TextColumn::make('atensi.kategori')->label('Kategori')->badge()->visible(fn() => $this->program === 'atensi'),
                TextColumn::make('atensi.jenis_bantuan_diterima')->label('Bantuan Diterima')->wrap()->visible(fn() => $this->program === 'atensi'),
                TextColumn::make('atensi.nominal_bantuan')->label('Nominal')->money('IDR')->visible(fn() => $this->program === 'atensi'),
                TextColumn::make('atensi.catatan_surveyor')->label('Catatan Surveyor')->color('danger')->wrap()->visible(fn() => $this->program === 'atensi' && $this->status_verifikasi === 'ditolak'),
            ])
            ->emptyStateHeading('Tidak ada data penerima')
            ->emptyStateDescription('Silakan sesuaikan filter di atas untuk melihat data.');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak_pdf')
                ->label('Cetak Laporan PDF')
                ->icon('heroicon-o-printer')
                ->color('danger')
                ->action(function () {
                    $url = route('rekapitulasi-pdf.print', [
                        'tipe_laporan' => $this->tipe_laporan,
                        'tanggal_mulai' => $this->tanggal_mulai,
                        'tanggal_sampai' => $this->tanggal_sampai,
                        'bulan' => $this->bulan,
                        'triwulan' => $this->triwulan,
                        'tahun' => $this->tahun,
                        'program' => $this->program,
                        'status_verifikasi' => $this->status_verifikasi,
                        'status_kpm' => 'semua',
                    ]);

                    $this->js("window.open('{$url}', '_blank');");
                }),
        ];
    }
}
