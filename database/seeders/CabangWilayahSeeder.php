<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\CabangWilayah;

class CabangWilayahSeeder extends Seeder
{
    public function run()
    {
        CabangWilayah::create(['id_wilayah' => 1, 'id_cabang' => 1]);
        CabangWilayah::create(['id_wilayah' => 2, 'id_cabang' => 2]);
        CabangWilayah::create(['id_wilayah' => 3, 'id_cabang' => 3]);
        CabangWilayah::create(['id_wilayah' => 2, 'id_cabang' => 3]);
  
    }
}   