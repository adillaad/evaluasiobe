<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemetaan Profil Lulusan - Mata Kuliah</title>
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
            background: #fff;
            margin: 0 auto;
            padding: 15mm;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .header-section {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }

        .title {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .subtitle {
            font-size: 10pt;
        }

        table.meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10pt;
        }

        table.meta td {
            padding: 2px 0;
            vertical-align: top;
        }

        table.meta .label {
            width: 130px;
            font-weight: bold;
        }

        table.meta .colon {
            width: 14px;
            text-align: center;
            font-weight: bold;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 4px 5px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        table.data th {
            background-color: #e5e5e5;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
            font-size: 9pt;
        }

        table.data td {
            font-size: 9pt;
        }

        .text-center {
            text-align: center;
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
                background: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
                font-size: 10pt;
            }

            .toolbar {
                display: none !important;
            }

            .page {
                display: block !important;
                width: auto !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                background: transparent !important;
            }

            @page {
                size: A4 portrait;
                margin: 1.5cm;
            }

            .header-section {
                margin-bottom: 8px !important;
            }

            table.meta {
                margin-bottom: 8px !important;
            }

            table.data thead {
                display: table-header-group;
            }

            table.data tr {
                page-break-inside: auto;
            }

            table.data th {
                background-color: #e5e5e5 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

    @php
        $backUrl = request()->query('backUrl', url()->previous());
    @endphp

    <div class="toolbar">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak / Print</button>
        <a href="{{ $backUrl }}" class="btn-back">← Kembali</a>
        <span class="hint">💡 Pilih <strong>"Save as PDF"</strong> untuk simpan sebagai PDF.</span>
    </div>

    <div class="page">
        <div class="header-section">
            <div class="title">Laporan Pemetaan Profil Lulusan - Mata Kuliah</div>
            <div class="subtitle">Data Distribusi Berdasarkan Filter Akademik</div>
        </div>

        <table class="meta">
            @if (!empty($namaUniversitas))
                <tr>
                    <td class="label">Universitas</td>
                    <td class="colon">:</td>
                    <td>{{ $namaUniversitas }}</td>
                </tr>
            @endif
            @if (!empty($namaFakultas))
                <tr>
                    <td class="label">Fakultas</td>
                    <td class="colon">:</td>
                    <td>{{ $namaFakultas }}</td>
                </tr>
            @endif
            @if (!empty($namaProdi))
                <tr>
                    <td class="label">Jurusan / Prodi</td>
                    <td class="colon">:</td>
                    <td>{{ $namaProdi }}</td>
                </tr>
            @endif
            @if (!empty($namaKurikulum))
                <tr>
                    <td class="label">Kurikulum</td>
                    <td class="colon">:</td>
                    <td>{{ $namaKurikulum }}</td>
                </tr>
            @endif
            <tr>
                <td class="label">Dicetak Pada</td>
                <td class="colon">:</td>
                <td>{{ now()->format('d F Y, H:i:s') }}</td>
            </tr>
        </table>

        @forelse ($groupedByKurikulum as $namaKurikulum => $grouped)
            <h4 style="margin-top: 15px; margin-bottom: 8px; font-size: 11pt; text-transform: uppercase;">{{ $namaKurikulum }}</h4>
            <table class="data" style="margin-bottom: 20px;">
                <thead>
                    <tr>
                        @if ($otoritas === 'Penjamin Mutu Universitas')
                            <th style="width:4%;">No</th>
                            <th style="width:8%;">Kode<br>Profil</th>
                            <th style="width:18%;">Nama Profil Lulusan</th>
                            <th style="width:13%;">Fakultas</th>
                            <th style="width:13%;">Jurusan</th>
                            <th style="width:10%;">Kode MK</th>
                            <th style="width:24%;">Nama Mata Kuliah</th>
                            <th style="width:10%;">Kurikulum</th>
                        @elseif ($otoritas === 'Penjamin Mutu Fakultas')
                            <th style="width:4%;">No</th>
                            <th style="width:10%;">Kode<br>Profil</th>
                            <th style="width:24%;">Nama Profil Lulusan</th>
                            <th style="width:16%;">Jurusan</th>
                            <th style="width:10%;">Kode MK</th>
                            <th style="width:26%;">Nama Mata Kuliah</th>
                            <th style="width:10%;">Kurikulum</th>
                        @else
                            <th style="width:5%;">No</th>
                            <th style="width:12%;">Kode<br>Profil</th>
                            <th style="width:30%;">Nama Profil Lulusan</th>
                            <th style="width:13%;">Kode MK</th>
                            <th style="width:30%;">Nama Mata Kuliah</th>
                            <th style="width:10%;">Kurikulum</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach ($grouped as $profilKode => $items)
                        @php $rowspan = max($items->count(), 1); @endphp
                        @foreach ($items as $index => $item)
                            <tr>
                                @if ($index === 0)
                                    <td rowspan="{{ $rowspan }}" class="text-center">{{ $no++ }}</td>
                                    <td rowspan="{{ $rowspan }}" class="text-center">{{ $item->profil_kode ?? '-' }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $item->profil_nama ?? '-' }}</td>

                                    @if ($otoritas === 'Penjamin Mutu Universitas')
                                        <td rowspan="{{ $rowspan }}">{{ $item->fakultas_nama ?? '-' }}</td>
                                        <td rowspan="{{ $rowspan }}">{{ $item->prodi_nama ?? '-' }}</td>
                                    @elseif ($otoritas === 'Penjamin Mutu Fakultas')
                                        <td rowspan="{{ $rowspan }}">{{ $item->prodi_nama ?? '-' }}</td>
                                    @endif
                                @endif

                                <td class="text-center">{{ $item->mk_kode ?? '-' }}</td>
                                <td>{{ $item->mk_nama ?? '-' }}</td>
                                <td class="text-center">{{ $item->kurikulum_tahun ?? '-' }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        @empty
            <table class="data">
                <tbody>
                    <tr>
                        <td class="text-center" style="padding:15px; color:#888;">
                            Data tidak tersedia.
                        </td>
                    </tr>
                </tbody>
            </table>
        @endforelse
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 800);
        });
    </script>

</body>

</html>
