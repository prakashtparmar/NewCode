<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Create a default admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'role' => 'admin',
                'password' => Hash::make('password'), // Choose a strong default password
                'email_verified_at' => now(),
                'mobile' => '1234567890',
            ]
        );

        // Get all permissions for the 'web' guard
        $permissions = Permission::where('guard_name', 'web')->pluck('name')->all();

        // If there are permissions, assign them to the admin user
        if (!empty($permissions)) {
            // Assign all permissions to the admin user
            $adminUser->givePermissionTo($permissions);

            // Optional: Create an 'admin' role and assign it to the admin user
            // This is good practice for managing permissions through roles
            $adminRole = Role::firstOrCreate(
                ['name' => 'admin', 'guard_name' => 'web']
            );
            $adminRole->givePermissionTo($permissions);
            $adminUser->assignRole('admin');
        }

        echo "Admin user created and all permissions assigned.\n";
    }
}
