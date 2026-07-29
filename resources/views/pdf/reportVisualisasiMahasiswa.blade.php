<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Visualisasi Mahasiswa {{ $nama }}</title>
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
        <h2>Laporan Visualisasi Mahasiswa</h2>
        <h3>{{ $universitas }}</h3>
        <table class="data-table">
            <tr>
                <th>Nama</th>
                <td>{{ $nama }}</td>
            </tr>
            <tr>
                <th>NPM</th>
                <td>{{ $npm }}</td>
            </tr>
            <tr>
                <th>Angkatan</th>
                <td>{{ $angkatan }}</td>
            </tr>
            <tr>
                <th>Program Studi</th>
                <td>{{ $prodi }}</td>
            </tr>
        </table>

        <h3>All Course Taken</h3>
        <ol>
            @foreach ($courseList as $cl)
                <li>{{ $cl }}</li>
            @endforeach
        </ol>

        <div class="chart">
            <h4>Achievement of CPL based on Courses (%)</h4>
            <img src="{{ $radarChartCapaianCplImg }}">
        </div>

        <h3>Mata Kuliah Lulus</h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
            <thead>
                <tr>
                    <th style="border: 1px solid #000; padding: 5px; text-align: center; width: 40px;">#</th>
                    <th style="border: 1px solid #000; padding: 5px;">Mata Kuliah</th>
                    <th style="border: 1px solid #000; padding: 5px;">Kode</th>
                    <th style="border: 1px solid #000; padding: 5px; text-align: center;">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mataKuliahLulus as $mk)
                    <tr>
                        <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ $loop->iteration }}</td>
                        <td style="border: 1px solid #000; padding: 5px;">{{ $mk['courseName'] }}</td>
                        <td style="border: 1px solid #000; padding: 5px;">{{ $mk['courseCode'] }}</td>
                        <td style="border: 1px solid #000; padding: 5px; text-align: center;"><strong>{{ $mk['nilai'] }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="border: 1px solid #000; padding: 5px; text-align: center;"">Data Kosong
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <h3>Mata Kuliah Tidak Lulus</h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
            <thead>
                <tr>
                    <th style="border: 1px solid #000; padding: 5px; text-align: center; width: 40px;">#</th>
                    <th style="border: 1px solid #000; padding: 5px;">Mata Kuliah</th>
                    <th style="border: 1px solid #000; padding: 5px;">Kode</th>
                    <th style="border: 1px solid #000; padding: 5px; text-align: center;">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mataKuliahTidakLulus as $mk)
                    <tr>
                        <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ $loop->iteration }}</td>
                        <td style="border: 1px solid #000; padding: 5px;">{{ $mk['courseName'] }}</td>
                        <td style="border: 1px solid #000; padding: 5px;">{{ $mk['courseCode'] }}</td>
                        <td style="border: 1px solid #000; padding: 5px; text-align: center;"><strong>{{ $mk['nilai'] }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="border: 1px solid #000; padding: 5px; text-align: center;"">Data Kosong
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="chart">
            <h4>Achievement of CPL Scores</h4>
            <img src="{{ $radarChartImg }}">
        </div>

        @if (!empty($descriptions) && is_array($descriptions))
            <h3>Descriptions</h3>
            <ol>
                @foreach ($descriptions as $desc)
                    <li>{{ $desc }}</li>
                @endforeach
            </ol>
        @endif

        <h3>Questions with the Lowest CPL</h3>
        <table border="1" width="100%" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Course Name</th>
                    <th>Types of Assessment</th>
                    <th>Questions</th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($soalTerendah))
                    @foreach ($soalTerendah as $row)
                        <tr>
                            <td>{{ $row['no'] }}</td>
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

        <h3>Career Mapping Details Based on CPL</h3>
        <table border="1" width="100%" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Profile Career</th>
                    <th>Graduate Profile</th>
                    <th>CPL</th>
                    <th>Profile Weight</th>
                    <th>CPL Result</th>
                    <th>Profile Weight * CPL Result</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($hasilProfil))
                    @foreach ($hasilProfil as $row)
                        <tr>
                            @if (isset($row['is_first_row']) && $row['is_first_row'])
                                <td rowspan="{{ $row['total_rows'] }}">{{ $row['no'] }}</td>
                                <td rowspan="{{ $row['total_rows'] }}">{{ $row['profile_career'] }}</td>
                                <td rowspan="{{ $row['total_rows'] }}">{{ $row['graduate_profile'] }}</td>
                            @endif
                            <td>{{ $row['cpl'] }}</td>
                            <td>{{ $row['profile_weight'] }}</td>
                            <td>{{ $row['cpl_result'] }}</td>
                            <td>{{ $row['profile_weight_cpl_result'] }}</td>
                            @if (isset($row['is_first_row']) && $row['is_first_row'])
                                <td rowspan="{{ $row['total_rows'] }}">{{ $row['total'] }}</td>
                            @endif
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8">Tidak ada data tersedia</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="chart">
            <h4>Career Mapping Graph Based on CPL</h4>
            <img src="{{ $profilChartImg }}">
        </div>
    </div>

</body>

</html>
