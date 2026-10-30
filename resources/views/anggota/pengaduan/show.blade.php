@extends('layouts.app')

@section('title', 'Detail Pengaduan ' . $pengaduan->kode_tiket)
@section('page-title', 'Detail Pengaduan')
@section('page-subtitle', 'Informasi lengkap laporan pengaduan dan tanggapan resmi administrator')

@section('topbar-actions')
    <a href="{{ route('anggota.pengaduan.index') }}" class="btn-pill-white">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        <span>Kembali</span>
    </a>
@endsection

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
    <!-- Kolom Kiri: Detail Laporan & Tanggapan Admin -->
    <div style="display: flex; flex-direction: column; gap: 24px; grid-column: span 2;">
        <!-- Kartu Detail Pengaduan -->
        <div class="form-card">
            <!-- Header Tiket & Status -->
            <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(19, 88, 184, 0.12); padding-bottom: 18px; margin-bottom: 20px;">
                <div>
                    <span style="font-size: 0.72rem; text-transform: uppercase; color: #64748B; font-weight: 700; letter-spacing: 0.6px;">NOMOR TIKET RESMI</span>
                    <h2 style="font-size: 1.35rem; font-family: monospace; font-weight: 800; color: #1358B8; margin-top: 2px;">
                        {{ $pengaduan->kode_tiket }}
                    </h2>
                </div>
                <div style="text-align: right;">
                    <span class="badge {{ $pengaduan->status_badge_class }}" style="font-size: 0.85rem; padding: 6px 16px;">
                        {{ $pengaduan->status_label }}
                    </span>
                    <div style="font-size: 0.75rem; color: #64748B; margin-top: 4px;">
                        Diajukan pada {{ $pengaduan->created_at->format('d F Y, H:i') }} WIB
                    </div>
                </div>
            </div>

            <!-- Metadata Singkat -->
            <div style="display: flex; flex-wrap: wrap; gap: 16px; margin-bottom: 20px; font-size: 0.82rem;">
                <div style="background: #E6EFFA; padding: 8px 14px; border-radius: 10px; border: 1px solid rgba(19, 88, 184, 0.15);">
                    <span style="color: #64748B;">Kategori:</span>
                    <strong style="color: #0B3F8C; margin-left: 4px;">{{ $pengaduan->kategori ? $pengaduan->kategori->nama_kategori : '-' }}</strong>
                </div>

                @if($pengaduan->lokasi)
                    <div style="background: #E6EFFA; padding: 8px 14px; border-radius: 10px; border: 1px solid rgba(19, 88, 184, 0.15); display: flex; align-items: center; gap: 6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #1358B8;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span style="color: #64748B;">Lokasi:</span>
                        <strong style="color: #0B3F8C;">{{ $pengaduan->lokasi }}</strong>
                    </div>
                @endif
            </div>

            <!-- Judul & Isi Laporan -->
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
                    <h4 style="font-size: 0.88rem; font-weight: 700; color: #0F172A; margin-bottom: 10px;">Foto Lampiran Bukti:</h4>
                    <div style="max-width: 480px; overflow: hidden; border-radius: 16px; border: 1px solid rgba(19, 88, 184, 0.15); box-shadow: 0 4px 12px rgba(19, 88, 184, 0.06);">
                        <a href="{{ asset('storage/' . $pengaduan->foto) }}" target="_blank" title="Klik untuk memperbesar gambar">
                            <img src="{{ asset('storage/' . $pengaduan->foto) }}" alt="Lampiran Pengaduan" style="width: 100%; display: block; object-fit: cover;">
                        </a>
                    </div>
                    <span style="font-size: 0.72rem; color: #64748B; margin-top: 4px; display: block;">* Klik pada gambar untuk melihat ukuran penuh</span>
                </div>
            @endif
        </div>

        <!-- Tanggapan Resmi Administrator -->
        <div class="form-card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid rgba(19, 88, 184, 0.12); padding-bottom: 14px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #1358B8; color: #fff; display: flex; align-items: center; justify-content: center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <div>
                        <h3 style="font-size: 1.05rem; font-weight: 700; color: #0F172A;">Tanggapan Resmi Administrator</h3>
                        <span style="font-size: 0.75rem; color: #64748B;">Respon langsung dari pihak berwenang kampus</span>
                    </div>
                </div>
                <span class="badge badge-menunggu" style="font-size: 0.75rem;">{{ $pengaduan->tanggapan->count() }} Tanggapan</span>
            </div>

            @if($pengaduan->tanggapan->isEmpty())
                <div style="padding: 30px; text-align: center; background: #F8FAFC; border-radius: 14px; border: 1px dashed rgba(19, 88, 184, 0.2);">
                    <p style="font-size: 0.85rem; color: #64748B;">
                        Belum ada tanggapan resmi dari administrator untuk pengaduan ini. Laporan Anda sedang dalam antrean verifikasi petugas.
                    </p>
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    @foreach($pengaduan->tanggapan as $tanggapan)
                        <div style="background: #F8FAFC; border: 1px solid rgba(19, 88, 184, 0.15); border-radius: 14px; padding: 18px;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 28px; height: 28px; border-radius: 50%; background: #1358B8; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700;">
                                        A
                                    </div>
                                    <div>
                                        <div style="font-size: 0.85rem; font-weight: 700; color: #0B3F8C;">
                                            {{ $tanggapan->admin ? $tanggapan->admin->nama : 'Administrator LPM' }}
                                        </div>
                                        <div style="font-size: 0.7rem; color: #64748B;">Petugas Layanan Pengaduan UMI</div>
                                    </div>
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
        </div>
    </div>

    <!-- Kolom Kanan: Timeline Riwayat Status -->
    <div>
        <div class="form-card" style="padding: 24px; position: sticky; top: 90px;">
            <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 4px; color: #0F172A;">Riwayat Status Laporan</h3>
            <p style="font-size: 0.75rem; color: #64748B; margin-bottom: 20px;">Jejak audit transparan perubahan status</p>

            <div style="position: relative; padding-left: 20px; border-left: 2px solid #E6EFFA; display: flex; flex-direction: column; gap: 20px; margin-left: 8px;">
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
