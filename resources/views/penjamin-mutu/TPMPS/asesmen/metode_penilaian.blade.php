@php
    $routePrefix = [
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp

@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('title', 'Metode Penilaian')
@section('page_title', 'Metode Penilaian')
@section('content')

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        .modern-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1F3BB3;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -0.01em;
            margin-bottom: 0;
        }

        /* Custom CPL Filter Tabs */
        .cpl-tabs-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 8px;
            margin-bottom: 20px;
            border-bottom: 2px solid #f1f5f9;
            scrollbar-width: thin;
        }

        .cpl-tab-btn {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .cpl-tab-btn:hover {
            background: #eff6ff;
            color: #1F3BB3;
            border-color: #bfdbfe;
        }

        .cpl-tab-btn.active {
            background: #1F3BB3;
            color: #ffffff;
            border-color: #1F3BB3;
            box-shadow: 0 3px 10px rgba(31, 59, 179, 0.2);
        }

        .cpl-tab-btn .badge-count {
            font-size: 0.75rem;
            padding: 2px 7px;
            border-radius: 12px;
            background: rgba(0, 0, 0, 0.08);
            color: inherit;
        }

        .cpl-tab-btn.active .badge-count {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        /* Table Styling */
        .matrix-table-container {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow-x: auto;
            background: #ffffff;
        }

        .matrix-table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .matrix-table thead th {
            background: #f8fafc;
            color: #334155;
            font-weight: 700;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            padding: 14px 16px;
            border-bottom: 2px solid #e2e8f0;
            text-align: center;
            vertical-align: middle;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .matrix-table thead th.text-start {
            text-align: left;
        }

        .matrix-table tbody td {
            padding: 12px 16px;
            font-size: 0.875rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            text-align: center;
        }

        .matrix-table tbody td.text-start {
            text-align: left;
        }

        .matrix-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .matrix-table tbody tr:last-child td {
            border-bottom: none;
        }

        .cell-check {
            color: #16a34a;
            font-size: 1.15rem;
            font-weight: 800;
        }

        .cell-dash {
            color: #94a3b8;
            font-weight: 400;
        }
    </style>

    <div class="container-fluid">
        @if (session()->has('failed'))
            <div class="alert alert-danger shadow-sm border-0 mb-3" role="alert" id="box">
                <div>{{ session('failed') }}</div>
            </div>
        @elseif (session()->has('success'))
            <div class="alert greenAdd shadow-sm border-0 mb-3" role="alert" id="box">
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <div class="card modern-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                    <div>
                        <h4 class="section-title"><i class="bi bi-grid-3x3-gap-fill"></i> Metode Penilaian</h4>
                        <p class="text-muted small mb-0 mt-1">Matriks pemetaan metode penilaian per CPL, Mata Kuliah, dan CPMK.</p>
                    </div>
                    @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                        <a href="{{ route($currentPrefix . 'asesmen.metode-penilaian-add') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                            <i class="bi bi-plus-lg"></i> Tambah Metode Penilaian
                        </a>
                    @endif
                </div>

                {{-- Grouping CPL IDs in used rows --}}
                @php
                    $usedCplIds = [];
                    foreach ($penilaian as $rowId => $rows) {
                        $cplId = $rows[0]->cpl_id ?? null;
                        if ($cplId) {
                            $usedCplIds[$cplId] = ($usedCplIds[$cplId] ?? 0) + 1;
                        }
                    }
                @endphp

                {{-- CPL Filter Tabs --}}
                <div class="cpl-tabs-wrapper">
                    <button type="button" class="cpl-tab-btn active" data-cpl="all">
                        Semua CPL <span class="badge-count">{{ count($penilaian) }}</span>
                    </button>
                    @foreach ($cpls as $cplId => $cplObj)
                        @if (isset($usedCplIds[$cplId]))
                            <button type="button" class="cpl-tab-btn" data-cpl="{{ $cplId }}">
                                {{ $cplObj->kode }} <span class="badge-count">{{ $usedCplIds[$cplId] }}</span>
                            </button>
                        @endif
                    @endforeach
                </div>

                {{-- Matrix Table --}}
                <div class="matrix-table-container">
                    <table class="table matrix-table" id="metodeMatrixTable">
                        <thead>
                            <tr>
                                <th class="text-start" style="width: 120px;">CPL</th>
                                <th class="text-start" style="width: 150px;">MK</th>
                                <th class="text-start" style="width: 150px;">CPMK</th>
                                @foreach ($metodes as $metodeNama)
                                    <th>{{ $metodeNama }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($penilaian as $rowId => $rows)
                                @php
                                    $row = $rows[0];
                                    $cplId = $row->cpl_id;
                                    $cplKode = $cpls[$cplId]->kode ?? '-';
                                    $mkKode = $mks[$row->mk_kode]->kode ?? $row->mk_kode ?? '-';
                                    $cpmkKode = $cpmks[$row->cpmk_id]->kode ?? '-';
                                @endphp
                                <tr class="matrix-row" data-cpl-id="{{ $cplId }}">
                                    <td class="text-start fw-bold text-primary">{{ $cplKode }}</td>
                                    <td class="text-start fw-semibold">{{ $mkKode }}</td>
                                    <td class="text-start text-secondary">{{ $cpmkKode }}</td>
                                    @foreach ($metodes as $metodeId => $metodeNama)
                                        @php
                                            $penilaianMetode = $rows->where('metode_id', $metodeId)->first();
                                        @endphp
                                        <td>
                                            @if ($penilaianMetode)
                                                <span class="cell-check" title="Terpakai"><i class="bi bi-check-lg"></i></span>
                                            @else
                                                <span class="cell-dash">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr id="emptyMatrixRow">
                                    <td colspan="{{ 3 + count($metodes) }}" class="text-center py-4 text-muted">
                                        Belum ada data metode penilaian yang dikonfigurasi.
                                    </td>
                                </tr>
                            @endforelse
                            <tr id="emptyTabRow" style="display: none;">
                                <td colspan="{{ 3 + count($metodes) }}" class="text-center py-4 text-muted">
                                    Tidak ada data metode penilaian untuk CPL ini.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tabBtns = document.querySelectorAll('.cpl-tab-btn');
            var rows = document.querySelectorAll('.matrix-row');
            var emptyTabRow = document.getElementById('emptyTabRow');

            tabBtns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    tabBtns.forEach(function(b) { b.classList.remove('active'); });
                    this.classList.add('active');

                    var selectedCpl = this.getAttribute('data-cpl');
                    var visibleCount = 0;

                    rows.forEach(function(row) {
                        var rowCpl = row.getAttribute('data-cpl-id');
                        if (selectedCpl === 'all' || rowCpl === selectedCpl) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    if (emptyTabRow) {
                        emptyTabRow.style.display = visibleCount === 0 ? '' : 'none';
                    }
                });
            });
        });
    </script>
@endsection
