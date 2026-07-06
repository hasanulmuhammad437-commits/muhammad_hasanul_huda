<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminDashboardController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

// Halaman Utama: Otomatis Lempar ke Login
Route::get('/', function () {
    return redirect()->route('login');
});

// ====================================================
//            ALUR AUTENTIKASI MANUAL (LENGKAP)
// ====================================================

// Tampilkan Halaman Login
Route::get('/login', function () { 
    return view('auth.login'); 
})->name('login');

// Proses Data Login (POST)
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // FITUR CERDAS: Jika login menggunakan email khusus admin dan belum terdaftar, buatkan detik itu juga
    if ($credentials['email'] === 'admin@pangkas.com') {
        $adminExists = User::where('email', 'admin@pangkas.com')->exists();
        if (!$adminExists && $credentials['password'] === 'password') {
            User::create([
                'name' => 'Admin Utama Barbershop',
                'email' => 'admin@pangkas.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
        }
    }

    if (auth()->attempt($credentials)) {
        $request->session()->regenerate();

        // Amankan & Redirect sesuai role masing-masing
        if (auth()->user()->role === 'admin' || auth()->user()->email === 'admin@pangkas.com') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('dashboard');
    }

    return back()->withErrors([
        'email' => 'Email atau password yang Anda masukkan salah.',
    ]);
});

// Tampilkan Halaman Register
Route::get('/register', function () { 
    return view('auth.register'); 
})->name('register');

// Proses Registrasi User Baru (POST)
Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'user',
    ]);

    auth()->login($user);

    return redirect()->route('dashboard');
});

// Proses Logout (POST)
Route::post('/logout', function(Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');


// ====================================================
//            ALUR UTAMA DASHBOARD & BOOKING
// ====================================================

// Halaman Khusus Pelanggan (User)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [BookingController::class, 'index'])->name('dashboard');
    Route::get('/booking/create', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/{booking}', [BookingController::class, 'show'])->name('booking.show');
});

// Halaman Khusus Admin
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
});