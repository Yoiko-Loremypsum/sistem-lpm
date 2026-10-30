@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi')
@section('header-title', 'Lupa Kata Sandi?')
@section('header-subtitle', 'Masukkan email atau NIM/NIDN Anda untuk menerima tautan pemulihan sandi')

@section('content')

@if (session('status'))
    <div style="background: #DCFCE7; border-left: 4px solid #16A34A; border-radius: 8px; padding: 14px 16px; margin-bottom: 20px;">
        <p style="margin: 0; font-size: 0.85rem; color: #15803D; line-height: 1.5;">
            {{ session('status') }}
        </p>
    </div>
@endif

<form action="{{ route('password.email') }}" method="POST">
    @csrf

    <div class="form-group">
        <label class="form-label" for="email_atau_username">Email atau NIM / NIDN</label>
        <input
            type="text"
            id="email_atau_username"
            name="email_atau_username"
            class="form-input"
            placeholder="Contoh: 2201001 atau nama@student.umi.ac.id"
            value="{{ old('email_atau_username') }}"
            required
            autofocus
            autocomplete="email"
        >
        @error('email_atau_username')
            <p class="form-error">{{ $message }}</p>
        @enderror
        <p class="form-hint" style="font-size: 0.78rem; color: #64748B; margin-top: 6px;">
            Sistem akan mengirimkan instruksi dan tautan pembuatan kata sandi baru ke email yang terdaftar pada akun Anda.
        </p>
    </div>

    <button type="submit" class="btn-pill-primary" style="width: 100%; padding: 13px; font-size: 0.95rem; margin-top: 10px;">
        Kirim Tautan Reset Sandi &rarr;
    </button>
</form>

<div style="margin-top: 24px; text-align: center; font-size: 0.85rem; color: #64748B;">
    Ingat kata sandi Anda?
    <a href="{{ route('login') }}" style="color: #1358B8; font-weight: 700; text-decoration: none;">Kembali ke Halaman Masuk</a>
</div>

@endsection
