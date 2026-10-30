@extends('layouts.auth')

@section('title', 'Verifikasi Wajah')
@section('header-title', 'Verifikasi Identitas')
@section('header-subtitle', 'Langkah keamanan kedua: konfirmasi identitas dengan wajah Anda')

@section('content')

{{-- Info Privasi & Browser AI --}}
<div style="background: #E6EFFA; border-radius: 12px; padding: 14px 16px; margin-bottom: 20px; border-left: 4px solid #1358B8;">
    <p style="margin: 0; font-size: 0.82rem; color: #0B3F8C; line-height: 1.5;">
        🔒 Hadapkan wajah Anda ke kamera untuk menyelesaikan verifikasi.
        Ekstraksi wajah diproses secara lokal di browser Anda. Foto tidak dikirim ke server.
    </p>
</div>

{{-- Area Kamera --}}
<div id="kameraArea">
    <div style="position: relative; width: 100%; aspect-ratio: 4/3; background: #0F172A; border-radius: 16px;
                overflow: hidden; margin-bottom: 14px; border: 2px solid rgba(19,88,184,0.3);">
        <video id="kameraVideo" autoplay muted playsinline
               style="width: 100%; height: 100%; object-fit: cover; display: block; transform: scaleX(-1);"></video>
        {{-- Panduan oval wajah --}}
        <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; pointer-events: none;">
            <div id="ovalGuide" style="width: 170px; height: 210px; border: 3px solid rgba(19,88,184,0.8);
                        border-radius: 50% / 45%; transition: border-color 0.3s;
                        box-shadow: 0 0 0 2000px rgba(0,0,0,0.3);"></div>
        </div>
        <div id="statusKamera" style="position: absolute; bottom: 12px; left: 0; right: 0; text-align: center;">
            <span style="background: rgba(0,0,0,0.75); color: white; font-size: 0.78rem;
                         padding: 5px 14px; border-radius: 999px; backdrop-filter: blur(4px);">
                Menginisialisasi kamera &amp; modul AI...
            </span>
        </div>
    </div>

    <p style="font-size: 0.8rem; color: #64748B; text-align: center; margin-bottom: 14px; line-height: 1.5;">
        Posisikan wajah Anda tepat di dalam bingkai oval. Tatap kamera dan pastikan pencahayaan cukup.
    </p>

    <button id="btnVerifikasi" onclick="verifikasiWajah()"
            class="btn-pill-primary"
            style="width: 100%; padding: 13px; font-size: 0.95rem; display: none;">
        🔍 Verifikasi Wajah Saya
    </button>
</div>

{{-- Pemisah --}}
<div style="display: flex; align-items: center; gap: 12px; margin: 20px 0;">
    <div style="flex: 1; height: 1px; background: #E2E8F0;"></div>
    <span style="font-size: 0.78rem; color: #94A3B8;">atau</span>
    <div style="flex: 1; height: 1px; background: #E2E8F0;"></div>
</div>

{{-- OTP Cadangan --}}
<div style="border: 1.5px solid #E2E8F0; border-radius: 14px; padding: 16px;">
    <p style="font-size: 0.82rem; font-weight: 600; color: #475569; margin: 0 0 6px;">
        📧 Verifikasi via OTP
    </p>
    <p style="font-size: 0.78rem; color: #94A3B8; margin: 0 0 12px; line-height: 1.5;">
        Kirim kode OTP ke email institusi: <strong style="color: #475569;">{{ $emailSensor }}</strong>
    </p>

    <button id="btnKirimOtp" onclick="kirimOtp()"
            style="width: 100%; padding: 10px; border: 1.5px solid #1358B8; background: #E6EFFA;
                   color: #1358B8; border-radius: 12px; cursor: pointer; font-weight: 600; font-size: 0.85rem;">
        Kirim Kode OTP
    </button>

    <div id="otpInputArea" style="display: none; margin-top: 12px;">
        <div style="display: flex; gap: 8px; align-items: flex-end;">
            <div style="flex: 1;">
                <label style="font-size: 0.78rem; color: #64748B; display: block; margin-bottom: 4px;">Kode OTP (6 digit)</label>
                <input type="text" id="inputOtp" maxlength="6" placeholder="123456"
                       style="width: 100%; padding: 11px 14px; border: 1.5px solid #CBD5E1; border-radius: 10px;
                              font-size: 1.1rem; letter-spacing: 4px; text-align: center; outline: none; box-sizing: border-box;">
            </div>
            <button onclick="verifikasiOtp()"
                    style="padding: 11px 16px; background: #1358B8; color: white; border: none;
                           border-radius: 10px; cursor: pointer; font-weight: 700;">
                Kirim
            </button>
        </div>
        <p id="pesanOtp" style="font-size: 0.78rem; margin-top: 6px; color: #64748B;"></p>
    </div>
</div>

{{-- Pesan Global --}}
<div id="pesanGlobal" style="display: none; margin-top: 16px; padding: 12px 16px; border-radius: 10px; font-size: 0.85rem; font-weight: 500;"></div>

{{-- Tombol kembali ke login --}}
<div style="margin-top: 20px; text-align: center;">
    <a href="{{ route('login') }}" style="font-size: 0.82rem; color: #94A3B8; text-decoration: none;">
        &larr; Kembali ke Login
    </a>
</div>

@push('scripts')
{{-- face-api.js v0.22.2 dari CDN resmi --}}
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script src="{{ asset('js/face-service.js') }}"></script>
<script>
    let cameraStream = null;

    const video        = document.getElementById('kameraVideo');
    const btnVerif     = document.getElementById('btnVerifikasi');
    const statusKamera = document.getElementById('statusKamera');
    const ovalGuide    = document.getElementById('ovalGuide');
    const pesanGlobal  = document.getElementById('pesanGlobal');

    async function initKamera() {
        if (!LpmFaceService.isSecureOrigin()) {
            statusKamera.innerHTML = '<span style="background:rgba(239,68,68,0.9);color:white;font-size:0.78rem;padding:5px 14px;border-radius:999px;">Akses Tidak Aman</span>';
            tampilPesan('Akses kamera hanya diperbolehkan melalui koneksi aman HTTPS atau localhost. Gunakan opsi OTP cadangan di bawah.', 'error');
            return;
        }

        try {
            statusKamera.innerHTML = '<span style="background:rgba(19,88,184,0.85);color:white;font-size:0.78rem;padding:5px 14px;border-radius:999px;">Mengaktifkan kamera...</span>';
            cameraStream = await LpmFaceService.startCamera(video);

            statusKamera.innerHTML = '<span style="background:rgba(19,88,184,0.85);color:white;font-size:0.78rem;padding:5px 14px;border-radius:999px;">Memuat modul AI pengenal wajah...</span>';
            await LpmFaceService.loadModels('/models');

            statusKamera.innerHTML = '<span style="background:rgba(22,163,74,0.85);color:white;font-size:0.78rem;padding:5px 14px;border-radius:999px;">Kamera &amp; AI Siap</span>';
            btnVerif.style.display = 'block';
        } catch (err) {
            console.error('Kamera / AI error:', err);
            statusKamera.innerHTML = '<span style="background:rgba(239,68,68,0.85);color:white;font-size:0.78rem;padding:5px 14px;border-radius:999px;">Kamera / AI Tidak Tersedia</span>';
            tampilPesan(err.message || 'Akses kamera tidak diizinkan atau modul AI gagal dimuat. Gunakan opsi OTP di bawah.', 'error');
        }
    }

    async function verifikasiWajah() {
        btnVerif.disabled    = true;
        btnVerif.textContent = 'Memindai wajah...';
        statusKamera.innerHTML = '<span style="background:rgba(19,88,184,0.85);color:white;font-size:0.78rem;padding:5px 14px;border-radius:999px;">Menganalisis wajah Anda...</span>';

        try {
            const extractRes = await LpmFaceService.extractSingleFaceDescriptor(video);

            if (!extractRes.success) {
                ovalGuide.style.borderColor = 'rgba(239,68,68,0.9)';
                tampilPesan('⚠️ ' + extractRes.message, 'error');
                btnVerif.disabled    = false;
                btnVerif.textContent = '🔍 Coba Lagi';
                statusKamera.innerHTML = '<span style="background:rgba(239,68,68,0.85);color:white;font-size:0.78rem;padding:5px 14px;border-radius:999px;">Posisikan wajah Anda</span>';
                return;
            }

            statusKamera.innerHTML = '<span style="background:rgba(19,88,184,0.85);color:white;font-size:0.78rem;padding:5px 14px;border-radius:999px;">Memverifikasi identitas di server...</span>';

            // Kirim descriptor 128-float ke server (PHP) untuk dihitung jarak Euclidean
            const resp = await fetch('{{ route("verifikasi-wajah.proses") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ face_descriptor: extractRes.descriptor }),
            });

            const data = await resp.json();

            if (data.berhasil) {
                ovalGuide.style.borderColor = 'rgba(22,163,74,0.9)';
                tampilPesan('✅ ' + data.pesan, 'success');
                btnVerif.textContent = 'Berhasil!';
                LpmFaceService.stopCamera(cameraStream);
                setTimeout(() => { window.location.href = data.redirect; }, 1500);
            } else if (data.terlalu_banyak) {
                ovalGuide.style.borderColor = 'rgba(239,68,68,0.9)';
                tampilPesan('⚠️ ' + data.pesan, 'error');
                LpmFaceService.stopCamera(cameraStream);
                setTimeout(() => { window.location.href = data.redirect; }, 3000);
            } else if (data.otp_mode) {
                ovalGuide.style.borderColor = 'rgba(239,68,68,0.9)';
                tampilPesan('⚠️ ' + data.pesan, 'error');
                document.getElementById('otpInputArea').style.display = 'block';
                btnVerif.disabled    = false;
                btnVerif.textContent = '🔍 Coba Lagi';
                statusKamera.innerHTML = '<span style="background:rgba(239,68,68,0.85);color:white;font-size:0.78rem;padding:5px 14px;border-radius:999px;">Gunakan alternatif OTP</span>';
            } else {
                ovalGuide.style.borderColor = 'rgba(239,68,68,0.9)';
                tampilPesan('❌ ' + data.pesan, 'error');
                btnVerif.disabled    = false;
                btnVerif.textContent = '🔍 Coba Lagi';
                statusKamera.innerHTML = '<span style="background:rgba(239,68,68,0.85);color:white;font-size:0.78rem;padding:5px 14px;border-radius:999px;">Wajah tidak cocok</span>';
            }
        } catch (err) {
            console.error('Error verifikasi:', err);
            tampilPesan('Terjadi kesalahan jaringan atau batasan permintaan (maks. 5 kali/menit). Coba lagi beberapa saat.', 'error');
            btnVerif.disabled    = false;
            btnVerif.textContent = '🔍 Verifikasi Wajah Saya';
        }
    }

    async function kirimOtp() {
        const btn = document.getElementById('btnKirimOtp');
        btn.disabled    = true;
        btn.textContent = 'Mengirim...';

        try {
            const resp = await fetch('{{ route("verifikasi-wajah.otp.kirim") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({}),
            });
            const data = await resp.json();

            tampilPesan(data.pesan, data.berhasil ? 'success' : 'error');
            if (data.berhasil) {
                document.getElementById('otpInputArea').style.display = 'block';
            }
            btn.disabled    = false;
            btn.textContent = 'Kirim Ulang OTP';
        } catch (err) {
            tampilPesan('Gagal mengirim OTP.', 'error');
            btn.disabled    = false;
            btn.textContent = 'Kirim Kode OTP';
        }
    }

    async function verifikasiOtp() {
        const otp = document.getElementById('inputOtp').value.trim();
        if (otp.length !== 6) {
            document.getElementById('pesanOtp').textContent = 'OTP harus 6 digit angka.';
            return;
        }

        try {
            const resp = await fetch('{{ route("verifikasi-wajah.otp.verifikasi") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ otp }),
            });
            const data = await resp.json();
            document.getElementById('pesanOtp').textContent = data.pesan;

            if (data.berhasil) {
                tampilPesan('✅ ' + data.pesan, 'success');
                LpmFaceService.stopCamera(cameraStream);
                setTimeout(() => { window.location.href = data.redirect; }, 1500);
            }
        } catch (err) {
            document.getElementById('pesanOtp').textContent = 'Gagal verifikasi OTP. Coba lagi.';
        }
    }

    function tampilPesan(teks, tipe) {
        const warna = tipe === 'success'
            ? { bg: '#DCFCE7', border: '#16A34A', text: '#15803D' }
            : { bg: '#FEE2E2', border: '#EF4444', text: '#DC2626' };

        pesanGlobal.style.display    = 'block';
        pesanGlobal.style.background = warna.bg;
        pesanGlobal.style.border     = `1px solid ${warna.border}`;
        pesanGlobal.style.color      = warna.text;
        pesanGlobal.textContent      = teks;
    }

    initKamera();
</script>
@endpush

@endsection
