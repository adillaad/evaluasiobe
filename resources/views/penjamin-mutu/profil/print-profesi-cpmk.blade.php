<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pemetaan Profesi - CPMK</title>
    <style>
       
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #000;
            background: #f0f0f0;
            padding: 20px;
        }

        .page {
            width: 190mm;
            min-height: 270mm;
            background: #fff;
            margin: 0 auto 20px auto;
            padding: 15mm;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .header-section {
            text-align: center;
            margin-bottom: 16px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .title {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .subtitle {
            font-size: 10pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table tr {
            page-break-inside: avoid;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        table th {
            background-color: #e5e5e5;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
            font-size: 9pt;
        }

        table td {
            font-size: 9pt;
        }

        .text-center {
            text-align: center;
        }

        ol {
            margin: 0;
            padding-left: 16px;
        }

        .toolbar {
            width: 190mm;
            margin: 0 auto 16px auto;
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn-print {
            background: #1d4ed8;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 22px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .btn-print:hover {
            background: #1e40af;
        }

        .btn-back {
            background: #6b7280;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .btn-back:hover {
            background: #4b5563;
        }

        .hint {
            font-size: 12px;
            color: #6b7280;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .toolbar {
                display: none !important;
            }

            .page {
                width: 100%;
                min-height: auto;
                padding: 0;
                margin: 0;
                box-shadow: none;
            }

            @page {
                size: A4 portrait;
                margin: 1.5cm;
            }

            table tr {
                page-break-inside: avoid;
            }

            table th {
                background-color: #e5e5e5 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

    <div class="toolbar">
        <button class="btn-print" onclick="window.print()">
            🖨️ Cetak / Print
        </button>
        @php
            $backUrl = request()->query('backUrl', url()->previous());
        @endphp
        <a href="{{ $backUrl }}" class="btn-back">← Kembali</a>
        <span class="hint">
            💡 Atur kertas, orientasi, dan warna di dialog print. Pilih <strong>"Save as PDF"</strong> untuk simpan PDF.
        </span>
    </div>

    <div class="page">

        <div class="header-section">
            <div class="title">Laporan Pemetaan Profesi - CPMK</div>
            <div class="subtitle">
                Program Studi: {{ $prodi->nama ?? '-' }}
                &nbsp;&mdash;&nbsp; Dicetak pada: {{ now()->format('d F Y, H:i') }}
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:5%;">No</th>
                    <th style="width:13%;">Kode CPMK</th>
                    <th style="width:47%;">Deskripsi CPMK</th>
                    <th style="width:35%;">Profesi Terkait</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cpmks as $index => $cpmk)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $cpmk->kode }}</td>
                        <td>{{ $cpmk->judul }}</td>
                        <td>
                            @if ($cpmk->profesis->count() > 0)
                                <ol>
                                    @foreach ($cpmk->profesis as $profesi)
                                        <li>{{ $profesi->nama }}</li>
                                    @endforeach
                                </ol>
                            @else
                                <span style="color:#888;">Tidak ada profesi</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>

</body>

</html>
