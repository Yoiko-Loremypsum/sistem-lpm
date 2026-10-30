@extends('layouts.app')

@section('title', 'Kelola Seluruh Pengaduan')
@section('page-title', 'Kelola Pengaduan')
@section('page-subtitle', 'Pencarian, pemfilteran status, dan penindaklanjutan aspirasi civitas/masyarakat')

@section('topbar-actions')
    <a href="{{ route('admin.laporan.index') }}" class="btn-pill-white">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        <span>Cetak Laporan</span>
    </a>
@endsection

@section('content')
<!-- Filter & Pencarian Bar -->
<div class="glass-panel" style="padding: 20px 24px;">
    <form action="{{ route('admin.pengaduan.index') }}" method="GET" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 14px;">
        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 12px; flex: 1; min-width: 300px;">
            <!-- Input Cari -->
            <div style="position: relative; flex: 1; min-width: 220px;">
                <input 
                    type="text" 
                    name="q" 
                    class="form-input" 
                    style="padding-left: 38px; padding-top: 9px; padding-bottom: 9px;" 
                    placeholder="Cari tiket, judul, pelapor, atau lokasi..." 
                    value="{{ request('q') }}"
                >
                <div style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #64748B;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
            </div>

            <!-- Filter Status -->
            <select name="status" class="form-select" style="width: auto; padding-top: 9px; padding-bottom: 9px;">
                <option value="">Semua Status</option>
                <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Diproses</option>
                <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>

            <!-- Filter Kategori -->
            <select name="kategori_id" class="form-select" style="width: auto; padding-top: 9px; padding-bottom: 9px;">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $kategori)
                    <option value="{{ $kategori->id }}" {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                        {{ $kategori->nama_kategori }}
                    </option>
                @endforeach
            </select>

            <!-- Pengurutan -->
            <select name="sort" class="form-select" style="width: auto; padding-top: 9px; padding-bottom: 9px;">
                <option value="terbaru" {{ request('sort') === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                <option value="terlama" {{ request('sort') === 'terlama' ? 'selected' : '' }}>Terlama</option>
            </select>
        </div>

        <div style="display: flex; align-items: center; gap: 8px;">
            <button type="submit" class="btn-pill-primary" style="padding: 9px 20px; font-size: 0.85rem;">
                Terapkan Filter
            </button>
            @if(request()->hasAny(['q', 'status', 'kategori_id', 'sort']))
                <a href="{{ route('admin.pengaduan.index') }}" class="btn-pill-white" style="padding: 9px 14px; font-size: 0.85rem;" title="Reset Filter">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Tabel Pengaduan Lengkap -->
<div class="table-container">
    <table class="custom-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Tiket & Tanggal</th>
                <th>Pelapor</th>
                <th>Kategori</th>
                <th>Judul & Lokasi</th>
                <th>Status</th>
                <th>Respon</th>
                <th style="text-align: right; width: 120px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengaduans as $index => $pengaduan)
                <tr>
                    <td>{{ $pengaduans->firstItem() + $index }}</td>
                    <td>
                        <div style="font-family: monospace; font-weight: 700; color: #1358B8;">
                            {{ $pengaduan->kode_tiket }}
                        </div>
                        <div style="font-size: 0.72rem; color: #64748B;">
                            {{ $pengaduan->created_at->format('d/m/Y H:i') }} WIB
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 600; color: #0F172A;">{{ $pengaduan->user ? $pengaduan->user->nama : '-' }}</div>
                        <div style="font-size: 0.72rem; color: #64748B;">
                            {{ $pengaduan->user ? ($pengaduan->user->no_hp ?: $pengaduan->user->email) : '-' }}
                        </div>
                    </td>
                    <td>
                        <span style="font-size: 0.8rem; font-weight: 600; color: #0B3F8C; background: #E6EFFA; padding: 3px 8px; border-radius: 6px;">
                            {{ $pengaduan->kategori ? $pengaduan->kategori->nama_kategori : 'Umum' }}
                        </span>
                    </td>
                    <td>
                        <div class="complaint-title-text {{ $pengaduan->status === 'ditolak' ? 'status-ditolak-title' : '' }}" style="font-weight: 600; min-width: 200px; max-width: 280px; color: #0F172A;">
                            {{ $pengaduan->judul }}
                        </div>
                        @if($pengaduan->lokasi)
                            <div style="font-size: 0.72rem; color: #64748B; max-width: 280px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                📍 {{ $pengaduan->lokasi }}
                            </div>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $pengaduan->status_badge_class }}">
                            {{ $pengaduan->status_label }}
                        </span>
                    </td>
                    <td>
                        <span style="font-size: 0.8rem; font-weight: 600; color: #1358B8;">
                            {{ $pengaduan->tanggapan->count() }} tanggapan
                        </span>
                    </td>
                    <td style="text-align: right; white-space: nowrap;">
                        @if($pengaduan->status === 'selesai')
                            <a href="{{ route('admin.pengaduan.show', $pengaduan->id) }}" class="btn-pill-white" style="font-size: 0.78rem; padding: 6px 16px; border: 1.5px solid #1358B8; color: #1358B8; background: transparent;" title="Periksa detail pengaduan selesai">
                                Periksa
                            </a>
                        @else
                            <a href="{{ route('admin.pengaduan.show', $pengaduan->id) }}" class="btn-pill-primary" style="font-size: 0.78rem; padding: 6px 16px;" title="Tindak lanjuti & tanggapi pengaduan">
                                Tanggapi
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 48px 24px; color: #64748B;">
                        <div style="font-size: 1rem; font-weight: 700; color: #0F172A; margin-bottom: 4px;">Tidak Ada Data Pengaduan</div>
                        <p style="font-size: 0.83rem;">Silakan sesuaikan filter pencarian atau kata kunci yang Anda gunakan.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination Links -->
<div style="margin-top: 24px; display: flex; justify-content: center;">
    {{ $pengaduans->links() }}
</div>
@endsection
