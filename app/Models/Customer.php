<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

    protected $fillable = ['name', 'email', 'phone', 'address', 'executive_id'];

    public function executive()
    {
        return $this->belongsTo(Admin::class, 'executive_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
