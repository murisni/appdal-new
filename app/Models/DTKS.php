<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DTKS extends Model
{
    use HasFactory;

    protected $table = 'dtks';
    protected $guarded = [];

    protected $casts = [
        'anggota_keluarga' => 'array',
        'verified_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    const STATUS_DITINJAU = 'ditinjau';
    const STATUS_DIPROSES = 'diproses';
    const STATUS_DITERIMA = 'diterima';
    const STATUS_DITOLAK  = 'ditolak';

    public function pkh()
    {
        return $this->hasOne(PKH::class, 'dtks_id');
    }

    public function bpnt()
    {
        return $this->hasOne(BPNT::class, 'dtks_id');
    }

    public function pbijk()
    {
        return $this->hasOne(PBIJK::class, 'dtks_id');
    }

    public function atensi()
    {
        return $this->hasOne(ATENSI::class, 'dtks_id');
    }

    public function isSurveyed(): bool
    {
        return !empty($this->latitude) && !empty($this->foto_rumah_depan);
    }

    public function meninggal()
    {
        return $this->hasMany(Meninggal::class, 'dtks_id');
    }

    public function historiPenerimaan()
    {
        return $this->hasMany(HistoriPenerimaan::class, 'dtks_id');
    }

    public function hitungSkorLengkap(): int
    {
        $skor = 0;

        $totalPenghasilan = ($this->penghasilan_per_bulan ?? 0) + ($this->penghasilan_lainnya ?? 0);

        $tanggungan = ($this->jumlah_tanggungan ?? 0) + 1;

        $penghasilanPerKapita = $totalPenghasilan / $tanggungan;

        if ($totalPenghasilan > 4000000 || $this->daya_listrik === '2200') {
            return 0;
        }

        if ($penghasilanPerKapita <= 500000) {
            $skor += 35;
        } elseif ($penghasilanPerKapita <= 1000000) {
            $skor += 20;
        } elseif ($penghasilanPerKapita <= 1500000) {
            $skor += 10;
        }

        if ($this->daya_listrik === 'Non-PLN') {
            $skor += 25;
        } elseif ($this->daya_listrik === '450') {
            $skor += 15;
        } elseif ($this->daya_listrik === '900') {
            $skor += 5;
        } elseif ($this->daya_listrik === '1300') {
            $skor -= 10;
        }

        if ($this->jenis_lantai === 'Tanah') {
            $skor += 20;
        } elseif ($this->jenis_lantai === 'Bambu') {
            $skor += 15;
        } elseif ($this->jenis_lantai === 'Semen') {
            $skor += 5;
        } elseif ($this->jenis_lantai === 'Keramik') {
            $skor -= 10;
        }

        if ($this->sumber_air === 'Sungai') {
            $skor += 15;
        } elseif ($this->sumber_air === 'Sumur') {
            $skor += 5;
        } elseif ($this->sumber_air === 'PDAM') {
            $skor -= 5;
        }

        if ($this->ada_lansia_disabilitas) {
            $skor += 15;
        }

        return max($skor, 0);
    }

    public function getEstimasiDesilAttribute(): string
    {
        $totalSkor = $this->hitungSkorLengkap();

        if ($totalSkor >= 70) return 'Desil 1 (Sangat Miskin)';
        if ($totalSkor >= 50) return 'Desil 2 (Miskin)';
        if ($totalSkor >= 30) return 'Desil 3 (Hampir Miskin)';
        return 'Desil 4 (Rentan Miskin)';
    }

    public function getRekomendasiBantuan(): array
    {
        $skor = $this->hitungSkorLengkap();
        $rekomendasi = [];

        if ($skor >= 30) {
            $rekomendasi[] = 'PBI-JK';
        }

        if ($skor >= 50) {
            $rekomendasi[] = 'BPNT';
        }

        $punyaKomponenPKH = false;
        if (is_array($this->anggota_keluarga)) {
            foreach ($this->anggota_keluarga as $anggota) {
                if (isset($anggota['tanggal_lahir'])) {
                    $umur = \Carbon\Carbon::parse($anggota['tanggal_lahir'])->age;
                    if ($umur <= 18 || $umur >= 60) {
                        $punyaKomponenPKH = true;
                        break;
                    }
                }
            }
        }
        if ($this->ada_lansia_disabilitas) {
            $punyaKomponenPKH = true;
        }

        if ($skor >= 50 && $punyaKomponenPKH) {
            $rekomendasi[] = 'PKH';
        }

        if ($this->ada_lansia_disabilitas) {
            $rekomendasi[] = 'ATENSI';
        }

        return $rekomendasi;
    }
}
