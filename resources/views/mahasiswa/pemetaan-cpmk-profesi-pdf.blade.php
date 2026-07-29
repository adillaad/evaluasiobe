<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Pemetaan CPMK ke Profesi - {{ $mahasiswaData->nama_mhs ?? 'Mahasiswa' }}</title>
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
            width: 100px;
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

        /* ── RINGKASAN PROFESI ── */
        .prof-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 8px;
        }

        .prof-table th,
        .prof-table td {
            border: 1px solid #aaa;
            padding: 3px 5px;
            vertical-align: top;
        }

        .prof-table thead th {
            background-color: #e8e8e8;
            font-weight: bold;
            text-align: center;
        }

        .prof-table td.center {
            text-align: center;
        }

        .prof-table td.right {
            text-align: right;
            font-weight: bold;
        }

        /* ── PROGRESS BAR (via box) ── */
        .bar-wrap {
            width: 100%;
            height: 6px;
            background: #ddd;
            border-radius: 3px;
            display: block;
        }

        .bar-fill {
            height: 6px;
            border-radius: 3px;
            display: block;
        }

        /* ── DETAIL PROFESI ── */
        .box {
            border: 1px solid #ccc;
            padding: 5px 6px;
            margin-bottom: 6px;
            page-break-inside: avoid;
        }

        .box-header {
            font-weight: bold;
            font-size: 9.5pt;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
            margin-bottom: 4px;
        }

        .box-meta {
            font-size: 8pt;
            font-weight: normal;
            color: #444;
        }

        .cpmk-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }

        .cpmk-table td {
            padding: 2px 4px;
            border-bottom: 1px dotted #ccc;
            vertical-align: middle;
        }

        .cpmk-table tr:last-child td {
            border-bottom: none;
        }

        .td-kode {
            width: 15%;
            font-weight: bold;
            white-space: nowrap;
        }

        .td-desc {
            width: 52%;
            word-wrap: break-word;
            line-height: 1.15;
        }

        .td-bar {
            width: 25%;
            vertical-align: middle;
        }

        .td-pct {
            width: 8%;
            text-align: right;
            font-weight: bold;
            white-space: nowrap;
        }

        /* ── BADGE ── */
        .badge {
            display: inline-block;
            padding: 1px 4px;
            font-size: 7.5pt;
            border: 1px solid #999;
            white-space: nowrap;
            border-radius: 2px;
        }

        .badge-ok {
            background: #c8e6c9;
            color: #1b5e20;
            border-color: #81c784;
        }

        .badge-no {
            background: #ffccbc;
            color: #b71c1c;
            border-color: #ef9a9a;
        }

        /* ── WARNA PROFESI ── */
        /* digunakan inline via style attr */

        /* ── RINGKASAN CPMK ── */
        .sum-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 6px;
        }

        .sum-table td {
            padding: 2px 4px;
            border-bottom: 1px dotted #ccc;
            vertical-align: middle;
        }

        .sum-table tr:last-child td {
            border-bottom: none;
        }

        /* ── FOOTER ── */
        .footer {
            margin-top: 10px;
            padding-top: 4px;
            border-top: 1px solid #999;
            font-size: 7.5pt;
            text-align: center;
            line-height: 1.2;
        }

        .text-center {
            text-align: center;
        }

        .empty-msg {
            font-style: italic;
            color: #666;
        }

        .fw-bold {
            font-weight: bold;
        }
    </style>
</head>

<body>

    @php
        $logoPath = public_path('assets/img/logo_unila.png');
        $logoBase64 = null;
        if (file_exists($logoPath)) {
            $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
            $mime = in_array($ext, ['png', 'jpg', 'jpeg']) ? $ext : 'png';
            $logoBase64 = 'data:image/' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        // Helper warna berdasarkan persentase
        $pctColor = fn($v) => $v <= 50 ? '#D55E00' : ($v <= 74 ? '#E69F00' : '#0072B2');
        $pctBg = fn($v) => $v <= 50 ? '#FDEBD0' : ($v <= 74 ? '#FEF9E7' : '#D6EAF8');

        $top = $profesiDenganDetail->first();
        $avgAll = round($allCpmks->avg('nilai') ?? 0, 1);
    @endphp

    {{-- ── KOP ── --}}
    <div class="kop">
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    @if ($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo">
                    @else
                        <div style="font-size:7pt;color:#999;">[LOGO]</div>
                    @endif
                </td>
                <td class="kop-text">
                    <div class="kementerian">KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</div>
                    <div class="universitas">UNIVERSITAS LAMPUNG</div>
                    <div class="alamat">Jl. Prof. Dr. Soemantri Brojonegoro No. 1 Bandar Lampung</div>
                    <div class="alamat">Telp: 702767, 702971, 703475, 702673, 701252, 701609</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ── JUDUL ── --}}
    <div class="judul">PEMETAAN CAPAIAN PEMBELAJARAN MATA KULIAH (CPMK) KE PROFESI</div>

    {{-- ── INFO MAHASISWA ── --}}
    <table class="info-table">
        <tr>
            <td class="info-label">Nama</td>
            <td class="info-sep">:</td>
            <td><strong>{{ $mahasiswaData->nama_mhs ?? ($mahasiswaData->Nama ?? '-') }}</strong></td>
            <td class="info-label">Program Studi</td>
            <td class="info-sep">:</td>
            <td>{{ $prodi->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">NPM</td>
            <td class="info-sep">:</td>
            <td>{{ $mahasiswaData->npm ?? ($mahasiswaData->NPM ?? '-') }}</td>
            <td class="info-label">Total CPMK Dinilai</td>
            <td class="info-sep">:</td>
            <td>{{ $allCpmks->count() }} CPMK &nbsp;|&nbsp;
                Rata-rata: <strong style="color:{{ $pctColor($avgAll) }};">{{ $avgAll }}%</strong>
            </td>
        </tr>
        <tr>
            <td class="info-label">Tanggal Cetak</td>
            <td class="info-sep">:</td>
            <td>{{ date('d F Y') }}</td>
            <td class="info-label">Rekomendasi Utama</td>
            <td class="info-sep">:</td>
            <td><strong>{{ $top ? $top['nama'] : '-' }}</strong>
                @if ($top)
                    <span
                        style="color:{{ $pctColor($top['match_percentage']) }};">({{ $top['match_percentage'] }}%)</span>
                @endif
            </td>
        </tr>
    </table>

    {{-- ══════════════════════════════════════
     BAGIAN 1 — RINGKASAN SEMUA PROFESI
══════════════════════════════════════ --}}
    <div class="section-title">Ringkasan Kecocokan Profesi</div>
    <table class="prof-table">
        <thead>
            <tr>
                <th width="4%">#</th>
                <th width="35%">Nama Profesi</th>
                <th width="13%">CPMK Match</th>
                <th width="30%">Kecocokan</th>
                <th width="10%">%</th>
                <th width="8%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($profesiDenganDetail as $i => $p)
                @php
                    $c = $pctColor($p['match_percentage']);
                    $bg = $pctBg($p['match_percentage']);
                @endphp
                <tr style="background:{{ $i === 0 ? $bg : 'transparent' }};">
                    <td class="center">{{ $i + 1 }}</td>
                    <td>
                        <strong>{{ $p['nama'] }}</strong>
                        @if ($i === 0)
                            <span style="font-size:7pt;color:#666;"> ★ Tertinggi</span>
                        @endif
                    </td>
                    <td class="center">{{ $p['total_cpmk_matched'] }}/{{ $p['total_cpmk_required'] }}</td>
                    <td style="vertical-align:middle;padding-top:5px;">
                        <span class="bar-wrap">
                            <span class="bar-fill"
                                style="width:{{ min($p['match_percentage'], 100) }}%;background:{{ $c }};"></span>
                        </span>
                    </td>
                    <td class="right" style="color:{{ $c }};">{{ $p['match_percentage'] }}%</td>
                    <td class="center" style="font-size:7.5pt;color:{{ $c }};">{{ $p['status'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ══════════════════════════════════════
     BAGIAN 2 — DETAIL CPMK PER PROFESI
══════════════════════════════════════ --}}
    <div class="section-title">Detail CPMK per Profesi</div>

    @foreach ($profesiDenganDetail as $i => $p)
        @php $c = $pctColor($p['match_percentage']); @endphp
        <div class="box">
            <div class="box-header">
                {{ $i + 1 }}. {{ $p['nama'] }}
                <span class="box-meta">
                    &nbsp;—&nbsp;
                    Kecocokan: <span
                        style="color:{{ $c }}; font-weight:bold;">{{ $p['match_percentage'] }}%</span>
                    &nbsp;|&nbsp; {{ $p['total_cpmk_matched'] }}/{{ $p['total_cpmk_required'] }} CPMK dinilai
                    &nbsp;|&nbsp; {{ $p['status'] }}
                </span>
            </div>
            <table class="cpmk-table">
                <tbody>
                    @foreach ($p['cpmks'] as $cpmk)
                        @php $cc = $pctColor($cpmk['nilai']); @endphp
                        <tr>
                            <td class="td-kode" style="color:{{ $cc }};">{{ $cpmk['kode'] }}</td>
                            <td class="td-desc">{{ $cpmk['deskripsi'] }}</td>
                            <td class="td-bar">
                                @if ($cpmk['nilai'] > 0)
                                    <span class="bar-wrap">
                                        <span class="bar-fill"
                                            style="width:{{ min($cpmk['nilai'], 100) }}%;background:{{ $cc }};"></span>
                                    </span>
                                @else
                                    <span style="font-size:7pt;color:#999;font-style:italic;">Belum dinilai</span>
                                @endif
                            </td>
                            <td class="td-pct" style="color:{{ $cc }};">
                                @if ($cpmk['nilai'] > 0)
                                {{ $cpmk['nilai'] }}%@else—
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- MK terkait profesi ini (ringkas, hanya MK yang belum diambil) --}}
            @php
                $belumDiambil = collect($p['cpmks'])
                    ->flatMap(fn($c) => collect($c['courses']))
                    ->where('diambil', false)
                    ->unique('kode')
                    ->values();
            @endphp
            @if ($belumDiambil->isNotEmpty())
                <div style="margin-top:4px;font-size:7.5pt;color:#555;border-top:1px dotted #ccc;padding-top:3px;">
                    <strong>MK belum diambil:</strong>
                    {{ $belumDiambil->map(fn($m) => $m['kode'] . ' (' . $m['nama'] . ')')->implode(', ') }}
                </div>
            @endif
        </div>
    @endforeach

    {{-- ══════════════════════════════════════
     BAGIAN 3 — RINGKASAN SEMUA CPMK
══════════════════════════════════════ --}}
    <div class="section-title">Capaian Semua CPMK Mahasiswa</div>
    <table class="sum-table">
        <tbody>
            @foreach ($allCpmks->sortByDesc('nilai') as $cpmk)
                @php $cc = $pctColor($cpmk['nilai']); @endphp
                <tr>
                    <td style="width:14%;font-weight:bold;color:{{ $cc }};white-space:nowrap;">
                        {{ $cpmk['kode'] }}</td>
                    <td style="width:46%;font-size:8pt;word-wrap:break-word;">{{ $cpmk['deskripsi'] }}</td>
                    <td style="width:28%;vertical-align:middle;padding-top:4px;">
                        <span class="bar-wrap">
                            <span class="bar-fill"
                                style="width:{{ min($cpmk['nilai'], 100) }}%;background:{{ $cc }};"></span>
                        </span>
                    </td>
                    <td
                        style="width:7%;text-align:right;font-weight:bold;color:{{ $cc }};white-space:nowrap;">
                        {{ $cpmk['nilai'] }}%</td>
                    <td style="width:5%;text-align:center;font-size:7pt;color:{{ $cc }};">
                        {{ $cpmk['status'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ── FOOTER ── --}}
    <div class="footer">
        Dokumen ini dicetak otomatis oleh Sistem Pemetaan CPMK ke Profesi.<br>
        Dicetak pada: {{ date('d F Y H:i:s') }} &nbsp;|&nbsp; <em>Universitas Lampung — Sistem Penilaian Berbasis
            Kompetensi</em>
    </div>

</body>

</html>
