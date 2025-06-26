<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
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

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function tripLogs(): HasMany
    {
        return $this->hasMany(TripLog::class);
    }
}
