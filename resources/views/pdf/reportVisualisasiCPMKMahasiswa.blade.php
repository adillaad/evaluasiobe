<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <title>Laporan Visualisasi CPMK {{ $course }} - {{ $nama }}</title>
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
            border-bottom: 2px solid #1F3BB3;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .header h2 {
            margin: 0 0 4px;
            font-size: 13pt;
            color: #1F3BB3;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header h3 {
            margin: 0;
            font-size: 9.5pt;
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

        .metrics-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-bottom: 14px;
        }

        .metrics-cell {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 8px;
            text-align: center;
            width: 33.33%;
        }

        .metrics-title {
            font-size: 7.5pt;
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .metrics-val {
            font-size: 11pt;
            font-weight: bold;
            color: #1F3BB3;
        }

        .metrics-val-success {
            color: #16a34a;
        }

        .metrics-val-danger {
            color: #dc2626;
        }

        .section-box {
            margin-bottom: 14px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 9pt;
            font-weight: bold;
            color: #1F3BB3;
            background-color: #eef2ff;
            padding: 5px 8px;
            border-left: 4px solid #1F3BB3;
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
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            text-align: left;
        }

        .data-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: bold;
            text-align: center;
        }

        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .chart-container {
            text-align: center;
            margin: 6px 0 10px 0;
            page-break-inside: avoid;
        }

        .chart-container img {
            max-width: 80%;
            max-height: 250px;
            height: auto;
            border: 1px solid #e2e8f0;
            padding: 6px;
            background: #fff;
            border-radius: 6px;
        }

        .desc-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 8pt;
        }

        .desc-table td {
            padding: 4px 6px;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
        }

        .desc-badge {
            font-weight: bold;
            color: #1F3BB3;
            font-family: monospace;
            white-space: nowrap;
            width: 15%;
        }

        .desc-text {
            color: #334155;
            width: 85%;
        }

        .footer-note {
            margin-top: 14px;
            padding-top: 6px;
            border-top: 1px solid #e2e8f0;
            font-size: 7pt;
            color: #94a3b8;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Laporan Capaian CPMK Mahasiswa</h2>
        <h3>Mata Kuliah: {{ $course }}</h3>
    </div>

    {{-- Profil Mahasiswa & Mata Kuliah --}}
    <table class="info-table">
        <tr>
            <td class="info-label">Nama Mahasiswa</td>
            <td class="info-value"><strong>{{ $nama }}</strong></td>
            <td class="info-label">Program Studi</td>
            <td class="info-value">{{ $prodi }}</td>
        </tr>
        <tr>
            <td class="info-label">NPM</td>
            <td class="info-value font-monospace">{{ $npm }}</td>
            <td class="info-label">Angkatan</td>
            <td class="info-value">{{ $angkatan }}</td>
        </tr>
        <tr>
            <td class="info-label">Mata Kuliah</td>
            <td class="info-value">{{ $course }}</td>
            <td class="info-label">Universitas</td>
            <td class="info-value">{{ $universitas }}</td>
        </tr>
    </table>

    {{-- Executive Metrics --}}
    <table class="metrics-table">
        <tr>
            <td class="metrics-cell">
                <div class="metrics-title">Rata-rata Skor CPMK</div>
                <div class="metrics-val">{{ $avgScore }} <span style="font-size: 8pt; color: #64748b;">/ 100</span></div>
            </td>
            <td class="metrics-cell">
                <div class="metrics-title">CPMK Tertinggi</div>
                <div class="metrics-val metrics-val-success">{{ $highestCpmk }}</div>
            </td>
            <td class="metrics-cell">
                <div class="metrics-title">CPMK Terendah</div>
                <div class="metrics-val metrics-val-danger">{{ $lowestCpmk }}</div>
            </td>
        </tr>
    </table>

    {{-- Diagram Radar & Tabel Rincian Capaian CPMK --}}
    <div class="section-box">
        <div class="section-title">Visualisasi Diagram Radar & Rincian CPMK</div>
        
        @if (!empty($radarChartImg))
            <div class="chart-container">
                <img src="{{ $radarChartImg }}" alt="Radar Chart Capaian CPMK">
            </div>
        @endif

        @if (!empty($cpmkScores) && is_array($cpmkScores))
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Kode CPMK</th>
                        <th style="width: 60%;">Skor Capaian Mahasiswa</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cpmkScores as $row)
                        <tr>
                            <td style="text-align: center; font-weight: bold; color: #1F3BB3; font-family: monospace;">{{ $row['kode'] ?? '-' }}</td>
                            <td style="text-align: center; font-weight: bold; color: #1e293b;">{{ $row['skor'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Deskripsi CPMK --}}
    @if (!empty($descriptions) && is_array($descriptions))
        <div class="section-box">
            <div class="section-title">Deskripsi Capaian Pembelajaran Mata Kuliah (CPMK)</div>
            <table class="desc-table">
                @foreach ($descriptions as $desc)
                    @php
                        $parts = explode(':', $desc, 2);
                        $badge = trim($parts[0] ?? '');
                        $descText = trim($parts[1] ?? $desc);
                    @endphp
                    <tr>
                        <td class="desc-badge">{{ $badge }}</td>
                        <td class="desc-text">{{ $descText }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    {{-- Questions with Lowest CPMK --}}
    <div class="section-box">
        <div class="section-title">Questions with Lowest CPMK (Soal dengan Capaian CPMK Terendah)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 35px;">No</th>
                    <th style="width: 110px;">Asesmen</th>
                    <th>Soal Penilaian</th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($soalTerendah) && is_array($soalTerendah))
                    @foreach ($soalTerendah as $row)
                        <tr>
                            <td style="text-align: center; color: #64748b;">{{ $row['no'] ?? '-' }}</td>
                            <td style="text-align: center; font-weight: 500;">{{ $row['types_of_assessment'] ?? '-' }}</td>
                            <td>{{ $row['question'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" style="text-align: center; color: #94a3b8; padding: 8px;">Tidak ada data soal dengan nilai rendah.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="footer-note">
        Dicetak pada: {{ date('d F Y, H:i') }} WIB | Sistem Evaluasi Outcome-Based Education (OBE)
    </div>
</body>

</html>
