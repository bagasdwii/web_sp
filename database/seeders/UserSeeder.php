<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Event;
use App\Events\UserRegisteredMobile;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Tentukan batasan untuk setiap jabatan_id
        $limits = [
            1 => 1,
            2 => 5,
            3 => 10,
            4 => 20,
            5 => 50,
        ];

        // Hitung jumlah pengguna untuk setiap jabatan_id yang sudah ada di database
        $counts = User::select('jabatan_id', \DB::raw('count(*) as total'))
                        ->groupBy('jabatan_id')
                        ->pluck('total', 'jabatan_id')
                        ->toArray();

        // Pastikan counts memiliki nilai default 0 untuk setiap jabatan_id
        foreach ($limits as $id => $limit) {
            if (!isset($counts[$id])) {
                $counts[$id] = 0;
            }
        }

        // Tentukan jumlah pengguna baru yang akan di-generate
        $numberOfUsersToGenerate = 86; // Sesuaikan sesuai kebutuhan

        for ($i = 0; $i < $numberOfUsersToGenerate; $i++) {
            // Pilih jabatan_id yang masih di bawah batasan
            $validJabatanIds = array_filter(array_keys($limits), function($id) use ($counts, $limits) {
                return $counts[$id] < $limits[$id];
            });

            if (empty($validJabatanIds)) {
                break; // Tidak ada jabatan_id yang valid lagi
            }

            // Pilih jabatan_id secara acak dari daftar yang valid
            $jabatanId = $faker->randomElement($validJabatanIds);

            // Buat pengguna baru
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->email,
                'password' => bcrypt('password'),
                'jabatan_id' => $jabatanId
            ]);

            // Tambahkan count untuk jabatan_id yang dipilih
            $counts[$jabatanId]++;

            // Dispatch event secara manual
            // Event::dispatch(new UserRegisteredMobile($user));
        }
    }
}
