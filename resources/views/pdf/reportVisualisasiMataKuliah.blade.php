<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Visualisasi Mata Kuliah {{ $course }}</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 15pt;
            text-align: justify;
        }

        .container {
            width: 100%;
        }

        h2,
        h3 {
            text-align: center;
            margin-bottom: 10px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }

        .chart {
            text-align: center;
            margin-top: 30px;
        }

        .chart img {
            width: 100%;
            max-width: 800px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Laporan Visualisasi Mata Kuliah {{ $course }}</h2>
        <div class="chart">
            <h4>CPMK Batch</h4>
            <img src="{{ $radarChartAngkatanImg }}">
        </div>

        <div>
            <h3>Summary</h3>
            <p>{!! $summary !!}</p>
        </div>

        @if (!empty($descriptions) && is_array($descriptions))
            <h3>Descriptions</h3>
            <ol>
                @foreach ($descriptions as $desc)
                    <li>{{ $desc }}</li>
                @endforeach
            </ol>
        @endif

        <h3>Questions of CPMK Batch with the Lowest Average</h3>
        <table border="1" width="100%" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Types of Assessment</th>
                    <th>Questions</th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($soalTerendah))
                    @foreach ($soalTerendah as $row)
                        <tr>
                            <td>{{ $row['no'] }}</td>
                            <td>{{ $row['types_of_assessment'] }}</td>
                            <td>{{ $row['question'] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4">Tidak ada data tersedia</td>
                    </tr>
                @endif
            </tbody>
        </table>

</body>

</html>
