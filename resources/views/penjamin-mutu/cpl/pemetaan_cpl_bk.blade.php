@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')

    <div class="container-fluid cpl-bk-wrapper">
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
                    <h6 class="fw-bold mb-0 text-primary">Mode Edit Matriks Aktif</h6>
                    <small class="text-secondary">Klik sel mana saja pada tabel di bawah untuk mencentang atau menghapus centang pemetaan CPL ke Bahan Kajian.</small>
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
                        <h4 class="card-title fw-bold mb-1">List Pemetaan CPL-BK</h4>
                        <p class="text-muted small mb-0">Matriks keterhubungan antara Capaian Pembelajaran Lulusan (CPL) dan Bahan Kajian (BK).</p>
                    </div>

                    @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas']))
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" id="btn-toggle-matrix-edit" class="btn btn-outline-primary rounded-3 font-weight-bold d-inline-flex align-items-center gap-1 px-3 py-2">
                                <i class="mdi mdi-pencil-box-multiple-outline font-18"></i>
                                <span id="btn-matrix-text">Edit Matriks</span>
                            </button>

                            <a href="{{ route($currentPrefix . 'cpl.cpl-bk-add') }}" class="btn btn-primary rounded-3 font-weight-bold d-inline-flex align-items-center gap-1 px-3 py-2 shadow-sm">
                                <i class="mdi mdi-plus-circle font-18"></i>
                                <span>Tambah CPL - BK</span>
                            </a>
                        </div>
                    @endif
                </div>

                <form id="form-matrix-edit" action="{{ route($currentPrefix . 'cpl.cpl-bk-matrix-update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="kurikulum_id" value="{{ request('kurikulum_id') }}">

                    <div class="table-responsive mt-3">
                        <table id="cplBkTable" class="table table-bordered table-hover align-middle w-100 cpl-bk-table mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center col-no" style="width: 50px;">No</th>
                                    <th class="py-3 px-3">Kode BK</th>
                                    <th class="py-3 px-3">Nama Bahan Kajian</th>
                                    <th class="py-3 px-3 text-center" style="width: 120px;">Kurikulum</th>
                                    @foreach ($cpls as $cpl)
                                        <th class="py-3 px-2 text-center" style="min-width: 85px;" title="{{ $cpl->kode }}: {{ $cpl->judul }}">
                                            <span class="badge bg-primary-soft text-primary font-12 px-2 py-1 rounded-2">{{ $cpl->kode }}</span>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bks as $index => $bk)
                                    <tr>
                                        <td class="text-center font-weight-medium text-muted py-3 px-3">{{ $index + 1 }}</td>
                                        <td class="py-3 px-3 fw-bold text-dark font-13">{{ $bk->kode }}</td>
                                        <td class="py-3 px-3 font-13 text-secondary">{{ $bk->nama }}</td>
                                        <td class="text-center py-3 px-3 font-13">
                                            <span class="badge bg-light text-dark border px-2 py-1 rounded-1 fw-semibold">
                                                {{ $bk->kurikulum->tahun ?? '-' }}
                                            </span>
                                        </td>
                                        @foreach ($cpls as $cpl)
                                            @php
                                                $isChecked = $bk->cpl->contains('id', $cpl->id);
                                            @endphp
                                            <td class="text-center matrix-cell {{ $isChecked ? 'is-mapped' : '' }}"
                                                data-bk="{{ $bk->id }}"
                                                data-cpl="{{ $cpl->id }}">
                                                
                                                <!-- View Mode Icon -->
                                                <div class="cell-view-mode">
                                                    @if ($isChecked)
                                                        <i class="mdi mdi-check-circle text-primary font-20"></i>
                                                    @else
                                                        <span class="text-muted opacity-25 font-16">-</span>
                                                    @endif
                                                </div>

                                                <!-- Edit Mode Toggle Input -->
                                                <div class="cell-edit-mode d-none">
                                                    <input type="checkbox"
                                                           class="form-check-input matrix-checkbox border-slate cursor-pointer"
                                                           name="matrix[{{ $bk->id }}][]"
                                                           value="{{ $cpl->id }}"
                                                           {{ $isChecked ? 'checked' : '' }}
                                                           style="width: 20px; height: 20px; cursor: pointer;">
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 3 + count($cpls) }}" class="text-center py-4 text-muted">
                                            <i class="mdi mdi-file-table-outline font-32 d-block mb-1"></i>
                                            Belum ada data pemetaan CPL ke Bahan Kajian.
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

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-4">
            <div class="card-body p-4">
                <h4 class="card-title fw-bold mb-3">Deskripsi</h4>
                <div class="scrollable-descriptions pe-2" style="max-height: 400px; overflow-y: auto;">
                    @foreach ($bks as $bk)
                        <div class="mb-4 p-3 bg-light rounded-3 border-light">
                            <h6 class="fw-bold text-primary mb-1">
                                <span class="badge bg-primary text-white me-2">{{ $bk->kode }}</span>
                                {{ $bk->nama }}
                            </h6>
                            <div class="font-13 mt-2">
                                <strong class="text-dark d-block mb-1">CPL Terkait:</strong>
                                @if ($bk->cpl->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($bk->cpl as $cpl)
                                            <span class="badge bg-white text-dark border px-2 py-1.5 rounded-2 shadow-xs">
                                                <strong class="text-primary">{{ $cpl->kode }}:</strong> {{ $cpl->judul }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted fst-italic">Tidak ada CPL terkait.</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <style>
        .cpl-bk-wrapper .dataTables_wrapper {
            width: 100%;
        }

        .cpl-bk-wrapper .dataTables_wrapper .dataTables_length,
        .cpl-bk-wrapper .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }

        .cpl-bk-wrapper .dataTables_wrapper .dataTables_length label,
        .cpl-bk-wrapper .dataTables_wrapper .dataTables_filter label {
            display: inline-flex;
            align-items: center;
            flex-wrap: nowrap;
            gap: 0.4rem;
            margin-bottom: 0;
            white-space: nowrap;
            font-weight: normal;
        }

        .cpl-bk-wrapper .dataTables_wrapper .dataTables_length {
            float: left;
        }

        .cpl-bk-wrapper .dataTables_wrapper .dataTables_filter {
            float: right;
        }

        .cpl-bk-wrapper .dataTables_wrapper .dataTables_info {
            float: left;
            padding-top: 0.85rem;
        }

        .cpl-bk-wrapper .dataTables_wrapper .dataTables_paginate {
            float: right;
            padding-top: 0.5rem;
        }

        .cpl-bk-wrapper .dataTables_wrapper::after {
            content: '';
            display: block;
            clear: both;
        }

        .cpl-bk-table {
            border-collapse: separate !important;
            border-spacing: 0 !important;
            border: 1px solid #e2e8f0 !important;
        }

        .cpl-bk-table th {
            border-bottom: 2px solid #e2e8f0 !important;
            border-right: 1px solid rgba(0, 0, 0, 0.04) !important;
        }

        .cpl-bk-table tbody tr {
            border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
        }

        .cpl-bk-table tbody td {
            border-top: 1px solid rgba(0, 0, 0, 0.06) !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06) !important;
            border-right: 1px solid rgba(0, 0, 0, 0.04) !important;
        }

        .cpl-bk-table tbody tr:hover td {
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
            cursor: pointer;
        }

        .editing-active .matrix-cell:hover {
            background-color: rgba(2, 132, 199, 0.15) !important;
        }

        .editing-active .cell-view-mode {
            display: none !important;
        }

        .editing-active .cell-edit-mode {
            display: flex !important;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 576px) {
            .cpl-bk-wrapper .dataTables_wrapper .dataTables_length,
            .cpl-bk-wrapper .dataTables_wrapper .dataTables_filter {
                float: none;
                text-align: left;
            }

            .cpl-bk-wrapper .dataTables_wrapper .dataTables_filter {
                margin-top: 0.5rem;
            }
        }
    </style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const tableEl = $('#cplBkTable');
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
                    { targets: 0, className: 'text-center', width: '50px' },
                    { targets: 1, width: '100px' },
                    @if (count($cpls) > 0)
                    { targets: [{{ implode(',', range(3, 2 + count($cpls))) }}], className: 'text-center', orderable: false }
                    @endif
                ]
            });
        }

        const btnToggleMatrixEdit = document.getElementById('btn-toggle-matrix-edit');
        const btnMatrixText = document.getElementById('btn-matrix-text');
        const matrixBanner = document.getElementById('matrix-edit-banner');
        const matrixSaveBar = document.getElementById('matrix-save-bar');
        const matrixTable = document.getElementById('cplBkTable');

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

        // Click cell in edit mode using event delegation
        $(document).on('click', '.matrix-cell', function (e) {
            if (!isEditMode) return;

            const checkbox = this.querySelector('.matrix-checkbox');
            if (checkbox && e.target !== checkbox) {
                checkbox.checked = !checkbox.checked;
            }

            if (checkbox && checkbox.checked) {
                this.classList.add('is-mapped');
            } else {
                this.classList.remove('is-mapped');
            }
        });

        $(document).on('change', '.matrix-checkbox', function () {
            const cell = this.closest('.matrix-cell');
            if (cell) {
                if (this.checked) {
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