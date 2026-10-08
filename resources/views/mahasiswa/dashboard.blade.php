@extends('mahasiswa.template')

@php
    use App\Support\AptikomTheme;
    $themeColor = AptikomTheme::resolve(auth()->user(), '#006199');
@endphp

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Modern Dashboard & Visualisasi Consistent Styling */
        .modern-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease-in-out;
            overflow: visible !important;
        }

        .modern-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        .modern-card-header {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top-left-radius: 14px;
            border-top-right-radius: 14px;
        }

        /* Student Profile Hero Header (Consistent with Visualisasi Mahasiswa) */
        .student-profile-hero {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-left: 5px solid #1F3BB3 !important;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
            padding: 20px 24px;
            margin-bottom: 20px;
        }

        .student-label-tag {
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #1F3BB3;
            margin-bottom: 4px;
        }

        .student-name-text {
            font-size: 1.55rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
            margin-bottom: 10px;
            letter-spacing: -0.02em;
        }

        .student-meta-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .meta-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.88rem;
        }

        .meta-label {
            color: #64748b;
            font-weight: 500;
        }

        .meta-value {
            color: #0f172a;
            font-weight: 600;
        }

        .meta-pipe {
            color: #cbd5e1;
            font-weight: 300;
            user-select: none;
        }

        /* Section Titles */
        .section-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1F3BB3;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            letter-spacing: -0.01em;
            margin-bottom: 0;
        }

        .section-title i {
            font-size: 1.15rem;
            color: #1F3BB3;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Modern Buttons */
        .modern-btn-primary {
            height: 36px;
            padding: 0 16px;
            font-size: 0.84rem;
            font-weight: 600;
            border-radius: 8px;
            background: #1F3BB3;
            color: #ffffff;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            box-shadow: 0 2px 6px rgba(31, 59, 179, 0.2);
            transition: all 0.2s ease;
            text-decoration: none;
            white-space: nowrap;
        }

        .modern-btn-primary:hover {
            background: #172d88;
            box-shadow: 0 4px 10px rgba(31, 59, 179, 0.3);
            color: #ffffff;
        }

        .modern-btn-outline {
            height: 36px;
            padding: 0 14px;
            font-size: 0.84rem;
            font-weight: 500;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s ease;
            text-decoration: none;
            white-space: nowrap;
        }

        .modern-btn-outline:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #1e293b;
        }

        /* Stat KPI Cards */
        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease-in-out;
        }

        .stat-card:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
            border-color: #cbd5e1;
        }

        .stat-icon-pill {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .stat-value {
            font-size: 1.55rem;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        .progress {
            background-color: rgba(0, 0, 0, 0.06);
            border-radius: 99px;
        }

        /* Navigation Quick Cards */
        .nav-card {
            transition: all 0.2s ease-in-out;
            text-decoration: none;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }

        .nav-card:hover {
            box-shadow: 0 8px 24px rgba(31, 59, 179, 0.09) !important;
            transform: translateY(-3px);
            border-color: #bfdbfe;
        }

        .badge-predikat {
            font-size: 0.72rem;
            padding: 3px 8px;
            border-radius: 99px;
            font-weight: 600;
        }

        /* Legend Standard OBE Box */
        .obe-legend-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 16px;
            font-size: 0.82rem;
            color: #475569;
        }

        .legend-indicator-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }

        .btn-outline-purple {
            color: #6f42c1;
            border-color: rgba(111, 66, 193, 0.35);
            background-color: rgba(111, 66, 193, 0.05);
            transition: all 0.2s ease;
        }

        .btn-outline-purple:hover, .btn-outline-purple:focus {
            color: #ffffff;
            background-color: #6f42c1;
            border-color: #6f42c1;
            box-shadow: 0 2px 6px rgba(111, 66, 193, 0.25);
        }

        .btn-outline-soft-blue {
            color: #0284c7;
            border-color: rgba(2, 132, 199, 0.35);
            background-color: rgba(2, 132, 199, 0.08);
            transition: all 0.2s ease;
        }

        .btn-outline-soft-blue:hover, .btn-outline-soft-blue:focus {
            color: #ffffff;
            background-color: #0284c7;
            border-color: #0284c7;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
        }

        .btn-outline-theme {
            color: {{ $themeColor }};
            border-color: {{ $themeColor }}45;
            background-color: {{ $themeColor }}0d;
            transition: all 0.2s ease;
        }

        .btn-outline-theme:hover, .btn-outline-theme:focus {
            color: #ffffff !important;
            background-color: {{ $themeColor }} !important;
            border-color: {{ $themeColor }} !important;
            box-shadow: 0 2px 6px {{ $themeColor }}40;
        }

        .metric-footer-container {
            min-height: 52px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .metric-footer-text-row {
            min-height: 20px;
            display: flex;
            align-items: center;
        }

        .metric-footer-btn-row {
            min-height: 26px;
            display: flex;
            align-items: center;
            margin-top: 5px;
        }

        .metric-subtext-shared {
            font-size: 12px;
            font-weight: 500;
            color: #64748b;
            line-height: 1.4;
            display: inline-flex;
            align-items: center;
        }

        /* Modal Detail IPS Layout & True Viewport Centering */
        #modalDetailIps.modal {
            z-index: 1060;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        #modalDetailIps .modal-dialog {
            max-width: 960px;
            width: 92%;
            margin: 1.75rem auto !important;
            display: flex;
            align-items: center;
            min-height: calc(100% - 3.5rem);
        }

        #modalDetailIps .modal-content {
            width: 100%;
            max-height: calc(100vh - 3.5rem);
            border-radius: 16px;
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.18) !important;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        #modalDetailIps .modal-header {
            flex-shrink: 0;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 22px;
        }

        #modalDetailIps .modal-body {
            flex: 1 1 auto;
            overflow-y: auto;
            max-height: calc(100vh - 165px);
            padding: 18px 22px;
            background: #fbfcfe;
        }

        #modalDetailIps .modal-footer {
            flex-shrink: 0;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 12px 22px;
        }
    </style>
@endpush

@php
    if (!function_exists('wongColor')) {
        function wongColor($v)
        {
            return $v >= 75 ? '#0072B2' : ($v >= 51 ? '#E69F00' : '#D55E00');
        }
    }
    if (!function_exists('wongBg')) {
        function wongBg($v)
        {
            return $v >= 75 ? '#E0F0FA' : ($v >= 51 ? '#FFF3C4' : '#FDEBD8');
        }
    }
    if (!function_exists('wongText')) {
        function wongText($v)
        {
            return $v >= 75 ? '#004F80' : ($v >= 51 ? '#8A5F00' : '#8B3D00');
        }
    }

    $ipkNum = (float)($ipk ?? 0);
    [$predikatIpk, $predikatBg] = $mahasiswa?->prodi ? $mahasiswa->prodi->getPredikatKelulusan($ipkNum) : ['Belum Ada Data', 'bg-light text-muted'];
    $targetSks = $mahasiswa?->prodi ? $mahasiswa->prodi->getTargetSks() : 144;
@endphp

@section('content')
    <div class="container-fluid py-2 px-md-4">

        {{-- ── 1. Student Profile Hero Header ─────────────────────────────── --}}
        <div class="student-profile-hero">
            <div class="row align-items-center">
                <div class="col-12">
                    <div class="student-label-tag">
                        <i class="bi bi-mortarboard-fill me-1"></i> Dashboard Mahasiswa &bull; Hasil Evaluasi OBE
                    </div>
                    <div class="student-name-text">
                        {{ auth()->user()->name }}
                    </div>
                    <div class="student-meta-row">
                        <div class="meta-item">
                            <span class="meta-label">Program Studi:</span>
                            <span class="meta-value">{{ $mahasiswa->prodi->nama ?? '-' }}</span>
                        </div>
                        <span class="meta-pipe">|</span>
                        <div class="meta-item">
                            <span class="meta-label">NPM:</span>
                            <span class="meta-value">{{ $mahasiswa->NPM ?? '-' }}</span>
                        </div>
                        <span class="meta-pipe">|</span>
                        <div class="meta-item">
                            <span class="meta-label">Angkatan:</span>
                            <span class="meta-value">{{ $mahasiswa->angkatan ?? '-' }}</span>
                        </div>
                        <span class="meta-pipe">|</span>
                        <div class="meta-item">
                            <span class="meta-label">Jenjang:</span>
                            <span class="meta-value">{{ $mahasiswa->prodi->jenjang ?? 'S1' }}</span>
                        </div>
                        <span class="meta-pipe">|</span>
                        <div class="meta-item">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1" style="font-size: 11px;">
                                <i class="bi bi-check-circle-fill me-1"></i> Mahasiswa Aktif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 2. Alert Notice jika data kosong ───────────────────────────── --}}
        @if (!$hasData)
            <div class="alert alert-warning border-0 shadow-sm mb-4 py-3 px-4"
                style="background: #fff8e1; border-left: 5px solid #ffb300 !important; border-radius: 12px;">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-4 me-3 flex-shrink-0"></i>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Data Penilaian Belum Tersedia</h6>
                        <p class="mb-0 text-secondary small">
                            Belum ada riwayat asesmen OBE atau nilai mata kuliah yang tercatat pada sistem untuk akun mahasiswa Anda.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── 3. Statistik Utama (5 Metric Cards) ────────────────────────── --}}
        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 g-3 mb-4">

            {{-- IPK Kumulatif --}}
            <div class="col">
                <div class="stat-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold">IPK Kumulatif</span>
                            <div class="stat-icon-pill" style="background: {{ $themeColor }}15; color: {{ $themeColor }};">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>
                        </div>
                        <div class="stat-value mb-1" style="color: {{ $themeColor }};">{{ number_format($ipk, 2) }}</div>
                        <div class="progress mb-2" style="height:6px;">
                            <div class="progress-bar" style="background-color: {{ $themeColor }}; width:{{ min(round(($ipk / 4) * 100), 100) }}%"></div>
                        </div>
                    </div>
                    <div class="metric-footer-container">
                        <div class="metric-footer-text-row">
                            <span class="metric-subtext-shared text-truncate" title="{{ $predikatIpk }}">
                                {{ $predikatIpk }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- IPS Terakhir --}}
            <div class="col">
                <div class="stat-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold">IPS Terakhir</span>
                            <div class="stat-icon-pill" style="background: {{ $themeColor }}15; color: {{ $themeColor }};">
                                <i class="bi bi-speedometer2"></i>
                            </div>
                        </div>
                        <div class="stat-value mb-1" style="color: {{ $themeColor }};">{{ number_format($ips ?? 0, 2) }}</div>
                        <div class="progress mb-2" style="height:6px;">
                            <div class="progress-bar" style="background-color: {{ $themeColor }}; width:{{ min(round((($ips ?? 0) / 4) * 100), 100) }}%"></div>
                        </div>
                    </div>
                    <div class="metric-footer-container">
                        <div class="metric-footer-text-row">
                            <span class="metric-subtext-shared text-truncate" title="{{ $ipsSubtext }}">
                                <i class="bi bi-clock-history me-1 opacity-75"></i>{{ $ipsSubtext }}
                            </span>
                        </div>
                        <div class="metric-footer-btn-row">
                            <button type="button" class="btn btn-outline-theme btn-sm py-1 px-2 d-inline-flex align-items-center gap-1 shadow-none"
                                style="font-size: 10.5px; border-radius: 6px;"
                                data-bs-toggle="modal" data-bs-target="#modalDetailIps">
                                <i class="bi bi-calendar3-range"></i> <span>Lihat Detail IPS</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SKS Lulus --}}
            <div class="col">
                <div class="stat-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold">SKS Lulus</span>
                            <div class="stat-icon-pill" style="background: {{ $themeColor }}15; color: {{ $themeColor }};">
                                <i class="bi bi-journal-check"></i>
                            </div>
                        </div>
                        <div class="stat-value mb-1" style="color: {{ $themeColor }};">
                            {{ $sksLulus }}
                            <span class="fs-6 fw-normal text-muted">/ {{ $targetSks }}</span>
                        </div>
                        <div class="progress mb-2" style="height:6px;">
                            <div class="progress-bar" style="background-color: {{ $themeColor }}; width:{{ min(round(($sksLulus / $targetSks) * 100), 100) }}%"></div>
                        </div>
                    </div>
                    <div class="metric-footer-container">
                        <div class="metric-footer-text-row">
                            <span class="metric-subtext-shared text-truncate">
                                Sisa {{ max(0, $targetSks - $sksLulus) }} SKS Lulus
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rata-rata / Ketercapaian CPL --}}
            <div class="col">
                <div class="stat-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span id="cplCardTitle" class="text-muted small fw-semibold">Rata-rata CPL</span>
                            <div class="stat-icon-pill" style="background: {{ $themeColor }}15; color: {{ $themeColor }};">
                                <i class="bi bi-award-fill"></i>
                            </div>
                        </div>
                        <div id="cplCardVal" class="stat-value mb-1" style="color: {{ $themeColor }};">{{ number_format($avgSkorCpl, 2) }}</div>
                        <div class="progress mb-2" style="height:6px;">
                            <div id="cplCardBar" class="progress-bar" style="background-color: {{ $themeColor }}; width:{{ min($avgSkorCpl, 100) }}%"></div>
                        </div>
                    </div>
                    <div class="metric-footer-container">
                        <div class="metric-footer-text-row"></div>
                        <div class="metric-footer-btn-row">
                            <button type="button" id="btnToggleCplMode" class="btn btn-outline-theme btn-sm py-1 px-2 d-inline-flex align-items-center gap-1 shadow-none"
                                style="font-size: 10.5px; border-radius: 6px;">
                                <i class="bi bi-arrow-left-right"></i> <span id="btnToggleCplText">Lihat Ketercapaian</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rata-rata CPMK --}}
            <div class="col">
                <div class="stat-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold">Rata-rata CPMK</span>
                            <div class="stat-icon-pill" style="background: {{ $themeColor }}15; color: {{ $themeColor }};">
                                <i class="bi bi-patch-check-fill"></i>
                            </div>
                        </div>
                        <div class="stat-value mb-1" style="color: {{ $themeColor }};">{{ number_format($avgSkorCpmk, 2) }}</div>
                        <div class="progress mb-2" style="height:6px;">
                            <div class="progress-bar" style="background-color: {{ $themeColor }}; width:{{ min($avgSkorCpmk, 100) }}%"></div>
                        </div>
                    </div>
                    <div class="metric-footer-container">
                        <div class="metric-footer-text-row"></div>
                    </div>
                </div>
            </div>

        </div>


        {{-- ── 5. CPMK & Profesi Section ─────────────────────────────────── --}}
        <div class="row g-3 mb-4">

            {{-- Top Capaian CPMK --}}
            <div class="col-lg-6">
                <div class="modern-card h-100">
                    <div class="modern-card-header">
                        <div class="section-title">
                            <i class="bi bi-bar-chart-line-fill"></i>
                            <span>Capaian CPMK Tertinggi</span>
                        </div>
                        <a href="{{ route('mahasiswa.transkrip-kompetensi') }}" class="small text-decoration-none fw-semibold" style="color: #1F3BB3;">
                            Lihat Semua Transkrip &rarr;
                        </a>
                    </div>
                    <div class="p-4">
                        <div id="topCpmkContainer">
                            @forelse ($topCpmks as $cpmk)
                                @php $v = $cpmk['nilai']; @endphp
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <code class="fw-semibold px-2 py-1 rounded" style="font-size:.78rem; min-width:90px; background:#f1f5f9; color:#334155;" title="{{ $cpmk['deskripsi'] ?? '' }}">
                                        {{ $cpmk['kode'] }}
                                    </code>
                                    <div class="flex-grow-1">
                                        <div class="progress" style="height:7px;">
                                            <div class="progress-bar" style="width:{{ $v }}%; background:{{ wongColor($v) }};"></div>
                                        </div>
                                    </div>
                                    <span style="font-weight:700; font-size:13px; color:{{ wongColor($v) }}; min-width: 48px; text-align: right;">
                                        {{ number_format($v, 2) }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted small">
                                    <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                    Belum ada data capaian CPMK.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top Potensi Profesi --}}
            <div class="col-lg-6">
                <div class="modern-card h-100">
                    <div class="modern-card-header">
                        <div class="section-title">
                            <i class="bi bi-briefcase-fill" style="color: #E69F00;"></i>
                            <span>Potensi Karir & Profesi</span>
                        </div>
                        <a href="{{ route('mahasiswa.pemetaan-cpmk-profesi') }}" class="small text-decoration-none fw-semibold" style="color: #1F3BB3;">
                            Lihat Pemetaan Lengkap &rarr;
                        </a>
                    </div>
                    <div class="p-4">
                        <div id="topProfesiContainer">
                            @forelse ($topProfesi as $p)
                                @php $v = $p['match_percentage']; @endphp
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="small fw-semibold text-dark">{{ $p['nama'] }}</span>
                                            <span style="padding:2px 10px; border-radius:99px; font-size:12px; font-weight:700;
                                                background:{{ wongBg($v) }}; color:{{ wongText($v) }};">
                                                {{ $v }}%
                                            </span>
                                        </div>
                                        <div class="progress" style="height:7px;">
                                            <div class="progress-bar" style="width:{{ $v }}%; background:{{ wongColor($v) }};"></div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted small">
                                    <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                    Belum ada data pemetaan potensi profesi.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── 6. Navigasi Cepat Layanan Akademik ─────────────────────────── --}}
        <div class="row g-3">
            <div class="col-12">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-grid-fill text-muted" style="font-size: 0.9rem;"></i>
                    <small class="fw-bold text-uppercase text-muted" style="letter-spacing:.05em;">
                        Navigasi Layanan Akademik OBE
                    </small>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <a href="{{ route('mahasiswa.transkrip-kompetensi') }}" class="nav-card d-block p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2 flex-shrink-0"
                            style="width:46px;height:46px;display:flex;align-items:center;justify-content:center; background:#eff6ff; color:#1F3BB3;">
                            <i class="bi bi-journal-check" style="font-size:1.35rem;"></i>
                        </div>
                        <div>
                            <div class="fw-bold small text-dark">Transkrip Kompetensi</div>
                            <div class="text-muted" style="font-size:.78rem;">Rincian CPMK & CPL per mata kuliah</div>
                        </div>
                        <i class="bi bi-chevron-right text-muted ms-auto"></i>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-4">
                <a href="{{ route('mahasiswa.pemetaan-cpmk-profesi') }}" class="nav-card d-block p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2 flex-shrink-0"
                            style="width:46px;height:46px;display:flex;align-items:center;justify-content:center; background:#fff7ed; color:#ea580c;">
                            <i class="bi bi-briefcase" style="font-size:1.35rem;"></i>
                        </div>
                        <div>
                            <div class="fw-bold small text-dark">Pemetaan Profesi</div>
                            <div class="text-muted" style="font-size:.78rem;">Kecocokan CPMK ke profil karir</div>
                        </div>
                        <i class="bi bi-chevron-right text-muted ms-auto"></i>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-4">
                <a href="{{ route('mahasiswa.rekomendasi-mk') }}" class="nav-card d-block p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2 flex-shrink-0"
                            style="width:46px;height:46px;display:flex;align-items:center;justify-content:center; background:#f0fdf4; color:#16a34a;">
                            <i class="bi bi-book-half" style="font-size:1.35rem;"></i>
                        </div>
                        <div>
                            <div class="fw-bold small text-dark">Rekomendasi MK</div>
                            <div class="text-muted" style="font-size:.78rem;">Mata kuliah anjuran semester depan</div>
                        </div>
                        <i class="bi bi-chevron-right text-muted ms-auto"></i>
                    </div>
                </a>
            </div>
        </div>

    {{-- ── MODAL DETAIL IPS PER TAHUN AJARAN & SEMESTER ────────────────── --}}
    <div class="modal fade" id="modalDetailIps" tabindex="-1" aria-labelledby="modalDetailIpsLabel" aria-hidden="true" data-bs-backdrop="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-pill" style="background: rgba(111, 66, 193, 0.12); color: #6f42c1; width: 40px; height: 40px; font-size: 1.2rem;">
                            <i class="bi bi-calendar3-range"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="modalDetailIpsLabel" style="font-size: 1.05rem;">
                                Rincian Indeks Prestasi Semester (IPS)
                            </h5>
                            <p class="text-muted small mb-0" style="font-size: 0.78rem;">
                                Histori capaian per semester dan tahun ajaran untuk <strong>{{ $mahasiswa?->Nama ?? 'Mahasiswa' }}</strong> ({{ $mahasiswa?->NPM ?? '-' }})
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    {{-- 4 Stat Cards Ringkasan IPS & Akademik --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6 col-md-3">
                            <div class="p-3 bg-white rounded-3 border text-center h-100 shadow-xs">
                                <span class="text-muted d-block small mb-1" style="font-size: 0.75rem;">IPK Kumulatif</span>
                                <span class="fw-bold text-success fs-5">{{ number_format($ipk ?? 0, 2) }}</span>
                                <div class="small mt-1"><span class="badge {{ $predikatBg }}" style="font-size: 10px;">{{ $predikatIpk }}</span></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 bg-white rounded-3 border text-center h-100 shadow-xs">
                                <span class="text-muted d-block small mb-1" style="font-size: 0.75rem;">IPS Terakhir</span>
                                <span class="fw-bold fs-5" style="color: #6f42c1;">{{ number_format($ips ?? 0, 2) }}</span>
                                <div class="text-muted small mt-1 text-truncate" style="font-size: 10px;" title="{{ $ipsSubtext }}">{{ $ipsSubtext }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 bg-white rounded-3 border text-center h-100 shadow-xs">
                                <span class="text-muted d-block small mb-1" style="font-size: 0.75rem;">Total SKS Lulus</span>
                                <span class="fw-bold text-primary fs-5">{{ $sksLulus }} <span class="fs-6 fw-normal text-muted">/ {{ $targetSks }}</span></span>
                                <div class="text-muted small mt-1" style="font-size: 10px;">Sisa {{ max(0, $targetSks - $sksLulus) }} SKS</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 bg-white rounded-3 border text-center h-100 shadow-xs">
                                <span class="text-muted d-block small mb-1" style="font-size: 0.75rem;">Total Semester</span>
                                <span class="fw-bold text-dark fs-5">{{ count($academicBreakdown['semesters'] ?? []) }}</span>
                                <div class="text-muted small mt-1" style="font-size: 10px;">Semester Selesai</div>
                            </div>
                        </div>
                    </div>

                    {{-- Interactive Filter Buttons (Tahun Ajaran) jika tahun > 1 --}}
                    @php
                        $yearsData = $academicBreakdown['years'] ?? [];
                    @endphp

                    @if(count($yearsData) > 1)
                        <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                            <span class="text-muted small fw-semibold me-1"><i class="bi bi-funnel"></i> Filter Tahun:</span>
                            <button type="button" class="btn btn-sm btn-primary py-1 px-3 filter-ta-btn active shadow-none" data-ta="all" style="font-size: 11px; border-radius: 20px;">
                                Semua Tahun
                            </button>
                            @foreach($yearsData as $y)
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-3 filter-ta-btn shadow-none" data-ta="{{ \Illuminate\Support\Str::slug($y['tahun_akademik']) }}" style="font-size: 11px; border-radius: 20px;">
                                    TA {{ $y['tahun_akademik'] }}
                                </button>
                            @endforeach
                        </div>
                    @endif

                    {{-- Grouping Per Tahun Ajaran --}}
                    @if(!empty($yearsData))
                        <div class="d-flex flex-column gap-3">
                            @foreach($yearsData as $y)
                                <div class="card border rounded-3 overflow-hidden shadow-sm ta-group-card" data-ta="{{ \Illuminate\Support\Str::slug($y['tahun_akademik']) }}">
                                    {{-- Year Header Bar --}}
                                    <div class="px-3 py-2 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: #f1f5f9; border-bottom: 1px solid #e2e8f0;">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-calendar-check-fill text-primary"></i>
                                            <strong class="text-dark" style="font-size: 0.88rem;">Tahun Ajaran {{ $y['tahun_akademik'] }}</strong>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap" style="font-size: 0.78rem;">
                                            <span class="badge bg-white text-secondary border px-2 py-1">
                                                Total SKS: <strong>{{ $y['total_sks'] }}</strong> (Lulus: {{ $y['total_sks_lulus'] }} SKS)
                                            </span>
                                            <span class="badge px-2 py-1" style="background: rgba(111, 66, 193, 0.12); color: #6f42c1; border: 1px solid rgba(111, 66, 193, 0.25);">
                                                IP Tahun: <strong>{{ number_format($y['ip_tahun'], 2) }}</strong>
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Semesters inside this Academic Year --}}
                                    <div class="p-3 d-flex flex-column gap-3" style="background: #ffffff;">
                                        @foreach($y['semesters'] as $sem)
                                            <div class="border rounded-3 p-3" style="background: #f8fafc;">
                                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2 pb-2 border-bottom">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="badge text-white px-2 py-1" style="background: #6f42c1; font-size: 0.82rem;">
                                                            Semester {{ $sem['semester'] }} ({{ $sem['jenis_semester'] }})
                                                        </span>
                                                        <span class="text-muted small" style="font-size: 0.78rem;">
                                                            Periode: <strong>{{ $sem['periode'] }}</strong>
                                                        </span>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                                        <span class="badge bg-white border text-dark px-2 py-1" style="font-size: 0.78rem;">
                                                            Beban: <strong>{{ $sem['sks_semester'] }} SKS</strong>
                                                        </span>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 0.78rem;">
                                                            Lulus: <strong>{{ $sem['sks_lulus_semester'] }} SKS</strong>
                                                        </span>
                                                        <span class="badge px-2 py-1" style="background: rgba(111, 66, 193, 0.15); color: #6f42c1; font-size: 0.78rem; font-weight: 700;">
                                                            IPS: {{ number_format($sem['ips'], 2) }}
                                                        </span>
                                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1" style="font-size: 0.78rem;">
                                                            IPK: <strong>{{ number_format($sem['ipk_kumulatif'], 2) }}</strong>
                                                        </span>
                                                    </div>
                                                </div>

                                                {{-- Progress Bar Nilai IPS Semester --}}
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <small class="text-muted" style="font-size: 0.74rem; min-width: 60px;">Skala IPS:</small>
                                                    <div class="progress flex-grow-1" style="height: 5px;">
                                                        <div class="progress-bar" style="background: #6f42c1; width: {{ min(round(($sem['ips'] / 4) * 100), 100) }}%;"></div>
                                                    </div>
                                                    <small class="fw-semibold" style="color: #6f42c1; font-size: 0.74rem;">{{ number_format($sem['ips'], 2) }} / 4.00</small>
                                                </div>

                                                {{-- Tabel Mata Kuliah di Semester Ini --}}
                                                @if(!empty($sem['courses']))
                                                    <div class="table-responsive bg-white rounded border">
                                                        <table class="table table-sm table-hover mb-0" style="font-size: 0.8rem;">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th style="width: 35px;" class="text-center">#</th>
                                                                    <th style="width: 120px;" class="text-center">Kode MK</th>
                                                                    <th>Nama Mata Kuliah</th>
                                                                    <th style="width: 60px;" class="text-center">SKS</th>
                                                                    <th style="width: 70px;" class="text-center">Nilai</th>
                                                                    <th style="width: 60px;" class="text-center">Huruf</th>
                                                                    <th style="width: 60px;" class="text-center">Bobot</th>
                                                                    <th style="width: 85px;" class="text-center">Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($sem['courses'] as $idx => $c)
                                                                    <tr>
                                                                        <td class="text-center text-muted">{{ $idx + 1 }}</td>
                                                                        <td class="text-center font-monospace fw-medium text-secondary small">{{ $c['kode'] }}</td>
                                                                        <td class="fw-medium text-dark">{{ $c['nama'] }}</td>
                                                                        <td class="text-center fw-semibold">{{ $c['sks'] }}</td>
                                                                        <td class="text-center fw-bold">{{ number_format($c['nilai_akhir'], 2) }}</td>
                                                                        <td class="text-center">
                                                                            <span class="badge {{ $c['is_lulus'] ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} px-2 py-1 font-monospace" style="font-size: 11px;">
                                                                                {{ $c['nilai_huruf'] }}
                                                                            </span>
                                                                        </td>
                                                                        <td class="text-center text-muted">{{ number_format($c['bobot'], 2) }}</td>
                                                                        <td class="text-center">
                                                                            @if($c['is_lulus'])
                                                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 11px;">
                                                                                    Lulus
                                                                                </span>
                                                                            @else
                                                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" style="font-size: 11px;">
                                                                                    Tidak Lulus
                                                                                </span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <div class="text-muted small text-center py-2">Belum ada mata kuliah di semester ini.</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            Belum ada data histori semester atau mata kuliah yang tercatat.
                        </div>
                    @endif
                </div>

                <div class="modal-footer d-flex justify-content-between align-items-center">
                    <small class="text-muted" style="font-size: 0.78rem;">
                        <i class="bi bi-info-circle me-1"></i> Data dihitung berdasarkan riwayat penilaian hasil asesmen OBE.
                    </small>
                    <button type="button" class="btn btn-secondary btn-sm px-3 shadow-none" data-bs-dismiss="modal" style="border-radius: 8px;">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // State for CPL card mode: 'rata-rata' or 'ketercapaian'
            var cplDisplayMode = 'rata-rata';
            var currentAvgSkorCpl = {{ $avgSkorCpl ?? 0 }};
            var currentAvgKetercapaianCpl = {{ $avgKetercapaianCpl ?? 0 }};

            function updateCplCardUI() {
                if (cplDisplayMode === 'rata-rata') {
                    $('#cplCardTitle').text('Rata-rata CPL');
                    $('#cplCardVal').text(Number(currentAvgSkorCpl).toFixed(2));
                    $('#cplCardBar').css('width', Math.min(currentAvgSkorCpl, 100) + '%');
                    $('#btnToggleCplText').text('Lihat Ketercapaian');
                } else {
                    $('#cplCardTitle').text('Ketercapaian CPL');
                    $('#cplCardVal').text(Number(currentAvgKetercapaianCpl).toFixed(1) + '%');
                    $('#cplCardBar').css('width', Math.min(currentAvgKetercapaianCpl, 100) + '%');
                    $('#btnToggleCplText').text('Lihat Rata-rata');
                }
            }

            $('#btnToggleCplMode').on('click', function(e) {
                e.preventDefault();
                cplDisplayMode = (cplDisplayMode === 'rata-rata') ? 'ketercapaian' : 'rata-rata';
                updateCplCardUI();
            });

            // Pindahkan modal ke <body> agar terbebas dari overflow/transform .main-panel dan berada tepat di tengah layar
            if ($('#modalDetailIps').length && $('#modalDetailIps').parent().is(':not(body)')) {
                $('#modalDetailIps').appendTo('body');
            }

            // Filter Tahun Ajaran pada Modal Detail IPS
            $(document).on('click', '.filter-ta-btn', function(e) {
                e.preventDefault();
                $('.filter-ta-btn').removeClass('btn-primary active').addClass('btn-outline-secondary');
                $(this).removeClass('btn-outline-secondary').addClass('btn-primary active');

                var selectedTa = $(this).data('ta');
                if (selectedTa === 'all') {
                    $('.ta-group-card').fadeIn(200);
                } else {
                    $('.ta-group-card').hide();
                    $('.ta-group-card[data-ta="' + selectedTa + '"]').fadeIn(200);
                }
            });
        });
    </script>
@endpush
