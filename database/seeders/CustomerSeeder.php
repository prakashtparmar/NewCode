<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Company;
use App\Models\User;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();

        // Get all executives by role (adjust if needed)
        $executives = User::where('role', 'executive')->get();

        if ($companies->count() === 0 || $executives->count() === 0) {
            $this->command->warn('No companies or executives found. Seed them first.');
            return;
        }

        foreach ($companies as $company) {
            for ($i = 1; $i <= 5; $i++) {
                DB::table('customers')->insert([
                    'name'         => 'Customer ' . $i . ' - ' . $company->name,
                    'email'        => 'customer' . $i . '_' . $company->id . '@example.com',
                    'phone'        => '98765432' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'address'      => 'Address ' . $i . ' for ' . $company->name,
                    'company_id'   => $company->id,
                    'executive_id' => $executives->random()->id,
                    'is_active'    => rand(0, 1),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }
    }
}
