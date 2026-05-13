<?php

namespace App\Filament\Pages;

use App\Models\HistoriPenerimaan;
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
    public $program = 'semua'; // Default ke 'semua'

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
        $query = HistoriPenerimaan::query()->with('dtks');

        if ($this->tipe_laporan === 'harian' && $this->tanggal_mulai && $this->tanggal_sampai) {
            $sampai = Carbon::parse($this->tanggal_sampai)->endOfDay();
            $query->whereBetween('tanggal_terima', [$this->tanggal_mulai, $sampai]);
        } elseif ($this->tipe_laporan === 'bulanan' && $this->bulan && $this->tahun) {
            $query->whereMonth('tanggal_terima', $this->bulan)->whereYear('tanggal_terima', $this->tahun);
        } elseif ($this->tipe_laporan === 'triwulan' && $this->triwulan && $this->tahun) {
            $months = match ($this->triwulan) {
                '1' => [1, 2, 3],
                '2' => [4, 5, 6],
                '3' => [7, 8, 9],
                '4' => [10, 11, 12],
                default => [1, 2, 3],
            };
            $query->whereIn(\DB::raw('MONTH(tanggal_terima)'), $months)->whereYear('tanggal_terima', $this->tahun);
        } elseif ($this->tipe_laporan === 'tahunan' && $this->tahun) {
            $query->whereYear('tanggal_terima', $this->tahun);
        }

        if ($this->program !== 'semua') {
            $query->where('program', $this->program);
        }

        return $query->latest('tanggal_terima');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => $this->getFilteredQuery())
            ->columns([
                TextColumn::make('dtks.no_kk')->label('No. KK')->searchable(),
                TextColumn::make('nama_kpm')
                    ->label('Nama KPM')
                    ->getStateUsing(function ($record) {
                        if (!$record->dtks) return '-';
                        $anggota = is_string($record->dtks->anggota_keluarga) ? json_decode($record->dtks->anggota_keluarga, true) : $record->dtks->anggota_keluarga;
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
                    ->searchable(query: fn(Builder $query, string $search) => $query->whereHas('dtks', fn($q) => $q->where('anggota_keluarga', 'like', "%{$search}%"))),

                TextColumn::make('program')
                    ->label('Program')
                    ->badge(),

                TextColumn::make('tanggal_terima')
                    ->label('Tgl Terima')
                    ->date('d M Y'),

                TextColumn::make('periode_bantuan')
                    ->label('Periode')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('nominal_bantuan')
                    ->label('Nominal')
                    ->money('IDR')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('lokasi_penyerahan')
                    ->label('Lokasi')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('petugas_penyerah')
                    ->label('Petugas')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status_penerimaan')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'diterima' => 'success',
                        'tidak' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('catatan_penerimaan')
                    ->label('Catatan')
                    ->wrap()
                    ->getStateUsing(fn($record) => $record->catatan_penerimaan ?: '-'),

                ImageColumn::make('foto_bukti')
                    ->label('Bukti Foto')
                    ->square(),
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
