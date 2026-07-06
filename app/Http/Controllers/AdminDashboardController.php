<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\Service;
use App\Models\Barber;
use Illuminate\Support\Facades\Hash;

class AdminDashboardController extends Controller
{
    private function autoSeedData()
    {
        // 1. Otomatis Buat Akun Admin jika belum ada
        $adminExists = User::where('email', 'admin@pangkas.com')->exists();
        if (!$adminExists) {
            User::create([
                'name' => 'Admin Utama Barbershop',
                'email' => 'admin@pangkas.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
        }

        // 2. Otomatis Buat Data Layanan jika kosong
        if (Service::count() == 0) {
            Service::create(['name' => 'Classic Haircut + Wash', 'price' => 45000, 'duration_minutes' => 30]);
            Service::create(['name' => 'Premium Cukur + Masker Wajah', 'price' => 75000, 'duration_minutes' => 50]);
            Service::create(['name' => 'Kids Haircut (Potong Anak)', 'price' => 35000, 'duration_minutes' => 25]);
        }

        // 3. Otomatis Buat Data Barber jika kosong
        if (Barber::count() == 0) {
            Barber::create(['name' => 'Alex (Top Fade Specialist)', 'is_active' => true]);
            Barber::create(['name' => 'Dani (Classic Cut Specialist)', 'is_active' => true]);
        }
    }

    public function index()
    {
        // Jalankan pengecekan dan pengisian data otomatis
        $this->autoSeedData();

        $total_pendapatan = Booking::where('payment_status', 'success')->sum('total_price');
        $total_pelanggan = User::where('role', 'user')->count();
        $all_bookings = Booking::with(['user', 'service', 'barber'])->latest()->get();

        return view('admin-dashboard', compact('total_pendapatan', 'total_pelanggan', 'all_bookings'));
    }
}