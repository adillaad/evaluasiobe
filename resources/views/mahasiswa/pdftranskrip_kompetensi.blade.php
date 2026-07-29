<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Transkrip Kompetensi - {{ $mahasiswaData->nama_mhs ?? 'Mahasiswa' }}</title>
    <style>
        /* ===== PAGE SETUP ===== */
        @page {
            margin: 10mm 10mm 15mm 10mm;
            size: A4 portrait;
        }

        /* ===== BASE ===== */
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
            /* ✅ HAPUS width: 210mm → biarkan DomPDF atur sendiri */
        }

        /* ===== PRINT BROWSER ===== */
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm 10mm 15mm 10mm;
            }

            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }
        }

        /* ===== KOP ===== */
        .kop {
            border-bottom: 2px double #000;
            padding-bottom: 6px;
            margin-bottom: 10px;
            text-align: center;
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
            width: 65px;
            text-align: left;
        }

        .kop-logo img {
            width: 55px;
            height: auto;
        }

        .kop-text {
            text-align: center;
        }

        .kementerian {
            font-weight: bold;
            font-size: 10pt;
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
            font-size: 8pt;
            line-height: 1.1;
        }

        /* ===== JUDUL & INFO ===== */
        .judul {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            text-transform: uppercase;
            margin: 10px 0 12px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 9pt;
        }

        .info-table td {
            padding: 2px 3px;
            vertical-align: top;
        }

        .info-label {
            width: 100px;
            font-weight: bold;
        }

        .info-sep {
            width: 8px;
        }

        /* ===== SECTION & BOX ===== */
        .section-title {
            font-weight: bold;
            font-size: 10pt;
            text-transform: uppercase;
            margin: 12px 0 6px;
            padding-bottom: 3px;
            border-bottom: 1px solid #000;
        }

        .box {
            border: 1px solid #000;
            padding: 6px;
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        /* ===== MK HEADER ===== */
        .mk-header {
            font-weight: bold;
            font-size: 9.5pt;
            margin-bottom: 5px;
            padding-bottom: 4px;
            border-bottom: 1px solid #000;
        }

        .mk-meta {
            font-size: 8.5pt;
            margin-top: 2px;
            font-weight: normal;
        }

        /* ===== TABEL CPMK/CPL - SIMPLE & DOMPDF-FRIENDLY ===== */
        .list-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            /* ✅ HAPUS table-layout: fixed → biarkan auto agar DomPDF tidak error */
        }

        .list-table tr {
            border-bottom: 1px dotted #999;
        }

        .list-table tr:last-child {
            border-bottom: none;
        }

        .list-table td {
            padding: 3px 4px;
            vertical-align: top;
        }

        /* ✅ Gunakan inline style untuk lebar kolom (lebih kompatibel) */
        .td-kode {
            width: 18%;
            font-weight: bold;
            white-space: nowrap;
        }

        .td-desc {
            width: 52%;
            white-space: normal;
            word-wrap: break-word;
            line-height: 1.15;
        }

        .td-nilai {
            width: 13%;
            text-align: right;
            white-space: nowrap;
            font-weight: bold;
        }

        .td-status {
            width: 17%;
            text-align: right;
            white-space: nowrap;
        }

        /* ===== BADGE - FIX POTONG "Sangat Ba" ===== */
        .badge {
            display: inline-block;
            padding: 1px 5px;
            /* ✅ Kurangi padding */
            font-size: 7.5pt;
            border: 1px solid #000;
            text-align: center;
            /* ✅ HAPUS min-width → biarkan menyesuaikan konten */
            white-space: nowrap;
        }

        .bg-sangat-baik {
            background-color: #2ecc71 !important;
            color: #fff !important;
        }

        .bg-baik {
            background-color: #f39c12 !important;
            color: #fff !important;
        }

        .bg-cukup {
            background-color: #3498db !important;
            color: #fff !important;
        }

        .bg-kurang {
            background-color: #e74c3c !important;
            color: #fff !important;
        }

        /* ===== IPK SUMMARY ===== */
        .ipk-summary {
            margin: 12px 0 10px;
            padding: 6px;
            border: 1px solid #000;
            background: #f9f9f9;
            page-break-inside: avoid;
        }

        .ipk-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ipk-table td {
            padding: 3px 5px;
            font-size: 9pt;
        }

        .ipk-label {
            font-weight: bold;
            width: 130px;
        }

        .ipk-value {
            text-align: right;
            font-weight: bold;
        }

        /* ===== FOOTER ===== */
        .footer {
            margin-top: 12px;
            padding-top: 6px;
            border-top: 1px solid #999;
            font-size: 8pt;
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
    </style>
</head>

<body onload="window.print()">

    @php
        $logoPath = public_path('assets/img/logo_unila.png');
        $logoBase64 = null;
        if (file_exists($logoPath)) {
            $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
            $data = file_get_contents($logoPath);
            $mime = in_array($ext, ['png', 'jpg', 'jpeg']) ? $ext : 'png';
            $logoBase64 = 'data:image/' . $mime . ';base64,' . base64_encode($data);
        }
    @endphp

    {{-- KOP --}}
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

    {{-- JUDUL --}}
    <div class="judul">TRANSKRIP KOMPETENSI (SEMENTARA)</div>

    {{-- INFO --}}
    <table class="info-table">
        <tr>
            <td class="info-label">Nama</td>
            <td class="info-sep">:</td>
            <td><strong>{{ $mahasiswaData->nama_mhs ?? '-' }}</strong></td>
            <td class="info-label">Program Studi</td>
            <td class="info-sep">:</td>
            <td>{{ $prodi->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">NPM</td>
            <td class="info-sep">:</td>
            <td>{{ $mahasiswaData->npm ?? '-' }}</td>
            <td class="info-label">Angkatan</td>
            <td class="info-sep">:</td>
            <td>{{ $mahasiswaData->angkatan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Tanggal Cetak</td>
            <td class="info-sep">:</td>
            <td>{{ date('d F Y') }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>

    {{-- CPL --}}
    <div class="section-title">Capaian Pembelajaran Lulusan (CPL)</div>
    <div class="box">
        @php
            $cpls = $transkripData['cpls'] ?? [];
            usort($cpls, fn($a, $b) => strcmp($a['kode'] ?? '', $b['kode'] ?? ''));
        @endphp
        @if (count($cpls) > 0)
            @foreach ($cpls as $cpl)
                <table class="list-table">
                    <tr>
                        <td class="td-kode">{{ $cpl['kode'] ?? '-' }}</td>
                        <td class="td-desc">{{ $cpl['deskripsi'] ?? '-' }}</td>
                        <td class="td-nilai">{{ number_format($cpl['nilai'] ?? 0, 2) }}%</td>
                        <td class="td-status">
                            <span class="badge bg-{{ $cpl['badge_class'] ?? 'cukup' }}">
                                {{ $cpl['status'] ?? '-' }}
                            </span>
                        </td>
                    </tr>
                </table>
            @endforeach
        @else
            <div class="empty-msg text-center">Belum ada data CPL</div>
        @endif
    </div>

    {{-- CPMK --}}
    <div class="section-title">CPMK per Mata Kuliah</div>
    @forelse(($transkripData['courses']??[]) as $course)
        @php
            $cpmks = $course['cpmks'] ?? [];
            usort($cpmks, fn($a, $b) => strcmp($a['kode'] ?? '', $b['kode'] ?? ''));
        @endphp
        <div class="box">
            <div class="mk-header">
                {{ $course['kode'] ?? '-' }} - {{ $course['nama'] ?? '-' }}
                <div class="mk-meta">
                    {{ $course['tahun'] ?? '-' }}
                    @if ($course['semester'])
                        | Semester {{ $course['semester'] }}
                    @endif
                    @if ($course['sks'])
                        | SKS: {{ $course['sks'] }}
                    @endif
                    @if ($course['grade_huruf'])
                        | Grade: <strong>{{ $course['grade_huruf'] }}</strong>
                    @endif
                </div>
            </div>
            @if (count($cpmks) > 0)
                @foreach ($cpmks as $cpmk)
                    <table class="list-table">
                        <tr>
                            <td class="td-kode">{{ $cpmk['kode'] ?? '-' }}</td>
                            <td class="td-desc">{{ $cpmk['deskripsi'] ?? '-' }}</td>
                            <td class="td-nilai">{{ number_format($cpmk['nilai'] ?? 0, 2) }}%</td>
                            <td class="td-status">
                                <span class="badge bg-{{ $cpmk['badge_class'] ?? 'cukup' }}">
                                    {{ $cpmk['status'] ?? '-' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                @endforeach
            @else
                <div class="empty-msg text-center">Tidak ada data CPMK</div>
            @endif
        </div>
    @empty
        <div class="box text-center empty-msg">Belum ada data mata kuliah</div>
    @endforelse

    {{-- IPK --}}
    @if (isset($transkripData['total_sks']) && $transkripData['total_sks'] > 0)
        <div class="ipk-summary">
            <table class="ipk-table">
                <tr>
                    <td class="ipk-label">Total SKS</td>
                    <td class="ipk-value">{{ $transkripData['total_sks'] ?? 0 }}</td>
                </tr>
                <tr>
                    <td class="ipk-label"><strong>IPK Sementara</strong></td>
                    <td class="ipk-value"><strong>{{ number_format($transkripData['ipk'] ?? 0, 2) }}</strong></td>
                </tr>
            </table>
        </div>
    @endif

    {{-- FOOTER --}}
    <div class="footer">
        Dokumen ini dicetak otomatis oleh sistem Transkrip Kompetensi (Sementara).<br>
        Dicetak pada: {{ date('d F Y H:i:s') }}<br>
        <em>Universitas Lampung - Sistem Penilaian Berbasis Kompetensi</em>
    </div>

</body>

</html>
