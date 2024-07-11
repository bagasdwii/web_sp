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
        $jumlahPegawaiAccountOffice = \App\Models\PegawaiAccountOffice::count();

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

            // Ambil id_account_officer dari PegawaiAccountOffice secara acak
            $nasabah->id_account_officer = \App\Models\PegawaiAccountOffice::inRandomOrder()->first()->id_account_officer;

            // Ambil id_admin_kas dari PegawaiAdminKas secara acak
            $nasabah->id_admin_kas = \App\Models\PegawaiAdminKas::inRandomOrder()->first()->id_admin_kas;

            $nasabah->save();
        }
    }
}
