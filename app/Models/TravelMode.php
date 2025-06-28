<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TravelMode
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class TravelMode extends Model
{
    protected $fillable = ['name'];
}
