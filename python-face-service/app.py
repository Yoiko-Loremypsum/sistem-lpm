"""
[DEPRECATED / TIDAK DIGUNAKAN LAGI]
Layanan Python untuk Verifikasi Wajah — Sistem LPM UMI
=======================================================
Fitur pengenalan wajah telah dimigrasikan ke face-api.js di browser klien.
Laravel tidak lagi memanggil server Python ini.
File ini dipertahankan hanya sebagai arsip dan tidak perlu dijalankan lagi.
=======================================================
"""

import base64
import logging
import os
from io import BytesIO

import numpy as np
from PIL import Image
from flask import Flask, jsonify, request

# Konfigurasi logging — JANGAN log array embedding
logging.basicConfig(level=logging.INFO, format='%(asctime)s %(levelname)s %(message)s')
logger = logging.getLogger(__name__)

app = Flask(__name__)

# =========================================================
# Inisialisasi InsightFace
# =========================================================
try:
    import insightface
    from insightface.app import FaceAnalysis

    face_app = FaceAnalysis(
        name='buffalo_l',   # Model ArcFace paling akurat
        providers=['CPUExecutionProvider'],  # Ganti ke CUDAExecutionProvider jika ada GPU
    )
    face_app.prepare(ctx_id=0, det_size=(640, 640))
    logger.info("InsightFace berhasil diinisialisasi dengan model buffalo_l")

except ImportError:
    logger.error("insightface tidak terinstall. Jalankan: pip install insightface onnxruntime")
    face_app = None
except Exception as e:
    logger.error(f"Gagal inisialisasi InsightFace: {e}")
    face_app = None


# =========================================================
# Helper
# =========================================================

def decode_image_base64(b64_string: str) -> np.ndarray:
    """
    Decode string base64 (dengan atau tanpa prefix data URI)
    menjadi array numpy BGR yang siap dipakai InsightFace.
    """
    # Hapus prefix data URI jika ada (contoh: data:image/jpeg;base64,...)
    if ',' in b64_string:
        b64_string = b64_string.split(',', 1)[1]

    image_bytes = base64.b64decode(b64_string)
    pil_image   = Image.open(BytesIO(image_bytes)).convert('RGB')
    # InsightFace mengharapkan format BGR (OpenCV)
    img_array = np.array(pil_image)[:, :, ::-1]
    return img_array


def cosine_similarity(emb1: list, emb2: list) -> float:
    """Hitung cosine similarity antara dua vektor embedding."""
    v1 = np.array(emb1)
    v2 = np.array(emb2)
    return float(np.dot(v1, v2) / (np.linalg.norm(v1) * np.linalg.norm(v2)))


# =========================================================
# Endpoint: Health Check
# =========================================================

@app.route('/health', methods=['GET'])
def health():
    return jsonify({
        'status'   : 'ok',
        'model'    : 'buffalo_l (InsightFace ArcFace)',
        'siap'     : face_app is not None,
    })


# =========================================================
# Endpoint: /embed — Buat embedding dari gambar wajah
# =========================================================

@app.route('/embed', methods=['POST'])
def embed():
    """
    Input  (JSON): { "image_base64": "<base64 string>" }
    Output (JSON): { "embedding": [float, ...] } atau { "error": "..." }

    Catatan keamanan:
    - Embedding TIDAK pernah di-log di sini
    - Laravel yang bertanggung jawab mengenkripsi embedding sebelum disimpan
    """
    if face_app is None:
        return jsonify({'error': 'Layanan wajah tidak tersedia'}), 503

    data = request.get_json(silent=True)
    if not data or 'image_base64' not in data:
        return jsonify({'error': 'Parameter image_base64 wajib ada'}), 400

    try:
        img = decode_image_base64(data['image_base64'])
    except Exception as e:
        logger.warning(f"Gagal decode gambar: {e}")
        return jsonify({'error': 'Format gambar tidak valid'}), 400

    try:
        faces = face_app.get(img)
    except Exception as e:
        logger.error(f"Error saat deteksi wajah: {e}")
        return jsonify({'error': 'Gagal memproses gambar'}), 500

    if not faces:
        # Tidak ada wajah terdeteksi
        return jsonify({'embedding': None, 'pesan': 'Wajah tidak terdeteksi'}), 200

    if len(faces) > 1:
        # Ambil wajah dengan detection score tertinggi
        faces = sorted(faces, key=lambda f: f.det_score, reverse=True)

    best_face = faces[0]

    # Normalisasi embedding (unit vector)
    emb = best_face.normed_embedding.tolist()

    logger.info(f"Embedding berhasil dibuat (panjang: {len(emb)}, score: {best_face.det_score:.3f})")
    # JANGAN log nilai embedding

    return jsonify({'embedding': emb})


# =========================================================
# Endpoint: /compare — Bandingkan dua embedding
# =========================================================

@app.route('/compare', methods=['POST'])
def compare():
    """
    Input  (JSON): { "embedding1": [...], "embedding2": [...] }
    Output (JSON): { "match": bool, "distance": float, "similarity": float }

    Threshold cosine similarity: 0.4 (standar ArcFace, bisa disesuaikan)
    Keputusan match/no-match dibuat DI SINI (server-side), bukan di browser.
    """
    if face_app is None:
        return jsonify({'error': 'Layanan wajah tidak tersedia'}), 503

    data = request.get_json(silent=True)
    if not data or 'embedding1' not in data or 'embedding2' not in data:
        return jsonify({'error': 'Parameter embedding1 dan embedding2 wajib ada'}), 400

    emb1 = data['embedding1']
    emb2 = data['embedding2']

    if not emb1 or not emb2:
        return jsonify({'error': 'Embedding tidak boleh kosong'}), 400

    try:
        similarity = cosine_similarity(emb1, emb2)
        distance   = 1.0 - similarity

        # Threshold yang umum dipakai untuk ArcFace: >= 0.4 dianggap cocok
        # Sesuaikan jika ingin lebih ketat (0.45) atau lebih longgar (0.35)
        THRESHOLD = float(os.environ.get('FACE_THRESHOLD', '0.40'))

        match = similarity >= THRESHOLD

        logger.info(f"Perbandingan wajah: similarity={similarity:.4f}, match={match}")
        # JANGAN log nilai embedding

        return jsonify({
            'match'      : match,
            'similarity' : round(similarity, 4),
            'distance'   : round(distance, 4),
            'threshold'  : THRESHOLD,
        })

    except Exception as e:
        logger.error(f"Error saat membandingkan embedding: {e}")
        return jsonify({'error': 'Gagal membandingkan wajah'}), 500


# =========================================================
# Main
# =========================================================

if __name__ == '__main__':
    port = int(os.environ.get('FACE_SERVICE_PORT', 5001))
    # debug=False di produksi
    app.run(host='127.0.0.1', port=port, debug=os.environ.get('APP_ENV') == 'local')
