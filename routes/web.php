<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RekapitulasiPdfController;
use App\Models\DTKS;
use Illuminate\Http\Request;


Route::get('/', function () {
    return view('home'); //
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

    // Cari data berdasarkan No KK (sesuaikan nama kolom no_kk jika berbeda di migration Anda)
    $dtks = DTKS::where('no_kk', $request->no_kk)->first();

    if (!$dtks) {
        return response()->json(['status' => 'not_found']);
    }

    // Menentukan Alur (Step) berdasarkan kondisi data DTKS
    $step = 1; // Default: Input Data (Baru ditinjau)

    if ($dtks->isSurveyed()) {
        $step = 2; // Sudah disurvey lapangan
    }

    // Asumsi jika status sudah diproses/diterima berarti skor sudah dinilai
    if (in_array($dtks->status, [DTKS::STATUS_DIPROSES, DTKS::STATUS_DITERIMA, DTKS::STATUS_DITOLAK])) {
        $step = 3;
    }

    if ($dtks->status === DTKS::STATUS_DITERIMA) {
        $step = 4; // Final: Diterima
    }

    return response()->json([
        'status' => 'found',
        // PERBAIKAN BUG C: Ubah nama_kepala_keluarga menjadi nama (sesuai kolom database/tabel)
        'name' => $dtks->nama ?? 'Keluarga Bpk/Ibu',
        'step' => $step,
        // PERBAIKAN BUG B: Tambahkan fallback string kosong (?? '') agar ucfirst tidak error jika status null
        'status_text' => ucfirst($dtks->status ?? ''),
        'desil' => $dtks->estimasi_desil,
        'rekomendasi' => $dtks->getRekomendasiBantuan()
    ]);
});
