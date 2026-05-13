<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Histori Bantuan</title>
    <style>
        @page { size: A4 landscape; margin: 1.5cm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; color: #000; }
        .kop-surat { width: 100%; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 15px; position: relative; text-align: center; }
        .kop-surat img { position: absolute; left: 0; top: 0; width: 80px; height: auto; }
        .kop-surat h1 { margin: 0; font-size: 14pt; font-weight: normal; letter-spacing: 1px; }
        .kop-surat h2 { margin: 0; font-size: 16pt; font-weight: bold; letter-spacing: 1px; }
        .kop-surat p { margin: 2px 0 0 0; font-size: 10pt; }
        .judul-laporan { text-align: center; font-weight: bold; font-size: 12pt; text-transform: uppercase; margin-bottom: 5px; }
        .info-filter { text-align: center; font-size: 10pt; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #000; padding: 6px 8px; vertical-align: top; text-align: left; }
        thead th { background-color: #e0e0e0; font-weight: bold; font-size: 9pt; text-align: center; }
        .ttd-container { width: 100%; margin-top: 30px; page-break-inside: avoid; }
        .ttd-box { float: right; width: 300px; text-align: center; }
        .nama-kadis { margin-top: 60px; font-weight: bold; text-decoration: underline; }
        .foto-bukti { width: 120px; height: auto; object-fit: cover; border: 1px solid #ccc; padding: 2px; }
        .debug-text { font-size: 9px; color: red; word-break: break-all; margin-top: 5px; display: block; font-style: italic; }
    </style>
</head>
<body>

    <div class="kop-surat">
        @php
            $logoData = null;
            $logoPath = public_path('images/logo-kapuas.png');
            if(@file_exists($logoPath)) {
                $logoData = 'data:image/png;base64,' . base64_encode(@file_get_contents($logoPath));
            }
        @endphp
        @if($logoData)
            <img src="{{ $logoData }}" alt="Logo">
        @endif
        
        <h1>PEMERINTAH KABUPATEN KAPUAS</h1>
        <h2>DINAS SOSIAL</h2>
        <p>Jalan Pemuda Km. 5,5 Kuala Kapuas, Kalimantan Tengah 73514</p>
    </div>

    <div class="judul-laporan">REKAPITULASI HISTORI PENYALURAN BANTUAN {{ strtoupper($program === 'semua' ? 'SEMUA PROGRAM' : $program) }}</div>
    
    <div class="info-filter">
        Periode Laporan: 
        @if(request('tipe_laporan') == 'harian')
            {{ \Carbon\Carbon::parse(request('tanggal_mulai'))->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse(request('tanggal_sampai'))->translatedFormat('d F Y') }}
        @elseif(request('tipe_laporan') == 'bulanan') Bulan {{ request('bulan') }} Tahun {{ request('tahun') }}
        @elseif(request('tipe_laporan') == 'triwulan') Triwulan {{ request('triwulan') }} Tahun {{ request('tahun') }}
        @else Tahun {{ request('tahun') }} @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Data Penerima (KPM)</th>
                <th style="width: 45%;">Detail Histori & Penyaluran</th>
                <th style="width: 25%;">Bukti Foto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $row)
            @php
                // Cari Kepala Keluarga
                $anggota = is_string($row->dtks->anggota_keluarga) ? json_decode($row->dtks->anggota_keluarga, true) : $row->dtks->anggota_keluarga;
                $namaKpm = '-';
                if (is_array($anggota)) {
                    foreach ($anggota as $a) {
                        if (isset($a['status_hubungan']) && strtolower($a['status_hubungan']) === 'kepala keluarga') { $namaKpm = $a['nama']; break; }
                    }
                    if($namaKpm === '-') $namaKpm = $anggota[0]['nama'] ?? '-';
                }

                // BYPASS ENGINE: Memaksa baca data mentah (Ignore Permission Block)
                $imageData = null;
                $debugMsg = '';

                if (!empty($row->foto_bukti)) {
                    // Bersihkan karakter aneh barangkali tersimpan sebagai array JSON ["..."]
                    $cleanPath = trim(str_replace(['"', '[', ']', '\\'], '', $row->foto_bukti));
                    $cleanPath = ltrim($cleanPath, '/');
                    $ext = pathinfo($cleanPath, PATHINFO_EXTENSION) ?: 'jpg';

                    try {
                        // METODE 1: Membaca langsung via Storage Laravel (Cara persis Filament membaca file)
                        $fileContent = \Illuminate\Support\Facades\Storage::disk('public')->get($cleanPath);
                        $imageData = 'data:image/' . $ext . ';base64,' . base64_encode($fileContent);
                    } catch (\Exception $e) {
                        // METODE 2: Tembak via URL Web langsung (Menduplikasi cara Browser memuat gambar)
                        try {
                            $url = asset('storage/' . $cleanPath);
                            // @ mencegah error menghentikan PDF
                            $fileContent = @file_get_contents($url); 
                            
                            if ($fileContent) {
                                $imageData = 'data:image/' . $ext . ';base64,' . base64_encode($fileContent);
                            } else {
                                $debugMsg = "Gagal memuat dari Storage maupun URL: " . $url;
                            }
                        } catch (\Exception $e2) {
                            $debugMsg = "File corrupt atau tidak ditemukan.";
                        }
                    }
                }
            @endphp
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>
                    <b>{{ $namaKpm }}</b><br>
                    No KK: {{ $row->dtks->no_kk }}
                </td>
                <td>
                    @if($program === 'semua')
                        <b>Program:</b> {{ $row->program }}<br>
                    @endif
                    
                    <b>Tanggal Terima:</b> {{ \Carbon\Carbon::parse($row->tanggal_terima)->translatedFormat('d F Y') }}<br>
                    @if($row->periode_bantuan) <b>Periode:</b> {{ $row->periode_bantuan }}<br> @endif
                    @if($row->nominal_bantuan) <b>Nominal:</b> Rp {{ number_format($row->nominal_bantuan, 0, ',', '.') }}<br> @endif
                    @if($row->lokasi_penyerahan) <b>Lokasi:</b> {{ $row->lokasi_penyerahan }}<br> @endif
                    @if($row->petugas_penyerah) <b>Petugas:</b> {{ $row->petugas_penyerah }}<br> @endif
                    
                    <b>Status:</b> {{ ucfirst($row->status_penerimaan) }}<br>
                    <b>Catatan:</b> {{ $row->catatan_penerimaan ?? '-' }}
                </td>
                <td style="text-align: center;">
                    @if($imageData)
                        <img src="{{ $imageData }}" class="foto-bukti" alt="Bukti">
                    @else
                        <i>Tidak ada foto</i>
                        @if($debugMsg)
                            <span class="debug-text">{{ $debugMsg }}</span>
                        @endif
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align: center; padding: 20px;">Tidak ada histori KPM pada program dan periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="ttd-container">
        <div class="ttd-box">
            <p>Kuala Kapuas, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Kepala Dinas Sosial</p><br>
            <p class="nama-kadis">( NAMA KEPALA DINAS )</p>
            <p>NIP. 1970XXXXXXXXXXXXXX</p>
        </div>
    </div>
</body>
</html>