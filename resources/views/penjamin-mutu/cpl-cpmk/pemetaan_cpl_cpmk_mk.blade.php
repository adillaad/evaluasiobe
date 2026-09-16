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
        @if ($isAptikom)
            /* ── APTIKOM Theme (Sky Blue Accent) ── */
            .btn-primary {
                background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
                border-color: #0284c7 !important;
                color: #ffffff !important;
            }
            .btn-primary:hover, .btn-primary:focus {
                background: #0369a1 !important;
                border-color: #0369a1 !important;
                color: #ffffff !important;
            }
            .btn-outline-primary {
                color: #0284c7 !important;
                border-color: #0284c7 !important;
                background-color: #ffffff !important;
            }
            .btn-outline-primary:hover, .btn-outline-primary.active, .btn-outline-primary:focus {
                background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
                color: #ffffff !important;
                border-color: #0284c7 !important;
                box-shadow: 0 4px 12px rgba(14, 165, 233, 0.28) !important;
            }
            .badge-mk {
                color: #0284c7 !important;
                background-color: #f0f9ff !important;
                border: 1px solid #bae6fd !important;
            }
            .cpl-nav-pills .nav-link.active {
                background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
                color: #ffffff !important;
                border-color: #0284c7 !important;
                box-shadow: 0 4px 12px rgba(14, 165, 233, 0.28) !important;
            }
            .text-primary {
                color: #0284c7 !important;
            }
        @else
            /* ── NON-APTIKOM Theme (Warm Amber/Gold Accent) ── */
            .btn-primary {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
                border-color: #d97706 !important;
                color: #ffffff !important;
            }
            .btn-primary:hover, .btn-primary:focus {
                background: #b45309 !important;
                border-color: #b45309 !important;
                color: #ffffff !important;
            }
            .btn-outline-primary {
                color: #d97706 !important;
                border-color: #d97706 !important;
                background-color: #ffffff !important;
            }
            .btn-outline-primary:hover, .btn-outline-primary.active, .btn-outline-primary:focus {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
                color: #ffffff !important;
                border-color: #d97706 !important;
                box-shadow: 0 4px 12px rgba(217, 119, 6, 0.28) !important;
            }
            .badge-mk {
                color: #b45309 !important;
                background-color: #fffbe6 !important;
                border: 1px solid #fde68a !important;
            }
            .cpl-nav-pills .nav-link.active {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
                color: #ffffff !important;
                border-color: #d97706 !important;
                box-shadow: 0 4px 12px rgba(217, 119, 6, 0.28) !important;
            }
            .text-primary {
                color: #d97706 !important;
            }
        @endif
        .table-pemetaan {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        .table-pemetaan th {
            background-color: #f8fafc !important;
            color: #334155 !important;
            font-weight: 600 !important;
            font-size: 13.5px !important;
            padding: 12px 14px !important;
            vertical-align: middle !important;
            border-bottom: 2px solid #cbd5e1 !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table-pemetaan td {
            vertical-align: top !important;
            line-height: 1.6 !important;
            padding: 12px 14px !important;
            font-size: 13.5px !important;
            color: #1e293b !important;
            white-space: normal !important;
            word-break: break-word !important;
        }
        .table-pemetaan tbody tr:hover {
            background-color: #f8fafc !important;
        }
        .cpl-tabs-wrapper {
            background: #ffffff;
            border-radius: 12px;
            padding: 14px 18px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            border: 1px solid #e2e8f0;
        }
        .cpl-nav-pills {
            gap: 8px;
        }
        .cpl-nav-pills .nav-link {
            color: #475569 !important;
            background-color: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 8px 14px !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            transition: all 0.2s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            white-space: nowrap !important;
        }
        .cpl-nav-pills .nav-link:hover {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
        }
        .cpl-nav-pills .nav-link.active {
            background-color: #0284c7 !important;
            color: #ffffff !important;
            border-color: #0284c7 !important;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2) !important;
        }
        .cpl-nav-pills .nav-link.active .badge-cpmk-count {
            background-color: rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
        }
        .badge-cpmk-count {
            background-color: #e2e8f0;
            color: #475569;
            font-size: 11px;
            border-radius: 6px;
            padding: 2px 6px;
            margin-left: 6px;
        }
        .badge-mk {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: 600;
            color: #0284c7;
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 6px;
            margin: 2px 1px;
            transition: transform 0.15s ease;
        }
        .badge-mk:hover {
            transform: translateY(-1px);
            background-color: #e0f2fe;
        }
        .cpl-card-header {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-left: 4px solid #0284c7;
            border-radius: 8px;
            padding: 16px;
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

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <h4 class="card-title mb-0 me-auto">List Pemetaan CPL-CPMK-MK</h4>
                </div>

                {{-- CPL Tabs Navigation --}}
                <div class="cpl-tabs-wrapper mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                        <span class="fw-bold text-secondary text-uppercase small" style="letter-spacing: 0.5px;">
                            <i class="mdi mdi-filter-variant me-1"></i> Pilih CPL:
                        </span>
                        <span class="text-muted small">Klik tab CPL untuk memfilter pemetaan</span>
                    </div>
                    <ul class="nav nav-pills cpl-nav-pills overflow-auto flex-nowrap pb-1" id="cplTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-all-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#tab-all" data-target="#tab-all" type="button" role="tab" aria-controls="tab-all" aria-selected="true">
                                <i class="mdi mdi-grid me-1"></i> Semua CPL
                            </button>
                        </li>
                        @foreach ($cpls as $cpl)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-cpl-{{ $cpl->id }}-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#tab-cpl-{{ $cpl->id }}" data-target="#tab-cpl-{{ $cpl->id }}" type="button" role="tab" aria-controls="tab-cpl-{{ $cpl->id }}" aria-selected="false">
                                    {{ $cpl->kode }}
                                    <span class="badge-cpmk-count">{{ $cpl->cpmk->count() }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Tab Content Panes --}}
                <div class="tab-content" id="cplTabContent">
                    
                    {{-- TAB 1: SEMUA CPL --}}
                    <div class="tab-pane fade show active" id="tab-all" role="tabpanel" aria-labelledby="tab-all-tab">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle table-pemetaan">
                                <thead>
                                    <tr>
                                        <th style="width: 10%;">CPL</th>
                                        <th style="width: 25%;">Deskripsi CPL</th>
                                        <th style="width: 12%;">CPMK</th>
                                        <th style="width: 33%;">Deskripsi CPMK</th>
                                        <th style="width: 20%;">Kode MK</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cpls as $cpl)
                                        @php
                                            $rowspanCPL = $cpl->cpmk->count() ?: 1;
                                        @endphp
                                        <tr>
                                            <td rowspan="{{ $rowspanCPL }}" class="fw-bold text-primary align-middle bg-light text-center">{{ $cpl->kode }}</td>
                                            <td rowspan="{{ $rowspanCPL }}" class="align-middle">{{ $cpl->judul }}</td>
                                            @foreach ($cpl->cpmk as $indexCpmk => $cpmk)
                                                @if ($indexCpmk > 0)
                                                    <tr>
                                                @endif
                                                <td class="fw-semibold text-dark">{{ $cpmk->kode }}</td>
                                                <td>{{ $cpmk->judul }}</td>
                                                <td>
                                                    @php
                                                        $matchedMks = $cpmk->mks->filter(function($mk) use ($cpl) {
                                                            return $cpl->mk->contains('kode', $mk->kode);
                                                        });
                                                    @endphp
                                                    @if($matchedMks->count() > 0)
                                                        <div class="d-flex flex-wrap gap-1">
                                                            @foreach ($matchedMks as $mk)
                                                                <span class="badge-mk" title="{{ $mk->nama }}">{{ $mk->kode }}</span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">-</span>
                                                    @endif
                                                </td>
                                                @if ($indexCpmk < $cpl->cpmk->count() - 1)
                                                    </tr>
                                                @endif
                                            @endforeach
                                            @if ($cpl->cpmk->isEmpty())
                                                <td colspan="3" class="text-muted fst-italic">Tidak ada CPMK terkait</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB INDIVIDUAL CPL --}}
                    @foreach ($cpls as $cpl)
                        <div class="tab-pane fade" id="tab-cpl-{{ $cpl->id }}" role="tabpanel" aria-labelledby="tab-cpl-{{ $cpl->id }}-tab">
                            <div class="cpl-card-header mb-3">
                                <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
                                    <div>
                                        <h4 class="fw-bold text-primary mb-1">{{ $cpl->kode }}</h4>
                                        <p class="text-dark mb-0 fw-medium" style="line-height: 1.5;">{{ $cpl->judul }}</p>
                                    </div>
                                    <span class="badge bg-primary px-3 py-2 fs-6">
                                        <i class="mdi mdi-format-list-bulleted me-1"></i> {{ $cpl->cpmk->count() }} CPMK
                                    </span>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle table-pemetaan">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 15%; text-align: center;">CPMK</th>
                                            <th style="width: 50%;">Deskripsi CPMK</th>
                                            <th style="width: 35%;">Mata Kuliah Terkait (Kode MK)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($cpl->cpmk as $cpmk)
                                            <tr>
                                                <td class="text-center fw-bold text-primary bg-light align-middle">{{ $cpmk->kode }}</td>
                                                <td>{{ $cpmk->judul }}</td>
                                                <td>
                                                    @php
                                                        $matchedMks = $cpmk->mks->filter(function($mk) use ($cpl) {
                                                            return $cpl->mk->contains('kode', $mk->kode);
                                                        });
                                                    @endphp
                                                    @if($matchedMks->count() > 0)
                                                        <div class="d-flex flex-wrap gap-1">
                                                            @foreach ($matchedMks as $mk)
                                                                <span class="badge-mk p-2" title="{{ $mk->nama }}">
                                                                    <i class="mdi mdi-book-outline me-1"></i> <strong>{{ $mk->kode }}</strong> - {{ $mk->nama }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="text-muted small fs-7">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-4">
                                                    <i class="mdi mdi-information-outline me-1 fs-5"></i> Tidak ada CPMK terkait untuk {{ $cpl->kode }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach

                </div>

            </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        $('#cplTab button, #cplTab a').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
        });

        function getSelectedValue() {
            var mkKode = document.getElementById("mk_kode").value;
            const otoritas = "{{ $userOtoritas }}";
            let urlget = '';
            if(otoritas === "Kepala Program Studi"){
                urlget = `/kepala-program-studi/cpl-cpmk/get-cpmk-by-mk/${mkKode}`;
            } else if (otoritas === 'Penjamin Mutu Program Studi'){
                urlget = `/penjamin-mutu/program-studi/cpl-cpmk/get-cpmk-by-mk/${mkKode}`;
            }
            if(mkKode && urlget){
                $.ajax({
                    url: urlget,
                    method: 'GET',
                    success: function(data) {
                        let cpmkOptions = '';
                        if (data.cpmks && data.cpmks.length > 0) {
                            data.cpmks.forEach(cpmk => {
                                cpmkOptions += `<div class="form-check mb-2 ps-4" style="position: relative;">
                                                    <input class="form-check-input" type="checkbox" name="cpmk_ids[]" value="${cpmk.id}" id="cpmk_${cpmk.id}" style="margin-left: -1.5rem; cursor: pointer;">
                                                    <label class="form-check-label ms-1" for="cpmk_${cpmk.id}" style="cursor: pointer;">
                                                        <strong>${cpmk.kode}</strong> - ${cpmk.judul}
                                                    </label>
                                                </div>`;
                            });
                        } else {
                            cpmkOptions = `<label class="text-muted fst-italic">Tidak ada data CPMK untuk MK yang dipilih.</label>`;
                        }
                        $('#cpmk_id').html(cpmkOptions);
                    },
                    error: function(xhr, status, error) {
                        alert('Gagal memuat data CPMK.');
                    }
                });
            } else {
                $('#cpmk_id').html('');
            }
        }

        var mkElement = document.getElementById("mk_kode");
        if (mkElement) {
            mkElement.addEventListener("change", getSelectedValue);
        }
    });
</script>
@endsection
