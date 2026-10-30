<?php

use App\Http\Controllers\Admin\AnggotaController as AdminAnggotaController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\KategoriController as AdminKategoriController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\PengaduanController as AdminPengaduanController;
use App\Http\Controllers\Admin\PersetujuanAkunController;
use App\Http\Controllers\Anggota\DashboardController as AnggotaDashboardController;
use App\Http\Controllers\Anggota\PengaduanController as AnggotaPengaduanController;
use App\Http\Controllers\Anggota\ProfilController as AnggotaProfilController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LupaSandiController;
use App\Http\Controllers\RegistrasiController;
use App\Http\Controllers\VerifikasiWajahController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Layanan Pengaduan Masyarakat (LPM) UMI
|--------------------------------------------------------------------------
*/

// Redirect root ke halaman yang sesuai
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('anggota.dashboard');
    }
    return redirect()->route('login');
});

// =========================================================
// AUTENTIKASI — Hanya untuk tamu (belum login)
// =========================================================
Route::middleware('guest')->group(function () {

    // --- Login 2 Tahap ---
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1'); // Throttle 10 req/menit di level route

    // --- Registrasi 3 Langkah ---
    Route::get('/daftar',           [RegistrasiController::class, 'showLangkah1'])->name('registrasi.langkah1');
    Route::post('/daftar/cek',      [RegistrasiController::class, 'prosesLangkah1'])->name('registrasi.langkah1.proses');
    Route::get('/daftar/akun',      [RegistrasiController::class, 'showLangkah2'])->name('registrasi.langkah2');
    Route::post('/daftar/akun',     [RegistrasiController::class, 'prosesLangkah2'])->name('registrasi.langkah2.proses');
    Route::get('/daftar/wajah',     [RegistrasiController::class, 'showLangkah3'])->name('registrasi.langkah3');
    Route::post('/daftar/wajah',    [RegistrasiController::class, 'daftarWajah'])->name('registrasi.wajah.daftar');
    Route::post('/daftar/otp/kirim', [RegistrasiController::class, 'kirimOtpRegistrasi'])->name('registrasi.otp.kirim');
    Route::post('/daftar/otp/verifikasi', [RegistrasiController::class, 'verifikasiOtpRegistrasi'])->name('registrasi.otp.verifikasi');
    Route::get('/daftar/selesai',   [RegistrasiController::class, 'selesai'])->name('registrasi.selesai');

    // --- Lupa Sandi & Reset Password ---
    Route::get('/lupa-sandi',           [LupaSandiController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/lupa-sandi',          [LupaSandiController::class, 'sendResetLinkEmail'])->name('password.email')->middleware('throttle:6,1');
    Route::get('/reset-sandi/{token}',  [LupaSandiController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-sandi',         [LupaSandiController::class, 'reset'])->name('password.update')->middleware('throttle:6,1');
});

// =========================================================
// VERIFIKASI WAJAH — Tahap 2 Login
// Dilindungi WajahMiddleware: hanya bisa diakses setelah
// melewati tahap password (session 'menunggu_wajah' harus ada).
// =========================================================
Route::middleware('auth.wajah')->group(function () {
    Route::get('/verifikasi-wajah',               [VerifikasiWajahController::class, 'show'])->name('verifikasi-wajah.show');
    Route::post('/verifikasi-wajah',              [VerifikasiWajahController::class, 'verifikasi'])
        ->name('verifikasi-wajah.proses')
        ->middleware('throttle:5,1');
    Route::post('/verifikasi-wajah/otp/kirim',    [VerifikasiWajahController::class, 'kirimOtp'])->name('verifikasi-wajah.otp.kirim');
    Route::post('/verifikasi-wajah/otp/verifikasi', [VerifikasiWajahController::class, 'verifikasiOtp'])->name('verifikasi-wajah.otp.verifikasi');
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// =========================================================
// RUTE ANGGOTA
// =========================================================
Route::middleware(['auth', 'auth.anggota', 'akun.aktif'])->prefix('anggota')->name('anggota.')->group(function () {
    Route::get('/dashboard', [AnggotaDashboardController::class, 'index'])->name('dashboard');

    // Pengaduan
    Route::get('/pengaduan',        [AnggotaPengaduanController::class, 'index'])->name('pengaduan.index');
    Route::get('/pengaduan/buat',   [AnggotaPengaduanController::class, 'create'])->name('pengaduan.create');
    Route::post('/pengaduan',       [AnggotaPengaduanController::class, 'store'])->name('pengaduan.store');
    Route::get('/pengaduan/{id}',   [AnggotaPengaduanController::class, 'show'])->name('pengaduan.show');

    // Profil & Password
    Route::get('/profil',           [AnggotaProfilController::class, 'edit'])->name('profil');
    Route::put('/profil',           [AnggotaProfilController::class, 'update'])->name('profil.update');
    Route::put('/profil/password',  [AnggotaProfilController::class, 'updatePassword'])->name('profil.password');

    // Hapus data wajah (hak UU PDP)
    Route::delete('/profil/wajah',  [VerifikasiWajahController::class, 'hapusDataWajah'])->name('profil.wajah.hapus');
});

// =========================================================
// RUTE ADMINISTRATOR
// =========================================================
Route::middleware(['auth', 'auth.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Persetujuan Akun (fitur baru)
    Route::get('/persetujuan-akun',             [PersetujuanAkunController::class, 'index'])->name('persetujuan-akun.index');
    Route::patch('/persetujuan-akun/{id}/setujui', [PersetujuanAkunController::class, 'setujui'])->name('persetujuan-akun.setujui');
    Route::patch('/persetujuan-akun/{id}/tolak',   [PersetujuanAkunController::class, 'tolak'])->name('persetujuan-akun.tolak');

    // Pengaduan
    Route::get('/pengaduan',                    [AdminPengaduanController::class, 'index'])->name('pengaduan.index');
    Route::get('/pengaduan/{id}',               [AdminPengaduanController::class, 'show'])->name('pengaduan.show');
    Route::post('/pengaduan/{id}/status',       [AdminPengaduanController::class, 'updateStatus'])->name('pengaduan.status');
    Route::post('/pengaduan/{id}/tanggapan',    [AdminPengaduanController::class, 'storeTanggapan'])->name('pengaduan.tanggapan');

    // Kategori
    Route::get('/kategori',         [AdminKategoriController::class, 'index'])->name('kategori.index');
    Route::post('/kategori',        [AdminKategoriController::class, 'store'])->name('kategori.store');
    Route::put('/kategori/{id}',    [AdminKategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{id}', [AdminKategoriController::class, 'destroy'])->name('kategori.destroy');

    // Anggota
    Route::get('/anggota',              [AdminAnggotaController::class, 'index'])->name('anggota.index');
    Route::post('/anggota/{id}/toggle-status', [AdminAnggotaController::class, 'toggleStatus'])->name('anggota.toggle');

    // Laporan
    Route::get('/laporan',          [AdminLaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/cetak',    [AdminLaporanController::class, 'exportPrint'])->name('laporan.print');
    Route::get('/laporan/excel',    [AdminLaporanController::class, 'exportExcel'])->name('laporan.excel');
});
