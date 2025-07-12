<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pincode extends Model
{
    protected $fillable = ['city_id', 'pincode'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
