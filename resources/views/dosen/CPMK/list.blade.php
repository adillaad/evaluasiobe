@php
    $isAptikom = auth()->check() && auth()->user()->prodi ? (bool) auth()->user()->prodi->is_aptikom : true;
    $userOtoritas = $userOtoritas ?? (auth()->user()->otoritas->otoritas ?? 'Dosen');

    $routePrefixMap = [
        'Koordinator Program Studi' => 'koordinator-program-studi.',
        'Kepala Program Studi' => 'kepala-program-studi.',
        'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.',
        'Penjamin Mutu Fakultas' => 'penjamin-mutu.fakultas.',
        'Penjamin Mutu Universitas' => 'penjamin-mutu.universitas.',
        'Wakil Dekan' => 'wakil-dekan.',
        'Wakil Rektor' => 'wakil-rektor.',
        'Dosen' => 'dosen.',
        'Admin Universitas' => 'admin-universitas.',
        'Admin' => 'admin.',
    ];
    $currentPrefix = $routePrefixMap[$userOtoritas] ?? 'dosen.';
    $canManageCpmk = in_array($userOtoritas, ['Dosen', 'Koordinator Program Studi', 'Kepala Program Studi', 'Penjamin Mutu Program Studi', 'Admin', 'Admin Universitas']);

    $getRouteUrl = function($action, $id = null) use ($currentPrefix) {
        $name1 = $currentPrefix . 'cpmk-' . $action;
        $name2 = $currentPrefix . $action . '-cpmk';
        if (\Illuminate\Support\Facades\Route::has($name1)) {
            return $id !== null ? route($name1, $id) : route($name1);
        }
        if (\Illuminate\Support\Facades\Route::has($name2)) {
            return $id !== null ? route($name2, $id) : route($name2);
        }
        if (\Illuminate\Support\Facades\Route::has('dosen.cpmk-' . $action)) {
            return $id !== null ? route('dosen.cpmk-' . $action, $id) : route('dosen.cpmk-' . $action);
        }
        return '#';
    };

    $getSubCpmkRoute = function($action, $id = null) use ($currentPrefix) {
        $names = [
            $currentPrefix . 'sub-cpmk-' . $action,
            $currentPrefix . 'subCpmk-' . $action,
            'dosen.sub-cpmk-' . $action,
            'dosen.subCpmk-' . $action,
        ];
        foreach ($names as $name) {
            if (\Illuminate\Support\Facades\Route::has($name)) {
                return $id !== null ? route($name, $id) : route($name);
            }
        }
        return '#';
    };
@endphp

@extends('dosen.template')

@section('content')
<style>
    .filter-tab-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 14px 18px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }

    .tab-filter-cpl {
        color: #475569 !important;
        background-color: #f8fafc !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 10px !important;
        padding: 11px 22px !important;
        font-size: 13.5px !important;
        font-weight: 600 !important;
        transition: all 0.2s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        cursor: pointer;
        white-space: nowrap !important;
    }

    .tab-filter-cpl:hover {
        background-color: #f1f5f9 !important;
        color: #1e293b !important;
    }

    .tab-filter-cpl .badge-count {
        background-color: #e2e8f0;
        color: #334155;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        margin-left: 4px;
        transition: all 0.2s ease;
    }

    .badge-cpl-kode {
        background-color: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12.5px;
        display: inline-block;
        white-space: nowrap !important;
        word-break: keep-all !important;
    }
    .cell-nowrap {
        white-space: nowrap !important;
        word-break: keep-all !important;
    }
    .badge-mk-pill {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 4px 8px;
        display: flex;
        flex-direction: column;
        gap: 2px;
        transition: all 0.15s ease;
    }
    .badge-mk-pill:hover {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
    }
    .badge-mk-code {
        font-weight: 700;
        font-size: 11px;
        color: {{ $isAptikom ? '#006199' : '#2664F5' }};
        white-space: nowrap;
    }
    .badge-mk-name {
        font-size: 11.5px;
        color: #334155;
        line-height: 1.3;
        font-weight: 500;
    }

    @if ($isAptikom)
        /* ── APTIKOM Theme (#006199 Accent) ── */
        .tab-filter-cpl.active {
            background: linear-gradient(135deg, #006199 0%, #004c78 100%) !important;
            border-color: #006199 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(0, 97, 153, 0.28) !important;
        }
        .tab-filter-cpl.active .badge-count {
            background-color: rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
        }
    @else
        /* ── NON-APTIKOM Theme (#2664F5 Accent) ── */
        .tab-filter-cpl.active {
            background: linear-gradient(135deg, #2664F5 0%, #1d52cc 100%) !important;
            border-color: #2664F5 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(38, 100, 245, 0.28) !important;
        }
        .tab-filter-cpl.active .badge-count {
            background-color: rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
        }
    @endif
    .cpmk-table-container {
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        overflow-x: auto;
        background-color: #ffffff;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
    }
    .cpmk-table-container table {
        width: 100% !important;
        min-width: 1100px !important;
        margin-bottom: 0 !important;
        border-collapse: collapse !important;
    }
    .cpmk-table-container th {
        background-color: #f8fafc !important;
        color: #334155 !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        padding: 12px 10px !important;
        border-bottom: 2px solid #cbd5e1 !important;
        vertical-align: middle !important;
    }
    .cpmk-table-container td {
        padding: 12px 10px !important;
        font-size: 13px !important;
        color: #1e293b !important;
        border-bottom: 1px solid #e2e8f0 !important;
        vertical-align: middle !important;
        white-space: normal !important;
        word-break: break-word !important;
    }
    .cpmk-table-container tbody tr:hover {
        background-color: #f8fafc !important;
    }
</style>

<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="card-header py-3 mb-3">
                <h4 class="mb-0 fw-bold">Daftar CPMK & Sub-CPMK</h4>
            </div>

            @if ($userOtoritas === 'Dosen')
                <!-- Alert Peringatan Terhubung Prodi -->
                <div class="alert alert-warning border-0 shadow-sm mb-4 d-flex align-items-start gap-3" style="border-left: 5px solid #f59e0b !important; background-color: #fffbeb; color: #92400e; border-radius: 10px; padding: 16px 20px;">
                    <i class="ti-alert me-1 mt-1" style="font-size: 1.5rem; color: #d97706; flex-shrink: 0;"></i>
                    <div>
                        <h6 class="fw-bold mb-1" style="color: #92400e; font-size: 0.95rem;">PERHATIAN PENTING - INTEGRASI DATA CPMK PRODI</h6>
                        <p class="mb-0" style="font-size: 0.85rem; line-height: 1.5; color: #b45309;">
                            Data <strong>CPMK dan Sub-CPMK</strong> ini terintegrasi langsung dengan seluruh sistem kurikulum Program Studi (termasuk dashboard & pemetaan pada <strong>Kaprodi</strong>, <strong>Penjamin Mutu</strong>, dan laporan evaluasi OBE). 
                            Mengubah atau menghapus CPMK akan berdampak langsung pada pemetaan CPL-CPMK-MK dan nilai evaluasi yang sudah tercatat. Mohon berhati-hati saat melakukan pengubahan data.
                        </p>
                    </div>
                </div>
            @endif

            <x-filter-form
                :universities="$universities"
                :faculties="$faculties"
                :programs="$programs"
                :kurikulums="$kurikulums"
                :showKurikulum="true"
            />

            @php
                $cplRowCounts = [];
                $totalTableRows = 0;

                foreach ($cpmks as $cpmk) {
                    $cplKode = $cpmk->cpl->kode ?? 'Tanpa CPL';
                    $subCount = $cpmk->subCpmks->count();
                    $rowCount = $subCount > 0 ? $subCount : 1;

                    if (!isset($cplRowCounts[$cplKode])) {
                        $cplRowCounts[$cplKode] = 0;
                    }
                    $cplRowCounts[$cplKode] += $rowCount;
                    $totalTableRows += $rowCount;
                }
                ksort($cplRowCounts);
            @endphp

            @if(count($cplRowCounts) > 0)
                <div class="filter-tab-card mb-4 mt-2">
                    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-1">
                            <i class="ti-filter me-1 text-secondary" style="font-size: 0.85rem;"></i>
                            <span class="text-uppercase fw-bold text-secondary" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                PILIH CPL PRODI:
                            </span>
                        </div>
                        <small class="text-muted" style="font-size: 0.75rem;">Klik tab CPL untuk memfilter daftar CPMK & Sub-CPMK</small>
                    </div>
                    <div class="d-flex flex-row flex-nowrap gap-2 overflow-auto pb-1" id="cplTabContainer" style="scrollbar-width: thin;">
                        <button type="button" class="btn tab-filter-cpl active" data-cpl="all">
                            <i class="ti-layout-grid2 me-1"></i>Semua CPL
                            <span class="badge-count">{{ $totalTableRows }}</span>
                        </button>
                        @foreach($cplRowCounts as $cplKode => $rowCount)
                            <button type="button" class="btn tab-filter-cpl" data-cpl="{{ $cplKode }}">
                                {{ $cplKode }}
                                <span class="badge-count">{{ $rowCount }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="cpmk-table-container mt-3">
                <table class="table table-hover dataTable mb-0" id="tableCpmkList">
                    <thead class="bg-light">
                        <tr class="align-middle">
                            <th style="min-width: 50px;" class="text-center cell-nowrap">No</th>
                            <th style="min-width: 110px;" class="text-center cell-nowrap">Kode CPL</th>
                            <th style="min-width: 120px;" class="text-center cell-nowrap">Kode CPMK</th>
                            <th style="min-width: 180px;">Mata Kuliah</th>
                            <th style="min-width: 250px;">Rincian CPMK</th>
                            <th style="min-width: 140px;" class="text-center cell-nowrap">Kode Sub</th>
                            <th style="min-width: 250px;">Uraian Sub-CPMK</th>
                            <th style="min-width: 100px;" class="text-center cell-nowrap">Kurikulum</th>
                            @if (in_array($userOtoritas, ['Wakil Rektor', 'Wakil Dekan']))
                                <th style="min-width: 140px;" class="cell-nowrap">Prodi</th>
                            @endif
                            @if (in_array($userOtoritas, ['Wakil Rektor']))
                                <th style="min-width: 140px;" class="cell-nowrap">Fakultas</th>
                            @endif
                            @if ($canManageCpmk)
                                <th style="min-width: 110px;" class="text-center cell-nowrap">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp

                        @forelse ($cpmks as $cpmk)
                            @php
                                $cplKode = $cpmk->cpl->kode ?? '-';
                                $tahunKurikulum = $cpmk->cpl->kurikulum->tahun ?? '-';
                                $subs = $cpmk->subCpmks->sortBy('kode')->values();
                                $cpmkMks = $cpmk->mks ? $cpmk->mks->unique('kode')->sortBy('kode') : collect();
                            @endphp

                            @if ($subs->count() > 0)
                                @foreach ($subs as $subIndex => $sub)
                                    <tr data-cpl="{{ $cplKode }}" data-kurikulum="{{ $tahunKurikulum }}" class="row-cpmk">
                                        <td class="text-center align-middle row-no cell-nowrap">{{ $no++ }}</td>
                                        <td class="text-center align-middle cell-nowrap" data-cpl="{{ $cplKode }}" data-filter="{{ $cplKode }}" data-search="{{ $cplKode }}">
                                            <span class="badge-cpl-kode">{{ $cplKode }}</span>
                                        </td>
                                        <td class="text-center align-middle fw-bold text-primary cell-nowrap">{{ $cpmk->kode }}</td>
                                        <td class="align-middle" style="min-width: 180px;">
                                            @if ($cpmkMks->isNotEmpty())
                                                <div class="d-flex flex-column gap-1">
                                                    @foreach ($cpmkMks as $mk)
                                                        <div class="badge-mk-pill">
                                                            <span class="badge-mk-code">[{{ $mk->kode }}]</span>
                                                            <span class="badge-mk-name">{{ $mk->nama }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted fst-italic small">- Belum dipetakan -</span>
                                            @endif
                                        </td>
                                        <td class="align-middle" style="white-space: normal; word-break: break-word;">
                                            {{ $cpmk->judul ?? '-' }}
                                        </td>
                                        <td class="text-center align-middle fw-semibold text-secondary cell-nowrap">{{ $sub->kode }}</td>
                                        <td class="align-middle" style="white-space: normal; word-break: break-word;">
                                            {{ $sub->uraian ?? '-' }}
                                        </td>
                                        <td class="text-center align-middle cell-nowrap">{{ $tahunKurikulum }}</td>
                                        @if (in_array($userOtoritas, ['Wakil Rektor', 'Wakil Dekan']))
                                            <td class="align-middle cell-nowrap">{{ $cpmk->prodi->nama ?? '-' }}</td>
                                        @endif
                                        @if (in_array($userOtoritas, ['Wakil Rektor']))
                                            <td class="align-middle cell-nowrap">{{ $cpmk->prodi->fakultas->nama ?? '-' }}</td>
                                        @endif
                                        @if ($canManageCpmk)
                                            <td class="text-center align-middle cell-nowrap">
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <button type="button" class="btn btn-warning btn-icons text-white me-1 btn-edit-subcpmk"
                                                        title="Edit Sub-CPMK"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalEditSubCpmk"
                                                        data-toggle="modal"
                                                        data-target="#modalEditSubCpmk"
                                                        data-id="{{ $sub->id }}"
                                                        data-kode="{{ $sub->kode }}"
                                                        data-uraian="{{ $sub->uraian }}"
                                                        data-cpmk-kode="{{ $cpmk->kode }}"
                                                        data-cpmk-judul="{{ $cpmk->judul }}">
                                                        <i class="ti-pencil"></i>
                                                    </button>
                                                    <form action="{{ $getSubCpmkRoute('delete', $sub->id) }}" method="post" class="d-inline m-0 p-0">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="btn btn-danger btn-icons"
                                                            title="Hapus Sub-CPMK"
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus Sub-CPMK {{ $sub->kode }}?')">
                                                            <i class="ti-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <tr data-cpl="{{ $cplKode }}" data-kurikulum="{{ $tahunKurikulum }}" class="row-cpmk">
                                    <td class="text-center align-middle row-no cell-nowrap">{{ $no++ }}</td>
                                    <td class="text-center align-middle cell-nowrap" data-cpl="{{ $cplKode }}" data-filter="{{ $cplKode }}" data-search="{{ $cplKode }}">
                                        <span class="badge-cpl-kode">{{ $cplKode }}</span>
                                    </td>
                                    <td class="text-center align-middle fw-bold text-primary cell-nowrap">{{ $cpmk->kode }}</td>
                                    <td class="align-middle" style="min-width: 180px;">
                                        @if ($cpmkMks->isNotEmpty())
                                            <div class="d-flex flex-column gap-1">
                                                @foreach ($cpmkMks as $mk)
                                                    <div class="badge-mk-pill">
                                                        <span class="badge-mk-code">[{{ $mk->kode }}]</span>
                                                        <span class="badge-mk-name">{{ $mk->nama }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted fst-italic small">- Belum dipetakan -</span>
                                        @endif
                                    </td>
                                    <td class="align-middle" style="white-space: normal; word-break: break-word;">
                                        {{ $cpmk->judul ?? '-' }}
                                    </td>
                                    <td class="text-center align-middle text-muted cell-nowrap">-</td>
                                    <td class="text-center text-muted fst-italic align-middle bg-light">
                                        - Tidak ada Sub-CPMK -
                                    </td>
                                    <td class="text-center align-middle cell-nowrap">{{ $tahunKurikulum }}</td>
                                    @if (in_array($userOtoritas, ['Wakil Rektor', 'Wakil Dekan']))
                                        <td class="align-middle cell-nowrap">{{ $cpmk->prodi->nama ?? '-' }}</td>
                                    @endif
                                    @if (in_array($userOtoritas, ['Wakil Rektor']))
                                        <td class="align-middle cell-nowrap">{{ $cpmk->prodi->fakultas->nama ?? '-' }}</td>
                                    @endif
                                    @if ($canManageCpmk)
                                        <td class="text-center align-middle cell-nowrap">
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <button type="button" class="btn btn-success btn-icons text-white me-1 btn-add-subcpmk"
                                                    title="Tambah Sub-CPMK"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalAddSubCpmk"
                                                    data-toggle="modal"
                                                    data-target="#modalAddSubCpmk"
                                                    data-cpmk-id="{{ $cpmk->id }}"
                                                    data-cpmk-kode="{{ $cpmk->kode }}"
                                                    data-cpmk-judul="{{ $cpmk->judul }}">
                                                    <i class="ti-plus"></i>
                                                </button>
                                                <form action="{{ $getRouteUrl('delete', $cpmk->id) }}" method="post" class="d-inline m-0 p-0">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-danger btn-icons"
                                                        title="Hapus CPMK Kosong"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus CPMK {{ $cpmk->kode }} yang belum memiliki Sub-CPMK?')">
                                                        <i class="ti-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">Data CPMK tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Sub-CPMK -->
<div class="modal fade" id="modalEditSubCpmk" tabindex="-1" aria-labelledby="modalEditSubCpmkLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formEditSubCpmk" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="modalEditSubCpmkLabel">
                        <i class="ti-pencil-alt text-warning me-2"></i>Edit Sub-CPMK
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-light border mb-3 p-2.5 d-flex align-items-center gap-2 rounded-3">
                        <span class="badge bg-primary px-2 py-1">CPMK Induk</span>
                        <span class="fw-semibold small text-dark" id="edit_sub_cpmk_info">-</span>
                    </div>
                    <div class="mb-3">
                        <label for="edit_sub_kode" class="form-label fw-bold">Kode Sub-CPMK <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_sub_kode" name="kode" required placeholder="Contoh: Sub-CPMK011">
                    </div>
                    <div class="mb-3">
                        <label for="edit_sub_uraian" class="form-label fw-bold">Uraian Sub-CPMK <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_sub_uraian" name="uraian" rows="4" required placeholder="Tuliskan uraian capaian Sub-CPMK..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="ti-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Sub-CPMK (Untuk CPMK yang belum memiliki Sub-CPMK) -->
<div class="modal fade" id="modalAddSubCpmk" tabindex="-1" aria-labelledby="modalAddSubCpmkLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formAddSubCpmk" action="" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="modalAddSubCpmkLabel">
                        <i class="ti-plus text-success me-2"></i>Tambah Sub-CPMK
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-light border mb-3 p-2.5 d-flex align-items-center gap-2 rounded-3">
                        <span class="badge bg-primary px-2 py-1">CPMK Induk</span>
                        <span class="fw-semibold small text-dark" id="add_sub_cpmk_info">-</span>
                    </div>
                    <div class="mb-3">
                        <label for="add_sub_kode" class="form-label fw-bold">Kode Sub-CPMK <small class="text-muted fw-normal">(Opsional, otomatis dari sistem jika kosong)</small></label>
                        <input type="text" class="form-control" id="add_sub_kode" name="kode" placeholder="Kosongkan untuk penomoran otomatis">
                    </div>
                    <div class="mb-3">
                        <label for="add_sub_uraian" class="form-label fw-bold">Uraian Sub-CPMK <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="add_sub_uraian" name="uraian" rows="4" required placeholder="Tuliskan uraian capaian Sub-CPMK..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success text-white px-4 fw-bold">
                        <i class="ti-plus me-1"></i> Tambah Sub-CPMK
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('assets/js/dynamic-filters.js') }}"></script>
    <script>
        function getModalInstance(modalEl) {
            if (!modalEl) return null;
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                if (typeof bootstrap.Modal.getOrCreateInstance === 'function') {
                    return bootstrap.Modal.getOrCreateInstance(modalEl);
                }
                if (typeof bootstrap.Modal.getInstance === 'function') {
                    var inst = bootstrap.Modal.getInstance(modalEl);
                    if (inst) return inst;
                }
                return new bootstrap.Modal(modalEl);
            }
            return null;
        }

        function showModalSafely(modalEl, modalSelector) {
            var bsModal = getModalInstance(modalEl);
            if (bsModal && typeof bsModal.show === 'function') {
                bsModal.show();
            } else if (typeof $ !== 'undefined' && typeof $(modalSelector).modal === 'function') {
                $(modalSelector).modal('show');
            }
        }

        function populateEditSubCpmk(id, kode, uraian, cpmkKode, cpmkJudul) {
            var routeTemplate = "{{ $getSubCpmkRoute('update', ':id') }}";
            var actionUrl = routeTemplate.replace(':id', id);
            $('#formEditSubCpmk').attr('action', actionUrl);
            $('#edit_sub_kode').val(kode || '');
            $('#edit_sub_uraian').val(uraian || '');
            $('#edit_sub_cpmk_info').text((cpmkKode || '-') + ' – ' + (cpmkJudul || '-'));
        }

        function openEditSubCpmkModal(id, kode, uraian, cpmkKode, cpmkJudul) {
            populateEditSubCpmk(id, kode, uraian, cpmkKode, cpmkJudul);
            var modalEl = document.getElementById('modalEditSubCpmk');
            showModalSafely(modalEl, '#modalEditSubCpmk');
        }
        window.openEditSubCpmkModal = openEditSubCpmkModal;

        function populateAddSubCpmk(cpmkId, cpmkKode, cpmkJudul) {
            var routeTemplate = "{{ $getSubCpmkRoute('store-for-cpmk', ':cpmkId') }}";
            var actionUrl = routeTemplate.replace(':cpmkId', cpmkId);
            $('#formAddSubCpmk').attr('action', actionUrl);
            $('#add_sub_kode').val('');
            $('#add_sub_uraian').val('');
            $('#add_sub_cpmk_info').text((cpmkKode || '-') + ' – ' + (cpmkJudul || '-'));
        }

        function openAddSubCpmkModal(cpmkId, cpmkKode, cpmkJudul) {
            populateAddSubCpmk(cpmkId, cpmkKode, cpmkJudul);
            var modalEl = document.getElementById('modalAddSubCpmk');
            showModalSafely(modalEl, '#modalAddSubCpmk');
        }
        window.openAddSubCpmkModal = openAddSubCpmkModal;

        // Delegated click handlers for Sub-CPMK action buttons
        $(document).on('click', '.btn-edit-subcpmk', function(e) {
            var $btn = $(this).closest('.btn-edit-subcpmk');
            var id = $btn.attr('data-id') || $btn.data('id');
            var kode = $btn.attr('data-kode') || $btn.data('kode');
            var uraian = $btn.attr('data-uraian') || $btn.data('uraian');
            var cpmkKode = $btn.attr('data-cpmk-kode') || $btn.data('cpmk-kode');
            var cpmkJudul = $btn.attr('data-cpmk-judul') || $btn.data('cpmk-judul');
            openEditSubCpmkModal(id, kode, uraian, cpmkKode, cpmkJudul);
        });

        $(document).on('click', '.btn-add-subcpmk', function(e) {
            var $btn = $(this).closest('.btn-add-subcpmk');
            var cpmkId = $btn.attr('data-cpmk-id') || $btn.data('cpmk-id');
            var cpmkKode = $btn.attr('data-cpmk-kode') || $btn.data('cpmk-kode');
            var cpmkJudul = $btn.attr('data-cpmk-judul') || $btn.data('cpmk-judul');
            openAddSubCpmkModal(cpmkId, cpmkKode, cpmkJudul);
        });

        // Bootstrap show.bs.modal listeners for native data-bs-toggle
        var editModalEl = document.getElementById('modalEditSubCpmk');
        if (editModalEl) {
            editModalEl.addEventListener('show.bs.modal', function(event) {
                var btn = event.relatedTarget;
                if (btn) {
                    var $btn = $(btn).closest('.btn-edit-subcpmk');
                    var id = $btn.attr('data-id') || $btn.data('id');
                    var kode = $btn.attr('data-kode') || $btn.data('kode');
                    var uraian = $btn.attr('data-uraian') || $btn.data('uraian');
                    var cpmkKode = $btn.attr('data-cpmk-kode') || $btn.data('cpmk-kode');
                    var cpmkJudul = $btn.attr('data-cpmk-judul') || $btn.data('cpmk-judul');
                    if (id) {
                        populateEditSubCpmk(id, kode, uraian, cpmkKode, cpmkJudul);
                    }
                }
            });
        }

        var addModalEl = document.getElementById('modalAddSubCpmk');
        if (addModalEl) {
            addModalEl.addEventListener('show.bs.modal', function(event) {
                var btn = event.relatedTarget;
                if (btn) {
                    var $btn = $(btn).closest('.btn-add-subcpmk');
                    var cpmkId = $btn.attr('data-cpmk-id') || $btn.data('cpmk-id');
                    var cpmkKode = $btn.attr('data-cpmk-kode') || $btn.data('cpmk-kode');
                    var cpmkJudul = $btn.attr('data-cpmk-judul') || $btn.data('cpmk-judul');
                    if (cpmkId) {
                        populateAddSubCpmk(cpmkId, cpmkKode, cpmkJudul);
                    }
                }
            });
        }

        $(document).ready(function() {
            var selectedCplFilter = 'all';

            // Custom search filter for DataTables
            if ($.fn.DataTable) {
                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    if (settings.nTable && settings.nTable.id !== 'tableCpmkList') {
                        return true;
                    }
                    if (selectedCplFilter === 'all') {
                        return true;
                    }
                    var rowNode = settings.aoData && settings.aoData[dataIndex] ? settings.aoData[dataIndex].nTr : null;
                    var rowCpl = '';
                    if (rowNode) {
                        rowCpl = $(rowNode).attr('data-cpl') || $(rowNode).data('cpl');
                    }
                    if (!rowCpl && data && data.length > 1) {
                        rowCpl = (data[1] || '').replace(/<[^>]*>/g, '').trim();
                    }
                    return String(rowCpl).trim() === String(selectedCplFilter).trim();
                });
            }

            function getCpmkDataTable() {
                if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tableCpmkList')) {
                    return $('#tableCpmkList').DataTable();
                }
                return null;
            }

            function renumberVisibleRows(dt) {
                if (dt) {
                    let i = 1;
                    dt.cells(null, 0, { search: 'applied', order: 'applied' }).every(function () {
                        this.data(i++);
                    });
                } else {
                    let i = 1;
                    $('.row-cpmk:visible').each(function() {
                        $(this).find('.row-no').text(i++);
                    });
                }
            }

            var dtTable = getCpmkDataTable();
            if (dtTable) {
                dtTable.on('order.dt search.dt page.dt draw.dt', function () {
                    renumberVisibleRows(dtTable);
                });
            }

            $('.tab-filter-cpl').on('click', function(e) {
                e.preventDefault();
                var targetCpl = $(this).data('cpl') ? $(this).data('cpl').toString().trim() : 'all';
                selectedCplFilter = targetCpl;

                $('.tab-filter-cpl').removeClass('active');
                $(this).addClass('active');

                var dt = getCpmkDataTable();
                if (dt) {
                    dt.draw();
                } else {
                    $('.row-cpmk').each(function() {
                        var rowCpl = ($(this).attr('data-cpl') || $(this).data('cpl') || $(this).find('td:eq(1)').text()).toString().trim();
                        if (targetCpl === 'all' || rowCpl === targetCpl) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                    renumberVisibleRows(null);
                }
            });
        });
    </script>
@endpush
@endsection