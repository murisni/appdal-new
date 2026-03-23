<?php

namespace App\Filament\Pages;

use App\Models\DTKS;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class RekapitulasiHistori extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clock';
    protected string $view = 'filament.pages.rekapitulasi-histori';
    protected static ?string $navigationLabel = 'Rekapitulasi Histori';
    protected static ?string $pluralModelLabel = 'Rekapitulasi Histori';
    protected static string | UnitEnum | null $navigationGroup = 'Rekapitulasi';
    protected static ?int $navigationSort = 12;
    protected static ?string $title = 'Laporan Histori Penyaluran Bantuan';

    public $tipe_laporan = 'bulanan';
    public $tanggal_mulai;
    public $tanggal_sampai;
    public $bulan;
    public $triwulan = '1';
    public $tahun;
    public $program = 'pkh'; // Default ke PKH agar histori langsung terfokus

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

        // 1. Filter Waktu
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

        // 2. Filter Program (Wajib memiliki histori agar tidak kosong)
        if ($this->program !== 'semua') {
            $relasi = $this->program;
            // Hanya tampilkan KPM yang data bantuannya ada dan berstatus diterima/ditinjau ulang (memiliki histori)
            $query->whereHas($relasi, function ($q) {
                $q->whereNotNull('histori_penerimaan');
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
                TextColumn::make('nama_kpm')
                    ->label('Nama KPM')
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
                    ->searchable(query: fn(Builder $query, string $search) => $query->where('anggota_keluarga', 'like', "%{$search}%")),

                // Mengambil Histori berdasarkan Program yang difilter
                TextColumn::make('histori_tanggal')
                    ->label('Tanggal Penyaluran')
                    ->html()
                    ->getStateUsing(function ($record) {
                        $relasi = $this->program;
                        if ($relasi === 'semua') return '- Pilih Program -';

                        $histori = $record->$relasi->histori_penerimaan ?? [];
                        if (is_string($histori)) $histori = json_decode($histori, true);

                        if (!empty($histori) && is_array($histori)) {
                            $tgl = [];
                            foreach ($histori as $h) {
                                if (isset($h['tanggal_diterima'])) $tgl[] = Carbon::parse($h['tanggal_diterima'])->translatedFormat('d F Y');
                            }
                            return implode('<br>', $tgl);
                        }
                        return 'Belum ada histori';
                    }),

                TextColumn::make('histori_catatan')
                    ->label('Catatan Histori')
                    ->html()
                    ->wrap()
                    ->getStateUsing(function ($record) {
                        $relasi = $this->program;
                        if ($relasi === 'semua') return 'Pilih program untuk melihat detail';

                        $histori = $record->$relasi->histori_penerimaan ?? [];
                        if (is_string($histori)) $histori = json_decode($histori, true);

                        if (!empty($histori) && is_array($histori)) {
                            $catatan = [];
                            foreach ($histori as $h) {
                                if (isset($h['catatan'])) $catatan[] = '- ' . $h['catatan'];
                            }
                            return implode('<br>', $catatan);
                        }
                        return '-';
                    }),
            ])
            ->emptyStateHeading('Tidak ada data histori')
            ->emptyStateDescription('Pilih program bantuan yang memiliki data penyaluran.');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak_pdf')
                ->label('Cetak Laporan PDF')
                ->icon('heroicon-o-printer')
                ->color('danger')
                ->action(function () {
                    $url = route('rekapitulasi-histori-pdf.print', [
                        'tipe_laporan' => $this->tipe_laporan,
                        'tanggal_mulai' => $this->tanggal_mulai,
                        'tanggal_sampai' => $this->tanggal_sampai,
                        'bulan' => $this->bulan,
                        'triwulan' => $this->triwulan,
                        'tahun' => $this->tahun,
                        'program' => $this->program,
                    ]);

                    $this->js("window.open('{$url}', '_blank');");
                }),
        ];
    }
}
