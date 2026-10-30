@extends('layouts.app')

@section('title', 'Daftar Pengaduan Saya')
@section('page-title', 'Riwayat Pengaduan Saya')
@section('page-subtitle', 'Semua laporan aspirasi dan keluhan yang telah Anda sampaikan')

@section('topbar-actions')
    <a href="{{ route('anggota.pengaduan.create') }}" class="btn-pill-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Ajukan Baru</span>
    </a>
@endsection

@section('content')
<!-- Filter & Pencarian Bar -->
<div class="glass-panel" style="padding: 18px 24px;">
    <form action="{{ route('anggota.pengaduan.index') }}" method="GET" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 14px;">
        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 12px; flex: 1; min-width: 280px;">
            <!-- Input Cari -->
            <div style="position: relative; flex: 1; min-width: 200px;">
                <input 
                    type="text" 
                    name="q" 
                    class="form-input" 
                    style="padding-left: 38px; padding-top: 9px; padding-bottom: 9px;" 
                    placeholder="Cari judul, tiket, atau lokasi..." 
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
        </div>

        <div style="display: flex; align-items: center; gap: 8px;">
            <button type="submit" class="btn-pill-primary" style="padding: 9px 18px; font-size: 0.85rem;">
                Filter
            </button>
            @if(request()->hasAny(['q', 'status', 'kategori_id']))
                <a href="{{ route('anggota.pengaduan.index') }}" class="btn-pill-white" style="padding: 9px 14px; font-size: 0.85rem;" title="Reset Filter">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Grid Pengaduan -->
@if($pengaduans->isEmpty())
    <div class="glass-panel" style="padding: 60px 24px; text-align: center;">
        <div style="width: 56px; height: 56px; border-radius: 50%; background: #E6EFFA; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px; color: #1358B8;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="8" x2="16" y1="12" y2="12"/></svg>
        </div>
        <h3 style="font-size: 1.05rem; font-weight: 700; color: #0F172A;">Tidak Ditemukan Pengaduan</h3>
        <p style="font-size: 0.83rem; color: #64748B; max-width: 420px; margin: 6px auto 20px auto;">
            Tidak ada laporan pengaduan yang sesuai dengan kriteria filter atau pencarian Anda.
        </p>
        <a href="{{ route('anggota.pengaduan.create') }}" class="btn-pill-primary">
            Buat Pengaduan Baru
        </a>
    </div>
@else
    <div class="complaints-grid">
        @foreach($pengaduans as $pengaduan)
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

                    <div style="font-size: 0.72rem; color: #1358B8; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; margin-bottom: 4px;">
                        {{ $pengaduan->kategori ? $pengaduan->kategori->nama_kategori : 'Umum' }}
                    </div>

                    <h3 class="complaint-title {{ $pengaduan->status === 'ditolak' ? 'status-ditolak-title' : '' }}">
                        {{ $pengaduan->judul }}
                    </h3>
                    <p class="complaint-excerpt">{{ $pengaduan->isi_pengaduan }}</p>

                    @if($pengaduan->lokasi)
                        <div style="display: flex; align-items: center; gap: 6px; font-size: 0.76rem; color: #64748B; margin-bottom: 12px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #1358B8;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $pengaduan->lokasi }}</span>
                        </div>
                    @endif
                </div>

                <div class="complaint-footer">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span>{{ $pengaduan->created_at->format('d M Y') }}</span>
                        @if($pengaduan->tanggapan->count() > 0)
                            <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.72rem; color: #1358B8; font-weight: 600;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                {{ $pengaduan->tanggapan->count() }} Respon
                            </span>
                        @endif
                    </div>
                    <span class="badge {{ $pengaduan->status_badge_class }}">
                        {{ $pengaduan->status_label }}
                    </span>
                </div>
            </a>
        @endforeach
    </div>

    <!-- Pagination -->
    <div style="margin-top: 24px; display: flex; justify-content: center;">
        {{ $pengaduans->links() }}
    </div>
@endif
@endsection
