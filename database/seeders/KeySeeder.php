<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            DB::table('keys')->insert([
                'key' => random_int(100000, 999999), // Menghasilkan angka acak 6 digit
                'jabatan' => ($i % 5) + 1, // Menetapkan jabatan antara 1 dan 5
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
