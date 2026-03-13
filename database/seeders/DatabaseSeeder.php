<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BahanBakuSeeder::class,
            SupplierSeeder::class,
            PembelianSeeder::class,
            DetailPembelianSeeder::class,
            ResepSeeder::class,
            KomposisiResepSeeder::class,
            ProdukSeeder::class,
            ProduksiSeeder::class,
            DetailProduksiSeeder::class,
            LokasiSeeder::class,
            DistribusiSeeder::class,
            DistribusiDetailSeeder::class,
            ItemDistribusiSeeder::class,
        ]);
    }
    // public function run(): void
    // {
    //     // User::factory(10)->create();

    //     User::factory()->create([
    //         'name' => 'Test User',
    //         'email' => 'test@example.com',
    //     ]);
    // }
}
