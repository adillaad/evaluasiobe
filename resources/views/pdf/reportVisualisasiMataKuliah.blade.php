<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <title>Laporan Evaluasi CPMK Mata Kuliah {{ $course }}</title>
    <style>
        @page {
            margin: 12mm 12mm 15mm 12mm;
            size: A4 portrait;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 8.5pt;
            line-height: 1.35;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* ── KOP SURAT RESMI ── */
        .kop {
            margin-bottom: 10px;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-table td {
            vertical-align: middle;
            padding: 0;
        }

        .kop-logo {
            width: 70px;
            text-align: center;
        }

        .kop-logo img {
            width: 65px;
            height: auto;
        }

        .kop-text {
            text-align: center;
            padding-left: 8px;
        }

        .kementerian {
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.15;
        }

        .universitas {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 2px 0;
            letter-spacing: 0.5px;
        }

        .prodi-title {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .alamat,
        .kontak {
            font-size: 7.5pt;
            line-height: 1.2;
            color: #222;
        }

        .kop-line {
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
            height: 2px;
            margin-top: 6px;
            margin-bottom: 12px;
        }

        /* ── JUDUL DOKUMEN ── */
        .doc-title-container {
            text-align: center;
            margin-bottom: 12px;
        }

        .doc-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: underline;
        }

        .doc-subtitle {
            font-size: 8.5pt;
            margin-top: 2px;
            color: #333;
            letter-spacing: 0.3px;
        }

        /* ── METADATA / INFO TABLE ── */
        .info-box {
            width: 100%;
            border: 1px solid #333;
            background-color: #fafafa;
            margin-bottom: 14px;
            padding: 3px 4px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 3px 6px;
            font-size: 8.5pt;
            vertical-align: top;
        }

        .info-label {
            width: 18%;
            font-weight: bold;
            color: #111;
        }

        .info-sep {
            width: 2%;
            text-align: center;
            font-weight: bold;
        }

        .info-val {
            width: 30%;
            color: #000;
        }

        /* ── SECTION HEADERS ── */
        .section-box {
            margin-bottom: 14px;
            page-break-inside: avoid;
        }

        .section-header {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #f1f5f9;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            border-left: 4px solid #1a365d;
            padding: 4px 8px;
            margin-top: 12px;
            margin-bottom: 8px;
            page-break-after: avoid;
        }

        .summary-box {
            border: 1px solid #666;
            background-color: #fcfcfc;
            padding: 6px 10px;
            margin-bottom: 8px;
            font-size: 8pt;
            line-height: 1.4;
            color: #222;
        }

        /* ── DATA TABLES ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 8pt;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #444;
            padding: 4px 6px;
            vertical-align: middle;
        }

        .data-table th {
            background-color: #e5e9ee;
            color: #000;
            font-weight: bold;
            text-align: center;
            font-size: 8pt;
        }

        /* ── CHARTS ── */
        .chart-box {
            text-align: center;
            margin: 8px auto 10px;
            page-break-inside: avoid;
        }

        .chart-box img {
            max-width: 80%;
            max-height: 260px;
            height: auto;
            border: 1px solid #777;
            padding: 4px;
            background: #fff;
        }

        .figure-caption {
            font-size: 7.5pt;
            font-style: italic;
            color: #333;
            margin-top: 3px;
            text-align: center;
        }

        ol,
        ul {
            margin: 4px 0 6px 18px;
            padding: 0;
            font-size: 8pt;
        }

        li {
            margin-bottom: 2.5px;
            line-height: 1.3;
        }

        /* ── TANDA TANGAN / PENGESAHAN ── */
        .signature-section {
            page-break-inside: avoid;
            margin-top: 20px;
            width: 100%;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            vertical-align: top;
        }

        /* ── FOOTER ── */
        .footer {
            margin-top: 15px;
            border-top: 1px solid #888;
            padding-top: 4px;
            font-size: 7pt;
            color: #555;
        }
    </style>
</head>

<body>

    @php
        $logoPath = public_path('assets/img/logo_unila.png');
        $logoBase64 = null;
        if (file_exists($logoPath)) {
            $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
            $data = file_get_contents($logoPath);
            $mime = in_array($ext, ['png', 'jpg', 'jpeg']) ? $ext : 'png';
            $logoBase64 = 'data:image/' . $mime . ';base64,' . base64_encode($data);
        }

        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $tglCetak = date('d') . ' ' . ($bulanIndo[(int)date('m')] ?? date('F')) . ' ' . date('Y');

        $rincianCpmkAngkatan = $rincianCpmkAngkatan ?? [];
        $descriptions = $descriptions ?? [];
        $soalTerendah = $soalTerendah ?? [];
    @endphp

    {{-- KOP SURAT RESMI --}}
    <div class="kop">
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    @if ($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo Unila">
                    @endif
                </td>
                <td class="kop-text">
                    <div class="kementerian">KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</div>
                    <div class="universitas">{{ strtoupper($universitas ?? 'UNIVERSITAS LAMPUNG') }}</div>
                    <div class="prodi-title">PROGRAM STUDI {{ strtoupper($prodi ?? 'ILMU KOMPUTER') }}</div>
                    <div class="alamat">Jalan Prof. Dr. Sumantri Brojonegoro No. 1, Bandar Lampung 35145</div>
                    <div class="kontak">Telepon: (0721) 701609 &bull; Laman: www.unila.ac.id &bull; Surel: rektorat@unila.ac.id</div>
                </td>
            </tr>
        </table>
        <div class="kop-line"></div>
    </div>

    {{-- JUDUL DOKUMEN --}}
    <div class="doc-title-container">
        <div class="doc-title">LAPORAN EVALUASI CAPAIAN PEMBELAJARAN MATA KULIAH (CPMK)</div>
        <div class="doc-subtitle">Sistem Evaluasi Berbasis Outcome-Based Education (OBE)</div>
    </div>

    {{-- METADATA MATA KULIAH --}}
    <div class="info-box">
        <table class="info-table">
            <tr>
                <td class="info-label">Mata Kuliah</td>
                <td class="info-sep">:</td>
                <td class="info-val"><strong>{{ $course }}</strong></td>
                <td class="info-label">Program Studi</td>
                <td class="info-sep">:</td>
                <td class="info-val">{{ $prodi ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Angkatan / Kohor</td>
                <td class="info-sep">:</td>
                <td class="info-val"><strong>{{ $angkatan }}</strong></td>
                <td class="info-label">Universitas</td>
                <td class="info-sep">:</td>
                <td class="info-val">{{ $universitas ?? 'Universitas Lampung' }}</td>
            </tr>
            <tr>
                <td class="info-label">Tanggal Cetak</td>
                <td class="info-sep">:</td>
                <td class="info-val">{{ $tglCetak }}</td>
                <td class="info-label">Status Dokumen</td>
                <td class="info-sep">:</td>
                <td class="info-val">Laporan Resmi Asesmen MK</td>
            </tr>
        </table>
    </div>

    {{-- BAGIAN I: CAPAIAN CPMK ANGKATAN --}}
    <div class="section-box">
        <div class="section-header">I. Capaian CPMK Angkatan (CPMK Batch Achievement)</div>

        @if (!empty($radarChartAngkatanImg))
            <div class="chart-box">
                <img src="{{ $radarChartAngkatanImg }}" alt="Diagram Radar CPMK Angkatan">
                <div class="figure-caption">Gambar 1. Diagram Radar Capaian CPMK Mahasiswa Angkatan {{ $angkatan }} pada Mata Kuliah {{ $course }}</div>
            </div>
        @endif

        @if (!empty($summary))
            <div style="font-weight: bold; margin-top: 6px; margin-bottom: 3px; font-size: 8pt;">Ringkasan Evaluasi CPMK:</div>
            <div class="summary-box">
                {!! $summary !!}
            </div>
        @endif

        {{-- Tabel Rincian Capaian CPMK Angkatan --}}
        @if (!empty($rincianCpmkAngkatan) && count($rincianCpmkAngkatan) > 0)
            <div style="font-weight: bold; margin-top: 6px; margin-bottom: 3px; font-size: 8pt;">Rincian Nilai Capaian CPMK:</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 35%; text-align: left; padding-left: 8px;">Kode CPMK</th>
                        <th style="width: 65%; text-align: center;">Skor Rata-rata Capaian (0–100)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rincianCpmkAngkatan as $item)
                        <tr>
                            <td style="font-weight: bold; padding-left: 8px; font-family: monospace;">{{ $item['kode'] ?? '-' }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ $item['avgScore'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Deskripsi CPMK --}}
        @if (!empty($descriptions) && is_array($descriptions))
            <div style="font-weight: bold; margin-top: 6px; margin-bottom: 3px; font-size: 8pt;">Deskripsi Capaian Pembelajaran Mata Kuliah (CPMK):</div>
            <ol>
                @foreach ($descriptions as $desc)
                    <li>{{ $desc }}</li>
                @endforeach
            </ol>
        @endif
    </div>

    {{-- BAGIAN II: SOAL DENGAN CAPAIAN TERENDAH --}}
    @if (!empty($soalTerendah) && count($soalTerendah) > 0)
        <div class="section-box">
            <div class="section-header">II. Pertanyaan / Asesmen dengan Capaian CPMK Terendah</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 28px;">No</th>
                        <th style="width: 30%; text-align: center;">Jenis Asesmen</th>
                        <th style="text-align: left;">Pertanyaan / Deskripsi Soal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($soalTerendah as $row)
                        @php
                            $noVal = is_array($row) ? ($row['no'] ?? $loop->iteration) : ($row->no ?? $loop->iteration);
                            $jenisVal = is_array($row) ? ($row['types_of_assessment'] ?? ($row['Jenis'] ?? '-')) : ($row->types_of_assessment ?? ($row->Jenis ?? '-'));
                            $soalVal = is_array($row) ? ($row['question'] ?? ($row['soal'] ?? '-')) : ($row->question ?? ($row->soal ?? '-'));
                        @endphp
                        <tr>
                            <td style="text-align: center;">{{ $noVal }}</td>
                            <td style="text-align: center;">{{ $jenisVal }}</td>
                            <td>{{ $soalVal }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- TANDA TANGAN / PENGESAHAN RESMI --}}
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td style="width: 55%; font-size: 7.5pt; color: #444; line-height: 1.4; padding-right: 15px;">
                    <div style="font-weight: bold; color: #000; margin-bottom: 2px;">Catatan Evaluasi Mata Kuliah:</div>
                    <div>1. Laporan ini merupakan hasil evaluasi ketercapaian CPMK kelas/angkatan berdasarkan seluruh instrumen penilaian RPS.</div>
                    <div>2. Nilai rata-rata dan soal dengan nilai terendah digunakan sebagai bahan refleksi Continuous Quality Improvement (CQI) dosen pengampu.</div>
                    <div>3. Laporan ini resmi diverifikasi oleh Tim Kurikulum dan Penjaminan Mutu Program Studi.</div>
                </td>
                <td style="width: 45%; text-align: center; font-size: 8.5pt;">
                    <div>Bandar Lampung, {{ $tglCetak }}</div>
                    <div style="margin-top: 2px;">Mengetahui,</div>
                    <div style="font-weight: bold;">Dosen Pengampu / Koordinator MK</div>
                    <div style="height: 50px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;">( ______________________________ )</div>
                    <div style="margin-top: 2px;">NIP. ....................................................</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="text-align: left;">Sistem Informasi Evaluasi OBE &bull; {{ $universitas ?? 'Universitas Lampung' }}</td>
                <td style="text-align: right;">Dicetak pada: {{ date('d/m/Y H:i:s') }} WIB</td>
            </tr>
        </table>
    </div>

</body>

</html>
