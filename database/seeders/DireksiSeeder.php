<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DireksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    $faker = Faker::create('id_ID');
        Direks::create([
            'nama' => $faker->name,
            'alamat' => $faker->address,
            'id_account_officer' => $accountOfficer->id_account_officer,
            'id_cabang' => $accountOfficer->id_cabang,
            'id_wilayah' => $accountOfficer->id_wilayah,
            'id_admin_kas' => $accountOfficer->id_admin_kas,
            'foto_bukti' => null,
            'bukti_sp_pdf' => null,
        ]);
    } 
}
