<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Urutan penting:
     * 1. AnggotaResmiSeeder harus dijalankan sebelum AdminSeeder
     *    karena AdminSeeder akan membuat user admin tanpa relasi anggota_resmi.
     * 2. Jalankan: php artisan db:seed
     */
    public function run(): void
    {
        $this->call([
            AnggotaResmiSeeder::class, // Data resmi civitas UMI dari CSV
            AdminSeeder::class,        // Akun-akun administrator sistem
        ]);
    }
}
