<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PBIJK extends Model
{
    protected $table = 'pbijk';
    protected $guarded = [];

    public function dtks()
    {
        return $this->belongsTo(DTKS::class, 'dtks_id');
    }
}
