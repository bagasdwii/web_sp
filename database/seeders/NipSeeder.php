<?php

namespace Database\Seeders;

use App\Models\Nip;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class NipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Generate 10 NIP
        for ($i = 0; $i < 10; $i++) {
            $nip = $faker->unique()->numerify('######'); // Gunakan format NIP sesuai kebutuhan
            
            // Simpan NIP ke database, contoh jika Anda memiliki model Nip
            Nip::create([
               'nip' => $nip,
            ]);
        }
    }
}
