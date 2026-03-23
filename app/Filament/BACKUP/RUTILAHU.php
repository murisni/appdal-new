<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RUTILAHU extends Model
{
    protected $table = 'rutilahu';
    protected $guarded = [];

    public function dtks()
    {
        return $this->belongsTo(DTKS::class, 'dtks_id');
    }
}
