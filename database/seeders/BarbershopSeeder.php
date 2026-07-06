<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Barber;
use App\Models\User;

class BarbershopSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Akun Admin Untuk Test Login
        User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@pangkas.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        // Buat Akun User Untuk Test Login
        User::create([
            'name' => 'Budi Pelanggan',
            'email' => 'budi@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);

        // Isi Layanan
        Service::create(['name' => 'Classic Haircut + Wash', 'price' => 45000, 'duration_minutes' => 30]);
        Service::create(['name' => 'Premium Cukur + Masker Wajah', 'price' => 75000, 'duration_minutes' => 50]);
        Service::create(['name' => 'Kids Haircut (Potong Anak)', 'price' => 35000, 'duration_minutes' => 25]);

        // Isi Barber / Kapster
        Barber::create(['name' => 'Alex (Top Fade Specialist)']);
        Barber::create(['name' => 'Dani (Classic Cut Specialist)']);
    }
}