@extends('layouts.app')

@section('title', 'Dashboard Pengguna')
@section('page-title', 'Dashboard Aspirasi Pengguna')
@section('page-subtitle', 'Pantau status penanganan laporan pengaduan Anda secara transparan dan berkala')

@section('topbar-actions')
    <a href="{{ route('anggota.pengaduan.create') }}" class="btn-pill-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Ajukan Pengaduan</span>
    </a>
@endsection

@section('content')
<!-- Kartu Statistik Palet UMI -->
<div class="stats-grid">
    <!-- Satu Kartu Biru UMI #1358B8 dengan Angka Putih -->
    <div class="stat-card stat-card-blue">
        <div class="stat-header">
            <span class="stat-label">Total Pengaduan</span>
            <div class="stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
        </div>
        <div class="stat-number">{{ $stats['total'] }}</div>
        <div class="stat-meta">Laporan telah Anda sampaikan</div>
    </div>

    <!-- Kartu Putih dengan Teks Angka Biru Gelap: Menunggu Verifikasi -->
    <div class="stat-card stat-card-white">
        <div class="stat-header">
            <span class="stat-label">Menunggu</span>
            <div class="stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
        </div>
        <div class="stat-number">{{ $stats['menunggu'] }}</div>
        <div class="stat-meta">Menunggu peninjauan admin</div>
    </div>

    <!-- Kartu Putih: Sedang Diproses -->
    <div class="stat-card stat-card-white">
        <div class="stat-header">
            <span class="stat-label">Sedang Diproses</span>
            <div class="stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="23 4 23 10 17 10"/>
                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                </svg>
            </div>
        </div>
        <div class="stat-number">{{ $stats['diproses'] }}</div>
        <div class="stat-meta">Dalam penanganan teknis</div>
    </div>

    <!-- Kartu Putih: Selesai Ditangani -->
    <div class="stat-card stat-card-white">
        <div class="stat-header">
            <span class="stat-label">Tuntas Selesai</span>
            <div class="stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
        </div>
        <div class="stat-number">{{ $stats['selesai'] }}</div>
        <div class="stat-meta">Telah diselesaikan oleh tim</div>
    </div>
</div>

<!-- Section Tengah: Ring Progress UMI & Tombol Tambah Pengaduan Putus-Putus -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
    <!-- Kartu Progres Ring UMI: Biru #1358B8 di atas track #E6EFFA -->
    <div class="donut-card">
        <div class="donut-circle">
            <svg viewBox="0 0 36 36" style="width: 100%; height: 100%; transform: rotate(-90deg);">
                <!-- Track Lingkaran: #E6EFFA -->
                <path
                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                    fill="none"
                    stroke="#E6EFFA"
                    stroke-width="3.5"
                />
                <!-- Lingkaran Progres: #1358B8 -->
                <path
                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                    fill="none"
                    stroke="#1358B8"
                    stroke-width="3.8"
                    stroke-dasharray="{{ $stats['persentase_selesai'] }}, 100"
                    stroke-linecap="round"
                />
            </svg>
            <div class="donut-center-text">
                {{ $stats['persentase_selesai'] }}%
                <span class="donut-center-sub">TUNTAS</span>
            </div>
        </div>
        <div>
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #0F172A; margin-bottom: 6px;">Tingkat Penyelesaian</h3>
            <p style="font-size: 0.83rem; color: #64748B; line-height: 1.45;">
                {{ $stats['selesai'] }} dari total {{ $stats['total'] }} laporan pengaduan Anda telah berhasil ditindaklanjuti dan diselesaikan oleh administrator.
            </p>
        </div>
    </div>

    <!-- Tombol "Tambah Pengaduan": Border Putus-putus Biru & Ikon Plus Biru -->
    <a href="{{ route('anggota.pengaduan.create') }}" class="add-complaint-card">
        <div class="add-icon-circle">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
        </div>
        <div>
            <div style="font-size: 1.05rem; font-weight: 700; color: #1358B8;">Tambah Pengaduan Baru</div>
            <div style="font-size: 0.8rem; color: #64748B; margin-top: 4px;">Klik untuk menyampaikan laporan fasilitas, layanan, atau keluhan</div>
        </div>
    </a>
</div>

<!-- Daftar Pengaduan Terkini -->
<div style="margin-top: 4px;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px;">
        <div>
            <h2 style="font-size: 1.15rem; font-weight: 700; letter-spacing: -0.3px; color: #0F172A;">Laporan Pengaduan Terkini</h2>
            <p style="font-size: 0.8rem; color: #64748B;">Daftar 5 laporan terakhir yang Anda kirimkan</p>
        </div>
        <a href="{{ route('anggota.pengaduan.index') }}" class="btn-pill-white" style="font-size: 0.8rem; padding: 7px 16px;">
            Lihat Semua ({{ $stats['total'] }})
        </a>
    </div>

    @if($pengaduanTerbaru->isEmpty())
        <div class="glass-panel" style="padding: 48px 24px; text-align: center;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: #E6EFFA; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px; color: #1358B8;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <h3 style="font-size: 1rem; font-weight: 700; color: #0F172A;">Belum Ada Pengaduan</h3>
            <p style="font-size: 0.83rem; color: #64748B; max-width: 420px; margin: 6px auto 20px auto;">
                Anda belum pernah mengajukan pengaduan. Sampaikan keluhan fasilitas atau layanan kampus Anda sekarang.
            </p>
            <a href="{{ route('anggota.pengaduan.create') }}" class="btn-pill-primary">
                Buat Laporan Pertama
            </a>
        </div>
    @else
        <div class="complaints-grid">
            @foreach($pengaduanTerbaru as $pengaduan)
                <a href="{{ route('anggota.pengaduan.show', $pengaduan->id) }}" class="complaint-card">
                    <div>
                        <div class="complaint-card-top">
                            <div class="complaint-icon-badge">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="12"/>
                                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                            </div>
                            <span class="complaint-ticket">{{ $pengaduan->kode_tiket }}</span>
                        </div>

                        <h3 class="complaint-title {{ $pengaduan->status === 'ditolak' ? 'status-ditolak-title' : '' }}">
                            {{ $pengaduan->judul }}
                        </h3>
                        <p class="complaint-excerpt">{{ $pengaduan->isi_pengaduan }}</p>
                    </div>

                    <div class="complaint-footer">
                        <span>{{ $pengaduan->created_at->format('d M Y') }}</span>
                        <span class="badge {{ $pengaduan->status_badge_class }}">
                            {{ $pengaduan->status_label }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
