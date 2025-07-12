<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateSeeder extends Seeder
{
    public function run()
    {
        DB::table('states')->insert([
            ['name' => 'Maharashtra'],
            ['name' => 'Karnataka'],
            ['name' => 'Gujarat'],
            ['name' => 'Tamil Nadu'],
        ]);

       
    }
}
