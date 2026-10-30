@extends('layouts.app')

@section('title', 'Tindak Lanjut ' . $pengaduan->kode_tiket)
@section('page-title', 'Tindak Lanjut Pengaduan')
@section('page-subtitle', 'Pemeriksaan laporan, pembaruan status, dan pemberian tanggapan resmi')

@section('topbar-actions')
    <a href="{{ route('admin.pengaduan.index') }}" class="btn-pill-white">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        <span>Kembali ke Daftar</span>
    </a>
@endsection

@section('content')
<!-- Banner & Badge Penanda Khusus Pengaduan Selesai -->
@if($pengaduan->status === 'selesai')
    <div style="background: #E6EFFA; border: 1.5px solid #1358B8; border-radius: 16px; padding: 16px 22px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; box-shadow: 0 4px 14px rgba(19, 88, 184, 0.08);">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 42px; height: 42px; border-radius: 50%; background: #1358B8; color: #FFFFFF; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 8px rgba(19, 88, 184, 0.25);">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div>
                <h4 style="font-size: 1.05rem; font-weight: 800; color: #0B3F8C; margin-bottom: 2px;">
                    Pengaduan Ini Sudah Selesai Ditangani
                </h4>
                <p style="font-size: 0.83rem; color: #475569; margin: 0;">
                    Laporan ini telah diselesaikan secara tuntas dan ditutup. Seluruh data laporan pengaduan dan riwayat tanggapan bersifat arsip baca saja (read-only).
                </p>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <span class="badge badge-selesai" style="font-size: 0.88rem; padding: 7px 18px; font-weight: 700; letter-spacing: 0.3px;">
                ✓ Pengaduan Selesai
            </span>
        </div>
    </div>
@endif

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
    <!-- Kolom Kiri: Detail Laporan, Foto, & Riwayat Tanggapan -->
    <div style="display: flex; flex-direction: column; gap: 24px; grid-column: span 2;">
        <!-- Kartu Detail Laporan -->
        <div class="form-card">
            <!-- Header Tiket & Status -->
            <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(19, 88, 184, 0.12); padding-bottom: 18px; margin-bottom: 20px;">
                <div>
                    <span style="font-size: 0.72rem; text-transform: uppercase; color: #64748B; font-weight: 700; letter-spacing: 0.6px;">NOMOR TIKET</span>
                    <h2 style="font-size: 1.4rem; font-family: monospace; font-weight: 800; color: #1358B8; margin-top: 2px;">
                        {{ $pengaduan->kode_tiket }}
                    </h2>
                </div>
                <div style="text-align: right;">
                    <span class="badge {{ $pengaduan->status_badge_class }}" style="font-size: 0.85rem; padding: 6px 16px;">
                        {{ $pengaduan->status_label }}
                    </span>
                    <div style="font-size: 0.75rem; color: #64748B; margin-top: 4px;">
                        Masuk: {{ $pengaduan->created_at->format('d/m/Y H:i') }} WIB
                    </div>
                </div>
            </div>

            <!-- Metadata Kategori & Lokasi -->
            <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; font-size: 0.82rem;">
                <div style="background: #E6EFFA; padding: 8px 14px; border-radius: 10px; border: 1px solid rgba(19, 88, 184, 0.15);">
                    <span style="color: #64748B;">Kategori:</span>
                    <strong style="color: #0B3F8C; margin-left: 4px;">{{ $pengaduan->kategori ? $pengaduan->kategori->nama_kategori : 'Umum' }}</strong>
                </div>

                @if($pengaduan->lokasi)
                    <div style="background: #E6EFFA; padding: 8px 14px; border-radius: 10px; border: 1px solid rgba(19, 88, 184, 0.15); display: flex; align-items: center; gap: 6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #1358B8;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span style="color: #64748B;">Lokasi:</span>
                        <strong style="color: #0B3F8C;">{{ $pengaduan->lokasi }}</strong>
                    </div>
                @endif
            </div>

            <!-- Isi Laporan -->
            <div style="margin-bottom: 24px;">
                <h3 class="{{ $pengaduan->status === 'ditolak' ? 'status-ditolak-title' : '' }}" style="font-size: 1.25rem; font-weight: 800; letter-spacing: -0.3px; margin-bottom: 12px; color: #0F172A;">
                    {{ $pengaduan->judul }}
                </h3>
                <div style="font-size: 0.92rem; color: #334155; line-height: 1.7; white-space: pre-line; background: #F8FAFC; padding: 20px; border-radius: 14px; border: 1px solid rgba(19, 88, 184, 0.12);">
                    {{ $pengaduan->isi_pengaduan }}
                </div>
            </div>

            <!-- Lampiran Foto Bukti -->
            @if($pengaduan->foto)
                <div style="margin-top: 20px;">
                    <h4 style="font-size: 0.88rem; font-weight: 700; color: #0F172A; margin-bottom: 10px;">Foto Bukti Kejadian:</h4>
                    <div style="max-width: 480px; overflow: hidden; border-radius: 16px; border: 1px solid rgba(19, 88, 184, 0.15); box-shadow: 0 4px 12px rgba(19, 88, 184, 0.06);">
                        <a href="{{ asset('storage/' . $pengaduan->foto) }}" target="_blank" title="Klik untuk memperbesar gambar">
                            <img src="{{ asset('storage/' . $pengaduan->foto) }}" alt="Lampiran Pengaduan" style="width: 100%; display: block; object-fit: cover;">
                        </a>
                    </div>
                    <span style="font-size: 0.72rem; color: #64748B; margin-top: 4px; display: block;">* Klik pada gambar untuk melihat resolusi penuh</span>
                </div>
            @endif
        </div>

        <!-- Thread Tanggapan & Form Kirim Tanggapan Baru -->
        <div class="form-card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid rgba(19, 88, 184, 0.12); padding-bottom: 14px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #1358B8; color: #fff; display: flex; align-items: center; justify-content: center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <div>
                        <h3 style="font-size: 1.05rem; font-weight: 700; color: #0F172A;">Riwayat Tanggapan Resmi</h3>
                        <span style="font-size: 0.75rem; color: #64748B;">Respon tertulis yang disampaikan kepada pelapor</span>
                    </div>
                </div>
                <span class="badge badge-menunggu">{{ $pengaduan->tanggapan->count() }} Respon</span>
            </div>

            <!-- List Tanggapan -->
            @if($pengaduan->tanggapan->isEmpty())
                <div style="padding: 24px; text-align: center; background: #F8FAFC; border-radius: 14px; border: 1px dashed rgba(19, 88, 184, 0.2); margin-bottom: 24px;">
                    <p style="font-size: 0.85rem; color: #64748B;">Belum ada tanggapan resmi yang diberikan untuk laporan ini.</p>
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: {{ $pengaduan->status !== 'selesai' ? '28px' : '0' }};">
                    @foreach($pengaduan->tanggapan as $tanggapan)
                        <div style="background: #F8FAFC; border: 1px solid rgba(19, 88, 184, 0.15); border-radius: 14px; padding: 18px;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 28px; height: 28px; border-radius: 50%; background: #1358B8; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700;">
                                        A
                                    </div>
                                    <span style="font-size: 0.85rem; font-weight: 700; color: #0B3F8C;">
                                        {{ $tanggapan->admin ? $tanggapan->admin->nama : 'Administrator LPM' }}
                                    </span>
                                </div>
                                <span style="font-size: 0.75rem; color: #64748B;">
                                    {{ $tanggapan->created_at->format('d M Y, H:i') }} WIB
                                </span>
                            </div>
                            <div style="font-size: 0.88rem; color: #0F172A; line-height: 1.6; white-space: pre-line;">
                                {{ $tanggapan->isi_tanggapan }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Form Tambah Tanggapan Baru (Hanya tampil jika pengaduan belum selesai) -->
            @if($pengaduan->status !== 'selesai')
                <form action="{{ route('admin.pengaduan.tanggapan', $pengaduan->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="isi_tanggapan">Tulis Tanggapan Resmi Baru</label>
                        <textarea 
                            id="isi_tanggapan" 
                            name="isi_tanggapan" 
                            class="form-textarea" 
                            rows="4" 
                            placeholder="Uraikan tindak lanjut, instruksi bagian terkait, atau hasil penyelesaian laporan secara resmi dan santun..." 
                            required
                        ></textarea>
                        @error('isi_tanggapan')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 14px; margin-top: 14px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <label for="ubah_status" style="font-size: 0.82rem; font-weight: 600; color: #64748B;">Perbarui Status Sekaligus:</label>
                            <select id="ubah_status" name="ubah_status" class="form-select" style="width: auto; padding: 6px 12px; font-size: 0.82rem;">
                                <option value="">-- Tetap ({{ $pengaduan->status_label }}) --</option>
                                <option value="diproses" {{ $pengaduan->status !== 'diproses' ? '' : 'disabled' }}>Ubah ke Diproses</option>
                                <option value="selesai" {{ $pengaduan->status !== 'selesai' ? '' : 'disabled' }}>Ubah ke Selesai</option>
                                <option value="ditolak" {{ $pengaduan->status !== 'ditolak' ? '' : 'disabled' }}>Ubah ke Ditolak</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-pill-primary" style="padding: 10px 24px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                            <span>Kirim Tanggapan</span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <!-- Kolom Kanan: Pengubah Status Cepat, Profil Pelapor, & Log Status -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <!-- Card Ubah Status Cepat (Hanya tampil jika pengaduan belum selesai) -->
        @if($pengaduan->status !== 'selesai')
            <div class="form-card" style="padding: 24px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; border-bottom: 1px solid rgba(19, 88, 184, 0.12); padding-bottom: 12px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #1358B8;"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    <h3 style="font-size: 1rem; font-weight: 700; color: #0F172A;">Perbarui Status Laporan</h3>
                </div>

                <form action="{{ route('admin.pengaduan.status', $pengaduan->id) }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="status">Pilih Status Baru</label>
                        <select id="status" name="status" class="form-select" required>
                            <option value="menunggu" {{ $pengaduan->status === 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="diproses" {{ $pengaduan->status === 'diproses' ? 'selected' : '' }}>Diproses (Tindak Lanjut)</option>
                            <option value="selesai" {{ $pengaduan->status === 'selesai' ? 'selected' : '' }}>Selesai (Tuntas)</option>
                            <option value="ditolak" {{ $pengaduan->status === 'ditolak' ? 'selected' : '' }}>Ditolak (Tidak Valid)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="catatan">Catatan / Alasan Perubahan (Opsional)</label>
                        <textarea 
                            id="catatan" 
                            name="catatan" 
                            class="form-textarea" 
                            rows="3" 
                            placeholder="Contoh: Petugas lapangan telah diterjunkan untuk perbaikan..."
                        ></textarea>
                        <span class="form-hint">Catatan ini otomatis dicatat sebagai tanggapan sistem.</span>
                    </div>

                    <button type="submit" class="btn-pill-primary" style="width: 100%; padding: 11px;">
                        Simpan Perubahan Status
                    </button>
                </form>
            </div>
        @endif

        <!-- Profil Data Pelapor -->
        <div class="form-card" style="padding: 24px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; border-bottom: 1px solid rgba(19, 88, 184, 0.12); padding-bottom: 12px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #1358B8;"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <h3 style="font-size: 1rem; font-weight: 700; color: #0F172A;">Identitas Pelapor</h3>
            </div>

            @if($pengaduan->user)
                <div style="display: flex; flex-direction: column; gap: 10px; font-size: 0.85rem;">
                    <div>
                        <div style="color: #64748B; font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Nama Pengguna</div>
                        <div style="font-weight: 700; color: #0B3F8C;">{{ $pengaduan->user->nama }}</div>
                    </div>
                    <div>
                        <div style="color: #64748B; font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Email</div>
                        <div style="color: #0F172A;">{{ $pengaduan->user->email }}</div>
                    </div>
                    <div>
                        <div style="color: #64748B; font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Nomor Handphone</div>
                        <div style="color: #0F172A;">{{ $pengaduan->user->no_hp ?: '-' }}</div>
                    </div>
                    <div>
                        <div style="color: #64748B; font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Alamat Domisili</div>
                        <div style="color: #0F172A;">{{ $pengaduan->user->alamat ?: '-' }}</div>
                    </div>
                </div>
            @else
                <p style="font-size: 0.82rem; color: #64748B;">Akun pelapor tidak ditemukan.</p>
            @endif
        </div>

        <!-- Log Status Timeline -->
        <div class="form-card" style="padding: 24px;">
            <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 4px; color: #0F172A;">Riwayat Audit Status</h3>
            <p style="font-size: 0.75rem; color: #64748B; margin-bottom: 20px;">Log perubahan status pengaduan</p>

            <div style="position: relative; padding-left: 20px; border-left: 2px solid #E6EFFA; display: flex; flex-direction: column; gap: 18px; margin-left: 6px;">
                @foreach($pengaduan->logStatus as $log)
                    <div style="position: relative;">
                        <!-- Titik Indikator Biru UMI -->
                        <div style="position: absolute; left: -27px; top: 2px; width: 12px; height: 12px; border-radius: 50%; background: #1358B8; border: 3px solid #fff; box-shadow: 0 0 0 1px #1358B8;"></div>
                        <div>
                            <div style="font-size: 0.82rem; font-weight: 700; text-transform: uppercase; color: #0B3F8C;">
                                {{ $log->status_baru }}
                            </div>
                            <div style="font-size: 0.72rem; color: #64748B;">
                                Diubah oleh: {{ $log->pengubah ? $log->pengubah->nama : 'Sistem' }}
                            </div>
                            <div style="font-size: 0.7rem; color: #64748B; margin-top: 2px;">
                                {{ $log->created_at->format('d M Y, H:i') }} WIB
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
