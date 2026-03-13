<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'id_user' => 'USR01',
                'nama_user' => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('1234'),
                'role' => 'admin',
                'email' => 'admin@example.com',
                'no_telepon' => '081234567890',
                'status' => 'aktif',
            ],
            [
                'id_user' => 'USR02',
                'nama_user' => 'Karyawan Produksi 1',
                'username' => 'produksi',
                'password' => Hash::make('12345'),
                'role' => 'karyawanproduksi',
                'email' => 'produksi1@example.com',
                'no_telepon' => '081234567891',
                'status' => 'aktif',
            ],
            [
                'id_user' => 'USR03',
                'nama_user' => 'Owner Perusahaan',
                'username' => 'owner',
                'password' => Hash::make('123456'),
                'role' => 'owner',
                'email' => 'owner@example.com',
                'no_telepon' => '087895745633',
                'status' => 'aktif',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
// namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
// use Illuminate\Database\Seeder;

// class UserSeeder extends Seeder
// {
//     /**
//      * Run the database seeds.
//      */
//     public function run(): void
//     {
//         //
//     }
// }
