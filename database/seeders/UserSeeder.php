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
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'mobile' => '1234567890',
                'user_type' => 'Admin',
                'user_code' => 'ADM001',
                'headquarter' => 'Head Office',
                'date_of_birth' => '1990-01-01',
                'joining_date' => '2020-01-01',
                'emergency_contact_no' => '9111111111',
                'gender' => 'Male',
                'marital_status' => 'Single',
                'designation' => 'System Admin',
                'reporting_to' => null,
                'is_self_sale' => true,
                'is_multi_day_start_end_allowed' => true,
                'is_allow_tracking' => true,
                'address' => '123 Admin Street',
                'state' => 'Admin State',
                'district' => 'Admin District',
                'tehsil' => 'Admin Tehsil',
                'city' => 'Admin City',
                'latitude' => '28.6139',
                'longitude' => '77.2090',
                'pincode' => '110001',
                'depo' => 'Central Depot',
                'postal_address' => 'P.O. Box 123',
                'status' => 'Active',
            ]
        );

        // Assign all permissions and admin role
        $permissions = Permission::where('guard_name', 'web')->pluck('name')->all();

        if (!empty($permissions)) {
            $adminUser->givePermissionTo($permissions);

            $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
            $adminRole->givePermissionTo($permissions);

            $adminUser->assignRole('admin');
        }

        // Create a sample Sales Manager
        User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Sales Manager',
                'role' => 'manager',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'mobile' => '9999999999',
                'user_type' => 'Manager',
                'user_code' => 'MGR001',
                'headquarter' => 'North HQ',
                'date_of_birth' => '1985-05-15',
                'joining_date' => '2021-06-01',
                'emergency_contact_no' => '9111222233',
                'gender' => 'Female',
                'marital_status' => 'Married',
                'designation' => 'Sales Manager',
                'reporting_to' => 'Admin User',
                'is_self_sale' => false,
                'is_multi_day_start_end_allowed' => false,
                'is_allow_tracking' => true,
                'address' => '456 Manager Lane',
                'state' => 'Punjab',
                'district' => 'Ludhiana',
                'tehsil' => 'Samrala',
                'city' => 'Ludhiana',
                'latitude' => '30.9000',
                'longitude' => '75.8500',
                'pincode' => '141001',
                'depo' => 'Depot A',
                'postal_address' => 'P.O. Box 456',
                'status' => 'Active',
            ]
        );

        // Create a sample Field Executive
        User::firstOrCreate(
            ['email' => 'executive@example.com'],
            [
                'name' => 'Field Executive',
                'role' => 'executive',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'mobile' => '8888888888',
                'user_type' => 'Executive',
                'user_code' => 'EXEC001',
                'headquarter' => 'South HQ',
                'date_of_birth' => '1995-09-20',
                'joining_date' => '2023-01-10',
                'emergency_contact_no' => '9111333344',
                'gender' => 'Other',
                'marital_status' => 'Single',
                'designation' => 'Field Executive',
                'reporting_to' => 'Sales Manager',
                'is_self_sale' => true,
                'is_multi_day_start_end_allowed' => true,
                'is_allow_tracking' => true,
                'address' => '789 Executive Road',
                'state' => 'Karnataka',
                'district' => 'Bangalore',
                'tehsil' => 'Bangalore South',
                'city' => 'Bangalore',
                'latitude' => '12.9716',
                'longitude' => '77.5946',
                'pincode' => '560001',
                'depo' => 'Depot B',
                'postal_address' => 'P.O. Box 789',
                'status' => 'Active',
            ]
        );

        echo "Users seeded: Admin, Sales Manager, Field Executive\n";
    }
}
