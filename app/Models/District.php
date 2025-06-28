<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function tehsils()
    {
        return $this->hasMany(Tehsil::class);
    }
}
