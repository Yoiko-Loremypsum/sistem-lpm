<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan_Pengaduan_LPM_UMI_{{ date('Ymd_His') }}</title>
    <link rel="icon" href="{{ asset('images/logo-umi.jpg') }}">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #0F172A;
            background: #fff;
            margin: 0;
            padding: 24px;
        }

        .no-print {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .btn-print {
            background: #1358B8;
            color: #fff;
            padding: 10px 22px;
            border-radius: 9999px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            font-size: 13px;
            transition: all 0.2s;
        }

        .btn-print:hover {
            background: #0B3F8C;
        }

        .btn-back {
            background: #fff;
            color: #1358B8;
            padding: 10px 20px;
            border-radius: 9999px;
            border: 1px solid #1358B8;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }

        /* Kop Surat Resmi UMI */
        .kop-surat {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            border-bottom: 3px double #1358B8;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }

        .kop-logo {
            width: 75px;
            height: 75px;
            object-fit: contain;
        }

        .kop-text {
            text-align: center;
        }

        .kop-instansi {
            font-size: 13pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #1358B8;
        }

        .kop-dinas {
            font-size: 16pt;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 4px 0;
            color: #0F172A;
        }

        .kop-kontak {
            font-size: 9pt;
            color: #475569;
        }

        /* Judul Dokumen */
        .judul-dokumen {
            text-align: center;
            margin-bottom: 20px;
        }

        .judul-dokumen h2 {
            font-size: 14pt;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            text-decoration: underline;
            color: #0F172A;
        }

        .judul-dokumen p {
            font-size: 9pt;
            color: #64748B;
            margin: 0;
        }

        /* Info Filter */
        .meta-table {
            width: 100%;
            margin-bottom: 18px;
            font-size: 9.5pt;
        }

        .meta-table td {
            padding: 3px 0;
        }

        /* Tabel Rekap Data */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 24px;
        }

        .data-table th, .data-table td {
            border: 1px solid #CBD5E1;
            padding: 8px 10px;
            text-align: left;
        }

        .data-table th {
            background-color: #E6EFFA;
            color: #0B3F8C;
            font-weight: bold;
            text-align: center;
        }

        /* Ringkasan Box */
        .summary-box {
            display: flex;
            border: 1px solid #CBD5E1;
            margin-bottom: 20px;
            background: #F8FAFC;
            border-radius: 6px;
            overflow: hidden;
        }

        .summary-item {
            flex: 1;
            padding: 10px;
            text-align: center;
            border-right: 1px solid #CBD5E1;
        }

        .summary-item:last-child {
            border-right: none;
        }

        .summary-num {
            font-size: 14pt;
            font-weight: bold;
            color: #1358B8;
        }

        .summary-lbl {
            font-size: 8pt;
            text-transform: uppercase;
            color: #64748B;
            font-weight: 600;
        }

        /* Tanda Tangan */
        .tanda-tangan-wrapper {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
            page-break-inside: avoid;
        }

        .tanda-tangan-box {
            text-align: center;
            width: 240px;
        }

        .ttd-space {
            height: 70px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print">
        <button type="button" onclick="window.print()" class="btn-print">Cetak / Simpan PDF</button>
        <button type="button" onclick="window.close()" class="btn-back">Tutup Tab</button>
    </div>

    <!-- Kop Surat Resmi UMI -->
    <div class="kop-surat">
        <img src="{{ asset('images/logo-umi.jpg') }}" alt="Logo Universitas Methodist Indonesia" class="kop-logo">
        <div class="kop-text">
            <div class="kop-instansi">UNIVERSITAS METHODIST INDONESIA</div>
            <div class="kop-dinas">SISTEM LAYANAN PENGADUAN MASYARAKAT (LPM)</div>
            <div class="kop-kontak">
                Jl. Hang Tuah No. 8, Madras Hulu, Kec. Medan Polonia, Kota Medan, Sumatera Utara 20151<br>
                Website: www.methodist.ac.id | Email: info@methodist.ac.id
            </div>
        </div>
    </div>

    <!-- Judul Dokumen -->
    <div class="judul-dokumen">
        <h2>Laporan Rekapitulasi Pengaduan</h2>
        <p>Nomor Dokumen: UMI-LPM/REKAP/{{ date('Ym') }}/{{ str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) }}</p>
    </div>

    <!-- Info Filter -->
    <table class="meta-table">
        <tr>
            <td style="width: 130px;"><strong>Periode Laporan</strong></td>
            <td>: {{ $filterInfo['tanggal_mulai'] }} s/d {{ $filterInfo['tanggal_selesai'] }}</td>
            <td style="width: 130px;"><strong>Tanggal Cetak</strong></td>
            <td>: {{ date('d F Y, H:i') }} WIB</td>
        </tr>
        <tr>
            <td><strong>Filter Status</strong></td>
            <td>: {{ $filterInfo['status'] }}</td>
            <td><strong>Dicetak Oleh</strong></td>
            <td>: {{ auth()->user()->nama }} (Administrator)</td>
        </tr>
    </table>

    <!-- Ringkasan Angka -->
    <div class="summary-box">
        <div class="summary-item">
            <div class="summary-num">{{ $summary['total'] }}</div>
            <div class="summary-lbl">Total Laporan</div>
        </div>
        <div class="summary-item">
            <div class="summary-num">{{ $summary['menunggu'] }}</div>
            <div class="summary-lbl">Menunggu</div>
        </div>
        <div class="summary-item">
            <div class="summary-num">{{ $summary['diproses'] }}</div>
            <div class="summary-lbl">Diproses</div>
        </div>
        <div class="summary-item">
            <div class="summary-num">{{ $summary['selesai'] }}</div>
            <div class="summary-lbl">Selesai</div>
        </div>
        <div class="summary-item">
            <div class="summary-num">{{ $summary['ditolak'] }}</div>
            <div class="summary-lbl">Ditolak</div>
        </div>
    </div>

    <!-- Tabel Rekap Data -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 90px;">Tiket</th>
                <th style="width: 75px;">Tanggal</th>
                <th>Nama Pelapor</th>
                <th>Kategori</th>
                <th>Judul Laporan</th>
                <th>Lokasi</th>
                <th style="width: 70px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengaduans as $index => $p)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="font-family: monospace; font-weight: bold;">{{ $p->kode_tiket }}</td>
                    <td>{{ $p->created_at->format('d/m/Y') }}</td>
                    <td>{{ $p->user ? $p->user->nama : 'Pengguna' }}</td>
                    <td>{{ $p->kategori ? $p->kategori->nama_kategori : 'Umum' }}</td>
                    <td>{{ $p->judul }}</td>
                    <td>{{ $p->lokasi ?: '-' }}</td>
                    <td style="text-align: center; text-transform: uppercase; font-size: 8pt; font-weight: bold;">
                        {{ $p->status_label }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">
                        Tidak ada data pengaduan yang sesuai filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan Penanggung Jawab -->
    <div class="tanda-tangan-wrapper">
        <div class="tanda-tangan-box">
            <div>Medan, {{ date('d F Y') }}</div>
            <div style="font-weight: bold; margin-top: 4px;">Kepala Layanan Pengaduan UMI</div>
            <div class="ttd-space"></div>
            <div style="font-weight: bold; text-decoration: underline;">{{ auth()->user()->nama }}</div>
            <div style="font-size: 9pt; color: #475569;">Administrator Sistem LPM</div>
        </div>
    </div>

</body>
</html>
