<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        //
        DB::table('wilayahs')->insert([
            ['nama_wilayah' => 'Dolopo'],
            ['nama_wilayah' => 'Jiwan'],
            ['nama_wilayah' => 'Sumotoro'],
            ['nama_wilayah' => 'Rejoso'],
        ]);
    }
}
