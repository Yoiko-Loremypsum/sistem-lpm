@extends('layouts.auth')

@section('title', 'Atur Ulang Kata Sandi')
@section('header-title', 'Buat Kata Sandi Baru')
@section('header-subtitle', 'Masukkan kata sandi baru untuk akun Sistem LPM UMI Anda')

@section('content')

<form action="{{ route('password.update') }}" method="POST">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <div class="form-group">
        <label class="form-label" for="email">Alamat Email</label>
        <input
            type="email"
            id="email"
            name="email"
            class="form-input"
            value="{{ old('email', $email) }}"
            required
            readonly
            style="background-color: #F1F5F9; cursor: not-allowed;"
        >
        @error('email')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    {{-- Kata Sandi Baru --}}
    <div class="form-group">
        <label class="form-label" for="password">Kata Sandi Baru</label>
        <div class="password-input-wrapper">
            <input
                type="password"
                id="password"
                name="password"
                class="form-input"
                placeholder="Min. 8 karakter (huruf & angka)"
                required
                autocomplete="new-password"
                autofocus
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
        {{-- Indikator kekuatan password --}}
        <div id="passwordStrength" style="margin-top: 6px; display: none;">
            <div style="height: 4px; border-radius: 2px; background: #E6EFFA; overflow: hidden;">
                <div id="strengthBar" style="height: 100%; width: 0%; transition: all 0.3s; border-radius: 2px;"></div>
            </div>
            <p id="strengthLabel" style="font-size: 0.72rem; margin-top: 4px; color: #94A3B8;"></p>
        </div>
    </div>

    {{-- Konfirmasi Kata Sandi Baru --}}
    <div class="form-group">
        <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
        <div class="password-input-wrapper">
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="form-input"
                placeholder="Ulangi kata sandi baru"
                required
                autocomplete="new-password"
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

    <button type="submit" class="btn-pill-primary" style="width: 100%; padding: 13px; font-size: 0.95rem; margin-top: 10px;">
        Simpan Kata Sandi Baru &rarr;
    </button>
</form>

<div style="margin-top: 24px; text-align: center; font-size: 0.85rem; color: #64748B;">
    Batal mengatur ulang kata sandi?
    <a href="{{ route('login') }}" style="color: #1358B8; font-weight: 700; text-decoration: none;">Kembali ke Halaman Masuk</a>
</div>

@push('scripts')
<script>
    const passwordInput = document.getElementById('password');
    const strengthDiv   = document.getElementById('passwordStrength');
    const strengthBar   = document.getElementById('strengthBar');
    const strengthLabel = document.getElementById('strengthLabel');

    if (passwordInput && strengthDiv) {
        passwordInput.addEventListener('input', function () {
            const val = this.value;
            strengthDiv.style.display = val.length > 0 ? 'block' : 'none';

            let score = 0;
            if (val.length >= 8)  score++;
            if (/[a-zA-Z]/.test(val)) score++;
            if (/\d/.test(val))    score++;
            if (/[^a-zA-Z0-9]/.test(val)) score++;

            const levels = [
                { pct: '25%', color: '#EF4444', label: 'Sangat lemah' },
                { pct: '50%', color: '#F97316', label: 'Lemah' },
                { pct: '75%', color: '#EAB308', label: 'Cukup kuat' },
                { pct: '100%', color: '#16A34A', label: 'Kuat' },
            ];
            const lvl = levels[Math.min(score - 1, 3)] || levels[0];

            strengthBar.style.width      = lvl.pct;
            strengthBar.style.background = lvl.color;
            strengthLabel.textContent    = lvl.label;
            strengthLabel.style.color    = lvl.color;
        });
    }
</script>
@endpush

@endsection
