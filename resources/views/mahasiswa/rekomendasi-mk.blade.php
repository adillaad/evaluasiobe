@extends('mahasiswa.template')

@section('content')
    <div class="container py-4">

        <div class="mb-4">
            <h4 class="fw-bold mb-1">Rekomendasi Mata Kuliah</h4>
            <p class="text-muted mb-0">
                Berdasarkan capaian CPMK Anda. Diurutkan dari mata kuliah yang <strong>direkomendasikan</strong>
                (capaian CPMK terendah = paling perlu ditingkatkan).
            </p>
        </div>

        @if ($rekomendasiMk->isEmpty())
            <div class="alert alert-info d-flex align-items-center gap-2">
                <i class="bi bi-info-circle-fill fs-5"></i>
                <span>Belum ada mata kuliah yang terhubung dengan CPMK Anda. Hubungi administrator.</span>
            </div>
        @else
            <div class="row g-3 mb-4">

                {{-- SKS Progress --}}
                <div class="col-12 col-md-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            @php
                                $sksPct = min(round(($sksLulus / 144) * 100, 1), 100);
                                $sksColor = $sksPct <= 50 ? '#D55E00' : ($sksPct <= 74 ? '#E69F00' : '#0072B2');
                            @endphp
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing:.05em;">
                                        Progres SKS Lulus
                                    </div>
                                    <div class="fs-4 fw-bold mt-1 lh-1">
                                        {{ $sksLulus }}
                                        <span class="fs-6 fw-normal text-muted">/ 144 SKS</span>
                                    </div>
                                </div>
                                <span class="badge rounded-pill fs-6 px-3 py-2 text-white"
                                    style="background-color:{{ $sksColor }};">
                                    {{ $sksPct }}%
                                </span>
                            </div>
                            <div class="progress mb-2" style="height:10px;border-radius:99px;">
                                <div class="progress-bar" role="progressbar"
                                    style="width:{{ $sksPct }}%;background-color:{{ $sksColor }};border-radius:99px;"
                                    aria-valuenow="{{ $sksPct }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <div class="text-muted small">
                                Sisa <strong>{{ max(0, 144 - $sksLulus) }} SKS</strong> lagi untuk mencapai batas kelulusan
                                (144 SKS)
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CPMK Keseluruhan --}}
                <div class="col-12 col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div
                            class="card-body d-flex flex-column align-items-center justify-content-center text-center py-3">
                            @php
                                $avgAll = $stats['avg_keseluruhan'];
                                $avgColor = $avgAll <= 50 ? '#D55E00' : ($avgAll <= 74 ? '#E69F00' : '#0072B2');
                            @endphp
                            <div class="text-muted small fw-semibold text-uppercase mb-2" style="letter-spacing:.05em;">
                                Rata-rata CPMK
                            </div>
                            <div class="w-100">
                                <div class="fw-bold mb-1" style="color:{{ $avgColor }}">
                                    {{ $avgAll }}%
                                </div>
                                <div class="progress" style="height:8px;border-radius:99px;">
                                    <div class="progress-bar"
                                        style="width:{{ $avgAll }}%;background-color:{{ $avgColor }};">
                                    </div>
                                </div>
                            </div>
                            <div class="text-muted small">Keseluruhan CPMK</div>
                        </div>
                    </div>
                </div>

                {{-- Ringkasan --}}
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small fw-semibold text-uppercase mb-3" style="letter-spacing:.05em;">
                                Ringkasan Rekomendasi
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small">Total Persentase nilai CPMK tiap MK</span>
                                <strong>{{ $stats['total_mk_rekomendasi'] }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small d-flex align-items-center gap-1">
                                    <span class="rounded-circle"
                                        style="width:10px;height:10px;background:#D55E00;display:inline-block;"></span>
                                    Nilai Persentase CPMK (&lt;50%)
                                </span>
                                <strong style="color:#D55E00;">
                                    {{ $rekomendasiMk->filter(fn($m) => $m['avg_cpmk'] <= 50)->count() }}
                                </strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small d-flex align-items-center gap-1">
                                    <span class="rounded-circle"
                                        style="width:10px;height:10px;background:#E69F00;display:inline-block;"></span>
                                    Nilai Persentase CPMK (51–74%)
                                </span>
                                <strong style="color:#E69F00;">
                                    {{ $rekomendasiMk->filter(fn($m) => $m['avg_cpmk'] > 50 && $m['avg_cpmk'] < 75)->count() }}
                                </strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small d-flex align-items-center gap-1">
                                    <span class="rounded-circle"
                                        style="width:10px;height:10px;background:#0072B2;display:inline-block;"></span>
                                    Nilai Persentase CPMK (≥75%)
                                </span>
                                <strong style="color:#0072B2;">
                                    {{ $rekomendasiMk->filter(fn($m) => $m['avg_cpmk'] >= 75)->count() }}
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- /row stats --}}

            @php
                $semesterList = $rekomendasiMk->pluck('semester')->unique()->sort()->values();
            @endphp
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body py-2 px-3">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="small text-muted fw-semibold me-1">Filter Semester:</span>
                        <select class="form-select form-select-sm" style="max-width:200px;"
                            onchange="filterSemDropdown(this)">
                            <option value="all">Semua Semester</option>
                            @foreach ($semesterList as $sem)
                                <option value="{{ $sem }}">Semester {{ $sem }}</option>
                            @endforeach
                        </select>

                        <div class="ms-auto">
                            <input type="text" id="mkSearch" class="form-control form-control-sm"
                                placeholder="Cari kode / nama MK…" style="min-width:220px;" oninput="applyFilters()">
                        </div>
                    </div>
                </div>
            </div>

            <div id="mkList">
                @foreach ($rekomendasiMk as $index => $mk)
                    @php
                        $avg = $mk['avg_cpmk'];
                        $barColor = $avg <= 50 ? '#D55E00' : ($avg <= 74 ? '#E69F00' : '#0072B2');
                        $bgLight = $avg <= 50 ? '#D55E000D' : ($avg <= 74 ? '#E69F000D' : '#0072B20D');
                    @endphp

                    <div class="card border-0 shadow-sm mb-3 mk-item" data-sem="{{ $mk['semester'] }}"
                        data-search="{{ strtolower($mk['kode'] . ' ' . $mk['nama']) }}">
                        <div class="card-body p-0">

                            {{-- ── MK Header ── --}}
                            <div class="d-flex flex-wrap align-items-start gap-3 px-4 pt-3 pb-2"
                                style="border-left:4px solid {{ $barColor }};">

                                {{-- Nomor urut --}}
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0 mt-1"
                                    style="width:34px;height:34px;font-size:.82rem;
                                background-color:{{ $barColor }}1A;
                                color:{{ $barColor }};
                                border:1.5px solid {{ $barColor }};">
                                    {{ $index + 1 }}
                                </div>

                                {{-- Info utama --}}
                                <div class="flex-grow-1" style="min-width:0;">
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                        <code class="text-secondary fw-bold"
                                            style="font-size:.78rem;">{{ $mk['kode'] }}</code>
                                        <span class="fw-bold fs-6">{{ $mk['nama'] }}</span>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-2">

                                        {{-- Semester --}}
                                        <span
                                            class="badge bg-secondary bg-opacity-10 text-secondary fw-normal small border">
                                            <i class="bi bi-calendar3 me-1"></i>Semester {{ $mk['semester'] }}
                                        </span>

                                        {{-- SKS --}}
                                        <span
                                            class="badge bg-secondary bg-opacity-10 text-secondary fw-normal small border">
                                            <i class="bi bi-book me-1"></i>{{ $mk['sks'] }} SKS
                                        </span>

                                        {{-- Wajib / Pilihan --}}
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

                                        {{-- Rumpun --}}
                                        @if (!empty($mk['rumpun']))
                                            <span
                                                class="badge bg-secondary bg-opacity-10 text-secondary fw-normal small border">
                                                {{ $mk['rumpun'] }}
                                            </span>
                                        @endif

                                    </div>

                                    @if (!empty($mk['deskripsi']))
                                        <div class="text-muted small mt-1">{{ $mk['deskripsi'] }}</div>
                                    @endif
                                </div>

                                {{-- Avg CPMK badge (kanan) --}}
                                <div class="text-end flex-shrink-0" style="min-width:80px;">
                                    <div class="fw-bold lh-1 mb-1" style="font-size:1.35rem;color:{{ $barColor }};">
                                        {{ round($avg, 1) }}%
                                    </div>
                                    <div class="text-muted mb-1" style="font-size:.68rem;white-space:nowrap;">rata-rata
                                        CPMK</div>
                                    <div style="height:6px;border-radius:99px;background:#e9ecef;">
                                        <div
                                            style="width:{{ min($avg, 100) }}%;height:100%;border-radius:99px;background:{{ $barColor }};">
                                        </div>
                                    </div>
                                </div>

                            </div>{{-- /MK header --}}

                            <hr class="my-0 mx-4">

                            {{-- ── CPMK Terkait ── --}}
                            <div class="px-4 pb-3 pt-2" style="padding-left:5rem !important;">
                                <div class="text-muted mb-2"
                                    style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.07em;">
                                    CPMK Terkait &mdash; {{ count($mk['cpmk_terkait']) }} CPMK
                                </div>

                                @foreach ($mk['cpmk_terkait']->sortBy('persentase') as $cpmk)
                                    @php
                                        $cp = $cpmk['persentase'];
                                        $cc = $cp <= 50 ? '#D55E00' : ($cp <= 74 ? '#E69F00' : '#0072B2');
                                        $cb = $cp <= 50 ? '#D55E000D' : ($cp <= 74 ? '#E69F000D' : '#0072B20D');
                                    @endphp
                                    <div class="d-flex align-items-center gap-3 rounded mb-2 px-2 py-2"
                                        style="background:{{ $cb }};border:1px solid {{ $cc }}22;">

                                        {{-- Kode CPMK --}}
                                        <div style="min-width:88px;flex-shrink:0;">
                                            <code class="fw-bold" style="font-size:.72rem;color:{{ $cc }};">
                                                {{ $cpmk['kode'] }}
                                            </code>
                                        </div>

                                        {{-- Deskripsi + progress --}}
                                        <div class="flex-grow-1" style="min-width:0;">
                                            <div class="small text-muted text-truncate mb-1"
                                                title="{{ $cpmk['deskripsi'] }}" style="max-width:760px;">
                                                {{ $cpmk['deskripsi'] }}
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="flex-grow-1"
                                                    style="height:6px;border-radius:99px;background:#e9ecef;">
                                                    <div
                                                        style="width:{{ min($cp, 100) }}%;height:100%;border-radius:99px;background:{{ $cc }};transition:width .5s;">
                                                    </div>
                                                </div>
                                                <span class="fw-bold"
                                                    style="font-size:.75rem;color:{{ $cc }};min-width:36px;text-align:right;">
                                                    {{ $cp }}%
                                                </span>
                                            </div>
                                        </div>

                                    </div>{{-- /cpmk row --}}
                                @endforeach

                            </div>{{-- /cpmk section --}}

                        </div>{{-- /card-body --}}
                    </div>{{-- /card --}}
                @endforeach

                {{-- No result --}}
                <div id="noResult" class="text-center text-muted py-5" style="display:none;">
                    <i class="bi bi-search fs-1 d-block mb-2 opacity-25"></i>
                    <div>Tidak ada mata kuliah yang cocok dengan filter atau pencarian Anda.</div>
                </div>

            </div>{{-- /#mkList --}}
        @endif
    </div>

    <script>
        let activeSem = 'all';

        function filterSem(btn, sem) {
            activeSem = sem;
            document.querySelectorAll('#semFilter button, .card button[onclick]').forEach(b => {
                if (b.getAttribute('onclick') && b.getAttribute('onclick').startsWith('filterSem')) {
                    b.classList.remove('btn-primary');
                    b.classList.add('btn-outline-primary');
                }
            });
            // simpler: just reset all filter buttons
            document.querySelectorAll('button[onclick^="filterSem"]').forEach(b => {
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline-primary');
            });
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-primary');
            applyFilters();
        }

        function filterSemDropdown(select) {
            activeSem = select.value;
            applyFilters();
        }

        function applyFilters() {
            const q = (document.getElementById('mkSearch')?.value || '').toLowerCase().trim();
            const items = document.querySelectorAll('.mk-item');
            let visible = 0;

            items.forEach(item => {
                const semMatch = activeSem === 'all' || String(item.dataset.sem) === String(activeSem);
                const searchMatch = !q || item.dataset.search.includes(q);
                const show = semMatch && searchMatch;
                item.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            const noRes = document.getElementById('noResult');
            if (noRes) noRes.style.display = visible === 0 ? 'block' : 'none';
        }
    </script>
@endsection
