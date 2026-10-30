<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * Controller: AuthController
 *
 * Menangani login 2 tahap:
 *   Tahap 1 → Verifikasi username (NIM/NIDN) + password
 *             Jika berhasil, JANGAN login langsung. Simpan user_id di session
 *             dengan key 'menunggu_wajah' lalu arahkan ke verifikasi wajah.
 *   Tahap 2 → Ditangani oleh VerifikasiWajahController
 *
 * Keamanan:
 * - Throttle 5 percobaan/menit per IP+username
 * - Pesan error selalu umum ("Username atau password salah")
 * - Admin tidak diwajibkan verifikasi wajah (status_akun tidak diperiksa)
 */
class AuthController extends Controller
{
    // =========================================================
    // Login — Tahap 1: Username + Password
    // =========================================================

    /** Tampilkan halaman login */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectSesuaiRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Proses login tahap 1.
     * Throttle: maks 5 percobaan per menit per (IP + username).
     */
    public function login(Request $request)
    {
        $request->validate(
            [
                'username' => ['required', 'string'],
                'password' => ['required', 'string'],
            ],
            [
                'username.required' => 'Username wajib diisi.',
                'password.required' => 'Kata sandi wajib diisi.',
            ]
        );

        // Kunci throttle unik per IP dan username
        $throttleKey = Str::lower($request->input('username')) . '|' . $request->ip();

        // Cek apakah sudah terlalu banyak percobaan
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'username' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
            ])->withInput($request->only('username'));
        }

        // Cari user berdasarkan username (NIM/NIDN) atau email
        $user = User::where('username', $request->username)
                    ->orWhere('email', $request->username)
                    ->first();

        // Verifikasi password — pesan error selalu generik
        if (! $user || ! password_verify($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 60); // Tambah counter throttle
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput($request->only('username'));
        }

        // Cek apakah akun aktif (kolom status_aktif)
        if (! $user->isActive()) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput($request->only('username'));
        }

        // Admin: login langsung tanpa verifikasi wajah
        if ($user->isAdmin()) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            RateLimiter::clear($throttleKey);

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang, ' . $user->nama . '!');
        }

        // Anggota: cek status_akun terlebih dahulu
        if ($user->status_akun === 'menunggu_verifikasi') {
            return back()->withErrors([
                'username' => 'Akun Anda masih menunggu persetujuan administrator.',
            ])->withInput($request->only('username'));
        }

        if ($user->status_akun === 'ditolak') {
            return back()->withErrors([
                'username' => 'Akun Anda ditolak oleh administrator. Hubungi pusat bantuan.',
            ])->withInput($request->only('username'));
        }

        // Bersihkan throttle setelah berhasil
        RateLimiter::clear($throttleKey);

        // JANGAN login penuh dulu — simpan user_id ke session, arahkan ke verifikasi wajah
        $request->session()->put('menunggu_wajah', $user->id);
        $request->session()->put('menunggu_wajah_remember', $request->boolean('remember'));

        return redirect()->route('verifikasi-wajah.show');
    }

    // =========================================================
    // Logout
    // =========================================================

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    // =========================================================
    // Helper
    // =========================================================

    private function redirectSesuaiRole(User $user)
    {
        return $user->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('anggota.dashboard');
    }
}
