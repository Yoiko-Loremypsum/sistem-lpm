@extends('layouts.app')

@section('title', 'Buat Pengaduan Baru')
@section('page-title', 'Form Pengaduan')
@section('page-subtitle', 'Sampaikan laporan keluhan fasilitas kampus atau layanan secara lengkap dan jelas')

@section('topbar-actions')
    <a href="{{ route('anggota.pengaduan.index') }}" class="btn-pill-white">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        <span>Kembali ke Daftar</span>
    </a>
@endsection

@section('content')
<div style="max-width: 860px; margin: 0 auto; width: 100%;">
    <div class="form-card">
        <form action="{{ route('anggota.pengaduan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Judul Pengaduan -->
            <div class="form-group">
                <label class="form-label" for="judul">Judul Pengaduan <span style="color:#DC2626;">*</span></label>
                <input 
                    type="text" 
                    id="judul" 
                    name="judul" 
                    class="form-input" 
                    placeholder="Contoh: Kerusakan Proyektor di Ruang Kuliah 302 Gedung A" 
                    value="{{ old('judul') }}" 
                    required 
                    autofocus
                >
                <span class="form-hint">Tulis judul singkat yang menggambarkan inti keluhan atau laporan Anda.</span>
                @error('judul')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kategori & Lokasi -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label class="form-label" for="kategori_id">Kategori Pengaduan <span style="color:#DC2626;">*</span></label>
                    <select id="kategori_id" name="kategori_id" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="lokasi">Lokasi Kejadian / Titik Fasilitas</label>
                    <input 
                        type="text" 
                        id="lokasi" 
                        name="lokasi" 
                        class="form-input" 
                        placeholder="Contoh: Gedung Rektorat Lt. 2 / Lab Komputer UMI" 
                        value="{{ old('lokasi') }}"
                    >
                    <span class="form-hint">Sertakan patokan lokasi yang jelas agar mudah disurvei petugas kampus.</span>
                    @error('lokasi')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Isi Pengaduan -->
            <div class="form-group">
                <label class="form-label" for="isi_pengaduan">Uraian Isi Pengaduan <span style="color:#DC2626;">*</span></label>
                <textarea 
                    id="isi_pengaduan" 
                    name="isi_pengaduan" 
                    class="form-textarea" 
                    rows="6" 
                    placeholder="Jelaskan secara terperinci kejadian atau kendala yang Anda temui, kronologi waktu, serta dampaknya..." 
                    required
                >{{ old('isi_pengaduan') }}</textarea>
                @error('isi_pengaduan')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Lampiran Foto Bukti -->
            <div class="form-group">
                <label class="form-label" for="fotoInput">Lampiran Foto Bukti (Opsional)</label>
                <input 
                    type="file" 
                    id="fotoInput" 
                    name="foto" 
                    class="form-input" 
                    accept="image/png, image/jpeg, image/jpg, image/webp"
                >
                <span class="form-hint">Format yang didukung: JPG, JPEG, PNG, WEBP. Ukuran file maksimal: 2 MB.</span>
                @error('foto')
                    <p class="form-error">{{ $message }}</p>
                @enderror

                <!-- Pratinjau Foto -->
                <div id="fotoPreviewContainer" style="display: none; margin-top: 14px;">
                    <div style="font-size: 0.78rem; font-weight: 600; color: #64748B; margin-bottom: 6px;">Pratinjau Foto Bukti:</div>
                    <img id="fotoPreviewImage" src="#" alt="Pratinjau Foto Bukti" style="max-height: 220px; border-radius: 12px; border: 1px solid rgba(19, 88, 184, 0.2); object-fit: cover;">
                </div>
            </div>

            <!-- Aksi Tombol -->
            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(19, 88, 184, 0.12);">
                <a href="{{ route('anggota.pengaduan.index') }}" class="btn-pill-white">
                    Batalkan
                </a>
                <button type="submit" class="btn-pill-primary" style="padding: 11px 28px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                    <span>Kirim Pengaduan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
