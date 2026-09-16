@extends('mahasiswa.template')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Modern Consistency Styles */
        .modern-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease-in-out;
            margin-bottom: 20px;
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

        .page-header-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
            padding: 18px 24px;
            margin-bottom: 20px;
        }

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
        }

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

        .cursor-pointer {
            cursor: pointer;
        }

        .cursor-pointer:hover td {
            background-color: #f8fafc !important;
        }
    </style>
@endpush

@php
    if (!function_exists('pctColor')) {
        function pctColor($v)
        {
            if ($v <= 50) {
                return '#D55E00';
            }
            if ($v <= 74) {
                return '#E69F00';
            }
            return '#0072B2';
        }
    }
    if (!function_exists('pctBg')) {
        function pctBg($v)
        {
            if ($v <= 50) {
                return '#D55E001A';
            }
            if ($v <= 74) {
                return '#E69F001A';
            }
            return '#0072B21A';
        }
    }
@endphp

@section('content')
    <div class="container-fluid py-2 px-md-4">

        {{-- ── 1. Page Header Bar (No Info Card) ─────────────────────────── --}}
        <div class="page-header-box">
            <div class="row align-items-center">
                <div class="col-lg-8 mb-3 mb-lg-0">
                    <h4 class="fw-bold mb-1 text-dark">
                        <i class="bi bi-briefcase text-warning me-2"></i>Pemetaan CPMK ke Profesi
                    </h4>
                    <p class="text-muted small mb-0">
                        Analisis kecocokan potensi karir dan profesi lulusan berdasarkan capaian outcome pembelajaran (CPMK)
                    </p>
                </div>

                <div class="col-lg-4 text-lg-end">
                    <div class="d-flex align-items-center justify-content-lg-end gap-2 flex-wrap">
                        <button class="modern-btn-primary" onclick="printPetaan()">
                            <i class="bi bi-printer"></i> Cetak
                        </button>
                        <a href="{{ route('mahasiswa.pemetaan-cpmk-profesi.pdf') }}" target="_blank" class="modern-btn-outline">
                            <i class="bi bi-file-earmark-pdf text-danger"></i> Unduh PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>


        @if ($profesiDenganDetail->isEmpty())
            <div class="alert alert-warning border-0 shadow-sm mb-4 py-3 px-4"
                style="background: #fff8e1; border-left: 5px solid #ffb300 !important; border-radius: 12px;">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-4 me-3 flex-shrink-0"></i>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Belum Ada Data Pemetaan Profesi</h6>
                        <p class="mb-0 text-secondary small">
                            Belum ada riwayat CPMK mata kuliah yang terhubung dengan pemetaan profesi kurikulum program studi Anda.
                        </p>
                    </div>
                </div>
            </div>
        @else
            {{-- ── 3. Grafik Potensi Profesi ───────────────────────────────── --}}
            <div class="modern-card">
                <div class="modern-card-header">
                    <div class="section-title">
                        <i class="bi bi-bar-chart-horizontal-fill"></i>
                        <span>Potensi Profesi Sesuai Outcome Mahasiswa</span>
                    </div>
                </div>
                <div class="p-4">
                    <div style="position:relative;height:260px;">
                        <canvas id="profesiChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- ── 4. Daftar Profesi & Kecocokan ───────────────────────────── --}}
            <div class="modern-card">
                <div class="modern-card-header">
                    <div class="section-title">
                        <i class="bi bi-briefcase"></i>
                        <span>Daftar Profesi & Rincian Kecocokan CPMK</span>
                    </div>
                </div>
                <div class="p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.04em; color: #475569;">
                                <tr>
                                    <th width="5%" class="text-center">#</th>
                                    <th>Profesi</th>
                                    <th width="15%" class="text-center">CPMK Match</th>
                                    <th width="15%" class="text-center">Kecocokan</th>
                                    <th width="12%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($profesiDenganDetail as $i => $p)
                                    @php $c = pctColor($p['match_percentage']); @endphp

                                    <tr class="cursor-pointer" onclick="toggleDetail({{ $p['id'] }})">
                                        <td class="text-center fw-bold">{{ $i + 1 }}</td>
                                        <td>
                                            <strong class="text-dark">{{ $p['nama'] }}</strong>
                                            @if (!empty($p['courses_involved']))
                                                <br><small class="text-success">
                                                    <i class="bi bi-check-circle"></i> Terkait MK:
                                                    {{ implode(', ', array_slice($p['courses_involved'], 0, 2)) }}{{ count($p['courses_involved']) > 2 ? '…' : '' }}
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge text-white" style="background:{{ $c }}; font-size: 11px; padding: 4px 8px;">
                                                {{ $p['total_cpmk_matched'] }}/{{ $p['total_cpmk_required'] }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold fs-6" style="color:{{ $c }};">{{ $p['match_percentage'] }}%</span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-primary" id="btn-{{ $p['id'] }}"
                                                onclick="event.stopPropagation();toggleDetail({{ $p['id'] }},this)">
                                                <span class="btn-text">Detail</span>
                                            </button>
                                        </td>
                                    </tr>

                                    {{-- ── Detail Row ── --}}
                                    <tr id="detail-{{ $p['id'] }}" style="display:none;">
                                        <td colspan="5" class="p-0 bg-light">
                                            <div class="p-4 border-top border-bottom" style="background: #f8fafc;">

                                                {{-- Header Detail --}}
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div>
                                                        <strong class="text-dark fs-6">{{ $p['nama'] }}</strong>
                                                        <span class="ms-2 badge text-white" style="background:{{ $c }};">
                                                            {{ $p['match_percentage'] }}% — {{ $p['status'] }}
                                                        </span>
                                                    </div>
                                                    <button class="btn btn-sm btn-close"
                                                        onclick="event.stopPropagation();closeDetail({{ $p['id'] }})">
                                                    </button>
                                                </div>

                                                {{-- Tabel CPMK --}}
                                                <div class="table-responsive mb-3 bg-white rounded border">
                                                    <table class="table table-sm table-bordered mb-0">
                                                        <thead class="table-light" style="font-size: 0.8rem;">
                                                            <tr>
                                                                <th width="15%">Kode CPMK</th>
                                                                <th>Deskripsi CPMK</th>
                                                                <th width="30%">Nilai Capaian</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($p['cpmks'] as $cpmk)
                                                                @php $cc = pctColor($cpmk['nilai']); @endphp
                                                                <tr>
                                                                    <td><code class="fw-bold" style="color:{{ $cc }};">{{ $cpmk['kode'] }}</code></td>
                                                                    <td class="small">{{ $cpmk['deskripsi'] }}</td>
                                                                    <td>
                                                                        @if ($cpmk['nilai'] > 0)
                                                                            <div class="d-flex align-items-center gap-2">
                                                                                <div class="flex-grow-1" style="height:6px;border-radius:99px;background:#e9ecef;">
                                                                                    <div style="width:{{ min($cpmk['nilai'], 100) }}%;height:100%;border-radius:99px;background:{{ $cc }};"></div>
                                                                                </div>
                                                                                <span class="fw-bold small" style="color:{{ $cc }};min-width:36px;text-align:right;">
                                                                                    {{ $cpmk['nilai'] }}%
                                                                                </span>
                                                                            </div>
                                                                        @else
                                                                            <span class="badge bg-secondary-subtle text-secondary">
                                                                                <i class="bi bi-dash-circle"></i> Belum Dinilai
                                                                            </span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                                {{-- Total & progress --}}
                                                <div class="p-3 bg-white rounded border">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <small class="text-muted">
                                                            Rata-rata dari {{ $p['total_cpmk_matched'] }} CPMK dinilai / {{ $p['total_cpmk_required'] }} total CPMK
                                                        </small>
                                                        <span class="fw-bold fs-5" style="color:{{ $c }};">
                                                            {{ number_format($p['match_percentage'], 1) }}%
                                                        </span>
                                                    </div>
                                                    <div class="progress" style="height:8px;border-radius:99px;">
                                                        <div class="progress-bar" role="progressbar"
                                                            style="width:{{ $p['match_percentage'] }}%;background:{{ $c }};border-radius:99px;">
                                                        </div>
                                                    </div>
                                                </div>

                                                @if ($p['total_cpmk_matched'] < $p['total_cpmk_required'])
                                                    <div class="alert alert-warning mt-2 mb-0 py-2 small border-0" style="background:#fff8e1; border-left: 4px solid #ffb300 !important;">
                                                        <i class="bi bi-lightbulb-fill text-warning me-1"></i>
                                                        <strong>Saran:</strong> Ambil mata kuliah yang memuat CPMK:
                                                        <strong>{{ implode(', ', collect($p['cpmks'])->where('nilai', 0)->pluck('kode')->take(3)->toArray()) }}</strong>
                                                    </div>
                                                @endif

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ── 5. Ringkasan Profil & Rekomendasi Utama ───────────────────── --}}
            <div class="modern-card">
                <div class="modern-card-header">
                    <div class="section-title">
                        <i class="bi bi-pie-chart"></i>
                        <span>Ringkasan Profil Kompetensi</span>
                    </div>
                </div>
                <div class="p-4">
                    @php $top = $profesiDenganDetail->first(); @endphp
                    @if ($top)
                        <div class="alert mb-4 py-3 px-4 border-0 rounded-3 shadow-none"
                            style="background:{{ pctBg($top['match_percentage']) }}; border-left: 4px solid {{ pctColor($top['match_percentage']) }} !important;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fs-4">🎯</span>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">Rekomendasi Profesi Utama: {{ $top['nama'] }}</h6>
                                    <small style="color:{{ pctColor($top['match_percentage']) }}; font-weight:600;">
                                        Tingkat Kecocokan: {{ $top['match_percentage'] }}% — Sesuai dengan capaian CPMK tertinggi Anda
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row g-4">
                        {{-- Semua CPMK Dinilai --}}
                        <div class="col-lg-6">
                            <div class="p-3 bg-light rounded border h-100">
                                <div class="fw-bold mb-3 small text-uppercase text-muted" style="letter-spacing:.05em;">
                                    <i class="bi bi-list-check me-1"></i> Seluruh CPMK Dinilai ({{ $allCpmks->count() }} CPMK)
                                </div>
                                <div style="max-height: 280px; overflow-y: auto;">
                                    @foreach ($allCpmks->sortByDesc('nilai') as $cpmk)
                                        @php
                                            $cc = pctColor($cpmk['nilai']);
                                        @endphp
                                        <div class="d-flex align-items-center gap-2 mb-2 p-2 bg-white rounded border">
                                            <div style="min-width:80px;">
                                                <code class="fw-bold small" style="color:{{ $cc }};">{{ $cpmk['kode'] }}</code>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="small text-muted text-truncate mb-1" style="max-width:280px;" title="{{ $cpmk['deskripsi'] }}">
                                                    {{ $cpmk['deskripsi'] }}
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="flex-grow-1" style="height:6px;border-radius:99px;background:#e9ecef;">
                                                        <div style="width:{{ min($cpmk['nilai'], 100) }}%;height:100%;border-radius:99px;background:{{ $cc }};"></div>
                                                    </div>
                                                    <span class="fw-bold small" style="color:{{ $cc }};min-width:36px;text-align:right;">{{ $cpmk['nilai'] }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Rata-rata & Daftar Profesi Cocok --}}
                        <div class="col-lg-6">
                            <div class="p-3 bg-light rounded border h-100 d-flex flex-column justify-content-between">
                                @php
                                    $avgAll = round($allCpmks->avg('nilai') ?? 0, 1);
                                    $avgColor = pctColor($avgAll);
                                @endphp
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                        <span class="fw-bold small text-uppercase text-muted" style="letter-spacing:.05em;">
                                            <i class="bi bi-calculator me-1"></i> Rata-rata Skor CPMK
                                        </span>
                                        <span class="fw-bold fs-5" style="color:{{ $avgColor }};">{{ $avgAll }}%</span>
                                    </div>
                                    <div class="fw-bold mb-2 small text-uppercase text-muted" style="letter-spacing:.05em;">
                                        <i class="bi bi-briefcase me-1"></i> Profesi Terpetakan ({{ $profesiDenganDetail->count() }})
                                    </div>
                                    <div style="max-height: 220px; overflow-y: auto;">
                                        @foreach ($profesiDenganDetail as $p)
                                            @php
                                                $pc = pctColor($p['match_percentage']);
                                                $pbg = pctBg($p['match_percentage']);
                                            @endphp
                                            <div class="d-flex align-items-center gap-2 mb-2 p-2 bg-white rounded border">
                                                <div class="flex-grow-1">
                                                    <div class="small fw-bold text-dark">{{ $p['nama'] }}</div>
                                                    <div class="d-flex align-items-center gap-2 mt-1">
                                                        <div class="flex-grow-1" style="height:5px;border-radius:99px;background:#e9ecef;">
                                                            <div style="width:{{ min($p['match_percentage'], 100) }}%;height:100%;border-radius:99px;background:{{ $pc }};"></div>
                                                        </div>
                                                        <span class="small fw-bold" style="color:{{ $pc }};min-width:35px;text-align:right;">{{ $p['match_percentage'] }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        @endif

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function printPetaan() {
            const url = "{{ route('mahasiswa.pemetaan-cpmk-profesi.pdf') }}";
            const w = window.open(url, '_blank');
            setTimeout(() => {
                if (w) {
                    w.focus();
                    w.print();
                }
            }, 800);
        }

        function toggleDetail(id, btn) {
            const row = document.getElementById('detail-' + id);
            const b = btn || document.getElementById('btn-' + id);
            const isOpen = row && row.style.display !== 'none';

            document.querySelectorAll('[id^="detail-"]').forEach(el => {
                el.style.display = 'none';
                const ob = document.getElementById('btn-' + el.id.replace('detail-', ''));
                if (ob) {
                    ob.classList.remove('btn-primary', 'text-white');
                    ob.classList.add('btn-outline-primary');
                    ob.querySelector('.btn-text').textContent = 'Detail';
                }
            });

            if (!isOpen && row) {
                row.style.display = 'table-row';
                if (b) {
                    b.classList.remove('btn-outline-primary');
                    b.classList.add('btn-primary', 'text-white');
                    b.querySelector('.btn-text').textContent = 'Tutup';
                }
                row.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }
        }

        function closeDetail(id) {
            const row = document.getElementById('detail-' + id);
            const btn = document.getElementById('btn-' + id);
            if (row) row.style.display = 'none';
            if (btn) {
                btn.classList.remove('btn-primary', 'text-white');
                btn.classList.add('btn-outline-primary');
                btn.querySelector('.btn-text').textContent = 'Detail';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const chartData = @json($chartProfesi ?? []);
            const ids = @json($profesiDenganDetail->pluck('id')->take(5)->values() ?? []);

            if (chartData && chartData.length > 0) {
                const labels = chartData.map(d => d.label);
                const values = chartData.map(d => d.value);
                const colors = values.map(v => v <= 50 ? '#D55E00' : v <= 74 ? '#E69F00' : '#0072B2');

                const chartEl = document.getElementById('profesiChart');
                if (chartEl) {
                    new Chart(chartEl.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [{
                                data: values,
                                backgroundColor: colors.map(c => c + 'CC'),
                                borderColor: colors,
                                borderWidth: 2,
                                borderRadius: 6,
                                borderSkipped: false
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: ctx => ` ${chartData[ctx.dataIndex].full_name}: ${ctx.parsed.x}%`
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    max: 100,
                                    grid: {
                                        color: 'rgba(0,0,0,.05)'
                                    },
                                    ticks: {
                                        callback: v => v + '%'
                                    }
                                },
                                y: {
                                    grid: {
                                        display: false
                                    }
                                }
                            },
                            onClick: (e, els) => {
                                if (els.length && ids[els[0].index]) toggleDetail(ids[els[0].index]);
                            }
                        }
                    });
                }
            }
        });
    </script>
@endpush
