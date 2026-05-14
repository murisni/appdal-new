<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;
use Carbon\Carbon;
use App\Models\DTKS;

class DummySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        Schema::disableForeignKeyConstraints();
        DB::table('dtks')->truncate();
        DB::table('pkh')->truncate();
        DB::table('bpnt')->truncate();
        DB::table('pbijk')->truncate();
        DB::table('atensi')->truncate();
        DB::table('meninggal')->truncate();
        DB::table('histori_penerimaan')->truncate();
        Schema::enableForeignKeyConstraints();

        $fotoDibutuhkan = [
            'ktp.jpg',
            'kk.jpg',
            'rumah_depan.jpg',
            'rumah_dalam.jpg',
            'rumah_dapur.jpg',
            'sktm.jpg',
            'skk.png',
            'pkh.jpg',
            'bpnt.jpg',
            'pbijk.jpg',
            'atensi.jpg'
        ];

        Storage::disk('public')->makeDirectory('dummy');

        foreach ($fotoDibutuhkan as $foto) {
            $sourcePath = public_path('img/' . $foto);
            if (file_exists($sourcePath)) {
                Storage::disk('public')->put('dummy/' . $foto, file_get_contents($sourcePath));
            }
        }

        $pathKtp        = 'dummy/ktp.jpg';
        $pathKk         = 'dummy/kk.jpg';
        $pathRumahDepan = 'dummy/rumah_depan.jpg';
        $pathRumahTamu  = 'dummy/rumah_dalam.jpg';
        $pathRumahDapur = 'dummy/rumah_dapur.jpg';
        $pathSktm       = 'dummy/sktm.jpg';
        $pathMeninggal  = 'dummy/skk.png';

        $nikCounter = 1;
        $kkCounter = 1;

        $dataWilayah = [
            'Basarang' => ['Basarang', 'Basarang Jaya', 'Basungkai', 'Batu Nindan', 'Batuah', 'Bungai Jaya', 'Lunuk Ramba', 'Maluen'],
            'Bataguh' => ['Bamban Raya', 'Bangun Harjo', 'Budi Mufakat', 'Pulau Kupang', 'Pulau Mambulau', 'Sei Jangkit', 'Sei Lunuk', 'Tamban Luar'],
            'Dadahup' => ['Dadahup', 'Dadahup Raya', 'Sumber Alaska', 'Harapan Baru'],
            'Kapuas Barat' => ['Mandomai', 'Saka Mangkahai', 'Sei Pitung', 'Majene'],
            'Kapuas Hilir' => ['Barimba', 'Dahirang', 'Hampatung', 'Mambulau', 'Sei Pasah', 'Bakungin', 'Saka Batur', 'Sei Asam'],
            'Kapuas Hulu' => ['Sei Hanyu', 'Supang', 'Tumbang Puroh', 'Barunang'],
            'Kapuas Kuala' => ['Lupak Dalam', 'Lupak Timur', 'Tamban Baru', 'Wargo Mulyo'],
            'Kapuas Murung' => ['Palingkau Lama', 'Palingkau Baru', 'Tajupan', 'Sukamukti'],
            'Kapuas Tengah' => ['Pujon', 'Bajuh', 'Kayu Bulan', 'Kota Baru'],
            'Kapuas Timur' => ['Anjir Mambulau Barat', 'Anjir Mambulau Tengah', 'Anjir Mambulau Timur', 'Anjir Serapat'],
            'Mandau Talawang' => ['Sei Pinang', 'Tumbang Bukoi', 'Tumbang Manyarung'],
            'Mantangai' => ['Mantangai Hilir', 'Mantangai Tengah', 'Mantangai Hulu', 'Kalumpang'],
            'Pasak Talawang' => ['Jangkang', 'Kaburan', 'Dandang', 'Hurung Kampin'],
            'Pulau Petak' => ['Sei Tatas', 'Sei Tatas Hilir', 'Handiwung', 'Palangkai'],
            'Selat' => ['Selat Dalam', 'Selat Hilir', 'Selat Hulu', 'Selat Tengah', 'Selat Utara', 'Selat Barat', 'Murung Keramat', 'Panamas', 'Pulau Telo', 'Pulau Telo Baru'],
            'Tamban Catur' => ['Tamban Baru Tengah', 'Tamban Baru Timur', 'Tamban Makmur', 'Warna Sari'],
            'Timpah' => ['Timpah', 'Aruk', 'Lungkuh Layang', 'Lawang Kajang']
        ];

        $statusBantuanArr = ['ditinjau', 'diproses', 'diterima', 'diterima', 'ditolak'];

        foreach ($dataWilayah as $kecamatanPilih => $kelurahans) {
            for ($i = 0; $i < 10; $i++) {
                $no_kk = '631122334455' . str_pad($kkCounter++, 4, '0', STR_PAD_LEFT);
                $anggotaKeluarga = [];

                $statBalita = 0;
                $statSD = 0;
                $statSMP = 0;
                $statSMA = 0;
                $statLansia = 0;
                $ibuHamil = false;
                $adaDisabilitas = $faker->boolean(15);

                $umurKepala = $faker->numberBetween(25, 70);
                $tglLahirKepala = Carbon::now()->subYears($umurKepala)->subMonths(rand(1, 11))->format('Y-m-d');
                $namaKepalaKeluarga = $faker->name($faker->boolean(80) ? 'male' : 'female');
                $nikKepala = '637111223344' . str_pad($nikCounter++, 4, '0', STR_PAD_LEFT);

                if ($umurKepala >= 60) $statLansia++;
                $pekerjaanKepala = $umurKepala > 60 ? 'Belum / Tidak Bekerja' : $faker->randomElement(['Buruh Tani Serabutan', 'Pedagang Asongan', 'Tukang Becak', 'Kuli Bangunan']);

                $anggotaKeluarga[] = [
                    'nik'             => $nikKepala,
                    'nama'            => $namaKepalaKeluarga,
                    'jenis_kelamin'   => $faker->randomElement(['L', 'P']),
                    'tanggal_lahir'   => $tglLahirKepala,
                    'tempat_lahir'    => $faker->city(),
                    'status_hubungan' => 'Kepala Keluarga',
                    'agama'           => 'Islam',
                    'pendidikan'      => $faker->randomElement(['SD Sederajat', 'SMP Sederajat']),
                    'pekerjaan'       => $pekerjaanKepala,
                    'file_ktp'        => $pathKtp,
                ];

                $adaIstri = $anggotaKeluarga[0]['jenis_kelamin'] === 'L' && $faker->boolean(80);
                if ($adaIstri) {
                    $umurIstri = $umurKepala - $faker->numberBetween(2, 8);
                    $tglLahirIstri = Carbon::now()->subYears($umurIstri)->format('Y-m-d');

                    if ($umurIstri >= 60) $statLansia++;
                    if ($umurIstri >= 20 && $umurIstri <= 40 && $faker->boolean(25)) $ibuHamil = true;

                    $anggotaKeluarga[] = [
                        'nik'             => '637111223344' . str_pad($nikCounter++, 4, '0', STR_PAD_LEFT),
                        'nama'            => $faker->name('female'),
                        'jenis_kelamin'   => 'P',
                        'tanggal_lahir'   => $tglLahirIstri,
                        'tempat_lahir'    => $faker->city(),
                        'status_hubungan' => 'Istri',
                        'agama'           => 'Islam',
                        'pendidikan'      => 'SD Sederajat',
                        'pekerjaan'       => 'Mengurus Rumah Tangga',
                        'file_ktp'        => $pathKtp,
                    ];
                }

                $maxUmurAnak = max(1, $umurKepala - 20);
                $jumlahAnak = $faker->numberBetween(0, ($maxUmurAnak > 25 ? 4 : 2));

                for ($a = 0; $a < $jumlahAnak; $a++) {
                    $umurAnak = $faker->numberBetween(1, $maxUmurAnak);
                    $tglLahirAnak = Carbon::now()->subYears($umurAnak)->format('Y-m-d');

                    if ($umurAnak <= 6) $statBalita++;
                    elseif ($umurAnak >= 7 && $umurAnak <= 12) $statSD++;
                    elseif ($umurAnak >= 13 && $umurAnak <= 15) $statSMP++;
                    elseif ($umurAnak >= 16 && $umurAnak <= 18) $statSMA++;

                    $pekerjaanAnak = ($umurAnak > 18) ? 'Belum / Tidak Bekerja' : 'Pelajar / Mahasiswa';

                    $anggotaKeluarga[] = [
                        'nik'             => '637111223344' . str_pad($nikCounter++, 4, '0', STR_PAD_LEFT),
                        'nama'            => $faker->name(),
                        'jenis_kelamin'   => $faker->randomElement(['L', 'P']),
                        'tanggal_lahir'   => $tglLahirAnak,
                        'tempat_lahir'    => $faker->city(),
                        'status_hubungan' => 'Anak',
                        'agama'           => 'Islam',
                        'pendidikan'      => $umurAnak > 18 ? 'SMA / SLTA Sederajat' : 'Belum Tamat SD',
                        'pekerjaan'       => $pekerjaanAnak,
                        'file_ktp'        => $umurAnak >= 17 ? $pathKtp : null,
                    ];
                }

                $menanggungLansia = $faker->boolean(20);
                if ($menanggungLansia) {
                    $umurLansia = min(95, $umurKepala + $faker->numberBetween(20, 30));
                    $tglLahirLansia = Carbon::now()->subYears($umurLansia)->format('Y-m-d');
                    $statLansia++;

                    $anggotaKeluarga[] = [
                        'nik'             => '637111223344' . str_pad($nikCounter++, 4, '0', STR_PAD_LEFT),
                        'nama'            => $faker->name(),
                        'jenis_kelamin'   => $faker->randomElement(['L', 'P']),
                        'tanggal_lahir'   => $tglLahirLansia,
                        'tempat_lahir'    => $faker->city(),
                        'status_hubungan' => $faker->randomElement(['Orang Tua', 'Mertua']),
                        'agama'           => 'Islam',
                        'pendidikan'      => 'Tidak Sekolah',
                        'pekerjaan'       => 'Belum / Tidak Bekerja',
                        'file_ktp'        => $pathKtp,
                    ];
                }

                $jumlahTanggungan = count($anggotaKeluarga) - 1;

                $randStatus = $faker->numberBetween(1, 100);
                if ($randStatus <= 60) $statusDTKS = 'diterima';
                elseif ($randStatus <= 75) $statusDTKS = 'diproses';
                elseif ($randStatus <= 90) $statusDTKS = 'ditinjau';
                else $statusDTKS = 'ditolak';

                $kelurahanPilih = $faker->randomElement($kelurahans);

                $catatanPeninjau = null;
                $catatanSurveyor = null;
                $verifiedAt = null;

                if ($statusDTKS === 'diproses') {
                    $catatanPeninjau = 'Administrasi valid KK dan KTP sinkron, teruskan cek lapangan.';
                } elseif ($statusDTKS === 'diterima') {
                    $catatanPeninjau = 'Berkas lengkap sesuai syarat.';
                    $catatanSurveyor = 'Rumah lantai tanah, penghasilan tidak menentu. Sangat layak dibantu.';
                    $verifiedAt      = now()->subDays($faker->numberBetween(1, 60));
                } elseif ($statusDTKS === 'ditolak') {
                    $catatanPeninjau = 'Berkas lengkap.';
                    $catatanSurveyor = 'KPM menolak disurvey / Ternyata memiliki mobil dan toko besar.';
                    $verifiedAt      = now()->subDays($faker->numberBetween(1, 30));
                }

                $akanMeninggal    = $statusDTKS === 'diterima' && $faker->boolean(15);
                $adaPengganti     = $akanMeninggal && count($anggotaKeluarga) > 1;
                $namaPengganti    = $adaPengganti ? $anggotaKeluarga[1]['nama'] : null;
                $nikPengganti     = $adaPengganti ? $anggotaKeluarga[1]['nik'] : null;
                $hubunganPengganti = $adaPengganti ? $anggotaKeluarga[1]['status_hubungan'] : null;
                $tanggalMeninggal = $akanMeninggal ? $faker->dateTimeBetween('-1 years', 'now')->format('Y-m-d') : null;

                $dtks = DTKS::create([
                    'no_kk'                    => $no_kk,
                    'alamat'                   => $faker->streetAddress(),
                    'rt'                       => str_pad($faker->numberBetween(1, 10), 3, '0', STR_PAD_LEFT),
                    'rw'                       => str_pad($faker->numberBetween(1, 3), 3, '0', STR_PAD_LEFT),
                    'provinsi'                 => 'Kalimantan Tengah',
                    'kabupaten'                => 'Kapuas',
                    'kecamatan'                => $kecamatanPilih,
                    'kelurahan'                => $kelurahanPilih,
                    'anggota_keluarga'         => $anggotaKeluarga,
                    'detail_pekerjaan'         => $pekerjaanKepala,
                    'nama_tempat_kerja'        => '-',
                    'penghasilan_per_bulan'    => $faker->numberBetween(4, 12) * 100000,
                    'penghasilan_lainnya'      => 0,
                    'pengeluaran_per_bulan'    => $faker->numberBetween(5, 15) * 100000,
                    'jumlah_tanggungan'        => (string)$jumlahTanggungan,
                    'status_kepemilikan_rumah' => $faker->randomElement(['Milik Sendiri', 'Sewa / Kontrak', 'Bebas Sewa (Numpang)']),
                    'daya_listrik'             => $faker->randomElement(['Non-PLN', '450', '900']),
                    'jenis_lantai'             => $faker->randomElement(['Tanah', 'Bambu', 'Kayu Kualitas Rendah']),
                    'sumber_air'               => $faker->randomElement(['Sungai', 'Sumur', 'Mata Air']),
                    'ada_lansia_disabilitas'   => ($statLansia > 0 || $adaDisabilitas),
                    'aset_lainnya'             => 'Tidak Ada',
                    'file_kk'                  => $pathKk,
                    'file_sktm'                => $pathSktm,
                    'foto_rumah_depan'         => $pathRumahDepan,
                    'foto_rumah_tamu'          => $pathRumahTamu,
                    'foto_rumah_dapur'         => $pathRumahDapur,
                    'latitude'                 => $faker->latitude(-3.1, -2.9),
                    'longitude'                => $faker->longitude(114.3, 114.5),
                    'status'                   => $statusDTKS,
                    'catatan_peninjau'         => $catatanPeninjau,
                    'catatan_surveyor'         => $catatanSurveyor,
                    'verified_at'              => $verifiedAt,
                    'status_kpm'               => $akanMeninggal ? 'Meninggal' : 'Aktif',
                    'tanggal_meninggal'        => $tanggalMeninggal,
                    'bukti_meninggal'          => $akanMeninggal ? $pathMeninggal : null,
                    'nama_pengganti'           => $namaPengganti,
                    'nik_pengganti'            => $nikPengganti,
                    'hubungan_pengganti'       => $hubunganPengganti,
                ]);

                if ($statusDTKS === 'diterima') {
                    $bantuanAcak = $faker->randomElements(['PKH', 'BPNT', 'PBIJK', 'ATENSI'], $faker->numberBetween(1, 3));

                    if (($statLansia > 0 || $adaDisabilitas) && !in_array('ATENSI', $bantuanAcak)) {
                        $bantuanAcak[] = 'ATENSI';
                    }

                    if (in_array('PKH', $bantuanAcak)) {
                        $stat = $faker->randomElement($statusBantuanArr);
                        DB::table('pkh')->insert([
                            'dtks_id'            => $dtks->id,
                            'ibu_hamil'          => $ibuHamil,
                            'anak_usia_dini'     => $statBalita > 0,
                            'jumlah_sd'          => $statSD,
                            'jumlah_smp'         => $statSMP,
                            'jumlah_sma'         => $statSMA,
                            'disabilitas_berat'  => $adaDisabilitas,
                            'lanjut_usia'        => $statLansia > 0,
                            'status_penerima'    => $stat === 'diterima' ? 'aktif' : 'belum aktif',
                            'status'             => $stat,
                            'catatan_peninjau'   => $stat === 'diproses' ? 'Menunggu kuota Kemensos.' : null,
                            'catatan_surveyor'   => $stat === 'ditolak' ? 'Komponen PKH sudah tidak ada.' : null,
                            'status_kpm'         => $akanMeninggal ? 'Meninggal' : 'Aktif',
                            'created_at'         => now(),
                            'updated_at'         => now(),
                        ]);

                        if ($stat === 'diterima') $this->generateHistori($faker, 'PKH', $dtks->id);
                    }

                    if (in_array('BPNT', $bantuanAcak)) {
                        $stat = $faker->randomElement($statusBantuanArr);
                        DB::table('bpnt')->insert([
                            'dtks_id'            => $dtks->id,
                            'no_kartu_kks'       => $stat === 'diterima' ? $faker->numerify('1234############') : null,
                            'status_pangan'      => $stat === 'diterima' ? 'terima' : 'tidak',
                            'status'             => $stat,
                            'catatan_peninjau'   => $stat === 'diproses' ? 'Proses cetak kartu bank Himbara.' : null,
                            'catatan_surveyor'   => $stat === 'ditolak' ? 'Gagal burekol.' : null,
                            'status_kpm'         => $akanMeninggal ? 'Meninggal' : 'Aktif',
                            'created_at'         => now(),
                            'updated_at'         => now(),
                        ]);

                        if ($stat === 'diterima') $this->generateHistori($faker, 'BPNT', $dtks->id);
                    }

                    if (in_array('PBIJK', $bantuanAcak)) {
                        $stat = $faker->randomElement($statusBantuanArr);
                        DB::table('pbijk')->insert([
                            'dtks_id'            => $dtks->id,
                            'nama_penerima'      => $namaKepalaKeluarga,
                            'nomor_bpjs'         => $stat === 'diterima' ? $faker->numerify('000#########') : null,
                            'faskes_tingkat_1'   => 'Puskesmas ' . $kecamatanPilih,
                            'status'             => $stat,
                            'catatan_peninjau'   => $stat === 'diproses' ? 'Sinkronisasi data BPJS Kesehatan.' : null,
                            'catatan_surveyor'   => $stat === 'ditolak' ? 'Punya asuransi mandiri.' : null,
                            'status_kpm'         => $akanMeninggal ? 'Meninggal' : 'Aktif',
                            'created_at'         => now(),
                            'updated_at'         => now(),
                        ]);

                        if ($stat === 'diterima') $this->generateHistori($faker, 'PBIJK', $dtks->id);
                    }

                    if (in_array('ATENSI', $bantuanAcak)) {
                        $stat = $faker->randomElement($statusBantuanArr);

                        $kategoriAtensi = 'anak';
                        if ($statLansia > 0) $kategoriAtensi = 'lansia';
                        elseif ($adaDisabilitas) $kategoriAtensi = 'disabilitas';

                        DB::table('atensi')->insert([
                            'dtks_id'                => $dtks->id,
                            'nama_penerima'          => $namaKepalaKeluarga,
                            'kategori'               => $kategoriAtensi,
                            'jenis_bantuan_diterima' => 'Paket Sembako & Pemenuhan Kebutuhan Dasar',
                            'nominal_bantuan'        => $faker->randomElement([500000, 800000, 1000000]),
                            'status'                 => $stat,
                            'catatan_peninjau'       => $stat === 'diproses' ? 'Menunggu jadwal penyaluran.' : null,
                            'catatan_surveyor'       => $stat === 'ditolak' ? 'Tidak layak atensi.' : null,
                            'status_kpm'             => $akanMeninggal ? 'Meninggal' : 'Aktif',
                            'created_at'             => now(),
                            'updated_at'             => now(),
                        ]);

                        if ($stat === 'diterima') $this->generateHistori($faker, 'ATENSI', $dtks->id);
                    }

                    if ($akanMeninggal) {
                        $programTerdampak = array_values(array_intersect($bantuanAcak, ['PKH', 'BPNT', 'PBIJK', 'ATENSI']));

                        DB::table('meninggal')->insert([
                            'dtks_id'            => $dtks->id,
                            'nama_almarhum'      => $namaKepalaKeluarga,
                            'nik_almarhum'       => $nikKepala,
                            'status_hubungan'    => 'Kepala Keluarga',
                            'tanggal_meninggal'  => $tanggalMeninggal,
                            'bukti_meninggal'    => $pathMeninggal,
                            'catatan'            => 'Dilaporkan oleh RT setempat.',
                            'nama_pengganti'     => $namaPengganti,
                            'nik_pengganti'      => $nikPengganti,
                            'hubungan_pengganti' => $hubunganPengganti,
                            'program_terdampak'  => json_encode($programTerdampak),
                            'created_at'         => now(),
                            'updated_at'         => now(),
                        ]);
                    }
                }
            }
        }
    }

    private function generateHistori(object $faker, string $jenisBantuan, int $dtksId): void
    {
        $jumlah = $faker->numberBetween(1, 3);

        $fotoBantuan = match ($jenisBantuan) {
            'PKH'    => 'dummy/pkh.jpg',
            'BPNT'   => 'dummy/bpnt.jpg',
            'PBIJK'  => 'dummy/pbijk.jpg',
            'ATENSI' => 'dummy/atensi.jpg',
            default  => 'dummy/pkh.jpg'
        };

        $programEnum = $jenisBantuan === 'PBIJK' ? 'PBI-JK' : $jenisBantuan;

        for ($h = 0; $h < $jumlah; $h++) {
            $catatan = match ($jenisBantuan) {
                'PKH' => 'Pencairan dana tahap ' . ($h + 1) . ' di Bank Himbara/Kantor Pos.',
                'BPNT' => 'Pengambilan beras dan telur di E-Warong.',
                'PBIJK' => 'Iuran dibayarkan oleh pemerintah pusat.',
                'ATENSI' => 'Penyerahan langsung paket sembako di balai desa.',
                default => 'Berhasil disalurkan.'
            };

            $nominal = match ($jenisBantuan) {
                'PKH' => $faker->randomElement([500000, 750000, 1000000]),
                'BPNT' => 200000,
                'PBIJK' => 42000,
                'ATENSI' => $faker->randomElement([500000, 800000, 1000000]),
                default => 0
            };

            DB::table('histori_penerimaan')->insert([
                'dtks_id'            => $dtksId,
                'program'            => $programEnum,
                'tanggal_terima'     => Carbon::now()->subMonths($h * 3)->format('Y-m-d'),
                'periode_bantuan'    => 'Tahap ' . ($h + 1) . ' Tahun ' . Carbon::now()->year,
                'nominal_bantuan'    => $nominal,
                'lokasi_penyerahan'  => 'Balai Desa / Bank Himbara',
                'petugas_penyerah'   => 'Petugas Dinas Sosial',
                'status_penerimaan'  => 'diterima',
                'foto_bukti'         => $fotoBantuan,
                'catatan_penerimaan' => $catatan,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }
    }
}
