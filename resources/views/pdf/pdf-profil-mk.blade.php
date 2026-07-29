<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Pemetaan Profil Lulusan dan Mata Kuliah</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .header-section {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .title {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .subtitle {
            font-size: 11pt;
        }

        table.meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10pt;
        }

        table.meta td {
            padding: 3px 0;
            vertical-align: top;
        }

        table.meta .label {
            width: 120px;
            font-weight: bold;
        }

        table.meta .colon {
            width: 15px;
            text-align: center;
            font-weight: bold;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.data tr {
            page-break-inside: avoid;
        }

        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 6px 5px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: normal;
        }

        table.data th {
            background-color: #e5e5e5;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header-section">
        <div class="title">Laporan Pemetaan Profil Lulusan - Mata Kuliah</div>
        <div class="subtitle">Data Distribusi Berdasarkan Filter Akademik</div>
    </div>

    <table class="meta">
        @if ($namaUniversitas)
            <tr>
                <td class="label">Universitas</td>
                <td class="colon">:</td>
                <td>{{ $namaUniversitas }}</td>
            </tr>
        @endif
        @if ($namaFakultas)
            <tr>
                <td class="label">Fakultas</td>
                <td class="colon">:</td>
                <td>{{ $namaFakultas }}</td>
            </tr>
        @endif
        @if ($namaProdi)
            <tr>
                <td class="label">Jurusan / Prodi</td>
                <td class="colon">:</td>
                <td>{{ $namaProdi }}</td>
            </tr>
        @endif
        @if ($namaKurikulum)
            <tr>
                <td class="label">Kurikulum</td>
                <td class="colon">:</td>
                <td>{{ $namaKurikulum }}</td>
            </tr>
        @endif
        <tr>
            <td class="label">Dicetak Pada</td>
            <td class="colon">:</td>
            <td>{{ $generated_at }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                @if ($otoritas === 'Penjamin Mutu Universitas')
                    <th style="width: 4%;">No</th>
                    <th style="width: 9%;">Kode Profil</th>
                    <th style="width: 20%;">Nama Profil Lulusan</th>
                    <th style="width: 16%;">Fakultas</th>
                    <th style="width: 16%;">Jurusan</th>
                    <th style="width: 9%;">Kode MK</th>
                    <th style="width: 19%;">Nama Mata Kuliah</th>
                    <th style="width: 7%;">Kuri-<br>kulum</th>
                @elseif($otoritas === 'Penjamin Mutu Fakultas')
                    <th style="width: 4%;">No</th>
                    <th style="width: 10%;">Kode Profil</th>
                    <th style="width: 24%;">Nama Profil Lulusan</th>
                    <th style="width: 18%;">Jurusan</th>
                    <th style="width: 10%;">Kode MK</th>
                    <th style="width: 26%;">Nama Mata Kuliah</th>
                    <th style="width: 8%;">Kuri-<br>kulum</th>
                @else
                    <th style="width: 5%;">No</th>
                    <th style="width: 12%;">Kode Profil</th>
                    <th style="width: 28%;">Nama Profil Lulusan</th>
                    <th style="width: 12%;">Kode MK</th>
                    <th style="width: 33%;">Nama Mata Kuliah</th>
                    <th style="width: 10%;">Kurikulum</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp

            @forelse($grouped as $profilKode => $items)
                @php
                    $rowspan = max($items->count(), 1);
                @endphp

                @foreach ($items as $index => $item)
                    <tr>
                        @if ($index === 0)
                            <td rowspan="{{ $rowspan }}" class="text-center">{{ $no++ }}</td>
                            <td rowspan="{{ $rowspan }}" class="text-center">{{ $item->profil_kode ?? '-' }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $item->profil_nama ?? '-' }}</td>

                            @if ($otoritas === 'Penjamin Mutu Universitas')
                                <td rowspan="{{ $rowspan }}">{{ $item->fakultas_nama ?? '-' }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $item->prodi_nama ?? '-' }}</td>
                            @elseif($otoritas === 'Penjamin Mutu Fakultas')
                                <td rowspan="{{ $rowspan }}">{{ $item->prodi_nama ?? '-' }}</td>
                            @endif
                        @endif

                        <td class="text-center">{{ $item->mk_kode ?? '-' }}</td>
                        <td>{{ $item->mk_nama ?? '-' }}</td>
                        <td class="text-center">{{ $item->kurikulum_tahun ?? '-' }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="
                        @if ($otoritas === 'Penjamin Mutu Universitas') 8
                        @elseif($otoritas === 'Penjamin Mutu Fakultas') 7
                        @else 6 @endif
                    "
                        class="text-center" style="padding: 15px;">
                        Data tidak tersedia untuk filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
