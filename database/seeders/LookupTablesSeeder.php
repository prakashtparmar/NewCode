<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // <-- This was missing

class LookupTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('travel_modes')->insert([
            ['name' => 'Car'],
            ['name' => 'Bike'],
            ['name' => 'Walk'],
        ]);

        DB::table('purposes')->insert([
            ['name' => 'Official'],
            ['name' => 'Client Visit'],
            ['name' => 'Inspection'],
        ]);

        DB::table('tour_types')->insert([
            ['name' => 'Local'],
            ['name' => 'Outstation'],
            ['name' => 'International'],
        ]);
    }
}
