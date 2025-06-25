<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripLog extends Model
{
    protected $fillable = [
        'trip_id',
        'latitude',
        'longitude',
        'recorded_at',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
