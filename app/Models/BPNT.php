<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BPNT extends Model
{
    protected $table = 'bpnt';
    protected $guarded = [];

    public function dtks()
    {
        return $this->belongsTo(DTKS::class, 'dtks_id');
    }
}
