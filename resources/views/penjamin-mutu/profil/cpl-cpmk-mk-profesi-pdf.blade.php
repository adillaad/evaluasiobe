<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Pemetaan CPL-CPMK-MK-Profesi</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-center {
            text-align: center;
        }

        h3 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <h3>Pemetaan CPL - CPMK - MK - Profesi</h3>
    <table>
        <thead>
            <tr>
                <th>CPL</th>
                <th>Deskripsi CPL</th>
                <th>CPMK</th>
                <th>Deskripsi CPMK</th>
                <th>Kode MK</th>
                <th>Profesi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cpls as $cpl)
                @if ($cpl->cpmk->isEmpty())
                    <tr>
                        <td>{{ $cpl->kode }}</td>
                        <td>{{ $cpl->judul }}</td>
                        <td colspan="4">Tidak ada CPMK terkait</td>
                    </tr>
                @else
                    @foreach ($cpl->cpmk as $cpmk)
                        @php
                            $mkList = $cpmk->mks->pluck('kode')->implode(', ') ?: 'Tidak ada MK terkait';
                            $profesiList = $cpmk->profesis->pluck('nama')->implode(', ') ?: 'Tidak ada profesi terkait';
                        @endphp
                        <tr>
                            <td>{{ $cpl->kode }}</td>
                            <td>{{ $cpl->judul }}</td>
                            <td>{{ $cpmk->kode }}</td>
                            <td>{{ $cpmk->judul }}</td>
                            <td>{{ $mkList }}</td>
                            <td>{{ $profesiList }}</td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        </tbody>
    </table>
</body>

</html>
