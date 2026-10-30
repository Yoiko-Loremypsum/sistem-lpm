@extends('layouts.app')

@section('title', 'Kelola Kategori Pengaduan')
@section('page-title', 'Kategori Pengaduan')
@section('page-subtitle', 'Kelola klasifikasi topik pengaduan untuk memudahkan penanganan')

@section('topbar-actions')
    <button type="button" onclick="openModal('modalTambahKategori')" class="btn-pill-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Tambah Kategori</span>
    </button>
@endsection

@section('content')
<div class="table-container">
    <table class="custom-table">
        <thead>
            <tr>
                <th style="width: 60px;">No</th>
                <th>Nama Kategori</th>
                <th>Deskripsi Lingkup Masalah</th>
                <th>Jumlah Laporan</th>
                <th>Tanggal Dibuat</th>
                <th style="text-align: right; width: 180px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kategoris as $index => $kategori)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong style="color: #0F172A; font-size: 0.92rem;">{{ $kategori->nama_kategori }}</strong>
                    </td>
                    <td>
                        <div style="font-size: 0.83rem; color: #64748B; max-width: 480px;">
                            {{ $kategori->deskripsi ?: '-' }}
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-menunggu" style="font-weight: 700;">
                            {{ $kategori->pengaduan_count }} Pengaduan
                        </span>
                    </td>
                    <td>
                        <span style="font-size: 0.78rem; color: #64748B;">
                            {{ $kategori->created_at ? $kategori->created_at->format('d/m/Y') : '-' }}
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <div style="display: inline-flex; align-items: center; justify-content: flex-end; gap: 8px;">
                            <button 
                                type="button" 
                                onclick="editKategori({{ $kategori->id }}, '{{ addslashes($kategori->nama_kategori) }}', '{{ addslashes($kategori->deskripsi ?? '') }}')" 
                                class="btn-pill-white" 
                                style="padding: 6px 14px; font-size: 0.78rem;"
                            >
                                Edit
                            </button>

                            <form action="{{ route('admin.kategori.destroy', $kategori->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ addslashes($kategori->nama_kategori) }}?')">
                                @csrf
                                @method('DELETE')
                                <button 
                                    type="submit" 
                                    class="btn-pill-white" 
                                    style="padding: 6px 14px; font-size: 0.78rem; color: #DC2626; border-color: #FECACA; {{ $kategori->pengaduan_count > 0 ? 'opacity: 0.5; cursor: not-allowed;' : '' }}"
                                    title="{{ $kategori->pengaduan_count > 0 ? 'Tidak dapat dihapus karena masih memiliki pengaduan terkait' : 'Hapus Kategori' }}"
                                    {{ $kategori->pengaduan_count > 0 ? 'disabled' : '' }}
                                >
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 36px; color: #64748B;">
                        Belum ada kategori pengaduan yang dibuat.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal Tambah Kategori -->
<div id="modalTambahKategori" class="modal-overlay">
    <div class="modal-box">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid rgba(19, 88, 184, 0.12); padding-bottom: 12px;">
            <h3 style="font-size: 1.1rem; font-weight: 700; color: #0F172A;">Tambah Kategori Baru</h3>
            <button type="button" onclick="closeModal('modalTambahKategori')" style="background:none; border:none; font-size:1.4rem; cursor:pointer; color:#64748B;" aria-label="Tutup modal">&times;</button>
        </div>

        <form action="{{ route('admin.kategori.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="nama_kategori">Nama Kategori <span style="color:#DC2626;">*</span></label>
                <input type="text" id="nama_kategori" name="nama_kategori" class="form-input" placeholder="Contoh: Infrastruktur Kampus & Lab" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="deskripsi">Deskripsi Cakupan</label>
                <textarea id="deskripsi" name="deskripsi" class="form-textarea" rows="3" placeholder="Jelaskan jenis-jenis masalah yang masuk dalam kategori ini..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                <button type="button" onclick="closeModal('modalTambahKategori')" class="btn-pill-white">Batalkan</button>
                <button type="submit" class="btn-pill-primary">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div id="modalEditKategori" class="modal-overlay">
    <div class="modal-box">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid rgba(19, 88, 184, 0.12); padding-bottom: 12px;">
            <h3 style="font-size: 1.1rem; font-weight: 700; color: #0F172A;">Ubah Data Kategori</h3>
            <button type="button" onclick="closeModal('modalEditKategori')" style="background:none; border:none; font-size:1.4rem; cursor:pointer; color:#64748B;" aria-label="Tutup modal">&times;</button>
        </div>

        <form id="formEditKategori" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label" for="edit_nama_kategori">Nama Kategori <span style="color:#DC2626;">*</span></label>
                <input type="text" id="edit_nama_kategori" name="nama_kategori" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="edit_deskripsi">Deskripsi Cakupan</label>
                <textarea id="edit_deskripsi" name="deskripsi" class="form-textarea" rows="3"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                <button type="button" onclick="closeModal('modalEditKategori')" class="btn-pill-white">Batalkan</button>
                <button type="submit" class="btn-pill-primary">Perbarui Kategori</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function editKategori(id, nama, deskripsi) {
        const form = document.getElementById('formEditKategori');
        form.action = `/admin/kategori/${id}`;
        document.getElementById('edit_nama_kategori').value = nama;
        document.getElementById('edit_deskripsi').value = deskripsi || '';
        openModal('modalEditKategori');
    }
</script>
@endpush
@endsection
