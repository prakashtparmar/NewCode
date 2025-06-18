<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    protected $fillable = [
        'user_id',
        'trip_date',
        'start_time',
        'end_time',
        'start_lat',
        'start_lng',
        'end_lat',
        'end_lng',
        'total_distance_km',
        'travel_mode',
        'purpose',
        'status',
        'approval_status',
        'approval_reason',
        'approved_by',
        'approved_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(TripLocation::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(TripMedia::class);
    }

    public function approvedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    /**
     * Get the admin who created the trip request.
     */
    public function createdByAdmin()
    {
        return $this->belongsTo(Admin::class, 'user_id'); // Assuming 'user_id' is the foreign key in 'trips' table
    }
}
