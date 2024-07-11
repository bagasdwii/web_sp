<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\CabangWilayahSeeder;
// use Database\Seeders\JabatanSeeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            WilayahSeeder::class,
            CabangSeeder::class,
            JabatanSeeder::class,
            UserSeeder::class,
            NasabahSeeder::class,
            KepalaCabangSeeder::class,
            SupervisorSeeder::class,
            AdminKasSeeder::class,
            AccountOfficerSeeder::class,
            NasabahSeeder::class,
            NipSeeder::class
        ]);
    
    }
}
