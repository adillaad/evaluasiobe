<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <title>Laporan Visualisasi Angkatan {{ $angkatan }}</title>
    <style>
        @page {
            margin: 15mm 12mm 15mm 12mm;
        }

        body {
            font-family: "DejaVu Sans", "Helvetica Neue", Arial, sans-serif;
            font-size: 8.5pt;
            line-height: 1.4;
            color: #222;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #2980b9;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .header h2 {
            margin: 0 0 4px;
            font-size: 13pt;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header h3 {
            margin: 0;
            font-size: 10pt;
            font-weight: normal;
            color: #555;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .info-table td {
            padding: 5px 10px;
            font-size: 8.5pt;
            vertical-align: middle;
        }

        .info-label {
            width: 18%;
            font-weight: bold;
            color: #495057;
        }

        .info-value {
            width: 32%;
            color: #212529;
        }

        .metric-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
            margin-bottom: 14px;
        }

        .metric-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 10px;
            text-align: center;
        }

        .metric-label {
            font-size: 7.5pt;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .metric-value-primary {
            font-size: 11.5pt;
            font-weight: bold;
            color: #1F3BB3;
        }

        .metric-value-success {
            font-size: 11.5pt;
            font-weight: bold;
            color: #16a34a;
        }

        .metric-value-danger {
            font-size: 11.5pt;
            font-weight: bold;
            color: #dc2626;
        }

        .section-box {
            margin-bottom: 16px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 9.5pt;
            font-weight: bold;
            color: #1a5276;
            background-color: #ebf5fb;
            padding: 5px 8px;
            border-left: 4px solid #2980b9;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .summary-box {
            background-color: #fcfcfc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 10px;
            margin-bottom: 10px;
            font-size: 8pt;
            color: #333;
            line-height: 1.45;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 8pt;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #bdc3c7;
            padding: 4px 6px;
            text-align: left;
        }

        .data-table th {
            background-color: #f2f4f4;
            color: #2c3e50;
            font-weight: bold;
            text-align: center;
        }

        .data-table tr:nth-child(even) {
            background-color: #fafafa;
        }

        .chart-container {
            text-align: center;
            margin: 8px 0;
            page-break-inside: avoid;
        }

        .chart-container img {
            max-width: 85%;
            max-height: 270px;
            height: auto;
            border: 1px solid #e0e0e0;
            padding: 4px;
            background: #fff;
        }

        .badge-success {
            color: #1e8449;
            font-weight: bold;
        }

        .badge-warning {
            color: #d35400;
            font-weight: bold;
        }

        .badge-danger {
            color: #c0392b;
            font-weight: bold;
        }

        .badge-info {
            color: #2980b9;
            font-weight: bold;
        }

        ol, ul {
            margin: 4px 0 8px 18px;
            padding: 0;
            font-size: 8pt;
        }

        li {
            margin-bottom: 3px;
        }

        .matrix-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
        }

        .matrix-table th,
        .matrix-table td {
            border: 1px solid #bdc3c7;
            padding: 3px 4px;
            text-align: center;
        }

        .matrix-table th {
            background-color: #eaf2f8;
            color: #1b4f72;
            font-weight: bold;
        }

        .matrix-table tr:nth-child(even) {
            background-color: #fdfefe;
        }

        .footer {
            margin-top: 15px;
            border-top: 1px solid #ccc;
            padding-top: 5px;
            text-align: center;
            font-size: 7.5pt;
            color: #7f8c8d;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <h2>Laporan Visualisasi Capaian Pembelajaran Lulusan (CPL) Angkatan</h2>
        <h3>{{ $universitas }} &bull; Program Studi {{ $prodi }}</h3>
    </div>

    {{-- INFORMASI ANGKATAN & PERIODE --}}
    @php
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
            $activePeriodLabel = "Tahun Ajaran {$calYear}/{$nextYear} (Semester {$sem1} & {$sem2})";
        }
    @endphp
    <table class="info-table">
        <tr>
            <td class="info-label">Angkatan</td>
            <td class="info-value"><strong>{{ $angkatan }}</strong></td>
            <td class="info-label">Program Studi</td>
            <td class="info-value">{{ $prodi }}</td>
        </tr>
        <tr>
            <td class="info-label">Tanggal Cetak</td>
            <td class="info-value">{{ date('d F Y') }}</td>
            <td class="info-label">Periode</td>
            <td class="info-value"><strong style="color: #1a5276;">{{ $activePeriodLabel }}</strong></td>
        </tr>
    </table>

    {{-- EXECUTIVE SUMMARY METRICS --}}
    @if (!empty($avgCpl) || !empty($maxCpl) || !empty($minCpl))
        <table class="metric-table">
            <tr>
                <td style="width: 33.33%;">
                    <div class="metric-card">
                        <div class="metric-label">Rata-rata Ketercapaian CPL</div>
                        <div class="metric-value-primary">{{ $avgCpl ?? '-' }}</div>
                    </div>
                </td>
                <td style="width: 33.33%;">
                    <div class="metric-card">
                        <div class="metric-label">CPL Tertinggi</div>
                        <div class="metric-value-success">{{ $maxCpl ?? '-' }}</div>
                    </div>
                </td>
                <td style="width: 33.33%;">
                    <div class="metric-card">
                        <div class="metric-label">CPL Terendah</div>
                        <div class="metric-value-danger">{{ $minCpl ?? '-' }}</div>
                    </div>
                </td>
            </tr>
        </table>
    @endif

    {{-- 1. CPL ACHIEVEMENT PERCENTAGE (BATCH) --}}
    <div class="section-box">
        <div class="section-title">1. CPL Achievement Percentage (%) (Batch) - Capaian CPL Angkatan</div>

        @if (!empty($radarChartAngkatanImg))
            <div class="chart-container">
                <img src="{{ $radarChartAngkatanImg }}">
            </div>
        @endif

        {{-- Ringkasan Evaluasi / Summary --}}
        @if (!empty($summary))
            <div style="font-weight: bold; margin-top: 4px; margin-bottom: 2px; font-size: 8pt; color: #2c3e50;">Ringkasan Evaluasi Capaian CPL Angkatan:</div>
            <div class="summary-box">
                {!! $summary !!}
            </div>
        @endif

        {{-- Tabel Rincian Capaian CPL Angkatan Per Tahun --}}
        @if (!empty($rincianCplPerTahun) && count($rincianCplPerTahun) > 0)
            <div style="font-weight: bold; margin-top: 6px; margin-bottom: 3px; font-size: 8pt; color: #2c3e50;">Rincian Ketercapaian CPL (%):</div>
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
                                <td style="text-align: center; font-weight: bold; color: {{ $sc === '-' ? '#95a5a6' : '#2c3e50' }}; {{ $sc === '-' ? 'background-color: #fcfcfc;' : '' }}">
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
                        <tr style="background-color: #ebf5fb; font-weight: bold;">
                            <td style="color: #1a5276; font-weight: bold; padding-left: 8px;">Rata-rata Ketercapaian</td>
                            @foreach ($footerAvgList as $avg)
                                <td style="text-align: center; color: #1a5276; font-weight: bold;">
                                    {{ $avg }}
                                </td>
                            @endforeach
                        </tr>
                    @endif
                </tbody>
            </table>
        @elseif (!empty($rincianCplScoresAngkatan) && count($rincianCplScoresAngkatan) > 0)
            {{-- Fallback 2 Kolom --}}
            <div style="font-weight: bold; margin-top: 6px; margin-bottom: 3px; font-size: 8pt; color: #2c3e50;">Rincian Ketercapaian CPL (%):</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30%;">Kode CPL</th>
                        <th style="width: 70%; text-align: center;">Ketercapaian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rincianCplScoresAngkatan as $item)
                        <tr>
                            <td style="font-weight: bold;">{{ $item['kode'] }}</td>
                            <td style="text-align: center; font-weight: bold; color: #2980b9;">{{ $item['avgScore'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Deskripsi CPL --}}
        @if (!empty($descriptions) && is_array($descriptions))
            <div style="font-weight: bold; margin-top: 6px; margin-bottom: 2px; font-size: 8pt;">Deskripsi Naratif CPL:</div>
            <ol>
                @foreach ($descriptions as $desc)
                    <li>{{ $desc }}</li>
                @endforeach
            </ol>
        @endif
    </div>

    {{-- 2. QUESTIONS WITH LOWEST CPL --}}
    @if (!empty($soalTerendah) && count($soalTerendah) > 0)
        <div class="section-box">
            <div class="section-title">2. Questions with Lowest CPL (Soal dengan Capaian Rata-rata Terendah)</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 28px;">No</th>
                        <th style="width: 28%;">Course Name</th>
                        <th style="width: 22%;">Assessment Type</th>
                        <th>Questions</th>
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

    {{-- 3. CPL CALCULATION COURSES --}}
    @if (!empty($courseList) && count($courseList) > 0)
        <div class="section-box">
            <div class="section-title">3. CPL Calculation Courses (Mata Kuliah Perhitungan CPL)</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 32px; text-align: center;">No</th>
                        <th style="width: 95px; text-align: center;">Kode MK</th>
                        <th>Nama Mata Kuliah</th>
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
                            <td style="text-align: center; font-weight: bold; color: #1F3BB3;">{{ $kode }}</td>
                            <td>{{ $nama }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- 4. MATRIKS CAPAIAN SELURUH MAHASISWA ANGKATAN --}}
    @if (!empty($mahasiswaAngkatan) && count($mahasiswaAngkatan) > 0)
        {{-- Matriks 1: Persentase Ketercapaian CPL (%) --}}
        <div class="section-box" style="page-break-before: auto;">
            <div class="section-title">4. Matriks Persentase Ketercapaian CPL (%) Seluruh Mahasiswa Angkatan {{ $angkatan }}</div>
            <table class="matrix-table">
                <thead>
                    <tr>
                        <th style="width: 24px;">No</th>
                        <th style="width: 70px;">NPM</th>
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
                            <td>{{ $student['npm'] }}</td>
                            <td style="text-align: left; padding-left: 6px;">{{ $student['nama_mhs'] }}</td>
                            @if (!empty($student['cpls']))
                                @foreach ($student['cpls'] as $cpl)
                                    @php
                                        $pVal = isset($cpl['persen']) ? (float)$cpl['persen'] : (isset($cpl['nilai']) ? (float)$cpl['nilai'] : 0);
                                    @endphp
                                    <td style="{{ $pVal >= 65 ? 'color: #1e8449; font-weight: bold;' : ($pVal > 0 ? 'color: #d35400;' : 'color: #888;') }}">
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
        <div class="section-box" style="page-break-before: auto; margin-top: 15px;">
            <div class="section-title">5. Matriks Skor Nilai Capaian CPL (0–100) Seluruh Mahasiswa Angkatan {{ $angkatan }}</div>
            <table class="matrix-table">
                <thead>
                    <tr>
                        <th style="width: 24px;">No</th>
                        <th style="width: 70px;">NPM</th>
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
                            <td>{{ $student['npm'] }}</td>
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

    {{-- FOOTER --}}
    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh Sistem Evaluasi OBE &bull; {{ $universitas }}<br>
        Dicetak pada: {{ date('d F Y H:i:s') }}
    </div>

</body>

</html>