@extends('mahasiswa.template')

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
    $predikatIpk = 'Memuaskan';
    $predikatBg = 'bg-info-subtle text-info';
    if ($ipkNum >= 3.51) {
        $predikatIpk = 'Dengan Pujian (Cumlaude)';
        $predikatBg = 'bg-success-subtle text-success';
    } elseif ($ipkNum >= 3.00) {
        $predikatIpk = 'Sangat Memuaskan';
        $predikatBg = 'bg-primary-subtle text-primary';
    } elseif ($ipkNum >= 2.76) {
        $predikatIpk = 'Memuaskan';
        $predikatBg = 'bg-warning-subtle text-warning';
    } elseif ($ipkNum > 0) {
        $predikatIpk = 'Cukup';
        $predikatBg = 'bg-secondary-subtle text-secondary';
    } else {
        $predikatIpk = 'Belum Ada Data';
        $predikatBg = 'bg-light text-muted';
    }
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
                            <div class="stat-icon-pill bg-success-subtle text-success">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>
                        </div>
                        <div class="stat-value mb-1 text-success">{{ number_format($ipk, 2) }}</div>
                        <div class="progress mb-2" style="height:6px;">
                            <div class="progress-bar bg-success" style="width:{{ min(round(($ipk / 4) * 100), 100) }}%"></div>
                        </div>
                    </div>
                    <div>
                        <span class="badge {{ $predikatBg }} badge-predikat text-truncate d-inline-block" style="max-width: 100%;">
                            {{ $predikatIpk }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- IPS Terakhir --}}
            <div class="col">
                <div class="stat-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold">IPS Terakhir</span>
                            <div class="stat-icon-pill" style="background: rgba(111, 66, 193, 0.12); color: #6f42c1;">
                                <i class="bi bi-speedometer2"></i>
                            </div>
                        </div>
                        <div class="stat-value mb-1" style="color: #6f42c1;">{{ number_format($ips ?? 0, 2) }}</div>
                        <div class="progress mb-2" style="height:6px;">
                            <div class="progress-bar" style="background: #6f42c1; width:{{ min(round((($ips ?? 0) / 4) * 100), 100) }}%"></div>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted text-truncate d-block" title="{{ $ipsSubtext }}">
                            <i class="bi bi-clock-history me-1"></i>{{ $ipsSubtext }}
                        </small>
                    </div>
                </div>
            </div>

            {{-- SKS Lulus --}}
            <div class="col">
                <div class="stat-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold">SKS Lulus</span>
                            <div class="stat-icon-pill bg-primary-subtle text-primary">
                                <i class="bi bi-journal-check"></i>
                            </div>
                        </div>
                        <div class="stat-value mb-1 text-primary">
                            {{ $sksLulus }}
                            <span class="fs-6 fw-normal text-muted">/ 144</span>
                        </div>
                        <div class="progress mb-2" style="height:6px;">
                            <div class="progress-bar bg-primary" style="width:{{ min(round(($sksLulus / 144) * 100), 100) }}%"></div>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted">
                            Sisa <strong>{{ max(0, 144 - $sksLulus) }}</strong> SKS Lulus
                        </small>
                    </div>
                </div>
            </div>

            {{-- Rata-rata / Ketercapaian CPL --}}
            <div class="col">
                <div class="stat-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span id="cplCardTitle" class="text-muted small fw-semibold">Rata-rata CPL</span>
                            <div class="stat-icon-pill bg-info-subtle text-info">
                                <i class="bi bi-award-fill"></i>
                            </div>
                        </div>
                        <div id="cplCardVal" class="stat-value mb-1 text-info">{{ $avgSkorCpl }}</div>
                        <div class="progress mb-2" style="height:6px;">
                            <div id="cplCardBar" class="progress-bar bg-info" style="width:{{ min($avgSkorCpl, 100) }}%"></div>
                        </div>
                        <small id="cplCardSubtext" class="text-muted text-truncate d-block mb-2">Standar Kelulusan &ge; 65.0</small>
                    </div>
                    <button type="button" id="btnToggleCplMode" class="btn btn-outline-info btn-sm w-100 py-1 px-2 d-flex align-items-center justify-content-center gap-1 shadow-none"
                        style="font-size: 11px; border-radius: 6px;">
                        <i class="bi bi-arrow-left-right"></i> <span id="btnToggleCplText">Lihat Ketercapaian</span>
                    </button>
                </div>
            </div>

            {{-- Rata-rata CPMK --}}
            <div class="col">
                <div class="stat-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold">Rata-rata CPMK</span>
                            <div class="stat-icon-pill bg-warning-subtle text-warning">
                                <i class="bi bi-patch-check-fill"></i>
                            </div>
                        </div>
                        <div class="stat-value mb-1 text-warning">{{ $avgSkorCpmk }}</div>
                        <div class="progress mb-2" style="height:6px;">
                            <div class="progress-bar bg-warning" style="width:{{ min($avgSkorCpmk, 100) }}%"></div>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted text-truncate d-block">Skor Asesmen Terpenuhi</small>
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
                                    <span style="font-weight:700; font-size:13px; color:{{ wongColor($v) }}; min-width: 45px; text-align: right;">
                                        {{ $v }}
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
                    $('#cplCardVal').text(currentAvgSkorCpl);
                    $('#cplCardBar').css('width', Math.min(currentAvgSkorCpl, 100) + '%');
                    $('#cplCardSubtext').text('Standar Kelulusan \u2265 65.0');
                    $('#btnToggleCplText').text('Lihat Ketercapaian');
                } else {
                    $('#cplCardTitle').text('Ketercapaian CPL');
                    $('#cplCardVal').text(currentAvgKetercapaianCpl + '%');
                    $('#cplCardBar').css('width', Math.min(currentAvgKetercapaianCpl, 100) + '%');
                    $('#cplCardSubtext').text('MK CPL Terpenuhi');
                    $('#btnToggleCplText').text('Lihat Rata-rata');
                }
            }

            $('#btnToggleCplMode').on('click', function(e) {
                e.preventDefault();
                cplDisplayMode = (cplDisplayMode === 'rata-rata') ? 'ketercapaian' : 'rata-rata';
                updateCplCardUI();
            });
        });
    </script>
@endpush
