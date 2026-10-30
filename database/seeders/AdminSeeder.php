<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'nama' => 'Perdi Aritonang, S.Kom.',
                'username' => 'admin',
                'email' => 'perdi@gmail.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status_aktif' => 1,
                'status_akun' => 'aktif',
                'no_hp' => '081234567801',
                'alamat' => 'Biro Fikom Umi',
            ],
            [
                'nama' => 'Alan Panggabean, S.Kom.',
                'username' => 'admin2',
                'email' => 'alan@gmail.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status_aktif' => 1,
                'status_akun' => 'aktif',
                'no_hp' => '081234567802',
                'alamat' => 'Biro Fikom Umi',
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']],
                $admin
            );
        }
    }
}
