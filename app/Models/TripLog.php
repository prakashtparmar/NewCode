<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripLog extends Model
{
    protected $fillable = [
        'trip_id',
        'latitude',
        'longitude',
        'recorded_at',
        'battery_percentage',
        'gps_status'
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
