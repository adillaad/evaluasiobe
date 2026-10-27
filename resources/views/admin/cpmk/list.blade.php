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
    $currentPrefix = $routePrefixMap[$userOtoritas] ?? 'admin-universitas.';
    $canManageCpmk = in_array($userOtoritas, ['Dosen', 'Koordinator Program Studi', 'Kepala Program Studi', 'Penjamin Mutu Program Studi', 'Admin', 'Admin Universitas']);
    $templateToExtend = in_array($userOtoritas, ['Admin Universitas', 'Admin']) ? 'admin.template' : (in_array($userOtoritas, ['Koordinator Program Studi', 'Kepala Program Studi', 'Penjamin Mutu Program Studi', 'Penjamin Mutu Fakultas', 'Penjamin Mutu Universitas']) ? 'penjamin-mutu.template' : 'dosen.template');

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

@extends($templateToExtend)

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
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="card-header bg-white py-3 mb-3 border-0 px-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h4 class="mb-0 fw-bold text-dark">Daftar CPMK</h4>
                @if ($canManageCpmk)
                    <button type="button" class="btn btn-primary text-white btn-icon-text d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahCpmk">
                        <i class="ti-plus"></i>
                        <span>Tambah CPMK</span>
                    </button>
                @endif
            </div>

            @if ($userOtoritas === 'Dosen')
                <!-- Alert Peringatan Terhubung Prodi -->
                <div class="alert alert-warning border-0 shadow-sm mb-4 d-flex align-items-start gap-3" style="border-left: 5px solid #f59e0b !important; background-color: #fffbeb; color: #92400e; border-radius: 10px; padding: 16px 20px;">
                    <i class="ti-alert me-1 mt-1" style="font-size: 1.5rem; color: #d97706; flex-shrink: 0;"></i>
                    <div>
                        <h6 class="fw-bold mb-1" style="color: #92400e; font-size: 0.95rem;">PERHATIAN PENTING - INTEGRASI DATA CPMK PRODI</h6>
                        <p class="mb-0" style="font-size: 0.85rem; line-height: 1.5; color: #b45309;">
                            Data <strong>CPMK</strong> ini terintegrasi langsung dengan seluruh sistem kurikulum Program Studi (termasuk dashboard & pemetaan pada <strong>Kaprodi</strong>, <strong>Penjamin Mutu</strong>, dan laporan evaluasi OBE). 
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
                :show-kurikulum="true" 
                :show-cpl="true"
                :cpls-filter="$cplsFilter ?? []"
            />

            @php
                $cplRowCounts = [];
                $totalTableRows = count($cpmks);

                foreach ($cpmks as $cpmk) {
                    $cplKode = $cpmk->cpl->kode ?? 'Tanpa CPL';
                    if (!isset($cplRowCounts[$cplKode])) {
                        $cplRowCounts[$cplKode] = 0;
                    }
                    $cplRowCounts[$cplKode]++;
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
                        <small class="text-muted" style="font-size: 0.75rem;">Klik tab CPL untuk memfilter daftar CPMK</small>
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
                            <th style="min-width: 320px;">Rincian CPMK</th>
                            <th style="min-width: 100px;" class="text-center cell-nowrap">Kurikulum</th>
                            @if (in_array($userOtoritas, ['Wakil Rektor', 'Wakil Dekan', 'Admin', 'Admin Universitas', 'Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas']))
                                <th style="min-width: 140px;" class="cell-nowrap">Prodi</th>
                            @endif
                            @if (in_array($userOtoritas, ['Wakil Rektor', 'Admin', 'Admin Universitas', 'Penjamin Mutu Universitas']))
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
                            @endphp
                            <tr data-cpl="{{ $cplKode }}" data-kurikulum="{{ $tahunKurikulum }}" class="row-cpmk">
                                <td class="text-center align-middle row-no cell-nowrap">{{ $no++ }}</td>
                                <td class="text-center align-middle cell-nowrap" data-cpl="{{ $cplKode }}" data-filter="{{ $cplKode }}" data-search="{{ $cplKode }}">
                                    <span class="badge-cpl-kode">{{ $cplKode }}</span>
                                </td>
                                <td class="text-center align-middle fw-bold text-primary cell-nowrap">{{ $cpmk->kode }}</td>
                                <td class="align-middle" style="white-space: normal; word-break: break-word;">
                                    {{ $cpmk->judul ?? '-' }}
                                </td>
                                <td class="text-center align-middle cell-nowrap">{{ $tahunKurikulum }}</td>
                                @if (in_array($userOtoritas, ['Wakil Rektor', 'Wakil Dekan', 'Admin', 'Admin Universitas', 'Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas']))
                                    <td class="align-middle cell-nowrap">{{ $cpmk->prodi->nama ?? '-' }}</td>
                                @endif
                                @if (in_array($userOtoritas, ['Wakil Rektor', 'Admin', 'Admin Universitas', 'Penjamin Mutu Universitas']))
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
                                                    onclick="return confirm('Yakin ingin menghapus CPMK {{ $cpmk->kode }}?')">
                                                    <i class="ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                @php
                                    $colCount = 6;
                                    if (in_array($userOtoritas, ['Wakil Rektor', 'Wakil Dekan', 'Admin', 'Admin Universitas', 'Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas'])) $colCount++;
                                    if (in_array($userOtoritas, ['Wakil Rektor', 'Admin', 'Admin Universitas', 'Penjamin Mutu Universitas'])) $colCount++;
                                    if (!$canManageCpmk) $colCount--;
                                @endphp
                                <td colspan="{{ $colCount }}" class="text-center text-muted py-4">Data CPMK tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if ($canManageCpmk)
    <!-- Modal Tambah CPMK -->
    <div class="modal fade" id="modalTambahCpmk" tabindex="-1" aria-labelledby="modalTambahCpmkLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0" style="border-radius: 14px; overflow: hidden;">
                <form action="{{ $getRouteUrl('store') }}" method="post" id="formTambahCpmk">
                    @csrf
                    <div class="modal-header bg-light py-3 px-4 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-target text-primary" style="font-size: 1.4rem;"></i>
                            <h5 class="modal-title fw-bold mb-0 text-dark" id="modalTambahCpmkLabel">Tambah CPMK</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger py-2 small mb-3">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="modal_kurikulum_id" class="form-label fw-semibold">Kurikulum <span class="text-danger">*</span></label>
                            <select name="kurikulum_id" id="modal_kurikulum_id" class="form-select w-100" required>
                                <option value="">-- Pilih Kurikulum --</option>
                                @php
                                    $kurikulumList = $kurikulumsAdd ?? $kurikulums;
                                @endphp
                                @foreach ($kurikulumList as $k)
                                    <option value="{{ $k->id }}" {{ old('kurikulum_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->tahun }} - {{ $k->nama_prodi ?? ($k->prodi->nama ?? '') }}
                                        @if (in_array($userOtoritas, ['Admin Universitas', 'Admin', 'Wakil Rektor']))
                                            - {{ $k->nama_fakultas ?? ($k->prodi->fakultas->nama ?? '') }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="modal_cplSelect" class="form-label fw-semibold">Pilih CPL <span class="text-danger">*</span></label>
                            <select id="modal_cplSelect" name="cpl" class="form-select w-100" required disabled>
                                <option value="">-- Pilih Kurikulum Terlebih Dahulu --</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                                <span>Judul / Rincian CPMK <span class="text-danger">*</span></span>
                                <button type="button" id="modal-dynamic-ar" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 py-1 px-2">
                                    <i class="ti-plus"></i> Tambah Baris
                                </button>
                            </label>
                            <div id="modalDynamicAddRemove" class="d-flex flex-column gap-2">
                                @foreach (old('judul', ['']) as $key => $value)
                                    <div class="clone-row d-flex align-items-center gap-2">
                                        <input type="text" name="judul[]" class="form-control" placeholder="Tuliskan rincian Capaian Pembelajaran Mata Kuliah" value="{{ $value }}" autocomplete="off" required>
                                        @if ($loop->first)
                                            <button type="button" class="btn btn-secondary btn-sm opacity-50" style="min-width: 40px; height: 38px;" disabled title="Baris utama tidak dapat dihapus">
                                                <i class="ti-trash"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-danger btn-sm text-white remove-input-field" style="min-width: 40px; height: 38px;" title="Hapus baris ini">
                                                <i class="ti-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted mt-1 d-block">Klik <strong>Tambah Baris</strong> jika ingin menambahkan lebih dari satu CPMK sekaligus untuk CPL ini.</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2 px-4 border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary text-white d-inline-flex align-items-center gap-1">
                            <i class="ti-save"></i>
                            <span>Simpan CPMK</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@push('scripts')
    <script src="{{ asset('assets/js/dynamic-filters.js') }}"></script>
    <script>
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

            // --- AJAX & Dynamic Row for Modal Tambah CPMK ---
            function loadModalCpl(selectedCplId = null) {
                var kurikulumID = $('#modal_kurikulum_id').val();
                if (!kurikulumID) {
                    $('#modal_cplSelect').html('<option value="">-- Pilih Kurikulum Terlebih Dahulu --</option>').prop('disabled', true);
                    return;
                }

                var urlget = "{{ route($currentPrefix . 'getCplByKurikulum', ['kurikulum_id' => ':id']) }}".replace(':id', kurikulumID);

                $('#modal_cplSelect').prop('disabled', true).html('<option value="">Memuat CPL...</option>');

                $.ajax({
                    url: urlget,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('#modal_cplSelect').empty().append('<option value="">-- Pilih CPL --</option>');
                        $.each(data, function(key, value) {
                            var isSelected = (selectedCplId && selectedCplId == value.id) ? 'selected' : '';
                            $('#modal_cplSelect').append('<option value="' + value.id + '" ' + isSelected + '>' + value.kode + ' - ' + value.judul + '</option>');
                        });
                        $('#modal_cplSelect').prop('disabled', false);
                    },
                    error: function() {
                        $('#modal_cplSelect').html('<option value="">Gagal memuat CPL</option>').prop('disabled', true);
                    }
                });
            }

            $('#modal_kurikulum_id').on('change', function() {
                loadModalCpl();
            });

            // Trigger load CPL if old input exists (e.g. on validation failure)
            @if ($errors->any())
                var modalEl = document.getElementById('modalTambahCpmk');
                if (modalEl) {
                    var modalTambah = new bootstrap.Modal(modalEl);
                    modalTambah.show();
                    if ($('#modal_kurikulum_id').val()) {
                        loadModalCpl("{{ old('cpl') }}");
                    }
                }
            @endif

            // Dynamic Add/Remove rows in modal
            $("#modal-dynamic-ar").click(function () {
                $("#modalDynamicAddRemove").append(`
                    <div class="clone-row d-flex align-items-center gap-2">
                        <input type="text" name="judul[]" placeholder="Tuliskan rincian Capaian Pembelajaran Mata Kuliah" class="form-control" autocomplete="off" required />
                        <button type="button" class="btn btn-danger btn-sm text-white remove-input-field" style="min-width: 40px; height: 38px;" title="Hapus baris ini">
                            <i class="ti-trash"></i>
                        </button>
                    </div>
                `);
            });

            $(document).on('click', '.remove-input-field', function () {
                $(this).closest('.clone-row').remove();
            });
        });
    </script>
@endpush
@endsection
