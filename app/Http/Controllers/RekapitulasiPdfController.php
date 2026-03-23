<?php

namespace App\Http\Controllers;

use App\Models\DTKS;
use App\Models\Meninggal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RekapitulasiPdfController extends Controller
{
    public function print(Request $request)
    {
        $query = DTKS::query()->with(['pkh', 'bpnt', 'pbijk', 'atensi']);

        // FILTER WAKTU
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

        // FILTER PROGRAM
        if ($request->program !== 'semua') {
            $query->whereHas($request->program, function ($q) use ($request) {
                if ($request->status_verifikasi !== 'semua') {
                    $q->where('status', $request->status_verifikasi);
                }
            });
        }

        $records = $query->latest()->get();

        $pdf = Pdf::loadView('pdf.rekapitulasi-pdf', [
            'records' => $records
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('rekapitulasi.pdf');
    }

    public function printRekapitulasiMeninggal(Request $request)
    {
        // Query dari tabel meninggal langsung, bukan dari DTKS
        $query = Meninggal::query()->with('dtks');

        // Filter Waktu berdasarkan tanggal_meninggal
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

        // Filter Program — cek di kolom program_terdampak JSON
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
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('rekapitulasi-meninggal.pdf');
    }

    public function printRekapitulasiHistori(Request $request)
    {
        $query = \App\Models\DTKS::query()->with(['pkh', 'bpnt', 'pbijk', 'atensi']);

        if ($request->tipe_laporan === 'harian' && $request->tanggal_mulai && $request->tanggal_sampai) {
            $sampai = \Carbon\Carbon::parse($request->tanggal_sampai)->endOfDay();
            $query->whereBetween('created_at', [$request->tanggal_mulai, $sampai]);
        } elseif ($request->tipe_laporan === 'bulanan' && $request->bulan && $request->tahun) {
            $query->whereMonth('created_at', $request->bulan)->whereYear('created_at', $request->tahun);
        } elseif ($request->tipe_laporan === 'triwulan' && $request->triwulan && $request->tahun) {
            $months = match ($request->triwulan) {
                '1' => [1, 2, 3],
                '2' => [4, 5, 6],
                '3' => [7, 8, 9],
                '4' => [10, 11, 12],
                default => [1, 2, 3]
            };
            $query->whereIn(\DB::raw('MONTH(created_at)'), $months)->whereYear('created_at', $request->tahun);
        } elseif ($request->tipe_laporan === 'tahunan' && $request->tahun) {
            $query->whereYear('created_at', $request->tahun);
        }

        $relasi = $request->program;
        $query->whereHas($relasi, function ($q) {
            $q->whereNotNull('histori_penerimaan');
        });

        $records = $query->latest()->get();
        $program = $request->program;

        return view('pdf.rekapitulasi-histori-pdf', compact('records', 'program'));
    }
}
