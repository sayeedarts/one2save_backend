<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('doctors')->insert([
            'name' => Str::random(10),
            'email' => Str::random(10).'@gmail.com',
            'details' => Str::random(200),
            'user_id' => 1,
            'hospital_id' => rand(2,8),
            'department_id' => rand(10,12),
            'phone' => rand(1000000001, 9999999999)
        ]);
    }
}
