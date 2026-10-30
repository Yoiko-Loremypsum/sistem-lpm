<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: AkunAktifMiddleware
 *
 * Memastikan user yang sudah login penuh memiliki status_akun = 'aktif'.
 * Digunakan pada rute anggota untuk mencegah akses meski sudah terautentikasi
 * tetapi akun belum disetujui admin (kondisi aneh yang mungkin terjadi jika
 * admin menangguhkan akun setelah user login).
 */
class AkunAktifMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Admin selalu diizinkan — skip pengecekan status_akun
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Cek status_akun untuk anggota
        if (! $user->isAkunAktif()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $pesan = match ($user->status_akun) {
                'menunggu_verifikasi' => 'Akun Anda masih menunggu persetujuan administrator.',
                'ditolak'             => 'Akun Anda ditolak oleh administrator. Hubungi pusat bantuan.',
                default               => 'Akun Anda tidak aktif.',
            };

            return redirect()->route('login')->with('error', $pesan);
        }

        return $next($request);
    }
}
