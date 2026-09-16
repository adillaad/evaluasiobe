@php
    $selectedProdiId = request('prodi_id');
    if (!$selectedProdiId && request('kurikulum_id')) {
        $selectedProdiId = \Illuminate\Support\Facades\DB::table('kurikulums')->where('id', request('kurikulum_id'))->value('id_prodi');
    }
    if (!$selectedProdiId && auth()->check()) {
        $selectedProdiId = auth()->user()->id_prodiUser ?? (auth()->user()->prodi ? auth()->user()->prodi->id : null);
    }
    if ($selectedProdiId) {
        $isAptikom = (bool) \Illuminate\Support\Facades\DB::table('prodi')->where('id', $selectedProdiId)->value('is_aptikom');
    } else {
        $isAptikom = auth()->check() && auth()->user()->prodi ? (bool) auth()->user()->prodi->is_aptikom : true;
    }
@endphp

@extends(auth()->user()->otoritas->otoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')

@section('content')
    @if (session()->has('failed'))
        <div class="alert alert-danger" role="alert" id="box">
            <div>{{ session('failed') }}</div>
        </div>
    @elseif (session()->has('success'))
        <div class="alert greenAdd" role="alert" id="box">
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <style>
        .smt-tabs-wrapper, .cpl-tabs-wrapper {
            background: #ffffff;
            border-radius: 12px;
            padding: 14px 18px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            border: 1px solid #e2e8f0;
        }

        .cpl-tabs-scroll-container {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            white-space: nowrap !important;
            gap: 8px !important;
            padding-bottom: 8px !important;
            -webkit-overflow-scrolling: touch;
        }
        .cpl-tabs-scroll-container::-webkit-scrollbar {
            height: 6px;
        }
        .cpl-tabs-scroll-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        .cpl-tabs-scroll-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .cpl-tabs-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .cpl-nav-pills .nav-item {
            flex-shrink: 0 !important;
        }
        .cpl-nav-pills .nav-link {
            color: #475569 !important;
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 8px 16px !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            transition: all 0.2s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            white-space: nowrap !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        }

        @if ($isAptikom)
            /* ── APTIKOM Theme (Sky Blue Accent) ── */
            .cpl-nav-pills .nav-link:hover {
                color: #0284c7 !important;
                background-color: #f0f9ff !important;
                border-color: #38bdf8 !important;
            }
            .cpl-nav-pills .nav-link.active {
                color: #ffffff !important;
                background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
                border-color: #0284c7 !important;
                box-shadow: 0 4px 12px rgba(14, 165, 233, 0.28) !important;
            }
            .smt-nav-tabs .nav-link {
                color: #0284c7 !important;
                border: none !important;
                border-bottom: 2px solid transparent !important;
                padding: 10px 18px !important;
                font-size: 14px !important;
                font-weight: 500 !important;
                background: transparent !important;
                transition: all 0.2s ease !important;
            }
            .smt-nav-tabs .nav-link:hover {
                color: #0369a1 !important;
                border-bottom: 2px solid #38bdf8 !important;
            }
            .smt-nav-tabs .nav-link.active {
                color: #0284c7 !important;
                font-weight: 700 !important;
                border: none !important;
                border-bottom: 3px solid #0284c7 !important;
                background: transparent !important;
            }
            .badge-code-cpmk {
                color: #0284c7 !important;
                background-color: #f0f9ff !important;
                border: 1px solid #bae6fd !important;
                font-weight: 700;
                padding: 4px 10px;
                border-radius: 6px;
                font-size: 12px;
                display: inline-block;
            }
            .badge-code-cpl {
                background-color: #f0f9ff !important;
                color: #0284c7 !important;
                border: 1px solid #bae6fd !important;
                font-weight: 700;
                padding: 4px 10px;
                border-radius: 6px;
                font-size: 12px;
                display: inline-block;
            }
        @else
            /* ── NON-APTIKOM Theme (Warm Amber/Gold Accent) ── */
            .cpl-nav-pills .nav-link:hover {
                color: #d97706 !important;
                background-color: #fffbe6 !important;
                border-color: #fcd34d !important;
            }
            .cpl-nav-pills .nav-link.active {
                color: #ffffff !important;
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
                border-color: #d97706 !important;
                box-shadow: 0 4px 12px rgba(217, 119, 6, 0.28) !important;
            }
            .smt-nav-tabs .nav-link {
                color: #d97706 !important;
                border: none !important;
                border-bottom: 2px solid transparent !important;
                padding: 10px 18px !important;
                font-size: 14px !important;
                font-weight: 500 !important;
                background: transparent !important;
                transition: all 0.2s ease !important;
            }
            .smt-nav-tabs .nav-link:hover {
                color: #b45309 !important;
                border-bottom: 2px solid #fcd34d !important;
            }
            .smt-nav-tabs .nav-link.active {
                color: #d97706 !important;
                font-weight: 700 !important;
                border: none !important;
                border-bottom: 3px solid #d97706 !important;
                background: transparent !important;
            }
            .badge-code-cpmk {
                color: #b45309 !important;
                background-color: #fffbe6 !important;
                border: 1px solid #fde68a !important;
                font-weight: 700;
                padding: 4px 10px;
                border-radius: 6px;
                font-size: 12px;
                display: inline-block;
            }
            .badge-code-cpl {
                background-color: #fffbe6 !important;
                color: #b45309 !important;
                border: 1px solid #fde68a !important;
                font-weight: 700;
                padding: 4px 10px;
                border-radius: 6px;
                font-size: 12px;
                display: inline-block;
            }
        @endif

        .badge-count {
            background-color: #e2e8f0;
            color: #475569;
            font-size: 11px;
            border-radius: 6px;
            padding: 2px 6px;
            margin-left: 6px;
            transition: all 0.2s ease;
        }
        .nav-link.active .badge-count {
            background-color: rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
        }
        .table-matrix-semester {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        .table-matrix-semester th {
            vertical-align: middle !important;
            font-size: 12.5px !important;
            text-transform: uppercase !important;
            padding: 10px 8px !important;
            background-color: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
        }
        .table-matrix-semester td {
            vertical-align: middle !important;
            padding: 10px 8px !important;
            font-size: 13px !important;
            border: 1px solid #e2e8f0 !important;
            white-space: normal !important;
            word-break: break-word !important;
        }
        .badge-code-mk {
            background-color: #f8fafc;
            color: {{ $isAptikom ? '#0284c7' : '#d97706' }};
            border: 1px solid #e2e8f0;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11.5px;
            display: inline-block;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .th-mk-header {
            min-width: 140px !important;
            max-width: 220px !important;
            width: 160px !important;
            text-align: center !important;
            white-space: normal !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            padding: 10px 8px !important;
            vertical-align: top !important;
        }
        .th-mk-header .mk-title-text {
            display: block !important;
            font-size: 11px !important;
            line-height: 1.35 !important;
            white-space: normal !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            hyphens: auto !important;
            color: #475569 !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            margin-top: 4px !important;
        }
    </style>

    <div class="container-fluid">
        @if(isset($kurikulums))
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-body pb-0">
                    <x-filter-form :showKurikulum="true" :kurikulums="$kurikulums" />
                </div>
            </div>
        @endif

        {{-- CPL Filter Pills (Scrollable Row) --}}
        <div class="cpl-tabs-wrapper mb-3">
            <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                <span class="fw-bold text-secondary text-uppercase small" style="letter-spacing: 0.5px;">
                    <i class="mdi mdi-filter-variant me-1"></i> PILIH CPL:
                </span>
                <span class="text-muted small">Klik tab CPL untuk memfilter tampilan</span>
            </div>
            <ul class="nav nav-pills cpl-nav-pills cpl-tabs-scroll-container" id="cplFilterTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active filter-cpl-btn" data-cpl-target="all" type="button">
                        <i class="mdi mdi-view-grid me-1"></i> Semua CPL
                    </button>
                </li>
                @foreach ($cpls as $cpl)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link filter-cpl-btn" data-cpl-target="{{ $cpl->kode }}" type="button">
                            {{ $cpl->kode }}
                            <span class="badge-count">{{ $cpl->cpmk->count() }}</span>
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Tab Content per Semester --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0 px-4">
                {{-- Semester Tabs Navigation --}}
                <ul class="nav nav-tabs card-header-tabs smt-nav-tabs border-bottom" id="smtTab" role="tablist">
                    @foreach ($semesters as $index => $semester)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="tab-smt-{{ $semester->semester }}-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#tab-smt-{{ $semester->semester }}" data-target="#tab-smt-{{ $semester->semester }}" type="button" role="tab" aria-controls="tab-smt-{{ $semester->semester }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                Semester {{ $semester->semester }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="card-body px-4 pt-3">
                <div class="tab-content" id="smtTabContent">
                    @foreach ($semesters as $index => $semester)
                        @php
                            $mksInSmt = $mks->where('semester', $semester->semester);
                        @endphp
                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="tab-smt-{{ $semester->semester }}" role="tabpanel" aria-labelledby="tab-smt-{{ $semester->semester }}-tab">
                            
                            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                <div>
                                    <h4 class="card-title mb-1 fw-bold text-dark">Matriks MK-CPMK &mdash; Semester {{ $semester->semester }}</h4>
                                    <p class="text-muted small mb-0">Ikon centang menunjukkan Mata Kuliah yang terikat dengan CPMK terkait.</p>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-matrix-semester align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 90px;" class="text-center fw-bold text-dark">CPL</th>
                                            <th style="min-width: 200px; max-width: 280px;" class="fw-bold text-dark">CPMK</th>
                                            @forelse ($mksInSmt as $mk)
                                                <th class="th-mk-header" title="{{ $mk->nama }}">
                                                    <span class="badge-code-mk mb-1">{{ $mk->kode }}</span>
                                                    <span class="mk-title-text">{{ $mk->nama }}</span>
                                                </th>
                                            @empty
                                                <th class="text-center text-muted">-</th>
                                            @endforelse
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $hasCpmkData = false; @endphp
                                        @foreach ($cpls as $cpl)
                                            @php
                                                $cpmkList = $cpl->cpmk;
                                                $rowSpanCPL = $cpmkList->count();
                                            @endphp
                                            @if ($rowSpanCPL > 0)
                                                @foreach ($cpmkList as $cIdx => $cpmk)
                                                    @php $hasCpmkData = true; @endphp
                                                    <tr class="cpmk-row" data-cpl-kode="{{ $cpl->kode }}">
                                                        @if ($cIdx === 0)
                                                            <td rowspan="{{ $rowSpanCPL }}" class="text-center align-middle bg-light">
                                                                <span class="badge-code-cpl">{{ $cpl->kode }}</span>
                                                            </td>
                                                        @endif
                                                        <td class="align-top py-2.5 px-3">
                                                            <div class="mb-1">
                                                                <span class="badge-code-cpmk">{{ $cpmk->kode }}</span>
                                                            </div>
                                                            <div class="text-secondary" style="font-size: 12.5px; line-height: 1.4; white-space: normal; word-break: break-word;">{{ $cpmk->judul }}</div>
                                                        </td>

                                                        @forelse ($mksInSmt as $mk)
                                                            @php
                                                                $isMapped = $cpmk->mks->contains('kode', $mk->kode);
                                                            @endphp
                                                            <td class="text-center align-middle">
                                                                @if ($isMapped)
                                                                    <i class="mdi mdi-check-circle text-success fs-5" title="{{ $mk->kode }} terhubung dengan {{ $cpmk->kode }}"></i>
                                                                @else
                                                                    <span class="text-muted small opacity-25">-</span>
                                                                @endif
                                                            </td>
                                                        @empty
                                                            <td class="text-center text-muted">-</td>
                                                        @endforelse
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach

                                        @if(!$hasCpmkData)
                                            <tr>
                                                <td colspan="{{ 2 + max(1, $mksInSmt->count()) }}" class="text-center text-muted py-4">Tidak ada data CPL / CPMK.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Semester Tab Click Handler
            $('#smtTab button, #smtTab a').on('click', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });

            // CPL Filter Pill Click Handler
            $('.filter-cpl-btn').on('click', function() {
                $('.filter-cpl-btn').removeClass('active');
                $(this).addClass('active');

                var targetCpl = $(this).data('cpl-target');
                if (targetCpl === 'all') {
                    $('.cpmk-row').show();
                } else {
                    $('.cpmk-row').hide();
                    $('.cpmk-row[data-cpl-kode="' + targetCpl + '"]').show();
                }
            });
        });
    </script>
@endsection
