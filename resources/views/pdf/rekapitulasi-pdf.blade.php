<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Bantuan Sosial</title>
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

        /* Kunci height agar kedua logo seimbang dan simetris */
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
            vertical-align: middle;
            text-align: center;
        }

        thead th {
            background-color: #e0e0e0;
            font-weight: bold;
            font-size: 9pt;
        }

        tbody td {
            font-size: 8pt;
            text-align: left;
        }

        tbody td.text-center {
            text-align: center;
        }

        .text-danger {
            color: #d32f2f;
            font-weight: bold;
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
    </style>
</head>

<body>

    @php
        $program = request('program', 'semua');
        $statusVerif = request('status_verifikasi', 'semua');

        function getKepalaKeluarga($row)
        {
            $anggota = is_array($row->anggota_keluarga)
                ? $row->anggota_keluarga
                : json_decode($row->anggota_keluarga, true);

            if (is_array($anggota)) {
                foreach ($anggota as $a) {
                    if (isset($a['status_hubungan']) && strtolower($a['status_hubungan']) === 'kepala keluarga') {
                        return $a;
                    }
                }
                return $anggota[0] ?? ['nama' => '-', 'nik' => '-'];
            }
            return ['nama' => '-', 'nik' => '-'];
        }

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

    <div class="judul-laporan">
        REKAPITULASI PENERIMA BANTUAN SOSIAL
    </div>

    <div class="info-filter">
        @php
            $labelProgram = match ($program) {
                'pkh' => 'PKH (Program Keluarga Harapan)',
                'bpnt' => 'BPNT (Bantuan Pangan Non Tunai)',
                'pbijk' => 'PBI-JK (Penerima Bantuan Iuran Jaminan Kesehatan)',
                'atensi' => 'ATENSI (Asistensi Rehabilitasi Sosial)',
                default => 'Semua DTKS',
            };

            $labelStatus = match ($statusVerif) {
                'ditinjau' => 'Dalam Peninjauan',
                'diproses' => 'Sedang Disurvey',
                'diterima' => 'Diterima (Layak & Aktif)',
                'ditolak' => 'Ditolak / Tidak Layak',
                default => 'Semua Status',
            };
        @endphp

        {{-- Baris Program & Status --}}
        <div style="font-weight: bold; font-size: 11pt; margin-bottom: 3px;">
            @if ($program === 'semua')
                Semua DTKS
            @else
                {{ $labelProgram }} — {{ $labelStatus }}
            @endif
        </div>

        {{-- Baris Periode --}}
        <div>
            Periode:
            @if (request('tipe_laporan') == 'harian')
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
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 15%;">No. KK</th>

                @if ($program === 'atensi')
                    <th style="width: 10%;">NIK Penerima</th>
                    <th style="width: 20%;">Nama Penerima</th>
                @else
                    <th style="width: 20%;">Nama Kepala Keluarga</th>
                @endif

                @if ($program === 'semua')
                    <th style="width: 20%;">Alamat</th>
                    <th style="width: 22%;">Status Program Bantuan</th>
                @elseif($program === 'pkh')
                    <th>Kondisi (Bumil/Balita)</th>
                    <th>Tanggungan Anak Sekolah</th>
                    @if ($statusVerif === 'ditolak')
                        <th>Catatan Penolakan</th>
                    @endif
                @elseif($program === 'bpnt')
                    <th>Nomor Kartu KKS</th>
                    <th>Status Penerimaan Sembako</th>
                    @if ($statusVerif === 'ditolak')
                        <th>Catatan Penolakan</th>
                    @endif
                @elseif($program === 'pbijk')
                    <th>Nomor BPJS KIS</th>
                    <th>Faskes Tingkat 1</th>
                    @if ($statusVerif === 'ditolak')
                        <th>Catatan Penolakan</th>
                    @endif
                @elseif($program === 'atensi')
                    <th>Kategori Sasaran</th>
                    <th>Jenis & Nominal Bantuan</th>
                    @if ($statusVerif === 'ditolak')
                        <th>Catatan Penolakan</th>
                    @endif
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $row)
                @php $kepala = getKepalaKeluarga($row); @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>

                    {{-- No. KK --}}
                    <td class="text-center">{{ $row->no_kk }}</td>

                    {{-- Kolom identitas --}}
                    @if ($program === 'atensi')
                        {{-- ATENSI: NIK dan Nama Penerima individu --}}
                        <td class="text-center">
                            {{ $row->atensi->nik_penerima ?? ($kepala['nik'] ?? '-') }}
                        </td>
                        <td>
                            <b>{{ $row->atensi->nama_penerima ?? ($kepala['nama'] ?? '-') }}</b>
                            @if ($row->status_kpm === 'Meninggal')
                                <br><small class="text-danger"><i>⚠ Wafat</i></small>
                            @endif
                        </td>
                    @else
                        {{-- PKH, BPNT, PBIJK, SEMUA: Nama Kepala Keluarga --}}
                        <td>
                            <b>{{ $kepala['nama'] ?? '-' }}</b>
                            <br><small>NIK: {{ $kepala['nik'] ?? '-' }}</small>
                            @if ($row->status_kpm === 'Meninggal')
                                <br><small class="text-danger">
                                    <i>⚠ Wafat — Pengganti: {{ $row->nama_pengganti ?? '-' }}</i>
                                </small>
                            @endif
                        </td>
                    @endif

                    {{-- DATA DINAMIS BERDASARKAN PROGRAM --}}
                    @if ($program === 'semua')
                        <td>{{ $row->alamat ?? '-' }}</td>
                        <td>
                            <ul style="margin:0; padding-left:15px; font-size:8pt;">
                                @if ($row->pkh)
                                    <li>PKH: {{ ucfirst($row->pkh->status) }}</li>
                                @endif
                                @if ($row->bpnt)
                                    <li>BPNT: {{ ucfirst($row->bpnt->status) }}</li>
                                @endif
                                @if ($row->pbijk)
                                    <li>PBI-JK: {{ ucfirst($row->pbijk->status) }}</li>
                                @endif
                                @if ($row->atensi)
                                    <li>ATENSI: {{ ucfirst($row->atensi->status) }}</li>
                                @endif
                                @if (!$row->pkh && !$row->bpnt && !$row->pbijk && !$row->atensi)
                                    <li>Belum ada program</li>
                                @endif
                            </ul>
                        </td>
                    @elseif($program === 'pkh' && $row->pkh)
                        <td>
                            Ibu Hamil: {{ $row->pkh->ibu_hamil ? 'Ya' : 'Tidak' }}<br>
                            Balita: {{ $row->pkh->anak_usia_dini ? 'Ya' : 'Tidak' }}
                        </td>
                        <td>
                            SD: {{ $row->pkh->jumlah_sd }} org<br>
                            SMP: {{ $row->pkh->jumlah_smp }} org<br>
                            SMA: {{ $row->pkh->jumlah_sma }} org
                        </td>
                        @if ($statusVerif === 'ditolak')
                            <td><i class="text-danger">{{ $row->pkh->catatan_surveyor ?? 'Tidak ada catatan' }}</i>
                            </td>
                        @endif
                    @elseif($program === 'bpnt' && $row->bpnt)
                        <td class="text-center"><b>{{ $row->bpnt->no_kartu_kks ?? '-' }}</b></td>
                        <td class="text-center">
                            {{ $row->bpnt->status_pangan === 'terima' ? 'Aktif Menerima' : 'Tidak / Saldo Kosong' }}
                        </td>
                        @if ($statusVerif === 'ditolak')
                            <td><i class="text-danger">{{ $row->bpnt->catatan_surveyor ?? 'Tidak ada catatan' }}</i>
                            </td>
                        @endif
                    @elseif($program === 'pbijk' && $row->pbijk)
                        <td class="text-center"><b>{{ $row->pbijk->nomor_bpjs ?? '-' }}</b></td>
                        <td class="text-center">{{ $row->pbijk->faskes_tingkat_1 ?? '-' }}</td>
                        @if ($statusVerif === 'ditolak')
                            <td><i class="text-danger">{{ $row->pbijk->catatan_surveyor ?? 'Tidak ada catatan' }}</i>
                            </td>
                        @endif
                    @elseif($program === 'atensi' && $row->atensi)
                        <td class="text-center">{{ strtoupper($row->atensi->kategori ?? '-') }}</td>
                        <td>
                            {{ $row->atensi->jenis_bantuan_diterima ?? '-' }}<br>
                            <b>Rp {{ number_format($row->atensi->nominal_bantuan ?? 0, 0, ',', '.') }}</b>
                        </td>
                        @if ($statusVerif === 'ditolak')
                            <td><i class="text-danger">{{ $row->atensi->catatan_surveyor ?? 'Tidak ada catatan' }}</i>
                            </td>
                        @endif
                    @else
                        <td colspan="4" class="text-center text-danger">
                            Data program tidak ditemukan untuk KPM ini.
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px;">
                        Tidak ada data penerima bantuan pada kriteria ini.
                    </td>
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
