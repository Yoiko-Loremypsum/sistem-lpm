<?php

namespace App\Http\Controllers;

use App\Models\AnggotaResmi;
use App\Models\User;
use App\Services\FaceRecognitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

/**
 * Controller: RegistrasiController
 *
 * Menangani alur registrasi 3 langkah:
 *   Langkah 1 → Cek NIM/NIDN (apakah ada di anggota_resmi)
 *   Langkah 2 → Buat akun (username + password + persetujuan biometrik)
 *   Langkah 3 → Daftar wajah via kamera atau OTP cadangan
 */
class RegistrasiController extends Controller
{
    // =========================================================
    // LANGKAH 1: Cek NIM/NIDN
    // =========================================================

    /** Tampilkan form cek NIM/NIDN */
    public function showLangkah1()
    {
        if (Auth::check()) {
            return redirect()->route('anggota.dashboard');
        }

        return view('auth.registrasi.langkah1');
    }

    /** Proses cek NIM/NIDN — validasi dan simpan ke session */
    public function prosesLangkah1(Request $request)
    {
        $request->validate(
            ['nim_nidn' => ['required', 'string', 'max:50']],
            ['nim_nidn.required' => 'NIM atau NIDN wajib diisi.']
        );

        $nimNidn = trim($request->nim_nidn);

        // Cari di tabel anggota resmi
        $anggota = AnggotaResmi::where('nim_nidn', $nimNidn)->first();

        if (! $anggota) {
            return back()->withErrors([
                'nim_nidn' => 'Data Anda tidak terdaftar sebagai mahasiswa atau dosen.',
            ])->withInput();
        }

        // Cek apakah sudah punya akun
        if ($anggota->sudahPunyaAkun()) {
            return back()->withErrors([
                'nim_nidn' => 'NIM/NIDN ini sudah memiliki akun. Silakan masuk atau hubungi administrator.',
            ])->withInput();
        }

        // Simpan ID anggota resmi di session, lanjut ke langkah 2
        $request->session()->put('registrasi.anggota_resmi_id', $anggota->id);
        $request->session()->put('registrasi.langkah', 2);

        return redirect()->route('registrasi.langkah2');
    }

    // =========================================================
    // LANGKAH 2: Lengkapi Akun
    // =========================================================

    /** Tampilkan form lengkapi akun */
    public function showLangkah2(Request $request)
    {
        // Pastikan sudah melewati langkah 1
        if (! $request->session()->has('registrasi.anggota_resmi_id')) {
            return redirect()->route('registrasi.langkah1')
                ->with('error', 'Silakan mulai dari langkah pertama.');
        }

        $anggota = AnggotaResmi::findOrFail(
            $request->session()->get('registrasi.anggota_resmi_id')
        );

        return view('auth.registrasi.langkah2', compact('anggota'));
    }

    /** Proses pembuatan akun */
    public function prosesLangkah2(Request $request)
    {
        // Pastikan sudah melewati langkah 1
        if (! $request->session()->has('registrasi.anggota_resmi_id')) {
            return redirect()->route('registrasi.langkah1');
        }

        $anggota = AnggotaResmi::findOrFail(
            $request->session()->get('registrasi.anggota_resmi_id')
        );

        // Validasi input langkah 2
        $request->validate(
            [
                'password' => [
                    'required',
                    'confirmed',
                    Password::min(8)->letters()->numbers()->uncompromised(),
                ],
                'setuju_biometrik' => ['accepted'],
            ],
            [
                'password.required'           => 'Kata sandi wajib diisi.',
                'password.confirmed'          => 'Konfirmasi kata sandi tidak cocok.',
                'password.min'                => 'Kata sandi minimal 8 karakter.',
                'password.letters'            => 'Kata sandi harus mengandung huruf.',
                'password.numbers'            => 'Kata sandi harus mengandung angka.',
                'password.uncompromised'      => 'Kata sandi terlalu umum atau pernah bocor. Gunakan kata sandi lain.',
                'setuju_biometrik.accepted'   => 'Anda harus menyetujui pemrosesan data biometrik untuk melanjutkan.',
            ]
        );

        // Buat akun baru dengan status menunggu_verifikasi
        $user = User::create([
            'nama'              => $anggota->nama,
            'username'          => $anggota->nim_nidn, // otomatis dari nim_nidn
            'email'             => $anggota->email_institusi,
            'password'          => Hash::make($request->password),
            'role'              => 'anggota',
            'status_aktif'      => true,
            'status_akun'       => 'menunggu_verifikasi',
            'anggota_resmi_id'  => $anggota->id,
            'face_terdaftar'    => false,
            'gagal_login_wajah' => 0,
        ]);

        // Simpan user id ke session untuk langkah 3
        $request->session()->put('registrasi.user_id', $user->id);
        $request->session()->put('registrasi.langkah', 3);

        return redirect()->route('registrasi.langkah3');
    }

    // =========================================================
    // LANGKAH 3: Daftar Wajah
    // =========================================================

    /** Tampilkan halaman kamera untuk daftar wajah */
    public function showLangkah3(Request $request)
    {
        // Pastikan sudah melewati langkah 2
        if (! $request->session()->has('registrasi.user_id')) {
            return redirect()->route('registrasi.langkah1')
                ->with('error', 'Sesi tidak valid. Mulai dari awal.');
        }

        $user = User::findOrFail($request->session()->get('registrasi.user_id'));

        return view('auth.registrasi.langkah3', compact('user'));
    }

    /**
     * Proses pendaftaran wajah via kamera (face-api.js di browser).
     * Menerima descriptor 128-float dari browser, validasi, dan
     * simpan descriptor terenkripsi ke database (tanpa dependensi Python).
     */
    public function daftarWajah(Request $request, FaceRecognitionService $faceService)
    {
        // Pastikan sudah melewati langkah 2
        if (! $request->session()->has('registrasi.user_id')) {
            return response()->json([
                'berhasil' => false,
                'pesan'    => 'Sesi pendaftaran tidak valid. Silakan ulangi langkah pendaftaran.',
            ], 403);
        }

        $request->validate(
            [
                'face_descriptor' => ['required', 'array', 'size:128'],
            ],
            [
                'face_descriptor.required' => 'Data biometrik wajah wajib disertakan.',
                'face_descriptor.array'    => 'Format biometrik wajah tidak valid.',
                'face_descriptor.size'     => 'Vektor biometrik wajah harus tepat 128 dimensi.',
            ]
        );

        $descriptor = $request->input('face_descriptor');

        if (! $faceService->isValidDescriptor($descriptor)) {
            return response()->json([
                'berhasil' => false,
                'pesan'    => 'Format data wajah tidak valid. Pastikan wajah menghadap kamera dengan jelas.',
            ], 422);
        }

        $user = User::findOrFail($request->session()->get('registrasi.user_id'));

        // Simpan descriptor wajah terenkripsi ke database
        $user->simpanFaceDescriptor($descriptor);

        // Bersihkan session registrasi setelah selesai
        $request->session()->forget(['registrasi.anggota_resmi_id', 'registrasi.user_id', 'registrasi.langkah']);

        return response()->json([
            'berhasil' => true,
            'pesan'    => 'Wajah berhasil didaftarkan! Akun Anda sedang menunggu persetujuan administrator.',
            'redirect' => route('registrasi.selesai'),
        ]);
    }

    /**
     * Opsi cadangan: kirim OTP ke email institusi jika kamera tidak tersedia.
     */
    public function kirimOtpRegistrasi(Request $request)
    {
        if (! $request->session()->has('registrasi.user_id')) {
            return response()->json(['berhasil' => false, 'pesan' => 'Sesi tidak valid.'], 403);
        }

        $user = User::findOrFail($request->session()->get('registrasi.user_id'));

        // Buat OTP 6 digit dan simpan ke session dengan masa berlaku 10 menit
        $otp     = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiry  = now()->addMinutes(10)->timestamp;

        $request->session()->put('registrasi.otp', $otp);
        $request->session()->put('registrasi.otp_expiry', $expiry);

        // Kirim OTP via email (MAIL_MAILER=log di development)
        try {
            Mail::raw(
                "Kode OTP Registrasi Sistem LPM UMI Anda adalah: {$otp}\n\n"
                . "Kode ini berlaku selama 10 menit.\n"
                . "Jangan bagikan kode ini kepada siapa pun.",
                fn($message) => $message
                    ->to($user->email)
                    ->subject('[Sistem LPM UMI] Kode OTP Verifikasi Pendaftaran')
            );
        } catch (\Exception $e) {
            Log::error('Gagal mengirim OTP registrasi', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return response()->json([
                'berhasil' => false,
                'pesan'    => 'Gagal mengirim OTP. Coba lagi.',
            ], 500);
        }

        return response()->json([
            'berhasil' => true,
            'pesan'    => "OTP telah dikirim ke {$user->email}. Berlaku 10 menit.",
        ]);
    }

    /**
     * Verifikasi OTP cadangan dan selesaikan pendaftaran tanpa wajah.
     */
    public function verifikasiOtpRegistrasi(Request $request)
    {
        if (! $request->session()->has('registrasi.user_id')) {
            return response()->json(['berhasil' => false, 'pesan' => 'Sesi tidak valid.'], 403);
        }

        $request->validate(
            ['otp' => ['required', 'digits:6']],
            ['otp.required' => 'Kode OTP wajib diisi.', 'otp.digits' => 'OTP harus 6 digit angka.']
        );

        $sessionOtp    = $request->session()->get('registrasi.otp');
        $sessionExpiry = $request->session()->get('registrasi.otp_expiry');

        // Cek OTP kadaluarsa
        if (! $sessionOtp || ! $sessionExpiry || now()->timestamp > $sessionExpiry) {
            return response()->json([
                'berhasil' => false,
                'pesan'    => 'Kode OTP sudah kadaluarsa. Kirim ulang OTP.',
            ]);
        }

        // Bandingkan OTP secara constant-time untuk mencegah timing attack
        if (! hash_equals((string) $sessionOtp, (string) $request->otp)) {
            return response()->json([
                'berhasil' => false,
                'pesan'    => 'Kode OTP tidak valid.',
            ]);
        }

        // OTP valid — tandai email terverifikasi meski tanpa wajah
        $user = User::findOrFail($request->session()->get('registrasi.user_id'));
        $user->email_verified_at = now();
        $user->save();

        // Bersihkan session
        $request->session()->forget([
            'registrasi.anggota_resmi_id',
            'registrasi.user_id',
            'registrasi.langkah',
            'registrasi.otp',
            'registrasi.otp_expiry',
        ]);

        return response()->json([
            'berhasil' => true,
            'pesan'    => 'Verifikasi berhasil! Akun menunggu persetujuan administrator.',
            'redirect' => route('registrasi.selesai'),
        ]);
    }

    /** Halaman konfirmasi setelah registrasi selesai */
    public function selesai()
    {
        return view('auth.registrasi.selesai');
    }
}
