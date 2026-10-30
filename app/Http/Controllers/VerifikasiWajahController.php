<?php

namespace App\Http\Controllers;

use App\Mail\OtpLoginMail;
use App\Models\User;
use App\Services\FaceRecognitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Controller: VerifikasiWajahController
 *
 * Menangani tahap 2 login: verifikasi wajah.
 *
 * Aturan keamanan KRITIS:
 * - Keputusan "cocok / tidak cocok" HANYA dibuat di server (PHP), bukan di JavaScript.
 * - face_embedding tidak pernah dikirim ke browser.
 * - Setelah 3 kali gagal, kirim OTP ke email institusi dan paksa kembali ke login.
 *
 * Halaman ini hanya bisa diakses melalui WajahMiddleware yang memastikan
 * session 'menunggu_wajah' ada (sudah melewati tahap password).
 */
class VerifikasiWajahController extends Controller
{
    /** Tampilkan halaman kamera verifikasi wajah */
    public function show(Request $request)
    {
        // WajahMiddleware sudah memastikan session 'menunggu_wajah' ada
        $userId = $request->session()->get('menunggu_wajah');
        $user   = User::findOrFail($userId);

        return view('auth.verifikasi-wajah', [
            'emailSensor' => $this->sensorEmail($user->email),
        ]);
    }

    /**
     * Proses verifikasi wajah (face-api.js di browser + perbandingan Euclidean di server).
     *
     * Alur:
     * 1. Terima descriptor 128-float dari browser klien
     * 2. Ambil descriptor tersimpan pengguna dari DB (didekripsi di server)
     * 3. Hitung jarak Euclidean (L2 Distance) di PHP
     * 4. Jika jarak <= threshold (default 0.50) → Cocok, lakukan login penuh
     * 5. Jika jarak > threshold → Tidak cocok, tambahkan counter gagal (maks 3x)
     */
    public function verifikasi(Request $request, FaceRecognitionService $faceService)
    {
        $userId = $request->session()->get('menunggu_wajah');

        if (! $userId) {
            return response()->json([
                'berhasil' => false,
                'pesan'    => 'Sesi verifikasi tidak valid. Silakan login ulang.',
                'redirect' => route('login'),
            ], 403);
        }

        $request->validate(
            [
                'face_descriptor' => ['required', 'array', 'size:128'],
            ],
            [
                'face_descriptor.required' => 'Data biometrik wajah wajib disertakan.',
                'face_descriptor.array'    => 'Format biometrik wajah tidak valid.',
                'face_descriptor.size'     => 'Panjang vektor biometrik wajah harus tepat 128 dimensi.',
            ]
        );

        $inputDescriptor = $request->input('face_descriptor');

        if (! $faceService->isValidDescriptor($inputDescriptor)) {
            return response()->json([
                'berhasil' => false,
                'pesan'    => 'Data wajah tidak valid. Pastikan wajah berada tepat di dalam area oval kamera.',
            ], 422);
        }

        $user = User::findOrFail($userId);

        // Cek apakah user sudah mendaftarkan wajah
        if (! $user->face_terdaftar) {
            return response()->json([
                'berhasil'  => false,
                'otp_mode'  => true,
                'pesan'     => 'Anda belum mendaftarkan biometrik wajah. Silakan gunakan OTP email.',
            ]);
        }

        // Ambil descriptor tersimpan (didekripsi di server, tidak pernah dikirim ke browser)
        $descriptorTersimpan = $user->getFaceDescriptor();

        if (! $descriptorTersimpan) {
            Log::error('Gagal mendekripsi descriptor wajah saat login', ['user_id' => $user->id]);
            return response()->json([
                'berhasil' => false,
                'pesan'    => 'Terjadi kesalahan sistem dekripsi biometrik. Hubungi administrator.',
            ], 500);
        }

        // Penanganan jika data wajah tersimpan merupakan versi model lama (misal 512 float dari ArcFace)
        if (count($descriptorTersimpan) !== 128) {
            Log::warning('Data wajah tersimpan menggunakan versi model lama (bukan 128 float)', [
                'user_id' => $user->id,
                'dimensi' => count($descriptorTersimpan),
            ]);
            return response()->json([
                'berhasil'  => false,
                'otp_mode'  => true,
                'pesan'     => 'Data biometrik wajah Anda menggunakan format model terdahulu. Silakan masuk via OTP dan perbarui data wajah pada menu profil.',
            ]);
        }

        // ======================================================
        // Bandingkan dua descriptor di server (PHP) secara aman
        // ======================================================
        $result = $faceService->compare($descriptorTersimpan, $inputDescriptor);

        if ($result['match'] === true) {
            return $this->loginPenuh($request, $user);
        } else {
            return $this->tanganiGagal($request, $user);
        }
    }

    /**
     * Kirim OTP ke email institusi sebagai alternatif verifikasi wajah.
     */
    public function kirimOtp(Request $request)
    {
        $userId = $request->session()->get('menunggu_wajah');
        if (! $userId) {
            return response()->json(['berhasil' => false, 'pesan' => 'Sesi tidak valid.'], 403);
        }

        $user   = User::findOrFail($userId);
        $otp    = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiry = now()->addMinutes(10)->timestamp;

        $request->session()->put('wajah_otp', $otp);
        $request->session()->put('wajah_otp_expiry', $expiry);

        try {
            Mail::to($user->email)->send(new OtpLoginMail($user, $otp, 10));
        } catch (\Exception $e) {
            Log::error('Gagal kirim OTP login: ' . $e->getMessage(), ['user_id' => $user->id]);
            return response()->json(['berhasil' => false, 'pesan' => 'Gagal mengirim OTP ke email. Pastikan koneksi atau pengaturan email server sudah benar.'], 500);
        }

        $pesan = "OTP dikirim ke {$this->sensorEmail($user->email)}. Berlaku 10 menit.";
        if (config('mail.default') === 'log') {
            $pesan .= " [Catatan: MAIL_MAILER=log aktif, kode OTP dapat dilihat di storage/logs/laravel.log: {$otp}]";
        }

        return response()->json([
            'berhasil' => true,
            'pesan'    => $pesan,
        ]);
    }

    /**
     * Verifikasi OTP login dan masukkan user jika valid.
     */
    public function verifikasiOtp(Request $request)
    {
        $userId = $request->session()->get('menunggu_wajah');
        if (! $userId) {
            return response()->json(['berhasil' => false, 'pesan' => 'Sesi tidak valid.'], 403);
        }

        $request->validate(
            ['otp' => ['required', 'digits:6']],
            ['otp.required' => 'Kode OTP wajib diisi.', 'otp.digits' => 'OTP harus 6 digit.']
        );

        $sessionOtp    = $request->session()->get('wajah_otp');
        $sessionExpiry = $request->session()->get('wajah_otp_expiry');

        if (! $sessionOtp || now()->timestamp > $sessionExpiry) {
            return response()->json(['berhasil' => false, 'pesan' => 'OTP kadaluarsa. Kirim ulang.']);
        }

        if (! hash_equals((string) $sessionOtp, (string) $request->otp)) {
            return response()->json(['berhasil' => false, 'pesan' => 'Kode OTP tidak valid.']);
        }

        $user = User::findOrFail($userId);
        return $this->loginPenuh($request, $user);
    }

    /**
     * Hapus data wajah pengguna (hak atas UU PDP).
     * Hanya bisa diakses oleh user yang sudah login penuh.
     */
    public function hapusDataWajah(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $user->hapusDataWajah();

        return back()->with('success', 'Data biometrik wajah Anda berhasil dihapus dari sistem.');
    }

    // =========================================================
    // Private Helpers
    // =========================================================

    /**
     * Lakukan login penuh: regenerasi session, hapus key sementara, masuk.
     */
    private function loginPenuh(Request $request, User $user)
    {
        $remember = $request->session()->get('menunggu_wajah_remember', false);

        // Reset counter gagal wajah
        $user->gagal_login_wajah = 0;
        $user->save();

        // Hapus key-key sementara dari session
        $request->session()->forget([
            'menunggu_wajah',
            'menunggu_wajah_remember',
            'wajah_otp',
            'wajah_otp_expiry',
        ]);

        // Regenerasi session untuk mencegah session fixation
        $request->session()->regenerate();

        Auth::login($user, $remember);

        return response()->json([
            'berhasil' => true,
            'pesan'    => 'Verifikasi berhasil! Selamat datang, ' . $user->nama . '.',
            'redirect' => route('anggota.dashboard'),
        ]);
    }

    /**
     * Tangani kegagalan verifikasi wajah.
     * Setelah 3 kali gagal → paksa OTP, bersihkan session wajah.
     */
    private function tanganiGagal(Request $request, User $user)
    {
        $user->increment('gagal_login_wajah');

        if ($user->gagal_login_wajah >= 3) {
            // Reset counter dan bersihkan session wajah
            $user->gagal_login_wajah = 0;
            $user->save();

            // Kirim OTP otomatis
            $this->kirimOtpOtomatis($user);

            // Hapus session wajah → paksa kembali ke login dengan info OTP
            $request->session()->forget(['menunggu_wajah', 'menunggu_wajah_remember']);

            return response()->json([
                'berhasil'       => false,
                'terlalu_banyak' => true,
                'pesan'          => 'Verifikasi wajah gagal 3 kali. Kode OTP telah dikirim ke email institusi Anda. '
                                  . 'Silakan login kembali dan gunakan OTP.',
                'redirect'       => route('login'),
            ]);
        }

        $sisa = 3 - $user->gagal_login_wajah;

        return response()->json([
            'berhasil' => false,
            'pesan'    => "Wajah tidak cocok. {$sisa} percobaan tersisa sebelum akun dikunci sementara.",
        ]);
    }

    /** Kirim OTP otomatis saat counter gagal mencapai batas */
    private function kirimOtpOtomatis(User $user): void
    {
        try {
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            Mail::raw(
                "Login Anda gagal 3 kali karena verifikasi wajah tidak berhasil.\n\n"
                . "Kode OTP darurat Anda: {$otp}\n\n"
                . "Kode ini berlaku 15 menit. Gunakan saat login berikutnya.\n"
                . "Jika bukan Anda, segera hubungi administrator.",
                fn($m) => $m
                    ->to($user->email)
                    ->subject('[Sistem LPM UMI] OTP Darurat — Verifikasi Wajah Gagal')
            );
            // Simpan OTP ke cache agar bisa diverifikasi saat login berikutnya
            cache()->put("otp_darurat_{$user->id}", $otp, 900); // 15 menit
        } catch (\Exception $e) {
            Log::error('Gagal kirim OTP otomatis', ['user_id' => $user->id, 'error' => $e->getMessage()]);
        }
    }

    /** Sembunyikan sebagian email untuk ditampilkan ke pengguna */
    private function sensorEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email);
        $hidden = substr($local, 0, 3) . str_repeat('*', max(0, strlen($local) - 3));
        return $hidden . '@' . $domain;
    }
}
