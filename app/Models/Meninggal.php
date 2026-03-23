<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meninggal extends Model
{
    use HasFactory;

    protected $table = 'meninggal';
    protected $guarded = [];

    protected $casts = [
        'tanggal_meninggal' => 'date',
        'program_terdampak' => 'array',
    ];

    public function dtks()
    {
        return $this->belongsTo(DTKS::class, 'dtks_id');
    }
}
