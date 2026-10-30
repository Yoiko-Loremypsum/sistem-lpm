@extends('layouts.auth')

@section('title', 'Pendaftaran Berhasil')
@section('header-title', 'Pendaftaran Selesai!')
@section('header-subtitle', 'Akun Anda sedang dalam proses peninjauan')

@section('content')

<div style="text-align: center; padding: 10px 0;">
    {{-- Ikon sukses --}}
    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #16A34A, #22C55E);
                border-radius: 50%; display: flex; align-items: center; justify-content: center;
                margin: 0 auto 20px; box-shadow: 0 12px 30px rgba(22,163,74,0.3);">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
    </div>

    <h2 style="font-size: 1.2rem; font-weight: 800; color: #0F172A; margin: 0 0 8px;">Pendaftaran Berhasil!</h2>
    <p style="font-size: 0.85rem; color: #64748B; line-height: 1.6; margin: 0 0 24px;">
        Akun Anda telah berhasil dibuat dan sedang menunggu persetujuan administrator.
        Anda akan mendapat notifikasi melalui email institusi setelah akun disetujui.
    </p>

    <div style="background: #E6EFFA; border-radius: 12px; padding: 16px; margin-bottom: 24px; text-align: left;">
        <p style="font-size: 0.8rem; font-weight: 700; color: #1358B8; margin: 0 0 8px;">Langkah selanjutnya:</p>
        <ul style="font-size: 0.8rem; color: #475569; padding-left: 18px; margin: 0; line-height: 1.8;">
            <li>Administrator akan meninjau akun Anda dalam 1×24 jam</li>
            <li>Notifikasi persetujuan dikirim ke email institusi Anda</li>
            <li>Setelah disetujui, Anda dapat masuk menggunakan NIM/NIDN dan kata sandi</li>
        </ul>
    </div>

    <a href="{{ route('login') }}" class="btn-pill-primary" style="display: block; padding: 13px; font-size: 0.95rem; text-decoration: none; text-align: center;">
        Menuju Halaman Login
    </a>
</div>

@endsection
