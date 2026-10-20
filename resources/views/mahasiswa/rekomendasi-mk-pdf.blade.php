<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Rekomendasi Mata Kuliah - {{ $mahasiswaData->Nama ?? $mahasiswaData->nama ?? 'Mahasiswa' }}</title>
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
            width: 130px;
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

        /* ===== BADGE ===== */
        .badge {
            display: inline-block;
            padding: 1px 5px;
            font-size: 7.5pt;
            border: 1px solid #000;
            text-align: center;
            white-space: nowrap;
        }

        .bg-baik,
        .bg-sangat-baik {
            background-color: #0072B2 !important;
            color: #fff !important;
        }

        .bg-cukup {
            background-color: #E69F00 !important;
            color: #fff !important;
        }

        .bg-kurang,
        .bg-perlu-peningkatan {
            background-color: #D55E00 !important;
            color: #fff !important;
        }

        /* ===== SUMMARY BOX ===== */
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
            width: 180px;
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

<body @if (!empty($isPrint)) onload="window.print()" @endif>

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
    <div class="judul">REKOMENDASI PENGAMBILAN MATA KULIAH</div>

    {{-- INFO --}}
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
            <td><strong>{{ method_exists($prodi, 'getJenjangFullLabel') ? $prodi->getJenjangFullLabel() : ($prodi->jenjang ?? 'S1') }}</strong></td>
        </tr>
        <tr>
            <td class="info-label">Tahun Masuk / Angkatan</td>
            <td class="info-sep">:</td>
            <td>{{ $mahasiswaData->angkatan ?? '-' }}</td>
            <td class="info-label">Progres SKS Lulus</td>
            <td class="info-sep">:</td>
            <td><strong>{{ $sksLulus }}</strong> / {{ (method_exists($prodi, 'getTargetSks') ? $prodi->getTargetSks() : ($prodi->target_sks ?? 144)) }} SKS</td>
        </tr>
    </table>

    {{-- DAFTAR REKOMENDASI MATA KULIAH --}}
    <div class="section-title">Daftar Mata Kuliah yang Direkomendasikan</div>
    @forelse ($rekomendasiMk as $mk)
        @php
            $cpmks = $mk['cpmk_terkait'] ?? [];
            $avg = $mk['avg_cpmk'] ?? 0;
            $prasyarat = $mk['prasyarat'] ?? null;
            $prasyaratTerpenuhi = $mk['prasyarat_terpenuhi'] ?? true;
        @endphp
        <div class="box">
            <div class="mk-header">
                {{ $mk['kode'] ?? '-' }} - {{ strtoupper($mk['nama'] ?? '-') }}
                <div class="mk-meta">
                    @if (!empty($mk['semester']))
                        Semester {{ $mk['semester'] }}
                    @endif
                    @if (!empty($mk['sks']))
                        | SKS: {{ $mk['sks'] }}
                    @endif
                    @if (!empty($mk['rumpun']))
                        | Rumpun: {{ $mk['rumpun'] }}
                    @endif
                    | Rata-rata Kebutuhan CPMK: <strong>{{ number_format($avg, 2) }}%</strong>
                    @if (!empty($prasyarat) && $prasyarat !== '-')
                        | Prasyarat: {{ $prasyarat }} ({{ $prasyaratTerpenuhi ? 'Terpenuhi' : 'Belum Terpenuhi' }})
                    @endif
                </div>
            </div>
            @if (count($cpmks) > 0)
                @foreach ($cpmks as $cpmk)
                    @php
                        $cKode = is_array($cpmk) ? ($cpmk['kode'] ?? '-') : ($cpmk->kode ?? '-');
                        $cDesc = is_array($cpmk) ? ($cpmk['deskripsi'] ?? '-') : ($cpmk->deskripsi ?? '-');
                        $cNilai = is_array($cpmk) ? ($cpmk['persentase'] ?? 0) : ($cpmk->persentase ?? 0);
                        $cStatus = is_array($cpmk) ? ($cpmk['status'] ?? null) : ($cpmk->status ?? null);
                        if (!$cStatus) {
                            $cStatus = $cNilai >= 75 ? 'Baik' : ($cNilai >= 51 ? 'Cukup' : 'Perlu Peningkatan');
                        }
                        $badgeClass = ($cStatus === 'Baik' || $cStatus === 'Sangat Baik') 
                            ? 'bg-baik' 
                            : (($cStatus === 'Cukup') ? 'bg-cukup' : 'bg-kurang');
                    @endphp
                    <table class="list-table">
                        <tr>
                            <td class="td-kode">{{ $cKode }}</td>
                            <td class="td-desc">{{ $cDesc }}</td>
                            <td class="td-nilai">{{ number_format($cNilai, 2) }}%</td>
                            <td class="td-status">
                                <span class="badge {{ $badgeClass }}">
                                    {{ $cStatus }}
                                </span>
                            </td>
                        </tr>
                    </table>
                @endforeach
            @else
                <div class="empty-msg text-center">Tidak ada data CPMK terkait</div>
            @endif
        </div>
    @empty
        <div class="box text-center empty-msg">Tidak ada mata kuliah rekomendasi lanjutan</div>
    @endforelse

    {{-- RINGKASAN REKOMENDASI --}}
    @php
        $totalMk = $stats['total_mk_rekomendasi'] ?? count($rekomendasiMk);
        $totalSks = collect($rekomendasiMk)->sum('sks');
        $avgKeseluruhan = $stats['avg_keseluruhan'] ?? (count($rekomendasiMk) > 0 ? collect($rekomendasiMk)->avg('avg_cpmk') : 0);
    @endphp
    <div class="ipk-summary">
        <table class="ipk-table">
            <tr>
                <td class="ipk-label">Total MK Direkomendasikan</td>
                <td class="ipk-value">{{ $totalMk }} Mata Kuliah</td>
            </tr>
            <tr>
                <td class="ipk-label">Total SKS Rekomendasi</td>
                <td class="ipk-value">{{ $totalSks }} SKS</td>
            </tr>
            <tr>
                <td class="ipk-label"><strong>Rata-rata Kebutuhan CPMK</strong></td>
                <td class="ipk-value"><strong>{{ number_format($avgKeseluruhan, 2) }}%</strong></td>
            </tr>
        </table>
    </div>

    {{-- PETUNJUK PENILAIAN --}}
    @php
        $jenjang = strtoupper(trim($prodi->jenjang ?? ''));
        $isDoktor = in_array($jenjang, ['S3', 'S-3', 'DOKTOR', 'SUB SPESIALIS', 'SP-2', 'SP2', 'S3 TERAPAN']) || str_contains($jenjang, 'DOKTOR') || str_contains($jenjang, 'S3');
        $isMagister = in_array($jenjang, ['S2', 'S-2', 'MAGISTER', 'SPESIALIS', 'SP-1', 'SP1', 'S2 TERAPAN']) || str_contains($jenjang, 'MAGISTER') || str_contains($jenjang, 'S2') || str_contains($jenjang, 'SPESIALIS');
        $labelJenjang = $isDoktor ? 'Doktor / Subspesialis' : ($isMagister ? 'Magister / Spesialis' : ($jenjang === 'D3' ? 'Diploma III' : ($jenjang === 'D4' ? 'Sarjana Terapan (D4)' : 'Sarjana / Diploma')));
    @endphp
    <div class="section-title">Keterangan & Indikator Penilaian</div>
    <div class="box" style="font-size: 8pt; margin-bottom: 10px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding-right: 8px;">
                    <strong>Predikat Kompetensi OBE (CPL & CPMK):</strong>
                    <ul style="margin: 3px 0 0 15px; padding: 0; line-height: 1.3;">
                        <li><strong>Baik:</strong> &ge; 75.00%</li>
                        <li><strong>Cukup:</strong> 51.00% - &lt; 75.00%</li>
                        <li><strong>Perlu Peningkatan:</strong> &le; 50.00%</li>
                    </ul>
                </td>
                <td style="width: 50%; vertical-align: top; padding-left: 8px; border-left: 1px solid #ccc;">
                    <strong>Prinsip Rekomendasi Pengambilan Mata Kuliah:</strong>
                    <ul style="margin: 3px 0 0 15px; padding: 0; line-height: 1.3;">
                        <li>Mata kuliah diprioritaskan berdasarkan CPMK yang masih memerlukan peningkatan (&lt; 70.00%).</li>
                        <li>Mata kuliah memperhatikan prasyarat akademik yang telah lulus ditempuh.</li>
                    </ul>
                </td>
            </tr>
        </table>
    </div>

    {{-- TANDA TANGAN --}}
    <table style="width: 100%; border-collapse: collapse; margin-top: 15px; page-break-inside: avoid; font-size: 8.5pt;">
        <tr>
            <td style="width: 55%; font-size: 7.5pt; color: #555; line-height: 1.3; vertical-align: top; padding-right: 15px;">
                <strong>Catatan Akademik:</strong><br>
                Daftar rekomendasi mata kuliah ini dihitung secara dinamis berdasarkan pemetaan ketercapaian kompetensi CPMK yang telah ditempuh untuk mendukung penyelesaian kurikulum tepat waktu.
            </td>
            <td style="width: 45%; text-align: center; vertical-align: top;">
                Bandar Lampung, {{ date('d F Y') }}<br>
                Mengetahui,<br>
                Ketua Program Studi {{ $prodi->nama ?? '' }}<br>
                <div style="height: 50px;"></div>
                <strong><u>( ________________________ )</u></strong><br>
                NIP. ....................................................
            </td>
        </tr>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        Dokumen ini dicetak otomatis oleh sistem Rekomendasi Pengambilan Mata Kuliah.<br>
        Dicetak pada: {{ date('d F Y H:i:s') }}<br>
        <em>Universitas Lampung - Sistem Penilaian Berbasis Kompetensi</em>
    </div>

</body>

</html>
