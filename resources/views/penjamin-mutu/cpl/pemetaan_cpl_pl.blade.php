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

                    @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Koordinator Program Studi', 'Kepala Program Studi', 'Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas']))
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
                    <strong>Daftar CPL:</strong>
                    @forelse ($cpls as $cplItem)
                        <span class="badge bg-light text-dark border ms-1 mb-1">{{ $cplItem->kode }} ({{ $cplItem->kurikulum->tahun ?? '-' }})</span>
                    @empty
                        <span class="text-muted">— belum ada data</span>
                    @endforelse
                </p>

                @php
                    $unmetPls = [];
                    foreach ($profilLulusans as $plCheck) {
                        $sumBobot = 0;
                        foreach ($cpls as $cplCheck) {
                            $mapped = $cplCheck->profilLulusan->firstWhere('id', $plCheck->id);
                            if ($mapped && $mapped->pivot->bobot !== null) {
                                $sumBobot += (float)$mapped->pivot->bobot;
                            }
                        }
                        $sumBobotFmt = round($sumBobot, 2);
                        if ($sumBobotFmt < 100) {
                            $unmetPls[] = [
                                'nama' => $plCheck->namaProfil ?: ($plCheck->kode ?: 'Profil Lulusan'),
                                'total' => $sumBobotFmt == (int)$sumBobotFmt ? (int)$sumBobotFmt : $sumBobotFmt,
                                'kurang' => round(100 - $sumBobotFmt, 2)
                            ];
                        }
                    }
                @endphp

                @if(count($unmetPls) > 0)
                    <div class="alert alert-info border-0 shadow-sm rounded-4 mb-3 p-3 d-flex align-items-center gap-3" style="background-color: #f0f7ff; border-left: 4px solid #0284c7 !important;">
                        <div class="badge bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-information-outline font-20"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-primary">Informasi: Total Bobot {{ count($unmetPls) }} Profil Lulusan Belum Mencapai 100%</h6>
                            <div class="small text-secondary">
                                @foreach($unmetPls as $unmet)
                                    <span class="badge bg-white text-dark border me-1 mb-1">
                                        <strong>{{ $unmet['nama'] }}</strong>: Total <strong>{{ $unmet['total'] }}%</strong> (Kurang <strong>{{ $unmet['kurang'] }}%</strong>)
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-3 p-3 d-flex align-items-center gap-2" style="background-color: #f0fdf4;">
                        <i class="mdi mdi-check-circle text-success font-20"></i>
                        <span class="text-success small fw-semibold">Semua Profil Lulusan telah memiliki total bobot pas 100%.</span>
                    </div>
                @endif

                <form id="form-matrix-edit" action="{{ route($currentPrefix . 'cpl.cpl-pl-matrix-update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="kurikulum_id" value="{{ request('kurikulum_id') }}">

                    <div class="table-responsive">
                        <table id="cplPlTable" class="table table-bordered table-hover align-middle w-100 cpl-pl-table mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center col-no" style="width: 50px;">No</th>
                                    <th class="text-start col-pl" style="min-width: 240px; width: 280px;">Profil Lulusan (PL)</th>
                                    @forelse ($cpls as $cpl)
                                        <th class="text-center col-cpl" title="{{ $cpl->judul }}" style="width: 95px; min-width: 85px; white-space: normal; font-size: 12px; line-height: 1.3;">
                                            <div class="fw-bold">{{ $cpl->kode }}</div>
                                            <div class="text-muted font-11 fw-normal">{{ $cpl->kurikulum->tahun ?? '' }}</div>
                                        </th>
                                    @empty
                                        <th class="text-center col-cpl">-</th>
                                    @endforelse
                                    <th class="text-center col-total" style="width: 175px; min-width: 175px;">Total Bobot</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($profilLulusans as $index => $profilLulusan)
                                    @php
                                        $totalBobotPL = 0;
                                        foreach ($cpls as $cpl) {
                                            $mappedProfil = $cpl->profilLulusan->firstWhere('id', $profilLulusan->id);
                                            if ($mappedProfil && $mappedProfil->pivot->bobot !== null) {
                                                $totalBobotPL += (float)$mappedProfil->pivot->bobot;
                                            }
                                        }
                                        $totalBobotPL = round($totalBobotPL, 2);
                                        $formattedTotal = ($totalBobotPL == (int)$totalBobotPL) ? (int)$totalBobotPL : $totalBobotPL;
                                        $isLessThan100 = ($totalBobotPL < 100);
                                        $isExact100 = (abs($totalBobotPL - 100) < 0.01);
                                    @endphp
                                    <tr>
                                        <td class="text-center align-middle">{{ $index + 1 }}</td>
                                        <td class="text-start align-middle">
                                            <div class="fw-bold text-dark text-wrap text-break" style="min-width: 200px; max-width: 320px; line-height: 1.4; word-break: break-word;">
                                                {{ $profilLulusan->namaProfil ?: ($profilLulusan->kode ?: 'Profil Lulusan') }}
                                            </div>
                                        </td>
                                        @foreach ($cpls as $cpl)
                                            @php
                                                $mappedProfil = $cpl->profilLulusan->firstWhere('id', $profilLulusan->id);
                                                $isChecked = !is_null($mappedProfil);
                                                $bobotRaw = $isChecked ? $mappedProfil->pivot->bobot : null;
                                                $formattedBobot = null;
                                                if ($bobotRaw !== null && $bobotRaw !== '') {
                                                    $num = (float)$bobotRaw;
                                                    $formattedBobot = ($num == (int)$num) ? (int)$num : $num;
                                                }
                                            @endphp
                                            <td class="text-center matrix-cell {{ $isChecked ? 'is-mapped' : '' }}"
                                                data-cpl="{{ $cpl->id }}"
                                                data-pl="{{ $profilLulusan->id }}">
                                                
                                                <!-- View Mode Icon & Bobot -->
                                                <div class="cell-view-mode">
                                                    @if ($isChecked)
                                                        <div class="d-flex align-items-center justify-content-center">
                                                            <span class="badge bg-primary-soft text-primary fw-bold font-12 px-2 py-1 rounded-pill d-inline-flex align-items-center gap-1">
                                                                <i class="mdi mdi-check-circle font-14"></i>
                                                                <span>{{ $formattedBobot !== null ? $formattedBobot . '%' : '' }}</span>
                                                            </span>
                                                        </div>
                                                    @else
                                                        <span class="text-muted opacity-25 font-16">-</span>
                                                    @endif
                                                </div>

                                                <!-- Edit Mode Inputs (Checkbox + Bobot Input) -->
                                                <div class="cell-edit-mode d-none align-items-center justify-content-center gap-2">
                                                    <input type="checkbox"
                                                           class="form-check-input matrix-checkbox border-slate cursor-pointer m-0"
                                                           name="matrix[{{ $cpl->id }}][]"
                                                           value="{{ $profilLulusan->id }}"
                                                           {{ $isChecked ? 'checked' : '' }}
                                                           style="width: 18px; height: 18px; cursor: pointer;">
                                                    
                                                    <input type="number"
                                                           class="form-control form-control-sm matrix-bobot-input text-center p-1"
                                                           name="bobot[{{ $cpl->id }}][{{ $profilLulusan->id }}]"
                                                           placeholder="%"
                                                           step="any"
                                                           min="0"
                                                           max="100"
                                                           value="{{ $isChecked && $formattedBobot !== null ? $formattedBobot : '' }}"
                                                           {{ $isChecked ? '' : 'disabled' }}
                                                           style="width: 60px; height: 30px; font-size: 12px;">
                                                </div>
                                            </td>
                                        @endforeach
                                        <td class="text-center align-middle">
                                            <div class="total-bobot-badge d-flex justify-content-center align-items-center">
                                                @if ($isExact100)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1.5 rounded-pill font-11 fw-bold d-inline-flex align-items-center gap-1 text-nowrap">
                                                        <i class="mdi mdi-check-decagram"></i> 100% (Sesuai)
                                                    </span>
                                                @elseif ($isLessThan100)
                                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1.5 rounded-pill font-11 fw-bold d-inline-flex align-items-center gap-1 text-nowrap"
                                                          title="Total bobot belum mencapai 100%">
                                                        <i class="mdi mdi-information-outline"></i> {{ $formattedTotal }}% (Kurang {{ round(100 - $formattedTotal, 2) }}%)
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1.5 rounded-pill font-11 fw-bold d-inline-flex align-items-center gap-1 text-nowrap">
                                                        <i class="mdi mdi-alert-octagon"></i> {{ $formattedTotal }}% (Lebih {{ round($formattedTotal - 100, 2) }}%)
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 3 + max($cpls->count(), 1) }}" class="text-center text-muted py-4">
                                            Belum ada data Profil Lulusan.
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
                <h4 class="card-title fw-bold mb-3">Deskripsi Profil Lulusan</h4>
                <div class="scrollable-descriptions pe-2" style="max-height: 400px; overflow-y: auto;">
                    @foreach ($profilLulusans as $profil)
                        <div class="mb-3">
                            <p class="mb-1"><strong>{{ $profil->namaProfil ?: ($profil->kode ?: 'Profil Lulusan') }} :</strong> {{ $profil->deskripsi }}</p>
                            <p class="mb-1"><strong>Pemetaan CPL:</strong></p>
                            @php
                                $mappedCpls = $cpls->filter(fn($c) => $c->profilLulusan->contains('id', $profil->id));
                            @endphp
                            @if ($mappedCpls->isNotEmpty())
                                <ul class="mb-0">
                                    @foreach ($mappedCpls as $cplItem)
                                        @php
                                            $pItem = $cplItem->profilLulusan->firstWhere('id', $profil->id);
                                            $bRaw = $pItem ? $pItem->pivot->bobot : null;
                                            $bFmt = null;
                                            if ($bRaw !== null && $bRaw !== '') {
                                                $num = (float)$bRaw;
                                                $bFmt = ($num == (int)$num) ? (int)$num : $num;
                                            }
                                        @endphp
                                        <li>
                                            {{ $cplItem->kode }} - {{ $cplItem->judul }}
                                            @if ($bFmt !== null)
                                                <span class="badge bg-light text-primary border ms-1 font-11">Bobot: {{ $bFmt }}%</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <em>Tidak ada CPL terkait.</em>
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
            table-layout: auto !important;
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
            border: 1px solid #e2e8f0 !important;
        }

        .cpl-pl-table th {
            border-bottom: 2px solid #e2e8f0 !important;
            border-right: 1px solid rgba(0, 0, 0, 0.04) !important;
            vertical-align: middle !important;
        }

        .cpl-pl-table tbody tr {
            border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
        }

        .cpl-pl-table tbody td {
            border-top: 1px solid rgba(0, 0, 0, 0.06) !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06) !important;
            border-right: 1px solid rgba(0, 0, 0, 0.04) !important;
            white-space: normal !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            vertical-align: middle !important;
        }

        .cpl-pl-table tbody tr:hover td {
            background-color: rgba(2, 132, 199, 0.03) !important;
        }

        .cpl-pl-table thead th,
        .cpl-pl-table tbody td {
            padding: 0.75rem 0.65rem;
        }

        .cpl-pl-table .col-no {
            width: 50px !important;
            min-width: 50px !important;
        }

        .cpl-pl-table .col-pl {
            min-width: 240px !important;
            width: 280px !important;
            white-space: normal !important;
            word-break: break-word !important;
        }

        .cpl-pl-table .col-cpl {
            min-width: 85px !important;
            width: 95px !important;
        }

        .cpl-pl-table .col-total {
            width: 175px !important;
            min-width: 175px !important;
        }

        .cpl-pl-table tbody td {
            text-align: center;
        }

        .cpl-pl-table tbody td:nth-child(2),
        .cpl-pl-table tbody td.text-start {
            text-align: left !important;
            font-weight: 600;
            white-space: normal !important;
            word-break: break-word !important;
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
                    { targets: 0, className: 'text-center', width: '50px' },
                    { targets: 1, className: 'text-start', width: '280px' },
                    @if ($cpls->count() > 0)
                    { targets: [{{ implode(',', range(2, 1 + $cpls->count())) }}], className: 'text-center', orderable: false, width: '95px' },
                    @endif
                    { targets: {{ 2 + $cpls->count() }}, className: 'text-center', width: '175px', orderable: false }
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

        function updateRowTotal(tr) {
            let total = 0;
            $(tr).find('.matrix-checkbox:checked').each(function() {
                let cell = $(this).closest('.matrix-cell');
                let val = parseFloat(cell.find('.matrix-bobot-input').val()) || 0;
                total += val;
            });
            total = Math.round(total * 100) / 100;
            let badgeEl = $(tr).find('.total-bobot-badge');
            
            if (Math.abs(total - 100) < 0.01) {
                badgeEl.html('<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1.5 rounded-pill font-12 fw-bold d-inline-flex align-items-center gap-1"><i class="mdi mdi-check-decagram"></i> 100% (Sesuai)</span>');
            } else if (total < 100) {
                let kurang = Math.round((100 - total) * 100) / 100;
                badgeEl.html('<span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1.5 rounded-pill font-12 fw-bold d-inline-flex align-items-center gap-1" title="Total bobot belum mencapai 100%"><i class="mdi mdi-information-outline"></i> ' + total + '% (Kurang ' + kurang + '%)</span>');
            } else {
                let lebih = Math.round((total - 100) * 100) / 100;
                badgeEl.html('<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1.5 rounded-pill font-12 fw-bold d-inline-flex align-items-center gap-1"><i class="mdi mdi-alert-octagon"></i> ' + total + '% (Lebih ' + lebih + '%)</span>');
            }
        }

        // Live calculation when inputs/checkboxes change
        $(document).on('input change', '.matrix-bobot-input, .matrix-checkbox', function() {
            updateRowTotal($(this).closest('tr'));
        });

        // Click cell in edit mode using event delegation
        $(document).on('click', '.matrix-cell', function (e) {
            if (!isEditMode) return;

            // If user clicked inside the bobot input field, do not toggle checkbox
            if ($(e.target).is('.matrix-bobot-input')) return;

            const checkbox = this.querySelector('.matrix-checkbox');
            if (checkbox && e.target !== checkbox) {
                checkbox.checked = !checkbox.checked;
                $(checkbox).trigger('change');
            }
        });

        $(document).on('change', '.matrix-checkbox', function () {
            const cell = $(this).closest('.matrix-cell');
            const bobotInput = cell.find('.matrix-bobot-input');
            if (this.checked) {
                cell.addClass('is-mapped');
                bobotInput.prop('disabled', false);
            } else {
                cell.removeClass('is-mapped');
                bobotInput.prop('disabled', true).val('');
            }
        });

        // Form submit handler to collect input from all DataTables pages
        $('#form-matrix-edit').on('submit', function (e) {
            if (dataTableInst) {
                const form = this;
                dataTableInst.$('input.matrix-checkbox:checked').each(function () {
                    if (!$.contains(document.body, this)) {
                        $(form).append(
                            $('<input>')
                                .attr('type', 'hidden')
                                .attr('name', this.name)
                                .val(this.value)
                        );

                        const cell = $(this).closest('.matrix-cell');
                        const bobotInput = cell.find('.matrix-bobot-input');
                        if (bobotInput.length && bobotInput.val() !== '') {
                            $(form).append(
                                $('<input>')
                                    .attr('type', 'hidden')
                                    .attr('name', bobotInput.attr('name'))
                                    .val(bobotInput.val())
                            );
                        }
                    }
                });
            }
        });
    });
</script>
@endpush
