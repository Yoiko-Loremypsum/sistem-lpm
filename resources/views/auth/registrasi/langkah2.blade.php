@extends('layouts.auth')

@section('title', 'Daftar Akun — Langkah 2 dari 3')
@section('header-title', 'Lengkapi Akun')
@section('header-subtitle', 'Buat kata sandi untuk akun Sistem LPM UMI Anda')

@section('content')

{{-- Indikator Langkah --}}
<div style="display: flex; align-items: center; justify-content: center; gap: 0; margin-bottom: 28px;">
    {{-- Langkah 1 (selesai) --}}
    <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
        <div style="width: 36px; height: 36px; border-radius: 50%; background: #16A34A; color: white;
                    display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;">✓</div>
        <span style="font-size: 0.72rem; font-weight: 600; color: #16A34A;">Cek Data</span>
    </div>
    <div style="flex: 1; height: 2px; background: #1358B8; margin: 0 8px; margin-bottom: 16px;"></div>
    {{-- Langkah 2 (aktif) --}}
    <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
        <div style="width: 36px; height: 36px; border-radius: 50%; background: #1358B8; color: white;
                    display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;
                    box-shadow: 0 0 0 4px rgba(19,88,184,0.18);">2</div>
        <span style="font-size: 0.72rem; font-weight: 600; color: #1358B8;">Buat Akun</span>
    </div>
    <div style="flex: 1; height: 2px; background: #E6EFFA; margin: 0 8px; margin-bottom: 16px;"></div>
    {{-- Langkah 3 --}}
    <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
        <div style="width: 36px; height: 36px; border-radius: 50%; background: #E6EFFA; color: #94A3B8;
                    display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;">3</div>
        <span style="font-size: 0.72rem; font-weight: 600; color: #94A3B8;">Daftar Wajah</span>
    </div>
</div>

<form action="{{ route('registrasi.langkah2.proses') }}" method="POST">
    @csrf

    {{-- Data Resmi (read-only) --}}
    <div style="background: #E6EFFA; border-radius: 12px; padding: 16px; margin-bottom: 20px; border: 1px solid rgba(19,88,184,0.15);">
        <p style="font-size: 0.75rem; font-weight: 600; color: #1358B8; margin: 0 0 10px; text-transform: uppercase; letter-spacing: 0.5px;">
            Data Resmi Terverifikasi
        </p>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <div>
                <p style="font-size: 0.75rem; color: #64748B; margin: 0 0 2px;">Nama Lengkap</p>
                <p style="font-size: 0.9rem; font-weight: 700; color: #0F172A; margin: 0;">{{ $anggota->nama }}</p>
            </div>
            <div>
                <p style="font-size: 0.75rem; color: #64748B; margin: 0 0 2px;">{{ $anggota->label_nim_nidn }}</p>
                <p style="font-size: 0.9rem; font-weight: 700; color: #0F172A; margin: 0; font-family: monospace;">{{ $anggota->nim_nidn }}</p>
            </div>
            <div>
                <p style="font-size: 0.75rem; color: #64748B; margin: 0 0 2px;">Status</p>
                <p style="font-size: 0.9rem; font-weight: 700; color: #0F172A; margin: 0;">{{ $anggota->label_jenis }}</p>
            </div>
            @if($anggota->prodi)
            <div>
                <p style="font-size: 0.75rem; color: #64748B; margin: 0 0 2px;">Program Studi</p>
                <p style="font-size: 0.9rem; font-weight: 700; color: #0F172A; margin: 0;">{{ $anggota->prodi }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Username (read-only) --}}
    <div class="form-group">
        <label class="form-label" for="username">Username (NIM/NIDN)</label>
        <input
            type="text"
            id="username"
            name="username"
            class="form-input"
            value="{{ $anggota->nim_nidn }}"
            readonly
            style="background: #F8FAFC; color: #64748B; cursor: not-allowed;"
        >
        <p style="font-size: 0.75rem; color: #94A3B8; margin-top: 4px;">
            Username diisi otomatis dengan NIM/NIDN dan tidak dapat diubah.
        </p>
    </div>

    {{-- Kata Sandi --}}
    <div class="form-group">
        <label class="form-label" for="password">Kata Sandi</label>
        <div class="password-input-wrapper">
            <input
                type="password"
                id="password"
                name="password"
                class="form-input"
                placeholder="Min. 8 karakter, huruf & angka"
                required
                autocomplete="new-password"
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

    {{-- Konfirmasi Kata Sandi --}}
    <div class="form-group">
        <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi</label>
        <div class="password-input-wrapper">
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="form-input"
                placeholder="Ulangi kata sandi"
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

    {{-- Persetujuan Biometrik (UU PDP No. 27/2022) --}}
    <div style="background: #FFF8E1; border: 1px solid #FCC419; border-radius: 12px; padding: 16px; margin-bottom: 20px;">
        <p style="font-size: 0.8rem; font-weight: 700; color: #92400E; margin: 0 0 8px;">
            ⚠ Persetujuan Pemrosesan Data Biometrik
        </p>
        <p style="font-size: 0.78rem; color: #78350F; line-height: 1.6; margin: 0 0 12px;">
            Sesuai <strong>Undang-Undang Perlindungan Data Pribadi No. 27 Tahun 2022</strong>,
            sistem ini akan memproses dan menyimpan data biometrik wajah Anda dalam bentuk vektor embedding
            yang terenkripsi. Data ini digunakan <em>semata-mata</em> untuk verifikasi identitas saat login.
            Data biometrik tidak akan dibagikan kepada pihak ketiga dan dapat dihapus atas permintaan Anda.
        </p>
        <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer;">
            <input
                type="checkbox"
                name="setuju_biometrik"
                id="setuju_biometrik"
                value="1"
                style="accent-color: #1358B8; width: 16px; height: 16px; margin-top: 2px; flex-shrink: 0; cursor: pointer;"
                {{ old('setuju_biometrik') ? 'checked' : '' }}
            >
            <span style="font-size: 0.78rem; color: #78350F; line-height: 1.5;">
                Saya menyetujui pemrosesan data biometrik wajah saya sesuai kebijakan privasi UMI
                dan ketentuan UU PDP No. 27/2022.
            </span>
        </label>
        @error('setuju_biometrik')
            <p class="form-error" style="margin-top: 8px;">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="btn-pill-primary" style="width: 100%; padding: 13px; font-size: 0.95rem;">
        Buat Akun &amp; Lanjutkan &rarr;
    </button>
</form>

<div style="margin-top: 16px; text-align: center; font-size: 0.85rem; color: #64748B;">
    <a href="{{ route('registrasi.langkah1') }}" style="color: #1358B8; text-decoration: none;">&larr; Kembali ke Langkah 1</a>
</div>

@push('scripts')
<script>
    // Toggle visibilitas password
    function toggleVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
        }
    }

    // Indikator kekuatan password
    const passwordInput = document.getElementById('password');
    const strengthDiv   = document.getElementById('passwordStrength');
    const strengthBar   = document.getElementById('strengthBar');
    const strengthLabel = document.getElementById('strengthLabel');

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

        strengthBar.style.width    = lvl.pct;
        strengthBar.style.background = lvl.color;
        strengthLabel.textContent  = lvl.label;
        strengthLabel.style.color  = lvl.color;
    });
</script>
@endpush

@endsection
