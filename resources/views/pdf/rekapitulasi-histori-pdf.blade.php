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
    </style>
</head>
<body>

    <div class="kop-surat">
        <img src="{{ public_path('images/logo-kapuas.png') }}" alt="Logo">
        <h1>PEMERINTAH KABUPATEN KAPUAS</h1>
        <h2>DINAS SOSIAL</h2>
        <p>Jalan Pemuda Km. 5,5 Kuala Kapuas, Kalimantan Tengah 73514</p>
    </div>

    <div class="judul-laporan">REKAPITULASI HISTORI PENYALURAN BANTUAN PROGRAM {{ strtoupper($program) }}</div>
    
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
                <th style="width: 70%;">Jejak Penyaluran / Histori (Tanggal, Catatan, & Bukti Foto)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $row)
            @php
                // Cari Kepala Keluarga
                $anggota = is_string($row->anggota_keluarga) ? json_decode($row->anggota_keluarga, true) : $row->anggota_keluarga;
                $namaKpm = '-';
                if (is_array($anggota)) {
                    foreach ($anggota as $a) {
                        if (isset($a['status_hubungan']) && strtolower($a['status_hubungan']) === 'kepala keluarga') { $namaKpm = $a['nama']; break; }
                    }
                    if($namaKpm === '-') $namaKpm = $anggota[0]['nama'] ?? '-';
                }

                // Ambil Histori
                $historiData = $row->$program->histori_penerimaan ?? [];
                if(is_string($historiData)) $historiData = json_decode($historiData, true);
            @endphp
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>
                    <b>{{ $namaKpm }}</b><br>
                    No KK: {{ $row->no_kk }}
                </td>
                <td>
                    @if(!empty($historiData) && is_array($historiData))
                        <table style="width: 100%; border: none; margin: 0;">
                            @foreach($historiData as $h)
                            <tr style="border-bottom: 1px dashed #ccc;">
                                <td style="border: none; width: 60%;">
                                    <b>Tanggal:</b> {{ isset($h['tanggal_diterima']) ? \Carbon\Carbon::parse($h['tanggal_diterima'])->translatedFormat('d F Y') : '-' }}<br>
                                    <b>Catatan:</b> {{ $h['catatan'] ?? 'Tidak ada catatan' }}
                                </td>
                                <td style="border: none; width: 40%; text-align: center;">
                                    @if(!empty($h['foto_bukti']))
                                        <img src="{{ public_path('storage/' . $h['foto_bukti']) }}" class="foto-bukti" alt="Bukti">
                                    @else
                                        <i>Tidak ada foto</i>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    @else
                        <i>Belum ada histori detail.</i>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="3" style="text-align: center; padding: 20px;">Tidak ada histori KPM pada program dan periode ini.</td></tr>
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