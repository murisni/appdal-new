<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriPenerimaan extends Model
{
    use HasFactory;

    protected $table = 'histori_penerimaan';
    protected $guarded = [];

    protected $casts = [
        'tanggal_terima'  => 'date',
        'nominal_bantuan' => 'decimal:2',
    ];

    public function dtks()
    {
        return $this->belongsTo(DTKS::class, 'dtks_id');
    }
}
