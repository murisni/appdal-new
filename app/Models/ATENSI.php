<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ATENSI extends Model
{
    protected $table = 'atensi';
    protected $guarded = [];

    public function dtks()
    {
        return $this->belongsTo(DTKS::class, 'dtks_id');
    }
}
