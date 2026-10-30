<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi - Sistem LPM UMI</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #F8FAFC; margin: 0; padding: 24px; color: #1E293B;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 560px; margin: 0 auto; background-color: #FFFFFF; border-radius: 16px; border: 1px solid #E2E8F0; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
        <!-- Header -->
        <tr>
            <td style="background-color: #1358B8; padding: 28px 32px; text-align: center;">
                <h1 style="color: #FFFFFF; margin: 0; font-size: 20px; font-weight: 700; letter-spacing: 0.5px;">
                    SISTEM LPM UMI
                </h1>
                <p style="color: #BFDBFE; margin: 6px 0 0; font-size: 13px;">
                    Layanan Pengaduan Masyarakat — Universitas Methodist Indonesia
                </p>
            </td>
        </tr>

        <!-- Body -->
        <tr>
            <td style="padding: 32px;">
                <p style="margin: 0 0 16px; font-size: 15px; color: #334155;">
                    Halo, <strong>{{ $user->nama }}</strong>,
                </p>
                <p style="margin: 0 0 20px; font-size: 14px; line-height: 1.6; color: #475569;">
                    Kami menerima permintaan untuk mereset kata sandi akun Sistem LPM UMI Anda. Klik tombol di bawah ini untuk membuat kata sandi baru:
                </p>

                <!-- Reset Button -->
                <div style="text-align: center; margin: 28px 0;">
                    <a href="{{ $resetUrl }}" style="display: inline-block; background-color: #1358B8; color: #FFFFFF; text-decoration: none; font-size: 14px; font-weight: 700; padding: 14px 28px; border-radius: 9999px; box-shadow: 0 4px 12px rgba(19, 88, 184, 0.28);">
                        Atur Ulang Kata Sandi &rarr;
                    </a>
                </div>

                <p style="margin: 0 0 16px; font-size: 13px; line-height: 1.5; color: #64748B;">
                    Tautan reset kata sandi ini hanya berlaku selama <strong>{{ $expiryMinutes }} menit</strong>.
                </p>

                <!-- Security Note -->
                <div style="background-color: #F1F5F9; border-radius: 8px; padding: 14px; margin-bottom: 24px; font-size: 12px; color: #64748B; line-height: 1.5;">
                    <p style="margin: 0 0 8px;">
                        Jika tombol di atas tidak berfungsi, salin dan tempel URL berikut ke peramban (browser) Anda:
                    </p>
                    <p style="margin: 0; word-break: break-all; color: #1358B8;">
                        <a href="{{ $resetUrl }}" style="color: #1358B8; text-decoration: underline;">{{ $resetUrl }}</a>
                    </p>
                </div>

                <p style="margin: 0; font-size: 13px; line-height: 1.5; color: #64748B;">
                    Jika Anda tidak pernah meminta reset kata sandi, abaikan email ini. Kata sandi akun Anda tetap aman dan tidak berubah.
                </p>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background-color: #F1F5F9; padding: 18px 32px; text-align: center; border-top: 1px solid #E2E8F0;">
                <p style="margin: 0; font-size: 12px; color: #64748B;">
                    &copy; {{ date('Y') }} Sistem LPM Universitas Methodist Indonesia. All rights reserved.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
