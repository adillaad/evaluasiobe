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
            margin-bottom: 16px;
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

@section('content')
    <div class="container-fluid py-2 px-md-4">

        {{-- ── 1. Page Header Bar (No Info Card) ─────────────────────────── --}}
        <div class="page-header-box">
            <div class="row align-items-center">
                <div class="col-lg-8 mb-3 mb-lg-0">
                    <h4 class="fw-bold mb-1 text-dark">
                        <i class="bi bi-book-half text-primary me-2"></i>Rekomendasi Pengambilan Mata Kuliah
                    </h4>
                    <p class="text-muted small mb-0">
                        Rekomendasi mata kuliah lanjutan kurikulum program studi berdasarkan hasil analisis capaian CPMK prasyarat dan ketercapaian OBE
                    </p>
                </div>

                <div class="col-lg-4 text-lg-end">
                    <div class="d-flex align-items-center justify-content-lg-end gap-2 flex-wrap">
                        <button class="modern-btn-primary" onclick="printRekomendasi()">
                            <i class="bi bi-printer"></i> Cetak
                        </button>
                        <a href="{{ route('mahasiswa.rekomendasi-mk.pdf') }}" target="_blank" class="modern-btn-outline">
                            <i class="bi bi-file-earmark-pdf text-danger"></i> Unduh PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if ($rekomendasiMk->isEmpty())
            <div class="alert alert-info border-0 shadow-sm mb-4 py-3 px-4"
                style="background: #e8f4fd; border-left: 5px solid #0288d1 !important; border-radius: 12px;">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle-fill text-info fs-4 me-3 flex-shrink-0"></i>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Tidak Ada Mata Kuliah Rekomendasi</h6>
                        <p class="mb-0 text-secondary small">
                            Semua mata kuliah pada kurikulum program studi telah diambil atau belum ada pemetaan CPMK lanjutan.
                        </p>
                    </div>
                </div>
            </div>
        @else
            {{-- ── 2. Stat Metric Cards ────────────────────────────────────── --}}
            <div class="row g-3 mb-4">
                {{-- Progres SKS --}}
                <div class="col-12 col-md-4">
                    <div class="stat-card h-100 d-flex flex-column justify-content-between">
                        @php
                            $sksPct = min(round(($sksLulus / 144) * 100, 1), 100);
                            $sksColor = $sksPct <= 50 ? '#D55E00' : ($sksPct <= 74 ? '#E69F00' : '#0072B2');
                        @endphp
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Progres SKS Lulus</span>
                                <div class="stat-icon-pill bg-primary-subtle text-primary">
                                    <i class="bi bi-journal-check"></i>
                                </div>
                            </div>
                            <div class="stat-value mb-1 text-primary">
                                {{ $sksLulus }} <span class="fs-6 fw-normal text-muted">/ 144 SKS</span>
                            </div>
                            <div class="progress mb-2" style="height:6px;">
                                <div class="progress-bar" style="width:{{ $sksPct }}%; background-color:{{ $sksColor }};"></div>
                            </div>
                        </div>
                        <small class="text-muted">
                            Sisa <strong>{{ max(0, 144 - $sksLulus) }} SKS</strong> lagi menuju batas kelulusan
                        </small>
                    </div>
                </div>

                {{-- Rata-rata Skor CPMK --}}
                <div class="col-12 col-md-4">
                    <div class="stat-card h-100 d-flex flex-column justify-content-between">
                        @php
                            $avgAll = $stats['avg_keseluruhan'];
                            $avgColor = $avgAll <= 50 ? '#D55E00' : ($avgAll <= 74 ? '#E69F00' : '#0072B2');
                        @endphp
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Rata-rata Skor CPMK</span>
                                <div class="stat-icon-pill bg-warning-subtle text-warning">
                                    <i class="bi bi-award"></i>
                                </div>
                            </div>
                            <div class="stat-value mb-1" style="color:{{ $avgColor }};">{{ $avgAll }}</div>
                            <div class="progress mb-2" style="height:6px;">
                                <div class="progress-bar" style="width:{{ min($avgAll, 100) }}%; background-color:{{ $avgColor }};"></div>
                            </div>
                        </div>
                        <small class="text-muted">Skor Keseluruhan CPMK Terpenuhi</small>
                    </div>
                </div>

                {{-- Ringkasan Kategori --}}
                <div class="col-12 col-md-4">
                    <div class="stat-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Status Prioritas MK</span>
                                <div class="stat-icon-pill bg-danger-subtle text-danger">
                                    <i class="bi bi-exclamation-octagon"></i>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted d-flex align-items-center gap-1">
                                    <span class="legend-indicator-dot" style="background:#D55E00;"></span> Perlu Peningkatan (&le; 50)
                                </span>
                                <strong style="color:#D55E00;">{{ $rekomendasiMk->filter(fn($m) => $m['avg_cpmk'] <= 50)->count() }} MK</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted d-flex align-items-center gap-1">
                                    <span class="legend-indicator-dot" style="background:#E69F00;"></span> Cukup (51–74)
                                </span>
                                <strong style="color:#E69F00;">{{ $rekomendasiMk->filter(fn($m) => $m['avg_cpmk'] > 50 && $m['avg_cpmk'] < 75)->count() }} MK</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted d-flex align-items-center gap-1">
                                    <span class="legend-indicator-dot" style="background:#0072B2;"></span> Baik (&ge; 75)
                                </span>
                                <strong style="color:#0072B2;">{{ $rekomendasiMk->filter(fn($m) => $m['avg_cpmk'] >= 75)->count() }} MK</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- ── 4. Search Bar (Semester Filter Removed) ──────────────────── --}}
            <div class="modern-card mb-4">
                <div class="p-3">
                    <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted fw-semibold">
                                <i class="bi bi-list-stars me-1 text-primary"></i> Total Rekomendasi: <strong>{{ $rekomendasiMk->count() }} Mata Kuliah</strong>
                            </span>
                        </div>

                        <div class="flex-grow-1" style="max-width:340px;">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="mkSearch" class="form-control border-start-0" placeholder="Cari kode atau nama mata kuliah…" oninput="applyFilters()">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── 5. Daftar Kartu Rekomendasi MK ─────────────────────────── --}}
            <div id="mkList">
                @foreach ($rekomendasiMk as $index => $mk)
                    @php
                        $avg = $mk['avg_cpmk'];
                        $barColor = $avg <= 50 ? '#D55E00' : ($avg <= 74 ? '#E69F00' : '#0072B2');
                        $bgLight = $avg <= 50 ? '#D55E000D' : ($avg <= 74 ? '#E69F000D' : '#0072B20D');
                    @endphp

                    <div class="modern-card mk-item"
                        data-search="{{ strtolower($mk['kode'] . ' ' . $mk['nama']) }}">
                        <div class="p-0">

                            {{-- MK Header --}}
                            <div class="d-flex flex-wrap align-items-start gap-3 px-4 pt-3 pb-3"
                                style="border-left: 5px solid {{ $barColor }};">

                                {{-- Nomor Urut --}}
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0 mt-1"
                                    style="width:34px;height:34px;font-size:.82rem; background-color:{{ $barColor }}1A; color:{{ $barColor }}; border:1.5px solid {{ $barColor }};">
                                    {{ $index + 1 }}
                                </div>

                                {{-- Info Utama MK --}}
                                <div class="flex-grow-1" style="min-width:0;">
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                        <code class="fw-bold px-2 py-1 rounded" style="font-size:.78rem; background:#f1f5f9; color:#334155;">{{ $mk['kode'] }}</code>
                                        <span class="fw-bold fs-6 text-dark">{{ $mk['nama'] }}</span>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge bg-secondary-subtle text-secondary fw-semibold small border">
                                            <i class="bi bi-calendar3 me-1"></i>Semester {{ $mk['semester'] }}
                                        </span>
                                        <span class="badge bg-primary-subtle text-primary fw-semibold small border border-primary-subtle">
                                            <i class="bi bi-book me-1"></i>{{ $mk['sks'] }} SKS
                                        </span>
                                        @if (!empty($mk['jenis']))
                                            @if (strtolower($mk['jenis']) === 'wajib')
                                                <span class="badge text-white small" style="background:#0072B2;">
                                                    <i class="bi bi-check2-circle me-1"></i>Wajib
                                                </span>
                                            @else
                                                <span class="badge bg-secondary small">
                                                    <i class="bi bi-circle me-1"></i>Pilihan
                                                </span>
                                            @endif
                                        @endif
                                        @if (!empty($mk['rumpun']))
                                            <span class="badge bg-light text-muted fw-normal small border">
                                                {{ $mk['rumpun'] }}
                                            </span>
                                        @endif
                                    </div>

                                    @if (!empty($mk['deskripsi']))
                                        <div class="text-muted small mt-2" style="font-size: 0.83rem;">{{ $mk['deskripsi'] }}</div>
                                    @endif
                                </div>

                                {{-- Avg CPMK Badge Kanan --}}
                                <div class="text-end flex-shrink-0" style="min-width:90px;">
                                    <div class="fw-bold lh-1 mb-1" style="font-size:1.45rem;color:{{ $barColor }};">
                                        {{ round($avg, 1) }}
                                    </div>
                                    <div class="text-muted mb-1" style="font-size:.72rem;white-space:nowrap;">Rata-rata CPMK</div>
                                    <div style="height:6px;border-radius:99px;background:#e9ecef;">
                                        <div style="width:{{ min($avg, 100) }}%;height:100%;border-radius:99px;background:{{ $barColor }};"></div>
                                    </div>
                                </div>

                            </div>

                            <hr class="my-0 mx-4" style="color:#f1f5f9;">

                            {{-- CPMK Terkait --}}
                            <div class="px-4 pb-3 pt-3">
                                <div class="text-muted mb-2" style="font-size:.74rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">
                                    <i class="bi bi-list-check me-1"></i> CPMK Terkait &mdash; {{ count($mk['cpmk_terkait']) }} CPMK
                                </div>

                                <div class="row g-2">
                                    @foreach ($mk['cpmk_terkait']->sortBy('persentase') as $cpmk)
                                        @php
                                            $cp = $cpmk['persentase'];
                                            $cc = $cp <= 50 ? '#D55E00' : ($cp <= 74 ? '#E69F00' : '#0072B2');
                                            $cb = $cp <= 50 ? '#D55E000D' : ($cp <= 74 ? '#E69F000D' : '#0072B20D');
                                        @endphp
                                        <div class="col-12">
                                            <div class="d-flex align-items-center gap-3 rounded p-2" style="background:{{ $cb }};border:1px solid {{ $cc }}22;">
                                                <div style="min-width:85px;flex-shrink:0;">
                                                    <code class="fw-bold" style="font-size:.74rem;color:{{ $cc }};">
                                                        {{ $cpmk['kode'] }}
                                                    </code>
                                                </div>
                                                <div class="flex-grow-1" style="min-width:0;">
                                                    <div class="small text-muted text-truncate mb-1" title="{{ $cpmk['deskripsi'] }}" style="max-width:760px;">
                                                        {{ $cpmk['deskripsi'] }}
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="flex-grow-1" style="height:6px;border-radius:99px;background:#e9ecef;">
                                                            <div style="width:{{ min($cp, 100) }}%;height:100%;border-radius:99px;background:{{ $cc }};"></div>
                                                        </div>
                                                        <span class="fw-bold" style="font-size:.75rem;color:{{ $cc }};min-width:36px;text-align:right;">
                                                            {{ $cp }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>

                        </div>
                    </div>
                @endforeach

                {{-- No result --}}
                <div id="noResult" class="text-center text-muted py-5" style="display:none;">
                    <i class="bi bi-search fs-1 d-block mb-2 opacity-25"></i>
                    <div>Tidak ada mata kuliah yang cocok dengan pencarian Anda.</div>
                </div>
            </div>
        @endif

    </div>
@endsection

@push('scripts')
    <script>
        function printRekomendasi() {
            const url = "{{ route('mahasiswa.rekomendasi-mk.pdf') }}";
            const w = window.open(url, '_blank');
            setTimeout(() => {
                if (w) {
                    w.focus();
                    w.print();
                }
            }, 800);
        }

        function applyFilters() {
            const q = (document.getElementById('mkSearch')?.value || '').toLowerCase().trim();
            const items = document.querySelectorAll('.mk-item');
            let visible = 0;

            items.forEach(item => {
                const searchMatch = !q || (item.dataset.search && item.dataset.search.includes(q));
                item.style.display = searchMatch ? '' : 'none';
                if (searchMatch) visible++;
            });

            const noRes = document.getElementById('noResult');
            if (noRes) noRes.style.display = visible === 0 ? 'block' : 'none';
        }
    </script>
@endpush
