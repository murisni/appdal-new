<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Histori Bantuan</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.5cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            color: #000;
        }

        .kop-surat {
            width: 100%;
            border-bottom: 3px solid #000;
            padding-bottom: 12px;
            margin-bottom: 15px;
            position: relative;
            text-align: center;
        }

        .kop-surat img.logo-kiri {
            position: absolute;
            left: 10px;
            top: 0;
            height: 85px;
            width: auto;
        }

        .kop-surat img.logo-kanan {
            position: absolute;
            right: 10px;
            top: 20;
            height: 50px;
            width: 150px;
        }

        .kop-surat h1 {
            margin: 0;
            font-size: 14pt;
            font-weight: normal;
            letter-spacing: 1px;
        }

        .kop-surat h2 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .kop-surat p {
            margin: 2px 0 0 0;
            font-size: 10pt;
        }

        .judul-laporan {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .info-filter {
            text-align: center;
            font-size: 10pt;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
            text-align: left;
        }

        thead th {
            background-color: #e0e0e0;
            font-weight: bold;
            font-size: 9pt;
            text-align: center;
        }

        .ttd-container {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .ttd-box {
            float: right;
            width: 300px;
            text-align: center;
        }

        .ttd-box p {
            margin: 2px 0;
        }

        .nama-kadis {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }

        .foto-bukti {
            width: 120px;
            height: auto;
            object-fit: cover;
            border: 1px solid #ccc;
            padding: 2px;
        }

        .debug-text {
            font-size: 9px;
            color: red;
            word-break: break-all;
            margin-top: 5px;
            display: block;
            font-style: italic;
        }
    </style>
</head>

<body>

    @php
        // Load Logos Base64
        $logoKiriData = null;
        $logoKananData = null;

        $logoKiriPath = public_path('img/logo.png');
        if (@file_exists($logoKiriPath)) {
            $logoKiriData = 'data:image/png;base64,' . base64_encode(@file_get_contents($logoKiriPath));
        }

        $logoKananPath = public_path('img/dinsos.webp');
        if (@file_exists($logoKananPath)) {
            $logoKananData = 'data:image/webp;base64,' . base64_encode(@file_get_contents($logoKananPath));
        }
    @endphp

    <div class="kop-surat">
        @if ($logoKiriData)
            <img src="{{ $logoKiriData }}" class="logo-kiri" alt="Logo Pemkab">
        @endif
        @if ($logoKananData)
            <img src="{{ $logoKananData }}" class="logo-kanan" alt="Logo Dinsos">
        @endif

        <h1>PEMERINTAH KABUPATEN KAPUAS</h1>
        <h2>DINAS SOSIAL</h2>
        <p>Jalan Patih Rumbih No. 11 Kuala Kapuas 73514</p>
        <p>Telepon: (0513) 21088 Faksimile: (0513) 21088</p>
        <p>Website: https://dinsos.kapuaskab.go.id Email: dinsos@kapuaskab.go.id</p>
    </div>

    <div class="judul-laporan">REKAPITULASI HISTORI PENERIMAAN BANTUAN
        {{ strtoupper($program === 'semua' ? 'SEMUA PROGRAM' : $program) }}</div>

    <div class="info-filter">
        Periode Laporan:
        @if (request('tipe_laporan') == 'harian')
            {{ \Carbon\Carbon::parse(request('tanggal_mulai'))->translatedFormat('d F Y') }} s/d
            {{ \Carbon\Carbon::parse(request('tanggal_sampai'))->translatedFormat('d F Y') }}
        @elseif(request('tipe_laporan') == 'bulanan')
            Bulan {{ request('bulan') }} Tahun {{ request('tahun') }}
        @elseif(request('tipe_laporan') == 'triwulan')
            Triwulan {{ request('triwulan') }} Tahun {{ request('tahun') }}
        @else
            Tahun {{ request('tahun') }}
        @endif
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
                    $anggota = is_string($row->dtks->anggota_keluarga)
                        ? json_decode($row->dtks->anggota_keluarga, true)
                        : $row->dtks->anggota_keluarga;
                    $namaKpm = '-';
                    if (is_array($anggota)) {
                        foreach ($anggota as $a) {
                            if (
                                isset($a['status_hubungan']) &&
                                strtolower($a['status_hubungan']) === 'kepala keluarga'
                            ) {
                                $namaKpm = $a['nama'];
                                break;
                            }
                        }
                        if ($namaKpm === '-') {
                            $namaKpm = $anggota[0]['nama'] ?? '-';
                        }
                    }

                    $imageData = null;
                    $debugMsg = '';

                    if (!empty($row->foto_bukti)) {
                        $cleanPath = trim(str_replace(['"', '[', ']', '\\'], '', $row->foto_bukti));
                        $cleanPath = ltrim($cleanPath, '/');
                        $ext = pathinfo($cleanPath, PATHINFO_EXTENSION) ?: 'jpg';

                        try {
                            $fileContent = \Illuminate\Support\Facades\Storage::disk('public')->get($cleanPath);
                            $imageData = 'data:image/' . $ext . ';base64,' . base64_encode($fileContent);
                        } catch (\Exception $e) {
                            try {
                                $url = asset('storage/' . $cleanPath);
                                $fileContent = @file_get_contents($url);

                                if ($fileContent) {
                                    $imageData = 'data:image/' . $ext . ';base64,' . base64_encode($fileContent);
                                } else {
                                    $debugMsg = 'Gagal memuat dari Storage maupun URL: ' . $url;
                                }
                            } catch (\Exception $e2) {
                                $debugMsg = 'File corrupt atau tidak ditemukan.';
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
                        @if ($program === 'semua')
                            <b>Program:</b> {{ $row->program }}<br>
                        @endif

                        <b>Tanggal Terima:</b>
                        {{ \Carbon\Carbon::parse($row->tanggal_terima)->locale('id')->translatedFormat('d F Y') }}<br>
                        @if ($row->periode_bantuan)
                            <b>Periode:</b> {{ $row->periode_bantuan }}<br>
                        @endif
                        @if ($row->nominal_bantuan)
                            <b>Nominal:</b> Rp {{ number_format($row->nominal_bantuan, 0, ',', '.') }}<br>
                        @endif
                        @if ($row->lokasi_penyerahan)
                            <b>Lokasi:</b> {{ $row->lokasi_penyerahan }}<br>
                        @endif
                        @if ($row->petugas_penyerah)
                            <b>Petugas:</b> {{ $row->petugas_penyerah }}<br>
                        @endif

                        <b>Status:</b> {{ ucfirst($row->status_penerimaan) }}<br>
                        <b>Catatan:</b> {{ $row->catatan_penerimaan ?? '-' }}
                    </td>
                    <td style="text-align: center;">
                        @if ($imageData)
                            <img src="{{ $imageData }}" class="foto-bukti" alt="Bukti">
                        @else
                            <i>Tidak ada foto</i>
                            @if ($debugMsg)
                                <span class="debug-text">{{ $debugMsg }}</span>
                            @endif
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px;">Tidak ada histori KPM pada program dan
                        periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @php
        $ttdBase64 = null;
        if (isset($kepalaDinas) && $kepalaDinas->foto_ttd) {
            try {
                $fileContent = \Illuminate\Support\Facades\Storage::disk('public')->get($kepalaDinas->foto_ttd);
                $ext = pathinfo($kepalaDinas->foto_ttd, PATHINFO_EXTENSION);
                $ttdBase64 = 'data:image/' . $ext . ';base64,' . base64_encode($fileContent);
            } catch (\Exception $e) {
                $ttdBase64 = null;
            }
        }
    @endphp

    <div class="ttd-container">
        <div class="ttd-box">
            <p>Kuala Kapuas, {{ now()->locale('id')->translatedFormat('d F Y') }}</p>

            @if (isset($kepalaDinas))
                <p>{{ $kepalaDinas->jabatan }}</p>
                @if ($kepalaDinas->status_pejabat !== 'Definitif')
                    <p>({{ $kepalaDinas->status_pejabat }})</p>
                @endif

                @if ($ttdBase64)
                    <img src="{{ $ttdBase64 }}" alt="Tanda Tangan"
                        style="height: 80px; width: auto; margin-top: 10px; margin-bottom: 5px;">
                @else
                    <br><br><br><br>
                @endif

                <p class="nama-kadis">{{ $kepalaDinas->nama_lengkap }}</p>
                <p>NIP. {{ $kepalaDinas->nip }}</p>
            @else
                <p>Kepala Dinas Sosial</p>
                <p>Kabupaten Kapuas,</p>
                <br><br><br><br>
                <p class="nama-kadis">( BELUM DIATUR )</p>
                <p>NIP. -</p>
            @endif
        </div>
        <div style="clear:both;"></div>
    </div>
</body>

</html>
