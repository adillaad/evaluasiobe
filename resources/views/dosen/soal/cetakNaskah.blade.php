<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Naskah Soal - {{ $mk->kode ?? 'MK' }} - {{ $namaMetode ?? 'Ujian' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo_unila.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    
    <style>
        /* Pengaturan Umum Halaman */
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            background-color: #f4f6f9;
            margin: 0;
            padding: 20px 0;
        }

        .paper {
            width: 210mm;
            min-height: 297mm;
            padding: 15mm 20mm 20mm 20mm;
            margin: 0 auto;
            background: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        /* Floating Toolbar untuk aksi di layar browser */
        .toolbar-no-print {
            position: fixed;
            top: 20px;
            right: 25px;
            z-index: 9999;
            display: flex;
            gap: 10px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            padding: 8px 14px;
            border-radius: 50px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.18);
            border: 1px solid #e2e8f0;
        }

        .btn-tool {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 13px;
            font-weight: 600;
            border-radius: 30px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-print {
            background-color: #006199;
            color: #ffffff;
        }
        .btn-print:hover {
            background-color: #004c78;
            transform: translateY(-1px);
        }

        .btn-close-tab {
            background-color: #f1f5f9;
            color: #475569;
        }
        .btn-close-tab:hover {
            background-color: #e2e8f0;
            color: #0f172a;
        }

        /* Kop Surat Resmi Universitas */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            border-bottom: 3px double #000000;
            padding-bottom: 8px;
        }

        .kop-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }

        .kop-logo {
            width: 2.3cm;
            text-align: center;
            padding-right: 12px;
        }

        .kop-logo img {
            width: 2.1cm;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .kop-text {
            text-align: center;
            line-height: 1.25;
            padding-right: 1.5cm;
        }

        .kop-text .instansi-kemen {
            font-size: 10.5pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .kop-text .instansi-prodi {
            font-size: 11.5pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .kop-text .instansi-fakultas {
            font-size: 12pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .kop-text .instansi-univ {
            font-size: 14pt;
            font-weight: 800;
            letter-spacing: 1px;
            margin-top: 1px;
        }

        .kop-text .instansi-alamat {
            font-size: 8.5pt;
            font-style: italic;
            margin-top: 5px;
            line-height: 1.35;
        }

        /* Judul Dokumen */
        .doc-title {
            text-align: center;
            margin: 14px 0 16px 0;
            line-height: 1.3;
        }

        .doc-title h2 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .doc-title .semester-info {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 2px;
            text-transform: uppercase;
        }

        /* Tabel Identitas Mata Kuliah */
        .table-identitas {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 10.5pt;
            border: 1px solid #000000;
        }

        .table-identitas td {
            padding: 4px 8px;
            vertical-align: top;
            border: 1px solid #000000;
        }

        .table-identitas td.label {
            font-weight: bold;
            background-color: #f8fafc;
            width: 22%;
        }

        .table-identitas td.separator {
            width: 2%;
            text-align: center;
        }

        .table-identitas td.value {
            width: 26%;
        }

        /* Petunjuk Ujian */
        .petunjuk-box {
            font-size: 9.5pt;
            line-height: 1.4;
            margin-bottom: 20px;
            padding: 6px 12px;
            border: 1px dashed #475569;
            background-color: #fafafa;
        }

        .petunjuk-box strong {
            display: block;
            margin-bottom: 2px;
            font-size: 10pt;
        }

        .petunjuk-box ol {
            margin: 0;
            padding-left: 18px;
        }

        /* Soal-Soal Container */
        .soal-list {
            margin-top: 15px;
        }

        .soal-item {
            margin-bottom: 22px;
            page-break-inside: avoid;
        }

        .soal-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 9.5pt;
            font-weight: bold;
            margin-bottom: 6px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 3px;
        }

        .badge-cpmk {
            display: inline-block;
            font-size: 8.5pt;
            font-weight: bold;
            border: 1px solid #000;
            padding: 1px 6px;
            border-radius: 3px;
            background: #f1f5f9;
        }

        .soal-body {
            font-size: 11pt;
            text-align: justify;
            line-height: 1.6;
        }

        .soal-body p {
            margin-top: 0;
            margin-bottom: 8px;
        }

        .soal-body p:last-child {
            margin-bottom: 0;
        }

        /* Render Rumus & Gambar Matematika */
        .soal-body img.math-tex,
        .soal-body img[src*="latex.codecogs.com"] {
            vertical-align: middle;
            display: inline-block;
            margin: 0 4px;
            max-height: 2.6em;
        }

        .soal-body table {
            width: 100% !important;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .soal-body table td,
        .soal-body table th {
            border: 1px solid #000;
            padding: 6px 10px;
        }

        /* Lembar Pengesahan Tanda Tangan */
        .pengesahan-table {
            width: 100%;
            margin-top: 35px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .pengesahan-table td {
            width: 50%;
            vertical-align: top;
            text-align: center;
            font-size: 10.5pt;
            border: none;
            padding: 0 10px;
        }

        .ttd-space {
            height: 2.2cm;
        }

        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: none;
                padding: 0;
            }

            .paper {
                width: 100%;
                min-height: auto;
                box-shadow: none;
                padding: 0;
                margin: 0;
            }

            .toolbar-no-print {
                display: none !important;
            }

            @page {
                size: A4 portrait;
                margin: 15mm 15mm 15mm 15mm;
            }
        }
    </style>
</head>
<body>

    {{-- Floating Toolbar --}}
    <div class="toolbar-no-print">
        <button type="button" class="btn-tool btn-print" onclick="window.print()">
            <i class="ti ti-printer" style="font-size: 16px;"></i> Cetak / Simpan PDF
        </button>
        <button type="button" class="btn-tool btn-close-tab" onclick="window.close()">
            <i class="ti ti-x" style="font-size: 16px;"></i> Tutup
        </button>
    </div>

    <div class="paper">
        {{-- KOP SURAT RESMI UNIVERSITAS --}}
        @php
            $rawProdi = strtoupper($prodi->nama ?? 'ILMU KOMPUTER');
            $prodiText = str_starts_with($rawProdi, 'PROGRAM STUDI') ? $rawProdi : 'PROGRAM STUDI ' . $rawProdi;
            $fakultasText = strtoupper($prodi->fakultas->nama ?? 'FAKULTAS MATEMATIKA DAN ILMU PENGETAHUAN ALAM');
            $univText = strtoupper($prodi->fakultas->universitas->nama ?? 'UNIVERSITAS LAMPUNG');
        @endphp
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    <img src="{{ $logoUniv ?? asset('assets/img/logo_unila.png') }}" alt="Logo Universitas">
                </td>
                <td class="kop-text">
                    <div class="instansi-kemen">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</div>
                    <div class="instansi-prodi">{{ $prodiText }}</div>
                    <div class="instansi-fakultas">{{ $fakultasText }}</div>
                    <div class="instansi-univ">{{ $univText }}</div>
                    <div class="instansi-alamat">{!! $alamatUniv ?? '' !!}</div>
                </td>
            </tr>
        </table>

        {{-- JUDUL DOKUMEN --}}
        <div class="doc-title">
            <h2>NASKAH SOAL {{ strtoupper($namaMetode) }}</h2>
            <div class="semester-info">TAHUN AKADEMIK {{ strtoupper($semester) }}</div>
        </div>

        {{-- TABEL IDENTITAS MATA KULIAH & UJIAN --}}
        <table class="table-identitas">
            <tr>
                <td class="label">Mata Kuliah</td>
                <td class="separator">:</td>
                <td class="value"><strong>{{ $mk->nama ?? '-' }}</strong></td>
                <td class="label">Hari, Tanggal</td>
                <td class="separator">:</td>
                <td class="value">........................................</td>
            </tr>
            <tr>
                <td class="label">Kode MK / SKS</td>
                <td class="separator">:</td>
                <td class="value">{{ $mk->kode ?? '-' }} / {{ ($mk->bobot_teori ?? 0) + ($mk->bobot_praktikum ?? 0) }} SKS</td>
                <td class="label">Waktu / Durasi</td>
                <td class="separator">:</td>
                <td class="value">........ Menit</td>
            </tr>
            <tr>
                <td class="label">Bentuk Asesmen</td>
                <td class="separator">:</td>
                <td class="value">{{ $namaMetode }}</td>
                <td class="label">Sifat Ujian</td>
                <td class="separator">:</td>
                <td class="value">Tutup Buku / Buka Buku</td>
            </tr>
            <tr>
                <td class="label">Dosen Pengampu</td>
                <td class="separator">:</td>
                <td class="value">{{ $dosenNama }}</td>
                <td class="label">CPMK Diukur</td>
                <td class="separator">:</td>
                <td class="value">
                    @forelse($cpmkList as $cpmk)
                        <span class="badge-cpmk">{{ $cpmk }}</span>
                    @empty
                        -
                    @endforelse
                </td>
            </tr>
        </table>

        {{-- PETUNJUK PENGERJAAN --}}
        <div class="petunjuk-box">
            <strong>Petunjuk Pengerjaan:</strong>
            <ol>
                <li>Awali pengerjaan ujian dengan membaca doa sesuai keyakinan masing-masing.</li>
                <li>Tuliskan identitas (Nama, NPM, dan Mata Kuliah) secara lengkap dan jelas pada lembar jawaban.</li>
                <li>Bacalah setiap butir pertanyaan dengan saksama sebelum mulai menjawab.</li>
                <li>Dilarang keras melakukan kecurangan atau bekerjasama dalam bentuk apa pun selama ujian berlangsung.</li>
                <li>Periksa kembali kelengkapan seluruh jawaban Anda sebelum mengumpulkannya kepada pengawas ujian.</li>
            </ol>
        </div>

        {{-- DAFTAR BUTIR SOAL --}}
        <div class="soal-list">
            @forelse($soals as $index => $soal)
                <div class="soal-item">
                    <div class="soal-meta">
                        <span>
                            <strong>Soal No. {{ $index + 1 }}</strong>
                            @if(!empty($soal->cpmk_kode))
                                &nbsp;&bull;&nbsp; <span class="badge-cpmk">{{ $soal->cpmk_kode }}</span>
                            @endif
                            @if(!empty($soal->cpl_kode))
                                <span class="badge-cpmk" style="background:#e0f2fe;">{{ $soal->cpl_kode }}</span>
                            @endif
                        </span>
                        @if(isset($persenSoal[$soal->id]))
                            <span style="font-weight: normal; color: #475569;">
                                Bobot CPMK: <strong>{{ $persenSoal[$soal->id] }}%</strong>
                            </span>
                        @endif
                    </div>
                    <div class="soal-body">
                        {!! $soal->pertanyaan ?? '<span style="color:#666; font-style:italic;">Pertanyaan belum diisi.</span>' !!}
                    </div>
                </div>
            @empty
                <div style="text-align: center; color: #64748b; padding: 30px;">
                    <em>Tidak ada butir soal yang dipilih.</em>
                </div>
            @endforelse
        </div>

        {{-- LEMBAR VALIDASI & PENGESAHAN --}}
        <table class="pengesahan-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>Ketua Program Studi</strong>
                    <div class="ttd-space"></div>
                    <div class="ttd-nama">{{ !empty($rps->kaprodi) ? $rps->kaprodi : '( ............................................................ )' }}</div>
                    <div>NIP. ......................................................</div>
                </td>
                <td>
                    Bandar Lampung, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    <strong>Dosen Pengampu Mata Kuliah</strong>
                    <div class="ttd-space"></div>
                    <div class="ttd-nama">{{ $dosenNama }}</div>
                    <div>NIP/NIDN. ......................................................</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
