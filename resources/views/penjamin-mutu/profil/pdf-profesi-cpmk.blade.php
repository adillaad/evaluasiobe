<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Pemetaan Profesi - CPMK</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
            
        }

        h3 {
            text-align: center;
            margin-bottom: 5px;
        }

        .info {
            text-align: center;
            font-size: 11px;
            margin-bottom: 15px;
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
            vertical-align: top;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
        }

        .number {
            width: 5%;
            text-align: center;
        }

        .kode {
            width: 15%;
        }

        .deskripsi {
            width: 40%;
        }

        .profesi {
            width: 40%;
        }

        ol {
            margin: 0;
            padding-left: 18px;
        }
    </style>
</head>

<body>

    <h3>Laporan Pemetaan Profesi - CPMK</h3>

    <div class="info">
        Program Studi: {{ $prodi->nama ?? '-' }} <br>
    </div>

    <table>
        <thead>
            <tr>
                <th class="number">No</th>
                <th class="kode">Kode CPMK</th>
                <th class="deskripsi">Deskripsi CPMK</th>
                <th class="profesi">Profesi Terkait</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($cpmks as $index => $cpmk)
                <tr>
                    <td class="number">{{ $index + 1 }}</td>
                    <td>{{ $cpmk->kode }}</td>
                    <td>{{ $cpmk->judul }}</td>

                    <td>
                        @if ($cpmk->profesis->count() > 0)
                            <ol>
                                @foreach ($cpmk->profesis as $profesi)
                                    <li>{{ $profesi->nama }}</li>
                                @endforeach
                            </ol>
                        @else
                            Tidak ada profesi
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
