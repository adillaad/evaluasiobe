@extends(auth()->user()->otoritas->otoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')
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
            'Penjamin Mutu Universitas' => ['prefix' => 'penjamin-mutu.universitas.'],
            'Penjamin Mutu Fakultas' => ['prefix' => 'penjamin-mutu.fakultas.'],
            'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
            'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
        ];
        $userOtoritas = auth()->user()->otoritas->otoritas;
        $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
    @endphp

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
        @if ($isAptikom)
            /* ── APTIKOM Theme (Sky Blue Accent) ── */
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
            .badge-cpmk {
                color: #0284c7 !important;
                background-color: #f0f9ff !important;
                border: 1px solid #bae6fd !important;
            }
            .badge-bobot {
                background-color: #e0f2fe !important;
                color: #0369a1 !important;
            }
            .smt-nav-pills .nav-link.active {
                background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
                color: #ffffff !important;
                border-color: #0284c7 !important;
                box-shadow: 0 4px 12px rgba(14, 165, 233, 0.28) !important;
            }
            .text-primary {
                color: #0284c7 !important;
            }
        @else
            /* ── NON-APTIKOM Theme (Warm Amber/Gold Accent) ── */
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
            .badge-cpmk {
                color: #b45309 !important;
                background-color: #fffbe6 !important;
                border: 1px solid #fde68a !important;
            }
            .badge-bobot {
                background-color: #fef3c7 !important;
                color: #92400e !important;
            }
            .smt-nav-pills .nav-link.active {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
                color: #ffffff !important;
                border-color: #d97706 !important;
                box-shadow: 0 4px 12px rgba(217, 119, 6, 0.28) !important;
            }
            .text-primary {
                color: #d97706 !important;
            }
        @endif
        .table-pemetaan {
            width: 100% !important;
            table-layout: fixed !important;
            border-collapse: collapse !important;
        }
        .table-pemetaan th {
            background-color: #f8fafc !important;
            color: #334155 !important;
            font-weight: 600 !important;
            font-size: 13.5px !important;
            padding: 12px 14px !important;
            vertical-align: middle !important;
            border-bottom: 2px solid #cbd5e1 !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table-pemetaan td {
            vertical-align: middle !important;
            line-height: 1.6 !important;
            padding: 12px 14px !important;
            font-size: 13.5px !important;
            color: #1e293b !important;
            white-space: normal !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
        }
        .table-pemetaan tbody tr:hover {
            background-color: rgba(2, 132, 199, 0.04) !important;
        }
        .tr-mk-even, .tr-mk-even > td {
            background-color: #ffffff !important;
        }
        .tr-mk-odd, .tr-mk-odd > td {
            background-color: #f1f5f9 !important;
        }
        .badge-cpmk {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: 600;
            color: #0284c7;
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 6px;
        }
        .badge-bobot {
            background-color: #e0f2fe;
            color: #0369a1;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 12px;
        }
        .smt-tabs-wrapper {
            background: #ffffff;
            border-radius: 12px;
            padding: 14px 18px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            border: 1px solid #e2e8f0;
        }
        .smt-nav-pills {
            gap: 8px;
        }
        .smt-nav-pills .nav-link {
            color: #475569 !important;
            background-color: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 8px 16px !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            transition: all 0.2s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            white-space: nowrap !important;
        }
        .smt-nav-pills .nav-link:hover {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
        }
        .smt-nav-pills .nav-link.active {
            background-color: #0284c7 !important;
            color: #ffffff !important;
            border-color: #0284c7 !important;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2) !important;
        }
        .badge-mk-count {
            background-color: #e2e8f0;
            color: #475569;
            font-size: 11px;
            border-radius: 6px;
            padding: 2px 6px;
            margin-left: 6px;
        }
        .smt-nav-pills .nav-link.active .badge-mk-count {
            background-color: rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
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
        /* Custom Hover Effects for Action Buttons (Outline default, Filled on hover) */
        .btn-outline-primary {
            color: #0d6efd !important;
            background-color: #ffffff !important;
            border-color: #0d6efd !important;
            transition: all 0.2s ease-in-out !important;
        }
        .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary:active {
            color: #ffffff !important;
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
        }
        .btn-outline-danger {
            color: #dc3545 !important;
            background-color: #ffffff !important;
            border-color: #dc3545 !important;
            transition: all 0.2s ease-in-out !important;
        }
        .btn-outline-danger:hover, .btn-outline-danger:focus, .btn-outline-danger:active {
            color: #ffffff !important;
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
        }
    </style>

    <div class="container-fluid">
        @if(isset($kurikulums))
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-body pb-0">
                    <x-filter-form :showKurikulum="true" :kurikulums="$kurikulums" />
                </div>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div>
                        <h4 class="card-title mb-1 me-auto">Pemetaan Mata Kuliah ke CPMK (MK - CPMK)</h4>
                        <p class="text-muted small mb-0">Kelola pemetaan dan persentase bobot kontribusi Mata Kuliah terhadap CPMK per Semester.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2 ms-auto">
                        {{-- Toggle Button Group (Tabel vs Tree) --}}
                        <div class="btn-group" role="group" aria-label="Tampilan Mode">
                            <button type="button" id="btnViewTable" onclick="switchViewMode('table')" class="btn btn-outline-primary active">
                                <i class="mdi mdi-view-grid me-1"></i> Tabel
                            </button>
                            <button type="button" id="btnViewTree" onclick="switchViewMode('tree')" class="btn btn-outline-primary">
                                <i class="mdi mdi-file-tree me-1"></i> Tree
                            </button>
                        </div>
                        @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                            <button class="btn btn-primary btn-icon-text" type="button" data-bs-toggle="collapse" data-bs-target="#formTambahMKCPMK" aria-expanded="false" aria-controls="formTambahMKCPMK">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="me-1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                <span>Tambah Pemetaan MK - CPMK</span>
                            </button>
                        @endif
                    </div>
                </div>

                @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                    <div class="collapse mb-4" id="formTambahMKCPMK">
                        <div class="card card-body border shadow-sm bg-light">
                            <h5 class="fw-bold mb-3 text-primary">Form Tambah Pemetaan MK ke CPMK & Bobot</h5>
                            <form action="{{ route($currentPrefix . 'cpl-cpmk.mk-cpmk-store') }}" method="POST">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="mk_kode" class="form-label fw-bold">Pilih Mata Kuliah:</label>
                                    <select class="form-control" id="mk_kode" name="mk_kode" required>
                                        <option value="" selected disabled>-- Pilih Mata Kuliah --</option>
                                        @if(isset($mks))
                                            @foreach ($mks as $mk)
                                                <option value="{{ $mk->kode }}">{{ $mk->kode }} - {{ $mk->nama }} (Semester {{ $mk->semester ?? '-' }})</option>  
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="cpmk_id" class="form-label fw-bold">Pilih CPMK & Tentukan Bobot Kontribusi (%):</label>
                                    <div id="cpmk_container" class="border rounded p-3 bg-white" style="max-height: 280px; overflow-y: auto;">
                                        <span class="text-muted small fst-italic">Silakan pilih Mata Kuliah terlebih dahulu untuk memuat daftar CPMK...</span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-3">
                                    <button type="button" class="btn btn-secondary" data-bs-toggle="collapse" data-bs-target="#formTambahMKCPMK">Batal</button>
                                    <button type="submit" class="btn btn-primary px-4">Simpan Pemetaan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- Semester Tabs Navigation --}}
                <div class="smt-tabs-wrapper mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                        <span class="fw-bold text-secondary text-uppercase small" style="letter-spacing: 0.5px;">
                            <i class="mdi mdi-filter-variant me-1"></i> Pilih Semester:
                        </span>
                        <span class="text-muted small">Klik tab Semester untuk memfilter pemetaan MK-CPMK</span>
                    </div>
                    <ul class="nav nav-pills smt-nav-pills overflow-auto flex-nowrap pb-1" id="smtTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-all-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#tab-all" data-target="#tab-all" type="button" role="tab" aria-controls="tab-all" aria-selected="true">
                                <i class="mdi mdi-grid me-1"></i> Semua Semester
                                <span class="badge-mk-count">{{ $mks->count() }}</span>
                            </button>
                        </li>
                        @if(isset($semesters))
                            @foreach ($semesters as $s)
                                @php
                                    $mkInSmt = $mks->where('semester', $s->semester);
                                @endphp
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-smt-{{ $s->semester }}-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#tab-smt-{{ $s->semester }}" data-target="#tab-smt-{{ $s->semester }}" type="button" role="tab" aria-controls="tab-smt-{{ $s->semester }}" aria-selected="false">
                                        Semester {{ $s->semester }}
                                        <span class="badge-mk-count">{{ $mkInSmt->count() }}</span>
                                    </button>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>

                {{-- Tab Content Panes --}}
                <div id="viewTableWrapper">
                    <div class="tab-content" id="smtTabContent">
                        
                        {{-- TAB ALL SEMESTERS --}}
                        <div class="tab-pane fade show active" id="tab-all" role="tabpanel" aria-labelledby="tab-all-tab">
                            @include('penjamin-mutu.cpl-cpmk.partials._table_mk_cpmk', ['mksList' => $mks])
                        </div>

                        {{-- TAB INDIVIDUAL SEMESTER --}}
                        @if(isset($semesters))
                            @foreach ($semesters as $s)
                                @php
                                    $mksSemester = $mks->where('semester', $s->semester);
                                @endphp
                                <div class="tab-pane fade" id="tab-smt-{{ $s->semester }}" role="tabpanel" aria-labelledby="tab-smt-{{ $s->semester }}-tab">
                                    @include('penjamin-mutu.cpl-cpmk.partials._table_mk_cpmk', ['mksList' => $mksSemester])
                                </div>
                            @endforeach
                        @endif

                    </div>
                </div>

                {{-- MODE 2: TREE VIEW --}}
                <div id="viewTreeWrapper" style="display: none;">
                    <div class="tree-container">
                        @forelse ($mks as $mkIndex => $mk)
                            @php
                                $totalBobotTree = $mk->cpmks->sum(function($c) { return (float)($c->pivot->bobot ?? 0); });
                            @endphp
                            <div class="tree-mk-card mb-3" data-semester="{{ $mk->semester ?? 0 }}">
                                {{-- Header Card MK --}}
                                <div class="p-3 d-flex align-items-center justify-content-between cursor-pointer" 
                                     data-bs-toggle="collapse" 
                                     data-bs-target="#collapseTreeMK_{{ $mkIndex }}"
                                     aria-expanded="false"
                                     onclick="toggleChevron(this)">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="mdi mdi-chevron-down rotate-chevron fs-5 text-muted ms-1"></i>
                                        <div class="bg-info text-white rounded p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                            <i class="mdi mdi-file-document-outline fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-6" style="font-size: 14.5px !important;">
                                                <span class="text-primary me-2">{{ $mk->kode }}</span>
                                                <span>{{ $mk->nama }}</span>
                                            </div>
                                            <div class="text-muted small" style="font-size: 12px;">
                                                Mata Kuliah &bull; Semester {{ $mk->semester ?? '-' }} &bull; {{ ($mk->bobot_teori ?? 0) + ($mk->bobot_praktikum ?? 0) }} SKS &bull; {{ $mk->cpmks->count() }} CPMK &bull; <span class="text-success fw-semibold">Total bobot {{ (float)$totalBobotTree }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                     <div class="d-flex align-items-center gap-2">
                                         @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                                             <button type="button" class="btn btn-outline-success btn-sm p-1 px-2 text-success border-success" 
                                                 type="button" 
                                                 data-bs-toggle="collapse" 
                                                 data-bs-target="#formTambahMKCPMK" 
                                                 title="Tambah Pemetaan">
                                                 <i class="mdi mdi-plus fs-6"></i>
                                             </button>
                                         @endif
                                     </div>
                                </div>

                                {{-- Collapsible Child Body (CPMK List) --}}
                                <div class="collapse show" id="collapseTreeMK_{{ $mkIndex }}">
                                    <div class="pt-0 ps-5 pe-3 pb-3">
                                        <div class="border-start border-2 border-primary border-opacity-25 ps-3 my-2">
                                            @forelse ($mk->cpmks as $cpmk)
                                                @php
                                                    $bCpmkVal = (float)($cpmk->pivot->bobot ?? 0);
                                                @endphp
                                                <div class="tree-cpmk-card mb-2 p-2 px-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="bg-success text-white rounded p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                                <i class="mdi mdi-format-list-bulleted fs-6"></i>
                                                            </div>
                                                            <div>
                                                                <div class="fw-semibold text-dark" style="font-size: 13.5px !important;">
                                                                    <span class="text-primary fw-bold me-2">{{ $cpmk->kode }}</span>
                                                                    <span>{{ $cpmk->judul }}</span>
                                                                </div>
                                                                <div class="text-muted small" style="font-size: 11.5px;">
                                                                    CPMK &bull; CPL {{ $cpmk->cpl->kode ?? '-' }} &bull; Bobot {{ (float)$bCpmkVal }}%
                                                                </div>
                                                            </div>
                                                        </div>
                                                         @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                                                             <div class="d-flex align-items-center gap-1">
                                                                 <button type="button" class="btn btn-outline-primary btn-sm edit-single-cpmk-btn p-1 px-2" 
                                                                     data-mk-kode="{{ $mk->kode }}" 
                                                                     data-mk-nama="{{ $mk->nama }}" 
                                                                     data-cpmk-id="{{ $cpmk->id }}"
                                                                     data-bobot="{{ (float)$bCpmkVal }}"
                                                                     data-bs-toggle="modal" 
                                                                     data-bs-target="#editSingleCpmkModal"
                                                                     title="Edit CPMK {{ $cpmk->kode }}">
                                                                     <i class="mdi mdi-square-edit-outline fs-6"></i>
                                                                 </button>
                                                                 <form action="{{ route(($currentPrefix ?? 'penjamin-mutu.program-studi.') . 'cpl-cpmk.mk-cpmk-destroy', ['mk_kode' => $mk->kode, 'cpmk_id' => $cpmk->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus CPMK {{ $cpmk->kode }} dari pemetaan MK {{ $mk->kode }}?');" class="d-inline">
                                                                     @csrf
                                                                     @method('DELETE')
                                                                     <button type="submit" class="btn btn-outline-danger btn-sm p-1 px-2" title="Hapus CPMK {{ $cpmk->kode }}">
                                                                         <i class="mdi mdi-delete-outline fs-6"></i>
                                                                     </button>
                                                                 </form>
                                                             </div>
                                                         @endif
                                                     </div>
                                                 </div>
                                            @empty
                                                <div class="text-muted small fst-italic py-2">
                                                    - Belum ada CPMK yang terpetakan untuk Mata Kuliah ini -
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">Tidak ada data Mata Kuliah.</div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL EDIT PEMETAAN MK-CPMK (PER MATA KULIAH) --}}
    @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
        <div class="modal fade" id="editMkCpmkModal" tabindex="-1" aria-labelledby="editMkCpmkModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="editMkCpmkForm" action="" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title fw-bold" id="editMkCpmkModalLabel">Kelola Pemetaan CPMK & Bobot</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="p-3 bg-light rounded mb-3 border border-primary border-opacity-25">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="text-muted small d-block">Mata Kuliah:</span>
                                        <h6 class="fw-bold text-primary mb-0" id="edit_mk_title">-</h6>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="px-3 py-1 rounded border bg-white text-center">
                                            <span class="text-muted small d-block" style="font-size: 11px;">Total Bobot</span>
                                            <span class="fw-bold fs-6" id="modal_total_bobot_badge">0%</span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-success fw-bold px-3" id="btn_add_cpmk_row">
                                            <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah CPMK
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="modal_bobot_alert" class="alert alert-warning py-2 px-3 small d-none mb-3">
                                <i class="mdi mdi-alert-circle-outline me-1"></i> <span id="modal_bobot_alert_msg">Total bobot CPMK harus bernilai 100%.</span>
                            </div>

                            <label class="fw-bold form-label mb-2">Daftar CPMK & Bobot Kontribusi (%):</label>
                            
                            {{-- Container for CPMK rows --}}
                            <div id="modal_cpmk_rows_container" class="d-flex flex-column gap-2" style="max-height: 400px; overflow-y: auto;">
                                <div class="text-center text-muted py-3">Loading data CPMK...</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" id="edit_mk_submit_btn" class="btn btn-primary px-4 fw-semibold">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL EDIT PER-BARIS CPMK (UNTUK TREE VIEW) --}}
        <div class="modal fade" id="editSingleCpmkModal" tabindex="-1" aria-labelledby="editSingleCpmkModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="editSingleCpmkForm" action="" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title fw-bold" id="editSingleCpmkModalLabel">Edit CPMK & Bobot</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="fw-bold form-label">Mata Kuliah:</label>
                                <div class="form-control bg-light fw-semibold" id="edit_single_mk_title">-</div>
                            </div>

                            <div class="mb-3">
                                <label for="edit_single_cpmk_select" class="fw-bold form-label">Pilih CPMK:</label>
                                <select name="cpmk_id" id="edit_single_cpmk_select" class="form-select pe-5" style="padding-right: 2.5rem !important;" required>
                                    <option value="" disabled>-- Pilih CPMK --</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="edit_single_bobot_input" class="fw-bold form-label">Bobot MK-CPMK (%):</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-semibold">Bobot</span>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control" name="bobot" id="edit_single_bobot_input" placeholder="Opsional (0)">
                                    <span class="input-group-text bg-light fw-bold">%</span>
                                </div>
                                <span class="text-muted small">Kosongkan/0 jika bernilai setara dengan CPMK lainnya.</span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary px-4 fw-semibold">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script type="text/javascript">
    function filterTreeBySemester() {
        const activeTab = $('#smtTab .nav-link.active');
        if (!activeTab.length) return;

        const tabId = activeTab.attr('id') || '';
        let selectedSemester = 'all';
        if (tabId.startsWith('tab-smt-')) {
            selectedSemester = tabId.replace('tab-smt-', '').replace('-tab', '');
        }

        let visibleCount = 0;
        $('.tree-mk-card').each(function() {
            const cardSmt = String($(this).attr('data-semester'));
            if (selectedSemester === 'all' || cardSmt === selectedSemester) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });

        $('#treeEmptySemesterMsg').remove();
        if (visibleCount === 0) {
            $('.tree-container').append('<div id="treeEmptySemesterMsg" class="text-center text-muted py-4">Tidak ada data Mata Kuliah pada semester yang dipilih.</div>');
        }
    }

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

            filterTreeBySemester();
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

    document.addEventListener("DOMContentLoaded", function() {
        $('#smtTab button, #smtTab a').on('click shown.bs.tab', function (e) {
            e.preventDefault();
            $(this).tab('show');
            filterTreeBySemester();
        });
        filterTreeBySemester();

        const otoritas = "{{ $userOtoritas }}";
        const routePrefixStr = "{{ $currentPrefix }}";

        window.currentMkCpmks = {
            @foreach ($mks as $mk)
                '{{ $mk->kode }}': {
                    @foreach ($mk->cpmks as $c)
                        '{{ $c->id }}': {{ (float)($c->pivot->bobot ?? 0) }},
                    @endforeach
                },
            @endforeach
        };

        function loadCpmkOptions(mkKode, containerSelector, isEdit = false) {
            let urlget = '';
            if(otoritas === "Kepala Program Studi"){
                urlget = `/kepala-program-studi/cpl-cpmk/get-cpmk-by-mk/${mkKode}`;
            } else if (otoritas === 'Penjamin Mutu Program Studi'){
                urlget = `/penjamin-mutu/program-studi/cpl-cpmk/get-cpmk-by-mk/${mkKode}`;
            }

            if(mkKode && urlget){
                $.ajax({
                    url: urlget,
                    method: 'GET',
                    success: function(data) {
                        let cpmkOptions = '';
                        if (data.cpmks && data.cpmks.length > 0) {
                            data.cpmks.forEach(cpmk => {
                                let isChecked = false;
                                let bobotVal = 0;

                                if (isEdit && window.currentMkCpmks && window.currentMkCpmks[cpmk.id]) {
                                    isChecked = true;
                                    bobotVal = window.currentMkCpmks[cpmk.id];
                                }

                                cpmkOptions += `
                                <div class="row align-items-center mb-2 p-2 border-bottom">
                                    <div class="col-md-8">
                                        <div class="form-check m-0">
                                            <input class="form-check-input" type="checkbox" name="cpmk_ids[]" value="${cpmk.id}" id="${isEdit ? 'edit_' : ''}cpmk_${cpmk.id}" ${isChecked ? 'checked' : ''} style="cursor: pointer;">
                                            <label class="form-check-label ms-1" for="${isEdit ? 'edit_' : ''}cpmk_${cpmk.id}" style="cursor: pointer;">
                                                <strong>${cpmk.kode}</strong> - ${cpmk.judul}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Bobot (%)</span>
                                            <input type="number" step="0.1" min="0" max="100" class="form-control" name="bobot[${cpmk.id}]" placeholder="0" value="${bobotVal}">
                                        </div>
                                    </div>
                                </div>`;
                            });
                        } else {
                            cpmkOptions = `<label class="text-muted fst-italic p-2">Tidak ada data CPMK untuk MK yang dipilih.</label>`;
                        }
                        $(containerSelector).html(cpmkOptions);
                    },
                    error: function(xhr, status, error) {
                        alert('Gagal memuat data CPMK.');
                    }
                });
            } else {
                $(containerSelector).html('');
            }
        }

        var mkElement = document.getElementById("mk_kode");
        if (mkElement) {
            mkElement.addEventListener("change", function() {
                loadCpmkOptions(this.value, '#cpmk_container', false);
            });
        }

        // Store all available CPMK list for current MK
        let availableCpmkList = [];

        function renderModalCpmkRow(cpmkId = '', bobotVal = '') {
            let optionsHtml = '<option value="" disabled selected>-- Pilih CPMK --</option>';
            if (availableCpmkList && availableCpmkList.length > 0) {
                availableCpmkList.forEach(c => {
                    const isSelected = (c.id == cpmkId) ? 'selected' : '';
                    optionsHtml += `<option value="${c.id}" ${isSelected}>${c.kode} - ${c.judul}</option>`;
                });
            } else {
                optionsHtml = '<option value="" disabled>Tidak ada data CPMK.</option>';
            }

            const bDisplayVal = (bobotVal !== '' && bobotVal !== null && parseFloat(bobotVal) > 0) ? parseFloat(bobotVal) : '';
            const nameAttr = cpmkId ? `name="bobot[${cpmkId}]"` : 'name="bobot_row[]"';

            return `
                <div class="row align-items-center g-2 modal-cpmk-item border rounded p-2 bg-white">
                    <div class="col-md-7">
                        <select name="cpmk_ids[]" class="form-select pe-5 modal-cpmk-select" style="padding-right: 2.5rem !important;" required>
                            ${optionsHtml}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light small">Bobot</span>
                            <input type="number" step="0.01" min="0" max="100" ${nameAttr} class="form-control modal-bobot-input" placeholder="Opsional (0)" value="${bDisplayVal}">
                            <span class="input-group-text bg-light fw-bold">%</span>
                        </div>
                    </div>
                    <div class="col-md-1 text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-cpmk-row-btn p-1" title="Hapus pemetaan ini">
                            <i class="mdi mdi-delete-outline fs-5"></i>
                        </button>
                    </div>
                </div>
            `;
        }

        // Dynamic update name attribute on select change
        $(document).on('change', '.modal-cpmk-select', function() {
            const selectedCpmkId = $(this).val();
            const inputBobot = $(this).closest('.modal-cpmk-item').find('.modal-bobot-input');
            if (selectedCpmkId) {
                inputBobot.attr('name', `bobot[${selectedCpmkId}]`);
            }
        });

        function recalculateTotalBobot() {
            let total = 0;
            let hasAnyFilled = false;
            let allFilledHasValue = true;
            const rowItems = $('#modal_cpmk_rows_container .modal-cpmk-item');

            rowItems.find('.modal-bobot-input').each(function() {
                const rawVal = $(this).val();
                if (rawVal !== null && rawVal !== undefined && rawVal.trim() !== '') {
                    hasAnyFilled = true;
                    const val = parseFloat(rawVal) || 0;
                    total += val;
                } else {
                    allFilledHasValue = false;
                }
            });

            total = Math.round(total * 100) / 100;
            const submitBtn = $('#edit_mk_submit_btn');

            if (!hasAnyFilled) {
                // Scenario A: User didn't fill any bobot input => Default weighting fallback mode
                $('#modal_total_bobot_badge').text('Default').removeClass('text-danger text-success').addClass('text-info');
                $('#modal_bobot_alert').addClass('d-none');
                submitBtn.prop('disabled', false);
                return { isValid: true, total: 0, hasAnyFilled: false };
            }

            // Scenario B: User filled in at least 1 bobot input
            $('#modal_total_bobot_badge').text(`${total}%`);

            let isTotalValid = Math.abs(total - 100) < 0.01;
            let isValid = isTotalValid && allFilledHasValue;

            if (isValid) {
                $('#modal_total_bobot_badge').removeClass('text-danger text-warning text-info').addClass('text-success');
                $('#modal_bobot_alert').addClass('d-none');
                submitBtn.prop('disabled', false);
            } else {
                $('#modal_total_bobot_badge').removeClass('text-success text-info').addClass('text-danger');
                if (!allFilledHasValue) {
                    $('#modal_bobot_alert_msg').text(`Setiap CPMK yang terpetakan harus diisi bobotnya jika menggunakan pembobotan manual.`);
                } else {
                    $('#modal_bobot_alert_msg').text(`Total bobot CPMK harus tepat 100%. Total saat ini: ${total}%.`);
                }
                $('#modal_bobot_alert').removeClass('d-none');
                submitBtn.prop('disabled', true);
            }

            return { isValid: isValid, total: total, hasAnyFilled: true };
        }

        // Handle input changes on bobot
        $(document).on('input change keyup', '.modal-bobot-input', function() {
            recalculateTotalBobot();
        });

        // Handle Add CPMK Row button in modal
        $(document).on('click', '#btn_add_cpmk_row', function() {
            $('#modal_cpmk_rows_container').append(renderModalCpmkRow());
            recalculateTotalBobot();
        });

        // Handle Remove CPMK Row button in modal
        $(document).on('click', '.remove-cpmk-row-btn', function() {
            $(this).closest('.modal-cpmk-item').remove();
            if ($('#modal_cpmk_rows_container .modal-cpmk-item').length === 0) {
                $('#modal_cpmk_rows_container').html('<div class="text-muted text-center py-3 fst-italic">Belum ada CPMK terpetakan. Klik "Tambah Pemetaan CPMK" untuk menambahkan.</div>');
            }
            recalculateTotalBobot();
        });

        // Handle Edit Button Click per MK
        $(document).on('click', '.edit-mk-cpmk-btn', function() {
            const mkKode = $(this).data('mk-kode');
            const mkNama = $(this).data('mk-nama');

            $('#edit_mk_title').text(`${mkKode} - ${mkNama}`);
            $('#modal_cpmk_rows_container').html('<div class="text-center text-muted py-3">Loading data CPMK...</div>');

            let updateUrl = '';
            if (otoritas === "Kepala Program Studi") {
                updateUrl = `/kepala-program-studi/cpl-cpmk/update-mk-cpmk/${mkKode}`;
            } else if (otoritas === 'Penjamin Mutu Program Studi') {
                updateUrl = `/penjamin-mutu/program-studi/cpl-cpmk/update-mk-cpmk/${mkKode}`;
            }
            $('#editMkCpmkForm').attr('action', updateUrl);

            // Fetch available CPMKs for this MK
            let urlget = '';
            if (otoritas === "Kepala Program Studi") {
                urlget = `/kepala-program-studi/cpl-cpmk/get-cpmk-by-mk/${mkKode}`;
            } else if (otoritas === 'Penjamin Mutu Program Studi') {
                urlget = `/penjamin-mutu/program-studi/cpl-cpmk/get-cpmk-by-mk/${mkKode}`;
            }

            if (mkKode && urlget) {
                $.ajax({
                    url: urlget,
                    method: 'GET',
                    success: function(data) {
                        availableCpmkList = data.cpmks || [];

                        // Map currently mapped CPMKs and bobot for this MK
                        let currentMapped = window.currentMkCpmks[mkKode] || {};
                        let mappedIds = Object.keys(currentMapped);

                        let rowsHtml = '';
                        if (mappedIds.length > 0) {
                            mappedIds.forEach(id => {
                                rowsHtml += renderModalCpmkRow(id, currentMapped[id]);
                            });
                        } else {
                            // If none mapped yet, show 1 empty row
                            rowsHtml = renderModalCpmkRow();
                        }
                        $('#modal_cpmk_rows_container').html(rowsHtml);
                        recalculateTotalBobot();
                    },
                    error: function() {
                        $('#modal_cpmk_rows_container').html('<div class="text-danger text-center py-3">Gagal memuat data CPMK.</div>');
                    }
                });
            }
        });

        // Ensure name attributes for bobot match selected cpmk_id upon form submit & validate 100% total
        $('#editMkCpmkForm').on('submit', function(e) {
            const result = recalculateTotalBobot();
            
            const itemCount = $('#modal_cpmk_rows_container .modal-cpmk-item').length;
            if (itemCount > 0 && result.hasAnyFilled && !result.isValid) {
                e.preventDefault();
                alert(`Gagal Menyimpan: Harap pastikan setiap CPMK diisi bobotnya dan total berjumlah 100%, atau kosongkan semua bobot jika ingin menggunakan bobot default.`);
                return false;
            }

            $('#modal_cpmk_rows_container .modal-cpmk-item').each(function() {
                const selectEl = $(this).find('.modal-cpmk-select');
                const cId = selectEl.val();
                const input = $(this).find('.modal-bobot-input');
                
                if (cId) {
                    input.attr('name', `bobot[${cId}]`);
                    if (input.val() === '' || input.val() === null || input.val() === undefined) {
                        input.val('0');
                    }
                }
            });
        });

        // Handle Edit Button Click per Single CPMK Row (in Tree View)
        $(document).on('click', '.edit-single-cpmk-btn', function() {
            const mkKode = $(this).data('mk-kode');
            const mkNama = $(this).data('mk-nama');
            const currentCpmkId = $(this).data('cpmk-id');
            const currentBobot = $(this).data('bobot') || 0;

            $('#edit_single_mk_title').text(`${mkKode} - ${mkNama}`);
            $('#edit_single_bobot_input').val(currentBobot > 0 ? currentBobot : '');

            let updateUrl = '';
            if (otoritas === "Kepala Program Studi") {
                updateUrl = `/kepala-program-studi/cpl-cpmk/update-single-mk-cpmk/${mkKode}/${currentCpmkId}`;
            } else if (otoritas === 'Penjamin Mutu Program Studi') {
                updateUrl = `/penjamin-mutu/program-studi/cpl-cpmk/update-single-mk-cpmk/${mkKode}/${currentCpmkId}`;
            }
            $('#editSingleCpmkForm').attr('action', updateUrl);

            // Populate CPMK dropdown
            let urlget = '';
            if (otoritas === "Kepala Program Studi") {
                urlget = `/kepala-program-studi/cpl-cpmk/get-cpmk-by-mk/${mkKode}`;
            } else if (otoritas === 'Penjamin Mutu Program Studi') {
                urlget = `/penjamin-mutu/program-studi/cpl-cpmk/get-cpmk-by-mk/${mkKode}`;
            }

            $('#edit_single_cpmk_select').html('<option value="">Loading...</option>');
            if (mkKode && urlget) {
                $.ajax({
                    url: urlget,
                    method: 'GET',
                    success: function(data) {
                        let options = '';
                        if (data.cpmks && data.cpmks.length > 0) {
                            data.cpmks.forEach(cpmk => {
                                const selected = (cpmk.id == currentCpmkId) ? 'selected' : '';
                                options += `<option value="${cpmk.id}" ${selected}>${cpmk.kode} - ${cpmk.judul}</option>`;
                            });
                        } else {
                            options = '<option value="" disabled>Tidak ada data CPMK.</option>';
                        }
                        $('#edit_single_cpmk_select').html(options);
                    },
                    error: function() {
                        $('#edit_single_cpmk_select').html('<option value="" disabled>Gagal memuat data CPMK.</option>');
                    }
                });
            }
        });
    });
</script>
@endsection
