<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <title>Laporan Visualisasi Mahasiswa - {{ $nama }}</title>
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
            margin-bottom: 14px;
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

        .disclaimer-box {
            background-color: #ebf5fb;
            border-left: 4px solid #2980b9;
            padding: 6px 10px;
            font-size: 7.5pt;
            color: #2c3e50;
            margin-top: 6px;
            margin-bottom: 6px;
            line-height: 1.35;
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
        <h2>Laporan Visualisasi Capaian Pembelajaran Lulusan (CPL)</h2>
        <h3>{{ $universitas }} &bull; Program Studi {{ $prodi }}</h3>
    </div>

    {{-- INFORMASI MAHASISWA & PROGRAM STUDI --}}
    @php
        $yearsList = $yearsList ?? [];
        $skorCplPerTahun = $skorCplPerTahun ?? [];
        $ketercapaianCplPerTahun = $ketercapaianCplPerTahun ?? [];
        $skorCplOverall = $skorCplOverall ?? [];
        $ketercapaianCplOverall = $ketercapaianCplOverall ?? [];
        $skorCplYearlyAvg = $skorCplYearlyAvg ?? [];
        $ketercapaianCplYearlyAvg = $ketercapaianCplYearlyAvg ?? [];
        $skorCplGrandAvg = $skorCplGrandAvg ?? 0;
        $ketercapaianCplGrandAvg = $ketercapaianCplGrandAvg ?? 0;
        $jenjang = $jenjang ?? 'S1';
        $maxYears = $maxYears ?? 7;
        $disclaimerMasaStudi = $disclaimerMasaStudi ?? '';
        $mataKuliahLulus = $mataKuliahLulus ?? [];
        $mataKuliahTidakLulus = $mataKuliahTidakLulus ?? [];
        $soalTerendah = $soalTerendah ?? [];
        $courseList = $courseList ?? [];
        $hasilProfil = $hasilProfil ?? [];
    @endphp
    <table class="info-table">
        <tr>
            <td class="info-label">Nama Mahasiswa</td>
            <td class="info-value"><strong>{{ $nama }}</strong></td>
            <td class="info-label">Program Studi</td>
            <td class="info-value">{{ $prodi }}</td>
        </tr>
        <tr>
            <td class="info-label">NPM</td>
            <td class="info-value">{{ $npm }}</td>
            <td class="info-label">Angkatan</td>
            <td class="info-value">{{ $angkatan }}</td>
        </tr>
        <tr>
            <td class="info-label">Tanggal Cetak</td>
            <td class="info-value">{{ date('d F Y') }}</td>
            <td class="info-label">Jenjang</td>
            <td class="info-value"><strong style="color: #1a5276;">{{ $jenjang }}</strong></td>
        </tr>
    </table>

    {{-- 1. ACHIEVEMENT OF CPL SCORES --}}
    <div class="section-box">
        <div class="section-title">1. Achievement of CPL Scores</div>

        @if (!empty($radarChartImg))
            <div class="chart-container">
                <img src="{{ $radarChartImg }}">
            </div>
        @endif

        {{-- Tabel Skor Capaian CPL Per Tahun --}}
        @if (!empty($skorCplPerTahun) && count($skorCplPerTahun) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="min-width: 80px; text-align: left;">Kode CPL</th>
                        @foreach ($yearsList as $yr)
                            @php $hasDataYr = $yearsWithData[$yr] ?? true; @endphp
                            @if ($hasDataYr)
                                <th style="text-align: center;">{{ $yr }}</th>
                            @else
                                <th style="text-align: center; color: #95a5a6; background-color: #f8f9f9;" title="Belum Ditempuh">{{ $yr }}</th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($skorCplPerTahun as $cplKode => $scores)
                        <tr>
                            <td style="font-weight: bold;">{{ $cplKode }}</td>
                            @foreach ($yearsList as $yr)
                                @php $hasDataYr = $yearsWithData[$yr] ?? true; @endphp
                                @if ($hasDataYr && isset($scores[$yr]) && $scores[$yr] !== null)
                                    <td style="text-align: center; font-weight: bold; color: #555;">
                                        {{ round($scores[$yr], 2) }}
                                    </td>
                                @else
                                    <td style="text-align: center; color: #95a5a6; background-color: #f8f9f9;">-</td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                    @if (!empty($skorCplYearlyAvg) && count($skorCplYearlyAvg) > 0)
                        <tr style="background-color: #ebf5fb; font-weight: bold;">
                            <td style="color: #1a5276; font-weight: bold;">Rata-rata Skor</td>
                            @foreach ($yearsList as $yr)
                                @php $hasDataYr = $yearsWithData[$yr] ?? true; @endphp
                                @if ($hasDataYr && isset($skorCplYearlyAvg[$yr]) && $skorCplYearlyAvg[$yr] !== null)
                                    <td style="text-align: center; color: #1a5276; font-weight: bold;">
                                        {{ round($skorCplYearlyAvg[$yr], 2) }}
                                    </td>
                                @else
                                    <td style="text-align: center; color: #95a5a6; background-color: #f8f9f9;">-</td>
                                @endif
                            @endforeach
                        </tr>
                    @endif
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

    {{-- 2. ACHIEVEMENT OF CPL --}}
    <div class="section-box">
        <div class="section-title">2. Achievement of CPL</div>

        @if (!empty($radarChartCapaianCplImg))
            <div class="chart-container">
                <img src="{{ $radarChartCapaianCplImg }}">
            </div>
        @endif

        {{-- Tabel Ketercapaian CPL (%) Per Tahun --}}
        @if (!empty($ketercapaianCplPerTahun) && count($ketercapaianCplPerTahun) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="min-width: 80px; text-align: left;">Kode CPL</th>
                        @foreach ($yearsList as $yr)
                            @php $hasDataYr = $yearsWithData[$yr] ?? true; @endphp
                            @if ($hasDataYr)
                                <th style="text-align: center;">{{ $yr }}</th>
                            @else
                                <th style="text-align: center; color: #95a5a6; background-color: #f8f9f9;" title="Belum Ditempuh">{{ $yr }}</th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ketercapaianCplPerTahun as $cplKode => $pcts)
                        <tr>
                            <td style="font-weight: bold;">{{ $cplKode }}</td>
                            @foreach ($yearsList as $yr)
                                @php $hasDataYr = $yearsWithData[$yr] ?? true; @endphp
                                @if ($hasDataYr && isset($pcts[$yr]) && $pcts[$yr] !== null)
                                    <td style="text-align: center; font-weight: bold; color: #555;">
                                        {{ round($pcts[$yr], 2) }}%
                                    </td>
                                @else
                                    <td style="text-align: center; color: #95a5a6; background-color: #f8f9f9;">-</td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                    @if (!empty($ketercapaianCplYearlyAvg) && count($ketercapaianCplYearlyAvg) > 0)
                        <tr style="background-color: #ebf5fb; font-weight: bold;">
                            <td style="color: #1a5276; font-weight: bold;">Rata-rata Ketercapaian</td>
                            @foreach ($yearsList as $yr)
                                @php $hasDataYr = $yearsWithData[$yr] ?? true; @endphp
                                @if ($hasDataYr && isset($ketercapaianCplYearlyAvg[$yr]) && $ketercapaianCplYearlyAvg[$yr] !== null)
                                    <td style="text-align: center; color: #1a5276; font-weight: bold;">
                                        {{ round($ketercapaianCplYearlyAvg[$yr], 2) }}%
                                    </td>
                                @else
                                    <td style="text-align: center; color: #95a5a6; background-color: #f8f9f9;">-</td>
                                @endif
                            @endforeach
                        </tr>
                    @endif
                </tbody>
            </table>
        @endif
    </div>

    {{-- 3. STATUS KELULUSAN MATA KULIAH --}}
    <div class="section-box">
        <div class="section-title">3. Status Mata Kuliah (Lulus & Tidak Lulus)</div>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px;">
            <tr>
                {{-- Kolom Kiri: Mata Kuliah Lulus --}}
                <td style="width: 50%; vertical-align: top; padding-right: 6px;">
                    <div style="font-weight: bold; margin-bottom: 4px; color: #1e8449;">&bull; Mata Kuliah Lulus:</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 25px; text-align: center;">#</th>
                                <th style="width: 65px; text-align: center;">Kode MK</th>
                                <th>Nama Mata Kuliah</th>
                                <th style="width: 45px; text-align: center;">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mataKuliahLulus as $mk)
                                <tr>
                                    <td style="text-align: center;">{{ $mk['no'] ?? $loop->iteration }}</td>
                                    <td style="text-align: center;">{{ $mk['courseCode'] ?? ($mk['kode'] ?? '') }}</td>
                                    <td>{{ $mk['courseName'] ?? ($mk['nama'] ?? '') }}</td>
                                    <td style="text-align: center; font-weight: bold; color: #1e8449;">{{ $mk['nilai'] ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #888;">Data Kosong</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>

                {{-- Kolom Kanan: Mata Kuliah Tidak Lulus --}}
                <td style="width: 50%; vertical-align: top; padding-left: 6px;">
                    <div style="font-weight: bold; margin-bottom: 4px; color: #c0392b;">&bull; Mata Kuliah Tidak Lulus:</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 25px; text-align: center;">#</th>
                                <th style="width: 65px; text-align: center;">Kode MK</th>
                                <th>Nama Mata Kuliah</th>
                                <th style="width: 45px; text-align: center;">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mataKuliahTidakLulus as $mk)
                                <tr>
                                    <td style="text-align: center;">{{ $mk['no'] ?? $loop->iteration }}</td>
                                    <td style="text-align: center;">{{ $mk['courseCode'] ?? ($mk['kode'] ?? '') }}</td>
                                    <td>{{ $mk['courseName'] ?? ($mk['nama'] ?? '') }}</td>
                                    <td style="text-align: center; font-weight: bold; color: #c0392b;">{{ $mk['nilai'] ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #888;">Data Kosong</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- 4. QUESTIONS WITH LOWEST CPL --}}
    @if (!empty($soalTerendah) && count($soalTerendah) > 0)
        <div class="section-box">
            <div class="section-title">4. Questions with Lowest CPL (Soal dengan Capaian CPL Terendah)</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 28px;">No</th>
                        <th style="width: 28%;">Course Name</th>
                        <th style="width: 22%;">Types of Assessment</th>
                        <th>Questions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($soalTerendah as $row)
                        <tr>
                            <td style="text-align: center;">{{ $row['no'] ?? $loop->iteration }}</td>
                            <td>{{ $row['course_name'] ?? $row['namaCourse'] ?? '-' }}</td>
                            <td>{{ $row['types_of_assessment'] ?? $row['Jenis'] ?? '-' }}</td>
                            <td>{{ $row['question'] ?? $row['soal'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- 5. ALL COURSES TAKEN --}}
    @if (!empty($courseList) && count($courseList) > 0)
        <div class="section-box">
            <div class="section-title">5. Data Mata Kuliah yang Telah Ditempuh (All Courses Taken)</div>
            <ol>
                @foreach ($courseList as $cl)
                    <li>{{ $cl }}</li>
                @endforeach
            </ol>
        </div>
    @endif

    {{-- 6. CAREER MAPPING DETAILS BASED ON CPL --}}
    <div class="section-box">
        <div class="section-title">6. Career Mapping Details Based on CPL (Pemetaan Profil Lulusan & Karir)</div>

        @if (!empty($profilChartImg))
            <div class="chart-container">
                <div style="font-weight: bold; margin-bottom: 4px; font-size: 8pt; color: #555;">Grafik Pemetaan Profil Karir Lulusan:</div>
                <img src="{{ $profilChartImg }}">
            </div>
        @endif

        @if (!empty($hasilProfil) && count($hasilProfil) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 25px;">No</th>
                        <th style="width: 20%;">Profile Career</th>
                        <th style="width: 25%;">Graduate Profile</th>
                        <th style="width: 45px; text-align: center;">CPL</th>
                        <th style="width: 50px; text-align: center;">Weight</th>
                        <th style="width: 50px; text-align: center;">Result</th>
                        <th style="width: 60px; text-align: center;">Weight * Result</th>
                        <th style="width: 50px; text-align: center;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hasilProfil as $row)
                        <tr>
                            @if (isset($row['is_first_row']) && $row['is_first_row'])
                                <td rowspan="{{ $row['total_rows'] }}" style="text-align: center; vertical-align: middle;">{{ $row['no'] }}</td>
                                <td rowspan="{{ $row['total_rows'] }}" style="vertical-align: middle; font-weight: bold;">{{ $row['profile_career'] }}</td>
                                <td rowspan="{{ $row['total_rows'] }}" style="vertical-align: middle;">{{ $row['graduate_profile'] }}</td>
                            @endif
                            <td style="text-align: center;">{{ $row['cpl'] }}</td>
                            <td style="text-align: center;">{{ $row['profile_weight'] }}</td>
                            <td style="text-align: center;">{{ $row['cpl_result'] }}</td>
                            <td style="text-align: center;">{{ $row['profile_weight_cpl_result'] }}</td>
                            @if (isset($row['is_first_row']) && $row['is_first_row'])
                                <td rowspan="{{ $row['total_rows'] }}" style="text-align: center; vertical-align: middle; font-weight: bold; color: #1a5276;">{{ $row['total'] }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh Sistem Evaluasi OBE &bull; {{ $universitas }}<br>
        Dicetak pada: {{ date('d F Y H:i:s') }}
    </div>

</body>

</html>
