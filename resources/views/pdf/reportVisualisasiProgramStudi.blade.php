<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <title>{{ $selectedProdi ? 'Laporan Evaluasi CPL - ' . $selectedProdi['nama'] : 'Laporan Evaluasi CPL Fakultas - ' . ($fakultas->nama ?? 'Fakultas') }}</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 10mm 12mm 10mm 12mm;
        }

        body {
            font-family: "DejaVu Sans", "Helvetica Neue", Arial, sans-serif;
            font-size: 8pt;
            line-height: 1.35;
            color: #1e293b;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #1F3BB3;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }

        .header h2 {
            margin: 0 0 3px;
            font-size: 12pt;
            color: #1F3BB3;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header h3 {
            margin: 0 0 2px;
            font-size: 9.5pt;
            font-weight: bold;
            color: #334155;
            text-transform: uppercase;
        }

        .header p {
            margin: 0;
            font-size: 7.5pt;
            color: #64748b;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
        }

        .info-table td {
            padding: 4px 8px;
            font-size: 8pt;
            vertical-align: middle;
        }

        .info-label {
            width: 22%;
            font-weight: bold;
            color: #475569;
        }

        .info-value {
            width: 28%;
            color: #0f172a;
        }

        .section-box {
            margin-bottom: 14px;
        }

        .section-title {
            font-size: 8.5pt;
            font-weight: bold;
            color: #1F3BB3;
            background-color: #eef2ff;
            padding: 4px 8px;
            border-left: 3px solid #1F3BB3;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 7.5pt;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            text-align: left;
        }

        .data-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: bold;
            text-align: center;
        }

        .data-table tr:nth-child(even) {
            background-color: #fafbfc;
        }

        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .fw-bold { font-weight: bold !important; }

        .avoid-break { page-break-inside: avoid; }

        .chart-container {
            text-align: center;
            margin: 6px 0;
            page-break-inside: avoid;
        }

        .chart-container img {
            max-width: 90%;
            max-height: 200px;
            height: auto;
            border: 1px solid #e2e8f0;
            padding: 4px;
            background: #fff;
        }

        .summary-card-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
            margin-bottom: 10px;
        }

        .summary-card {
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            padding: 6px 8px;
            text-align: center;
            border-radius: 4px;
        }

        .summary-card-title {
            font-size: 6.5pt;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .summary-card-value {
            font-size: 12pt;
            font-weight: bold;
            color: #1F3BB3;
        }

        .signature-table {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
            font-size: 8pt;
        }

        .signature-table td {
            vertical-align: top;
            padding: 0 10px;
        }
    </style>
</head>

<body>
    {{-- HEADER / KOP --}}
    <div class="header">
        <h2>{{ $universitas->nama ?? 'UNIVERSITAS' }}</h2>
        <h3>LAPORAN EVALUASI & CAPAIAN PEMBELAJARAN LULUSAN (CPL)</h3>
        <p>{{ $fakultas->nama ?? 'FAKULTAS' }} &bull; Penjaminan Mutu Akademik Berbasis OBE (Outcome-Based Education)</p>
    </div>

    @if ($selectedProdi)
        {{-- SINGLE PRODI REPORT --}}
        <table class="info-table">
            <tr>
                <td class="info-label">Program Studi</td>
                <td class="info-value fw-bold">{{ $selectedProdi['nama'] }} ({{ $selectedProdi['jenjang'] }})</td>
                <td class="info-label">Fakultas</td>
                <td class="info-value">{{ $fakultas->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Kategori Kurikulum</td>
                <td class="info-value">{{ $selectedProdi['is_aptikom'] ? 'Aptikom (Informatika & Komputer)' : 'Non-Aptikom' }}</td>
                <td class="info-label">Tanggal Cetak</td>
                <td class="info-value">{{ $tanggalCetak }}</td>
            </tr>
            <tr>
                <td class="info-label">Mahasiswa Dinilai</td>
                <td class="info-value" colspan="3">{{ $selectedProdi['total_mhs'] }} Mahasiswa</td>
            </tr>
        </table>

        {{-- SUMMARY CARDS --}}
        <table class="summary-card-table">
            <tr>
                <td class="summary-card" style="border-top: 3px solid #3b82f6;">
                    <div class="summary-card-title">Rata-rata Skor CPL</div>
                    <div class="summary-card-value" style="color: #2563eb;">{{ number_format($selectedProdi['avg_skor_cpl'], 1) }} <span style="font-size: 7.5pt; color: #64748b;">/ 100</span></div>
                </td>
                <td class="summary-card" style="border-top: 3px solid #10b981;">
                    <div class="summary-card-title">Rata-rata Capaian CPL</div>
                    <div class="summary-card-value" style="color: #059669;">{{ number_format($selectedProdi['avg_capaian_cpl'], 1) }}%</div>
                </td>
                <td class="summary-card" style="border-top: 3px solid #8b5cf6;">
                    <div class="summary-card-title">Total Butir CPL</div>
                    <div class="summary-card-value" style="color: #7c3aed;">{{ $selectedProdi['total_cpl'] }} <span style="font-size: 7.5pt; color: #64748b;">Butir</span></div>
                </td>
                <td class="summary-card" style="border-top: 3px solid #f59e0b;">
                    <div class="summary-card-title">Mahasiswa Terdaftar</div>
                    <div class="summary-card-value" style="color: #d97706;">{{ $selectedProdi['total_mhs'] }} <span style="font-size: 7.5pt; color: #64748b;">Mhs</span></div>
                </td>
            </tr>
        </table>

        {{-- TABEL RINCIAN CPL PRODI --}}
        <div class="section-box">
            <div class="section-title">Rincian Evaluasi Capaian Pembelajaran Lulusan (CPL)</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 25px;">No</th>
                        <th style="width: 70px;">Kode CPL</th>
                        <th style="width: 110px;">Aspek</th>
                        <th>Deskripsi / Judul Butir CPL</th>
                        <th style="width: 85px;">Rata-rata Skor</th>
                        <th style="width: 80px;">Capaian (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($selectedProdi['cpl_details'] as $idx => $cpl)
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td class="text-center fw-bold">{{ $cpl['kode'] }}</td>
                            <td class="text-center">{{ $cpl['aspek'] ?? '-' }}</td>
                            <td>{{ $cpl['judul'] ?? '-' }}</td>
                            <td class="text-center fw-bold">{{ number_format($cpl['avg_skor'], 1) }}</td>
                            <td class="text-center">{{ number_format($cpl['avg_capaian'], 1) }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data butir CPL pada program studi ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @else
        {{-- FULL FACULTY REPORT --}}
        <table class="info-table">
            <tr>
                <td class="info-label">Fakultas</td>
                <td class="info-value fw-bold">{{ $fakultas->nama ?? 'Fakultas' }}</td>
                <td class="info-label">Universitas</td>
                <td class="info-value">{{ $universitas->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Jumlah Program Studi</td>
                <td class="info-value">{{ count($fakultasCplData['prodi_stats'] ?? []) }} Program Studi</td>
                <td class="info-label">Tanggal Cetak</td>
                <td class="info-value">{{ $tanggalCetak }}</td>
            </tr>
            <tr>
                <td class="info-label">Total Mahasiswa Dinilai</td>
                <td class="info-value" colspan="3">{{ $fakultasCplData['summary']['total_mhs_evaluated'] ?? 0 }} Mahasiswa</td>
            </tr>
        </table>

        {{-- SUMMARY CARDS --}}
        <table class="summary-card-table">
            <tr>
                <td class="summary-card" style="border-top: 3px solid #3b82f6;">
                    <div class="summary-card-title">Rata-rata Skor CPL</div>
                    <div class="summary-card-value" style="color: #2563eb;">{{ number_format($fakultasCplData['summary']['faculty_avg_skor'] ?? 0, 1) }} <span style="font-size: 7.5pt; color: #64748b;">/ 100</span></div>
                </td>
                <td class="summary-card" style="border-top: 3px solid #10b981;">
                    <div class="summary-card-title">Rata-rata Capaian CPL</div>
                    <div class="summary-card-value" style="color: #059669;">{{ number_format($fakultasCplData['summary']['faculty_avg_capaian'] ?? 0, 1) }}%</div>
                </td>
                <td class="summary-card" style="border-top: 3px solid #8b5cf6;">
                    <div class="summary-card-title">Total Program Studi</div>
                    <div class="summary-card-value" style="color: #7c3aed;">{{ count($fakultasCplData['prodi_stats']) }} <span style="font-size: 7.5pt; color: #64748b;">Prodi</span></div>
                </td>
                <td class="summary-card" style="border-top: 3px solid #f59e0b;">
                    <div class="summary-card-title">Total Butir CPL</div>
                    <div class="summary-card-value" style="color: #d97706;">{{ $fakultasCplData['summary']['total_cpl_count'] ?? 0 }} <span style="font-size: 7.5pt; color: #64748b;">CPL</span></div>
                </td>
            </tr>
        </table>

        {{-- 1. PERKEMBANGAN SKOR CPL ANTAR TAHUN --}}
        <div class="section-box avoid-break">
            <div class="section-title">1. Perkembangan Skor CPL Antar Tahun</div>
            @if (!empty($trendChartImg))
                <div class="chart-container">
                    <img src="{{ $trendChartImg }}" alt="Grafik Tren Tahunan">
                </div>
            @endif
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 25px;">No</th>
                        <th>Program Studi</th>
                        <th style="width: 55px;">Jenjang</th>
                        @foreach ($availableYears as $yr)
                            <th style="width: 80px;">Tahun {{ $yr }}</th>
                        @endforeach
                        <th style="width: 95px;">Rata-rata</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($yearlyProgression as $idx => $yp)
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td class="fw-bold">{{ $yp['nama'] }}</td>
                            <td class="text-center">{{ $yp['jenjang'] }}</td>
                            @foreach ($availableYears as $yr)
                                <td class="text-center">
                                    {{ isset($yp['scores'][$yr]) && $yp['scores'][$yr] > 0 ? number_format($yp['scores'][$yr], 1) : '-' }}
                                </td>
                            @endforeach
                            <td class="text-center fw-bold">{{ number_format($yp['overall_skor'], 1) }} / 100</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end fw-bold">Rata-rata Skor Fakultas:</th>
                        @foreach ($availableYears as $yr)
                            <th class="text-center fw-bold text-primary">
                                {{ number_format($yearlyAverages[$yr]['avg_skor'] ?? 0, 1) }}
                            </th>
                        @endforeach
                        <th class="text-center fw-bold text-primary">
                            {{ number_format($fakultasCplData['summary']['faculty_avg_skor'], 1) }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- 2. KOMPARASI EVALUASI CPL SELURUH PROGRAM STUDI --}}
        <div class="section-box avoid-break">
            <div class="section-title">2. Komparasi Evaluasi CPL Seluruh Program Studi</div>
            @if (!empty($chartImg))
                <div class="chart-container">
                    <img src="{{ $chartImg }}" alt="Grafik Komparasi CPL Fakultas">
                </div>
            @endif
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 25px;">No</th>
                        <th>Program Studi</th>
                        <th style="width: 55px;">Jenjang</th>
                        <th style="width: 75px;">Kategori</th>
                        <th style="width: 75px;">Mahasiswa</th>
                        <th style="width: 65px;">Total CPL</th>
                        <th style="width: 85px;">Rata-rata Skor</th>
                        <th style="width: 80px;">Capaian (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($fakultasCplData['prodi_stats'] as $idx => $p)
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td class="fw-bold">{{ $p['nama'] }}</td>
                            <td class="text-center">{{ $p['jenjang'] }}</td>
                            <td class="text-center">{{ $p['is_aptikom'] ? 'Aptikom' : 'Non-Aptikom' }}</td>
                            <td class="text-center">{{ $p['total_mhs'] }} Mhs</td>
                            <td class="text-center">{{ $p['total_cpl'] }} CPL</td>
                            <td class="text-center fw-bold">{{ number_format($p['avg_skor_cpl'], 1) }}</td>
                            <td class="text-center">{{ number_format($p['avg_capaian_cpl'], 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- TANDA TANGAN / PENGESAHAN --}}
    <table class="signature-table">
        <tr>
            <td style="width: 55%;">
                <p style="margin-bottom: 2px;"><strong>Catatan Penjaminan Mutu:</strong></p>
                <p style="margin: 0; font-size: 7pt; color: #555; line-height: 1.35;">
                    Laporan ini digenerate secara otomatis melalui Sistem Evaluasi Kurikulum OBE sebagai instrumen monitoring progres dan pelaporan akademik berkala. Skor CPL dinilai dari akumulasi asesmen berbobot pada mata kuliah pendukung CPL.
                </p>
            </td>
            <td style="width: 45%; text-align: center;">
                <p style="margin-bottom: 36px;">
                    Dicetak pada: {{ $tanggalCetak }}<br>
                    <strong>Tim Penjamin Mutu / Pimpinan Fakultas</strong>
                </p>
                <p style="margin: 0; text-decoration: underline; font-weight: bold;">
                    ( .................................................... )
                </p>
            </td>
        </tr>
    </table>
</body>

</html>
