<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Rekomendasi Mata Kuliah - {{ $mahasiswaData->Nama ?? $mahasiswaData->nama ?? 'Mahasiswa' }}</title>
    <style>
        @page {
            margin: 10mm 10mm 12mm 10mm;
            size: A4 portrait;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 9pt;
            line-height: 1.2;
            color: #000;
            margin: 0;
            padding: 0;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm 10mm 12mm 10mm;
            }

            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }
        }

        /* ── KOP ── */
        .kop {
            border-bottom: 2px double #000;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-table td {
            vertical-align: middle;
            padding: 0;
        }

        .kop-logo {
            width: 62px;
        }

        .kop-logo img {
            width: 52px;
            height: auto;
        }

        .kop-text {
            text-align: center;
        }

        .kementerian {
            font-weight: bold;
            font-size: 9.5pt;
            text-transform: uppercase;
            line-height: 1.1;
        }

        .universitas {
            font-weight: bold;
            font-size: 13pt;
            text-transform: uppercase;
            margin: 2px 0;
        }

        .alamat {
            font-size: 7.5pt;
            line-height: 1.1;
        }

        /* ── JUDUL ── */
        .judul {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            text-transform: uppercase;
            margin: 8px 0 10px;
        }

        /* ── INFO ── */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9pt;
        }

        .info-table td {
            padding: 1.5px 3px;
            vertical-align: top;
        }

        .info-label {
            width: 120px;
            font-weight: bold;
        }

        .info-sep {
            width: 8px;
        }

        /* ── SECTION ── */
        .section-title {
            font-weight: bold;
            font-size: 9.5pt;
            text-transform: uppercase;
            margin: 10px 0 5px;
            padding-bottom: 2px;
            border-bottom: 1px solid #000;
        }

        /* ── TABEL REKOMENDASI ── */
        .mk-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 8px;
        }

        .mk-table th,
        .mk-table td {
            border: 1px solid #333;
            padding: 4px 5px;
            vertical-align: middle;
        }

        .mk-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .cpmk-subtable {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-top: 3px;
        }

        .cpmk-subtable td {
            border: none;
            padding: 1px 2px;
        }

        /* ── BADGE ── */
        .badge {
            display: inline-block;
            padding: 1px 5px;
            font-size: 7.5pt;
            font-weight: bold;
            border-radius: 2px;
            border: 1px solid #333;
        }

        .badge-kurang {
            background-color: #fce8e6;
            color: #c5221f;
        }

        .badge-cukup {
            background-color: #fef7e0;
            color: #b06000;
        }

        .badge-baik {
            background-color: #e6f4ea;
            color: #137333;
        }

        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 8.5pt;
        }

        .ttd-table td {
            vertical-align: top;
            padding: 2px;
        }
    </style>
</head>

<body>

    {{-- ── KOP SURAT ── --}}
    <div class="kop">
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    @php
                        $logoPath = public_path('assets/img/logo-unila.png');
                        if (!file_exists($logoPath)) {
                            $logoPath = public_path('assets/images/logo.png');
                        }
                    @endphp
                    @if (file_exists($logoPath))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="Logo">
                    @endif
                </td>
                <td class="kop-text">
                    <div class="kementerian">KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</div>
                    <div class="universitas">UNIVERSITAS LAMPUNG</div>
                    <div class="alamat">
                        Jalan Prof. Dr. Sumantri Brojonegoro No. 1 Bandar Lampung 35145<br>
                        Telepon (0721) 701609, Fax (0721) 702767 | Laman: www.unila.ac.id
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ── JUDUL ── --}}
    <div class="judul">REKOMENDASI PENGAMBILAN MATA KULIAH</div>

    {{-- ── INFO MAHASISWA ── --}}
    <table class="info-table">
        <tr>
            <td class="info-label">Nama Mahasiswa</td>
            <td class="info-sep">:</td>
            <td><strong>{{ $mahasiswaData->Nama ?? $mahasiswaData->nama ?? auth()->user()->name }}</strong></td>
            <td class="info-label">Program Studi</td>
            <td class="info-sep">:</td>
            <td>{{ $prodi->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">NPM</td>
            <td class="info-sep">:</td>
            <td>{{ $mahasiswaData->NPM ?? $mahasiswaData->npm ?? '-' }}</td>
            <td class="info-label">Jenjang Pendidikan</td>
            <td class="info-sep">:</td>
            <td>{{ $prodi->jenjang ?? 'S1' }}</td>
        </tr>
        <tr>
            <td class="info-label">Tahun Masuk / Angkatan</td>
            <td class="info-sep">:</td>
            <td>{{ $mahasiswaData->angkatan ?? '-' }}</td>
            <td class="info-label">Progres SKS Lulus</td>
            <td class="info-sep">:</td>
            <td>{{ $sksLulus }} / 144 SKS</td>
        </tr>
    </table>

    {{-- ── DAFTAR REKOMENDASI MK ── --}}
    <div class="section-title">Daftar Mata Kuliah yang Direkomendasikan</div>
    <table class="mk-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 65px;">Kode MK</th>
                <th>Nama Mata Kuliah</th>
                <th style="width: 45px;">Smt</th>
                <th style="width: 40px;">SKS</th>
                <th style="width: 65px;">Rata-rata CPMK</th>
                <th>CPMK Terkait & Kebutuhan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rekomendasiMk as $index => $mk)
                @php
                    $avg = $mk['avg_cpmk'];
                    $badgeClass = $avg <= 50 ? 'badge-kurang' : ($avg <= 74 ? 'badge-cukup' : 'badge-baik');
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $mk['kode'] }}</td>
                    <td>
                        <strong>{{ $mk['nama'] }}</strong>
                        @if (!empty($mk['rumpun']))
                            <br><small style="color: #555;">Rumpun: {{ $mk['rumpun'] }}</small>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $mk['semester'] }}</td>
                    <td style="text-align: center;">{{ $mk['sks'] }}</td>
                    <td style="text-align: center;">
                        <span class="badge {{ $badgeClass }}">{{ round($avg, 1) }}%</span>
                    </td>
                    <td>
                        <table class="cpmk-subtable">
                            @foreach ($mk['cpmk_terkait'] as $c)
                                <tr>
                                    <td style="width: 70px; font-weight: bold;">{{ $c['kode'] }}</td>
                                    <td>{{ $c['deskripsi'] }}</td>
                                    <td style="width: 45px; text-align: right; font-weight: bold;">{{ $c['persentase'] }}%</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 15px;">
                        Tidak ada mata kuliah rekomendasi lanjutan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ── TANDA TANGAN ── --}}
    <table class="ttd-table">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%; text-align: center;">
                Bandar Lampung, {{ date('d F Y') }}<br>
                Ketua Program Studi {{ $prodi->nama ?? '' }},<br>
                <br><br><br><br>
                <strong><u>( ________________________ )</u></strong><br>
                NIP.
            </td>
        </tr>
    </table>

</body>

</html>
