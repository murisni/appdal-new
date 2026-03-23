<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;
use Carbon\Carbon;
use App\Models\DTKS;

class DtksSeeder extends Seeder
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
        Schema::enableForeignKeyConstraints();

        $nikCounter = 1;

        $dataWilayah = [
            'Selat'        => ['Selat Dalam', 'Selat Hilir', 'Selat Hulu', 'Selat Tengah', 'Selat Utara', 'Selat Barat', 'Murung Keramat', 'Panamas', 'Pulau Telo', 'Pulau Telo Baru'],
            'Kapuas Hilir' => ['Barimba', 'Dahirang', 'Hampatung', 'Mambulau', 'Sei Pasah', 'Bakungin', 'Saka Batur', 'Sei Asam'],
            'Bataguh'      => ['Bamban Raya', 'Bangun Harjo', 'Budi Mufakat', 'Pulau Kupang', 'Pulau Mambulau', 'Sei Jangkit', 'Sei Lunuk', 'Tamban Luar'],
            'Basarang'     => ['Basarang', 'Basarang Jaya', 'Basungkai', 'Batu Nindan', 'Batuah', 'Bungai Jaya', 'Lunuk Ramba', 'Maluen'],
        ];

        $pathKk         = $this->createDummyImage('data-kk/dummy-kk.png');
        $pathSktm       = $this->createDummyImage('data-sktm/dummy-sktm.png');
        $pathRumahDepan = $this->createDummyImage('rumah/dummy-depan.png');
        $pathRumahTamu  = $this->createDummyImage('rumah/dummy-tamu.png');
        $pathRumahDapur = $this->createDummyImage('rumah/dummy-dapur.png');
        $pathKtp        = $this->createDummyImage('data-ktp/dummy-ktp.png');
        $pathMeninggal  = $this->createDummyImage('bukti-meninggal/dummy-meninggal.png');
        $pathBukti      = $this->createDummyImage('bukti-penyerahan/dummy-bukti.png');

        $statusSkenarios  = ['ditinjau', 'diproses', 'diterima', 'ditolak'];
        $statusBantuanArr = ['ditinjau', 'diproses', 'diterima', 'diterima', 'ditolak'];

        for ($i = 0; $i < 300; $i++) {
            $no_kk            = '631122334455' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            $jumlahTanggungan = $faker->numberBetween(2, 5);
            $anggotaKeluarga  = [];
            $namaKepalaKeluarga = '';
            $nikKepala          = '';

            for ($j = 0; $j < $jumlahTanggungan; $j++) {
                $nik          = '637111223344' . str_pad($nikCounter, 4, '0', STR_PAD_LEFT);
                $nikCounter++;
                $tanggalLahir = $faker->dateTimeBetween('-75 years', '-1 years')->format('Y-m-d');
                $umur         = Carbon::parse($tanggalLahir)->age;

                $pekerjaan = match (true) {
                    $umur >= 6 && $umur <= 18 => 'Pelajar / Mahasiswa',
                    $umur > 18                => $faker->randomElement(['Petani / Pekebun', 'Buruh', 'Mengurus Rumah Tangga']),
                    default                   => 'Belum / Tidak Bekerja',
                };

                $statusHubungan = match ($j) {
                    0       => 'Kepala Keluarga',
                    1       => $umur > 17 ? 'Istri' : 'Anak',
                    default => 'Anak',
                };

                $namaAnggota = $faker->name($j === 0 ? 'male' : ($j === 1 ? 'female' : null));

                if ($j === 0) {
                    $namaKepalaKeluarga = $namaAnggota;
                    $nikKepala          = $nik;
                }

                $anggotaKeluarga[] = [
                    'nik'             => $nik,
                    'nama'            => $namaAnggota,
                    'jenis_kelamin'   => $faker->randomElement(['L', 'P']),
                    'tanggal_lahir'   => $tanggalLahir,
                    'tempat_lahir'    => $faker->city(),
                    'status_hubungan' => $statusHubungan,
                    'agama'           => $faker->randomElement(['Islam', 'Kristen', 'Katolik']),
                    'pendidikan'      => $faker->randomElement(['SD Sederajat', 'SMP Sederajat', 'SMA / SLTA Sederajat', 'Lain-Lainnya']),
                    'pekerjaan'       => $pekerjaan,
                    'file_ktp'        => $umur >= 17 ? $pathKtp : null,
                ];
            }

            $kecamatanPilih = array_rand($dataWilayah);
            $kelurahanPilih = $faker->randomElement($dataWilayah[$kecamatanPilih]);
            $statusDTKS     = $faker->randomElement($statusSkenarios);

            $catatanPeninjau = null;
            $catatanSurveyor = null;
            $verifiedAt      = null;

            if ($statusDTKS === 'diproses') {
                $catatanPeninjau = 'Berkas administrasi lengkap, mohon tim lapangan cek kondisi rumah.';
            } elseif ($statusDTKS === 'diterima') {
                $catatanPeninjau = 'Berkas administrasi valid.';
                $catatanSurveyor = 'Kondisi rumah memprihatinkan, sesuai dengan kriteria KPM.';
                $verifiedAt      = now()->subDays($faker->numberBetween(1, 60));
            } elseif ($statusDTKS === 'ditolak') {
                $catatanPeninjau = 'Berkas lengkap.';
                $catatanSurveyor = 'Ditolak: Temuan lapangan KPM memiliki usaha besar dan kendaraan roda empat.';
                $verifiedAt      = now()->subDays($faker->numberBetween(1, 30));
            }

            // 10% dari yang diterima akan jadi dummy meninggal
            $akanMeninggal    = $statusDTKS === 'diterima' && $faker->boolean(10);
            $adaPengganti     = $akanMeninggal && count($anggotaKeluarga) > 1;
            $namaPengganti    = $adaPengganti ? $anggotaKeluarga[1]['nama'] : null;
            $nikPengganti     = $adaPengganti ? $anggotaKeluarga[1]['nik'] : null;
            $hubunganPengganti = $adaPengganti
                ? ($anggotaKeluarga[1]['status_hubungan'] === 'Istri' ? 'Istri' : 'Anak')
                : null;
            $tanggalMeninggal = $akanMeninggal
                ? $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d')
                : null;

            $dtks = DTKS::create([
                'no_kk'                    => $no_kk,
                'alamat'                   => $faker->streetAddress(),
                'rt'                       => str_pad($faker->numberBetween(1, 15), 3, '0', STR_PAD_LEFT),
                'rw'                       => str_pad($faker->numberBetween(1, 5), 3, '0', STR_PAD_LEFT),
                'provinsi'                 => 'Kalimantan Tengah',
                'kabupaten'                => 'Kapuas',
                'kecamatan'                => $kecamatanPilih,
                'kelurahan'                => $kelurahanPilih,
                'anggota_keluarga'         => $anggotaKeluarga,
                'detail_pekerjaan'         => $faker->randomElement(['Buruh Tani Serabutan', 'Pedagang Asongan', 'Tukang Becak', 'Pencari Barang Bekas']),
                'nama_tempat_kerja'        => '-',
                'penghasilan_per_bulan'    => $faker->numberBetween(5, 15) * 100000,
                'penghasilan_lainnya'      => 0,
                'pengeluaran_per_bulan'    => $faker->numberBetween(6, 12) * 100000,
                'jumlah_tanggungan'        => (string)($jumlahTanggungan - 1),
                'status_kepemilikan_rumah' => $faker->randomElement(['Milik Sendiri', 'Sewa / Kontrak', 'Bebas Sewa (Numpang)']),
                'daya_listrik'             => $faker->randomElement(['Non-PLN', '450', '900']),
                'jenis_lantai'             => $faker->randomElement(['Tanah', 'Bambu', 'Semen']),
                'sumber_air'               => $faker->randomElement(['Sungai', 'Sumur', 'PDAM']),
                'ada_lansia_disabilitas'   => $faker->boolean(30),
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

            // SEEDING BANTUAN HANYA JIKA DTKS DITERIMA
            if ($statusDTKS === 'diterima') {
                $bantuanAcak = $faker->randomElements(['PKH', 'BPNT', 'PBIJK', 'ATENSI'], $faker->numberBetween(1, 4));

                if (in_array('PKH', $bantuanAcak)) {
                    $stat    = $faker->randomElement($statusBantuanArr);
                    $histori = $stat === 'diterima' ? $this->generateHistori($faker) : null;
                    DB::table('pkh')->insert([
                        'dtks_id'            => $dtks->id,
                        'ibu_hamil'          => $faker->boolean(20),
                        'anak_usia_dini'     => $faker->boolean(30),
                        'jumlah_sd'          => $faker->numberBetween(0, 2),
                        'jumlah_smp'         => $faker->numberBetween(0, 1),
                        'jumlah_sma'         => $faker->numberBetween(0, 1),
                        'disabilitas_berat'  => $faker->boolean(10),
                        'lanjut_usia'        => $faker->boolean(20),
                        'status_penerima'    => $stat === 'diterima' ? 'aktif' : 'belum aktif',
                        'status'             => $stat,
                        'catatan_peninjau'   => $stat === 'diproses' ? 'Mohon verifikasi komponen di lapangan.' : null,
                        'catatan_surveyor'   => $stat === 'ditolak' ? 'Tidak memenuhi syarat komponen PKH.' : null,
                        'histori_penerimaan' => $histori ? json_encode($histori) : null,
                        'status_kpm'         => $akanMeninggal ? 'Meninggal' : 'Aktif',
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                }

                if (in_array('BPNT', $bantuanAcak)) {
                    $stat    = $faker->randomElement($statusBantuanArr);
                    $histori = $stat === 'diterima' ? $this->generateHistori($faker) : null;
                    DB::table('bpnt')->insert([
                        'dtks_id'            => $dtks->id,
                        'no_kartu_kks'       => $stat === 'diterima' ? $faker->numerify('1234############') : null,
                        'status_pangan'      => $stat === 'diterima' ? 'terima' : 'tidak',
                        'status'             => $stat,
                        'catatan_peninjau'   => $stat === 'diproses' ? 'Tunggu pencetakan kartu KKS.' : null,
                        'catatan_surveyor'   => $stat === 'ditolak' ? 'KKS tidak aktif.' : null,
                        'histori_penerimaan' => $histori ? json_encode($histori) : null,
                        'status_kpm'         => $akanMeninggal ? 'Meninggal' : 'Aktif',
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                }

                if (in_array('PBIJK', $bantuanAcak)) {
                    $stat        = $faker->randomElement($statusBantuanArr);
                    $anggotaAcak = $faker->randomElement($anggotaKeluarga);
                    $histori     = $stat === 'diterima' ? $this->generateHistori($faker) : null;
                    DB::table('pbijk')->insert([
                        'dtks_id'            => $dtks->id,
                        'nama_penerima'      => $anggotaAcak['nama'],
                        'nomor_bpjs'         => $stat === 'diterima' ? $faker->numerify('000#########') : null,
                        'faskes_tingkat_1'   => 'Puskesmas ' . $kecamatanPilih,
                        'status'             => $stat,
                        'catatan_peninjau'   => $stat === 'diproses' ? 'Verifikasi aktivasi KIS ke BPJS Kesehatan.' : null,
                        'catatan_surveyor'   => $stat === 'ditolak' ? 'BPJS tidak aktif.' : null,
                        'histori_penerimaan' => $histori ? json_encode($histori) : null,
                        'status_kpm'         => $akanMeninggal ? 'Meninggal' : 'Aktif',
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                }

                if (in_array('ATENSI', $bantuanAcak)) {
                    $stat    = $faker->randomElement($statusBantuanArr);
                    $histori = $stat === 'diterima' ? $this->generateHistori($faker) : null;
                    DB::table('atensi')->insert([
                        'dtks_id'                => $dtks->id,
                        'nama_penerima'          => $namaKepalaKeluarga,
                        'kategori'               => $faker->randomElement(['anak', 'lansia', 'disabilitas', 'korban_bencana']),
                        'jenis_bantuan_diterima' => 'Paket Sembako & Uang Tunai',
                        'nominal_bantuan'        => $faker->randomElement([500000, 1000000, 1500000]),
                        'status'                 => $stat,
                        'catatan_peninjau'       => $stat === 'diproses' ? 'Survey kelayakan kondisi KPM untuk ATENSI.' : null,
                        'catatan_surveyor'       => $stat === 'ditolak' ? 'Tidak masuk kategori ATENSI.' : null,
                        'histori_penerimaan'     => $histori ? json_encode($histori) : null,
                        'status_kpm'             => $akanMeninggal ? 'Meninggal' : 'Aktif',
                        'created_at'             => now(),
                        'updated_at'             => now(),
                    ]);
                }

                // SEEDING TABEL MENINGGAL
                if ($akanMeninggal) {
                    $programTerdampak = array_values(array_intersect(
                        $bantuanAcak,
                        ['PKH', 'BPNT', 'PBIJK', 'ATENSI']
                    ));

                    DB::table('meninggal')->insert([
                        'dtks_id'            => $dtks->id,
                        'nama_almarhum'      => $namaKepalaKeluarga,
                        'nik_almarhum'       => $nikKepala,
                        'status_hubungan'    => 'Kepala Keluarga',
                        'tanggal_meninggal'  => $tanggalMeninggal,
                        'bukti_meninggal'    => $pathMeninggal,
                        'catatan'            => 'Data dummy seeder.',
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

    private function generateHistori(object $faker): array
    {
        $histori = [];
        $jumlah  = $faker->numberBetween(1, 4);

        for ($h = 0; $h < $jumlah; $h++) {
            $histori[] = [
                'tanggal_terima'     => $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
                'foto_bukti'         => 'bukti-penyerahan/dummy-bukti.png',
                'catatan_penerimaan' => $faker->randomElement([
                    'Penyerahan berjalan lancar.',
                    'KPM hadir langsung.',
                    'Diterima oleh anggota keluarga.',
                    null,
                ]),
            ];
        }

        return $histori;
    }

    private function createDummyImage(string $path): string
    {
        Storage::disk('public')->makeDirectory(dirname($path));
        $base64Png = 'iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAQAAADa613fAAAAcUlEQVR42u3PAQ0AAAjDMLN/aGvwcCBJV8Gb2zBkyJAhQ4YMGTIwZMiQIUKGDBkyZMiQIUOGDBkyZMiQIUKGDBkyZMiQIUOGDBkyZMiQIUKGDBkyZMiQIUOGDBkyZMiQIUKGDBkyZMiQIUOGDBkydN4H2gEAEu4jFAAAAABJRU5ErkJggg==';
        Storage::disk('public')->put($path, base64_decode($base64Png));
        return $path;
    }
}
