<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <title>Laporan Evaluasi CPL Angkatan {{ $angkatan }}</title>
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
            margin-bottom: 12px;
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

        /* ── EXECUTIVE METRICS SUMMARY TABLE ── */
        .metric-summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .metric-summary-table td {
            border: 1px solid #444;
            padding: 6px 8px;
            text-align: center;
            background-color: #fbfbfb;
            width: 33.33%;
        }

        .metric-lbl {
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #444;
            margin-bottom: 2px;
        }

        .metric-val {
            font-size: 11.5pt;
            font-weight: bold;
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

        .data-table tr.avg-row td {
            background-color: #eef2f6;
            font-weight: bold;
            border-top: 1.5px solid #000;
        }

        /* ── MATRIX TABLES ── */
        .matrix-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-bottom: 8px;
        }

        .matrix-table th,
        .matrix-table td {
            border: 1px solid #555;
            padding: 3px 4px;
            text-align: center;
            vertical-align: middle;
        }

        .matrix-table th {
            background-color: #e2e8f0;
            color: #000;
            font-weight: bold;
        }

        /* ── CHARTS ── */
        .chart-box {
            text-align: center;
            margin: 8px auto 10px;
            page-break-inside: avoid;
        }

        .chart-box img {
            max-width: 80%;
            max-height: 250px;
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

        $baseAngkatan = !empty($angkatan) ? (int)$angkatan : (int)date('Y');
        $activePeriodLabel = 'Kumulatif (Semua Semester)';

        if (!empty($semester) && $semester !== 'all') {
            $semNumber = (int)$semester;
            $yearOffset = (int)floor(($semNumber - 1) / 2);
            $calYear = $baseAngkatan + $yearOffset;
            $nextYear = $calYear + 1;
            $semType = ($semNumber % 2 === 1) ? 'Ganjil' : 'Genap';
            $activePeriodLabel = "Semester {$semNumber} ({$semType} {$calYear}/{$nextYear})";
        } elseif (!empty($tahun) && $tahun !== 'all') {
            $yearOffset = 0;
            $calYear = $baseAngkatan;
            if (is_numeric($tahun)) {
                $val = (int)$tahun;
                if ($val >= 2000) {
                    $yearOffset = $val - $baseAngkatan;
                    $calYear = $val;
                } else {
                    $yearOffset = $val - 1;
                    $calYear = $baseAngkatan + $yearOffset;
                }
            } elseif (str_contains($tahun, '/')) {
                $startYear = (int)explode('/', $tahun)[0];
                $yearOffset = $startYear - $baseAngkatan;
                $calYear = $startYear;
            }
            $nextYear = $calYear + 1;
            $sem1 = max(1, $yearOffset * 2 + 1);
            $sem2 = max(2, $yearOffset * 2 + 2);
            $activePeriodLabel = "Tahun Akademik {$calYear}/{$nextYear} (Semester {$sem1} & {$sem2})";
        }
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
        <div class="doc-title">LAPORAN EVALUASI CAPAIAN PEMBELAJARAN LULUSAN (CPL) ANGKATAN {{ $angkatan }}</div>
        <div class="doc-subtitle">Sistem Evaluasi Berbasis Outcome-Based Education (OBE)</div>
    </div>

    {{-- METADATA ANGKATAN & PERIODE --}}
    <div class="info-box">
        <table class="info-table">
            <tr>
                <td class="info-label">Tahun Angkatan</td>
                <td class="info-sep">:</td>
                <td class="info-val"><strong>{{ $angkatan }}</strong></td>
                <td class="info-label">Program Studi</td>
                <td class="info-sep">:</td>
                <td class="info-val">{{ $prodi ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Periode Evaluasi</td>
                <td class="info-sep">:</td>
                <td class="info-val"><strong>{{ $activePeriodLabel }}</strong></td>
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
                <td class="info-val">Laporan Resmi OBE</td>
            </tr>
        </table>
    </div>

    {{-- RINGKASAN METRIK CAPAIAN CPL ANGKATAN --}}
    @if (!empty($avgCpl) || !empty($maxCpl) || !empty($minCpl))
        <table class="metric-summary-table">
            <tr>
                <td>
                    <div class="metric-lbl">Skor Capaian CPL</div>
                    <div class="metric-val">{{ $avgCpl ?? '-' }}</div>
                </td>
                <td>
                    <div class="metric-lbl">Capaian CPL Tertinggi</div>
                    <div class="metric-val">{{ $maxCpl ?? '-' }}</div>
                </td>
                <td>
                    <div class="metric-lbl">Capaian CPL Terendah</div>
                    <div class="metric-val">{{ $minCpl ?? '-' }}</div>
                </td>
            </tr>
        </table>
    @endif

    {{-- BAGIAN I: KETERCAPAIAN CPL ANGKATAN --}}
    <div class="section-box">
        <div class="section-header">I. Persentase Ketercapaian CPL Angkatan (CPL Achievement Percentage - Batch)</div>

        @if (!empty($radarChartAngkatanImg))
            <div class="chart-box">
                <img src="{{ $radarChartAngkatanImg }}" alt="Diagram Radar CPL Angkatan">
                <div class="figure-caption">Gambar 1. Diagram Radar Persentase Capaian CPL Rata-rata Angkatan {{ $angkatan }}</div>
            </div>
        @endif

        {{-- Ringkasan Evaluasi --}}
        @if (!empty($summary))
            <div style="font-weight: bold; margin-top: 6px; margin-bottom: 3px; font-size: 8pt;">Ringkasan Evaluasi Capaian CPL:</div>
            <div class="summary-box">
                {!! $summary !!}
            </div>
        @endif

        {{-- Tabel Rincian Capaian CPL Angkatan Per Tahun --}}
        @if (!empty($rincianCplPerTahun) && count($rincianCplPerTahun) > 0)
            <div style="font-weight: bold; margin-top: 6px; margin-bottom: 3px; font-size: 8pt;">Rincian Ketercapaian CPL (%):</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="min-width: 90px; text-align: left; padding-left: 8px;">Kode CPL</th>
                        @if (!empty($yearsHeader) && is_array($yearsHeader))
                            @foreach ($yearsHeader as $yr)
                                <th style="text-align: center;">{{ $yr }}</th>
                            @endforeach
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rincianCplPerTahun as $key => $item)
                        @php
                            $cplKode = is_array($item) && isset($item['kode']) ? $item['kode'] : (is_string($key) ? $key : '-');
                            $scoresList = [];
                            if (is_array($item) && isset($item['scores']) && is_array($item['scores'])) {
                                $scoresList = $item['scores'];
                            } elseif (is_array($item)) {
                                foreach (($yearsHeader ?? []) as $yr) {
                                    $val = $item[$yr] ?? null;
                                    $scoresList[] = ($val !== null && $val !== '') ? (is_numeric($val) ? round((float)$val, 2) . '%' : $val) : '-';
                                }
                            }
                        @endphp
                        <tr>
                            <td style="font-weight: bold; padding-left: 8px;">{{ $cplKode }}</td>
                            @foreach ($scoresList as $sc)
                                <td style="text-align: center; font-weight: bold; {{ $sc === '-' ? 'color: #888; background-color: #fafafa;' : '' }}">
                                    {{ $sc }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    @php
                        $footerAvgList = [];
                        if (!empty($yearlyAvg) && is_array($yearlyAvg)) {
                            $footerAvgList = $yearlyAvg;
                        } elseif (!empty($ketercapaianCplYearlyAvg) && is_array($ketercapaianCplYearlyAvg)) {
                            foreach (($yearsHeader ?? []) as $yr) {
                                $val = $ketercapaianCplYearlyAvg[$yr] ?? null;
                                $footerAvgList[] = ($val !== null && $val !== '') ? (is_numeric($val) ? round((float)$val, 2) . '%' : $val) : '-';
                            }
                        }
                    @endphp
                    @if (!empty($footerAvgList))
                        <tr class="avg-row">
                            <td style="font-weight: bold; padding-left: 8px;">Rata-rata Ketercapaian</td>
                            @foreach ($footerAvgList as $avg)
                                <td style="text-align: center; font-weight: bold;">
                                    {{ $avg }}
                                </td>
                            @endforeach
                        </tr>
                    @endif
                </tbody>
            </table>
        @elseif (!empty($rincianCplScoresAngkatan) && count($rincianCplScoresAngkatan) > 0)
            <div style="font-weight: bold; margin-top: 6px; margin-bottom: 3px; font-size: 8pt;">Rincian Ketercapaian CPL (%):</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30%; text-align: left; padding-left: 8px;">Kode CPL</th>
                        <th style="width: 70%; text-align: center;">Ketercapaian Rata-rata</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rincianCplScoresAngkatan as $item)
                        <tr>
                            <td style="font-weight: bold; padding-left: 8px;">{{ $item['kode'] }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ $item['avgScore'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if (!empty($descriptions) && is_array($descriptions))
            <div style="font-weight: bold; margin-top: 6px; margin-bottom: 3px; font-size: 8pt;">Deskripsi Naratif CPL:</div>
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
            <div class="section-header">II. Pertanyaan / Asesmen dengan Capaian CPL Terendah</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 28px;">No</th>
                        <th style="width: 28%; text-align: left;">Mata Kuliah</th>
                        <th style="width: 22%; text-align: center;">Jenis Asesmen</th>
                        <th style="text-align: left;">Pertanyaan / Deskripsi Soal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($soalTerendah as $row)
                        <tr>
                            <td style="text-align: center;">{{ $row['no'] }}</td>
                            <td>{{ $row['course_name'] }}</td>
                            <td style="text-align: center;">{{ $row['types_of_assessment'] }}</td>
                            <td>{{ $row['question'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- BAGIAN III: MATA KULIAH PERHITUNGAN CPL --}}
    @if (!empty($courseList) && count($courseList) > 0)
        <div class="section-box">
            <div class="section-header">III. Mata Kuliah Perhitungan CPL (CPL Calculation Courses)</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 32px; text-align: center;">No</th>
                        <th style="width: 95px; text-align: center;">Kode MK</th>
                        <th style="text-align: left;">Nama Mata Kuliah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($courseList as $idx => $cl)
                        @php
                            if (is_array($cl)) {
                                $kode = $cl['kode'] ?? ($cl['code'] ?? '-');
                                $nama = $cl['nama'] ?? ($cl['name'] ?? '-');
                            } else {
                                $parts = explode(' - ', $cl, 2);
                                $kode = isset($parts[1]) ? trim($parts[0]) : '-';
                                $nama = isset($parts[1]) ? trim($parts[1]) : trim($cl);
                            }
                        @endphp
                        <tr>
                            <td style="text-align: center;">{{ $idx + 1 }}</td>
                            <td style="text-align: center; font-family: monospace; font-weight: bold;">{{ $kode }}</td>
                            <td>{{ $nama }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- BAGIAN IV & V: MATRIKS CAPAIAN SELURUH MAHASISWA ANGKATAN --}}
    @if (!empty($mahasiswaAngkatan) && count($mahasiswaAngkatan) > 0)
        {{-- Matriks 1: Persentase Ketercapaian CPL (%) --}}
        <div class="section-box">
            <div class="section-header">IV. Matriks Persentase Ketercapaian CPL (%) Seluruh Mahasiswa Angkatan {{ $angkatan }}</div>
            <table class="matrix-table">
                <thead>
                    <tr>
                        <th style="width: 24px;">No</th>
                        <th style="width: 75px;">NPM</th>
                        <th style="text-align: left; padding-left: 6px;">Nama Mahasiswa</th>
                        @if (!empty($cplCodes))
                            @foreach ($cplCodes as $cplCode)
                                <th style="width: 42px;">{{ $cplCode }}</th>
                            @endforeach
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mahasiswaAngkatan as $student)
                        <tr>
                            <td>{{ $student['no'] }}</td>
                            <td style="font-family: monospace;">{{ $student['npm'] }}</td>
                            <td style="text-align: left; padding-left: 6px;">{{ $student['nama_mhs'] }}</td>
                            @if (!empty($student['cpls']))
                                @foreach ($student['cpls'] as $cpl)
                                    @php
                                        $pVal = isset($cpl['persen']) ? (float)$cpl['persen'] : (isset($cpl['nilai']) ? (float)$cpl['nilai'] : 0);
                                    @endphp
                                    <td style="{{ $pVal >= 65 ? 'font-weight: bold;' : '' }}">
                                        {{ number_format($pVal, 1) }}%
                                    </td>
                                @endforeach
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Matriks 2: Skor Nilai Capaian CPL (0–100) --}}
        <div class="section-box" style="margin-top: 14px;">
            <div class="section-header">V. Matriks Skor Nilai Capaian CPL (0–100) Seluruh Mahasiswa Angkatan {{ $angkatan }}</div>
            <table class="matrix-table">
                <thead>
                    <tr>
                        <th style="width: 24px;">No</th>
                        <th style="width: 75px;">NPM</th>
                        <th style="text-align: left; padding-left: 6px;">Nama Mahasiswa</th>
                        @if (!empty($cplCodes))
                            @foreach ($cplCodes as $cplCode)
                                <th style="width: 42px;">{{ $cplCode }}</th>
                            @endforeach
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mahasiswaAngkatan as $student)
                        <tr>
                            <td>{{ $student['no'] }}</td>
                            <td style="font-family: monospace;">{{ $student['npm'] }}</td>
                            <td style="text-align: left; padding-left: 6px;">{{ $student['nama_mhs'] }}</td>
                            @if (!empty($student['cpls']))
                                @foreach ($student['cpls'] as $cpl)
                                    @php
                                        $sVal = isset($cpl['skor']) ? (float)$cpl['skor'] : (isset($cpl['nilai']) ? (float)$cpl['nilai'] : 0);
                                    @endphp
                                    <td style="{{ $sVal >= 65 ? 'font-weight: bold;' : '' }}">
                                        {{ number_format($sVal, 1) }}
                                    </td>
                                @endforeach
                            @endif
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
                    <div style="font-weight: bold; color: #000; margin-bottom: 2px;">Catatan Akademik Angkatan:</div>
                    <div>1. Laporan ini merangkum ketercapaian kompetensi lulusan seluruh mahasiswa angkatan {{ $angkatan }}.</div>
                    <div>2. Digunakan sebagai dasar monitoring, perbaikan mutu pembelajaran berkelanjutan (CQI), dan pelaporan akreditasi.</div>
                    <div>3. Disahkan secara resmi oleh Pengelola Program Studi {{ $prodi ?? '' }}.</div>
                </td>
                <td style="width: 45%; text-align: center; font-size: 8.5pt;">
                    <div>Bandar Lampung, {{ $tglCetak }}</div>
                    <div style="margin-top: 2px;">Mengetahui,</div>
                    <div style="font-weight: bold;">Ketua / Koordinator Program Studi</div>
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