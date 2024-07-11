<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CabangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        //
        DB::table('cabangs')->insert([
            ['nama_cabang' => 'Nganjuk'],
            ['nama_cabang' => 'Madiun'],
            ['nama_cabang' => 'Ponorogo'],
        ]);
    }
}
