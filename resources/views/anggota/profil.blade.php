@extends('layouts.app')

@section('title', 'Profil & Kata Sandi')
@section('page-title', 'Pengaturan Profil Pengguna')
@section('page-subtitle', 'Perbarui data identitas diri dan amankan akun Anda')

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 24px; max-width: 1040px;">
    <!-- Form Perbarui Data Diri -->
    <div class="form-card">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid rgba(19, 88, 184, 0.12);">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #1358B8; color: #fff; display: flex; align-items: center; justify-content: center;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #0F172A;">Data Pribadi</h3>
                <span style="font-size: 0.75rem; color: #64748B;">Informasi kontak yang terdaftar</span>
            </div>
        </div>

        <form action="{{ route('anggota.profil.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label" for="nama">Nama Lengkap</label>
                <input 
                    type="text" 
                    id="nama" 
                    name="nama" 
                    class="form-input" 
                    value="{{ old('nama', $user->nama) }}" 
                    required
                >
                @error('nama')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Alamat Email</label>
                <input 
                    type="email" 
                    id="email" 
                    class="form-input" 
                    value="{{ $user->email }}" 
                    disabled 
                    style="background: #F1F5F9; cursor: not-allowed; color: #64748B;"
                >
                <span class="form-hint">Alamat email bersifat permanen sebagai identitas akun Anda.</span>
            </div>

            <div class="form-group">
                <label class="form-label" for="no_hp">Nomor Handphone / WhatsApp</label>
                <input 
                    type="text" 
                    id="no_hp" 
                    name="no_hp" 
                    class="form-input" 
                    value="{{ old('no_hp', $user->no_hp) }}"
                >
                @error('no_hp')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="alamat">Alamat Lengkap / Domisili</label>
                <textarea 
                    id="alamat" 
                    name="alamat" 
                    class="form-textarea" 
                    rows="3"
                >{{ old('alamat', $user->alamat) }}</textarea>
                @error('alamat')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-top: 24px; text-align: right;">
                <button type="submit" class="btn-pill-primary" style="padding: 10px 24px;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Form Ubah Kata Sandi -->
    <div class="form-card">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid rgba(19, 88, 184, 0.12);">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #1358B8; color: #fff; display: flex; align-items: center; justify-content: center;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #0F172A;">Keamanan Akun</h3>
                <span style="font-size: 0.75rem; color: #64748B;">Perbarui kata sandi secara berkala</span>
            </div>
        </div>

        <form action="{{ route('anggota.profil.password') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label" for="password_sekarang">Kata Sandi Saat Ini</label>
                <div class="password-input-wrapper">
                    <input 
                        type="password" 
                        id="password_sekarang" 
                        name="password_sekarang" 
                        class="form-input" 
                        placeholder="••••••••" 
                        required
                    >
                    <button type="button"
                            class="password-toggle-btn"
                            onclick="togglePasswordVisibility('password_sekarang', this)"
                            title="Lihat kata sandi"
                            aria-label="Lihat kata sandi">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                @error('password_sekarang')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password_baru">Kata Sandi Baru</label>
                <div class="password-input-wrapper">
                    <input 
                        type="password" 
                        id="password_baru" 
                        name="password_baru" 
                        class="form-input" 
                        placeholder="Min 8 karakter, huruf & angka" 
                        required
                    >
                    <button type="button"
                            class="password-toggle-btn"
                            onclick="togglePasswordVisibility('password_baru', this)"
                            title="Lihat kata sandi"
                            aria-label="Lihat kata sandi">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                @error('password_baru')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password_baru_confirmation">Ulangi Sandi Baru</label>
                <div class="password-input-wrapper">
                    <input 
                        type="password" 
                        id="password_baru_confirmation" 
                        name="password_baru_confirmation" 
                        class="form-input" 
                        placeholder="Ulangi sandi baru" 
                        required
                    >
                    <button type="button"
                            class="password-toggle-btn"
                            onclick="togglePasswordVisibility('password_baru_confirmation', this)"
                            title="Lihat kata sandi"
                            aria-label="Lihat kata sandi">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div style="margin-top: 24px; text-align: right;">
                <button type="submit" class="btn-pill-primary" style="padding: 10px 24px;">
                    Perbarui Kata Sandi
                </button>
            </div>
        </form>
    </div>

    <!-- Pengaturan Data Biometrik Wajah (UU PDP) -->
    <div class="form-card" style="grid-column: 1 / -1;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid rgba(19, 88, 184, 0.12);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #1358B8; color: #fff; display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 3H5a2 2 0 0 0-2 2v4m0 6v4a2 2 0 0 0 2 2h4m6 0h4a2 2 0 0 0 2-2v-4m0-6V5a2 2 0 0 0-2-2h-4"/>
                        <circle cx="12" cy="10" r="3"/>
                        <path d="M7 19c0-2.5 2.5-4 5-4s5 1.5 5 4"/>
                    </svg>
                </div>
                <div>
                    <h3 style="font-size: 1.05rem; font-weight: 700; color: #0F172A;">Data Biometrik Wajah</h3>
                    <span style="font-size: 0.75rem; color: #64748B;">Pengelolaan data biometrik sesuai Undang-Undang Pelindungan Data Pribadi (UU PDP)</span>
                </div>
            </div>
            @if($user->face_terdaftar)
                <span style="background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; font-size: 0.8rem; font-weight: 600; padding: 4px 12px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 6px;">
                    <span style="width: 6px; height: 6px; background: #059669; border-radius: 50%;"></span>
                    Wajah Terdaftar
                </span>
            @else
                <span style="background: #F1F5F9; color: #64748B; border: 1px solid #E2E8F0; font-size: 0.8rem; font-weight: 600; padding: 4px 12px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 6px;">
                    <span style="width: 6px; height: 6px; background: #94A3B8; border-radius: 50%;"></span>
                    Belum Terdaftar
                </span>
            @endif
        </div>

        <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 18px 20px; margin-bottom: 20px;">
            <p style="font-size: 0.88rem; color: #334155; line-height: 1.6; margin: 0 0 8px;">
                Sistem LPM UMI menjunjung tinggi privasi Anda. Kami hanya menyimpan ringkasan representasi matematis vektor (*facial embedding*) yang terenkripsi dan <strong>tidak pernah menyimpan foto asli wajah Anda</strong>.
            </p>
            <p style="font-size: 0.82rem; color: #64748B; margin: 0;">
                Sesuai hak subjek data pada UU PDP No. 27 Tahun 2022, Anda berhak menghapus data biometrik Anda kapan saja. Jika data biometrik dihapus, verifikasi login tahap 2 akan menggunakan kode OTP via email.
            </p>
        </div>

        @if($user->face_terdaftar)
            <form action="{{ route('anggota.profil.wajah.hapus') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data biometrik wajah Anda? Setelah dihapus, login berikutnya akan dialihkan menggunakan kode OTP email.');">
                @csrf
                @method('DELETE')
                <button type="submit" style="background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA; padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 0.88rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                    Hapus Data Wajah Saya
                </button>
            </form>
        @else
            <p style="font-size: 0.85rem; color: #64748B; margin: 0;">
                Anda saat ini tidak memiliki data wajah yang tersimpan di sistem.
            </p>
        @endif
    </div>
</div>
@endsection
