<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PKH extends Model
{
    protected $table = 'pkh';
    protected $guarded = [];

    public function dtks()
    {
        return $this->belongsTo(DTKS::class, 'dtks_id');
    }

    protected $casts = [
        'histori_penerimaan' => 'array',
    ];
}
