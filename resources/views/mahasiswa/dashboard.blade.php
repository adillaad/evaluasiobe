@extends('mahasiswa.template')

@push('styles')
    {{-- Bootstrap Icons (pastikan sudah ada di template, ini fallback) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .stat-card {
            transition: box-shadow .15s;
        }

        .stat-card:hover {
            box-shadow: 0 0 0 2px rgba(13, 110, 253, .2) !important;
        }

        .nav-card {
            transition: box-shadow .15s, transform .15s;
            text-decoration: none;
        }

        .nav-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, .08) !important;
            transform: translateY(-2px);
        }

        .progress {
            background-color: rgba(0, 0, 0, .06);
        }

        .obe-banner {
            background: linear-gradient(135deg, #e8f4ff 0%, #f0e8ff 100%);
            border-left: 4px solid #0d6efd;
        }
    </style>
@endpush
@php
    function wongColor($v)
    {
        return $v >= 75 ? '#0072B2' : ($v >= 51 ? '#E69F00' : '#D55E00');
    }
    function wongBg($v)
    {
        return $v >= 75 ? '#E0F0FA' : ($v >= 51 ? '#FFF3C4' : '#FDEBD8');
    }
    function wongText($v)
    {
        return $v >= 75 ? '#004F80' : ($v >= 51 ? '#8A5F00' : '#8B3D00');
    }
@endphp

@section('content')
    <div class="container-fluid py-4">

        {{-- ── Header ───────────────────────────────────────────────────── --}}
        <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-2">
            <div>
                <h5 class="fw-semibold mb-1">Selamat datang, {{ auth()->user()->name }}!</h5>
                <small class="text-muted">
                    {{-- nama_prodi dari join di controller --}}
                    {{ $mahasiswa->prodi->nama?? '-' }}
                    &middot;
                    Angkatan {{ $mahasiswa->angkatan ?? '-' }}
                    &middot;
                    NPM: {{ $mahasiswa->NPM ?? '-' }}
                </small>
            </div>
            <span class="badge bg-success-subtle text-success px-3 py-2">
                <i class="bi bi-circle-fill me-1" style="font-size:.5rem;vertical-align:middle;"></i>
                Aktif
            </span>
        </div>

        {{-- ── Banner OBE ───────────────────────────────────────────────── --}}
        <div class="obe-banner rounded-3 p-3 mb-4 d-flex align-items-start gap-3">
            <div class="flex-shrink-0 mt-1">
                <i class="bi bi-info-circle-fill text-primary fs-5"></i>
            </div>
            <div>
                <p class="fw-semibold mb-1 small text-primary">Sistem Berbasis Outcome-Based Education (OBE)</p>
                <p class="mb-0 small text-muted" style="line-height:1.6;">
                    Persentase yang ditampilkan di dashboard ini mencerminkan <strong>capaian outcome</strong> Anda secara
                    personal —
                    bukan nilai ujian konvensional. Setiap mahasiswa memiliki persentase berbeda sesuai
                    <strong>Capaian Pembelajaran Mata Kuliah (CPMK)</strong> dan
                    <strong>Capaian Pembelajaran Lulusan (CPL)</strong> yang telah dicapai.
                    Semakin tinggi persentase, semakin kuat kompetensi Anda untuk profesi terkait.
                </p>
            </div>
        </div>

        {{-- ── Statistik Utama ─────────────────────────────────────────── --}}
        <div class="row g-3 mb-4">

            {{-- SKS Lulus --}}
            <div class="col-6 col-md-3">
                <div class="card border shadow-none h-100 stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="rounded-2 p-1 bg-primary-subtle text-primary" style="line-height:1;">
                                <i class="bi bi-mortarboard-fill" style="font-size:1rem;"></i>
                            </div>
                            <p class="text-muted small mb-0">SKS Lulus</p>
                        </div>
                        <h4 class="fw-semibold mb-1">
                            {{ $sksLulus }}
                            <span class="fs-6 fw-normal text-muted">/ 144</span>
                        </h4>
                        <div class="progress mb-1" style="height:5px;border-radius:99px;">
                            <div class="progress-bar bg-primary"
                                style="width:{{ min(round(($sksLulus / 144) * 100), 100) }}%"></div>
                        </div>
                        <small class="text-muted">Sisa {{ max(0, 144 - $sksLulus) }} SKS</small>
                    </div>
                </div>
            </div>

            {{-- IPK --}}
            <div class="col-6 col-md-3">
                <div class="card border shadow-none h-100 stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="rounded-2 p-1 bg-success-subtle text-success" style="line-height:1;">
                                <i class="bi bi-graph-up-arrow" style="font-size:1rem;"></i>
                            </div>
                            <p class="text-muted small mb-0">IPK</p>
                        </div>
                        <h4 class="fw-semibold mb-1 text-success">{{ number_format($ipk, 2) }}</h4>
                        <div class="progress mb-1" style="height:5px;border-radius:99px;">
                            <div class="progress-bar bg-success" style="width:{{ min(round(($ipk / 4) * 100), 100) }}%">
                            </div>
                        </div>
                        <small class="text-muted">dari 4.00</small>
                    </div>
                </div>
            </div>

            {{-- Rata-rata CPMK --}}
            <div class="col-6 col-md-3">
                <div class="card border shadow-none h-100 stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="rounded-2 p-1 bg-warning-subtle text-warning" style="line-height:1;">
                                <i class="bi bi-patch-check-fill" style="font-size:1rem;"></i>
                            </div>
                            <p class="text-muted small mb-0">Rata-rata CPMK</p>
                        </div>
                        <h4 class="fw-semibold mb-1 text-warning">{{ $avgCpmk }}%</h4>
                        <div class="progress mb-1" style="height:5px;border-radius:99px;">
                            <div class="progress-bar bg-warning" style="width:{{ $avgCpmk }}%"></div>
                        </div>
                        <small class="text-muted">Capaian mata kuliah</small>
                    </div>
                </div>
            </div>

            {{-- Rata-rata CPL --}}
            <div class="col-6 col-md-3">
                <div class="card border shadow-none h-100 stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="rounded-2 p-1 bg-info-subtle text-info" style="line-height:1;">
                                <i class="bi bi-award-fill" style="font-size:1rem;"></i>
                            </div>
                            <p class="text-muted small mb-0">Rata-rata CPL</p>
                        </div>
                        <h4 class="fw-semibold mb-1 text-info">{{ $avgCpl }}%</h4>
                        <div class="progress mb-1" style="height:5px;border-radius:99px;">
                            <div class="progress-bar bg-info" style="width:{{ $avgCpl }}%"></div>
                        </div>
                        <small class="text-muted">Capaian lulusan</small>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── CPMK & Profesi ───────────────────────────────────────────── --}}
        <div class="row g-3 mb-4">

            {{-- Top CPMK --}}
            <div class="col-md-6">
                <div class="card border shadow-none h-100">
                    <div class="card-header bg-white border-bottom py-2 px-3 d-flex align-items-center gap-2">
                        <i class="bi bi-bar-chart-line-fill text-primary" style="font-size:.9rem;"></i>
                        <small class="fw-semibold text-uppercase text-muted" style="letter-spacing:.05em;">
                            Capaian CPMK Terkini
                        </small>
                    </div>
                    <div class="card-body py-3">
                        @forelse ($topCpmks as $cpmk)
                            @php
                                $v = $cpmk['nilai'];
                                $col = $v >= 75 ? 'primary' : ($v >= 60 ? 'warning' : 'danger');
                            @endphp
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <code class="text-muted" style="font-size:.72rem;min-width:90px;">
                                    {{ $cpmk['kode'] }}
                                </code>
                                <div class="flex-grow-1">
                                    <div class="progress" style="height:6px;border-radius:99px;">
                                        <div class="progress-bar"
                                            style="width:{{ $v }}%; background:{{ wongColor($v) }};"></div>
                                    </div>
                                </div>
                                <span
                                    style="font-weight:500; font-size:13px; color:{{ wongColor($v) }};">{{ $v }}%</span>
                            </div>
                        @empty
                            <div class="text-center py-3">
                                <i class="bi bi-inbox text-muted fs-4 d-block mb-1"></i>
                                <p class="text-muted small mb-0">Belum ada data CPMK.</p>
                            </div>
                        @endforelse

                        <div class="text-end mt-3">
                            <a href="{{ route('mahasiswa.transkrip-kompetensi') }}"
                                class="small text-decoration-none text-primary">
                                Lihat semua &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top Profesi --}}
            <div class="col-md-6">
                <div class="card border shadow-none h-100">
                    <div class="card-header bg-white border-bottom py-2 px-3 d-flex align-items-center gap-2">
                        <i class="bi bi-briefcase-fill text-warning" style="font-size:.9rem;"></i>
                        <small class="fw-semibold text-uppercase text-muted" style="letter-spacing:.05em;">
                            Potensi Profesi
                        </small>
                    </div>
                    <div class="card-body py-3">
                        @forelse ($topProfesi as $p)
                            @php
                                $v = $p['match_percentage'];
                                $col = $v >= 75 ? 'primary' : ($v >= 60 ? 'warning' : 'danger');
                            @endphp
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-semibold">{{ $p['nama'] }}</span>
                                        <span
                                            style="padding:2px 10px; border-radius:99px; font-size:12px; font-weight:500;
                                            background:{{ wongBg($v) }}; color:{{ wongText($v) }};">{{ $v }}%</span>
                                    </div>
                                    <div class="progress" style="height:6px;border-radius:99px;">
                                        <div class="progress-bar"
                                            style="width:{{ $v }}%; background:{{ wongColor($v) }};"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-3">
                                <i class="bi bi-inbox text-muted fs-4 d-block mb-1"></i>
                                <p class="text-muted small mb-0">Belum ada data profesi.</p>
                            </div>
                        @endforelse

                        <div class="text-end mt-2">
                            <a href="{{ route('mahasiswa.pemetaan-cpmk-profesi') }}"
                                class="small text-decoration-none text-primary">
                                Lihat semua &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Navigasi Cepat ───────────────────────────────────────────── --}}
        <div class="row g-3">
            <div class="col-12">
                <small class="fw-semibold text-uppercase text-muted d-block mb-2" style="letter-spacing:.05em;">
                    Navigasi Cepat
                </small>
            </div>

            <div class="col-12 col-md-4">
                <a href="{{ route('mahasiswa.transkrip-kompetensi') }}" class="card border shadow-none nav-card d-block">
                    <div class="card-body d-flex align-items-center gap-3 py-3">
                        <div class="rounded-3 p-2 bg-primary-subtle text-primary flex-shrink-0"
                            style="width:44px;height:44px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-journal-check" style="font-size:1.2rem;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small text-dark">Transkrip Kompetensi</div>
                            <div class="text-muted" style="font-size:.75rem;">CPMK & CPL per mata kuliah</div>
                        </div>
                        <i class="bi bi-chevron-right text-muted ms-auto small"></i>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-4">
                <a href="{{ route('mahasiswa.rekomendasi-mk') }}" class="card border shadow-none nav-card d-block">
                    <div class="card-body d-flex align-items-center gap-3 py-3">
                        <div class="rounded-3 p-2 bg-success-subtle text-success flex-shrink-0"
                            style="width:44px;height:44px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-book-half" style="font-size:1.2rem;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small text-dark">Rekomendasi MK</div>
                            <div class="text-muted" style="font-size:.75rem;">Mata kuliah yang perlu diambil</div>
                        </div>
                        <i class="bi bi-chevron-right text-muted ms-auto small"></i>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-4">
                <a href="{{ route('mahasiswa.pemetaan-cpmk-profesi') }}"
                    class="card border shadow-none nav-card d-block">
                    <div class="card-body d-flex align-items-center gap-3 py-3">
                        <div class="rounded-3 p-2 bg-warning-subtle text-warning flex-shrink-0"
                            style="width:44px;height:44px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-briefcase" style="font-size:1.2rem;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small text-dark">Pemetaan Profesi</div>
                            <div class="text-muted" style="font-size:.75rem;">Kecocokan CPMK ke karir</div>
                        </div>
                        <i class="bi bi-chevron-right text-muted ms-auto small"></i>
                    </div>
                </a>
            </div>

        </div>

    </div>
@endsection
