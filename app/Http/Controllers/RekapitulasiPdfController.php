<?php

namespace App\Http\Controllers;

use App\Models\DTKS;
use App\Models\Meninggal;
use App\Models\KepalaDinas;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RekapitulasiPdfController extends Controller
{
    public function print(Request $request)
    {
        $query = DTKS::query()->with(['pkh', 'bpnt', 'pbijk', 'atensi']);
        $kepalaDinas = KepalaDinas::where('is_active', true)->first();

        if ($request->tipe_laporan === 'harian') {
            $query->whereDate('created_at', $request->tanggal);
        }

        if ($request->tipe_laporan === 'bulanan') {
            $query->whereMonth('created_at', $request->bulan)
                ->whereYear('created_at', $request->tahun);
        }

        if ($request->tipe_laporan === 'triwulan') {
            $months = match ($request->triwulan) {
                '1' => [1, 2, 3],
                '2' => [4, 5, 6],
                '3' => [7, 8, 9],
                '4' => [10, 11, 12],
            };

            $query->whereIn(\DB::raw('MONTH(created_at)'), $months)
                ->whereYear('created_at', $request->tahun);
        }

        if ($request->tipe_laporan === 'tahunan') {
            $query->whereYear('created_at', $request->tahun);
        }

        if ($request->program !== 'semua') {
            $query->whereHas($request->program, function ($q) use ($request) {
                if ($request->status_verifikasi !== 'semua') {
                    $q->where('status', $request->status_verifikasi);
                }
            });
        }

        $records = $query->latest()->get();

        $pdf = Pdf::loadView('pdf.rekapitulasi-pdf', [
            'records' => $records,
            'kepalaDinas' => $kepalaDinas
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('rekapitulasi.pdf');
    }

    public function printRekapitulasiMeninggal(Request $request)
    {
        $query = Meninggal::query()->with('dtks');
        $kepalaDinas = KepalaDinas::where('is_active', true)->first();

        if ($request->tipe_laporan === 'harian' && $request->tanggal_mulai && $request->tanggal_sampai) {
            $sampai = \Carbon\Carbon::parse($request->tanggal_sampai)->endOfDay();
            $query->whereBetween('tanggal_meninggal', [$request->tanggal_mulai, $sampai]);
        } elseif ($request->tipe_laporan === 'bulanan' && $request->bulan && $request->tahun) {
            $query->whereMonth('tanggal_meninggal', $request->bulan)
                ->whereYear('tanggal_meninggal', $request->tahun);
        } elseif ($request->tipe_laporan === 'triwulan' && $request->triwulan && $request->tahun) {
            $months = match ($request->triwulan) {
                '1' => [1, 2, 3],
                '2' => [4, 5, 6],
                '3' => [7, 8, 9],
                '4' => [10, 11, 12],
                default => [1, 2, 3]
            };
            $query->whereIn(\DB::raw('MONTH(tanggal_meninggal)'), $months)
                ->whereYear('tanggal_meninggal', $request->tahun);
        } elseif ($request->tipe_laporan === 'tahunan' && $request->tahun) {
            $query->whereYear('tanggal_meninggal', $request->tahun);
        }

        if ($request->program !== 'semua') {
            $programLabel = match ($request->program) {
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

        $records = $query->latest('tanggal_meninggal')->get();

        $pdf = Pdf::loadView('pdf.rekapitulasi-meninggal-pdf', [
            'records' => $records,
            'kepalaDinas' => $kepalaDinas
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('rekapitulasi-meninggal.pdf');
    }

    public function printRekapitulasiHistori(Request $request)
    {
        $query = \App\Models\HistoriPenerimaan::query()->with('dtks');
        $kepalaDinas = KepalaDinas::where('is_active', true)->first();

        if ($request->tipe_laporan === 'harian' && $request->tanggal_mulai && $request->tanggal_sampai) {
            $sampai = \Carbon\Carbon::parse($request->tanggal_sampai)->endOfDay();
            $query->whereBetween('tanggal_terima', [$request->tanggal_mulai, $sampai]);
        } elseif ($request->tipe_laporan === 'bulanan' && $request->bulan && $request->tahun) {
            $query->whereMonth('tanggal_terima', $request->bulan)->whereYear('tanggal_terima', $request->tahun);
        } elseif ($request->tipe_laporan === 'triwulan' && $request->triwulan && $request->tahun) {
            $months = match ($request->triwulan) {
                '1' => [1, 2, 3],
                '2' => [4, 5, 6],
                '3' => [7, 8, 9],
                '4' => [10, 11, 12],
                default => [1, 2, 3]
            };
            $query->whereIn(\DB::raw('MONTH(tanggal_terima)'), $months)->whereYear('tanggal_terima', $request->tahun);
        } elseif ($request->tipe_laporan === 'tahunan' && $request->tahun) {
            $query->whereYear('tanggal_terima', $request->tahun);
        }

        if ($request->program !== 'semua') {
            $query->where('program', $request->program);
        }

        $records = $query->latest('tanggal_terima')->get();
        $program = $request->program;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.rekapitulasi-histori-pdf', compact('records', 'program', 'kepalaDinas'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-histori-bantuan.pdf');
    }
}
