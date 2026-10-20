<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <title>{{ $selectedFakultas ? 'Laporan Evaluasi CPL - ' . $selectedFakultas['nama'] : 'Laporan Evaluasi CPL Fakultas - ' . ($universitas->nama ?? 'Universitas') }}</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 10mm 12mm 10mm 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 8pt;
            line-height: 1.35;
            color: #000000;
            margin: 0;
            padding: 0;
        }

        /* ── KOP SURAT RESMI UNIVERSITAS LAMPUNG ── */
        .kop {
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
            width: 70px;
            text-align: center;
        }

        .kop-logo img {
            width: 65px;
            height: auto;
        }

        .kop-text {
            text-align: center;
            padding-left: 8px;
        }

        .kementerian {
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.15;
        }

        .universitas {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 2px 0;
            letter-spacing: 0.5px;
        }

        .fakultas-title {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
            letter-spacing: 0.3px;
        }

        .alamat,
        .kontak {
            font-size: 7.5pt;
            line-height: 1.2;
            color: #222222;
        }

        .kop-line {
            border-top: 2px solid #000000;
            border-bottom: 1px solid #000000;
            height: 2px;
            margin-top: 5px;
            margin-bottom: 10px;
        }

        /* ── JUDUL DOKUMEN ── */
        .doc-title-container {
            text-align: center;
            margin-bottom: 10px;
        }

        .doc-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: underline;
        }

        .doc-subtitle {
            font-size: 8.5pt;
            margin-top: 2px;
            color: #333333;
            letter-spacing: 0.3px;
        }

        /* ── METADATA / INFO TABLE ── */
        .info-box {
            width: 100%;
            border: 1px solid #333333;
            background-color: #fafafa;
            margin-bottom: 10px;
            padding: 3px 6px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 2.5px 4px;
            font-size: 8pt;
            vertical-align: top;
        }

        .info-label {
            width: 18%;
            font-weight: bold;
            color: #111111;
        }

        .info-sep {
            width: 2%;
            text-align: center;
            font-weight: bold;
        }

        .info-val {
            width: 30%;
            color: #000000;
        }

        /* ── SECTION BOX & HEADERS ── */
        .section-box {
            margin-bottom: 12px;
            page-break-inside: auto;
        }

        .section-header {
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1.5px solid #000000;
            padding-bottom: 2px;
            margin-bottom: 6px;
            letter-spacing: 0.3px;
        }

        .section-desc {
            font-size: 7.5pt;
            font-style: italic;
            color: #333333;
            margin-bottom: 5px;
        }

        /* ── FORMAL DATA TABLES ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 7.5pt;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #444444;
            padding: 4px 5px;
            vertical-align: middle;
        }

        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 7.5pt;
            letter-spacing: 0.2px;
        }

        .data-table tfoot th,
        .data-table tfoot td {
            background-color: #f7f7f7;
            font-weight: bold;
            border-top: 1.5px solid #000000;
            padding: 4px 5px;
        }

        .text-center { text-align: center !important; }
        .text-left { text-align: left !important; }
        .text-right { text-align: right !important; }
        .fw-bold { font-weight: bold !important; }

        /* ── CHART IMAGE ── */
        .chart-box {
            text-align: center;
            margin: 6px 0;
            page-break-inside: avoid;
        }

        .chart-box img {
            max-width: 95%;
            max-height: 180px;
            height: auto;
            border: 1px solid #cccccc;
            padding: 3px;
            background-color: #ffffff;
        }

        .figure-caption {
            font-size: 7pt;
            font-style: italic;
            margin-top: 2px;
            color: #444444;
        }

        /* ── SIGNATURE SECTION ── */
        .signature-section {
            width: 100%;
            margin-top: 12px;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            vertical-align: top;
            padding: 0;
        }

        .notes-box {
            font-size: 7pt;
            color: #333333;
            line-height: 1.35;
            padding-right: 15px;
        }

        .notes-title {
            font-weight: bold;
            color: #000000;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .sig-block {
            text-align: center;
            font-size: 8pt;
        }

        .sig-space {
            height: 48px;
        }

        .sig-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* ── FOOTER ── */
        .footer {
            margin-top: 10px;
            border-top: 1px solid #777777;
            padding-top: 3px;
            font-size: 6.8pt;
            color: #555555;
            width: 100%;
        }
    </style>
</head>

<body>
    @php
        $logoPath = public_path('assets/img/logo_unila.png');
        $logoBase64 = null;
        if (file_exists($logoPath)) {
            $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
            $data = file_get_contents($logoPath);
            $mime = in_array($ext, ['png', 'jpg', 'jpeg']) ? $ext : 'png';
            $logoBase64 = 'data:image/' . $mime . ';base64,' . base64_encode($data);
        }

        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $tglCetak = date('d') . ' ' . ($bulanIndo[(int)date('m')] ?? date('F')) . ' ' . date('Y');

        $fakultasName = $selectedFakultas['nama'] ?? ($fakultas->nama ?? 'Fakultas');
        $totalProdi = $selectedFakultas['total_prodi'] ?? 0;
        $totalMhs = $selectedFakultas['total_mhs'] ?? 0;
        $avgSkor = $selectedFakultas['avg_skor_cpl'] ?? 0.0;
        $avgCapaian = $selectedFakultas['avg_capaian_cpl'] ?? 0.0;

        $cplMeta = $selectedFakultasCplData['cpl_meta'] ?? [];
        $cplScores = $selectedFakultasCplData['cpl_scores'] ?? [];
        $cplCapaians = $selectedFakultasCplData['cpl_capaians'] ?? [];
        $yearlyAvgSkor = $selectedFakultasCplData['yearly_avg_skor'] ?? [];
        $yearlyAvgCapaian = $selectedFakultasCplData['yearly_avg_capaian'] ?? [];
        $years = $availableYears ?? [];
    @endphp

    {{-- ── KOP SURAT RESMI ── --}}
    <div class="kop">
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    @if ($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo Universitas Lampung">
                    @endif
                </td>
                <td class="kop-text">
                    <div class="kementerian">KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</div>
                    <div class="universitas">{{ strtoupper($universitas->nama ?? 'UNIVERSITAS LAMPUNG') }}</div>
                    <div class="fakultas-title">{{ strtoupper($fakultasName) }}</div>
                    <div class="alamat">Jalan Prof. Dr. Sumantri Brojonegoro No. 1, Gedong Meneng, Bandar Lampung 35145</div>
                    <div class="kontak">Telepon: (0721) 701609 &bull; Faksimile: (0721) 702767 &bull; Laman: www.unila.ac.id</div>
                </td>
            </tr>
        </table>
        <div class="kop-line"></div>
    </div>

    {{-- ── JUDUL DOKUMEN ── --}}
    <div class="doc-title-container">
        <div class="doc-title">LAPORAN EVALUASI CAPAIAN PEMBELAJARAN LULUSAN (CPL)</div>
        <div class="doc-subtitle">Evaluasi Skor Asesmen & Persentase Ketercapaian Fakultas Berbasis Outcome-Based Education (OBE)</div>
    </div>

    {{-- ── METADATA FAKULTAS ── --}}
    <div class="info-box">
        <table class="info-table">
            <tr>
                <td class="info-label">Fakultas</td>
                <td class="info-sep">:</td>
                <td class="info-val"><strong>{{ $fakultasName }}</strong></td>
                <td class="info-label">Perguruan Tinggi</td>
                <td class="info-sep">:</td>
                <td class="info-val">{{ $universitas->nama ?? 'Universitas Lampung' }}</td>
            </tr>
            <tr>
                <td class="info-label">Total Program Studi</td>
                <td class="info-sep">:</td>
                <td class="info-val"><strong>{{ $totalProdi }} Program Studi</strong></td>
                <td class="info-label">Tanggal Cetak</td>
                <td class="info-sep">:</td>
                <td class="info-val">{{ $tglCetak }}</td>
            </tr>
            <tr>
                <td class="info-label">Total Mahasiswa Dinilai</td>
                <td class="info-sep">:</td>
                <td class="info-val"><strong>{{ $totalMhs }} Mahasiswa</strong></td>
                <td class="info-label">Status Dokumen</td>
                <td class="info-sep">:</td>
                <td class="info-val">Laporan Resmi Penjaminan Mutu Akademik</td>
            </tr>
        </table>
    </div>

    {{-- ── BAGIAN I: SKOR CPL DISUSUN PER TAHUN ── --}}
    <div class="section-box">
        <div class="section-header">I. Skor Asesmen CPL Fakultas Disusun Per Tahun (Rentang Nilai 0 &ndash; 100)</div>
        <div class="section-desc">Evaluasi perolehan rata-rata skor asesmen butir CPL berdasarkan data tahun akademik yang telah ditempuh:</div>

        @if (!empty($radarChartImg))
            <div class="chart-box">
                <img src="{{ $radarChartImg }}" alt="Diagram Radar Skor Capaian CPL">
                <div class="figure-caption">Gambar 1. Diagram Radar Skor Capaian CPL {{ $fakultasName }}</div>
            </div>
        @endif

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 65px;">Kode</th>
                    <th style="width: 110px;">Aspek</th>
                    <th>Judul / Deskripsi Butir CPL</th>
                    @foreach ($years as $yr)
                        <th style="width: 55px;">{{ $yr }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @forelse ($cplMeta as $kode => $meta)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center fw-bold">{{ $kode }}</td>
                        <td class="text-center">{{ $meta['aspek'] ?? '-' }}</td>
                        <td>{{ $meta['judul'] ?? '-' }}</td>
                        @foreach ($years as $yr)
                            @php $val = $cplScores[$kode][$yr] ?? 0; @endphp
                            <td class="text-center {{ $val > 0 ? 'fw-bold' : '' }}">
                                {{ $val > 0 ? number_format($val, 1) : '-' }}
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 4 + count($years) }}" class="text-center">Belum ada data butir CPL pada fakultas ini.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-right">Rata-rata Skor CPL Fakultas:</th>
                    @foreach ($years as $yr)
                        <th class="text-center">
                            {{ isset($yearlyAvgSkor[$yr]) && $yearlyAvgSkor[$yr] > 0 ? number_format($yearlyAvgSkor[$yr], 1) : '-' }}
                        </th>
                    @endforeach
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- ── BAGIAN II: KETERCAPAIAN CPL (%) DISUSUN PER TAHUN ── --}}
    <div class="section-box" style="page-break-before: auto;">
        <div class="section-header">II. Persentase Ketercapaian CPL (%) Fakultas Disusun Per Tahun</div>
        <div class="section-desc">Tabel akumulasi persentase kelulusan mata kuliah pendukung CPL di fakultas yang disusun per tahun:</div>

        @if (!empty($radarChartCapaianCplImg))
            <div class="chart-box">
                <img src="{{ $radarChartCapaianCplImg }}" alt="Diagram Radar Persentase Ketercapaian CPL">
                <div class="figure-caption">Gambar 2. Diagram Radar Persentase Ketercapaian CPL {{ $fakultasName }}</div>
            </div>
        @endif

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 65px;">Kode</th>
                    <th style="width: 110px;">Aspek</th>
                    <th>Judul / Deskripsi Butir CPL</th>
                    @foreach ($years as $yr)
                        <th style="width: 55px;">{{ $yr }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @forelse ($cplMeta as $kode => $meta)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center fw-bold">{{ $kode }}</td>
                        <td class="text-center">{{ $meta['aspek'] ?? '-' }}</td>
                        <td>{{ $meta['judul'] ?? '-' }}</td>
                        @foreach ($years as $yr)
                            @php $pct = $cplCapaians[$kode][$yr] ?? 0; @endphp
                            <td class="text-center {{ $pct > 0 ? 'fw-bold' : '' }}">
                                {{ $pct > 0 ? number_format($pct, 1) . '%' : '-' }}
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 4 + count($years) }}" class="text-center">Belum ada data ketercapaian CPL pada fakultas ini.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-right">Rata-rata Ketercapaian CPL Fakultas:</th>
                    @foreach ($years as $yr)
                        <th class="text-center">
                            {{ isset($yearlyAvgCapaian[$yr]) && $yearlyAvgCapaian[$yr] > 0 ? number_format($yearlyAvgCapaian[$yr], 1) . '%' : '-' }}
                        </th>
                    @endforeach
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- ── LEMBAR PENGESAHAN & TANDA TANGAN ── --}}
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td style="width: 58%; padding-right: 15px;">
                    <div class="notes-box">
                        <div class="notes-title">Catatan Akademik & Pengesahan:</div>
                        <div>1. Laporan ini merupakan dokumen resmi evaluasi Capaian Pembelajaran Lulusan (CPL) Fakultas berbasis kurikulum Outcome-Based Education (OBE).</div>
                        <div>2. Seluruh skor dan ketercapaian diperoleh dari asesmen perkuliahan mahasiswa pada program studi di lingkungan fakultas yang dihitung secara sistematis melalui Sistem Evaluasi OBE Universitas Lampung.</div>
                        <div>3. Dokumen ini sah dan dapat digunakan sebagai bahan monitoring kurikulum, audit penjaminan mutu fakultas, serta pelaporan akreditasi institusi.</div>
                    </div>
                </td>
                <td style="width: 42%;">
                    <div class="sig-block">
                        <div>Bandar Lampung, {{ $tglCetak }}</div>
                        <div style="margin-top: 2px;">Mengetahui,</div>
                        <div style="font-weight: bold;">Dekan {{ $fakultasName }}</div>
                        <div class="sig-space"></div>
                        <div class="sig-name">( ____________________________________ )</div>
                        <div style="margin-top: 2px; font-size: 7.5pt;">NIP. ................................................................</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ── FOOTER ── --}}
    <div class="footer">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="text-align: left;">Sistem Informasi Evaluasi OBE &bull; {{ $universitas->nama ?? 'Universitas Lampung' }} &bull; {{ $fakultasName }}</td>
                <td style="text-align: right;">Dicetak secara otomatis pada: {{ date('d/m/Y H:i:s') }} WIB</td>
            </tr>
        </table>
    </div>
</body>

</html>
