<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $guard = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'role',
        'email',
        'password',
        'mobile',
        'image',

        // Added fields from the extended schema
        'user_type',
        'user_code',
        'headquarter',
        'date_of_birth',
        'joining_date',
        'emergency_contact_no',
        'gender',
        'marital_status',
        'designation',
        'role_rights',
        'reporting_to',
        'is_self_sale',
        'is_multi_day_start_end_allowed',
        'is_allow_tracking',
        'address',
        'state',
        'district',
        'tehsil',
        'city',
        'latitude',
        'longitude',
        'pincode',
        'depo',
        'postal_address',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
    'last_seen' => 'datetime',
];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'joining_date' => 'date',
            'is_self_sale' => 'boolean',
            'is_multi_day_start_end_allowed' => 'boolean',
            'is_allow_tracking' => 'boolean',
        ];
    }
}
