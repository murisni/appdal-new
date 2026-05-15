<?php

namespace App\Filament\Pages;

use App\Models\Meninggal;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class RekapitulasiMeninggal extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-minus';
    protected string $view = 'filament.pages.rekapitulasi-meninggal';
    protected static ?string $navigationLabel = 'Rekapitulasi Meninggal';
    protected static ?string $pluralModelLabel = 'Rekapitulasi Meninggal';
    protected static string | UnitEnum | null $navigationGroup = 'Rekapitulasi';
    protected static ?int $navigationSort = 11;
    protected static ?string $title = 'Laporan Meninggal & Pergantian KPM';

    public $tipe_laporan = 'bulanan';
    public $tanggal_mulai;
    public $tanggal_sampai;
    public $bulan;
    public $triwulan = '1';
    public $tahun;
    public $program = 'semua';
    public $listTahun = [];

    public function mount(): void
    {
        $this->tanggal_mulai  = date('Y-m-d');
        $this->tanggal_sampai = date('Y-m-d');
        $this->bulan          = date('m');
        $this->tahun          = date('Y');

        $currentYear     = date('Y');
        $years           = range(2024, $currentYear + 1);
        $this->listTahun = array_combine($years, $years);
    }

    public function getFilteredQuery(): Builder
    {
        $query = Meninggal::query()->with('dtks');

        if ($this->tipe_laporan === 'harian' && $this->tanggal_mulai && $this->tanggal_sampai) {
            $sampai = Carbon::parse($this->tanggal_sampai)->endOfDay();
            $query->whereBetween('tanggal_meninggal', [$this->tanggal_mulai, $sampai]);
        } elseif ($this->tipe_laporan === 'bulanan' && $this->bulan && $this->tahun) {
            $query->whereMonth('tanggal_meninggal', $this->bulan)
                ->whereYear('tanggal_meninggal', $this->tahun);
        } elseif ($this->tipe_laporan === 'triwulan' && $this->triwulan && $this->tahun) {
            $months = match ($this->triwulan) {
                '1' => [1, 2, 3],
                '2' => [4, 5, 6],
                '3' => [7, 8, 9],
                '4' => [10, 11, 12],
                default => [1, 2, 3],
            };
            $query->whereIn(\DB::raw('MONTH(tanggal_meninggal)'), $months)
                ->whereYear('tanggal_meninggal', $this->tahun);
        } elseif ($this->tipe_laporan === 'tahunan' && $this->tahun) {
            $query->whereYear('tanggal_meninggal', $this->tahun);
        }

        if ($this->program !== 'semua') {
            $programLabel = match ($this->program) {
                'pkh'    => 'PKH',
                'bpnt'   => 'BPNT',
                'pbijk'  => 'PBI-JK',
                'atensi' => 'ATENSI',
                default  => null,
            };
            if ($programLabel) {
                $query->whereJsonContains('program_terdampak', $programLabel);
            }
        }

        return $query->latest('tanggal_meninggal');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => $this->getFilteredQuery())
            ->columns([
                TextColumn::make('dtks.no_kk')
                    ->label('No. KK')
                    ->searchable()
                    ->sortable()
                    ->description(
                        fn(Meninggal $record): string =>
                        $record->dtks
                            ? 'Kec. ' . $record->dtks->kecamatan . ' — ' . $record->dtks->kelurahan
                            : ''
                    ),

                TextColumn::make('nama_almarhum')
                    ->label('Nama Almarhum/ah')
                    ->searchable()
                    ->color('danger')
                    ->description(
                        fn(Meninggal $record): string =>
                        'NIK: ' . ($record->nik_almarhum ?? '-') .
                            ' | ' . ($record->status_hubungan ?? '-')
                    ),

                TextColumn::make('tanggal_meninggal')
                    ->label('Tanggal Meninggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('nama_pengganti')
                    ->label('Ahli Waris / Pengganti')
                    ->default('Tidak Ada Pengganti')
                    ->color('success')
                    ->description(
                        fn(Meninggal $record): string =>
                        $record->nama_pengganti
                            ? 'NIK: ' . ($record->nik_pengganti ?? '-') .
                            ' (' . ($record->hubungan_pengganti ?? '-') . ')'
                            : ''
                    ),

                TextColumn::make('program_terdampak')
                    ->label('Program Terdampak')
                    ->badge()
                    ->color('warning')
                    ->separator(','),

                ImageColumn::make('bukti_meninggal')
                    ->label('Bukti')
                    ->circular()
                    ->defaultImageUrl(url('/images/no-image.png')),
            ])
            ->emptyStateHeading('Tidak ada data laporan meninggal')
            ->emptyStateDescription('Belum ada data KPM yang dilaporkan meninggal dunia pada periode ini.');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak_pdf')
                ->label('Cetak Laporan PDF')
                ->icon('heroicon-o-printer')
                ->color('danger')
                ->action(function () {
                    $url = route('rekapitulasi-meninggal-pdf.print', [
                        'tipe_laporan'  => $this->tipe_laporan,
                        'tanggal_mulai' => $this->tanggal_mulai,
                        'tanggal_sampai' => $this->tanggal_sampai,
                        'bulan'         => $this->bulan,
                        'triwulan'      => $this->triwulan,
                        'tahun'         => $this->tahun,
                        'program'       => $this->program,
                    ]);
                    $this->js("window.open('{$url}', '_blank');");
                }),
        ];
    }
}
