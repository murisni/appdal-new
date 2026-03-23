<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Meninggal KPM</title>
    <style>
        @page { size: A4 portrait; margin: 1.5cm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; color: #000; }
        .kop-surat { width: 100%; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 15px; position: relative; text-align: center; }
        .kop-surat img { position: absolute; left: 0; top: 0; width: 80px; height: auto; }
        .kop-surat h1 { margin: 0; font-size: 14pt; font-weight: normal; letter-spacing: 1px; }
        .kop-surat h2 { margin: 0; font-size: 16pt; font-weight: bold; letter-spacing: 1px; }
        .kop-surat p { margin: 2px 0 0 0; font-size: 10pt; }
        .judul-laporan { text-align: center; font-weight: bold; font-size: 12pt; text-transform: uppercase; margin-bottom: 5px; }
        .info-filter { text-align: center; font-size: 10pt; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #000; padding: 6px 8px; vertical-align: middle; text-align: center; }
        thead th { background-color: #e0e0e0; font-weight: bold; font-size: 9pt; }
        tbody td { font-size: 9pt; text-align: left; }
        tbody td.text-center { text-align: center; }
        .text-danger { color: #d32f2f; font-weight: bold; }
        .ttd-container { width: 100%; margin-top: 30px; page-break-inside: avoid; }
        .ttd-box { float: right; width: 300px; text-align: center; }
        .nama-kadis { margin-top: 60px; font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>

    <div class="kop-surat">
        <img src="{{ public_path('images/logo-kapuas.png') }}" alt="Logo Kapuas">
        <h1>PEMERINTAH KABUPATEN KAPUAS</h1>
        <h2>DINAS SOSIAL</h2>
        <p>Jalan Pemuda Km. 5,5 Kuala Kapuas, Kalimantan Tengah 73514</p>
        <p>Telepon: (0513) XXXXXX | Email: dinsos@kapuaskab.go.id</p>
    </div>

    <div class="judul-laporan">REKAPITULASI LAPORAN MENINGGAL KPM (PENERIMA BANTUAN)</div>

    <div class="info-filter">
        @php
            $programLabel = match(request('program', 'semua')) {
                'pkh'    => 'PKH',
                'bpnt'   => 'BPNT',
                'pbijk'  => 'PBI-JK',
                'atensi' => 'ATENSI',
                default  => 'Semua Program',
            };
        @endphp
        Program: {{ $programLabel }} &nbsp;|&nbsp;
        Periode:
        @if(request('tipe_laporan') == 'harian')
            {{ \Carbon\Carbon::parse(request('tanggal_mulai'))->translatedFormat('d F Y') }}
            s/d
            {{ \Carbon\Carbon::parse(request('tanggal_sampai'))->translatedFormat('d F Y') }}
        @elseif(request('tipe_laporan') == 'bulanan')
            Bulan {{ \Carbon\Carbon::createFromFormat('m', request('bulan'))->translatedFormat('F') }}
            Tahun {{ request('tahun') }}
        @elseif(request('tipe_laporan') == 'triwulan')
            Triwulan {{ request('triwulan') }} Tahun {{ request('tahun') }}
        @else
            Tahun {{ request('tahun') }}
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 16%;">No. KK</th>
                <th style="width: 20%;">Nama Almarhum/ah</th>
                <th style="width: 10%;">Tgl Meninggal</th>
                <th style="width: 20%;">Ahli Waris / Pengganti</th>
                <th style="width: 15%;">Program Terdampak</th>
                <th style="width: 15%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>

                {{-- No. KK + Kecamatan --}}
                <td class="text-center">
                    {{ $row->dtks->no_kk ?? '-' }}
                    @if($row->dtks)
                        <br><small>{{ $row->dtks->kecamatan }}</small>
                    @endif
                </td>

                {{-- Nama Almarhum --}}
                <td>
                    <b class="text-danger">{{ $row->nama_almarhum ?? '-' }}</b>
                    <br><small>NIK: {{ $row->nik_almarhum ?? '-' }}</small>
                    <br><small>{{ $row->status_hubungan ?? '-' }}</small>
                </td>

                {{-- Tanggal Meninggal --}}
                <td class="text-center">
                    {{ $row->tanggal_meninggal
                        ? \Carbon\Carbon::parse($row->tanggal_meninggal)->translatedFormat('d M Y')
                        : '-' }}
                </td>

                {{-- Ahli Waris --}}
                <td>
                    @if($row->nama_pengganti)
                        <b>{{ $row->nama_pengganti }}</b>
                        <br><small>NIK: {{ $row->nik_pengganti ?? '-' }}</small>
                        <br><small>{{ $row->hubungan_pengganti ?? '-' }}</small>
                    @else
                        <span style="color: gray;"><i>Tidak ada pengganti</i></span>
                    @endif
                </td>

                {{-- Program Terdampak --}}
                <td class="text-center">
                    @if($row->program_terdampak)
                        @php
                            $programs = is_array($row->program_terdampak)
                                ? $row->program_terdampak
                                : json_decode($row->program_terdampak, true);
                        @endphp
                        @foreach($programs ?? [] as $p)
                            <span>{{ $p }}</span><br>
                        @endforeach
                    @else
                        -
                    @endif
                </td>

                {{-- Catatan --}}
                <td>{{ $row->catatan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px;">
                    Tidak ada KPM meninggal dunia pada periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="ttd-container">
        <div class="ttd-box">
            <p>Kuala Kapuas, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Kepala Dinas Sosial</p>
            <p>Kabupaten Kapuas,</p>
            <br>
            <p class="nama-kadis">( NAMA KEPALA DINAS )</p>
            <p>NIP. 1970XXXXXXXXXXXXXX</p>
        </div>
        <div style="clear:both;"></div>
    </div>

</body>
</html>