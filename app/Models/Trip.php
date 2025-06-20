<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $fillable = [
        'user_id', 'company_id', 'trip_date', 'start_time', 'end_time',
        'start_lat', 'start_lng', 'end_lat', 'end_lng',
        'total_distance_km', 'travel_mode', 'purpose',
        'status', 'approval_reason', 'approved_by', 'approved_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
