@extends('layouts.auth')

@section('title', 'Masuk ke Sistem')
@section('header-title', 'Selamat Datang Kembali')
@section('header-subtitle', 'Masuk dengan NIM/NIDN dan kata sandi Anda')

@section('content')
<form action="{{ route('login') }}" method="POST">
    @csrf

    <div class="form-group">
        <label class="form-label" for="username">NIM / NIDN / Email / Username</label>
        <input
            type="text"
            id="username"
            name="username"
            class="form-input"
            placeholder="Contoh: 2201001, admin, atau perdi@gmail.com"
            value="{{ old('username') }}"
            required
            autofocus
            autocomplete="username"
        >
        @error('username')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="password">Kata Sandi</label>
        <div class="password-input-wrapper">
            <input
                type="password"
                id="password"
                name="password"
                class="form-input"
                placeholder="••••••••"
                required
                autocomplete="current-password"
            >
            <button type="button"
                    class="password-toggle-btn"
                    onclick="togglePasswordVisibility('password', this)"
                    title="Lihat kata sandi"
                    aria-label="Lihat kata sandi">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
            </button>
        </div>
        @error('password')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
        <label style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: #64748B; cursor: pointer;">
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                   style="accent-color: #1358B8; width: 16px; height: 16px; cursor: pointer;">
            Ingat saya
        </label>
        <a href="{{ route('password.request') }}" style="font-size: 0.82rem; color: #1358B8; font-weight: 600; text-decoration: none;">
            Lupa kata sandi?
        </a>
    </div>

    <button type="submit" class="btn-pill-primary" style="width: 100%; padding: 13px; font-size: 0.95rem;">
        Masuk &rarr;
    </button>
</form>

<div style="margin-top: 24px; text-align: center; font-size: 0.85rem; color: #64748B;">
    Belum memiliki akun?
    <a href="{{ route('registrasi.langkah1') }}" style="color: #1358B8; font-weight: 700; text-decoration: none;">Daftar Sekarang</a>
</div>

@endsection