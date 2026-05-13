<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RekapitulasiPdfController;
use App\Models\DTKS;
use Illuminate\Http\Request;


Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/rekapitulasi-pdf/print', [RekapitulasiPdfController::class, 'print'])
    ->name('rekapitulasi-pdf.print');

Route::get('/cetak-rekapitulasi', [RekapitulasiPdfController::class, 'printRekapitulasi'])->name('rekapitulasi-pdf.print');

Route::get('/cetak-rekapitulasi-meninggal', [RekapitulasiPdfController::class, 'printRekapitulasiMeninggal'])->name('rekapitulasi-meninggal-pdf.print');

Route::get('/cetak-rekapitulasi-histori', [\App\Http\Controllers\RekapitulasiPdfController::class, 'printRekapitulasiHistori'])->name('rekapitulasi-histori-pdf.print');

Route::post('/cek-bantuan', function (Request $request) {
    $request->validate([
        'no_kk' => 'required'
    ]);

    $dtks = DTKS::where('no_kk', $request->no_kk)->first();

    if (!$dtks) {
        return response()->json(['status' => 'not_found']);
    }

    $step = 1;

    if ($dtks->isSurveyed()) {
        $step = 2;
    }

    if (in_array($dtks->status, [DTKS::STATUS_DIPROSES, DTKS::STATUS_DITERIMA, DTKS::STATUS_DITOLAK])) {
        $step = 3;
    }

    if ($dtks->status === DTKS::STATUS_DITERIMA) {
        $step = 4;
    }

    return response()->json([
        'status' => 'found',
        'name' => $dtks->nama ?? 'Keluarga Bpk/Ibu',
        'step' => $step,
        'status_text' => ucfirst($dtks->status ?? ''),
        'desil' => $dtks->estimasi_desil,
        'rekomendasi' => $dtks->getRekomendasiBantuan()
    ]);
});
