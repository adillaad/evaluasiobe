@extends('mahasiswa.template')

@section('content')
    <div class="container-fluid py-3">

        <div class="mb-3">
            <h4 class="fw-bold mb-0">Pemetaan CPMK ke Profesi</h4>
            <small class="text-muted">Kecocokan dihitung berdasarkan rata-rata nilai CPMK yang relevan per profesi</small>
        </div>
    </div>
    <div class="d-flex gap-2 mb-3">

        <!-- UNDuh -->
        <a href="{{ route('mahasiswa.pemetaan-cpmk-profesi.pdf') }}" target="_blank" class="btn btn-danger btn-sm shadow-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i> Unduh PDF
        </a>

        <!-- CETAK -->
        <button class="btn btn-primary btn-sm shadow-sm" onclick="printPetaan()">
            <i class="bi bi-printer me-1"></i> Cetak
        </button>

    </div>
    </div>

    @php
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
    @endphp

    @if ($profesiDenganDetail->isEmpty())
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i> Belum ada profesi yang cocok dengan kriteria Anda.
        </div>
    @else
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <span class="fw-semibold">
                    <i class="bi bi-bar-chart-horizontal-fill me-1"></i> Potensi Profesi Sesuai OutCome Mahasiswa
                </span>
            </div>
            <div class="card-body">
                <div style="position:relative;height:260px;">
                    <canvas id="profesiChart"></canvas>
                </div>
            </div>
        </div>
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white border-bottom fw-semibold">
                <i class="bi bi-briefcase me-1"></i> Daftar Profesi & Kecocokan
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="4%">#</th>
                                <th>Profesi</th>
                                <th width="13%" class="text-center">CPMK Match</th>
                                <th width="13%" class="text-center">Kecocokan</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($profesiDenganDetail as $i => $p)
                                @php $c = pctColor($p['match_percentage']); @endphp

                                <tr class="cursor-pointer" onclick="toggleDetail({{ $p['id'] }})">
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <strong>{{ $p['nama'] }}</strong>
                                        @if (!empty($p['courses_involved']))
                                            <br><small class="text-success">
                                                <i class="bi bi-check-circle"></i>
                                                Terkait MK:
                                                {{ implode(', ', array_slice($p['courses_involved'], 0, 2)) }}{{ count($p['courses_involved']) > 2 ? '…' : '' }}
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge text-white" style="background:{{ $c }};">
                                            {{ $p['total_cpmk_matched'] }}/{{ $p['total_cpmk_required'] }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold"
                                            style="color:{{ $c }};">{{ $p['match_percentage'] }}%</span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-secondary" id="btn-{{ $p['id'] }}"
                                            onclick="event.stopPropagation();toggleDetail({{ $p['id'] }},this)">
                                            <span class="btn-text">Detail</span>
                                        </button>
                                    </td>
                                </tr>

                                {{-- ── Detail Row ── --}}
                                <tr id="detail-{{ $p['id'] }}" style="display:none;">
                                    <td colspan="5" class="p-0 bg-light">
                                        <div class="p-3">

                                            {{-- Header --}}
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <strong>{{ $p['nama'] }}</strong>
                                                    <span class="ms-2 badge text-white"
                                                        style="background:{{ $c }};">
                                                        {{ $p['match_percentage'] }}% — {{ $p['status'] }}
                                                    </span>
                                                </div>
                                                <button class="btn btn-sm btn-close"
                                                    onclick="event.stopPropagation();closeDetail({{ $p['id'] }})">
                                                </button>
                                            </div>

                                            {{-- Tabel CPMK — kolom Status diganti progress bar --}}
                                            <div class="table-responsive mb-3">
                                                <table class="table table-bordered table-sm mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th width="14%">Kode CPMK</th>
                                                            <th>Deskripsi</th>
                                                            <th width="30%">Nilai</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($p['cpmks'] as $cpmk)
                                                            @php $cc = pctColor($cpmk['nilai']); @endphp
                                                            <tr>
                                                                <td><strong>{{ $cpmk['kode'] }}</strong></td>
                                                                <td>{{ $cpmk['deskripsi'] }}</td>
                                                                <td>
                                                                    @if ($cpmk['nilai'] > 0)
                                                                        <div class="d-flex align-items-center gap-2">
                                                                            <div class="flex-grow-1"
                                                                                style="height:7px;border-radius:99px;background:#e9ecef;">
                                                                                <div
                                                                                    style="width:{{ min($cpmk['nilai'], 100) }}%;height:100%;border-radius:99px;background:{{ $cc }};transition:width .5s;">
                                                                                </div>
                                                                            </div>
                                                                            <span class="fw-bold small"
                                                                                style="color:{{ $cc }};min-width:36px;text-align:right;">
                                                                                {{ $cpmk['nilai'] }}%
                                                                            </span>
                                                                        </div>
                                                                    @else
                                                                        <span class="badge bg-secondary">
                                                                            <i class="bi bi-dash-circle"></i> Belum
                                                                            Dinilai
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            {{-- MK per CPMK --}}
                                            @foreach ($p['cpmks'] as $cpmk)
                                                @if (!empty($cpmk['courses']))
                                                    <div class="mb-3 ps-3 border-start border-3 border-info">
                                                        <small class="text-muted fw-bold d-block mb-1">
                                                            <i class="bi bi-book"></i> Mata Kuliah terkait CPMK
                                                            {{ $cpmk['kode'] }}:
                                                        </small>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered mb-0"
                                                                style="font-size:.85rem;">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th width="15%">Kode MK</th>
                                                                        <th>Nama Mata Kuliah</th>
                                                                        <th width="22%" class="text-center">Status
                                                                        </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($cpmk['courses'] as $course)
                                                                        <tr>
                                                                            <td><strong>{{ $course['kode'] }}</strong>
                                                                            </td>
                                                                            <td>{{ $course['nama'] }}</td>
                                                                            <td class="text-center">
                                                                                @if ($course['diambil'])
                                                                                    <span class="badge bg-success"><i
                                                                                            class="bi bi-check-circle"></i>
                                                                                        Sudah Diambil</span>
                                                                                @else
                                                                                    <span class="badge bg-secondary"><i
                                                                                            class="bi bi-x-circle"></i>
                                                                                        Belum Diambil</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach

                                            {{-- Total & progress --}}
                                            <div class="p-3 bg-white rounded border">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <small class="text-muted">
                                                        Rata-rata dari {{ $p['total_cpmk_matched'] }} CPMK dinilai /
                                                        {{ $p['total_cpmk_required'] }} total CPMK
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
                                                <div class="alert alert-warning mt-2 mb-0 py-2 small">
                                                    <i class="bi bi-lightbulb"></i>
                                                    <strong>Tips:</strong> Ambil MK yang memuat CPMK:
                                                    {{ implode(', ', collect($p['cpmks'])->where('nilai', 0)->pluck('kode')->take(3)->toArray()) }}
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
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom fw-semibold">
                <i class="bi bi-pie-chart me-1"></i> Ringkasan Profil
            </div>
            <div class="card-body p-0">

                {{-- Rekomendasi utama --}}
                @php $top = $profesiDenganDetail->first(); @endphp
                @if ($top)
                    <div class="px-3 pt-3 pb-2">
                        <div class="alert mb-0 py-2"
                            style="background:{{ pctBg($top['match_percentage']) }};border:1px solid {{ pctColor($top['match_percentage']) }}44;">
                            <strong>🎯 Rekomendasi Utama:</strong>
                            <span class="fw-bold">{{ $top['nama'] }}</span>
                            <span class="ms-1" style="color:{{ pctColor($top['match_percentage']) }};">
                                ({{ $top['match_percentage'] }}%)
                            </span>
                        </div>
                    </div>
                @endif

                {{-- 1. Semua CPMK Dinilai --}}
                <div class="border-bottom px-3 py-3">
                    <div class="fw-semibold mb-2 small text-uppercase text-muted" style="letter-spacing:.05em;">
                        <i class="bi bi-list-check me-1"></i> Semua CPMK Dinilai ({{ $allCpmks->count() }} CPMK)
                    </div>
                    @foreach ($allCpmks->sortByDesc('nilai') as $cpmk)
                        @php
                            $cc = pctColor($cpmk['nilai']);
                            $cbg = pctBg($cpmk['nilai']);
                        @endphp
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div style="min-width:90px;">
                                <code class="fw-bold small"
                                    style="color:{{ $cc }};">{{ $cpmk['kode'] }}</code>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small text-muted text-truncate mb-1" style="max-width:420px;"
                                    title="{{ $cpmk['deskripsi'] }}">
                                    {{ $cpmk['deskripsi'] }}
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="flex-grow-1" style="height:6px;border-radius:99px;background:#e9ecef;">
                                        <div
                                            style="width:{{ min($cpmk['nilai'], 100) }}%;height:100%;border-radius:99px;background:{{ $cc }};transition:width .5s;">
                                        </div>
                                    </div>
                                    <span class="fw-bold small"
                                        style="color:{{ $cc }};min-width:36px;text-align:right;">{{ $cpmk['nilai'] }}%</span>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

                {{-- 2. Rata-rata CPMK --}}
                @php
                    $avgAll = round($allCpmks->avg('nilai') ?? 0, 1);
                    $avgColor = pctColor($avgAll);
                @endphp
                <div class="border-bottom px-3 py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fw-semibold small text-uppercase text-muted mb-1" style="letter-spacing:.05em;">
                            <i class="bi bi-calculator me-1"></i> Rata-rata Nilai CPMK
                        </div>
                        <div class="mt-1" style="height:8px;width:200px;border-radius:99px;background:#e9ecef;">
                            <div
                                style="width:{{ $avgAll }}%;height:100%;border-radius:99px;background:{{ $avgColor }};">
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold fs-4 lh-1" style="color:{{ $avgColor }};">{{ $avgAll }}%</div>
                        <div class="small text-muted">dari semua CPMK</div>
                    </div>
                </div>

                {{-- 3. Profesi Cocok --}}
                <div class="px-3 py-3">
                    <div class="fw-semibold small text-uppercase text-muted mb-2" style="letter-spacing:.05em;">
                        <i class="bi bi-briefcase me-1"></i> Profesi Cocok ({{ $profesiDenganDetail->count() }}
                        Profesi)
                    </div>
                    @foreach ($profesiDenganDetail as $p)
                        @php
                            $pc = pctColor($p['match_percentage']);
                            $pbg = pctBg($p['match_percentage']);
                        @endphp
                        <div class="d-flex align-items-center gap-2 mb-2 p-2 rounded"
                            style="background:{{ $pbg }};border:1px solid {{ $pc }}22;">
                            <div class="flex-grow-1">
                                <div class="small fw-bold">{{ $p['nama'] }}</div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <div class="flex-grow-1" style="height:5px;border-radius:99px;background:#e9ecef;">
                                        <div
                                            style="width:{{ min($p['match_percentage'], 100) }}%;height:100%;border-radius:99px;background:{{ $pc }};transition:width .5s;">
                                        </div>
                                    </div>
                                    <span class="small fw-bold"
                                        style="color:{{ $pc }};min-width:40px;text-align:right;">{{ $p['match_percentage'] }}%</span>
                                </div>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <span class="badge small"
                                    style="background:{{ $pbg }};color:{{ $pc }};border:1px solid {{ $pc }}66;">
                                    {{ $p['total_cpmk_matched'] }}/{{ $p['total_cpmk_required'] }} CPMK
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    @endif
    </div>

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
            }, 800); // kasih delay dikit biar PDF kebuka dulu
        }
        // ── Toggle detail: bisa buka & tutup ──
        function toggleDetail(id, btn) {
            const row = document.getElementById('detail-' + id);
            const b = btn || document.getElementById('btn-' + id);
            const isOpen = row && row.style.display !== 'none';

            // tutup semua
            document.querySelectorAll('[id^="detail-"]').forEach(el => {
                el.style.display = 'none';
                const ob = document.getElementById('btn-' + el.id.replace('detail-', ''));
                if (ob) {
                    ob.classList.remove('btn-success', 'text-white');
                    ob.classList.add('btn-outline-secondary');
                    ob.querySelector('.btn-text').textContent = 'Detail';
                }
            });

            // jika tadi tertutup → buka
            if (!isOpen && row) {
                row.style.display = 'table-row';
                if (b) {
                    b.classList.remove('btn-outline-secondary');
                    b.classList.add('btn-success', 'text-white');
                    b.querySelector('.btn-text').textContent = 'Tutup';
                }
                row.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }
            // jika tadi terbuka → sudah ditutup di atas (toggle off)
        }

        function closeDetail(id) {
            const row = document.getElementById('detail-' + id);
            const btn = document.getElementById('btn-' + id);
            if (row) row.style.display = 'none';
            if (btn) {
                btn.classList.remove('btn-success', 'text-white');
                btn.classList.add('btn-outline-secondary');
                btn.querySelector('.btn-text').textContent = 'Detail';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const data = @json($chartProfesi);
            const ids = @json($profesiDenganDetail->pluck('id')->take(5)->values());
            const labels = data.map(d => d.label);
            const values = data.map(d => d.value);
            const colors = values.map(v => v <= 50 ? '#D55E00' : v <= 74 ? '#E69F00' : '#0072B2');

            new Chart(document.getElementById('profesiChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors.map(c => c + 'CC'),
                        borderColor: colors,
                        borderWidth: 2,
                        borderRadius: 4,
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
                                label: ctx => ` ${data[ctx.dataIndex].full_name}: ${ctx.parsed.x}%`
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
                        if (els.length) toggleDetail(ids[els[0].index]);
                    },
                    // label nilai di ujung kanan bar
                    animation: {
                        onComplete(ctx) {
                            const chart = ctx.chart;
                            const c = chart.ctx;
                            c.save();
                            c.font = 'bold 12px sans-serif';
                            c.textBaseline = 'middle';
                            chart.data.datasets[0].data.forEach((val, i) => {
                                const bar = chart.getDatasetMeta(0).data[i];
                                c.fillStyle = colors[i];
                                c.fillText(val + '%', bar.x + 6, bar.y);
                            });
                            c.restore();
                        }
                    }
                }
            });
        });
    </script>

    <style>
        .cursor-pointer {
            cursor: pointer;
        }

        .cursor-pointer:hover td {
            background-color: #f0f4ff !important;
        }
    </style>
@endsection
