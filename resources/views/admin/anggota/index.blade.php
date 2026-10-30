@extends('layouts.app')

@section('title', 'Kelola Data Pengguna')
@section('page-title', 'Data Pengguna & Civitas')
@section('page-subtitle', 'Manajemen akun pengguna masyarakat/civitas dan kontrol status aktif')

@section('content')
<!-- Filter & Pencarian Bar -->
<div class="glass-panel" style="padding: 18px 24px;">
    <form action="{{ route('admin.anggota.index') }}" method="GET" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 14px;">
        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 12px; flex: 1; min-width: 280px;">
            <div style="position: relative; flex: 1; min-width: 220px;">
                <input 
                    type="text" 
                    name="q" 
                    class="form-input" 
                    style="padding-left: 38px; padding-top: 9px; padding-bottom: 9px;" 
                    placeholder="Cari nama, email, nomor HP, atau alamat..." 
                    value="{{ request('q') }}"
                >
                <div style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #64748B;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
            </div>

            <select name="status" class="form-select" style="width: auto; padding-top: 9px; padding-bottom: 9px;">
                <option value="">Semua Status Akun</option>
                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        <div style="display: flex; align-items: center; gap: 8px;">
            <button type="submit" class="btn-pill-primary" style="padding: 9px 20px; font-size: 0.85rem;">
                Cari Data
            </button>
            @if(request()->hasAny(['q', 'status']))
                <a href="{{ route('admin.anggota.index') }}" class="btn-pill-white" style="padding: 9px 14px; font-size: 0.85rem;">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Tabel Anggota -->
<div class="table-container">
    <table class="custom-table">
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Nama Pengguna</th>
                <th>Kontak (Email / No. HP)</th>
                <th>Alamat Domisili</th>
                <th>Jumlah Pengaduan</th>
                <th>Status Akun</th>
                <th>Terdaftar Pada</th>
                <th style="text-align: right; width: 150px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($anggotas as $index => $anggota)
                <tr>
                    <td>{{ $anggotas->firstItem() + $index }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="user-avatar" style="width: 34px; height: 34px; font-size: 0.8rem; background: #1358B8;">
                                {{ strtoupper(substr($anggota->nama, 0, 1)) }}
                            </div>
                            <strong style="color: #0F172A; font-size: 0.9rem;">{{ $anggota->nama }}</strong>
                        </div>
                    </td>
                    <td>
                        <div style="font-size: 0.83rem; font-weight: 600; color: #0F172A;">{{ $anggota->email }}</div>
                        <div style="font-size: 0.72rem; color: #64748B;">{{ $anggota->no_hp ?: 'Belum ada No. HP' }}</div>
                    </td>
                    <td>
                        <div style="font-size: 0.82rem; color: #475569; max-width: 260px;">
                            {{ $anggota->alamat ?: '-' }}
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-menunggu" style="font-weight: 700;">
                            {{ $anggota->pengaduan_count }} Laporan
                        </span>
                    </td>
                    <td>
                        @if($anggota->status_aktif)
                            <span class="badge badge-selesai" style="font-size: 0.75rem;">Aktif</span>
                        @else
                            <span class="badge badge-ditolak" style="font-size: 0.75rem; text-decoration: none;">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-size: 0.78rem; color: #64748B;">
                            {{ $anggota->created_at ? $anggota->created_at->format('d/m/Y') : '-' }}
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <form action="{{ route('admin.anggota.toggle', $anggota->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin {{ $anggota->status_aktif ? 'menonaktifkan' : 'mengaktifkan kembali' }} akun {{ addslashes($anggota->nama) }}?')">
                            @csrf
                            @if($anggota->status_aktif)
                                <button type="submit" class="btn-pill-white" style="font-size: 0.75rem; padding: 6px 14px; color: #DC2626; border-color: #FECACA;">
                                    Nonaktifkan
                                </button>
                            @else
                                <button type="submit" class="btn-pill-primary" style="font-size: 0.75rem; padding: 6px 14px;">
                                    Aktifkan
                                </button>
                            @endif
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: #64748B;">
                        Tidak ada data pengguna yang ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination Links -->
<div style="margin-top: 24px; display: flex; justify-content: center;">
    {{ $anggotas->links() }}
</div>
@endsection
