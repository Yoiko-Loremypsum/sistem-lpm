@extends('layouts.auth')

@section('title', 'Daftar Akun — Langkah 1 dari 3')
@section('header-title', 'Pendaftaran Akun')
@section('header-subtitle', 'Verifikasi data resmi civitas Universitas Methodist Indonesia')

@section('content')

{{-- Indikator Langkah --}}
<div style="display: flex; align-items: center; justify-content: center; gap: 0; margin-bottom: 28px;">
    {{-- Langkah 1 (aktif) --}}
    <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
        <div style="width: 36px; height: 36px; border-radius: 50%; background: #1358B8; color: white;
                    display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;
                    box-shadow: 0 0 0 4px rgba(19,88,184,0.18);">1</div>
        <span style="font-size: 0.72rem; font-weight: 600; color: #1358B8;">Cek Data</span>
    </div>
    <div style="flex: 1; height: 2px; background: #E6EFFA; margin: 0 8px; margin-bottom: 16px;"></div>
    {{-- Langkah 2 --}}
    <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
        <div style="width: 36px; height: 36px; border-radius: 50%; background: #E6EFFA; color: #94A3B8;
                    display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;">2</div>
        <span style="font-size: 0.72rem; font-weight: 600; color: #94A3B8;">Buat Akun</span>
    </div>
    <div style="flex: 1; height: 2px; background: #E6EFFA; margin: 0 8px; margin-bottom: 16px;"></div>
    {{-- Langkah 3 --}}
    <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
        <div style="width: 36px; height: 36px; border-radius: 50%; background: #E6EFFA; color: #94A3B8;
                    display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;">3</div>
        <span style="font-size: 0.72rem; font-weight: 600; color: #94A3B8;">Daftar Wajah</span>
    </div>
</div>

{{-- Kotak Informasi --}}
<div style="background: #E6EFFA; border-radius: 12px; padding: 14px 16px; margin-bottom: 22px; border-left: 4px solid #1358B8;">
    <p style="margin: 0; font-size: 0.82rem; color: #0B3F8C; line-height: 1.5;">
        <strong>Hanya untuk civitas UMI.</strong> Masukkan NIM (mahasiswa) atau NIDN (dosen) Anda.
        Pendaftaran hanya bisa dilakukan jika data Anda terdaftar di sistem akademik universitas.
    </p>
</div>

<form action="{{ route('registrasi.langkah1.proses') }}" method="POST">
    @csrf

    <div class="form-group">
        <label class="form-label" for="nim_nidn">NIM / NIDN</label>
        <input
            type="text"
            id="nim_nidn"
            name="nim_nidn"
            class="form-input"
            placeholder="Contoh: 2201001 atau 0012345601"
            value="{{ old('nim_nidn') }}"
            required
            autofocus
            autocomplete="off"
        >
        @error('nim_nidn')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="btn-pill-primary" style="width: 100%; padding: 13px; font-size: 0.95rem; margin-top: 8px;">
        Verifikasi Data &rarr;
    </button>
</form>

<div style="margin-top: 24px; text-align: center; font-size: 0.85rem; color: #64748B;">
    Sudah memiliki akun?
    <a href="{{ route('login') }}" style="color: #1358B8; font-weight: 700; text-decoration: none;">Masuk di Sini</a>
</div>

@endsection
