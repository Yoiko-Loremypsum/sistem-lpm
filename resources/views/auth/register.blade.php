@extends('layouts.auth')

@section('title', 'Pendaftaran Akun Pengguna')
@section('header-title', 'Daftar Akun Baru')
@section('header-subtitle', 'Sampaikan aspirasi dan keluhan Anda secara transparan dan mudah')

@section('content')
<form action="{{ route('register') }}" method="POST">
    @csrf

    <div class="form-group">
        <label class="form-label" for="nama">Nama Lengkap</label>
        <input 
            type="text" 
            id="nama" 
            name="nama" 
            class="form-input" 
            placeholder="Contoh: Budi Santoso" 
            value="{{ old('nama') }}" 
            required 
            autofocus
        >
        @error('nama')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="email">Alamat Email Aktif</label>
        <input 
            type="email" 
            id="email" 
            name="email" 
            class="form-input" 
            placeholder="budi@example.com" 
            value="{{ old('email') }}" 
            required
        >
        @error('email')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="no_hp">Nomor Handphone / WhatsApp</label>
        <input 
            type="text" 
            id="no_hp" 
            name="no_hp" 
            class="form-input" 
            placeholder="0812xxxxxxxx" 
            value="{{ old('no_hp') }}"
        >
        @error('no_hp')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="alamat">Alamat Lengkap / Domisili</label>
        <input 
            type="text" 
            id="alamat" 
            name="alamat" 
            class="form-input" 
            placeholder="Nama jalan, gedung, atau domisili lengkap" 
            value="{{ old('alamat') }}"
        >
        @error('alamat')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
        <div class="form-group">
            <label class="form-label" for="password">Kata Sandi</label>
            <div class="password-input-wrapper">
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input" 
                    placeholder="Min 6 karakter" 
                    required
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

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Ulangi Sandi</label>
            <div class="password-input-wrapper">
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    class="form-input" 
                    placeholder="Ulangi sandi" 
                    required
                >
                <button type="button"
                        class="password-toggle-btn"
                        onclick="togglePasswordVisibility('password_confirmation', this)"
                        title="Lihat kata sandi"
                        aria-label="Lihat kata sandi">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <button type="submit" class="btn-pill-primary" style="width: 100%; padding: 13px; font-size: 0.95rem; margin-top: 8px;">
        Daftar Akun Sekarang
    </button>
</form>

<div style="margin-top: 24px; text-align: center; font-size: 0.85rem; color: #64748B;">
    Sudah memiliki akun terdaftar? 
    <a href="{{ route('login') }}" style="color: #1358B8; font-weight: 700; text-decoration: none;">Masuk di Sini</a>
</div>
@endsection
