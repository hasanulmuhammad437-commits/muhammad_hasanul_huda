<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Validasi: Jika user sudah login DAN memiliki role admin, izinkan masuk
        if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->email === 'admin@pangkas.com')) {
            return $next($request);
        }

        // Jika bukan admin, lempar kembali ke dashboard pelanggan
        return redirect()->route('dashboard');
    }
}