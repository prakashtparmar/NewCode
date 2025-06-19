<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'code', 'email', 'address'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function roles()
    {
        return $this->hasMany(\Spatie\Permission\Models\Role::class);
    }
}
