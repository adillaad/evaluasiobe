@php
    $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
@endphp
@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')

    <div class="container-fluid cpl-bk-mk-wrapper">
        @if(isset($kurikulums))
            <div class="card mb-3 border-0 shadow-sm rounded-4">
                <div class="card-body pb-3">
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
                    <h6 class="fw-bold mb-0 text-primary">Mode Edit Matriks CPL-BK-MK Aktif</h6>
                    <small class="text-secondary">Pilih/centang kode Mata Kuliah (MK) pada perpotongan Bahan Kajian (BK) dan CPL yang sesuai.</small>
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

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    <div>
                        <h4 class="card-title fw-bold mb-1">List Pemetaan CPL-BK-MK</h4>
                        <p class="text-muted small mb-0">Matriks keterhubungan antara Capaian Pembelajaran Lulusan (CPL), Bahan Kajian (BK), dan Mata Kuliah (MK).</p>
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

                <form id="form-matrix-edit" action="{{ route($currentPrefix . 'cpl.cpl-bk-mk-matrix-update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="kurikulum_id" value="{{ request('kurikulum_id') }}">

                    <div class="table-responsive mt-3">
                        <table id="cplBkMkTable" class="table table-bordered table-hover align-middle w-100 cpl-bk-mk-table mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="position: relative; width: 110px; height: 45px;" class="align-middle">
                                        <span class="double-table-head-data-top-right fw-bold text-primary">CPL</span>
                                        <span class="double-table-head-data-bottom-left fw-bold text-dark">BK</span>
                                        <span class="diagonal-line"></span>
                                    </th>
                                    @foreach ($cpls as $cpl)
                                        <th class="py-3 px-2 text-center" style="min-width: 95px;" title="{{ $cpl->kode }}: {{ $cpl->judul }}">
                                            <span class="badge bg-primary-soft text-primary font-12 px-2 py-1 rounded-2">{{ $cpl->kode }}</span>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bks as $bk)
                                    <tr>
                                        <td class="bg-light fw-bold text-dark text-center align-middle font-13" title="{{ $bk->kode }}: {{ $bk->nama }} (Kurikulum {{ $bk->kurikulum->tahun ?? '-' }})">
                                            <div>{{ $bk->kode }}</div>
                                            <small class="badge bg-white text-muted border font-10 fw-normal mt-0.5">Kur {{ $bk->kurikulum->tahun ?? '-' }}</small>
                                        </td>
                                        @foreach ($cpls as $cpl)
                                            @php
                                                $mappedMksInCell = collect();
                                                if ($bk->cpl->contains('id', $cpl->id)) {
                                                    foreach ($bk->mk as $mkItem) {
                                                        if ($mkItem->cpl->contains('id', $cpl->id)) {
                                                            $mappedMksInCell->push($mkItem);
                                                        }
                                                    }
                                                }
                                                $hasMappedMks = $mappedMksInCell->isNotEmpty();
                                            @endphp
                                            <td class="text-center matrix-cell align-middle {{ $hasMappedMks ? 'is-mapped' : '' }}"
                                                data-bk="{{ $bk->id }}"
                                                data-cpl="{{ $cpl->id }}">
                                                
                                                <!-- View Mode -->
                                                <div class="cell-view-mode py-1">
                                                    @if ($hasMappedMks)
                                                        <div class="d-flex flex-column gap-1 align-items-center">
                                                            @foreach ($mappedMksInCell as $mkItem)
                                                                <span class="badge bg-primary text-white font-11 px-2 py-1 rounded-2" title="{{ $mkItem->nama }}">
                                                                    {{ $mkItem->kode }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="text-muted opacity-25 font-16">-</span>
                                                    @endif
                                                </div>

                                                <!-- Edit Mode Input -->
                                                <div class="cell-edit-mode d-none p-1 bg-white rounded-2 border">
                                                    @if (isset($mks) && $mks->count() > 0)
                                                        <div class="d-flex flex-column gap-1" style="max-height: 150px; overflow-y: auto;">
                                                            @foreach ($mks as $mkOption)
                                                                @php
                                                                    $isMkChecked = $mappedMksInCell->contains('kode', $mkOption->kode);
                                                                @endphp
                                                                <label class="mk-check-item d-flex align-items-center gap-2 px-2 py-1 rounded-2 border text-start cursor-pointer mb-0 {{ $isMkChecked ? 'bg-primary-soft border-primary' : 'bg-light border-light' }}"
                                                                       style="font-size: 11px;">
                                                                    <input type="checkbox"
                                                                           class="matrix-checkbox cursor-pointer"
                                                                           name="matrix[{{ $bk->id }}][{{ $cpl->id }}][]"
                                                                           value="{{ $mkOption->kode }}"
                                                                           {{ $isMkChecked ? 'checked' : '' }}
                                                                           style="width: 15px; height: 15px; flex-shrink: 0; cursor: pointer;">
                                                                    <span class="fw-bold text-dark text-truncate" title="{{ $mkOption->kode }} - {{ $mkOption->nama }}">
                                                                        {{ $mkOption->kode }}
                                                                    </span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="text-muted font-11">Tidak ada data MK</span>
                                                    @endif
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 1 + count($cpls) }}" class="text-center py-4 text-muted">
                                            <i class="mdi mdi-file-table-outline font-32 d-block mb-1"></i>
                                            Belum ada data Bahan Kajian.
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

        <!-- Card Deskripsi -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-4">
            <div class="card-body p-4">
                <h4 class="card-title fw-bold mb-3">Deskripsi CPL, BK, dan MK</h4>
                <div class="scrollable-descriptions pe-2" style="max-height: 400px; overflow-y: auto;">
                    <h6 class="fw-bold text-primary mb-2">Deskripsi CPL</h6>
                    @foreach ($cpls as $cpl)
                        <div class="mb-2 p-2 bg-light rounded-2">
                            <p class="mb-0 font-13"><strong>{{ $cpl->kode }}</strong>: {{ $cpl->judul }}</p>
                        </div>
                    @endforeach
                    
                    <hr class="my-3">
                    <h6 class="fw-bold text-primary mb-2">Deskripsi BK</h6>
                    @foreach ($bks as $bk)
                        <div class="mb-2 p-2 bg-light rounded-2">
                            <p class="mb-0 font-13"><strong>{{ $bk->kode }}</strong>: {{ $bk->nama }}</p>
                        </div>
                    @endforeach

                    <hr class="my-3">
                    <h6 class="fw-bold text-primary mb-2">Deskripsi MK</h6>
                    @if (isset($mks))
                        @foreach ($mks as $mk)
                            <div class="mb-2 p-2 bg-light rounded-2">
                                <p class="mb-0 font-13"><strong>{{ $mk->kode }}</strong>: {{ $mk->nama }}</p>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .cpl-bk-mk-wrapper .dataTables_wrapper {
            width: 100%;
        }

        .cpl-bk-mk-wrapper .dataTables_wrapper .dataTables_length,
        .cpl-bk-mk-wrapper .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }

        .cpl-bk-mk-wrapper .dataTables_wrapper .dataTables_length label,
        .cpl-bk-mk-wrapper .dataTables_wrapper .dataTables_filter label {
            display: inline-flex;
            align-items: center;
            flex-wrap: nowrap;
            gap: 0.4rem;
            margin-bottom: 0;
            white-space: nowrap;
            font-weight: normal;
        }

        .cpl-bk-mk-wrapper .dataTables_wrapper .dataTables_length {
            float: left;
        }

        .cpl-bk-mk-wrapper .dataTables_wrapper .dataTables_filter {
            float: right;
        }

        .cpl-bk-mk-wrapper .dataTables_wrapper .dataTables_info {
            float: left;
            padding-top: 0.85rem;
        }

        .cpl-bk-mk-wrapper .dataTables_wrapper .dataTables_paginate {
            float: right;
            padding-top: 0.5rem;
        }

        .cpl-bk-mk-wrapper .dataTables_wrapper::after {
            content: '';
            display: block;
            clear: both;
        }

        .double-table-head-data-top-right {
            position: absolute;
            top: 4px;
            right: 8px;
            font-size: 11px;
        }

        .double-table-head-data-bottom-left {
            position: absolute;
            bottom: 4px;
            left: 8px;
            font-size: 11px;
        }

        .diagonal-line {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top right, transparent calc(50% - 1px), #dee2e6 50%, transparent calc(50% + 1px));
            pointer-events: none;
        }

        .cpl-bk-mk-table {
            border-collapse: separate !important;
            border-spacing: 0 !important;
            border: 1px solid #e2e8f0 !important;
        }

        .cpl-bk-mk-table th {
            border-bottom: 2px solid #e2e8f0 !important;
            border-right: 1px solid rgba(0, 0, 0, 0.04) !important;
        }

        .cpl-bk-mk-table tbody tr {
            border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
        }

        .cpl-bk-mk-table tbody td {
            border-top: 1px solid rgba(0, 0, 0, 0.06) !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06) !important;
            border-right: 1px solid rgba(0, 0, 0, 0.04) !important;
        }

        .cpl-bk-mk-table tbody tr:hover td {
            background-color: rgba(2, 132, 199, 0.03) !important;
        }

        .bg-primary-soft {
            background-color: rgba(2, 132, 199, 0.12);
        }

        .matrix-cell {
            position: relative;
            transition: background-color 0.15s ease-in-out;
        }

        .matrix-cell.is-mapped {
            background-color: rgba(2, 132, 199, 0.04);
        }

        .editing-active .matrix-cell {
            background-color: #fafafa;
        }

        .editing-active .cell-view-mode {
            display: none !important;
        }

        .editing-active .cell-edit-mode {
            display: block !important;
        }

        @media (max-width: 576px) {
            .cpl-bk-mk-wrapper .dataTables_wrapper .dataTables_length,
            .cpl-bk-mk-wrapper .dataTables_wrapper .dataTables_filter {
                float: none;
                text-align: left;
            }

            .cpl-bk-mk-wrapper .dataTables_wrapper .dataTables_filter {
                margin-top: 0.5rem;
            }
        }
    </style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const tableEl = $('#cplBkMkTable');
        let dataTableInst = null;

        if (tableEl.length) {
            if ($.fn.DataTable.isDataTable(tableEl)) {
                tableEl.DataTable().destroy();
            }

            dataTableInst = tableEl.DataTable({
                aaSorting: [],
                autoWidth: false,
                paging: true,
                pageLength: 10,
                lengthMenu: [[5, 10, 25, -1], [5, 10, 25, 'Semua']],
                language: {
                    lengthMenu: 'Tampilkan _MENU_ data',
                    search: 'Cari:',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                    infoFiltered: '(difilter dari _MAX_ total data)',
                    paginate: {
                        first: 'Awal',
                        last: 'Akhir',
                        next: 'Berikutnya',
                        previous: 'Sebelumnya'
                    },
                    zeroRecords: 'Data tidak ditemukan'
                },
                columnDefs: [
                    { targets: 0, width: '110px' },
                    @if (count($cpls) > 0)
                    { targets: [{{ implode(',', range(1, count($cpls))) }}], className: 'text-center', orderable: false }
                    @endif
                ]
            });
        }

        const btnToggleMatrixEdit = document.getElementById('btn-toggle-matrix-edit');
        const btnMatrixText = document.getElementById('btn-matrix-text');
        const matrixBanner = document.getElementById('matrix-edit-banner');
        const matrixSaveBar = document.getElementById('matrix-save-bar');
        const matrixTable = document.getElementById('cplBkMkTable');

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

        $(document).on('change', '.matrix-checkbox', function () {
            const label = $(this).closest('.mk-check-item');
            if (label.length) {
                if (this.checked) {
                    label.addClass('bg-primary-soft border-primary').removeClass('bg-light border-light');
                } else {
                    label.removeClass('bg-primary-soft border-primary').addClass('bg-light border-light');
                }
            }

            const cell = this.closest('.matrix-cell');
            if (cell) {
                const anyChecked = $(cell).find('.matrix-checkbox:checked').length > 0;
                if (anyChecked) {
                    cell.classList.add('is-mapped');
                } else {
                    cell.classList.remove('is-mapped');
                }
            }
        });

        // Form submit handler to collect input from all DataTables pages
        $('#form-matrix-edit').on('submit', function (e) {
            if (dataTableInst) {
                const form = this;
                dataTableInst.$('input[type="checkbox"]:checked').each(function () {
                    if (!$.contains(document.body, this)) {
                        $(form).append(
                            $('<input>')
                                .attr('type', 'hidden')
                                .attr('name', this.name)
                                .val(this.value)
                        );
                    }
                });
            }
        });
    });
</script>
@endpush
