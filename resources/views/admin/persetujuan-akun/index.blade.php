@extends('layouts.app')

@section('title', 'Persetujuan Akun')
@section('page-title', 'Persetujuan Akun')
@section('page-subtitle', 'Tinjau dan setujui akun anggota yang baru mendaftar')

@section('topbar-actions')
    @if($jumlahMenunggu > 0)
        <span style="background: #EF4444; color: white; font-size: 0.75rem; font-weight: 700;
                     padding: 4px 10px; border-radius: 999px;">
            {{ $jumlahMenunggu }} menunggu
        </span>
    @endif
@endsection

@section('content')
<div style="padding: 24px;">

    {{-- Tab Filter Status --}}
    <div style="display: flex; gap: 8px; margin-bottom: 24px; flex-wrap: wrap;">
        @foreach(['menunggu_verifikasi' => 'Menunggu', 'aktif' => 'Disetujui', 'ditolak' => 'Ditolak'] as $val => $label)
            <a href="{{ route('admin.persetujuan-akun.index', ['status' => $val]) }}"
               style="padding: 8px 18px; border-radius: 999px; font-size: 0.82rem; font-weight: 600;
                      text-decoration: none; transition: all 0.2s;
                      {{ $status === $val
                          ? 'background: #1358B8; color: white; box-shadow: 0 4px 12px rgba(19,88,184,0.3);'
                          : 'background: #F1F5F9; color: #64748B;' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Tabel --}}
    <div style="background: white; border-radius: 20px; border: 1px solid rgba(19,88,184,0.1);
                box-shadow: 0 4px 20px rgba(19,88,184,0.06); overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #E6EFFA;">
                        <th style="padding: 14px 18px; text-align: left; font-size: 0.78rem; font-weight: 700;
                                   color: #1358B8; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">Nama</th>
                        <th style="padding: 14px 18px; text-align: left; font-size: 0.78rem; font-weight: 700;
                                   color: #1358B8; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">Username (NIM/NIDN)</th>
                        <th style="padding: 14px 18px; text-align: left; font-size: 0.78rem; font-weight: 700;
                                   color: #1358B8; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">Jenis</th>
                        <th style="padding: 14px 18px; text-align: left; font-size: 0.78rem; font-weight: 700;
                                   color: #1358B8; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">Wajah</th>
                        <th style="padding: 14px 18px; text-align: left; font-size: 0.78rem; font-weight: 700;
                                   color: #1358B8; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">Tanggal Daftar</th>
                        <th style="padding: 14px 18px; text-align: center; font-size: 0.78rem; font-weight: 700;
                                   color: #1358B8; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($akun as $u)
                        <tr style="border-top: 1px solid #F1F5F9; transition: background 0.15s;"
                            onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='white'">
                            <td style="padding: 14px 18px;">
                                <div style="font-weight: 600; color: #0F172A; font-size: 0.88rem;">{{ $u->nama }}</div>
                                <div style="font-size: 0.75rem; color: #94A3B8;">{{ $u->email }}</div>
                            </td>
                            <td style="padding: 14px 18px;">
                                <span style="font-family: monospace; font-size: 0.88rem; color: #1358B8; font-weight: 600;">
                                    {{ $u->username }}
                                </span>
                            </td>
                            <td style="padding: 14px 18px;">
                                @if($u->anggotaResmi)
                                    <span style="background: {{ $u->anggotaResmi->jenis === 'dosen' ? '#FFF3E0' : '#E6EFFA' }};
                                                 color: {{ $u->anggotaResmi->jenis === 'dosen' ? '#E65100' : '#1358B8' }};
                                                 padding: 3px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">
                                        {{ $u->anggotaResmi->label_jenis }}
                                    </span>
                                @else
                                    <span style="color: #94A3B8; font-size: 0.82rem;">—</span>
                                @endif
                            </td>
                            <td style="padding: 14px 18px;">
                                @if($u->face_terdaftar)
                                    <span style="background: #DCFCE7; color: #15803D; padding: 3px 10px;
                                                 border-radius: 999px; font-size: 0.75rem; font-weight: 600;">✓ Terdaftar</span>
                                @else
                                    <span style="background: #FEF9C3; color: #854D0E; padding: 3px 10px;
                                                 border-radius: 999px; font-size: 0.75rem; font-weight: 600;">— OTP</span>
                                @endif
                            </td>
                            <td style="padding: 14px 18px; font-size: 0.82rem; color: #64748B; white-space: nowrap;">
                                {{ $u->created_at->format('d M Y, H:i') }}
                            </td>
                            <td style="padding: 14px 18px; text-align: center;">
                                @if($status === 'menunggu_verifikasi')
                                    <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                        {{-- Tombol Setujui --}}
                                        <form action="{{ route('admin.persetujuan-akun.setujui', $u->id) }}" method="POST"
                                              onsubmit="return confirm('Setujui akun {{ $u->nama }}?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    style="padding: 6px 14px; background: #16A34A; color: white; border: none;
                                                           border-radius: 8px; cursor: pointer; font-size: 0.78rem; font-weight: 600;">
                                                ✓ Setujui
                                            </button>
                                        </form>

                                        {{-- Tombol Tolak --}}
                                        <button onclick="bukaModalTolak({{ $u->id }}, '{{ addslashes($u->nama) }}')"
                                                style="padding: 6px 14px; background: #EF4444; color: white; border: none;
                                                       border-radius: 8px; cursor: pointer; font-size: 0.78rem; font-weight: 600;">
                                            ✕ Tolak
                                        </button>
                                    </div>
                                @else
                                    <span style="font-size: 0.78rem; color: #94A3B8;">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 40px; text-align: center; color: #94A3B8; font-size: 0.88rem;">
                                Tidak ada akun dengan status ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($akun->hasPages())
            <div style="padding: 16px 18px; border-top: 1px solid #F1F5F9;">
                {{ $akun->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal Tolak --}}
<div id="modalTolak" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999;
                             align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: white; border-radius: 20px; padding: 28px; max-width: 440px; width: 90%; margin: 0 auto;">
        <h3 style="font-size: 1rem; font-weight: 700; color: #0F172A; margin: 0 0 6px;">Tolak Akun</h3>
        <p id="modalTolakNama" style="font-size: 0.85rem; color: #64748B; margin: 0 0 16px;"></p>

        <form id="formTolak" method="POST">
            @csrf
            @method('PATCH')
            <div style="margin-bottom: 16px;">
                <label style="font-size: 0.82rem; font-weight: 600; color: #475569; display: block; margin-bottom: 6px;">
                    Alasan Penolakan (opsional)
                </label>
                <textarea name="alasan" rows="3" placeholder="Contoh: Data tidak sesuai, foto tidak jelas, dll."
                          style="width: 100%; padding: 10px 12px; border: 1.5px solid #E2E8F0; border-radius: 10px;
                                 font-size: 0.85rem; resize: vertical; outline: none; box-sizing: border-box;"></textarea>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="tutupModalTolak()"
                        style="flex: 1; padding: 10px; border: 1.5px solid #E2E8F0; background: white; color: #64748B;
                               border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 0.85rem;">
                    Batal
                </button>
                <button type="submit"
                        style="flex: 2; padding: 10px; background: #EF4444; color: white; border: none;
                               border-radius: 10px; cursor: pointer; font-weight: 700; font-size: 0.85rem;">
                    Ya, Tolak Akun Ini
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function bukaModalTolak(userId, nama) {
        document.getElementById('modalTolakNama').textContent = 'Menolak akun: ' + nama;
        document.getElementById('formTolak').action = '/admin/persetujuan-akun/' + userId + '/tolak';
        document.getElementById('modalTolak').style.display = 'flex';
    }

    function tutupModalTolak() {
        document.getElementById('modalTolak').style.display = 'none';
    }

    // Tutup modal saat klik luar
    document.getElementById('modalTolak').addEventListener('click', function(e) {
        if (e.target === this) tutupModalTolak();
    });
</script>
@endpush

@endsection
