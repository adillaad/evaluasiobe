<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemetaan CPL - CPMK - MK - Profesi</title>
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
            width: 277mm;
            min-height: 190mm;
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
        .toolbar {
            width: 277mm;
            margin: 0 auto 16px auto;
            display: flex;
            gap: 10px;
            align-items: center;
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
            margin-left: auto;
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
                size: A4 landscape;
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

        <a href="{{ $backUrl }}" class="btn-back">
            ← Kembali
        </a>
        <span class="hint">
            💡 Tips: Atur ukuran kertas, orientasi, dan warna di dialog print browser.
            Pilih <strong>"Save as PDF"</strong> jika ingin simpan sebagai PDF.
        </span>
    </div>

    <div class="page">

        <div class="header-section">
            <div class="title">Pemetaan CPL - CPMK - MK - Profesi</div>
            <div class="subtitle">
                @if (isset($prodi) && $prodi)
                    Program Studi: {{ $prodi->nama }}
                @endif
                &nbsp;&mdash;&nbsp; Dicetak pada: {{ now()->format('d F Y, H:i') }}
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:7%;">CPL</th>
                    <th style="width:22%;">Deskripsi CPL</th>
                    <th style="width:7%;">CPMK</th>
                    <th style="width:22%;">Deskripsi CPMK</th>
                    <th style="width:24%;">Kode MK</th>
                    <th style="width:18%;">Profesi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cpls as $cpl)
                    @php
                       
                        $cplPrinted = false;
                        $cplRowspan = 0;
                        foreach ($cpl->cpmk as $c) {
                            $cplRowspan += max($c->profesis->count(), 1);
                        }
                        $cplRowspan = max($cplRowspan, 1);
                    @endphp

                    @if ($cpl->cpmk->isEmpty())
                        <tr>
                            <td class="text-center">{{ $cpl->kode }}</td>
                            <td>{{ $cpl->judul }}</td>
                            <td colspan="4" class="text-center" style="color:#888;">Tidak ada CPMK terkait</td>
                        </tr>
                    @else
                        @foreach ($cpl->cpmk as $cpmk)
                            @php
                                $mkList = $cpmk->mks->pluck('kode')->implode(', ') ?: 'Tidak ada MK terkait';
                                $profesis = $cpmk->profesis;
                                $rowspanCpmk = max($profesis->count(), 1);
                            @endphp

                            @if ($profesis->isEmpty())
                                <tr>
                                    @if (!$cplPrinted)
                                        <td rowspan="{{ $cplRowspan }}" class="text-center">{{ $cpl->kode }}</td>
                                        <td rowspan="{{ $cplRowspan }}">{{ $cpl->judul }}</td>
                                        @php $cplPrinted = true; @endphp
                                    @endif
                                    <td class="text-center">{{ $cpmk->kode }}</td>
                                    <td>{{ $cpmk->judul }}</td>
                                    <td>{{ $mkList }}</td>
                                    <td style="color:#888;">Tidak ada profesi terkait</td>
                                </tr>
                            @else
                                @foreach ($profesis as $pi => $profesi)
                                    <tr>
                                        @if (!$cplPrinted)
                                            <td rowspan="{{ $cplRowspan }}" class="text-center">{{ $cpl->kode }}
                                            </td>
                                            <td rowspan="{{ $cplRowspan }}">{{ $cpl->judul }}</td>
                                            @php $cplPrinted = true; @endphp
                                        @endif
                                        @if ($pi === 0)
                                            <td rowspan="{{ $rowspanCpmk }}" class="text-center">{{ $cpmk->kode }}
                                            </td>
                                            <td rowspan="{{ $rowspanCpmk }}">{{ $cpmk->judul }}</td>
                                            <td rowspan="{{ $rowspanCpmk }}">{{ $mkList }}</td>
                                        @endif
                                        <td>{{ $profesi->nama }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
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
