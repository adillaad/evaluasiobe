@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')

    <div class="container-fluid cpl-pl-wrapper">
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
                    <small class="text-secondary">Klik sel mana saja pada tabel di bawah untuk mencentang atau menghapus centang pemetaan CPL ke Profil Lulusan.</small>
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
                        <h4 class="card-title fw-bold mb-1">List Pemetaan CPL-PL</h4>
                        <p class="text-muted small mb-0">Matriks keterhubungan antara Capaian Pembelajaran Lulusan (CPL) dan Profil Lulusan (PL).</p>
                    </div>

                    @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas']))
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" id="btn-toggle-matrix-edit" class="btn btn-outline-primary rounded-3 font-weight-bold d-inline-flex align-items-center gap-1 px-3 py-2">
                                <i class="mdi mdi-pencil-box-multiple-outline font-18"></i>
                                <span id="btn-matrix-text">Edit Matriks</span>
                            </button>

                            <a href="{{ route($currentPrefix . 'cpl.cpl-pl-add') }}" class="btn btn-primary rounded-3 font-weight-bold d-inline-flex align-items-center gap-1 px-3 py-2 shadow-sm">
                                <i class="mdi mdi-plus-circle font-18"></i>
                                <span>Tambah CPL - PL</span>
                            </a>
                        </div>
                    @endif
                </div>

                <p class="text-muted small mb-3">
                    <strong>Profil Lulusan:</strong>
                    @forelse ($profilLulusans as $profilLulusan)
                        <span class="badge bg-light text-dark border ms-1 mb-1">{{ $profilLulusan->namaProfil ?: ($profilLulusan->kode ?: 'Profil Lulusan') }}</span>
                    @empty
                        <span class="text-muted">— belum ada data</span>
                    @endforelse
                </p>

                <form id="form-matrix-edit" action="{{ route($currentPrefix . 'cpl.cpl-pl-matrix-update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="kurikulum_id" value="{{ request('kurikulum_id') }}">

                    <div class="table-responsive">
                        <table id="cplPlTable" class="table table-bordered table-hover align-middle w-100 cpl-pl-table mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center col-no" style="width: 60px;">No</th>
                                    <th class="text-center col-kode" style="width: 100px;">Kode CPL</th>
                                    <th class="text-center col-kurikulum" style="width: 110px;">Kurikulum</th>
                                    @forelse ($profilLulusans as $profilLulusan)
                                        <th class="text-center col-pl" title="{{ $profilLulusan->deskripsi }}" style="white-space: normal; word-wrap: break-word; font-size: 12px; line-height: 1.3;">
                                            {{ $profilLulusan->namaProfil ?: ($profilLulusan->kode ?: 'Profil Lulusan') }}
                                        </th>
                                    @empty
                                        <th class="text-center col-pl">-</th>
                                    @endforelse
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cpls as $index => $cpl)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="text-center fw-semibold">{{ $cpl->kode }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border px-2 py-1 font-12 fw-semibold">
                                                {{ $cpl->kurikulum->tahun ?? '-' }}
                                            </span>
                                        </td>
                                        @foreach ($profilLulusans as $profilLulusan)
                                            @php
                                                $isChecked = $cpl->profilLulusan->contains($profilLulusan->id);
                                            @endphp
                                            <td class="text-center matrix-cell {{ $isChecked ? 'is-mapped' : '' }}"
                                                data-cpl="{{ $cpl->id }}"
                                                data-pl="{{ $profilLulusan->id }}">
                                                
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
                                                           name="matrix[{{ $cpl->id }}][]"
                                                           value="{{ $profilLulusan->id }}"
                                                           {{ $isChecked ? 'checked' : '' }}
                                                           style="width: 20px; height: 20px; cursor: pointer;">
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 2 + max($profilLulusans->count(), 1) }}" class="text-center text-muted py-4">
                                            Belum ada data CPL.
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
                    @foreach ($cpls as $cpl)
                        <div class="mb-3">
                            <p class="mb-1"><strong>{{ $cpl->kode }} :</strong> {{ $cpl->judul }}</p>
                            <p class="mb-1"><strong>Profil Lulusan:</strong></p>
                            @if ($cpl->profilLulusan->isNotEmpty())
                                <ul class="mb-0">
                                    @foreach ($cpl->profilLulusan as $profil)
                                        <li>{{ $profil->kode }} - {{ $profil->deskripsi }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <em>Tidak ada profil lulusan terkait.</em>
                            @endif
                            <hr class="mt-3 mb-0">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <style>
        .cpl-pl-wrapper .dataTables_wrapper {
            width: 100%;
        }

        .cpl-pl-wrapper .dataTables_wrapper .dataTables_length,
        .cpl-pl-wrapper .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }

        .cpl-pl-wrapper .dataTables_wrapper .dataTables_length label,
        .cpl-pl-wrapper .dataTables_wrapper .dataTables_filter label {
            display: inline-flex;
            align-items: center;
            flex-wrap: nowrap;
            gap: 0.4rem;
            margin-bottom: 0;
            white-space: nowrap;
            font-weight: normal;
        }

        .cpl-pl-wrapper .dataTables_wrapper .dataTables_length {
            float: left;
        }

        .cpl-pl-wrapper .dataTables_wrapper .dataTables_filter {
            float: right;
        }

        .cpl-pl-wrapper .dataTables_wrapper .dataTables_info {
            float: left;
            padding-top: 0.85rem;
        }

        .cpl-pl-wrapper .dataTables_wrapper .dataTables_paginate {
            float: right;
            padding-top: 0.5rem;
        }

        .cpl-pl-wrapper .dataTables_wrapper::after {
            content: '';
            display: block;
            clear: both;
        }

        .cpl-pl-table {
            table-layout: fixed;
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
            border: 1px solid #e2e8f0 !important;
        }

        .cpl-pl-table th {
            border-bottom: 2px solid #e2e8f0 !important;
            border-right: 1px solid rgba(0, 0, 0, 0.04) !important;
        }

        .cpl-pl-table tbody tr {
            border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
        }

        .cpl-pl-table tbody td {
            border-top: 1px solid rgba(0, 0, 0, 0.06) !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06) !important;
            border-right: 1px solid rgba(0, 0, 0, 0.04) !important;
        }

        .cpl-pl-table tbody tr:hover td {
            background-color: rgba(2, 132, 199, 0.03) !important;
        }

        .cpl-pl-table thead th,
        .cpl-pl-table tbody td {
            vertical-align: middle;
            padding: 0.65rem 0.5rem;
        }

        .cpl-pl-table .col-no {
            width: 56px;
        }

        .cpl-pl-table .col-kode {
            width: 110px;
        }

        .cpl-pl-table .col-pl {
            width: auto;
            min-width: 72px;
        }

        .cpl-pl-table tbody td {
            text-align: center;
        }

        .cpl-pl-table tbody td:nth-child(2) {
            font-weight: 600;
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
            .cpl-pl-wrapper .dataTables_wrapper .dataTables_length,
            .cpl-pl-wrapper .dataTables_wrapper .dataTables_filter {
                float: none;
                text-align: left;
            }

            .cpl-pl-wrapper .dataTables_wrapper .dataTables_filter {
                margin-top: 0.5rem;
            }
        }
    </style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const tableEl = $('#cplPlTable');
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
                    { targets: 0, className: 'text-center', width: '56px' },
                    { targets: 1, className: 'text-center', width: '110px' },
                    @if ($profilLulusans->count() > 0)
                    { targets: [{{ implode(',', range(2, 1 + $profilLulusans->count())) }}], className: 'text-center', orderable: false }
                    @endif
                ]
            });
        }

        const btnToggleMatrixEdit = document.getElementById('btn-toggle-matrix-edit');
        const btnMatrixText = document.getElementById('btn-matrix-text');
        const matrixBanner = document.getElementById('matrix-edit-banner');
        const matrixSaveBar = document.getElementById('matrix-save-bar');
        const matrixTable = document.getElementById('cplPlTable');

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
                // Collect checked checkboxes from hidden DataTables rows
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
