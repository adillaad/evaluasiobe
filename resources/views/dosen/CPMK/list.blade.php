@php
    $isAptikom = auth()->check() && auth()->user()->prodi ? (bool) auth()->user()->prodi->is_aptikom : true;
    $userOtoritas = $userOtoritas ?? (auth()->user()->otoritas->otoritas ?? 'Dosen');

    $routePrefixMap = [
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
    $canManageCpmk = in_array($userOtoritas, ['Dosen', 'Kepala Program Studi', 'Penjamin Mutu Program Studi', 'Admin', 'Admin Universitas']);

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
                            @endphp

                            @if ($subs->count() > 0)
                                @foreach ($subs as $subIndex => $sub)
                                    <tr data-cpl="{{ $cplKode }}" data-kurikulum="{{ $tahunKurikulum }}" class="row-cpmk">
                                        <td class="text-center align-middle row-no cell-nowrap">{{ $no++ }}</td>
                                        <td class="text-center align-middle cell-nowrap">
                                            <span class="badge-cpl-kode">{{ $cplKode }}</span>
                                        </td>
                                        <td class="text-center align-middle fw-bold text-primary cell-nowrap">{{ $cpmk->kode }}</td>
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
                                                    <a href="{{ $getRouteUrl('edit', $cpmk->id) }}" class="btn btn-warning btn-icons text-white me-1"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Edit CPMK">
                                                        <i class="ti-pencil"></i>
                                                    </a>
                                                    <form action="{{ $getRouteUrl('delete', $cpmk->id) }}" method="post" class="d-inline m-0 p-0">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="btn btn-danger btn-icons"
                                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus CPMK"
                                                            onclick="return confirm('PERHATIAN: Menghapus CPMK {{ $cpmk->kode }} akan menghapus SEMUA Sub-CPMK di dalamnya dan berdampak pada pemetaan Kaprodi & Penjamin Mutu. Yakin ingin melanjutkan?')">
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
                                    <td class="text-center align-middle cell-nowrap">
                                        <span class="badge-cpl-kode">{{ $cplKode }}</span>
                                    </td>
                                    <td class="text-center align-middle fw-bold text-primary cell-nowrap">{{ $cpmk->kode }}</td>
                                    <td class="align-middle" style="white-space: normal; word-break: break-word;">
                                        {{ $cpmk->judul ?? '-' }}
                                    </td>
                                    <td colspan="2" class="text-center text-muted fst-italic align-middle bg-light">
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
                                                <a href="{{ $getRouteUrl('edit', $cpmk->id) }}" class="btn btn-warning btn-icons text-white me-1"
                                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Edit CPMK">
                                                    <i class="ti-pencil"></i>
                                                </a>
                                                <form action="{{ $getRouteUrl('delete', $cpmk->id) }}" method="post" class="d-inline m-0 p-0">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-danger btn-icons"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus CPMK"
                                                        onclick="return confirm('PERHATIAN: Menghapus CPMK {{ $cpmk->kode }} akan menghapus SEMUA Sub-CPMK di dalamnya dan berdampak pada pemetaan Kaprodi & Penjamin Mutu. Yakin ingin melanjutkan?')">
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
                                <td colspan="10" class="text-center text-muted py-4">Data CPMK tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('assets/js/dynamic-filters.js') }}"></script>
    <script>
        $(document).ready(function() {
            var dtTable = null;
            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tableCpmkList')) {
                dtTable = $('#tableCpmkList').DataTable();
            } else if ($.fn.DataTable && $.fn.DataTable.isDataTable('.dataTable')) {
                dtTable = $('.dataTable').DataTable();
            }

            if (dtTable) {
                dtTable.on('order.dt search.dt page.dt', function () {
                    let i = 1;
                    dtTable.cells(null, 0, { search: 'applied', order: 'applied' }).every(function () {
                        this.data(i++);
                    });
                });
            }

            $('.tab-filter-cpl').on('click', function() {
                var targetCpl = $(this).data('cpl').toString();

                $('.tab-filter-cpl').removeClass('active');
                $(this).addClass('active');

                var dt = dtTable;
                if (!dt) {
                    if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tableCpmkList')) {
                        dt = $('#tableCpmkList').DataTable();
                    } else if ($.fn.DataTable && $.fn.DataTable.isDataTable('.dataTable')) {
                        dt = $('.dataTable').DataTable();
                    }
                }

                if (dt) {
                    if (targetCpl === 'all') {
                        dt.column(1).search('').draw();
                    } else {
                        dt.column(1).search('^' + $.fn.dataTable.util.escapeRegex(targetCpl) + '$', true, false).draw();
                    }
                } else {
                    var count = 1;
                    $('.row-cpmk').each(function() {
                        var rowCpl = $(this).data('cpl').toString();
                        if (targetCpl === 'all' || rowCpl === targetCpl) {
                            $(this).show();
                            $(this).find('.row-no').text(count++);
                        } else {
                            $(this).hide();
                        }
                    });
                }
            });
        });
    </script>
@endpush
@endsection