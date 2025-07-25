<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\PermissionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //User::factory(20)->create();


        // Call other seeders
        $this->call([
            // PermissionSeeder::class,
            RoleSeeder::class,
            // AdminsTableSeeder::class,
            UserSeeder::class,

            StateSeeder::class,
            DistrictSeeder::class,
            CitySeeder::class,
            TehsilSeeder::class,
            PincodeSeeder::class,
            MultiCompanySeeder::class,
            LookupTablesSeeder::class,
            CustomerSeeder::class,
            // TripSeeder::class,
            
            // TripWithLogsSeeder::class,
            DesignationSeeder::class,
        ]);


        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
