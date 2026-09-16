<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <title>{{ $selectedFakultas ? 'Laporan Evaluasi CPL - ' . $selectedFakultas['nama'] : 'Laporan Evaluasi CPL Universitas - ' . ($universitas->nama ?? 'Universitas') }}</title>
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
        <p>Tingkat Universitas &bull; Penjaminan Mutu Akademik Berbasis OBE (Outcome-Based Education)</p>
    </div>

    @if ($selectedFakultas)
        {{-- SINGLE FAKULTAS REPORT --}}
        <table class="info-table">
            <tr>
                <td class="info-label">Fakultas</td>
                <td class="info-value fw-bold">{{ $selectedFakultas['nama'] }}</td>
                <td class="info-label">Universitas</td>
                <td class="info-value">{{ $universitas->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Jumlah Program Studi</td>
                <td class="info-value">{{ $selectedFakultas['total_prodi'] }} Program Studi</td>
                <td class="info-label">Tanggal Cetak</td>
                <td class="info-value">{{ $tanggalCetak }}</td>
            </tr>
            <tr>
                <td class="info-label">Mahasiswa Dinilai</td>
                <td class="info-value" colspan="3">{{ $selectedFakultas['total_mhs'] }} Mahasiswa</td>
            </tr>
        </table>

        {{-- SUMMARY CARDS --}}
        <table class="summary-card-table">
            <tr>
                <td class="summary-card" style="border-top: 3px solid #3b82f6;">
                    <div class="summary-card-title">Rata-rata Skor CPL</div>
                    <div class="summary-card-value" style="color: #2563eb;">{{ number_format($selectedFakultas['avg_skor_cpl'], 1) }} <span style="font-size: 7.5pt; color: #64748b;">/ 100</span></div>
                </td>
                <td class="summary-card" style="border-top: 3px solid #10b981;">
                    <div class="summary-card-title">Rata-rata Capaian CPL</div>
                    <div class="summary-card-value" style="color: #059669;">{{ number_format($selectedFakultas['avg_capaian_cpl'], 1) }}%</div>
                </td>
                <td class="summary-card" style="border-top: 3px solid #8b5cf6;">
                    <div class="summary-card-title">Total Program Studi</div>
                    <div class="summary-card-value" style="color: #7c3aed;">{{ $selectedFakultas['total_prodi'] }} <span style="font-size: 7.5pt; color: #64748b;">Prodi</span></div>
                </td>
                <td class="summary-card" style="border-top: 3px solid #f59e0b;">
                    <div class="summary-card-title">Mahasiswa Terdaftar</div>
                    <div class="summary-card-value" style="color: #d97706;">{{ $selectedFakultas['total_mhs'] }} <span style="font-size: 7.5pt; color: #64748b;">Mhs</span></div>
                </td>
            </tr>
        </table>

        {{-- TABEL RINCIAN PRODI FAKULTAS --}}
        <div class="section-box">
            <div class="section-title">Daftar Program Studi dan Capaian CPL</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 25px;">No</th>
                        <th>Program Studi</th>
                        <th style="width: 60px;">Jenjang</th>
                        <th style="width: 80px;">Mahasiswa</th>
                        <th style="width: 70px;">Total CPL</th>
                        <th style="width: 90px;">Rata-rata Skor</th>
                        <th style="width: 85px;">Capaian (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($selectedFakultas['prodi_stats'] as $idx => $p)
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td class="fw-bold">{{ $p['nama'] }}</td>
                            <td class="text-center">{{ $p['jenjang'] ?? '-' }}</td>
                            <td class="text-center">{{ $p['total_mhs'] }} Mhs</td>
                            <td class="text-center">{{ $p['total_cpl'] }} CPL</td>
                            <td class="text-center fw-bold">{{ number_format($p['avg_skor_cpl'], 1) }}</td>
                            <td class="text-center">{{ number_format($p['avg_capaian_cpl'], 1) }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data program studi pada fakultas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @else
        {{-- FULL UNIVERSITY REPORT --}}
        <table class="info-table">
            <tr>
                <td class="info-label">Universitas</td>
                <td class="info-value fw-bold">{{ $universitas->nama ?? 'Universitas' }}</td>
                <td class="info-label">Total Fakultas</td>
                <td class="info-value">{{ $universitasCplData['summary']['total_fakultas_count'] ?? 0 }} Fakultas</td>
            </tr>
            <tr>
                <td class="info-label">Jumlah Program Studi</td>
                <td class="info-value">{{ $universitasCplData['summary']['total_prodi_count'] ?? 0 }} Program Studi</td>
                <td class="info-label">Tanggal Cetak</td>
                <td class="info-value">{{ $tanggalCetak }}</td>
            </tr>
            <tr>
                <td class="info-label">Total Mahasiswa Dinilai</td>
                <td class="info-value" colspan="3">{{ $universitasCplData['summary']['total_mhs_evaluated'] ?? 0 }} Mahasiswa</td>
            </tr>
        </table>

        {{-- SUMMARY CARDS --}}
        <table class="summary-card-table">
            <tr>
                <td class="summary-card" style="border-top: 3px solid #3b82f6;">
                    <div class="summary-card-title">Rata-rata Skor CPL</div>
                    <div class="summary-card-value" style="color: #2563eb;">{{ number_format($universitasCplData['summary']['univ_avg_skor'] ?? 0, 1) }} <span style="font-size: 7.5pt; color: #64748b;">/ 100</span></div>
                </td>
                <td class="summary-card" style="border-top: 3px solid #10b981;">
                    <div class="summary-card-title">Rata-rata Capaian CPL</div>
                    <div class="summary-card-value" style="color: #059669;">{{ number_format($universitasCplData['summary']['univ_avg_capaian'] ?? 0, 1) }}%</div>
                </td>
                <td class="summary-card" style="border-top: 3px solid #8b5cf6;">
                    <div class="summary-card-title">Total Fakultas</div>
                    <div class="summary-card-value" style="color: #7c3aed;">{{ $universitasCplData['summary']['total_fakultas_count'] }} <span style="font-size: 7.5pt; color: #64748b;">Fakultas</span></div>
                </td>
                <td class="summary-card" style="border-top: 3px solid #f59e0b;">
                    <div class="summary-card-title">Total Program Studi</div>
                    <div class="summary-card-value" style="color: #d97706;">{{ $universitasCplData['summary']['total_prodi_count'] }} <span style="font-size: 7.5pt; color: #64748b;">Prodi</span></div>
                </td>
            </tr>
        </table>

        {{-- 1. PERKEMBANGAN SKOR CPL ANTAR TAHUN --}}
        <div class="section-box avoid-break">
            <div class="section-title">1. Perkembangan Skor CPL Antar Tahun (Tingkat Fakultas)</div>
            @if (!empty($trendChartImg))
                <div class="chart-container">
                    <img src="{{ $trendChartImg }}" alt="Grafik Tren Tahunan">
                </div>
            @endif
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 25px;">No</th>
                        <th>Fakultas</th>
                        <th style="width: 70px;">Total Prodi</th>
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
                            <td class="text-center">{{ $yp['total_prodi'] }} Prodi</td>
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
                        <th colspan="3" class="text-end fw-bold">Rata-rata Skor Universitas:</th>
                        @foreach ($availableYears as $yr)
                            <th class="text-center fw-bold text-primary">
                                {{ number_format($yearlyAverages[$yr]['avg_skor'] ?? 0, 1) }}
                            </th>
                        @endforeach
                        <th class="text-center fw-bold text-primary">
                            {{ number_format($universitasCplData['summary']['univ_avg_skor'], 1) }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- 2. KOMPARASI EVALUASI CPL SELURUH FAKULTAS --}}
        <div class="section-box avoid-break">
            <div class="section-title">2. Komparasi Evaluasi CPL Seluruh Fakultas</div>
            @if (!empty($chartImg))
                <div class="chart-container">
                    <img src="{{ $chartImg }}" alt="Grafik Komparasi CPL Fakultas">
                </div>
            @endif
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 25px;">No</th>
                        <th>Fakultas</th>
                        <th style="width: 80px;">Total Prodi</th>
                        <th style="width: 80px;">Mahasiswa</th>
                        <th style="width: 95px;">Rata-rata Skor</th>
                        <th style="width: 90px;">Capaian (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($universitasCplData['fakultas_stats'] as $idx => $f)
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td class="fw-bold">{{ $f['nama'] }}</td>
                            <td class="text-center">{{ $f['total_prodi'] }} Prodi</td>
                            <td class="text-center">{{ $f['total_mhs'] }} Mhs</td>
                            <td class="text-center fw-bold">{{ number_format($f['avg_skor_cpl'], 1) }}</td>
                            <td class="text-center">{{ number_format($f['avg_capaian_cpl'], 1) }}%</td>
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
                    Laporan ini digenerate secara otomatis melalui Sistem Evaluasi Kurikulum OBE sebagai instrumen monitoring progres dan pelaporan akademik berkala di tingkat universitas.
                </p>
            </td>
            <td style="width: 45%; text-align: center;">
                <p style="margin-bottom: 36px;">
                    Dicetak pada: {{ $tanggalCetak }}<br>
                    <strong>Tim Penjamin Mutu / Pimpinan Universitas</strong>
                </p>
                <p style="margin: 0; text-decoration: underline; font-weight: bold;">
                    ( .................................................... )
                </p>
            </td>
        </tr>
    </table>
</body>

</html>
