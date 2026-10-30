<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: WajahMiddleware
 *
 * Memastikan halaman verifikasi wajah hanya bisa diakses setelah
 * melewati tahap password terlebih dahulu (session 'menunggu_wajah' harus ada).
 *
 * Tanpa middleware ini, siapa pun bisa langsung mengakses URL verifikasi wajah
 * tanpa memasukkan username/password.
 */
class WajahMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Jika sudah login penuh, tidak perlu ke halaman verifikasi wajah
        if (auth()->check()) {
            $user = auth()->user();
            return $user->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('anggota.dashboard');
        }

        // Session 'menunggu_wajah' harus ada (diisi oleh AuthController setelah password berhasil)
        if (! $request->session()->has('menunggu_wajah')) {
            return redirect()->route('login')
                ->with('error', 'Silakan masukkan username dan password terlebih dahulu.');
        }

        return $next($request);
    }
}
