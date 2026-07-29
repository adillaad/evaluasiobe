@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')
    @if (session()->has('failed'))
        <div class="alert alert-danger" role="alert" id="box">
            <div>{{ session('failed') }}</div>
        </div>
    @elseif (session()->has('success'))
        <div class="alert alert-success" role="alert" id="box">
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <h3 class="px-4 pb-4 fw-bold text-center">Halaman CPL-PL</h3>

    <div class="container-fluid cpl-pl-wrapper">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <h4 class="card-title mb-0">List Pemetaan CPL-PL</h4>
                    @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                        <a href="{{ route($currentPrefix . 'cpl.cpl-pl-add') }}" class="btn btn-primary">
                            Tambah CPL-PL
                        </a>
                    @endif
                </div>

                <p class="text-muted small mb-2 mb-md-3">
                    <strong>Profil Lulusan</strong>
                    @forelse ($profilLulusans as $profilLulusan)
                        <span class="badge bg-light text-dark border ms-1">{{ $profilLulusan->kode }}</span>
                    @empty
                        <span class="text-muted">— belum ada data</span>
                    @endforelse
                </p>

                <div class="table-responsive">
                    <table id="cplPlTable" class="table table-bordered table-hover align-middle w-100 cpl-pl-table mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center col-no">No</th>
                                <th class="text-center col-kode">Kode CPL</th>
                                @forelse ($profilLulusans as $profilLulusan)
                                    <th class="text-center col-pl">{{ $profilLulusan->kode }}</th>
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
                                    @foreach ($profilLulusans as $profilLulusan)
                                        <td class="text-center text-primary">
                                            @if ($cpl->profilLulusan->contains($profilLulusan->id))
                                                <span class="cpl-pl-check">&#10003;</span>
                                            @endif
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
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h4 class="card-title">Deskripsi</h4>
                <div class="scrollable-descriptions" style="max-height: 400px; overflow-y: auto;">
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

        /* Sembunyikan baris header duplikat dari DataTables */
        .cpl-pl-wrapper table.dataTable thead .sorting:before,
        .cpl-pl-wrapper table.dataTable thead .sorting:after,
        .cpl-pl-wrapper table.dataTable thead .sorting_asc:before,
        .cpl-pl-wrapper table.dataTable thead .sorting_asc:after,
        .cpl-pl-wrapper table.dataTable thead .sorting_desc:before,
        .cpl-pl-wrapper table.dataTable thead .sorting_desc:after {
            bottom: 0.55rem;
        }

        .cpl-pl-check {
            display: inline-block;
            width: 1.25rem;
            font-size: 1.1rem;
            font-weight: 700;
            line-height: 1;
            text-align: center;
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
        if (!tableEl.length) return;

        if ($.fn.DataTable.isDataTable(tableEl)) {
            tableEl.DataTable().destroy();
        }

        tableEl.DataTable({
            aaSorting: [],
            autoWidth: false,
            paging: {{ $cpls->count() > 10 ? 'true' : 'false' }},
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
    });
</script>
@endpush
