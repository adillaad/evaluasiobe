@php
    $isAptikom = auth()->check() && auth()->user()->prodi ? (bool) auth()->user()->prodi->is_aptikom : true;
@endphp

@extends('dosen.template')
@section('content')

<style>
    /* Styling Filter Tab Samain dengan Kaprodi (.cpl-nav-pills) */
    .filter-tab-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 14px 18px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }

    .tab-filter-kurikulum {
        color: #475569 !important;
        background-color: #f8fafc !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 10px !important;
        padding: 11px 24px !important;
        font-size: 13.5px !important;
        font-weight: 600 !important;
        transition: all 0.2s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        cursor: pointer;
    }

    .tab-filter-kurikulum:hover {
        background-color: #f1f5f9 !important;
        color: #1e293b !important;
    }

    .tab-filter-kurikulum .badge-count {
        background-color: #e2e8f0;
        color: #334155;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        margin-left: 6px;
        transition: all 0.2s ease;
    }

    @if ($isAptikom)
        /* ── APTIKOM Theme (Ocean Blue #006199 Accent) ── */
        .tab-filter-kurikulum.active {
            background: linear-gradient(135deg, #006199 0%, #004c78 100%) !important;
            border-color: #006199 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(0, 97, 153, 0.28) !important;
        }
        .tab-filter-kurikulum.active .badge-count {
            background-color: rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
        }
    @else
        /* ── NON-APTIKOM Theme (Royal Blue #2664F5 Accent) ── */
        .tab-filter-kurikulum.active {
            background: linear-gradient(135deg, #2664F5 0%, #1d52cc 100%) !important;
            border-color: #2664F5 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(38, 100, 245, 0.28) !important;
        }
        .tab-filter-kurikulum.active .badge-count {
            background-color: rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
        }
    @endif

    /* Center alignment untuk kolom No, Kurikulum, dan Kode CPL */
    #tableCplmkList thead th.text-center {
        text-align: center !important;
    }
    #tableCplmkList tbody td.text-center {
        text-align: center !important;
    }
</style>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="card-header py-3 mb-3">
                    <h4 class="mb-0 fw-bold">Daftar Pemetaan CPL ke Mata Kuliah</h4>
                </div>

                @php
                    $userOtoritas = $userOtoritas ?? (auth()->user()->otoritas->otoritas ?? 'Dosen');
                @endphp



                <x-filter-form
                    :universities="$universities"
                    :faculties="$faculties"
                    :programs="$programs"
                />

                @php
                    $uniqueCplmks = $cplmks->unique(function ($item) {
                        return $item->mk_kode . '_' . $item->cpl_id;
                    });

                    $kurikulumGroups = $uniqueCplmks->groupBy(function($item) use ($cpls, $mks) {
                        foreach ($cpls as $cpl) {
                            if ($item->cpl_id == $cpl->id && isset($cpl->kurikulum->tahun)) {
                                return $cpl->kurikulum->tahun;
                            }
                        }
                        foreach ($mks as $mk) {
                            if ($item->mk_kode == $mk->kode && isset($mk->kurikulum->tahun)) {
                                return $mk->kurikulum->tahun;
                            }
                        }
                        return 'Tanpa Kurikulum';
                    })->sortKeysDesc();
                @endphp

                @if($kurikulumGroups->isNotEmpty())
                    <div class="filter-tab-card mb-4 mt-2">
                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-1">
                                <i class="ti-filter me-1 text-secondary" style="font-size: 0.85rem;"></i>
                                <span class="text-uppercase fw-bold text-secondary" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                    PILIH KURIKULUM:
                                </span>
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem;">Klik tab Kurikulum untuk memfilter daftar pemetaan</small>
                        </div>
                        <div class="d-flex flex-row flex-nowrap gap-2 overflow-auto pb-1" id="kurikulumTabContainer" style="scrollbar-width: thin;">
                            <button type="button" class="btn tab-filter-kurikulum active" data-kurikulum="all">
                                <i class="ti-layout-grid2 me-1"></i>Semua Kurikulum
                                <span class="badge-count">{{ $uniqueCplmks->count() }}</span>
                            </button>
                            @foreach($kurikulumGroups as $tahun => $groupCpls)
                                <button type="button" class="btn tab-filter-kurikulum" data-kurikulum="{{ $tahun }}">
                                    Kurikulum {{ $tahun }}
                                    <span class="badge-count">{{ $groupCpls->count() }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover dataTable" id="tableCplmkList">
                        <thead class="bg-light">
                            <tr class="align-middle">
                                <th style="width: 60px;" class="text-center">No</th>
                                <th style="width: 150px;">Kode MK</th>
                                <th>Nama MK</th>
                                <th style="width: 140px;" class="text-center">Kurikulum</th>
                                <th style="width: 130px;" class="text-center">Kode CPL</th>
                                @if (in_array(auth()->user()->otoritas->otoritas, ['Wakil Rektor', 'Wakil Dekan']))
                                    <th>Prodi</th>
                                @endif
                                @if (in_array(auth()->user()->otoritas->otoritas, ['Wakil Rektor']))
                                    <th>Fakultas</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp

                            @forelse ($uniqueCplmks as $cplmk)
                                @php
                                    $mkKode = $cplmk->mk_kode;
                                    $nama_mk = '-';
                                    $nama_prodi = '-';
                                    $nama_fakultas = '-';
                                    $kode_cpl = '-';
                                    $tahunKurikulum = '-';

                                    foreach ($mks as $mk) {
                                        if ($mkKode == $mk->kode) {
                                            $nama_mk = $mk->nama;
                                            if (isset($mk->kurikulum->tahun)) {
                                                $tahunKurikulum = $mk->kurikulum->tahun;
                                            }
                                            break;
                                        }
                                    }

                                    foreach ($cpls as $cpl) {
                                        if ($cplmk->cpl_id == $cpl->id) {
                                            $kode_cpl = $cpl->kode;
                                            if (isset($cpl->kurikulum->tahun)) {
                                                $tahunKurikulum = $cpl->kurikulum->tahun;
                                            }
                                            break;
                                        }
                                    }

                                    foreach ($rpss as $rps) {
                                        if ($rps->kode_mk == $mkKode) {
                                            $nama_prodi = $rps->prodi->nama ?? '-';
                                            $nama_fakultas = $rps->prodi->fakultas->nama ?? '-';
                                            break;
                                        }
                                    }
                                @endphp
                                <tr data-kurikulum="{{ $tahunKurikulum }}" class="row-cplmk">
                                    <td class="text-center align-middle">{{ $no++ }}</td>
                                    <td class="align-middle fw-bold">{{ $mkKode }}</td>
                                    <td class="align-middle">{{ $nama_mk }}</td>
                                    <td class="align-middle text-center">{{ $tahunKurikulum }}</td>
                                    <td class="align-middle text-center fw-bold">{{ $kode_cpl }}</td>
                                    @if (in_array(auth()->user()->otoritas->otoritas, ['Wakil Rektor', 'Wakil Dekan']))
                                        <td class="align-middle">{{ $nama_prodi }}</td>
                                    @endif
                                    @if (in_array(auth()->user()->otoritas->otoritas, ['Wakil Rektor']))
                                        <td class="align-middle">{{ $nama_fakultas }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Data pemetaan CPLMK belum ada.</td>
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
                // Tab filter per Kurikulum
                $('.tab-filter-kurikulum').on('click', function() {
                    var targetKurikulum = $(this).data('kurikulum').toString();

                    // Update tombol active
                    $('.tab-filter-kurikulum').removeClass('active');
                    $(this).addClass('active');

                    // Filter DataTables
                    if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tableCplmkList')) {
                        var dt = $('#tableCplmkList').DataTable();
                        if (targetKurikulum === 'all') {
                            dt.column(3).search('').draw();
                        } else {
                            dt.column(3).search(targetKurikulum).draw();
                        }
                    } else if ($.fn.DataTable && $.fn.DataTable.isDataTable('.dataTable')) {
                        var dt = $('.dataTable').DataTable();
                        if (targetKurikulum === 'all') {
                            dt.column(3).search('').draw();
                        } else {
                            dt.column(3).search(targetKurikulum).draw();
                        }
                    } else {
                        // Fallback filter tabel HTML biasa
                        $('.row-cplmk').each(function() {
                            var rowKurikulum = $(this).data('kurikulum').toString();
                            if (targetKurikulum === 'all' || rowKurikulum === targetKurikulum) {
                                $(this).show();
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
