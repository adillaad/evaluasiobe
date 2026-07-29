<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Visualisasi Angkatan {{ $angkatan }}</title>
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
            page-break-inside: auto;
        }

        .page-break {
            page-break-before: always;
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

        .mahasiswa-angkatan-table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
        }

        .mahasiswa-angkatan-thead {
            display: table-header-group;
        }

        .mahasiswa-angkatan-table th,
        .mahasiswa-angkatan-table td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Laporan Visualisasi Angkatan {{ $angkatan }}</h2>
        <table class="data-table">
            <tr>
                <th>Angkatan</th>
                <td>{{ $angkatan }}</td>
            </tr>
            <tr>
                <th>Program Studi</th>
                <td>{{ $prodi }}</td>
            </tr>
            <tr>
                <th>Universitas</th>
                <td>{{ $universitas }}</td>
            </tr>
        </table>

        <h3>CPL Calculation Courses</h3>
        <ol>
            @foreach ($courseList as $cl)
                <li>{{ $cl }}</li>
            @endforeach
        </ol>

        <div class="chart">
            <h4>CPL Achievement Percentage (%) (Batch)</h4>
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($radarChartAngkatanImg)) }}">
        </div>

        @if (!empty($descriptions) && is_array($descriptions))
            <h3>Descriptions</h3>
            <ol>
                @foreach ($descriptions as $desc)
                    <li>{{ $desc }}</li>
                @endforeach
            </ol>
        @endif

        <h3>Questions with the Lowest Average CPL</h3>
        <table border="1" width="100%" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th width="30px">No</th>
                    <th>Course Name</th>
                    <th>Types of Assessment</th>
                    <th>Questions</th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($soalTerendah))
                    @foreach ($soalTerendah as $row)
                        <tr>
                            <td width="30px">{{ $row['no'] }}</td>
                            <td>{{ $row['course_name'] }}</td>
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
        
        <h3>Mahasiswa Angkatan {{ $angkatan }}</h3>
        <table border="1" width="100%" cellspacing="0" cellpadding="5" class="mahasiswa-angkatan-table">
            <thead class="mahasiswa-angkatan-thead">
                <tr>
                    <th width="30px">No</th>
                    <th>NPM</th>
                    <th>Nama Mahasiswa</th>
                    @foreach ($cplCodes as $cplCode)
                        <th>{{ $cplCode }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @if (!empty($mahasiswaAngkatan))
                    @foreach ($mahasiswaAngkatan as $student)
                        <tr>
                            <td>{{ $student['no'] }}</td>
                            <td>{{ $student['npm'] }}</td>
                            <td>{{ $student['nama_mhs'] }}</td>
                            @foreach ($student['cpls'] as $cpl)
                                <td>{{ $cpl['nilai'] }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="{{ 3 + count($cplCodes) }}">Tidak ada data tersedia</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</body>
</html>