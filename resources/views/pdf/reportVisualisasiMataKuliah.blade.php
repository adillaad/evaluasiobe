<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <title>Laporan Visualisasi Mata Kuliah {{ $course }}</title>
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
        <h2>Laporan Visualisasi Capaian Pembelajaran Mata Kuliah (CPMK)</h2>
        <h3>{{ $universitas }} &bull; Program Studi {{ $prodi }}</h3>
    </div>

    {{-- INFORMASI MATA KULIAH & ANGKATAN --}}
    <table class="info-table">
        <tr>
            <td class="info-label">Mata Kuliah</td>
            <td class="info-value"><strong>{{ $course }}</strong></td>
            <td class="info-label">Program Studi</td>
            <td class="info-value">{{ $prodi }}</td>
        </tr>
        <tr>
            <td class="info-label">Angkatan</td>
            <td class="info-value"><strong>{{ $angkatan }}</strong></td>
            <td class="info-label">Tanggal Cetak</td>
            <td class="info-value">{{ date('d F Y') }}</td>
        </tr>
    </table>

    {{-- 1. CAPAIAN CPMK ANGKATAN --}}
    <div class="section-box">
        <div class="section-title">1. Capaian CPMK Angkatan (CPMK Batch Achievement)</div>

        @if (!empty($radarChartAngkatanImg))
            <div class="chart-container">
                <img src="{{ $radarChartAngkatanImg }}">
            </div>
        @endif

        {{-- Tabel Rincian Capaian CPMK Angkatan --}}
        @if (!empty($rincianCpmkAngkatan) && count($rincianCpmkAngkatan) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30%;">Kode CPMK</th>
                        <th style="width: 70%; text-align: center;">Skor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rincianCpmkAngkatan as $item)
                        <tr>
                            <td style="font-weight: bold; padding-left: 8px;">{{ $item['kode'] }}</td>
                            <td style="text-align: center; font-weight: bold; color: #1F3BB3;">{{ $item['avgScore'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Deskripsi CPMK --}}
        @if (!empty($descriptions) && is_array($descriptions))
            <div style="font-weight: bold; margin-top: 6px; margin-bottom: 2px; font-size: 8pt;">Deskripsi CPMK:</div>
            <ol>
                @foreach ($descriptions as $desc)
                    <li>{{ $desc }}</li>
                @endforeach
            </ol>
        @endif
    </div>

    {{-- 2. QUESTIONS WITH LOWEST CPMK --}}
    @if (!empty($soalTerendah) && count($soalTerendah) > 0)
        <div class="section-box">
            <div class="section-title">2. Questions with Lowest CPMK (Soal dengan Capaian Rata-rata Terendah)</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 28px;">No</th>
                        <th style="width: 30%;">Types of Assessment</th>
                        <th>Questions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($soalTerendah as $row)
                        <tr>
                            <td style="text-align: center;">{{ $row['no'] }}</td>
                            <td>{{ $row['types_of_assessment'] }}</td>
                            <td>{{ $row['question'] }}</td>
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
