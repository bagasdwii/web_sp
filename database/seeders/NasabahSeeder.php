<?php

namespace Database\Seeders;

use App\Models\Nasabah;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class NasabahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Ambil jumlah PegawaiAccountOffice yang ada di database
        $jumlahPegawaiAccountOffice = \App\Models\User::where('jabatan_id', 5)->count();

        // Tentukan jumlah nasabah yang akan di-generate sesuai dengan jumlah PegawaiAccountOffice
        $jumlahNasabah = $jumlahPegawaiAccountOffice;

        for ($i = 0; $i < $jumlahNasabah; $i++) {
            $nasabah = new Nasabah();
            $nasabah->no = $faker->bankAccountNumber;
            $nasabah->nama = $faker->name;
            $nasabah->pokok = $faker->randomNumber(7); // Contoh: 1000000 (IDR 1,000,000)
            $nasabah->bunga = $faker->randomNumber(5); // Contoh: 50000 (IDR 50,000)
            $nasabah->denda = $faker->randomNumber(4); // Contoh: 10000 (IDR 10,000)
            $nasabah->total = $faker->randomNumber(7); // Contoh: 2000000 (IDR 2,000,000)
            $nasabah->keterangan = $faker->sentence();
            $nasabah->ttd = $faker->dateTimeThisYear();
            $nasabah->kembali = $faker->dateTimeThisYear();

            // Ambil id_cabang secara acak
            $nasabah->id_cabang = \App\Models\Cabang::inRandomOrder()->first()->id_cabang;

            // Ambil id_wilayah secara acak
            $nasabah->id_wilayah = \App\Models\Wilayah::inRandomOrder()->first()->id_wilayah;

            // Ambil id_account_officer dari User yang memiliki jabatan_id = 5 secara acak
            $nasabah->id_account_officer = \App\Models\User::where('jabatan_id', 5)->inRandomOrder()->first()->id;

            // Ambil id_admin_kas dari User yang memiliki jabatan_id = 4 secara acak
            $nasabah->id_admin_kas = \App\Models\User::where('jabatan_id', 4)->inRandomOrder()->first()->id;

            $nasabah->save();
        }
    }
}
