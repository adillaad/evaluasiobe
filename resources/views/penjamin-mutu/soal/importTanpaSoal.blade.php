{{-- @php
$routePrefix = [
    'Penjamin Mutu Universitas' => ['prefix' => 'penjamin-mutu.universitas.'],
    'Penjamin Mutu Fakultas' => ['prefix' => 'penjamin-mutu.fakultas.'],
    'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
];

$userOtoritas = auth()->user()->otoritas->otoritas;
$currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp --}}
@extends('penjamin-mutu.template')
@section('content')
    @if (session()->has('failed'))
        <div class="alert alert-danger" role="alert" id="box">
            <div>{{ session('failed') }}</div>
        </div>
    @elseif (session()->has('success'))
        <div class="alert greenAdd" role="alert" id="box">
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @php
        $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
        $isUnivLevel = in_array($userOtoritas, ['Penjamin Mutu Universitas', 'Admin Universitas', 'Wakil Rektor']);
        $isFacultyLevel = in_array($userOtoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan']);
        $isProdiLevel = in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Koordinator Program Studi', 'Kepala Program Studi', 'Dosen']);
    @endphp

    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form id="filterFormTS" action="{{ route($currentPrefix . 'penilaian.filter') }}" method="get" class="mb-3">
                    @csrf
                    <input type="hidden" name="type" value="tanpa-soal">
                    <div class="d-flex flex-column gap-3">
                        {{-- 1. Baris Filter Dropdown (Di Atas) --}}
                        <div class="d-flex align-items-end flex-wrap gap-2">
                            {{-- Filter Fakultas (Khusus Level Universitas) --}}
                            @if ($isUnivLevel && isset($faculties) && $faculties->isNotEmpty())
                                <div style="min-width: 150px;">
                                    <label for="fakultas_id" class="form-label fw-bold small mb-1">Fakultas</label>
                                    <select name="fakultas_id" id="fakultas_id" class="form-select form-select-sm filter-auto-submit" style="height: 38px;">
                                        <option value="">-- Semua Fakultas --</option>
                                        @foreach ($faculties as $fac)
                                            <option value="{{ $fac->id }}" {{ request('fakultas_id') == $fac->id ? 'selected' : '' }}>
                                                {{ $fac->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            {{-- Filter Prodi (Level Universitas & Fakultas) --}}
                            @if (($isUnivLevel || $isFacultyLevel) && isset($programs) && $programs->isNotEmpty())
                                <div style="min-width: 150px;">
                                    <label for="prodi_id" class="form-label fw-bold small mb-1">Program Studi</label>
                                    <select name="prodi_id" id="prodi_id" class="form-select form-select-sm filter-auto-submit" style="height: 38px;">
                                        <option value="">-- Semua Prodi --</option>
                                        @foreach ($programs as $prog)
                                            <option value="{{ $prog->id }}" {{ request('prodi_id') == $prog->id ? 'selected' : '' }}>
                                                {{ $prog->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            {{-- Filter Kurikulum --}}
                            @if (isset($kurikulums) && $kurikulums->isNotEmpty())
                                <div style="min-width: 140px;">
                                    <label for="kurikulum_id" class="form-label fw-bold small mb-1">Kurikulum</label>
                                    <select name="kurikulum_id" id="kurikulum_id" class="form-select form-select-sm filter-auto-submit" style="height: 38px;">
                                        <option value="">-- Semua Kurikulum --</option>
                                        @foreach ($kurikulums as $kur)
                                            <option value="{{ $kur->id }}" {{ request('kurikulum_id') == $kur->id ? 'selected' : '' }}>
                                                {{ $kur->tahun }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            {{-- Filter Tahun Ajaran --}}
                            @if (isset($tahunAjarans) && $tahunAjarans->isNotEmpty())
                                <div style="min-width: 140px;">
                                    <label for="tahun_ajaran_id" class="form-label fw-bold small mb-1">Tahun Akademik</label>
                                    <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="form-select form-select-sm filter-auto-submit" style="height: 38px;">
                                        <option value="">-- Semua TA --</option>
                                        @foreach ($tahunAjarans as $ta)
                                            <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                                                {{ $ta->label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            {{-- Filter Mata Kuliah --}}
                            @if (isset($mks) && $mks->isNotEmpty())
                                <div style="min-width: 170px;">
                                    <label for="mk_kode" class="form-label fw-bold small mb-1">Mata Kuliah</label>
                                    <select name="mk_kode" id="mk_kode" class="form-select form-select-sm filter-auto-submit" style="height: 38px;">
                                        <option value="">-- Semua MK --</option>
                                        @foreach ($mks as $mk)
                                            <option value="{{ $mk->kode }}" {{ request('mk_kode') == $mk->kode ? 'selected' : '' }}>
                                                {{ $mk->nama }} ({{ $mk->kode }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            {{-- Filter Metode Penilaian --}}
                            @if (isset($metodePenilaians) && $metodePenilaians->isNotEmpty())
                                <div style="min-width: 150px;">
                                    <label for="metode_id" class="form-label fw-bold small mb-1">Metode Penilaian</label>
                                    <select name="metode_id" id="metode_id" class="form-select form-select-sm filter-auto-submit" style="height: 38px;">
                                        <option value="">-- Semua Metode --</option>
                                        @foreach ($metodePenilaians as $mp)
                                            <option value="{{ $mp->id }}" {{ request('metode_id') == $mp->id ? 'selected' : '' }}>
                                                {{ $mp->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            {{-- Tombol Reset --}}
                            @if (request('course') || request('fakultas_id') || request('prodi_id') || request('kurikulum_id') || request('tahun_ajaran_id') || request('mk_kode') || request('metode_id'))
                                <div>
                                    <a href="{{ route($currentPrefix . 'penilaian.penilaian-tanpa-soal') }}" class="btn btn-secondary btn-sm px-4 fw-semibold d-flex align-items-center justify-content-center" style="height: 38px;">
                                        <i class="mdi mdi-refresh me-1"></i> Reset
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- 2. Input Pencarian (Di Bawah) --}}
                        <div class="d-flex align-items-end gap-2">
                            <div style="max-width: 350px; width: 100%;">
                                <label for="course" class="form-label fw-bold small mb-1">Cari Mahasiswa</label>
                                <input id="course" name="course" type="text" class="form-control form-control-sm instant-search" style="height: 38px;"
                                    placeholder="Ketik nama atau NPM..."
                                    value="{{ request('course') }}" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </form>

                <style>
                    .toggle-soal-btn[aria-expanded="true"] .mdi-chevron-down {
                        transform: rotate(180deg);
                    }
                    .toggle-soal-btn .mdi-chevron-down {
                        transition: transform 0.2s ease-in-out;
                    }
                </style>

                                @php
                    $totalCols = $isUnivLevel ? 9 : ($isFacultyLevel ? 8 : 7);
                @endphp

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                @if ($isUnivLevel)
                                    <th>Fakultas</th>
                                    <th>Prodi</th>
                                @elseif ($isFacultyLevel)
                                    <th>Prodi</th>
                                @endif
                                <th style="width: 140px;">Tahun Akademik</th>
                                <th style="width: 70px;" class="text-center">Angkatan</th>
                                <th style="width: 120px;">NPM</th>
                                <th>Nama</th>
                                <th>Mata Kuliah</th>
                                <th class="text-center" style="width: 120px;">Nilai Akhir MK</th>
                                <th class="text-center" style="width: 130px;">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($mutus->isEmpty())
                                <tr>
                                    <td colspan="{{ $totalCols }}" class="text-center text-muted py-5">
                                        <i class="mdi mdi-folder-open-outline me-2"></i>Tidak ada data penilaian
                                    </td>
                                </tr>
                            @else
                                @php $groupIndex = 0; @endphp
                                @foreach ($mutus as $row)
                                    @php
                                        $groupIndex++;
                                        $detailId = 'detail-mk-ts-' . $groupIndex;
                                        $metodes   = $row->metodes ?? collect();
                                        $skorCpmk  = $row->skor_cpmk ?? [];
                                    @endphp

                                    {{-- Main Row: 1 baris per mahasiswa per MK --}}
                                    <tr>
                                        @if ($isUnivLevel)
                                            <td class="fw-semibold">{{ $row->nama_fakultas ?? '-' }}</td>
                                            <td class="fw-semibold">{{ $row->nama_prodi ?? '-' }}</td>
                                        @elseif ($isFacultyLevel)
                                            <td class="fw-semibold">{{ $row->nama_prodi ?? '-' }}</td>
                                        @endif
                                        <td class="small fw-semibold text-secondary">{{ $row->ta_label ?? '-' }}</td>
                                        <td class="text-center text-muted">{{ $row->angkatan ?: '-' }}</td>
                                        <td>
                                            <span class="font-monospace fw-semibold">{{ $row->npm }}</span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $row->nama_mhs ?: 'Tanpa Nama' }}</div>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold text-dark">{{ $row->nama_mk ?: $row->Course }}</div>
                                            <div class="text-muted small">{{ $row->Course }}</div>
                                        </td>
                                        <td class="text-center fw-bold text-dark">
                                            {{ number_format((float)($row->nilai_akhir_mk ?? 0), 2) }}
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 toggle-soal-btn d-inline-flex align-items-center gap-1 shadow-none"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#{{ $detailId }}"
                                                aria-expanded="false"
                                                aria-controls="{{ $detailId }}">
                                                <i class="mdi mdi-view-list"></i>
                                                <span>{{ $metodes->count() }} Metode</span>
                                                <i class="mdi mdi-chevron-down ms-1"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    {{-- Detail Collapse: Metode-level breakdown --}}
                                    <tr class="collapse bg-light bg-opacity-50" id="{{ $detailId }}">
                                        <td colspan="{{ $totalCols }}" class="p-3 border-top-0">
                                            <div class="card border-0 shadow-sm rounded-3">
                                                <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                                                    <span class="fw-bold small text-primary">
                                                        <i class="mdi mdi-school-outline me-1"></i>
                                                        Rincian Nilai per Metode &mdash; {{ $row->nama_mk ?: $row->Course }}
                                                    </span>
                                                    <span class="badge bg-light text-secondary border small">
                                                        {{ $metodes->count() }} Metode
                                                    </span>
                                                </div>
                                                <div class="p-3">
                                                    @foreach ($metodes as $mIdx => $metode)
                                                        @php
                                                            $metodeDetailId = $detailId . '-metode-' . $mIdx;
                                                        @endphp
                                                        <div class="mb-3">
                                                            <div class="d-flex align-items-center justify-content-between bg-white border rounded px-3 py-2 mb-0">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <span class="fw-bold text-dark">{{ strtoupper($metode->jenis_nama) }}</span>
                                                                    @if ($metode->bobot > 0)
                                                                        <span class="badge bg-warning bg-opacity-20 text-warning-emphasis border border-warning border-opacity-25 small">Bobot: {{ $metode->bobot }}%</span>
                                                                    @endif
                                                                    <span class="fw-bold text-primary ms-2">Nilai: {{ number_format((float)$metode->nilai, 2) }}</span>
                                                                </div>
                                                                @if ($metode->soal_count > 0)
                                                                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-0 toggle-soal-btn d-inline-flex align-items-center gap-1 shadow-none"
                                                                        type="button"
                                                                        data-bs-toggle="collapse"
                                                                        data-bs-target="#{{ $metodeDetailId }}"
                                                                        aria-expanded="false">
                                                                        <i class="mdi mdi-format-list-bulleted small"></i>
                                                                        <span class="small">{{ $metode->soal_count }} Detail</span>
                                                                        <i class="mdi mdi-chevron-down ms-1 small"></i>
                                                                    </button>
                                                                @endif
                                                            </div>

                                                            @if ($metode->soal_count > 0)
                                                                <div class="collapse" id="{{ $metodeDetailId }}">
                                                                    <div class="table-responsive border rounded-bottom border-top-0">
                                                                        <table class="table table-sm table-bordered align-middle mb-0 small">
                                                                            <thead class="table-light">
                                                                                <tr class="text-muted">
                                                                                    <th class="text-center" style="width: 45px;">No</th>
                                                                                    <th>Nama Soal / Instrumen</th>
                                                                                    <th class="text-center" style="width: 110px;">CPMK</th>
                                                                                    <th class="text-center" style="width: 100px;">Bobot (%)</th>
                                                                                    <th class="text-center" style="width: 100px;">Nilai</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @forelse ($metode->soals as $soal)
                                                                                    @php
                                                                                        $cpmkLabel = $soal->cpmk_kode ?? null;
                                                                                    @endphp
                                                                                    <tr>
                                                                                        <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                                                                        <td>
                                                                                            <span class="fw-semibold text-dark">{{ $soal->soal ?: 'Soal #1' }}</span>
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            @if (!empty($cpmkLabel))
                                                                                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">{{ $cpmkLabel }}</span>
                                                                                            @else
                                                                                                <span class="text-muted">-</span>
                                                                                            @endif
                                                                                        </td>
                                                                                        <td class="text-center fw-semibold text-muted">
                                                                                            {{ $soal->BobotSoal !== null ? $soal->BobotSoal . '%' : '-' }}
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            <span class="fw-bold text-dark">{{ is_numeric($soal->nilaiSoal ?? $soal->Nilai) ? number_format((float)($soal->nilaiSoal ?? $soal->Nilai), 2) : ($soal->nilaiSoal ?? $soal->Nilai ?? '0') }}</span>
                                                                                        </td>
                                                                                    </tr>
                                                                                @empty
                                                                                    <tr>
                                                                                        <td colspan="5" class="text-center text-muted py-2">Tidak ada rincian nilai.</td>
                                                                                    </tr>
                                                                                @endforelse
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

            @if ($mutus->hasPages())
                <div class="card-footer bg-white border-top py-2 px-3">
                    {{ $mutus->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('filterFormTS');
                if (!form) return;

                // Auto submit on dropdown select change
                const dropdowns = form.querySelectorAll('.filter-auto-submit');
                dropdowns.forEach(select => {
                    select.addEventListener('change', function() {
                        form.submit();
                    });
                });

                // Instant typing search (DOM instant filter + 600ms debounced server submit)
                const searchInput = form.querySelector('.instant-search');
                const tableRows = document.querySelectorAll('table tbody tr');

                if (searchInput) {
                    let timer = null;

                    // Restore focus and cursor position after page reload if search value exists
                    const val = searchInput.value;
                    if (val) {
                        searchInput.focus();
                        searchInput.setSelectionRange(val.length, val.length);
                    }

                    searchInput.addEventListener('input', function() {
                        const filterText = searchInput.value.toLowerCase().trim();

                        // 1. Filter rows in current table instantly (0ms UI feedback)
                        tableRows.forEach(row => {
                            const text = row.textContent.toLowerCase();
                            if (!filterText || text.includes(filterText)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });

                        // 2. Submit form after 600ms pause in typing for full DB search
                        clearTimeout(timer);
                        timer = setTimeout(function() {
                            form.submit();
                        }, 600);
                    });
                }
            });
        </script>
    @endpush
@endsection
