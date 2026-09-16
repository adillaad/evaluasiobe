<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <title>Laporan Visualisasi CPMK {{ $course }} Angkatan {{ $angkatan }}</title>
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
            width: 20%;
            font-weight: bold;
            color: #495057;
        }

        .info-value {
            width: 30%;
            color: #212529;
        }

        .section-box {
            margin-bottom: 16px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 9.5pt;
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
            margin: 10px 0;
            page-break-inside: avoid;
        }

        .chart-container img {
            max-width: 80%;
            max-height: 280px;
            height: auto;
            border: 1px solid #e0e0e0;
            padding: 4px;
            background: #fff;
        }

        ol, ul {
            margin: 4px 0 8px 18px;
            padding: 0;
        }

        li {
            margin-bottom: 3px;
            font-size: 8pt;
            color: #333;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 7.5pt;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 4px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Laporan Visualisasi Capaian CPMK Angkatan</h2>
        <h3>Mata Kuliah: {{ $course }} &mdash; Angkatan {{ $angkatan }}</h3>
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">Mata Kuliah</td>
            <td class="info-value" style="width: 80%;" colspan="3">{{ $course }}</td>
        </tr>
        <tr>
            <td class="info-label">Angkatan</td>
            <td class="info-value">{{ $angkatan }}</td>
            <td class="info-label">Program Studi</td>
            <td class="info-value">{{ $prodi }}</td>
        </tr>
        <tr>
            <td class="info-label">Universitas</td>
            <td class="info-value" colspan="3">{{ $universitas }}</td>
        </tr>
    </table>

    @if (!empty($radarChartAngkatanImg))
        <div class="section-box">
            <div class="section-title">Diagram Radar Capaian CPMK Angkatan</div>
            <div class="chart-container">
                <img src="{{ $radarChartAngkatanImg }}" alt="Radar Chart Capaian CPMK Angkatan">
            </div>
        </div>
    @endif

    @if (!empty($descriptions) && is_array($descriptions) && count($descriptions) > 0)
        <div class="section-box">
            <div class="section-title">Deskripsi Capaian Pembelajaran Mata Kuliah (CPMK)</div>
            <ol>
                @foreach ($descriptions as $desc)
                    <li>{{ $desc }}</li>
                @endforeach
            </ol>
        </div>
    @endif

    <div class="section-box">
        <div class="section-title">Questions with Lowest CPMK (Soal dengan Capaian CPMK Terendah)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 35px;">No</th>
                    <th style="width: 130px;">Jenis Asesmen</th>
                    <th>Pertanyaan / Soal</th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($soalTerendah) && count($soalTerendah) > 0)
                    @foreach ($soalTerendah as $row)
                        @php
                            $noVal = is_array($row) ? ($row['no'] ?? $loop->iteration) : ($row->no ?? $loop->iteration);
                            $jenisVal = is_array($row) ? ($row['types_of_assessment'] ?? ($row['Jenis'] ?? '-')) : ($row->types_of_assessment ?? ($row->Jenis ?? '-'));
                            $soalVal = is_array($row) ? ($row['question'] ?? ($row['soal'] ?? '-')) : ($row->question ?? ($row->soal ?? '-'));
                        @endphp
                        <tr>
                            <td style="text-align: center;">{{ $noVal }}</td>
                            <td>{{ $jenisVal }}</td>
                            <td>{{ $soalVal }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" style="text-align: center; color: #888; padding: 10px;">
                            Tidak ada data soal dengan nilai rendah pada CPMK ini.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="footer">
        Dicetak pada: {{ date('d-m-Y H:i:s') }} | Sistem Evaluasi OBE
    </div>
</body>

</html>
