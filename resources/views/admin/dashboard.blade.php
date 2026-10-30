@extends('layouts.app')

@section('title', 'Dashboard Administrator')
@section('page-title', 'Pusat Kendali Pengaduan')
@section('page-subtitle', 'Monitoring statistik pelayanan dan tren pengaduan FIKOM UMI')

@section('topbar-actions')
    <a href="{{ route('admin.laporan.index') }}" class="btn-pill-white">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        <span>Rekap Laporan</span>
    </a>
    <a href="{{ route('admin.pengaduan.index') }}" class="btn-pill-primary">
        <span>Semua Pengaduan</span>
    </a>
@endsection  

@section('content')
<!-- Grid Statistik Sesuai Spesifikasi UMI -->
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
        <div class="stat-number">{{ $totalPengaduan }}</div>
        <div class="stat-meta">Seluruh laporan masuk</div>
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
        <div class="stat-number">{{ $menunggu }}</div>
        <div class="stat-meta">Perlu respon & verifikasi</div>
    </div>

    <!-- Kartu Putih: Sedang Diproses -->
    <div class="stat-card stat-card-white">
        <div class="stat-header">
            <span class="stat-label">Diproses</span>
            <div class="stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="23 4 23 10 17 10"/>
                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                </svg>
            </div>
        </div>
        <div class="stat-number">{{ $diproses }}</div>
        <div class="stat-meta">Dalam penanganan teknis</div>
    </div>

    <!-- Kartu Putih: Selesai -->
    <div class="stat-card stat-card-white">
        <div class="stat-header">
            <span class="stat-label">Selesai</span>
            <div class="stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
        </div>
        <div class="stat-number">{{ $selesai }}</div>
        <div class="stat-meta">Penanganan tuntas</div>
    </div>

    <!-- Kartu Putih: Total Civitas / Warga -->
    <div class="stat-card stat-card-white">
        <div class="stat-header">
            <span class="stat-label">Total Civitas / Warga</span>
            <div class="stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                </svg>
            </div>
        </div>
        <div class="stat-number">{{ $totalAnggota }}</div>
        <div class="stat-meta">Akun pengguna terdaftar</div>
    </div>
</div>

<!-- Section Grafik Tren & Ring Progress -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
    <!-- Kartu Grafik Garis Biru UMI -->
    <div class="form-card" style="padding: 26px; grid-column: span 2;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 700; letter-spacing: -0.3px; color: #0F172A;">Tren Pengaduan Masuk</h3>
                <p style="font-size: 0.78rem; color: #64748B;">Grafik volume laporan pengaduan 6 bulan terakhir</p>
            </div>
            <div style="font-size: 0.75rem; background: #E6EFFA; color: #1358B8; padding: 4px 12px; border-radius: 9999px; font-weight: 700; border: 1px solid rgba(19, 88, 184, 0.15);">
                Periode Terkini
            </div>
        </div>
        <div style="height: 250px; position: relative;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- Kartu Progres Ring UMI: Biru #1358B8 di atas track #E6EFFA -->
    <div class="donut-card" style="flex-direction: column; text-align: center; justify-content: center; padding: 28px;">
        <div class="donut-circle" style="width: 120px; height: 120px; margin: 0 auto;">
            <svg viewBox="0 0 36 36" style="width: 100%; height: 100%; transform: rotate(-90deg);">
                <!-- Track Ring Progres: #E6EFFA -->
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
                    stroke-dasharray="{{ $persentaseSelesai }}, 100"
                    stroke-linecap="round"
                />
            </svg>
            <div class="donut-center-text">
                {{ $persentaseSelesai }}%
                <span class="donut-center-sub">RESOLUSI</span>
            </div>
        </div>
        <div style="margin-top: 14px;">
            <h4 style="font-size: 0.95rem; font-weight: 700; color: #0F172A;">Tingkat Penyelesaian</h4>
            <p style="font-size: 0.78rem; color: #64748B; margin-top: 4px;">
                {{ $selesai }} dari {{ $totalPengaduan }} laporan berhasil diselesaikan. Ditolak: {{ $ditolak }} laporan.
            </p>
        </div>
    </div>
</div>

<!-- Tabel Pengaduan Membutuhkan Tindakan -->
<div class="table-container">
    <div style="padding: 20px 24px; border-bottom: 1px solid rgba(19, 88, 184, 0.12); display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #0F172A;">Pengaduan Perlu Ditindaklanjuti</h3>
            <p style="font-size: 0.78rem; color: #64748B;">Daftar laporan masyarakat yang baru masuk atau sedang diproses</p>
        </div>
        <a href="{{ route('admin.pengaduan.index') }}" class="btn-pill-white" style="font-size: 0.8rem; padding: 7px 16px;">
            Buka Semua Pengaduan
        </a>
    </div>

    <table class="custom-table">
        <thead>
            <tr>
                <th>Tiket & Tanggal</th>
                <th>Nama Pelapor</th>
                <th>Kategori</th>
                <th>Judul Laporan</th>
                <th>Status</th>
                <th style="text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengaduanTerbaru as $p)
                <tr>
                    <td>
                        <div style="font-family: monospace; font-weight: 700; color: #1358B8;">{{ $p->kode_tiket }}</div>
                        <div style="font-size: 0.72rem; color: #64748B;">{{ $p->created_at->format('d/m/Y H:i') }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600; color: #0F172A;">{{ $p->user ? $p->user->nama : 'Pengguna' }}</div>
                        <div style="font-size: 0.72rem; color: #64748B;">{{ $p->user ? $p->user->email : '-' }}</div>
                    </td>
                    <td>
                        <span style="font-size: 0.78rem; font-weight: 600; color: #0B3F8C; background: #E6EFFA; padding: 3px 8px; border-radius: 6px;">
                            {{ $p->kategori ? $p->kategori->nama_kategori : 'Umum' }}
                        </span>
                    </td>
                    <td>
                        <div class="complaint-title-text {{ $p->status === 'ditolak' ? 'status-ditolak-title' : '' }}" style="font-weight: 600; min-width: 200px; max-width: 340px; color: #0F172A;">
                            {{ $p->judul }}
                        </div>
                        @if($p->lokasi)
                            <div style="font-size: 0.72rem; color: #64748B;">📍 {{ $p->lokasi }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $p->status_badge_class }}">
                            {{ $p->status_label }}
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('admin.pengaduan.show', $p->id) }}" class="btn-pill-primary" style="font-size: 0.78rem; padding: 6px 14px;">
                            Tanggapi
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 36px; color: #64748B;">
                        Belum ada laporan pengaduan masuk.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('trendChart');
        if (!ctx) return;

//Error merah itu bukan error dari Laravel atau PHP. 
// Itu hanya diagnosis dari editor VS Code yang salah membaca kodenya.
        const labels = @json($chartLabels);
        const data = @json($chartData);

        // Cari indeks nilai tertinggi untuk highlight lingkaran putih dengan tepi biru #1358B8
        const maxVal = Math.max(...data);
        const pointRadius = data.map(v => (v === maxVal && maxVal > 0) ? 6 : 4);
        const pointBg = data.map(v => (v === maxVal && maxVal > 0) ? '#FFFFFF' : '#1358B8');
        const pointBorderColor = data.map(v => (v === maxVal && maxVal > 0) ? '#1358B8' : '#FFFFFF');
        const pointBorderWidth = data.map(v => (v === maxVal && maxVal > 0) ? 3 : 1.5);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pengaduan',
                    data: data,
                    borderColor: '#1358B8', // Garis grafik biru #1358B8
                    borderWidth: 2.5,
                    backgroundColor: 'rgba(19, 88, 184, 0.08)',
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: pointBg, // Titik tertinggi lingkaran putih
                    pointBorderColor: pointBorderColor, // Dengan tepi biru
                    pointBorderWidth: pointBorderWidth,
                    pointRadius: pointRadius,
                    pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0B3F8C',
                        titleColor: '#FFFFFF',
                        bodyColor: '#E6EFFA',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' Laporan Masuk';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748B', font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#E6EFFA' },
                        ticks: {
                            stepSize: 1,
                            color: '#64748B',
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
