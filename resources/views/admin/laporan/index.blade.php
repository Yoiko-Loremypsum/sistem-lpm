@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi')
@section('page-title', 'Laporan Rekapitulasi Pengaduan')
@section('page-subtitle', 'Filter dan ekspor laporan pengaduan berdasarkan rentang tanggal dan status (PDF/Excel)')

@section('content')
<!-- Filter Form Laporan -->
<div class="form-card" style="padding: 24px;">
    <form action="{{ route('admin.laporan.index') }}" method="GET">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; align-items: flex-end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="tanggal_mulai">Tanggal Mulai</label>
                <input 
                    type="date" 
                    id="tanggal_mulai" 
                    name="tanggal_mulai" 
                    class="form-input" 
                    value="{{ request('tanggal_mulai') }}"
                >
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="tanggal_selesai">Tanggal Selesai</label>
                <input 
                    type="date" 
                    id="tanggal_selesai" 
                    name="tanggal_selesai" 
                    class="form-input" 
                    value="{{ request('tanggal_selesai') }}"
                >
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="status">Status Pengaduan</label>
                <select id="status" name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="kategori_id">Kategori</label>
                <select id="kategori_id" name="kategori_id" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kategori)
                        <option value="{{ $kategori->id }}" {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                            {{ $kategori->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Filter & Reset Dibuat Sejajar dengan Form Control -->
            <div class="form-group" style="margin-bottom: 0;">
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn-pill-primary" style="flex: 1; padding: 12px 18px; font-size: 0.88rem;">
                        Filter Data
                    </button>
                    @if(request()->hasAny(['tanggal_mulai', 'tanggal_selesai', 'status', 'kategori_id']))
                        <a href="{{ route('admin.laporan.index') }}" class="btn-pill-white" style="padding: 12px 14px;" title="Reset Filter">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Ringkasan Statistik Hasil Filter Palet UMI -->
<div class="stats-grid">
    <!-- Kartu Biru UMI -->
    <div class="stat-card stat-card-blue" style="padding: 20px;">
        <span class="stat-label">Total Hasil Filter</span>
        <div class="stat-number" style="font-size: 2rem; margin-top: 6px;">{{ $summary['total'] }}</div>
    </div>
    <!-- Kartu Putih dengan Angka Biru Gelap -->
    <div class="stat-card stat-card-white" style="padding: 20px;">
        <span class="stat-label">Menunggu</span>
        <div class="stat-number" style="font-size: 2rem; margin-top: 6px;">{{ $summary['menunggu'] }}</div>
    </div>
    <div class="stat-card stat-card-white" style="padding: 20px;">
        <span class="stat-label">Diproses</span>
        <div class="stat-number" style="font-size: 2rem; margin-top: 6px;">{{ $summary['diproses'] }}</div>
    </div>
    <div class="stat-card stat-card-white" style="padding: 20px;">
        <span class="stat-label">Selesai</span>
        <div class="stat-number" style="font-size: 2rem; margin-top: 6px;">{{ $summary['selesai'] }}</div>
    </div>
    <div class="stat-card stat-card-white" style="padding: 20px;">
        <span class="stat-label">Ditolak</span>
        <div class="stat-number" style="font-size: 2rem; margin-top: 6px;">{{ $summary['ditolak'] }}</div>
    </div>
</div>

<!-- Tombol Ekspor & Tabel Preview -->
<div class="table-container">
    <div style="padding: 18px 24px; border-bottom: 1px solid rgba(19, 88, 184, 0.12); display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px;">
        <div>
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #0F172A;">Pratinjau Data Laporan ({{ $pengaduans->count() }} Data)</h3>
            <span style="font-size: 0.78rem; color: #64748B;">Data siap dicetak atau diekspor ke format dokumen</span>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            <!-- Tombol Cetak / PDF -->
            <a href="{{ route('admin.laporan.print', request()->query()) }}" target="_blank" class="btn-pill-white">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect width="12" height="8" x="6" y="14"></rect>
                </svg>
                <span>Cetak / PDF</span>
            </a>

            <!-- Tombol Ekspor Excel / CSV -->
            <a href="{{ route('admin.laporan.excel', request()->query()) }}" class="btn-pill-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                <span>Ekspor Excel (.CSV)</span>
            </a>
        </div>
    </div>

    <table class="custom-table">
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Kode Tiket</th>
                <th>Tanggal Masuk</th>
                <th>Nama Pelapor</th>
                <th>Kategori</th>
                <th>Judul Laporan</th>
                <th>Lokasi Kejadian</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengaduans as $index => $p)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong style="font-family: monospace; color: #1358B8;">{{ $p->kode_tiket }}</strong>
                    </td>
                    <td>
                        <span style="font-size: 0.8rem; color: #64748B;">{{ $p->created_at->format('d/m/Y H:i') }}</span>
                    </td>
                    <td>
                        <div style="font-weight: 600; color: #0F172A;">{{ $p->user ? $p->user->nama : '-' }}</div>
                        <div style="font-size: 0.72rem; color: #64748B;">{{ $p->user ? $p->user->email : '' }}</div>
                    </td>
                    <td>
                        <span style="font-size: 0.78rem; font-weight: 600; color: #0B3F8C; background: #E6EFFA; padding: 3px 8px; border-radius: 6px;">
                            {{ $p->kategori ? $p->kategori->nama_kategori : 'Umum' }}
                        </span>
                    </td>
                    <td>
                        <div class="complaint-title-text {{ $p->status === 'ditolak' ? 'status-ditolak-title' : '' }}" style="font-weight: 600; min-width: 200px; max-width: 320px; color: #0F172A;">
                            {{ $p->judul }}
                        </div>
                    </td>
                    <td>
                        <span style="font-size: 0.82rem; color: #475569;">{{ $p->lokasi ?: '-' }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $p->status_badge_class }}">
                            {{ $p->status_label }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 36px; color: #64748B;">
                        Tidak ada data pengaduan yang sesuai dengan kriteria filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
