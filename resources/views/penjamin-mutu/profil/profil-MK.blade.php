@php
    $userOtoritas = auth()->user()->otoritas->otoritas ?? '';

    switch ($userOtoritas) {
        case 'Kepala Program Studi':
            $currentPrefix = 'kepala-program-studi.';
            $baseTemplate = 'penjamin-mutu.template';
            break;
        case 'Penjamin Mutu Universitas':
            $currentPrefix = 'penjamin-mutu.universitas.';
            $baseTemplate = 'penjamin-mutu.template';
            break;
        case 'Penjamin Mutu Fakultas':
            $currentPrefix = 'penjamin-mutu.fakultas.';
            $baseTemplate = 'penjamin-mutu.template';
            break;
        case 'Penjamin Mutu Program Studi':
            $currentPrefix = 'penjamin-mutu.program-studi.';
            $baseTemplate = 'penjamin-mutu.template';
            break;
        default:
            $currentPrefix = 'admin.';
            $baseTemplate = 'penjamin-mutu.template';
            break;
    }

    $selectedProdiId = request('prodi_id');
    if ($selectedProdiId) {
        $isAptikom = (bool) \Illuminate\Support\Facades\DB::table('prodi')->where('id', $selectedProdiId)->value('is_aptikom');
    } else {
        $isAptikom = auth()->check() && auth()->user()->prodi ? (bool) auth()->user()->prodi->is_aptikom : true;
    }
@endphp

@extends($baseTemplate)

@section('content')

    {{-- ── Flash messages ─────────────────────────────────────────── --}}
    @if (session()->has('failed'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            {{ session('failed') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <style>
        .custom-kurikulum-tabs {
            border-bottom: 2px solid #e2e8f0;
            gap: 10px;
            padding-bottom: 10px;
            margin-bottom: 20px !important;
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            white-space: nowrap !important;
            -webkit-overflow-scrolling: touch;
        }
        .custom-kurikulum-tabs::-webkit-scrollbar {
            height: 6px;
        }
        .custom-kurikulum-tabs::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        .custom-kurikulum-tabs::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .custom-kurikulum-tabs::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .custom-kurikulum-tabs .nav-item {
            flex-shrink: 0 !important;
        }
        .custom-kurikulum-tabs .nav-link {
            font-weight: 600;
            font-size: 13.5px;
            color: #475569;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 9px 20px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            white-space: nowrap !important;
        }

        @if ($isAptikom)
            /* ── APTIKOM Theme (Sky Blue Accent) ── */
            .custom-kurikulum-tabs .nav-link:hover {
                color: #0284c7;
                background: #f0f9ff;
                border-color: #38bdf8;
            }
            .custom-kurikulum-tabs .nav-link.active {
                color: #ffffff !important;
                background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
                border-color: #0284c7 !important;
                box-shadow: 0 4px 12px rgba(14, 165, 233, 0.28) !important;
            }
            .code-profil-badge {
                color: #0284c7;
                font-weight: 700;
                font-size: 13.5px;
                background: #f0f9ff;
                border: 1px solid #bae6fd;
                padding: 4px 10px;
                border-radius: 8px;
                display: inline-block;
            }
            .code-mk-badge {
                color: #0369a1;
                font-weight: 700;
                font-size: 13px;
                background: #e0f2fe;
                border: 1px solid #bae6fd;
                padding: 4px 9px;
                border-radius: 6px;
                display: inline-block;
            }
            .text-theme-accent {
                color: #0284c7 !important;
            }
            .btn-theme-cetak {
                background-color: #0284c7 !important;
                border-color: #0284c7 !important;
                color: #ffffff !important;
            }
            .btn-theme-cetak:hover {
                background-color: #0369a1 !important;
                border-color: #0369a1 !important;
            }
        @else
            /* ── NON-APTIKOM Theme (Warm Amber/Gold Accent) ── */
            .custom-kurikulum-tabs .nav-link:hover {
                color: #d97706;
                background: #fffbe6;
                border-color: #fcd34d;
            }
            .custom-kurikulum-tabs .nav-link.active {
                color: #ffffff !important;
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
                border-color: #d97706 !important;
                box-shadow: 0 4px 12px rgba(217, 119, 6, 0.28) !important;
            }
            .code-profil-badge {
                color: #b45309;
                font-weight: 700;
                font-size: 13.5px;
                background: #fffbe6;
                border: 1px solid #fde68a;
                padding: 4px 10px;
                border-radius: 8px;
                display: inline-block;
            }
            .code-mk-badge {
                color: #c2410c;
                font-weight: 700;
                font-size: 13px;
                background: #ffedd5;
                border: 1px solid #fed7aa;
                padding: 4px 9px;
                border-radius: 6px;
                display: inline-block;
            }
            .text-theme-accent {
                color: #d97706 !important;
            }
            .btn-theme-cetak {
                background-color: #d97706 !important;
                border-color: #d97706 !important;
                color: #ffffff !important;
            }
            .btn-theme-cetak:hover {
                background-color: #b45309 !important;
                border-color: #b45309 !important;
            }
        @endif

        .custom-kurikulum-tabs .nav-link .badge-count {
            font-weight: 600;
            font-size: 11px;
            border-radius: 12px;
            padding: 3px 8px;
            background-color: #e2e8f0;
            color: #334155;
            transition: all 0.2s ease;
        }
        .custom-kurikulum-tabs .nav-link.active .badge-count {
            background-color: rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
        }

        .table-custom-mk {
            margin-bottom: 0 !important;
        }
        .table-custom-mk thead th {
            background-color: #f8fafc !important;
            color: #334155;
            font-weight: 700;
            font-size: 13.5px;
            border-bottom: 2px solid #cbd5e1 !important;
            padding: 13px 16px;
            vertical-align: middle;
        }
        .table-custom-mk tbody td {
            padding: 13px 16px;
            vertical-align: middle;
            font-size: 13.5px;
            color: #1e293b;
            border-color: #f1f5f9;
        }
        .table-custom-mk tbody tr:hover td {
            background-color: #f8fafc;
        }
    </style>

    {{-- Filter Form --}}
    <div class="card mb-4 border-0 shadow-sm rounded-3">
        <div class="card-body pb-0">
            <x-filter-form :universities="$universities" :faculties="$faculties" :programs="$programs" :kurikulums="$kurikulums" :showKurikulum="true" />
        </div>
    </div>

    {{-- ── UNIFIED SINGLE CARD CONTAINER ────────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
        {{-- Card Header: Title + Action Buttons --}}
        <div class="card-header bg-white border-bottom py-3 px-4">
            <div class="row gy-2 align-items-center">
                {{-- Title --}}
                <div class="col-12 col-md-6">
                    <h5 class="fw-bold text-dark mb-0 lh-base">
                        Data Pemetaan Profil Lulusan - Mata Kuliah
                    </h5>
                </div>

                {{-- Action buttons --}}
                <div class="col-12 col-md-6 text-md-end">
                    <div class="d-flex flex-wrap gap-2 justify-content-md-end justify-content-start">
                        {{-- Unduh PDF --}}
                        <a href="{{ route($currentPrefix . 'generatePDFProfilMK', array_merge(request()->query(), ['kurikulum_tahun' => 'all'])) }}"
                            id="btn-pdf"
                            class="btn btn-danger btn-sm fw-semibold px-3">
                            <i class="mdi mdi-file-pdf-box me-1"></i> Unduh PDF
                        </a>

                        {{-- Cetak --}}
                        <a href="{{ route($currentPrefix . 'printProfilMK', array_merge(request()->query(), ['kurikulum_tahun' => 'all'])) }}"
                            id="btn-print"
                            class="btn btn-theme-cetak btn-sm fw-semibold px-3" target="_blank">
                            <i class="mdi mdi-printer me-1"></i> Cetak
                        </a>
                    </div>
                </div>
            </div>
        </div>


        {{-- Card Body: Kurikulum Tabs + Table Content --}}
        <div class="card-body p-4">
            @if (!empty($groupedByKurikulum) && $groupedByKurikulum->count() > 0)
                @php
                    $totalProfilCount = $groupedByKurikulum->sum(fn($g) => $g->count());
                @endphp

                {{-- Nav Tabs --}}
                <ul class="nav nav-pills custom-kurikulum-tabs flex-nowrap overflow-auto pb-2" id="kurikulumTab" role="tablist">
                    {{-- Tab 1: Semua Kurikulum (Gabungan) --}}
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active"
                                id="tab-gabungan-tab"
                                data-bs-toggle="pill"
                                data-bs-target="#tab-gabungan"
                                data-kurikulum-tahun="all"
                                type="button"
                                role="tab"
                                aria-controls="tab-gabungan"
                                aria-selected="true">
                            <i class="mdi mdi-book-multiple-outline me-2"></i>
                            <span>Semua Kurikulum (Gabungan)</span>
                            <span class="badge-count ms-2">{{ $totalProfilCount }} Profil</span>
                        </button>
                    </li>

                    {{-- Tabs 2..N: Per Kurikulum --}}
                    @foreach ($groupedByKurikulum as $namaKurikulum => $grouped)
                        @php
                            $tabSlug = 'tab-' . Str::slug($namaKurikulum);
                            $tahun = preg_replace('/[^0-9]/', '', $namaKurikulum) ?: 'all';
                        @endphp
                        <li class="nav-item" role="presentation">
                            <button class="nav-link"
                                    id="{{ $tabSlug }}-tab"
                                    data-bs-toggle="pill"
                                    data-bs-target="#{{ $tabSlug }}"
                                    data-kurikulum-tahun="{{ $tahun }}"
                                    type="button"
                                    role="tab"
                                    aria-controls="{{ $tabSlug }}"
                                    aria-selected="false">
                                <i class="mdi mdi-book-open-page-variant me-2"></i>
                                <span>{{ $namaKurikulum }}</span>
                                <span class="badge-count ms-2">{{ $grouped->count() }} Profil</span>
                            </button>
                        </li>
                    @endforeach
                </ul>

                {{-- Tab Content Panes --}}
                <div class="tab-content" id="kurikulumTabContent">
                    {{-- Tab Pane 1: Gabungan (Semua Kurikulum) --}}
                    <div class="tab-pane fade show active"
                         id="tab-gabungan"
                         role="tabpanel"
                         aria-labelledby="tab-gabungan-tab">
                        @foreach ($groupedByKurikulum as $namaKurikulum => $grouped)
                            <div class="d-flex align-items-center mb-3 mt-4 text-dark fw-bold border-bottom pb-2">
                                <i class="mdi mdi-bookmark-check text-theme-accent me-2 fs-5"></i>
                                <span class="fs-6 me-2">{{ $namaKurikulum }}</span>
                                <span class="badge bg-light text-dark border rounded-pill px-2 py-1" style="font-size: 11px;">
                                    {{ $grouped->count() }} Profil
                                </span>
                            </div>

                            <div class="table-responsive rounded-3 border mb-4">
                                <table class="table table-bordered table-hover table-custom-mk align-middle">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 52px;">No</th>
                                            <th class="text-center" style="width: 120px;">Kode Profil</th>
                                            <th style="min-width: 260px;">Deskripsi Profil Lulusan</th>
                                            <th class="text-center" style="width: 110px;">Kurikulum</th>

                                            @if ($userOtoritas === 'Penjamin Mutu Universitas')
                                                <th style="width: 150px;">Fakultas</th>
                                                <th style="width: 150px;">Jurusan</th>
                                            @elseif ($userOtoritas === 'Penjamin Mutu Fakultas')
                                                <th style="width: 150px;">Jurusan</th>
                                            @endif

                                            <th class="text-center" style="width: 120px;">Kode MK</th>
                                            <th style="min-width: 240px;">Nama Mata Kuliah</th>
                                            <th class="text-center" style="width: 90px;">SKS</th>
                                            <th class="text-center" style="width: 95px;">Semester</th>
                                            <th class="text-center" style="width: 100px;">Rumpun</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 1; @endphp
                                        @foreach ($grouped as $profilKode => $items)
                                            @php $rowspan = max($items->count(), 1); @endphp
                                            @foreach ($items as $index => $item)
                                                <tr>
                                                    @if ($index === 0)
                                                        <td rowspan="{{ $rowspan }}" class="text-center fw-semibold align-middle">
                                                            {{ $no++ }}
                                                        </td>
                                                        <td rowspan="{{ $rowspan }}" class="text-center align-middle">
                                                            <span class="code-profil-badge">{{ $item->profil_kode ?? '-' }}</span>
                                                        </td>
                                                        <td rowspan="{{ $rowspan }}" class="align-top" style="white-space:normal; word-break:break-word;">
                                                            <div class="fw-semibold text-dark mb-1">{{ $item->profil_nama ?? '-' }}</div>
                                                        </td>
                                                        <td rowspan="{{ $rowspan }}" class="text-center align-middle">
                                                            <span class="badge bg-light text-dark border px-2 py-1 fw-semibold">
                                                                {{ $item->kurikulum_tahun ?? '-' }}
                                                            </span>
                                                        </td>

                                                        @if ($userOtoritas === 'Penjamin Mutu Universitas')
                                                            <td rowspan="{{ $rowspan }}" class="align-top" style="white-space:normal; word-break:break-word;">
                                                                {{ $item->fakultas_nama ?? '-' }}
                                                            </td>
                                                            <td rowspan="{{ $rowspan }}" class="align-top" style="white-space:normal; word-break:break-word;">
                                                                {{ $item->prodi_nama ?? '-' }}
                                                            </td>
                                                        @elseif ($userOtoritas === 'Penjamin Mutu Fakultas')
                                                            <td rowspan="{{ $rowspan }}" class="align-top" style="white-space:normal; word-break:break-word;">
                                                                {{ $item->prodi_nama ?? '-' }}
                                                            </td>
                                                        @endif
                                                    @endif

                                                    <td class="text-center">
                                                        @if (!empty($item->mk_kode))
                                                            <span class="code-mk-badge">{{ $item->mk_kode }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td style="white-space:normal; word-break:break-word;">
                                                        @if (!empty($item->mk_nama))
                                                            <span class="fw-medium text-dark">{{ $item->mk_nama }}</span>
                                                        @else
                                                            <span class="text-muted fst-italic">- Belum terpetakan -</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center fw-semibold">
                                                        @if (!empty($item->mk_kode))
                                                            {{ ($item->bobot_teori ?? 0) + ($item->bobot_praktikum ?? 0) }} SKS
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if (!empty($item->mk_semester))
                                                            <span class="badge bg-light text-dark border px-2 py-1">Sem {{ $item->mk_semester }}</span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if (!empty($item->rumpun))
                                                            <span class="badge {{ $item->rumpun === 'Wajib' ? 'bg-success text-white' : 'bg-warning text-dark' }} px-2 py-1">
                                                                {{ $item->rumpun }}
                                                            </span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>

                    {{-- Tab Panes 2..N: Per Kurikulum --}}
                    @foreach ($groupedByKurikulum as $namaKurikulum => $grouped)
                        @php
                            $tabSlug = 'tab-' . Str::slug($namaKurikulum);
                        @endphp

                        <div class="tab-pane fade"
                             id="{{ $tabSlug }}"
                             role="tabpanel"
                             aria-labelledby="{{ $tabSlug }}-tab">

                            <div class="table-responsive rounded-3 border">
                                <table class="table table-bordered table-hover table-custom-mk align-middle">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 52px;">No</th>
                                            <th class="text-center" style="width: 120px;">Kode Profil</th>
                                            <th style="min-width: 260px;">Deskripsi Profil Lulusan</th>
                                            <th class="text-center" style="width: 110px;">Kurikulum</th>

                                            @if ($userOtoritas === 'Penjamin Mutu Universitas')
                                                <th style="width: 150px;">Fakultas</th>
                                                <th style="width: 150px;">Jurusan</th>
                                            @elseif ($userOtoritas === 'Penjamin Mutu Fakultas')
                                                <th style="width: 150px;">Jurusan</th>
                                            @endif

                                            <th class="text-center" style="width: 120px;">Kode MK</th>
                                            <th style="min-width: 240px;">Nama Mata Kuliah</th>
                                            <th class="text-center" style="width: 90px;">SKS</th>
                                            <th class="text-center" style="width: 95px;">Semester</th>
                                            <th class="text-center" style="width: 100px;">Rumpun</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 1; @endphp
                                        @foreach ($grouped as $profilKode => $items)
                                            @php $rowspan = max($items->count(), 1); @endphp
                                            @foreach ($items as $index => $item)
                                                <tr>
                                                    @if ($index === 0)
                                                        <td rowspan="{{ $rowspan }}" class="text-center fw-semibold align-middle">
                                                            {{ $no++ }}
                                                        </td>
                                                        <td rowspan="{{ $rowspan }}" class="text-center align-middle">
                                                            <span class="code-profil-badge">{{ $item->profil_kode ?? '-' }}</span>
                                                        </td>
                                                        <td rowspan="{{ $rowspan }}" class="align-top" style="white-space:normal; word-break:break-word;">
                                                            <div class="fw-semibold text-dark mb-1">{{ $item->profil_nama ?? '-' }}</div>
                                                        </td>
                                                        <td rowspan="{{ $rowspan }}" class="text-center align-middle">
                                                            <span class="badge bg-light text-dark border px-2 py-1 fw-semibold">
                                                                {{ $item->kurikulum_tahun ?? '-' }}
                                                            </span>
                                                        </td>

                                                        @if ($userOtoritas === 'Penjamin Mutu Universitas')
                                                            <td rowspan="{{ $rowspan }}" class="align-top" style="white-space:normal; word-break:break-word;">
                                                                {{ $item->fakultas_nama ?? '-' }}
                                                            </td>
                                                            <td rowspan="{{ $rowspan }}" class="align-top" style="white-space:normal; word-break:break-word;">
                                                                {{ $item->prodi_nama ?? '-' }}
                                                            </td>
                                                        @elseif ($userOtoritas === 'Penjamin Mutu Fakultas')
                                                            <td rowspan="{{ $rowspan }}" class="align-top" style="white-space:normal; word-break:break-word;">
                                                                {{ $item->prodi_nama ?? '-' }}
                                                            </td>
                                                        @endif
                                                    @endif

                                                    <td class="text-center">
                                                        @if (!empty($item->mk_kode))
                                                            <span class="code-mk-badge">{{ $item->mk_kode }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td style="white-space:normal; word-break:break-word;">
                                                        @if (!empty($item->mk_nama))
                                                            <span class="fw-medium text-dark">{{ $item->mk_nama }}</span>
                                                        @else
                                                            <span class="text-muted fst-italic">- Belum terpetakan -</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center fw-semibold">
                                                        @if (!empty($item->mk_kode))
                                                            {{ ($item->bobot_teori ?? 0) + ($item->bobot_praktikum ?? 0) }} SKS
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if (!empty($item->mk_semester))
                                                            <span class="badge bg-light text-dark border px-2 py-1">Sem {{ $item->mk_semester }}</span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if (!empty($item->rumpun))
                                                            <span class="badge {{ $item->rumpun === 'Wajib' ? 'bg-success text-white' : 'bg-warning text-dark' }} px-2 py-1">
                                                                {{ $item->rumpun }}
                                                            </span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="mdi mdi-database-off display-4 text-muted mb-2"></i>
                    <strong class="d-block mb-1">Data Belum Tersedia</strong>
                    Belum terdapat data pemetaan profil lulusan dengan mata kuliah.
                </div>
            @endif
        </div>
    </div>

    <script src="{{ asset('assets/js/dynamic-filters.js') }}"></script>

    <script>
        $(document).ready(function() {

            $('#filter-form').on('submit', function() {
                $(this).attr('method', 'GET');
                $(this).attr('action', '{{ url()->current() }}');
            });

            $('#reset-filter').on('click', function(e) {
                e.preventDefault();
                window.location.href = '{{ route(Request::route()->getName()) }}';
            });

            function updateExportUrls(kurikulumTahun) {
                let pdfBtn = $('#btn-pdf');
                let printBtn = $('#btn-print');

                if (pdfBtn.length) {
                    let pdfUrl = new URL(pdfBtn.attr('href'), window.location.origin);
                    if (kurikulumTahun) {
                        pdfUrl.searchParams.set('kurikulum_tahun', kurikulumTahun);
                    } else {
                        pdfUrl.searchParams.delete('kurikulum_tahun');
                    }
                    pdfBtn.attr('href', pdfUrl.pathname + pdfUrl.search);
                }

                if (printBtn.length) {
                    let printUrl = new URL(printBtn.attr('href'), window.location.origin);
                    if (kurikulumTahun) {
                        printUrl.searchParams.set('kurikulum_tahun', kurikulumTahun);
                    } else {
                        printUrl.searchParams.delete('kurikulum_tahun');
                    }
                    printBtn.attr('href', printUrl.pathname + printUrl.search);
                }
            }

            $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
                let tahun = $(e.target).data('kurikulum-tahun');
                updateExportUrls(tahun);
            });

        });
    </script>
@endsection

