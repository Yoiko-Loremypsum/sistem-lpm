@extends('layouts.auth')

@section('title', 'Daftar Akun — Langkah 3 dari 3')
@section('header-title', 'Pendaftaran Wajah')
@section('header-subtitle', 'Daftarkan biometrik wajah di browser untuk verifikasi identitas saat login')

@section('content')

{{-- Indikator Langkah --}}
<div style="display: flex; align-items: center; justify-content: center; gap: 0; margin-bottom: 24px;">
    <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
        <div style="width: 36px; height: 36px; border-radius: 50%; background: #16A34A; color: white;
                    display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;">✓</div>
        <span style="font-size: 0.72rem; font-weight: 600; color: #16A34A;">Cek Data</span>
    </div>
    <div style="flex: 1; height: 2px; background: #16A34A; margin: 0 8px; margin-bottom: 16px;"></div>
    <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
        <div style="width: 36px; height: 36px; border-radius: 50%; background: #16A34A; color: white;
                    display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;">✓</div>
        <span style="font-size: 0.72rem; font-weight: 600; color: #16A34A;">Buat Akun</span>
    </div>
    <div style="flex: 1; height: 2px; background: #1358B8; margin: 0 8px; margin-bottom: 16px;"></div>
    <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
        <div style="width: 36px; height: 36px; border-radius: 50%; background: #1358B8; color: white;
                    display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;
                    box-shadow: 0 0 0 4px rgba(19,88,184,0.18);">3</div>
        <span style="font-size: 0.72rem; font-weight: 600; color: #1358B8;">Daftar Wajah</span>
    </div>
</div>

{{-- Info Pemrosesan Lokal di Browser --}}
<div style="background: #E6EFFA; border-radius: 12px; padding: 12px 14px; margin-bottom: 16px; border-left: 4px solid #1358B8;">
    <p style="margin: 0; font-size: 0.8rem; color: #0B3F8C; line-height: 1.5;">
        🔒 <strong>Privasi Terjamin:</strong> Pengenalan wajah diproses langsung di browser Anda menggunakan AI. Foto mentah tidak dikirim ke server, hanya vektor biometrik terenkripsi.
    </p>
</div>

{{-- Area Kamera --}}
<div id="kameraArea">
    <div style="position: relative; width: 100%; aspect-ratio: 4/3; background: #0F172A; border-radius: 16px;
                overflow: hidden; margin-bottom: 14px; border: 2px solid rgba(19,88,184,0.3);">
        <video id="kameraVideo" autoplay muted playsinline
               style="width: 100%; height: 100%; object-fit: cover; display: block; transform: scaleX(-1);"></video>
        {{-- Overlay panduan wajah oval --}}
        <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; pointer-events: none;">
            <div id="ovalGuide" style="width: 170px; height: 210px; border: 3px solid rgba(19,88,184,0.7);
                        border-radius: 50% / 45%; opacity: 0.9; transition: border-color 0.3s;
                        box-shadow: 0 0 0 2000px rgba(0,0,0,0.3);"></div>
        </div>
        {{-- Label status kamera & AI --}}
        <div id="statusKamera" style="position: absolute; bottom: 12px; left: 0; right: 0; text-align: center;">
            <span style="background: rgba(0,0,0,0.75); color: white; font-size: 0.78rem;
                         padding: 5px 14px; border-radius: 999px; backdrop-filter: blur(4px);">
                Menginisialisasi kamera &amp; model AI...
            </span>
        </div>
    </div>

    {{-- Canvas tersembunyi untuk pratinjau snapshot --}}
    <canvas id="captureCanvas" style="display: none;"></canvas>

    <p style="font-size: 0.8rem; color: #64748B; text-align: center; margin-bottom: 14px; line-height: 1.5;">
        Posisikan wajah Anda di dalam bingkai oval. Pastikan pencahayaan cukup dan hadap lurus ke kamera.
    </p>

    <button id="btnAmbilWajah" onclick="pindaiWajah()"
            class="btn-pill-primary"
            style="width: 100%; padding: 13px; font-size: 0.95rem; display: none;">
        📸 Pindai &amp; Daftarkan Wajah Saya
    </button>

    {{-- Pratinjau wajah yang terdeteksi --}}
    <div id="previewArea" style="display: none; margin-top: 14px; text-align: center;">
        <img id="previewImg" style="width: 100%; border-radius: 12px; border: 2px solid #16A34A;" alt="Pratinjau wajah terdeteksi">
        <div style="display: flex; gap: 10px; margin-top: 10px;">
            <button onclick="ulangiPindai()"
                    style="flex: 1; padding: 10px; border: 1.5px solid #CBD5E1; background: white; color: #475569;
                           border-radius: 12px; cursor: pointer; font-weight: 600; font-size: 0.85rem;">
                🔄 Pindai Ulang
            </button>
            <button id="btnKirimDescriptor" onclick="kirimDescriptor()"
                    style="flex: 2; padding: 10px; background: #1358B8; color: white;
                           border: none; border-radius: 12px; cursor: pointer; font-weight: 700; font-size: 0.85rem;">
                ✓ Simpan &amp; Lanjutkan
            </button>
        </div>
    </div>
</div>

{{-- Pemisah --}}
<div style="display: flex; align-items: center; gap: 12px; margin: 20px 0;">
    <div style="flex: 1; height: 1px; background: #E2E8F0;"></div>
    <span style="font-size: 0.78rem; color: #94A3B8; font-weight: 500; white-space: nowrap;">atau</span>
    <div style="flex: 1; height: 1px; background: #E2E8F0;"></div>
</div>

{{-- OTP Cadangan --}}
<div style="border: 1.5px solid #E2E8F0; border-radius: 14px; padding: 16px;">
    <p style="font-size: 0.82rem; font-weight: 600; color: #475569; margin: 0 0 8px;">
        📧 Verifikasi via OTP (Cadangan)
    </p>
    <p style="font-size: 0.78rem; color: #94A3B8; margin: 0 0 12px; line-height: 1.5;">
        Jika kamera tidak tersedia atau bermasalah, kirim kode OTP ke email institusi Anda:
        <strong style="color: #475569;">{{ $user->email }}</strong>
    </p>

    <button id="btnKirimOtp" onclick="kirimOtp()"
            style="width: 100%; padding: 10px; border: 1.5px solid #1358B8; background: #E6EFFA;
                   color: #1358B8; border-radius: 12px; cursor: pointer; font-weight: 600; font-size: 0.85rem;">
        Kirim Kode OTP ke Email
    </button>

    <div id="otpInputArea" style="display: none; margin-top: 12px;">
        <div style="display: flex; gap: 8px; align-items: flex-end;">
            <div style="flex: 1;">
                <label style="font-size: 0.78rem; color: #64748B; display: block; margin-bottom: 4px;">Masukkan Kode OTP (6 digit)</label>
                <input type="text" id="inputOtp" maxlength="6" placeholder="123456"
                       style="width: 100%; padding: 11px 14px; border: 1.5px solid #CBD5E1; border-radius: 10px;
                              font-size: 1.1rem; letter-spacing: 4px; text-align: center; outline: none; box-sizing: border-box;">
            </div>
            <button onclick="verifikasiOtp()"
                    style="padding: 11px 16px; background: #1358B8; color: white; border: none;
                           border-radius: 10px; cursor: pointer; font-weight: 700; font-size: 0.85rem; white-space: nowrap;">
                Verifikasi
            </button>
        </div>
        <p id="pesanOtp" style="font-size: 0.78rem; margin-top: 6px; color: #64748B;"></p>
    </div>
</div>

{{-- Pesan status global --}}
<div id="pesanGlobal" style="display: none; margin-top: 16px; padding: 12px 16px; border-radius: 10px; font-size: 0.85rem; font-weight: 500;"></div>

@push('scripts')
{{-- face-api.js v0.22.2 dari CDN resmi --}}
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script src="{{ asset('js/face-service.js') }}"></script>
<script>
    let cameraStream = null;
    let extractedDescriptor = null;

    const video         = document.getElementById('kameraVideo');
    const canvas        = document.getElementById('captureCanvas');
    const btnAmbilWajah = document.getElementById('btnAmbilWajah');
    const statusKamera  = document.getElementById('statusKamera');
    const previewArea   = document.getElementById('previewArea');
    const previewImg    = document.getElementById('previewImg');
    const ovalGuide     = document.getElementById('ovalGuide');
    const pesanGlobal   = document.getElementById('pesanGlobal');

    async function init() {
        if (!LpmFaceService.isSecureOrigin()) {
            statusKamera.innerHTML = '<span style="background:rgba(239,68,68,0.9);color:white;font-size:0.78rem;padding:5px 14px;border-radius:999px;">Akses Tidak Aman</span>';
            tampilPesan('Akses kamera hanya diperbolehkan melalui koneksi aman (HTTPS atau localhost). Gunakan opsi verifikasi OTP di bawah.', 'error');
            return;
        }

        try {
            statusKamera.innerHTML = '<span style="background:rgba(19,88,184,0.85);color:white;font-size:0.78rem;padding:5px 14px;border-radius:999px;">Mengaktifkan kamera...</span>';
            cameraStream = await LpmFaceService.startCamera(video);

            statusKamera.innerHTML = '<span style="background:rgba(19,88,184,0.85);color:white;font-size:0.78rem;padding:5px 14px;border-radius:999px;">Memuat modul AI pengenal wajah...</span>';
            await LpmFaceService.loadModels('/models');

            statusKamera.innerHTML = '<span style="background:rgba(22,163,74,0.85);color:white;font-size:0.78rem;padding:5px 14px;border-radius:999px;">Kamera &amp; AI Siap — Posisikan wajah Anda</span>';
            btnAmbilWajah.style.display = 'block';
        } catch (err) {
            console.error('Inisialisasi kamera/AI gagal:', err);
            statusKamera.innerHTML = '<span style="background:rgba(239,68,68,0.85);color:white;font-size:0.78rem;padding:5px 14px;border-radius:999px;">Kamera / AI Tidak Tersedia</span>';
            tampilPesan(err.message || 'Gagal mengakses kamera atau memuat modul AI. Gunakan verifikasi OTP di bawah.', 'error');
        }
    }

    async function pindaiWajah() {
        btnAmbilWajah.disabled = true;
        btnAmbilWajah.textContent = 'Memindai wajah...';
        statusKamera.innerHTML = '<span style="background:rgba(19,88,184,0.85);color:white;font-size:0.78rem;padding:5px 14px;border-radius:999px;">Menganalisis wajah dengan AI...</span>';

        try {
            const res = await LpmFaceService.extractSingleFaceDescriptor(video);

            if (!res.success) {
                ovalGuide.style.borderColor = 'rgba(239,68,68,0.9)';
                tampilPesan('⚠️ ' + res.message, 'error');
                btnAmbilWajah.disabled = false;
                btnAmbilWajah.textContent = '📸 Coba Pindai Ulang';
                statusKamera.innerHTML = '<span style="background:rgba(239,68,68,0.85);color:white;font-size:0.78rem;padding:5px 14px;border-radius:999px;">Wajah belum sesuai</span>';
                return;
            }

            // Berhasil mendeteksi tepat 1 wajah
            extractedDescriptor = res.descriptor;
            ovalGuide.style.borderColor = 'rgba(22,163,74,0.9)';

            // Capture snapshot visual untuk ditampilkan kepada user sebagai konfirmasi
            canvas.width  = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(video, 0, 0);

            previewImg.src = canvas.toDataURL('image/jpeg', 0.85);
            previewArea.style.display = 'block';
            btnAmbilWajah.style.display = 'none';

            statusKamera.innerHTML = '<span style="background:rgba(22,163,74,0.85);color:white;font-size:0.78rem;padding:5px 14px;border-radius:999px;">Wajah berhasil dipindai (' + res.score + '% keyakinan)</span>';
            tampilPesan('✅ ' + res.message + ' Klik "Simpan & Lanjutkan" untuk mendaftarkan wajah.', 'success');

        } catch (err) {
            console.error('Error pindai wajah:', err);
            tampilPesan('Terjadi kesalahan saat memproses AI wajah: ' + err.message, 'error');
            btnAmbilWajah.disabled = false;
            btnAmbilWajah.textContent = '📸 Coba Pindai Ulang';
        }
    }

    function ulangiPindai() {
        extractedDescriptor = null;
        previewArea.style.display = 'none';
        btnAmbilWajah.style.display = 'block';
        btnAmbilWajah.disabled = false;
        btnAmbilWajah.textContent = '📸 Pindai & Daftarkan Wajah Saya';
        ovalGuide.style.borderColor = 'rgba(19,88,184,0.7)';
        statusKamera.innerHTML = '<span style="background:rgba(22,163,74,0.85);color:white;font-size:0.78rem;padding:5px 14px;border-radius:999px;">Kamera Siap — Posisikan wajah Anda</span>';
    }

    async function kirimDescriptor() {
        if (!extractedDescriptor || extractedDescriptor.length !== 128) {
            tampilPesan('Data biometrik belum lengkap. Silakan pindai ulang.', 'error');
            return;
        }

        const btn = document.getElementById('btnKirimDescriptor');
        btn.disabled    = true;
        btn.textContent = 'Menyimpan ke Server...';

        try {
            const resp = await fetch('{{ route("registrasi.wajah.daftar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ face_descriptor: extractedDescriptor }),
            });

            const data = await resp.json();

            if (data.berhasil) {
                tampilPesan('✅ ' + data.pesan, 'success');
                LpmFaceService.stopCamera(cameraStream);
                setTimeout(() => { window.location.href = data.redirect; }, 1800);
            } else {
                tampilPesan('❌ ' + data.pesan, 'error');
                btn.disabled    = false;
                btn.textContent = '✓ Simpan & Lanjutkan';
            }
        } catch (err) {
            console.error('Gagal kirim descriptor:', err);
            tampilPesan('Terjadi gangguan jaringan saat menyimpan data ke server.', 'error');
            btn.disabled    = false;
            btn.textContent = '✓ Simpan & Lanjutkan';
        }
    }

    // =========================================================
    // OTP Cadangan
    // =========================================================
    async function kirimOtp() {
        const btn = document.getElementById('btnKirimOtp');
        btn.disabled    = true;
        btn.textContent = 'Mengirim OTP...';

        try {
            const resp = await fetch('{{ route("registrasi.otp.kirim") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({}),
            });
            const data = await resp.json();
            tampilPesan(data.pesan, data.berhasil ? 'success' : 'error');

            if (data.berhasil) {
                document.getElementById('otpInputArea').style.display = 'block';
                btn.textContent = 'Kirim Ulang OTP';
                btn.disabled    = false;
            } else {
                btn.disabled    = false;
                btn.textContent = 'Kirim Kode OTP ke Email';
            }
        } catch (err) {
            tampilPesan('Gagal mengirim OTP. Coba lagi.', 'error');
            btn.disabled    = false;
            btn.textContent = 'Kirim Kode OTP ke Email';
        }
    }

    async function verifikasiOtp() {
        const otp = document.getElementById('inputOtp').value.trim();
        if (otp.length !== 6) {
            document.getElementById('pesanOtp').textContent = 'OTP harus 6 digit angka.';
            return;
        }

        try {
            const resp = await fetch('{{ route("registrasi.otp.verifikasi") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ otp }),
            });
            const data = await resp.json();
            document.getElementById('pesanOtp').textContent = data.pesan;

            if (data.berhasil) {
                tampilPesan('✅ ' + data.pesan, 'success');
                LpmFaceService.stopCamera(cameraStream);
                setTimeout(() => { window.location.href = data.redirect; }, 1800);
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

    // Jalankan inisialisasi kamera dan model
    init();
</script>
@endpush

@endsection
