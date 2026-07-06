<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use App\Models\Barber;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // Fungsi internal untuk mengisi data otomatis jika tabel kosong
    private function autoSeedData()
    {
        if (Service::count() == 0) {
            Service::create(['name' => 'Classic Haircut + Wash', 'price' => 45000, 'duration_minutes' => 30]);
            Service::create(['name' => 'Premium Cukur + Masker Wajah', 'price' => 75000, 'duration_minutes' => 50]);
            Service::create(['name' => 'Kids Haircut (Potong Anak)', 'price' => 35000, 'duration_minutes' => 25]);
        }

        if (Barber::count() == 0) {
            Barber::create(['name' => 'Alex (Top Fade Specialist)', 'is_active' => true]);
            Barber::create(['name' => 'Dani (Classic Cut Specialist)', 'is_active' => true]);
        }
    }

    public function index()
    {
        $bookings = Booking::with(['service', 'barber'])->where('user_id', auth()->id())->latest()->get();
        return view('dashboard', compact('bookings'));
    }

    public function create()
    {
        // Jalankan pengisian data otomatis sebelum menampilkan form
        $this->autoSeedData();

        $services = Service::all();
        $barbers = Barber::where('is_active', true)->get();
        return view('booking-create', compact('services', 'barbers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'barber_id' => 'required|exists:barbers,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
        ]);

        $service = Service::find($request->service_id);

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'service_id' => $request->service_id,
            'barber_id' => $request->barber_id,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'total_price' => $service->price,
            'payment_status' => 'pending',
        ]);

        // Token simulasi integrasi Midtrans Snap
        $booking->update([
            'snap_token' => 'SNAP-TOKEN-' . strtoupper(uniqid())
        ]);

        return redirect()->route('booking.show', $booking->id);
    }

    public function show(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) { abort(403); }
        return view('booking-show', compact('booking'));
    }
}