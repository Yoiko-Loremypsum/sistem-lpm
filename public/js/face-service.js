/**
 * Sistem LPM UMI - Face Recognition Client Service
 * Menggunakan face-api.js v0.22.2 (TensorFlow.js) di peramban (browser) klien.
 *
 * Menggantikan microservice Python:
 * - Seluruh deteksi & ekstraksi 128-float descriptor berjalan di browser klien.
 * - Tidak mengirim gambar/foto mentah ke server, hanya 128 angka float descriptor.
 * - Server hanya melakukan verifikasi perbandingan jarak Euclidean di PHP.
 */

window.LpmFaceService = (function () {
    let modelsLoaded = false;
    let loadingPromise = null;

    /**
     * Cek apakah konteks peramban aman untuk akses WebRTC/Kamera
     */
    function isSecureOrigin() {
        return window.isSecureContext ||
            window.location.hostname === 'localhost' ||
            window.location.hostname === '127.0.0.1' ||
            window.location.protocol === 'https:';
    }

    /**
     * Muat model AI dari direktori /models
     */
    async function loadModels(modelPath = '/models') {
        if (modelsLoaded) return true;
        if (loadingPromise) return loadingPromise;

        if (typeof faceapi === 'undefined') {
            throw new Error('Pustaka face-api.js belum dimuat. Periksa koneksi internet atau tag script CDN.');
        }

        loadingPromise = (async () => {
            try {
                // Muat model Tiny Face Detector (ringan & cepat untuk mobile/desktop)
                await faceapi.nets.tinyFaceDetector.loadFromUri(modelPath);
                // Muat model 68 titik landmark wajah
                await faceapi.nets.faceLandmark68Net.loadFromUri(modelPath);
                // Muat model ekstraksi vektor descriptor 128 dimensi
                await faceapi.nets.faceRecognitionNet.loadFromUri(modelPath);

                modelsLoaded = true;
                return true;
            } catch (err) {
                console.error('Gagal memuat model face-api:', err);
                throw new Error('Gagal memuat file model AI dari server (' + err.message + '). Pastikan folder public/models/ dapat diakses.');
            } finally {
                loadingPromise = null;
            }
        })();

        return loadingPromise;
    }

    /**
     * Inisialisasi stream kamera pengguna
     */
    async function startCamera(videoElement) {
        if (!isSecureOrigin()) {
            throw new Error('Akses kamera diblokir browser karena halaman tidak dibuka melalui HTTPS atau localhost.');
        }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error('Browser Anda tidak mendukung WebRTC Camera API. Gunakan browser modern (Chrome, Edge, Firefox, Safari).');
        }

        const stream = await navigator.mediaDevices.getUserMedia({
            video: {
                width: { ideal: 640 },
                height: { ideal: 480 },
                facingMode: 'user',
            },
            audio: false,
        });

        videoElement.srcObject = stream;

        await new Promise((resolve) => {
            videoElement.onloadedmetadata = () => {
                videoElement.play();
                resolve();
            };
        });

        return stream;
    }

    /**
     * Hentikan stream kamera
     */
    function stopCamera(stream) {
        if (stream) {
            stream.getTracks().forEach((track) => track.stop());
        }
    }

    /**
     * Deteksi wajah dan ekstrak descriptor 128 dimensi.
     * Mengembalikan objek:
     * {
     *   success: bool,
     *   descriptor: Array<number> (panjang 128) | null,
     *   faceCount: number,
     *   score: number,
     *   message: string
     * }
     */
    async function extractSingleFaceDescriptor(videoElement) {
        if (!modelsLoaded) {
            await loadModels();
        }

        if (!videoElement || videoElement.paused || videoElement.ended) {
            return {
                success: false,
                descriptor: null,
                faceCount: 0,
                score: 0,
                message: 'Kamera belum siap atau tidak aktif.',
            };
        }

        const options = new faceapi.TinyFaceDetectorOptions({
            inputSize: 416,
            scoreThreshold: 0.5,
        });

        const detections = await faceapi
            .detectAllFaces(videoElement, options)
            .withFaceLandmarks()
            .withFaceDescriptors();

        if (!detections || detections.length === 0) {
            return {
                success: false,
                descriptor: null,
                faceCount: 0,
                score: 0,
                message: 'Wajah tidak terdeteksi. Posisikan wajah tepat di area oval panduan dan pastikan pencahayaan cukup.',
            };
        }

        if (detections.length > 1) {
            return {
                success: false,
                descriptor: null,
                faceCount: detections.length,
                score: 0,
                message: 'Terdeteksi ' + detections.length + ' wajah! Pastikan hanya ada satu orang di depan kamera.',
            };
        }

        const bestFace = detections[0];
        const descriptorArray = Array.from(bestFace.descriptor);

        if (descriptorArray.length !== 128) {
            return {
                success: false,
                descriptor: null,
                faceCount: 1,
                score: bestFace.detection.score,
                message: 'Gagal mengekstrak vektor descriptor 128 dimensi.',
            };
        }

        return {
            success: true,
            descriptor: descriptorArray,
            faceCount: 1,
            score: Math.round(bestFace.detection.score * 100),
            message: 'Wajah terdeteksi dengan baik (' + Math.round(bestFace.detection.score * 100) + '% keyakinan).',
        };
    }

    return {
        isSecureOrigin,
        loadModels,
        startCamera,
        stopCamera,
        extractSingleFaceDescriptor,
    };
})();
