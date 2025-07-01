<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LookupTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyId = 1; // 👈 default master admin company id — replace if needed

        DB::table('travel_modes')->insert([
            ['name' => 'Car',  'company_id' => $companyId],
            ['name' => 'Bike', 'company_id' => $companyId],
            ['name' => 'Walk', 'company_id' => $companyId],
        ]);

        DB::table('purposes')->insert([
            ['name' => 'Official',     'company_id' => $companyId],
            ['name' => 'Client Visit', 'company_id' => $companyId],
            ['name' => 'Inspection',   'company_id' => $companyId],
        ]);

        DB::table('tour_types')->insert([
            ['name' => 'Local',        'company_id' => $companyId],
            ['name' => 'Outstation',   'company_id' => $companyId],
            ['name' => 'International','company_id' => $companyId],
        ]);
    }
}
