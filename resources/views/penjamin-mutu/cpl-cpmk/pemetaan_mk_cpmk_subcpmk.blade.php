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

    <style>
        @if ($isAptikom)
            /* ── APTIKOM Theme (Sky Blue Accent) ── */
            .matrix-banner-style {
                background-color: #e0f2fe !important;
                border: 1px solid #bae6fd !important;
            }
            .matrix-badge-style {
                background-color: #0ea5e9 !important;
                color: #ffffff !important;
            }
            .matrix-title-style {
                color: #0284c7 !important;
            }
            .btn-primary {
                background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
                border-color: #0284c7 !important;
                color: #ffffff !important;
            }
            .btn-primary:hover, .btn-primary:focus {
                background: #0369a1 !important;
                border-color: #0369a1 !important;
                color: #ffffff !important;
            }
            .btn-outline-primary {
                color: #0284c7 !important;
                border-color: #0284c7 !important;
                background-color: #ffffff !important;
            }
            .btn-outline-primary:hover, .btn-outline-primary.active, .btn-outline-primary:focus {
                background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
                color: #ffffff !important;
                border-color: #0284c7 !important;
                box-shadow: 0 4px 12px rgba(14, 165, 233, 0.28) !important;
            }
            .code-pink {
                color: #0284c7 !important;
                font-weight: 700;
            }
            .icon-box-cyan {
                background-color: #0ea5e9 !important;
                color: #fff;
            }
            .text-primary {
                color: #0284c7 !important;
            }
        @else
            /* ── NON-APTIKOM Theme (Warm Amber/Gold Accent) ── */
            .matrix-banner-style {
                background-color: #fef3c7 !important;
                border: 1px solid #fde68a !important;
            }
            .matrix-badge-style {
                background-color: #f59e0b !important;
                color: #ffffff !important;
            }
            .matrix-title-style {
                color: #d97706 !important;
            }
            .btn-primary {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
                border-color: #d97706 !important;
                color: #ffffff !important;
            }
            .btn-primary:hover, .btn-primary:focus {
                background: #b45309 !important;
                border-color: #b45309 !important;
                color: #ffffff !important;
            }
            .btn-outline-primary {
                color: #d97706 !important;
                border-color: #d97706 !important;
                background-color: #ffffff !important;
            }
            .btn-outline-primary:hover, .btn-outline-primary.active, .btn-outline-primary:focus {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
                color: #ffffff !important;
                border-color: #d97706 !important;
                box-shadow: 0 4px 12px rgba(217, 119, 6, 0.28) !important;
            }
            .code-pink {
                color: #d97706 !important;
                font-weight: 700;
            }
            .icon-box-cyan {
                background-color: #f59e0b !important;
                color: #fff;
            }
            .text-primary {
                color: #d97706 !important;
            }
        @endif

        .icon-box-cyan {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        .icon-box-green {
            background-color: #10ac84;
            color: #fff;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }
        .rotate-chevron {
            transition: transform 0.2s ease-in-out;
            display: inline-block;
        }
        .rotate-chevron.open {
            transform: rotate(90deg);
        }
        .tree-container {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
        }
        .tree-mk-card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            background: #ffffff;
            transition: all 0.2s ease-in-out;
        }
        .tree-mk-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        }
        .tree-cpmk-card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #ffffff;
        }
        .cursor-pointer {
            cursor: pointer;
        }
    </style>

    <div class="container-fluid mb-4">
        {{-- Form Tambah Sub CPMK jika authorized --}}
        @if(in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <form action="{{ route($currentPrefix. 'cpl-cpmk.subCpmk-store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="kurikulum_id" class="fw-bold">Kurikulum :</label>
                            <select name="kurikulum_id" id="kurikulum_id" class="form-select">
                                <option value="">-- Pilih Kurikulum --</option>
                                @foreach ($kurikulums as $kurikulum)
                                    <option value="{{ $kurikulum->id }}" {{ old('kurikulum_id') == $kurikulum->id ? 'selected' : '' }}>{{ $kurikulum->tahun }}</option>    
                                @endforeach
                            </select>
                            @error('kurikulum_id')
                                <div class="alert alert-danger mt-1 py-1 small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="cpl_id" class="fw-bold">CPL :</label>
                            <select name="cpl_id" id="cpl_id" class="form-select">
                                <option value="">-- Pilih CPL --</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="cpmk_id" class="fw-bold">CPMK :</label>
                            <input type="hidden" name="cpmk_kode" id="cpmk_kode">
                            <select name="cpmk_id" id="cpmk_id" class="form-select" required>
                                <option value="">-- Pilih CPMK --</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="uraian" class="fw-bold">Uraian Sub CPMK :</label>
                            <textarea name="uraian" id="uraian" class="form-control" style="height: 100px" placeholder="Uraian Sub CPMK">{{ old('uraian') }}</textarea>
                            @error('uraian')
                                <div class="alert alert-danger mt-1 py-1 small">{{ $message }}</div>
                            @enderror
                        </div>
                
                        <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan Sub CPMK</button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Filter Form Kurikulum --}}
        @if(isset($kurikulums))
            <div class="card mb-3 shadow-sm">
                <div class="card-body py-2">
                    <x-filter-form :showKurikulum="true" :kurikulums="$kurikulums" />
                </div>
            </div>
        @endif

        <!-- Banner Informatif Mode Edit Matriks -->
        <div id="matrix-edit-banner" class="matrix-banner-style shadow-sm rounded-3 mb-3 p-3 d-none align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="badge matrix-badge-style rounded-circle p-2 d-flex align-items-center justify-content-center">
                    <i class="ti-pencil font-18 text-white"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 matrix-title-style">Mode Edit Matriks Aktif</h6>
                    <small class="text-secondary">Centang atau hapus centang Sub CPMK di bawah untuk memperbarui pemetaan ke Mata Kuliah.</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" id="btn-cancel-matrix-banner" class="btn btn-sm btn-light rounded-pill px-3">Batal</button>
                <button type="submit" form="form-matrix-edit" class="btn btn-sm btn-primary rounded-pill px-4 font-weight-bold d-inline-flex align-items-center gap-1 shadow-sm">
                    <i class="ti-check me-1"></i>
                    <span>Simpan Matriks</span>
                </button>
            </div>
        </div>

        {{-- Main Pemetaan Card --}}
        <div class="card shadow-sm">
            <div class="card-body">
                {{-- Header Title & Action Buttons --}}
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <h4 class="card-title mb-0 me-auto">List Pemetaan MK-CPMK-Sub CPMK</h4>

                    <div class="d-flex align-items-center gap-2">
                        {{-- Toggle Button Group (Tabel vs Tree) --}}
                        <div class="btn-group" role="group" aria-label="Tampilan Mode">
                            <button type="button" id="btnViewTable" onclick="switchViewMode('table')" class="btn btn-outline-primary active">
                                <i class="ti-layout-grid2 me-1"></i> Tabel
                            </button>
                            <button type="button" id="btnViewTree" onclick="switchViewMode('tree')" class="btn btn-outline-primary">
                                <i class="ti-share2 me-1"></i> Tree
                            </button>
                        </div>

                        @if(in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas']))
                            <button type="button" id="btn-toggle-matrix-edit" class="btn btn-outline-primary font-weight-bold d-inline-flex align-items-center gap-1">
                                <i class="ti-pencil-alt"></i>
                                <span id="btn-matrix-text">Edit Matriks</span>
                            </button>

                            <a href="{{ route($currentPrefix. 'cpl-cpmk.subcpmk-kelola') }}"
                                class="btn btn-warning font-weight-bold btn-icon-text" style="color: #ffffff !important;">
                                <i class="ti-settings me-1 text-white"></i>
                                <span style="color: #ffffff !important;">Kelola Sub CPMK</span>
                            </a>

                            <a href="{{ route($currentPrefix. 'cpl-cpmk.cpmk-mk-subcpmk-add') }}"
                                class="btn btn-primary btn-icon-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                <span>Tambah MK - Sub CPMK</span>
                            </a>
                        @endif
                    </div>
                </div>

                <form id="form-matrix-edit" action="{{ route($currentPrefix . 'cpl-cpmk.mk-cpmk-subcpmk-matrix-update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="kurikulum_id" value="{{ request('kurikulum_id') }}">

                    {{-- MODE 1: TABEL VIEW --}}
                    <div id="viewTableWrapper">
                        <div class="table-responsive" style="max-height: 650px; overflow-y: auto;">
                            <table class="table table-hover dataTable align-middle">
                                <thead style="position: sticky; top: 0; z-index: 10;" class="bg-light">
                                    <tr>
                                        <th style="width: 140px;">MK</th>
                                        <th style="width: 120px;">CPMK</th>
                                        <th>Deskripsi CPMK</th>
                                        <th style="width: 150px;">Sub CPMK</th>
                                        <th>Uraian Sub CPMK</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($mks as $mk)
                                        @php
                                            $mappedSubCpmkIds = $mk->sub_cpmk->pluck('id');

                                            $allCpmks = clone $mk->cpmks;
                                            foreach ($mk->sub_cpmk as $sub) {
                                                if ($sub->cpmk && !$allCpmks->contains('id', $sub->cpmk->id)) {
                                                    $allCpmks->push($sub->cpmk);
                                                }
                                            }
                                            $validCpmks = $allCpmks->unique('id')->values();

                                            $totalSubCpmkView = $validCpmks->sum(function ($cpmk) use ($mappedSubCpmkIds) {
                                                return $cpmk->subCpmks->filter(function ($subCpmk) use ($mappedSubCpmkIds) {
                                                    return $mappedSubCpmkIds->contains($subCpmk->id);
                                                })->count();
                                            });

                                            $cpmkWithoutSubCpmkView = $validCpmks->filter(function ($cpmk) use ($mappedSubCpmkIds) {
                                                return $cpmk->subCpmks->filter(function ($subCpmk) use ($mappedSubCpmkIds) {
                                                    return $mappedSubCpmkIds->contains($subCpmk->id);
                                                })->isEmpty();
                                            })->count();

                                            $rowspanMK = $totalSubCpmkView + $cpmkWithoutSubCpmkView ?: 1;
                                        @endphp
                                        <tr>
                                            <td rowspan="{{ $rowspanMK }}" class="fw-bold align-top bg-white">{{ $mk->kode }}</td>

                                            @foreach ($validCpmks as $validIndex => $cpmk)
                                                @if ($validIndex > 0) <tr> @endif
                                                @php
                                                    $validSubCpmks = $cpmk->subCpmks->filter(function ($subCpmk) use ($mappedSubCpmkIds) {
                                                        return $mappedSubCpmkIds->contains($subCpmk->id);
                                                    });
                                                    $rowspanCPMK = $validSubCpmks->count() ?: 1;
                                                @endphp

                                                <td rowspan="{{ $rowspanCPMK }}" class="align-top bg-white">{{ $cpmk->kode }}</td>
                                                <td rowspan="{{ $rowspanCPMK }}" class="align-top bg-white" style="word-wrap:break-word; white-space:normal;">{{ $cpmk->judul }}</td>

                                                {{-- View Mode: Sub CPMK columns --}}
                                                @foreach ($validSubCpmks as $indexSubCpmk => $subCpmk)
                                                    @if ($indexSubCpmk > 0) <tr> @endif
                                                    <td class="view-mode-item"><span class="code-pink">{{ $subCpmk->kode }}</span></td>
                                                    <td class="view-mode-item" style="word-wrap:break-word; white-space:normal;">{{ $subCpmk->uraian }}</td>
                                                    @if ($indexSubCpmk < $validSubCpmks->count() - 1) </tr> @endif
                                                @endforeach

                                                @if ($validSubCpmks->isEmpty())
                                                    <td colspan="2" class="text-muted italic view-mode-item">Tidak ada Sub-CPMK terkait</td>
                                                @endif

                                                {{-- Edit Mode: Checkbox selection for all Sub CPMKs of CPMK --}}
                                                <td colspan="2" class="edit-mode-item d-none bg-light p-2">
                                                    @if($cpmk->subCpmks->isNotEmpty())
                                                        <div class="d-flex flex-column gap-1">
                                                            @foreach($cpmk->subCpmks as $subCpmk)
                                                                @php
                                                                    $isMapped = $mappedSubCpmkIds->contains($subCpmk->id);
                                                                @endphp
                                                                <div class="form-check p-2 border rounded bg-white shadow-xs me-0 mb-1">
                                                                    <div class="d-flex align-items-center gap-2 w-100">
                                                                        <input class="form-check-input matrix-checkbox flex-shrink-0 mt-0" type="checkbox"
                                                                            name="matrix[{{ $mk->kode }}][]"
                                                                            value="{{ $subCpmk->id }}"
                                                                            id="sub_tbl_{{ $mk->kode }}_{{ $subCpmk->id }}"
                                                                            data-mk="{{ $mk->kode }}"
                                                                            data-sub-id="{{ $subCpmk->id }}"
                                                                            {{ $isMapped ? 'checked' : '' }}
                                                                            data-initial="{{ $isMapped ? '1' : '0' }}">
                                                                        <input type="text"
                                                                            name="kode[{{ $subCpmk->id }}]"
                                                                            value="{{ $subCpmk->kode }}"
                                                                            class="form-control form-control-sm subcpmk-kode-input fw-bold code-pink border-secondary-subtle px-2 py-1"
                                                                            style="width: 140px; flex-shrink: 0;"
                                                                            data-sub-id="{{ $subCpmk->id }}"
                                                                            data-initial-kode="{{ $subCpmk->kode }}"
                                                                            placeholder="Kode Sub CPMK">
                                                                        <span class="fw-bold text-muted flex-shrink-0 me-1">:</span>
                                                                        <input type="text"
                                                                            name="uraian[{{ $subCpmk->id }}]"
                                                                            value="{{ $subCpmk->uraian }}"
                                                                            class="form-control form-control-sm subcpmk-uraian-input border-secondary-subtle px-2 py-1"
                                                                            data-sub-id="{{ $subCpmk->id }}"
                                                                            data-initial-text="{{ $subCpmk->uraian }}"
                                                                            placeholder="Deskripsi Sub CPMK">
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="text-muted small italic">Tidak ada Sub-CPMK terdaftar pada CPMK ini</div>
                                                    @endif
                                                </td>

                                                @if ($validIndex < $validCpmks->count() - 1) </tr> @endif
                                            @endforeach

                                            @if ($validCpmks->isEmpty())
                                                <td colspan="4" class="text-muted italic">Tidak ada CPMK terkait</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- MODE 2: TREE VIEW --}}
                    <div id="viewTreeWrapper" style="display: none;">
                        <div class="tree-container">
                            @forelse ($mks as $mkIndex => $mk)
                                @php
                                    $mappedSubCpmkIds = $mk->sub_cpmk->pluck('id');

                                    $allCpmks = clone $mk->cpmks;
                                    foreach ($mk->sub_cpmk as $sub) {
                                        if ($sub->cpmk && !$allCpmks->contains('id', $sub->cpmk->id)) {
                                            $allCpmks->push($sub->cpmk);
                                        }
                                    }
                                    $validCpmks = $allCpmks->unique('id')->values();
                                @endphp
                                <div class="tree-mk-card mb-3">
                                    {{-- Header Card MK --}}
                                    <div class="p-3 d-flex align-items-center justify-content-between cursor-pointer" 
                                         data-bs-toggle="collapse" 
                                         data-bs-target="#collapseTreeMK_{{ $mkIndex }}"
                                         aria-expanded="false"
                                         onclick="toggleChevron(this)">
                                        <div class="d-flex align-items-center gap-3">
                                            <i class="ti-angle-right rotate-chevron fs-5 text-muted ms-1 me-2"></i>
                                            <span class="icon-box-cyan">
                                                <i class="ti-book"></i>
                                            </span>
                                            <div>
                                                <h5 class="mb-1 fw-bold text-dark">
                                                    <span class="code-pink me-2">{{ $mk->kode }}</span>
                                                    <span>{{ $mk->nama }}</span>
                                                </h5>
                                                <div class="text-muted small">
                                                    Mata Kuliah &bull; {{ $validCpmks->count() }} CPMK
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Collapsible Child Body (CPMK List) --}}
                                    <div class="collapse" id="collapseTreeMK_{{ $mkIndex }}">
                                        <div class="pt-0 ps-5 pe-3 pb-3">
                                            <div class="border-start border-2 border-primary border-opacity-25 ps-3 my-2">
                                                @forelse ($validCpmks as $cpmk)
                                                    @php
                                                        $validSubCpmks = $cpmk->subCpmks->filter(function ($subCpmk) use ($mappedSubCpmkIds) {
                                                            return $mappedSubCpmkIds->contains($subCpmk->id);
                                                        });
                                                    @endphp
                                                    <div class="tree-cpmk-card mb-2 p-3">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <div class="d-flex align-items-center gap-3">
                                                                <span class="icon-box-green">
                                                                    <i class="ti-list"></i>
                                                                </span>
                                                                <div>
                                                                    <h6 class="mb-1 fw-bold text-dark">
                                                                        <span class="code-pink me-2">{{ $cpmk->kode }}</span>
                                                                        <span>{{ $cpmk->judul }}</span>
                                                                    </h6>
                                                                    <div class="text-muted small">
                                                                        @if($validSubCpmks->isEmpty())
                                                                            Tidak ada Sub-CPMK terkait
                                                                        @else
                                                                            {{ $validSubCpmks->count() }} Sub-CPMK
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- View Mode: Sub CPMK nested list --}}
                                                        <div class="view-mode-item mt-2 pt-2 border-top">
                                                            @if($validSubCpmks->isNotEmpty())
                                                                <div class="small fw-bold text-muted mb-1 me-2">Sub-CPMK:</div>
                                                                @foreach ($validSubCpmks as $subCpmk)
                                                                    <div class="d-flex align-items-center justify-content-between p-2 mb-1 bg-light rounded-2">
                                                                        <div class="small">
                                                                            <span class="code-pink me-2">{{ $subCpmk->kode }}</span>
                                                                            <span class="text-dark">{{ $subCpmk->uraian }}</span>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>

                                                        {{-- Edit Mode: Checkboxes for Sub CPMK --}}
                                                        <div class="edit-mode-item d-none mt-2 pt-2 border-top bg-light p-2 rounded">
                                                            <div class="small fw-bold text-muted mb-2">Pilih & Edit Sub-CPMK:</div>
                                                            @if($cpmk->subCpmks->isNotEmpty())
                                                                @foreach($cpmk->subCpmks as $subCpmk)
                                                                    @php
                                                                        $isMapped = $mappedSubCpmkIds->contains($subCpmk->id);
                                                                    @endphp
                                                                    <div class="form-check p-2 border rounded bg-white mb-1">
                                                                        <div class="d-flex align-items-center gap-2 w-100">
                                                                            <input class="form-check-input matrix-checkbox flex-shrink-0 mt-0" type="checkbox"
                                                                                name="matrix[{{ $mk->kode }}][]"
                                                                                value="{{ $subCpmk->id }}"
                                                                                id="sub_tree_{{ $mk->kode }}_{{ $subCpmk->id }}"
                                                                                data-mk="{{ $mk->kode }}"
                                                                                data-sub-id="{{ $subCpmk->id }}"
                                                                                {{ $isMapped ? 'checked' : '' }}
                                                                                data-initial="{{ $isMapped ? '1' : '0' }}">
                                                                            <input type="text"
                                                                                name="kode[{{ $subCpmk->id }}]"
                                                                                value="{{ $subCpmk->kode }}"
                                                                                class="form-control form-control-sm subcpmk-kode-input fw-bold code-pink border-secondary-subtle px-2 py-1"
                                                                                style="width: 140px; flex-shrink: 0;"
                                                                                data-sub-id="{{ $subCpmk->id }}"
                                                                                data-initial-kode="{{ $subCpmk->kode }}"
                                                                                placeholder="Kode Sub CPMK">
                                                                            <span class="fw-bold text-muted flex-shrink-0 me-1">:</span>
                                                                            <input type="text"
                                                                                name="uraian[{{ $subCpmk->id }}]"
                                                                                value="{{ $subCpmk->uraian }}"
                                                                                class="form-control form-control-sm subcpmk-uraian-input border-secondary-subtle px-2 py-1"
                                                                                data-sub-id="{{ $subCpmk->id }}"
                                                                                data-initial-text="{{ $subCpmk->uraian }}"
                                                                                placeholder="Deskripsi Sub CPMK">
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <div class="text-muted small italic">Tidak ada Sub-CPMK terdaftar</div>
                                                            @endif
                                                        </div>

                                                    </div>
                                                @empty
                                                    <div class="p-3 text-muted small italic bg-white rounded border">
                                                        Tidak ada CPMK yang dipetakan untuk mata kuliah ini.
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 text-muted">
                                    <h5>Belum ada data Mata Kuliah</h5>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>



    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script type="text/javascript">
        let isEditMode = false;

        function switchViewMode(mode) {
            const tableWrap = document.getElementById('viewTableWrapper');
            const treeWrap = document.getElementById('viewTreeWrapper');
            const btnTable = document.getElementById('btnViewTable');
            const btnTree = document.getElementById('btnViewTree');

            if (mode === 'tree') {
                tableWrap.style.display = 'none';
                treeWrap.style.display = 'block';

                btnTable.classList.remove('btn-primary', 'text-white', 'active');
                btnTable.classList.add('btn-outline-primary');

                btnTree.classList.remove('btn-outline-primary');
                btnTree.classList.add('btn-primary', 'text-white', 'active');
            } else {
                treeWrap.style.display = 'none';
                tableWrap.style.display = 'block';

                btnTree.classList.remove('btn-primary', 'text-white', 'active');
                btnTree.classList.add('btn-outline-primary');

                btnTable.classList.remove('btn-outline-primary');
                btnTable.classList.add('btn-primary', 'text-white', 'active');
            }
        }

        function toggleChevron(element) {
            const chevron = element.querySelector('.rotate-chevron');
            if (chevron) {
                chevron.classList.toggle('open');
            }
        }

        function toggleEditMode() {
            isEditMode = !isEditMode;
            const btn = document.getElementById('btn-toggle-matrix-edit');
            const btnText = document.getElementById('btn-matrix-text');
            const banner = document.getElementById('matrix-edit-banner');
            const viewItems = document.querySelectorAll('.view-mode-item');
            const editItems = document.querySelectorAll('.edit-mode-item');

            if (isEditMode) {
                if (btnText) btnText.textContent = 'Batal Edit';
                if (btn) {
                    btn.classList.remove('btn-outline-primary');
                    btn.classList.add('btn-danger', 'text-white');
                }
                if (banner) {
                    banner.classList.remove('d-none');
                    banner.classList.add('d-flex');
                }
                viewItems.forEach(el => el.classList.add('d-none'));
                editItems.forEach(el => el.classList.remove('d-none'));
            } else {
                if (btnText) btnText.textContent = 'Edit Matriks';
                if (btn) {
                    btn.classList.remove('btn-danger', 'text-white');
                    btn.classList.add('btn-outline-primary');
                }
                if (banner) {
                    banner.classList.add('d-none');
                    banner.classList.remove('d-flex');
                }
                viewItems.forEach(el => el.classList.remove('d-none'));
                editItems.forEach(el => el.classList.add('d-none'));
                resetCheckboxes();
                checkChanges();
            }
        }

        function checkChanges() {
            let hasChanges = false;
            document.querySelectorAll('.matrix-checkbox').forEach(cb => {
                const initial = cb.dataset.initial === '1';
                if (cb.checked !== initial) {
                    hasChanges = true;
                }
            });
            document.querySelectorAll('.subcpmk-kode-input').forEach(inp => {
                if (inp.value !== inp.dataset.initialKode) {
                    hasChanges = true;
                }
            });
            document.querySelectorAll('.subcpmk-uraian-input').forEach(inp => {
                if (inp.value !== inp.dataset.initialText) {
                    hasChanges = true;
                }
            });
        }

        function resetCheckboxes() {
            document.querySelectorAll('.matrix-checkbox').forEach(cb => {
                cb.checked = cb.dataset.initial === '1';
            });
            document.querySelectorAll('.subcpmk-kode-input').forEach(inp => {
                inp.value = inp.dataset.initialKode;
            });
            document.querySelectorAll('.subcpmk-uraian-input').forEach(inp => {
                inp.value = inp.dataset.initialText;
            });
        }

        document.addEventListener("DOMContentLoaded", function () {
            const editBtn = document.getElementById('btn-toggle-matrix-edit');
            if (editBtn) {
                editBtn.addEventListener('click', toggleEditMode);
            }

            const cancelBanner = document.getElementById('btn-cancel-matrix-banner');
            if (cancelBanner) {
                cancelBanner.addEventListener('click', toggleEditMode);
            }

            $(document).on('change', '.matrix-checkbox', function() {
                const mkKode = $(this).data('mk');
                const subId = $(this).data('sub-id');
                const isChecked = this.checked;
                if (mkKode && subId) {
                    $(`.matrix-checkbox[data-mk="${mkKode}"][data-sub-id="${subId}"]`).not(this).prop('checked', isChecked);
                }
                checkChanges();
            });

            $('#form-matrix-edit').on('submit', function() {
                if ($('#viewTableWrapper').is(':visible')) {
                    $('#viewTreeWrapper').find('input, select, textarea').prop('disabled', true);
                } else {
                    $('#viewTableWrapper').find('input, select, textarea').prop('disabled', true);
                }
            });

            $(document).on('input', '.subcpmk-kode-input', function() {
                const subId = $(this).data('sub-id');
                const val = $(this).val();
                $(`.subcpmk-kode-input[data-sub-id="${subId}"]`).not(this).val(val);
                checkChanges();
            });

            $(document).on('input', '.subcpmk-uraian-input', function() {
                const subId = $(this).data('sub-id');
                const val = $(this).val();
                $(`.subcpmk-uraian-input[data-sub-id="${subId}"]`).not(this).val(val);
                checkChanges();
            });

            const kurikulumElement = document.getElementById("kurikulum_id");
            const cplSelect = document.getElementById("cpl_id");
            const cpmkSelect = document.getElementById("cpmk_id");
            const hiddenCpmkKode = document.getElementById("cpmk_kode");
            const otoritas = "{{ $userOtoritas }}";

            function getPrefixUrl() {
                if (otoritas === "Kepala Program Studi") {
                    return "/kepala-program-studi/cpl-cpmk";
                }
                return "/penjamin-mutu/program-studi/cpl-cpmk";
            }

            function fetchCplByKurikulum() {
                const kurikulumId = kurikulumElement ? kurikulumElement.value : null;
                if (cplSelect) cplSelect.innerHTML = '<option value="">-- Pilih CPL --</option>';
                if (cpmkSelect) cpmkSelect.innerHTML = '<option value="">-- Pilih CPMK --</option>';
                if (hiddenCpmkKode) hiddenCpmkKode.value = '';

                if (!kurikulumId) return;

                if (cplSelect) cplSelect.innerHTML = '<option>Loading CPL...</option>';

                $.ajax({
                    url: `${getPrefixUrl()}/get-cpl-by-kurikulum/${kurikulumId}`,
                    method: 'GET',
                    success: function (data) {
                        if (!cplSelect) return;
                        cplSelect.innerHTML = '<option value="">-- Pilih CPL --</option>';
                        if (data.cpls && data.cpls.length > 0) {
                            data.cpls.forEach(cpl => {
                                cplSelect.innerHTML += `<option value="${cpl.id}">${cpl.kode} - ${cpl.judul}</option>`;
                            });
                        } else {
                            cplSelect.innerHTML = '<option value="" disabled>Tidak ada data CPL untuk kurikulum yang dipilih.</option>';
                        }
                    },
                    error: function () {
                        if (cplSelect) cplSelect.innerHTML = '<option value="" disabled>Gagal memuat data CPL.</option>';
                    }
                });
            }

            function fetchCpmkByCpl() {
                const cplId = cplSelect ? cplSelect.value : null;
                if (cpmkSelect) cpmkSelect.innerHTML = '<option value="">-- Pilih CPMK --</option>';
                if (hiddenCpmkKode) hiddenCpmkKode.value = '';

                if (!cplId) return;

                if (cpmkSelect) cpmkSelect.innerHTML = '<option>Loading CPMK...</option>';

                $.ajax({
                    url: `${getPrefixUrl()}/get-cpmk-by-cpl/${cplId}`,
                    method: 'GET',
                    success: function (data) {
                        if (!cpmkSelect) return;
                        cpmkSelect.innerHTML = '<option value="">-- Pilih CPMK --</option>';
                        if (data.cpmks && data.cpmks.length > 0) {
                            data.cpmks.forEach(cpmk => {
                                cpmkSelect.innerHTML += `<option value="${cpmk.id}">${cpmk.kode} - ${cpmk.judul}</option>`;
                            });
                        } else {
                            cpmkSelect.innerHTML = '<option value="" disabled>Tidak ada data CPMK untuk CPL yang dipilih.</option>';
                        }
                    },
                    error: function () {
                        if (cpmkSelect) cpmkSelect.innerHTML = '<option value="" disabled>Gagal memuat data CPMK.</option>';
                    }
                });
            }

            if (kurikulumElement) {
                kurikulumElement.addEventListener("change", fetchCplByKurikulum);
            }

            if (cplSelect) {
                cplSelect.addEventListener("change", fetchCpmkByCpl);
            }

            if (cpmkSelect) {
                cpmkSelect.addEventListener('change', function () {
                    const selectedText = this.options[this.selectedIndex]?.textContent;
                    const kode = selectedText?.split(" - ")[0] ?? '';
                    if (hiddenCpmkKode) hiddenCpmkKode.value = kode;
                });
            }
        });
    </script>
@endsection
