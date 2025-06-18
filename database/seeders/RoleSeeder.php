<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = ['admin', 'manager', 'executive'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role], ['guard_name' => 'web']);
        }
    }
}
