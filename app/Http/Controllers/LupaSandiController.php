<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

/**
 * Controller: LupaSandiController
 *
 * Menangani alur lupa kata sandi dan pengaturan ulang kata sandi melalui tautan email:
 * 1. Menampilkan form permohonan reset sandi (input email atau NIM/NIDN)
 * 2. Mengirimkan tautan reset kata sandi ke email pengguna
 * 3. Menampilkan form buat kata sandi baru melalui token aman
 * 4. Menyimpan kata sandi baru ke database
 */
class LupaSandiController extends Controller
{
    /**
     * Tampilkan formulir permintaan reset kata sandi.
     */
    public function showLinkRequestForm()
    {
        return view('auth.lupa-sandi');
    }

    /**
     * Kirim tautan reset kata sandi ke email pengguna.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(
            [
                'email_atau_username' => ['required', 'string'],
            ],
            [
                'email_atau_username.required' => 'Email atau NIM/NIDN wajib diisi.',
            ]
        );

        $input = trim($request->input('email_atau_username'));

        // Cari pengguna berdasarkan email atau username (NIM/NIDN)
        $user = User::where('email', $input)
            ->orWhere('username', $input)
            ->first();

        if (! $user) {
            return back()->withErrors([
                'email_atau_username' => 'Akun dengan email atau NIM/NIDN tersebut tidak terdaftar di sistem.',
            ])->withInput();
        }

        if (! $user->isActive()) {
            return back()->withErrors([
                'email_atau_username' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi administrator.',
            ])->withInput();
        }

        // Buat token reset sandi aman menggunakan Password Broker bawaan Laravel
        $token = Password::broker()->createToken($user);

        // Buat URL reset sandi
        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        $expiryMinutes = config('auth.passwords.users.expire', 60);

        try {
            Mail::to($user->email)->send(new ResetPasswordMail($user, $resetUrl, $expiryMinutes));
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email reset password: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'email'   => $user->email,
            ]);

            return back()->withErrors([
                'email_atau_username' => 'Gagal mengirim email reset sandi. Pastikan pengaturan email server sudah aktif.',
            ])->withInput();
        }

        $emailSensor = $this->sensorEmail($user->email);
        $pesan = "Tautan reset kata sandi telah dikirim ke {$emailSensor}. Silakan periksa kotak masuk atau spam email Anda (berlaku {$expiryMinutes} menit).";

        if (config('mail.default') === 'log') {
            $pesan .= " [Catatan Pengembang: MAIL_MAILER saat ini diatur ke 'log', tautan lengkap juga tercatat di storage/logs/laravel.log]";
        }

        return back()->with('success', $pesan);
    }

    /**
     * Tampilkan formulir reset kata sandi baru.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-sandi', [
            'token' => $token,
            'email' => $request->query('email', old('email', '')),
        ]);
    }

    /**
     * Proses penyimpanan kata sandi baru.
     */
    public function reset(Request $request)
    {
        $request->validate(
            [
                'token'    => ['required'],
                'email'    => ['required', 'email'],
                'password' => [
                    'required',
                    'confirmed',
                    PasswordRule::min(8)->letters()->numbers(),
                ],
            ],
            [
                'token.required'      => 'Token reset sandi tidak valid.',
                'email.required'      => 'Alamat email wajib diisi.',
                'email.email'         => 'Format email tidak valid.',
                'password.required'   => 'Kata sandi baru wajib diisi.',
                'password.confirmed'  => 'Konfirmasi kata sandi tidak cocok.',
                'password.min'        => 'Kata sandi minimal 8 karakter.',
                'password.letters'    => 'Kata sandi harus mengandung kombinasi huruf.',
                'password.numbers'    => 'Kata sandi harus mengandung angka.',
            ]
        );

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('success', 'Kata sandi Anda berhasil diperbarui! Silakan masuk dengan kata sandi baru.');
        }

        return back()->withErrors([
            'email' => 'Tautan reset kata sandi tidak valid atau telah kadaluarsa. Silakan ajukan permohonan baru.',
        ])->withInput($request->only('email'));
    }

    /**
     * Sensor alamat email untuk privasi tampilan pengguna.
     */
    private function sensorEmail(string $email): string
    {
        if (! str_contains($email, '@')) {
            return $email;
        }

        [$local, $domain] = explode('@', $email, 2);
        $visibleCount = min(3, strlen($local));
        $hidden = substr($local, 0, $visibleCount) . str_repeat('*', max(0, strlen($local) - $visibleCount));

        return $hidden . '@' . $domain;
    }
}
