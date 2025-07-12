<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    public function run()
    {
        
        DB::table('districts')->insert([
            ['name' => 'Pune', 'state_id' => 1],
            ['name' => 'Mumbai', 'state_id' => 1],
            ['name' => 'Bangalore', 'state_id' => 2],
            ['name' => 'Ahmedabad', 'state_id' => 3],
            ['name' => 'Chennai', 'state_id' => 4],
            ['name' => 'Nagpur', 'state_id' => 1],
        ]);

    }
}
