<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <title>Laporan Evaluasi CPL Mahasiswa - {{ $nama ?? 'Mahasiswa' }}</title>
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

        .data-table tr.total-row td,
        .data-table tr.avg-row td {
            background-color: #eef2f6;
            font-weight: bold;
            border-top: 1.5px solid #000;
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

        /* ── STANDAR KELULUSAN & DESKRIPSI ── */
        .standard-box {
            border: 1px solid #555;
            background-color: #fcfcfc;
            padding: 5px 8px;
            font-size: 7.5pt;
            color: #222;
            margin-bottom: 8px;
            line-height: 1.35;
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

        $yearsList = $yearsList ?? [];
        $yearsWithData = $yearsWithData ?? [];
        $skorCplPerTahun = $skorCplPerTahun ?? [];
        $ketercapaianCplPerTahun = $ketercapaianCplPerTahun ?? [];
        $skorCplYearlyAvg = $skorCplYearlyAvg ?? [];
        $ketercapaianCplYearlyAvg = $ketercapaianCplYearlyAvg ?? [];
        $jenjang = $jenjang ?? 'S1';
        $mataKuliahLulus = $mataKuliahLulus ?? [];
        $mataKuliahTidakLulus = $mataKuliahTidakLulus ?? [];
        $soalTerendah = $soalTerendah ?? [];
        $courseList = $courseList ?? [];
        $hasilProfil = $hasilProfil ?? [];
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
        <div class="doc-title">LAPORAN EVALUASI CAPAIAN PEMBELAJARAN LULUSAN (CPL) INDIVIDU MAHASISWA</div>
        <div class="doc-subtitle">Sistem Evaluasi Berbasis Outcome-Based Education (OBE)</div>
    </div>

    {{-- METADATA MAHASISWA --}}
    <div class="info-box">
        <table class="info-table">
            <tr>
                <td class="info-label">Nama Mahasiswa</td>
                <td class="info-sep">:</td>
                <td class="info-val"><strong>{{ $nama ?? '-' }}</strong></td>
                <td class="info-label">Program Studi</td>
                <td class="info-sep">:</td>
                <td class="info-val">{{ $prodi ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Nomor Pokok Mhs (NPM)</td>
                <td class="info-sep">:</td>
                <td class="info-val">{{ $npm ?? '-' }}</td>
                <td class="info-label">Angkatan / Jenjang</td>
                <td class="info-sep">:</td>
                <td class="info-val">{{ $angkatan ?? '-' }} / {{ $jenjang }}</td>
            </tr>
            <tr>
                <td class="info-label">Universitas</td>
                <td class="info-sep">:</td>
                <td class="info-val">{{ $universitas ?? 'Universitas Lampung' }}</td>
                <td class="info-label">Tanggal Cetak</td>
                <td class="info-sep">:</td>
                <td class="info-val">{{ $tglCetak }}</td>
            </tr>
        </table>
    </div>

    {{-- BAGIAN I: SKOR KETERCAPAIAN CPL --}}
    <div class="section-box">
        <div class="section-header">I. Skor Capaian Pembelajaran Lulusan (CPL Scores)</div>

        @if (!empty($radarChartImg))
            <div class="chart-box">
                <img src="{{ $radarChartImg }}" alt="Diagram Radar Skor CPL">
                <div class="figure-caption">Gambar 1. Diagram Radar Capaian Skor Nilai CPL Mahasiswa</div>
            </div>
        @endif

        @if (!empty($skorCplPerTahun) && count($skorCplPerTahun) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="min-width: 90px; text-align: left; padding-left: 8px;">Kode CPL</th>
                        @foreach ($yearsList as $yr)
                            @php $hasDataYr = $yearsWithData[$yr] ?? true; @endphp
                            <th style="text-align: center; {{ !$hasDataYr ? 'color: #777; background-color: #f7f7f7;' : '' }}">
                                {{ $yr }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($skorCplPerTahun as $cplKode => $scores)
                        <tr>
                            <td style="font-weight: bold; padding-left: 8px;">{{ $cplKode }}</td>
                            @foreach ($yearsList as $yr)
                                @php $hasDataYr = $yearsWithData[$yr] ?? true; @endphp
                                @if ($hasDataYr && isset($scores[$yr]) && $scores[$yr] !== null)
                                    <td style="text-align: center; font-weight: bold;">
                                        {{ round($scores[$yr], 2) }}
                                    </td>
                                @else
                                    <td style="text-align: center; color: #888; background-color: #fafafa;">-</td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                    @if (!empty($skorCplYearlyAvg) && count($skorCplYearlyAvg) > 0)
                        <tr class="avg-row">
                            <td style="font-weight: bold; padding-left: 8px;">Rata-rata Skor</td>
                            @foreach ($yearsList as $yr)
                                @php $hasDataYr = $yearsWithData[$yr] ?? true; @endphp
                                @if ($hasDataYr && isset($skorCplYearlyAvg[$yr]) && $skorCplYearlyAvg[$yr] !== null)
                                    <td style="text-align: center; font-weight: bold;">
                                        {{ round($skorCplYearlyAvg[$yr], 2) }}
                                    </td>
                                @else
                                    <td style="text-align: center; color: #888; background-color: #fafafa;">-</td>
                                @endif
                            @endforeach
                        </tr>
                    @endif
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

    {{-- BAGIAN II: PERSENTASE KETERCAPAIAN CPL --}}
    <div class="section-box">
        <div class="section-header">II. Persentase Ketercapaian CPL (CPL Achievement Percentage)</div>

        @if (!empty($radarChartCapaianCplImg))
            <div class="chart-box">
                <img src="{{ $radarChartCapaianCplImg }}" alt="Diagram Radar Persentase CPL">
                <div class="figure-caption">Gambar 2. Diagram Radar Persentase Ketercapaian CPL Mahasiswa</div>
            </div>
        @endif

        @if (!empty($ketercapaianCplPerTahun) && count($ketercapaianCplPerTahun) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="min-width: 90px; text-align: left; padding-left: 8px;">Kode CPL</th>
                        @foreach ($yearsList as $yr)
                            @php $hasDataYr = $yearsWithData[$yr] ?? true; @endphp
                            <th style="text-align: center; {{ !$hasDataYr ? 'color: #777; background-color: #f7f7f7;' : '' }}">
                                {{ $yr }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ketercapaianCplPerTahun as $cplKode => $pcts)
                        <tr>
                            <td style="font-weight: bold; padding-left: 8px;">{{ $cplKode }}</td>
                            @foreach ($yearsList as $yr)
                                @php $hasDataYr = $yearsWithData[$yr] ?? true; @endphp
                                @if ($hasDataYr && isset($pcts[$yr]) && $pcts[$yr] !== null)
                                    <td style="text-align: center; font-weight: bold;">
                                        {{ round($pcts[$yr], 2) }}%
                                    </td>
                                @else
                                    <td style="text-align: center; color: #888; background-color: #fafafa;">-</td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                    @if (!empty($ketercapaianCplYearlyAvg) && count($ketercapaianCplYearlyAvg) > 0)
                        <tr class="avg-row">
                            <td style="font-weight: bold; padding-left: 8px;">Rata-rata Ketercapaian</td>
                            @foreach ($yearsList as $yr)
                                @php $hasDataYr = $yearsWithData[$yr] ?? true; @endphp
                                @if ($hasDataYr && isset($ketercapaianCplYearlyAvg[$yr]) && $ketercapaianCplYearlyAvg[$yr] !== null)
                                    <td style="text-align: center; font-weight: bold;">
                                        {{ round($ketercapaianCplYearlyAvg[$yr], 2) }}%
                                    </td>
                                @else
                                    <td style="text-align: center; color: #888; background-color: #fafafa;">-</td>
                                @endif
                            @endforeach
                        </tr>
                    @endif
                </tbody>
            </table>
        @endif
    </div>

    {{-- BAGIAN III: STATUS MATA KULIAH --}}
    <div class="section-box">
        <div class="section-header">III. Status Kelulusan Mata Kuliah (Lulus & Tidak Lulus)</div>

        <div class="standard-box">
            <strong>Standar Batas Kelulusan Mata Kuliah Berdasarkan Jenjang ({{ $jenjang }}):</strong>
            @if (in_array(strtoupper(trim($jenjang)), ['S3', 'S-3', 'DOKTOR']))
                Nilai &ge; 75.00 (Huruf Mutu Minimal B / Bobot 3.00)
            @elseif (in_array(strtoupper(trim($jenjang)), ['S2', 'S-2', 'MAGISTER', 'SPESIALIS']))
                Nilai &ge; 65.00 (Huruf Mutu Minimal C+ / Bobot 2.50)
            @else
                Nilai &ge; 50.00 (Huruf Mutu Minimal D / Bobot 1.00)
            @endif
        </div>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px;">
            <tr>
                {{-- Kolom Kiri: Lulus --}}
                <td style="width: 50%; vertical-align: top; padding-right: 6px;">
                    <div style="font-weight: bold; margin-bottom: 4px; font-size: 8pt; color: #1e3a1e;">
                        &bull; Daftar Mata Kuliah Lulus:
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 24px; text-align: center;">No</th>
                                <th style="width: 65px; text-align: center;">Kode</th>
                                <th style="text-align: left;">Mata Kuliah</th>
                                <th style="width: 40px; text-align: center;">Skor</th>
                                <th style="width: 35px; text-align: center;">Huruf</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mataKuliahLulus as $mk)
                                <tr>
                                    <td style="text-align: center;">{{ $mk['no'] ?? $loop->iteration }}</td>
                                    <td style="text-align: center; font-family: monospace;">{{ $mk['courseCode'] ?? ($mk['kode'] ?? '') }}</td>
                                    <td>{{ $mk['courseName'] ?? ($mk['nama'] ?? '') }}</td>
                                    <td style="text-align: center; font-weight: bold;">{{ $mk['nilai'] ?? '-' }}</td>
                                    <td style="text-align: center; font-weight: bold;">{{ $mk['huruf'] ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; color: #777; font-style: italic;">Tidak ada data mata kuliah lulus</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>

                {{-- Kolom Kanan: Tidak Lulus --}}
                <td style="width: 50%; vertical-align: top; padding-left: 6px;">
                    <div style="font-weight: bold; margin-bottom: 4px; font-size: 8pt; color: #5a1a1a;">
                        &bull; Daftar Mata Kuliah Tidak Lulus:
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 24px; text-align: center;">No</th>
                                <th style="width: 65px; text-align: center;">Kode</th>
                                <th style="text-align: left;">Mata Kuliah</th>
                                <th style="width: 40px; text-align: center;">Skor</th>
                                <th style="width: 35px; text-align: center;">Huruf</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mataKuliahTidakLulus as $mk)
                                <tr>
                                    <td style="text-align: center;">{{ $mk['no'] ?? $loop->iteration }}</td>
                                    <td style="text-align: center; font-family: monospace;">{{ $mk['courseCode'] ?? ($mk['kode'] ?? '') }}</td>
                                    <td>{{ $mk['courseName'] ?? ($mk['nama'] ?? '') }}</td>
                                    <td style="text-align: center; font-weight: bold;">{{ $mk['nilai'] ?? '-' }}</td>
                                    <td style="text-align: center; font-weight: bold;">{{ $mk['huruf'] ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; color: #777; font-style: italic;">Tidak ada data mata kuliah yang belum/tidak lulus</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- BAGIAN IV: SOAL DENGAN CAPAIAN TERENDAH --}}
    @if (!empty($soalTerendah) && count($soalTerendah) > 0)
        <div class="section-box">
            <div class="section-header">IV. Pertanyaan / Asesmen dengan Capaian CPL Terendah</div>
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
                            <td style="text-align: center;">{{ $row['no'] ?? $loop->iteration }}</td>
                            <td>{{ $row['course_name'] ?? ($row['namaCourse'] ?? '-') }}</td>
                            <td style="text-align: center;">{{ $row['types_of_assessment'] ?? ($row['Jenis'] ?? '-') }}</td>
                            <td>{{ $row['question'] ?? ($row['soal'] ?? '-') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- BAGIAN V: MATA KULIAH YANG DITEMPUH --}}
    @if (!empty($courseList) && count($courseList) > 0)
        <div class="section-box">
            <div class="section-header">V. Seluruh Mata Kuliah yang Telah Ditempuh</div>
            <ol>
                @foreach ($courseList as $cl)
                    <li>{{ $cl }}</li>
                @endforeach
            </ol>
        </div>
    @endif

    {{-- BAGIAN VI: PEMETAAN PROFIL LULUSAN & KARIR --}}
    <div class="section-box">
        <div class="section-header">VI. Pemetaan Profil Lulusan dan Karir Berdasarkan CPL</div>

        @if (!empty($profilChartImg))
            <div class="chart-box">
                <img src="{{ $profilChartImg }}" alt="Grafik Pemetaan Profil Karir">
                <div class="figure-caption">Gambar 3. Diagram Pemetaan Kesesuaian Profil Lulusan & Karir Berdasarkan CPL</div>
            </div>
        @endif

        @if (!empty($hasilProfil) && count($hasilProfil) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 25px;">No</th>
                        <th style="width: 20%; text-align: left;">Profil Karir</th>
                        <th style="width: 25%; text-align: left;">Profil Lulusan</th>
                        <th style="width: 45px; text-align: center;">CPL</th>
                        <th style="width: 50px; text-align: center;">Bobot</th>
                        <th style="width: 50px; text-align: center;">Hasil CPL</th>
                        <th style="width: 60px; text-align: center;">Bobot &times; Hasil</th>
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
                                <td rowspan="{{ $row['total_rows'] }}" style="text-align: center; vertical-align: middle; font-weight: bold;">
                                    {{ $row['total'] }}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- TANDA TANGAN / PENGESAHAN RESMI --}}
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td style="width: 55%; font-size: 7.5pt; color: #444; line-height: 1.4; padding-right: 15px;">
                    <div style="font-weight: bold; color: #000; margin-bottom: 2px;">Catatan Akademik:</div>
                    <div>1. Dokumen ini merupakan laporan evaluasi resmi Capaian Pembelajaran Lulusan (CPL) mahasiswa berbasis Outcome-Based Education (OBE).</div>
                    <div>2. Nilai capaian dihitung berdasarkan seluruh instrumen asesmen mata kuliah yang telah ditempuh dan diverifikasi.</div>
                    <div>3. Dokumen ini sah dan dapat dipergunakan untuk monitoring kemajuan studi serta portofolio kompetensi lulusan.</div>
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
