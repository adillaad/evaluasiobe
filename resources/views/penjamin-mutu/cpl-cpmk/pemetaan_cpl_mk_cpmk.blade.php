@php
    $selectedProdiId = request('prodi_id');
    if (!$selectedProdiId && request('kurikulum_id')) {
        $selectedProdiId = \Illuminate\Support\Facades\DB::table('kurikulums')->where('id', request('kurikulum_id'))->value('id_prodi');
    }
    if (!$selectedProdiId && auth()->check()) {
        $selectedProdiId = auth()->user()->id_prodiUser ?? (auth()->user()->prodi ? auth()->user()->prodi->id : null);
    }
    if ($selectedProdiId) {
        $isAptikom = (bool) \Illuminate\Support\Facades\DB::table('prodi')->where('id', $selectedProdiId)->value('is_aptikom');
    } else {
        $isAptikom = auth()->check() && auth()->user()->prodi ? (bool) auth()->user()->prodi->is_aptikom : true;
    }

    $routePrefix = [
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
        'Penjamin Mutu Universitas' => ['prefix' => 'penjamin-mutu.universitas.'],
        'Penjamin Mutu Fakultas' => ['prefix' => 'penjamin-mutu.fakultas.'],
        'Dosen' => ['prefix' => 'dosen.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp

@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')

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

    <style>
        .cpl-tabs-wrapper {
            background: #ffffff;
            border-radius: 12px;
            padding: 14px 18px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            border: 1px solid #e2e8f0;
        }

        .cpl-tabs-scroll-container {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            white-space: nowrap !important;
            gap: 8px !important;
            padding-bottom: 8px !important;
            -webkit-overflow-scrolling: touch;
        }
        .cpl-tabs-scroll-container::-webkit-scrollbar {
            height: 6px;
        }
        .cpl-tabs-scroll-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        .cpl-tabs-scroll-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .cpl-tabs-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .cpl-nav-pills .nav-item {
            flex-shrink: 0 !important;
        }
        .cpl-nav-pills .nav-link {
            color: #475569 !important;
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 8px 16px !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            transition: all 0.2s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            white-space: nowrap !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        }

        @if ($isAptikom)
            /* ── APTIKOM Theme (Sky Blue Accent) ── */
            .cpl-nav-pills .nav-link:hover {
                color: #0284c7 !important;
                background-color: #f0f9ff !important;
                border-color: #38bdf8 !important;
            }
            .cpl-nav-pills .nav-link.active {
                color: #ffffff !important;
                background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
                border-color: #0284c7 !important;
                box-shadow: 0 4px 12px rgba(14, 165, 233, 0.28) !important;
            }
            .bg-primary-soft {
                background-color: rgba(2, 132, 199, 0.12);
            }
            .cpl-card-header {
                background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
                border-left: 4px solid #0284c7;
                border-radius: 8px;
                padding: 16px;
            }
        @else
            /* ── NON-APTIKOM Theme (Warm Amber/Gold Accent) ── */
            .cpl-nav-pills .nav-link:hover {
                color: #d97706 !important;
                background-color: #fffbe6 !important;
                border-color: #fcd34d !important;
            }
            .cpl-nav-pills .nav-link.active {
                color: #ffffff !important;
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
                border-color: #d97706 !important;
                box-shadow: 0 4px 12px rgba(217, 119, 6, 0.28) !important;
            }
            .bg-primary-soft {
                background-color: rgba(217, 119, 6, 0.12);
            }
            .cpl-card-header {
                background: linear-gradient(135deg, #fffbe6 0%, #fef3c7 100%);
                border-left: 4px solid #d97706;
                border-radius: 8px;
                padding: 16px;
            }
        @endif

        #matrix-table {
            border-collapse: separate !important;
            border-spacing: 0 !important;
            border: 1px solid #e2e8f0 !important;
        }
        #matrix-table th {
            border-bottom: 2px solid #e2e8f0 !important;
            border-right: 1px solid rgba(0, 0, 0, 0.04) !important;
        }
        #matrix-table tbody tr {
            border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
        }
        #matrix-table tbody td {
            border-top: 1px solid rgba(0, 0, 0, 0.06) !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06) !important;
            border-right: 1px solid rgba(0, 0, 0, 0.04) !important;
        }
        .editing-active .cell-view-mode {
            display: none !important;
        }
        .editing-active .cell-edit-mode {
            display: flex !important;
        }
        .hover-bg-light:hover {
            background-color: #f8fafc;
        }
    </style>
    
    <div class="container-fluid px-4 py-3">
        @if(isset($kurikulums))
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-body pb-0">
                    <x-filter-form :showKurikulum="true" :kurikulums="$kurikulums" />
                </div>
            </div>
        @endif

        <!-- Notification Banner for Matrix Edit Mode -->
        <div id="matrix-edit-banner" class="alert alert-info border-0 shadow-sm rounded-4 mb-4 p-3 d-none align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="badge bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center">
                    <i class="mdi mdi-pencil font-18 text-white"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-primary">Mode Edit Matriks Pemetaan CPL - MK - CPMK Aktif</h6>
                    <small class="text-secondary">Centang atau hapus centang CPMK pada tiap pertemuan sel CPL dan Mata Kuliah untuk mengonfigurasi pemetaan.</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" id="btn-cancel-matrix-banner" class="btn btn-sm btn-light rounded-pill px-3">Batal</button>
                <button type="submit" form="form-matrix-edit" class="btn btn-sm btn-primary rounded-pill px-4 font-weight-bold d-inline-flex align-items-center gap-1 shadow-sm">
                    <i class="mdi mdi-content-save-check font-16"></i>
                    <span>Simpan Matriks</span>
                </button>
            </div>
        </div>

        {{-- CPL Tabs Navigation (Scrollable) --}}
        <div class="cpl-tabs-wrapper mb-3">
            <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                <span class="fw-bold text-secondary text-uppercase small" style="letter-spacing: 0.5px;">
                    <i class="mdi mdi-filter-variant me-1"></i> Pilih CPL:
                </span>
                <span class="text-muted small">Klik tab CPL untuk memfilter tampilan</span>
            </div>
            <ul class="nav nav-pills cpl-nav-pills cpl-tabs-scroll-container" id="cplTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-all-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#tab-all" data-target="#tab-all" type="button" role="tab" aria-controls="tab-all" aria-selected="true">
                        <i class="mdi mdi-grid me-1"></i> Semua CPL
                    </button>
                </li>
                @foreach ($cpls as $cpl)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-cpl-{{ $cpl->id }}-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#tab-cpl-{{ $cpl->id }}" data-target="#tab-cpl-{{ $cpl->id }}" type="button" role="tab" aria-controls="tab-cpl-{{ $cpl->id }}" aria-selected="false">
                            {{ $cpl->kode }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Tab Content Panes --}}
        <div class="tab-content" id="cplTabContent">
            
            {{-- TAB 1: ALL CPL MATRIX --}}
            <div class="tab-pane fade show active" id="tab-all" role="tabpanel" aria-labelledby="tab-all-tab">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                            <div>
                                <h4 class="card-title fw-bold mb-1">List Pemetaan CPL-MK-CPMK</h4>
                                <p class="text-muted small mb-0">Matriks keterhubungan antara Capaian Pembelajaran Lulusan (CPL), Mata Kuliah (MK), dan CPMK.</p>
                            </div>

                            @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas']))
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" id="btn-toggle-matrix-edit" class="btn btn-outline-primary rounded-3 font-weight-bold d-inline-flex align-items-center gap-1 px-3 py-2">
                                        <i class="mdi mdi-pencil-box-multiple-outline font-18"></i>
                                        <span id="btn-matrix-text">Edit Matriks</span>
                                    </button>
                                </div>
                            @endif
                        </div>

                        <form id="form-matrix-edit" action="{{ route($currentPrefix . 'cpl-cpmk.cpl-mk-cpmk-matrix-update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="kurikulum_id" value="{{ request('kurikulum_id') }}">

                            <div class="table-responsive mt-3">
                                <table class="table table-hover table-bordered align-middle mb-0" id="matrix-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="position: relative; width: 100px; height: 45px;" class="align-middle text-center">
                                                <span class="double-table-head-data-top-right fw-bold text-primary">CPL</span>
                                                <span class="double-table-head-data-bottom-left fw-bold text-dark">MK</span>
                                                <span class="diagonal-line"></span>
                                            </th>
                                            @foreach ($cpls as $cpl)
                                                <th class="text-center align-middle" style="min-width: 130px;" title="{{ $cpl->kode }}: {{ $cpl->judul }}">
                                                    <span class="badge bg-primary-soft text-primary font-13 px-2.5 py-1 rounded-2">{{ $cpl->kode }}</span>
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($mks as $mk)
                                            @php
                                                $mappedCpmkIdsForMk = $mk->cpmks->pluck('id')->toArray();
                                            @endphp
                                            <tr>
                                                <td class="bg-light fw-bold text-dark text-center align-middle font-13" title="{{ $mk->kode }}: {{ $mk->nama }}">
                                                    {{ $mk->kode }}
                                                </td>
                                                @foreach ($cpls as $cpl)
                                                    @php
                                                        $cpmksForCpl = $cpl->cpmk;
                                                        $mappedCpmksInCell = [];
                                                        if ($mk->cpl->contains('id', $cpl->id)) {
                                                            foreach ($mk->cpmks as $cpmk) {
                                                                if ($cpmk->cpl && $cpmk->cpl->id == $cpl->id) {
                                                                    $mappedCpmksInCell[] = $cpmk;
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    <td class="align-top p-2 matrix-cell">
                                                        <!-- View Mode -->
                                                        <div class="cell-view-mode">
                                                            @if (!empty($mappedCpmksInCell))
                                                                <div class="d-flex flex-wrap gap-1">
                                                                    @foreach ($mappedCpmksInCell as $cpmk)
                                                                        <span class="badge bg-primary text-white font-11 px-2 py-1 rounded-2" title="{{ $cpmk->kode }}: {{ $cpmk->judul }}">
                                                                            {{ $cpmk->kode }}
                                                                        </span>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <span class="text-muted opacity-25 font-14 text-center d-block">-</span>
                                                            @endif
                                                        </div>

                                                        <!-- Edit Mode -->
                                                        <div class="cell-edit-mode d-none flex-column gap-1">
                                                            @forelse ($cpmksForCpl as $cpmkItem)
                                                                @php
                                                                    $isAttached = in_array($cpmkItem->id, $mappedCpmkIdsForMk);
                                                                @endphp
                                                                <label class="form-check-label d-flex align-items-center gap-1.5 p-1 rounded-2 cursor-pointer border hover-bg-light" style="font-size: 11px;">
                                                                    <input type="checkbox"
                                                                           class="form-check-input matrix-checkbox m-0"
                                                                           name="matrix[{{ $mk->kode }}][{{ $cpl->id }}][]"
                                                                           value="{{ $cpmkItem->id }}"
                                                                           {{ $isAttached ? 'checked' : '' }}>
                                                                    <span class="fw-semibold text-dark">{{ $cpmkItem->kode }}</span>
                                                                </label>
                                                            @empty
                                                                <span class="text-muted font-11 fst-italic">Tidak ada CPMK</span>
                                                            @endforelse
                                                        </div>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ 1 + count($cpls) }}" class="text-center py-4 text-muted">
                                                    <i class="mdi mdi-file-table-outline font-32 d-block mb-1"></i>
                                                    Belum ada data Mata Kuliah.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Action Bar inside Form for Edit Mode -->
                            <div id="matrix-save-bar" class="d-none justify-content-end gap-2 pt-3 border-top mt-3">
                                <button type="button" id="btn-cancel-matrix-bottom" class="btn btn-light px-4 py-2 rounded-3">Batal Edit</button>
                                <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 font-weight-bold d-inline-flex align-items-center gap-2 shadow-sm">
                                    <i class="mdi mdi-content-save-check font-18"></i>
                                    <span>Simpan Perubahan Matriks</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Deskripsi Semua MK --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="card-title fw-bold text-dark mb-3"><i class="mdi mdi-information-outline me-1 text-primary"></i> Deskripsi Mata Kuliah & CPMK</h4>
                        <div class="scrollable-descriptions pe-2" style="max-height: 400px; overflow-y: auto;">
                            @foreach ($mks as $mk)
                                <div class="mb-3 p-3 bg-light rounded-3 border">
                                    <h5 class="fw-bold text-dark mb-2">{{ $mk->kode }} - {{ $mk->nama }}</h5>
                                    <p class="mb-1 text-muted font-13"><strong>CPMK Terkait:</strong></p>
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        @forelse ($mk->cpmks as $cpmk)
                                            <span class="badge bg-white text-dark border px-2 py-1.5 rounded-2 shadow-xs" title="{{ $cpmk->judul }}">
                                                <strong class="text-primary">{{ $cpmk->kode }}:</strong> {{ $cpmk->judul }}
                                            </span>
                                        @empty
                                            <span class="text-muted small fst-italic">CPMK tidak tersedia</span>
                                        @endforelse
                                    </div>
                                    <p class="mb-1 text-muted font-13"><strong>CPL Terkait:</strong></p>
                                    <div class="d-flex flex-wrap gap-1 mb-0">
                                        @forelse ($mk->cpl as $cpl)
                                            <span class="badge bg-white text-dark border px-2 py-1.5 rounded-2 shadow-xs">
                                                <strong class="text-primary">{{ $cpl->kode }}:</strong> {{ $cpl->judul }}
                                            </span>
                                        @empty
                                            <span class="text-muted small fst-italic">CPL tidak tersedia</span>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- INDIVIDUAL CPL TABS --}}
            @foreach ($cpls as $cpl)
                <div class="tab-pane fade" id="tab-cpl-{{ $cpl->id }}" role="tabpanel" aria-labelledby="tab-cpl-{{ $cpl->id }}-tab">
                    <div class="cpl-card-header mb-3">
                        <h4 class="fw-bold text-primary mb-1">{{ $cpl->kode }} - {{ $cpl->judul }}</h4>
                        <p class="text-muted small mb-0">{{ $cpl->deskripsi ?? 'Detail Pemetaan Mata Kuliah & CPMK' }}</p>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5 class="card-title text-primary mb-3"><i class="mdi mdi-book-open-page-variant me-1"></i> Mata Kuliah & CPMK Terkait {{ $cpl->kode }}</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 15%; text-align: center;">Kode MK</th>
                                            <th style="width: 40%;">Nama Mata Kuliah</th>
                                            <th style="width: 45%;">CPMK Terkait untuk {{ $cpl->kode }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $hasMappedMk = false; @endphp
                                        @foreach ($mks as $mk)
                                            @if ($mk->cpl->contains('id', $cpl->id))
                                                @php $hasMappedMk = true; @endphp
                                                <tr>
                                                    <td class="text-center fw-bold text-primary bg-light">{{ $mk->kode }}</td>
                                                    <td class="fw-semibold">{{ $mk->nama }} (Semester {{ $mk->semester }})</td>
                                                    <td>
                                                        @php $cpmkList = []; @endphp
                                                        @foreach ($mk->cpmks as $cpmk)
                                                            @if ($cpmk->cpl && $cpmk->cpl->id == $cpl->id)
                                                                @php $cpmkList[] = $cpmk; @endphp
                                                            @endif
                                                        @endforeach

                                                        @if (count($cpmkList) > 0)
                                                            @foreach ($cpmkList as $cpmk)
                                                                <div class="mb-1 p-2 bg-light rounded border">
                                                                    <strong class="text-primary">{{ $cpmk->kode }}</strong> - {{ $cpmk->judul }}
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted small">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        @if (!$hasMappedMk)
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-4">
                                                    <i class="mdi mdi-information-outline me-1 fs-5"></i> Tidak ada Mata Kuliah terkait untuk {{ $cpl->kode }}
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnToggleMatrixEdit = document.getElementById('btn-toggle-matrix-edit');
    const btnMatrixText = document.getElementById('btn-matrix-text');
    const matrixBanner = document.getElementById('matrix-edit-banner');
    const matrixSaveBar = document.getElementById('matrix-save-bar');
    const matrixTable = document.getElementById('matrix-table');

    const btnCancelBanner = document.getElementById('btn-cancel-matrix-banner');
    const btnCancelBottom = document.getElementById('btn-cancel-matrix-bottom');

    let isEditMode = false;

    function enableEditMode() {
        isEditMode = true;
        if (matrixTable) matrixTable.classList.add('editing-active');
        if (matrixBanner) {
            matrixBanner.classList.remove('d-none');
            matrixBanner.classList.add('d-flex');
        }
        if (matrixSaveBar) {
            matrixSaveBar.classList.remove('d-none');
            matrixSaveBar.classList.add('d-flex');
        }
        
        if (btnToggleMatrixEdit) {
            btnToggleMatrixEdit.classList.remove('btn-outline-primary');
            btnToggleMatrixEdit.classList.add('btn-secondary');
        }
        if (btnMatrixText) btnMatrixText.textContent = 'Batal Edit';
    }

    function disableEditMode() {
        isEditMode = false;
        if (matrixTable) matrixTable.classList.remove('editing-active');
        if (matrixBanner) {
            matrixBanner.classList.add('d-none');
            matrixBanner.classList.remove('d-flex');
        }
        if (matrixSaveBar) {
            matrixSaveBar.classList.add('d-none');
            matrixSaveBar.classList.remove('d-flex');
        }

        if (btnToggleMatrixEdit) {
            btnToggleMatrixEdit.classList.remove('btn-secondary');
            btnToggleMatrixEdit.classList.add('btn-outline-primary');
        }
        if (btnMatrixText) btnMatrixText.textContent = 'Edit Matriks';
    }

    if (btnToggleMatrixEdit) {
        btnToggleMatrixEdit.addEventListener('click', function () {
            if (isEditMode) {
                disableEditMode();
            } else {
                enableEditMode();
            }
        });
    }

    if (btnCancelBanner) btnCancelBanner.addEventListener('click', disableEditMode);
    if (btnCancelBottom) btnCancelBottom.addEventListener('click', disableEditMode);

    // Bootstrap Tab switcher for CPL Pills
    $('#cplTab button, #cplTab a').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
});
</script>
@endpush
@endsection
