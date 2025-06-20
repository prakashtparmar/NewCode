<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\User;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        $executives = User::where('user_level', 'executive')->get(); // ✅ match your actual user level structure

        $hasCompanies = $companies->isNotEmpty();
        $hasExecutives = $executives->isNotEmpty();

        if (!$hasCompanies || !$hasExecutives) {
            $this->command->warn('No companies or executive users found. Proceeding with null assignments for mapping later.');
        }

        for ($i = 1; $i <= 20; $i++) {
            DB::table('customers')->insert([
                'name'       => 'Customer ' . $i,
                'email'      => 'customer' . $i . '@example.com',
                'phone'      => '98765432' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'address'    => 'Sample Address ' . $i,
                'company_id' => $hasCompanies ? $companies->random()->id : null,
                'user_id'    => $hasExecutives ? $executives->random()->id : null, // ✅ updated field
                'is_active'  => rand(0, 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Dummy customers seeded. You can assign company_id and user_id later.');
    }
}
