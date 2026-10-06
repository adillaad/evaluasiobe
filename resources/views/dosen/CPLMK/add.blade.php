@extends('dosen.template')
@section('content')

@php
    $isAptikom = auth()->check() && auth()->user()->prodi ? (bool) auth()->user()->prodi->is_aptikom : true;
@endphp

<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #e9ecef;
        border: 1px solid #ced4da;
        color: #495057;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .btn-light, .btn-secondary {
        background-color: #e2e8f0 !important;
        border: 1px solid #cbd5e1 !important;
        color: #334155 !important;
        font-weight: 600 !important;
        transition: all 0.2s ease !important;
    }
    .btn-light:hover, .btn-secondary:hover {
        background-color: #cbd5e1 !important;
        border-color: #94a3b8 !important;
        color: #0f172a !important;
    }

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
        .btn-primary {
            background: linear-gradient(135deg, #006199 0%, #004c78 100%) !important;
            border-color: #006199 !important;
            color: #ffffff !important;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background: #004c78 !important;
            border-color: #004c78 !important;
            color: #ffffff !important;
        }
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
        .btn-primary {
            background: linear-gradient(135deg, #2664F5 0%, #1d52cc 100%) !important;
            border-color: #2664F5 !important;
            color: #ffffff !important;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background: #1d52cc !important;
            border-color: #1d52cc !important;
            color: #ffffff !important;
        }
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
</style>

<div class="container-fluid mb-4">
    
    {{-- CARD 1: FORM MAPPING --}}
    <div class="card mb-4">
        <div class="card-header py-3">
            <h4 class="mb-0 fw-bold">Pemetaan CPL ke Mata Kuliah</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('dosen.cplmk-store') }}" method="post">
                @csrf
                
                {{-- Bagian Mata Kuliah --}}
                <div class="mb-4">
                    <label for="mataKuliah" class="form-label">Pilih Mata Kuliah <span class="text-danger">*</span></label>
                    <select id="mataKuliah" name="kode_mk" class="form-select form-select-sm" required>
                        <option value="" selected disabled>-- Pilih Mata Kuliah --</option>
                        @foreach ($mks as $mk)
                            <option value="{{$mk->kode}}">{{$mk->kode}} - {{$mk->nama}}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Bagian Pilih CPL (Multiple) --}}
                <div class="mb-4">
                    <label for="id_cpl" class="form-label">Pilih CPL Prodi <span class="text-danger">*</span></label>
                    <select name="id_cpl[]" id="id_cpl" class="js-example-basic-multiple form-select form-select-sm" multiple="multiple" required>
                        @foreach ($cpls as $cpl)
                            <option value="{{$cpl->id}}">
                                {{$cpl->kurikulum->tahun ?? '-'}} - {{$cpl->kode}} - {{ Str::limit($cpl->judul, 80) }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text mt-2">
                        <i class="ti-info-alt me-1"></i>Anda dapat memilih lebih dari satu CPL untuk mata kuliah ini.
                    </div>
                </div>

                <div class="border-top pt-3">
                    <button type="reset" class="btn btn-secondary me-2">Batal</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>

    {{-- CARD 2: DAFTAR CPL PRODI DENGAN TAB FILTER KURIKULUM SAMA PERSIS REFERENSI --}}
    <div class="card">
        <div class="card-header py-3">
            <h4 class="mb-0 fw-bold">Daftar CPL Prodi</h4>
        </div>
        <div class="card-body">

            @php
                $kurikulumGroups = $cpls->groupBy(function($item) {
                    return $item->kurikulum->tahun ?? 'Tanpa Kurikulum';
                })->sortKeysDesc();
            @endphp

            @if($kurikulumGroups->isNotEmpty())
                <div class="filter-tab-card mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-1">
                            <i class="ti-filter me-1 text-secondary" style="font-size: 0.85rem;"></i>
                            <span class="text-uppercase fw-bold text-secondary" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                PILIH KURIKULUM:
                            </span>
                        </div>
                        <small class="text-muted" style="font-size: 0.75rem;">Klik tab Kurikulum untuk memfilter daftar CPL</small>
                    </div>
                    <div class="d-flex flex-row flex-nowrap gap-2 overflow-auto pb-1" id="kurikulumTabContainer" style="scrollbar-width: thin;">
                        <button type="button" class="btn tab-filter-kurikulum active" data-kurikulum="all">
                            <i class="ti-layout-grid2 me-1"></i>Semua Kurikulum
                            <span class="badge-count">{{ $cpls->count() }}</span>
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
                <table class="table table-hover dataTable" id="tableCplProdi">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Aspek</th>
                            <th>Nomor</th>
                            <th>Kurikulum</th>
                            <th>Kode</th>
                            <th>Judul</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cpls as $cpl)
                            @php
                                $tahunKurikulum = $cpl->kurikulum->tahun ?? 'Tanpa Kurikulum';
                            @endphp
                            <tr data-kurikulum="{{ $tahunKurikulum }}" class="row-cpl">
                                <td class="py-3 iteration-col">{{$loop->iteration}}</td>
                                <td>{{$cpl->aspek}}</td>
                                <td>{{$cpl->nomor}}</td>
                                <td>{{$tahunKurikulum}}</td>
                                <td class="fw-bold">{{$cpl->kode}}</td>
                                <td>{{$cpl->judul}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
        <script src="{{ asset('/assets/template/vendors/select2/select2.min.js')}}"></script>
        <script src="{{ asset('/assets/template/js/select2.js')}}"></script>
        <script>
            $(document).ready(function() {
                var i = 0;
                $("#dynamic-ar-sik").click(function() {
                    ++i;
                    $("#dynamicAddRemoveSik").append('<div class="form-group row clone"><div class="col-2"><div class="form-group"><label>Kurikulum <span class="text-danger">*</span></label><input type="text"class="form-control" name="kurikulum[' + i +
                        ']"placeholder="Kurikulum" autocomplete="off"></div></div><div class="col-3"><div class="form-group"><label>Kode <span class="text-danger">*</span></label><div class="input-group mb-2"><div class="input-group-prepend"><span class="input-group-text">S</span></div><input type="text" class="form-control" name="kode[' + i +
                        ']" placeholder="Nomor" autocomplete="off"></div></div></div><div class="col-5"><div class="form-group"><label>Judul <span class="text-danger">*</span></label><input type="text" class="form-control"name="judul[' + i +
                        ']" placeholder="Judul" autocomplete="off"></div></div><input hidden type="text" name="aspek" value="Keterampilan"><div class="col-2"><label>Action</label><div class="form-group"><button type="button" class="btn btn-sm btn-danger remove-input-field-sik">Delete</button></div></div></div>'
                    );
                });
                $(document).on('click', '.remove-input-field-sik', function() {
                    $(this).parents('.clone').remove();
                });

                // Tab filter per Kurikulum dengan gaya Sesuai Referensi Gambar & Tema Aptikom/Non-Aptikom
                $('.tab-filter-kurikulum').on('click', function() {
                    var targetKurikulum = $(this).data('kurikulum').toString();

                    // Update tampilan active tab
                    $('.tab-filter-kurikulum').removeClass('active');
                    $(this).addClass('active');

                    // Filter tabel DataTables atau HTML biasa
                    if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tableCplProdi')) {
                        var dt = $('#tableCplProdi').DataTable();
                        if (targetKurikulum === 'all') {
                            dt.column(3).search('').draw();
                        } else {
                            dt.column(3).search('^' + targetKurikulum + '$', true, false).draw();
                        }
                    } else if ($.fn.DataTable && $.fn.DataTable.isDataTable('.dataTable')) {
                        var dt = $('.dataTable').DataTable();
                        if (targetKurikulum === 'all') {
                            dt.column(3).search('').draw();
                        } else {
                            dt.column(3).search('^' + targetKurikulum + '$', true, false).draw();
                        }
                    } else {
                        var count = 1;
                        $('.row-cpl').each(function() {
                            var rowKurikulum = $(this).data('kurikulum').toString();
                            if (targetKurikulum === 'all' || rowKurikulum === targetKurikulum) {
                                $(this).show();
                                $(this).find('.iteration-col').text(count++);
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
