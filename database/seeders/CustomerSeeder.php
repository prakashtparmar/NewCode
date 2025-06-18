<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            DB::table('customers')->insert([
                'name' => 'Customer ' . $i,
                'email' => 'customer' . $i . '@example.com',
                'phone' => '98765432' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'address' => 'Address ' . $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
