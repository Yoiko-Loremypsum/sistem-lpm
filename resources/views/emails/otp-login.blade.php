<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Login - Sistem LPM UMI</title>
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
                <p style="margin: 0 0 24px; font-size: 14px; line-height: 1.6; color: #475569;">
                    Anda menerima email ini karena ada permintaan login ke akun Sistem LPM UMI menggunakan verifikasi email/OTP.
                </p>

                <!-- OTP Box -->
                <div style="background-color: #EFF6FF; border: 1.5px dashed #1358B8; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 24px;">
                    <span style="display: block; font-size: 12px; text-transform: uppercase; color: #1D4ED8; font-weight: 600; letter-spacing: 1px; margin-bottom: 6px;">
                        Kode OTP Verifikasi Anda
                    </span>
                    <span style="display: inline-block; font-size: 32px; font-weight: 800; letter-spacing: 8px; color: #1358B8; font-family: monospace;">
                        {{ $otp }}
                    </span>
                    <span style="display: block; font-size: 12px; color: #64748B; margin-top: 6px;">
                        Berlaku selama <strong>{{ $expiryMinutes }} menit</strong>.
                    </span>
                </div>

                <!-- Security Warning -->
                <div style="background-color: #FEF3C7; border-left: 4px solid #F59E0B; border-radius: 6px; padding: 12px 14px; margin-bottom: 24px;">
                    <p style="margin: 0; font-size: 12px; color: #92400E; line-height: 1.5;">
                        ⚠️ <strong>Peringatan Keamanan:</strong> Jangan berikan kode OTP ini kepada siapa pun, termasuk staf atau pihak yang mengaku dari UMI.
                    </p>
                </div>

                <p style="margin: 0; font-size: 13px; line-height: 1.5; color: #64748B;">
                    Jika bukan Anda yang mencoba masuk ke sistem, segera amankan akun Anda atau laporkan ke administrator kampus.
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
