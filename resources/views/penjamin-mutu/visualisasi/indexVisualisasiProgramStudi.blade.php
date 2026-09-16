@php
    $routePrefix = [
        'Penjamin Mutu Universitas' => ['prefix' => 'penjamin-mutu.universitas.'],
        'Penjamin Mutu Fakultas' => ['prefix' => 'penjamin-mutu.fakultas.'],
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.fakultas.';
    $isUnivLevel = in_array($userOtoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor']);
@endphp
@extends('penjamin-mutu.template')
@section('title', 'Visualisasi CPL Per Program Studi')
@section('page_title', 'Visualisasi CPL Per Program Studi')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* Layout fix to ensure sticky elements work properly */
        html,
        body,
        .container-scroller,
        .page-body-wrapper,
        .main-panel,
        .content-wrapper,
        .content-wrapper > .row {
            overflow: visible !important;
        }

        /* Modern Dashboard Styling Consistent with other modules */
        .modern-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
            margin-bottom: 1.5rem;
            transition: all 0.2s ease-in-out;
            overflow: visible !important;
        }

        .modern-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        .modern-card-header {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 60px;
            border-top-left-radius: 14px;
            border-top-right-radius: 14px;
        }

        /* Section Titles with Icon and Larger Modern Typography */
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

        .section-title i {
            font-size: 1.25rem;
            color: #1F3BB3;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Hero Header Profile (Fixed / Sticky when scrolling, Prominent, Neat Alignment) */
        .faculty-profile-hero {
            position: -webkit-sticky !important;
            position: sticky !important;
            top: 75px !important;
            z-index: 1020 !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0;
            border-left: 5px solid #1F3BB3 !important;
            border-radius: 14px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            padding: 20px 28px;
            margin-bottom: 24px;
        }

        .faculty-label-tag {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #1F3BB3;
            margin-bottom: 4px;
        }

        .faculty-name-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
            margin-bottom: 8px;
            letter-spacing: -0.01em;
        }

        .faculty-meta-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .meta-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.88rem;
        }

        .meta-label {
            color: #64748b;
            font-weight: 500;
        }

        .meta-value {
            color: #0f172a;
            font-weight: 600;
        }

        .meta-pipe {
            color: #cbd5e1;
            font-weight: 300;
            user-select: none;
        }

        /* 4 Summary Highlight Metric Cards */
        .summary-box-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            transition: all 0.25s ease;
        }

        .summary-box-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        }

        .summary-box-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .summary-box-val {
            font-size: 1.85rem;
            font-weight: 800;
            line-height: 1.1;
            color: #0f172a;
        }

        .summary-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        /* Modern Action Buttons */
        .modern-btn-primary {
            background: #1F3BB3 !important;
            color: #ffffff !important;
            border: none !important;
            font-weight: 600 !important;
            font-size: 0.85rem !important;
            padding: 8px 18px !important;
            border-radius: 8px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            box-shadow: 0 4px 12px rgba(31, 59, 179, 0.2) !important;
            transition: all 0.2s ease !important;
            text-decoration: none !important;
            cursor: pointer !important;
            white-space: nowrap !important;
        }

        .modern-btn-primary:hover {
            background: #182e8e !important;
            box-shadow: 0 6px 16px rgba(31, 59, 179, 0.3) !important;
            transform: translateY(-1px) !important;
            color: #ffffff !important;
        }

        .btn-detail-action {
            background: #eff6ff !important;
            color: #1F3BB3 !important;
            border: 1px solid rgba(31, 59, 179, 0.2) !important;
            font-weight: 600 !important;
            font-size: 0.8rem !important;
            padding: 6px 14px !important;
            border-radius: 6px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
            text-decoration: none !important;
        }

        .btn-detail-action:hover {
            background: #1F3BB3 !important;
            color: #ffffff !important;
        }

        /* Modern & Formal Tables */
        .modern-table-container {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            background: #ffffff;
        }

        .modern-table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .modern-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 12px 14px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
            white-space: nowrap;
        }

        .modern-table tfoot th,
        .modern-table tfoot td {
            background: #f8fafc;
            color: #1e293b;
            font-weight: 700;
            font-size: 0.84rem;
            padding: 12px 14px;
            border-top: 2px solid #cbd5e1;
            vertical-align: middle;
        }

        .modern-table tbody td {
            padding: 12px 14px;
            font-size: 0.86rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
        }

        .modern-table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Smooth Custom Scrollbar & Seamless Sticky Columns (No, Prodi, Jenjang) */
        .table-scrollable-years {
            position: relative;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            max-width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #ffffff;
        }

        .table-scrollable-years::-webkit-scrollbar {
            height: 7px;
        }

        .table-scrollable-years::-webkit-scrollbar-track {
            background: #f8fafc;
            border-radius: 4px;
        }

        .table-scrollable-years::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .table-scrollable-years::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Sticky Column 1: No */
        .col-sticky-no {
            position: sticky !important;
            left: 0px !important;
            width: 50px !important;
            min-width: 50px !important;
            max-width: 50px !important;
            box-sizing: border-box !important;
            z-index: 5 !important;
            text-align: center !important;
        }

        /* Sticky Column 2: Program Studi */
        .col-sticky-prodi {
            position: sticky !important;
            left: 50px !important;
            width: 240px !important;
            min-width: 240px !important;
            max-width: 240px !important;
            box-sizing: border-box !important;
            z-index: 5 !important;
        }

        /* Sticky Column 3: Jenjang (Seamless border) */
        .col-sticky-jenjang {
            position: sticky !important;
            left: 290px !important;
            width: 100px !important;
            min-width: 100px !important;
            max-width: 100px !important;
            box-sizing: border-box !important;
            z-index: 5 !important;
            text-align: center !important;
            border-right: 2px solid #cbd5e1 !important;
            box-shadow: 3px 0 6px -2px rgba(0, 0, 0, 0.08);
        }

        .modern-table thead th.col-sticky-no,
        .modern-table thead th.col-sticky-prodi,
        .modern-table thead th.col-sticky-jenjang {
            background-color: #f8fafc !important;
            padding: 10px 8px !important;
        }

        .modern-table tbody td.col-sticky-no,
        .modern-table tbody td.col-sticky-prodi,
        .modern-table tbody td.col-sticky-jenjang {
            background-color: #ffffff !important;
            padding: 10px 8px !important;
        }

        .modern-table tbody tr:hover td.col-sticky-no,
        .modern-table tbody tr:hover td.col-sticky-prodi,
        .modern-table tbody tr:hover td.col-sticky-jenjang {
            background-color: #f8fafc !important;
        }

        .modern-table tfoot th.col-sticky-no,
        .modern-table tfoot th.col-sticky-prodi,
        .modern-table tfoot th.col-sticky-jenjang {
            background-color: #f8fafc !important;
            padding: 10px 8px !important;
        }

        .progress-custom-bar {
            height: 6px;
            border-radius: 6px;
            background-color: #f1f5f9;
            overflow: hidden;
        }

        .progress-custom-bar .progress-fill {
            height: 100%;
            border-radius: 6px;
            transition: width 0.6s ease;
        }

        /* High-End Modern Segmented Year Filter Control */
        .segmented-filter-bar {
            display: inline-flex;
            align-items: center;
            background: #f1f5f9;
            padding: 3px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            gap: 3px;
        }

        .segmented-filter-btn {
            border: none;
            background: transparent;
            color: #475569;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .segmented-filter-btn:hover {
            color: #1F3BB3;
            background: rgba(255, 255, 255, 0.6);
        }

        .segmented-filter-btn.active {
            background: #ffffff;
            color: #1F3BB3;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
    </style>

    <div class="container-fluid px-3 my-2">
        @if (isset($fakultasCplData) && $fakultasCplData)
            {{-- ========================================================================= --}}
            {{-- 1. HERO HEADER PROFILE (Fixed / Sticky saat scroll ke bawah) --}}
            {{-- ========================================================================= --}}
            <div class="faculty-profile-hero" id="facultyHeroCard">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <div class="faculty-label-tag">EVALUASI CAPAIAN PEMBELAJARAN LULUSAN (CPL)</div>
                        <h3 class="faculty-name-text">
                            {{ $fakultasCplData['fakultas']->nama ?? 'Fakultas' }}
                        </h3>
                        <div class="faculty-meta-row">
                            <div class="meta-item">
                                <span class="meta-label">Universitas:</span>
                                <span class="meta-value">{{ $universitas->nama ?? '-' }}</span>
                            </div>
                            <span class="meta-pipe">|</span>
                            <div class="meta-item">
                                <span class="meta-label">Total Program Studi:</span>
                                <span class="meta-value">{{ count($fakultasCplData['prodi_stats']) }} Program Studi</span>
                            </div>
                            <span class="meta-pipe">|</span>
                            <div class="meta-item">
                                <span class="meta-label">Mahasiswa Dinilai:</span>
                                <span class="meta-value">
                                    {{ $fakultasCplData['summary']['total_mhs_evaluated'] }} Mahasiswa
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Toolbar Tombol Aksi --}}
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        @if ($isUnivLevel && !empty($allFakultas) && count($allFakultas) > 1)
                            <form method="GET" action="{{ url()->current() }}" class="d-inline-block me-1">
                                <select name="fakultas_id" class="form-select form-select-sm" style="min-width: 180px; border-radius: 8px;" onchange="this.form.submit()">
                                    @foreach ($allFakultas as $fak)
                                        <option value="{{ $fak->id }}" {{ (string)$fakultasId === (string)$fak->id ? 'selected' : '' }}>{{ $fak->nama }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @endif

                        <form id="formCetakPdfFakultas" method="POST" action="{{ route($currentPrefix . 'visualisasi.generate-pdfVisualProgramStudi') }}" target="_blank">
                            @csrf
                            <input type="hidden" name="fakultas_id" value="{{ $fakultasId }}">
                            <input type="hidden" name="chartImg" id="inputChartImg" value="">
                            <input type="hidden" name="trendChartImg" id="inputTrendChartImg" value="">

                            <button type="button" id="btnPrintPdf" class="modern-btn-primary">
                                <i class="bi bi-file-earmark-pdf fs-6"></i>
                                Unduh PDF
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- 2. 4 SUMMARY STAT CARDS (Clean Title & Numbers Only) --}}
            {{-- ========================================================================= --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="summary-box-card" style="border-top: 3px solid #3b82f6;">
                        <div>
                            <div class="summary-box-label">Rata-rata Skor CPL</div>
                            <div class="summary-box-val" style="color: #2563eb;">
                                {{ number_format($fakultasCplData['summary']['faculty_avg_skor'], 1) }}
                                <span style="font-size: 0.9rem; font-weight: 500; color: #64748b;">/ 100</span>
                            </div>
                        </div>
                        <div class="summary-icon-wrapper" style="background: rgba(59, 130, 246, 0.1); color: #2563eb;">
                            <i class="bi bi-award-fill"></i>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="summary-box-card" style="border-top: 3px solid #10b981;">
                        <div>
                            <div class="summary-box-label">Rata-rata Capaian CPL</div>
                            <div class="summary-box-val" style="color: #059669;">
                                {{ number_format($fakultasCplData['summary']['faculty_avg_capaian'], 1) }}%
                            </div>
                        </div>
                        <div class="summary-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: #059669;">
                            <i class="bi bi-pie-chart-fill"></i>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="summary-box-card" style="border-top: 3px solid #8b5cf6;">
                        <div>
                            <div class="summary-box-label">Total Program Studi</div>
                            <div class="summary-box-val" style="color: #7c3aed;">
                                {{ count($fakultasCplData['prodi_stats']) }}
                                <span style="font-size: 0.9rem; font-weight: 500; color: #64748b;">Prodi</span>
                            </div>
                        </div>
                        <div class="summary-icon-wrapper" style="background: rgba(139, 92, 246, 0.1); color: #7c3aed;">
                            <i class="bi bi-building"></i>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="summary-box-card" style="border-top: 3px solid #f59e0b;">
                        <div>
                            <div class="summary-box-label">Total Butir CPL</div>
                            <div class="summary-box-val" style="color: #d97706;">
                                {{ $fakultasCplData['summary']['total_cpl_count'] }}
                                <span style="font-size: 0.9rem; font-weight: 500; color: #64748b;">Butir</span>
                            </div>
                        </div>
                        <div class="summary-icon-wrapper" style="background: rgba(245, 158, 11, 0.1); color: #d97706;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- 3. CARD 1: PERKEMBANGAN SKOR CPL ANTAR TAHUN --}}
            {{-- ========================================================================= --}}
            <div class="card modern-card mb-4">
                <div class="modern-card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h5 class="section-title mb-1">
                            <i class="bi bi-graph-up-arrow"></i> Perkembangan Skor CPL Antar Tahun
                        </h5>
                        <p class="text-muted small mb-0">Pemantauan riwayat perkembangan skor evaluasi CPL seluruh program studi pada {{ $fakultasCplData['fakultas']->nama ?? 'Fakultas' }} lintas tahun.</p>
                    </div>

                    {{-- High-End Modern, Formal, Neat & Responsive Year Filter Toolbar --}}
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div id="yearFilterStatusBadge" class="d-inline-flex align-items-center gap-1.5 px-3 py-1.5 bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill font-monospace fw-bold" style="font-size: 0.82rem;">
                            <i class="bi bi-calendar3"></i>
                            <span id="yearFilterStatusText">
                                @if(count($availableYears) > 0)
                                    {{ $availableYears[0] }} - {{ $availableYears[count($availableYears) - 1] }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>

                        {{-- Segmented Pill Filters --}}
                        <div class="segmented-filter-bar">
                            <button type="button" class="segmented-filter-btn active" data-filter="all">
                                <i class="bi bi-grid-fill"></i> Semua Tahun
                            </button>
                            @if (count($availableYears) >= 3)
                                <button type="button" class="segmented-filter-btn" data-filter="3">
                                    3 Tahun Terakhir
                                </button>
                            @endif
                            @if (count($availableYears) >= 5)
                                <button type="button" class="segmented-filter-btn" data-filter="5">
                                    5 Tahun Terakhir
                                </button>
                            @endif
                        </div>

                        {{-- Specific Single Year Selector --}}
                        <select id="selectSingleYearFilter" class="form-select form-select-sm" style="border-radius: 8px; font-weight: 600; font-size: 0.82rem; height: 35px; border: 1px solid #cbd5e1; width: auto; background-color: #ffffff;">
                            <option value="all">Pilih Tahun Tertentu...</option>
                            @foreach ($availableYears as $yr)
                                <option value="{{ $yr }}">Tahun {{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body p-4">
                    {{-- Diagram Garis Tren Tahunan --}}
                    <div class="mb-4">
                        <div style="position: relative; height: 260px; width: 100%;">
                            <canvas id="yearlyTrendChart"></canvas>
                        </div>
                    </div>

                    {{-- Tabel Perkembangan Per Tahun (Seamless Sticky Columns: No, Prodi, Jenjang) --}}
                    <div class="table-scrollable-years">
                        <table class="table modern-table align-middle" id="tableYearlyProgression" style="border-collapse: separate !important; border-spacing: 0 !important; margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th class="text-center col-sticky-no">No</th>
                                    <th class="col-sticky-prodi">Program Studi</th>
                                    <th class="text-center col-sticky-jenjang">Jenjang</th>
                                    @foreach ($availableYears as $yr)
                                        <th style="width: 110px; min-width: 110px;" class="text-center col-year col-year-{{ $yr }}">Tahun {{ $yr }}</th>
                                    @endforeach
                                    <th style="width: 120px; min-width: 120px;" class="text-center">Rata-rata</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($yearlyProgression as $idx => $yp)
                                    <tr>
                                        <td class="text-center fw-bold text-muted col-sticky-no">{{ $idx + 1 }}</td>
                                        <td class="col-sticky-prodi">
                                            <div class="fw-bold text-dark text-truncate" title="{{ $yp['nama'] }}">{{ $yp['nama'] }}</div>
                                            <small class="text-muted">{{ $yp['is_aptikom'] ? 'Aptikom' : 'Non-Aptikom' }}</small>
                                        </td>
                                        <td class="text-center col-sticky-jenjang"><span class="badge bg-light text-secondary border">{{ $yp['jenjang'] }}</span></td>
                                        @foreach ($availableYears as $yr)
                                            <td class="text-center col-year col-year-{{ $yr }}">
                                                @if (isset($yp['scores'][$yr]) && $yp['scores'][$yr] > 0)
                                                    <span class="fw-bold text-dark">
                                                        {{ number_format($yp['scores'][$yr], 1) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="text-center">
                                            <span class="fw-bold text-primary">{{ number_format($yp['overall_skor'], 1) }}</span>
                                            <small class="text-muted">/ 100</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 4 + count($availableYears) }}" class="text-center py-4 text-muted">Belum ada data evaluasi tahunan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-center col-sticky-no"></th>
                                    <th class="text-end fw-bold col-sticky-prodi">Rata-rata Skor Fakultas:</th>
                                    <th class="text-center col-sticky-jenjang"></th>
                                    @foreach ($availableYears as $yr)
                                        <th class="text-center fw-bold text-primary col-year col-year-{{ $yr }}">
                                            {{ number_format($yearlyAverages[$yr]['avg_skor'] ?? 0, 1) }}
                                        </th>
                                    @endforeach
                                    <th class="text-center fw-bold text-primary">
                                        {{ number_format($fakultasCplData['summary']['faculty_avg_skor'], 1) }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- 4. CARD 2: KOMPARASI EVALUASI CPL SELURUH PROGRAM STUDI --}}
            {{-- ========================================================================= --}}
            <div class="card modern-card mb-4">
                <div class="modern-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="section-title mb-0">
                        <i class="bi bi-bar-chart-line-fill"></i> Komparasi Evaluasi CPL Seluruh Program Studi
                    </h5>
                    <div class="d-flex align-items-center gap-3 small text-muted">
                        <span class="d-flex align-items-center gap-1.5"><span style="width: 12px; height: 12px; background: #3b82f6; border-radius: 3px; display: inline-block;"></span> Skor CPL (0-100)</span>
                        <span class="d-flex align-items-center gap-1.5"><span style="width: 12px; height: 12px; background: #10b981; border-radius: 3px; display: inline-block;"></span> Capaian CPL (%)</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    {{-- Diagram Batang Komparasi --}}
                    <div class="mb-4">
                        <div style="position: relative; height: 300px; width: 100%;">
                            <canvas id="facultyCplChart"></canvas>
                        </div>
                    </div>

                    {{-- Tabel Komparasi Seluruh Program Studi --}}
                    <div class="modern-table-container">
                        <div class="table-responsive">
                            <table class="table modern-table align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;" class="text-center">No</th>
                                        <th style="min-width: 220px;">Program Studi</th>
                                        <th class="text-center" style="width: 130px;">Mahasiswa Dinilai</th>
                                        <th class="text-center" style="width: 110px;">Total CPL</th>
                                        <th style="width: 210px;">
                                            Rata-rata Skor CPL
                                        </th>
                                        <th style="width: 190px;">
                                            Rata-rata Capaian CPL
                                            <small class="text-muted d-block fw-normal" style="font-size: 0.68rem;">(Persentase MK Lulus)</small>
                                        </th>
                                        <th class="text-center" style="width: 140px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($fakultasCplData['prodi_stats'] as $index => $prodiItem)
                                        <tr>
                                            <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $prodiItem['nama'] }}</div>
                                                <div class="d-flex align-items-center gap-1.5 mt-1">
                                                    <span class="badge bg-light text-secondary border" style="font-size: 0.7rem; border-radius: 4px;">{{ $prodiItem['jenjang'] }}</span>
                                                    @if ($prodiItem['is_aptikom'])
                                                        <span class="badge bg-info bg-opacity-10 text-primary border border-info border-opacity-25" style="font-size: 0.7rem; border-radius: 4px;">Aptikom</span>
                                                    @else
                                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25" style="font-size: 0.7rem; border-radius: 4px;">Non-Aptikom</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if ($prodiItem['total_mhs'] > 0)
                                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-2.5 py-1.5" style="font-size: 0.8rem; border-radius: 8px;">
                                                        <i class="bi bi-person-fill me-1"></i>{{ $prodiItem['total_mhs'] }} Mhs
                                                    </span>
                                                @else
                                                    <span class="text-muted small">0 Mhs</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark fw-bold border px-2.5 py-1.5" style="font-size: 0.8rem; border-radius: 8px;">
                                                    {{ $prodiItem['total_cpl'] }} CPL
                                                </span>
                                            </td>
                                            <td>
                                                @if ($prodiItem['avg_skor_cpl'] > 0 || $prodiItem['avg_capaian_cpl'] > 0)
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="fw-bold text-dark small">{{ number_format($prodiItem['avg_skor_cpl'], 1) }} <span class="text-muted font-monospace">/ 100</span></span>
                                                    </div>
                                                    <div class="progress-custom-bar">
                                                        <div class="progress-fill" style="width: {{ min(100, $prodiItem['avg_skor_cpl']) }}%; background-color: #3b82f6;"></div>
                                                    </div>
                                                @else
                                                    <span class="text-muted small">Belum Ada Data</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($prodiItem['avg_skor_cpl'] > 0 || $prodiItem['avg_capaian_cpl'] > 0)
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="fw-bold text-dark small">{{ number_format($prodiItem['avg_capaian_cpl'], 1) }}%</span>
                                                    </div>
                                                    <div class="progress-custom-bar">
                                                        <div class="progress-fill" style="width: {{ min(100, $prodiItem['avg_capaian_cpl']) }}%; background-color: #10b981;"></div>
                                                    </div>
                                                @else
                                                    <span class="text-muted small">Belum Ada Data</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn-detail-action btn-select-prodi-detail" 
                                                    data-prodi-id="{{ $prodiItem['id'] }}"
                                                    title="Lihat Rincian Butir CPL pada Halaman Ini">
                                                    <i class="bi bi-eye"></i> Rincian CPL
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data program studi.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- 5. CARD 3: RINCIAN CAPAIAN CPL PER PROGRAM STUDI (Full Inline Section with Reliable Select) --}}
            {{-- ========================================================================= --}}
            <div class="card modern-card mb-4" id="sectionRincianCpl">
                <div class="modern-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="section-title mb-1">
                            <i class="bi bi-list-check"></i> Rincian Capaian CPL Per Program Studi
                        </h5>
                        <p class="text-muted small mb-0">Evaluasi rata-rata perolehan skor asesmen dan ketercapaian per butir CPL pada masing-masing program studi</p>
                    </div>

                    {{-- Form Cetak PDF Prodi Aktif --}}
                    <form id="formCetakPdfProdiAktif" method="POST" action="{{ route($currentPrefix . 'visualisasi.generate-pdfVisualProgramStudi') }}" target="_blank">
                        @csrf
                        <input type="hidden" name="fakultas_id" value="{{ $fakultasId }}">
                        <input type="hidden" name="prodi_id" id="inputProdiIdInline" value="">
                        <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1.5" style="border-radius: 8px; font-weight: 600; font-size: 0.8rem; padding: 6px 14px;">
                            <i class="bi bi-file-earmark-pdf"></i> Unduh PDF
                        </button>
                    </form>
                </div>
                <div class="card-body p-4">
                    {{-- Standard, Solid & Searchable Program Studi Selector --}}
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6 col-lg-5">
                            <label for="selectProgramStudiInline" class="small fw-bold text-muted mb-1.5 d-block text-uppercase" style="letter-spacing: 0.5px;">
                                <i class="bi bi-mortarboard-fill text-primary me-1"></i> Pilih Program Studi:
                            </label>
                            <select id="selectProgramStudiInline" class="form-select" style="height: 42px; border-radius: 8px; font-weight: 600; font-size: 0.88rem; border: 1px solid #cbd5e1; background-color: #ffffff; color: #1e293b; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                                @foreach ($fakultasCplData['prodi_stats'] as $pOption)
                                    <option value="{{ $pOption['id'] }}">
                                        {{ $pOption['nama'] }} ({{ $pOption['jenjang'] }}) {{ $pOption['total_mhs'] == 0 ? '- [Belum Ada Mahasiswa]' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- 3 Metric Highlight Box untuk Prodi yang Aktif --}}
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-4">
                            <div class="p-3 bg-light border rounded-3 text-center">
                                <div class="text-muted small fw-bold text-uppercase">Rata-rata Skor CPL</div>
                                <div class="fs-4 fw-bold text-primary mt-1" id="inlineProdiSkorBadge">0 / 100</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="p-3 bg-light border rounded-3 text-center">
                                <div class="text-muted small fw-bold text-uppercase">Rata-rata Capaian CPL</div>
                                <div class="fs-4 fw-bold text-success mt-1" id="inlineProdiCapaianBadge">0%</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="p-3 bg-light border rounded-3 text-center">
                                <div class="text-muted small fw-bold text-uppercase">Total Butir CPL</div>
                                <div class="fs-4 fw-bold text-dark mt-1" id="inlineProdiTotalCplBadge">0 CPL</div>
                                <div class="text-muted small" style="font-size: 0.72rem;" id="inlineProdiMhsBadge">0 Mahasiswa Dinilai</div>
                            </div>
                        </div>
                    </div>

                    {{-- Filter Aspek & Search Bar --}}
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 bg-light p-2.5 rounded-3 border">
                        <div class="d-flex align-items-center gap-2">
                            <span class="small fw-bold text-muted text-nowrap"><i class="bi bi-funnel text-primary me-1"></i>Filter Aspek:</span>
                            <select id="inlineSelectAspek" class="form-select form-select-sm bg-white" style="min-width: 230px; border-radius: 8px;">
                                <option value="all">Semua Aspek</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="inlineSearchCpl" class="form-control border-start-0 ps-0" placeholder="Cari kode atau judul CPL...">
                            </div>
                        </div>
                    </div>

                    {{-- Tabel Butir CPL Inline --}}
                    <div class="modern-table-container">
                        <div class="table-responsive">
                            <table class="table modern-table align-middle mb-0" style="min-width: 900px; width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;" class="text-center">No</th>
                                        <th style="width: 120px;">Kode CPL</th>
                                        <th style="width: 160px;">Aspek</th>
                                        <th style="min-width: 320px;">Deskripsi / Judul CPL</th>
                                        <th style="width: 190px;">Rata-rata Skor</th>
                                        <th style="width: 170px;">Capaian (%)</th>
                                    </tr>
                                </thead>
                                <tbody id="inlineCplTableBody">
                                    {{-- Rendered dynamically via JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <div class="card modern-card p-4 text-center">
                <i class="bi bi-info-circle text-muted mb-2" style="font-size: 36px;"></i>
                <h5 class="fw-bold text-dark">Data Fakultas Tidak Ditemukan</h5>
                <p class="text-muted small mb-0">Pastikan akun Anda terasosiasi dengan data Fakultas yang valid untuk mengakses visualisasi evaluasi CPL per program studi.</p>
            </div>
        @endif
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
        <script>
            var facultyChartInstance = null;
            var trendChartInstance = null;

            var allAvailableYears = @json($availableYears ?? []);
            var yearlyProgressionData = @json($yearlyProgression ?? []);
            var allProdiStats = @json($fakultasCplData['prodi_stats'] ?? []);

            var selectedProdiId = (allProdiStats && allProdiStats.length > 0) ? allProdiStats[0].id : null;
            var activeInlineAspekFilter = 'all';
            var activeInlineSearchQuery = '';
            var currentActiveProdiData = null;

            $(function() {
                @if (isset($fakultasCplData) && $fakultasCplData)
                    initYearlyTrendChart(allAvailableYears);
                    initFacultyCplChart();
                @endif
                initYearFilterToolbar();
                initProdiSelector();
                initPdfPrintHandler();
            });

            // 1. Chart Tren Tahunan
            function initYearlyTrendChart(displayedYears) {
                var ctx = document.getElementById('yearlyTrendChart');
                if (!ctx) return;

                if (trendChartInstance) {
                    trendChartInstance.destroy();
                }

                var colors = ['#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#ec4899', '#06b6d4', '#14b8a6', '#f97316'];
                var datasets = [];

                yearlyProgressionData.forEach(function(item, idx) {
                    var dataPoints = [];
                    displayedYears.forEach(function(yr) {
                        dataPoints.push(item.scores[yr] || 0);
                    });

                    var clr = colors[idx % colors.length];
                    datasets.push({
                        label: item.nama,
                        data: dataPoints,
                        borderColor: clr,
                        backgroundColor: clr,
                        fill: false,
                        tension: 0.3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        borderWidth: 2
                    });
                });

                trendChartInstance = new Chart(ctx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: displayedYears.map(function(y) { return 'Tahun ' + y; }),
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    max: 100,
                                    stepSize: 20
                                },
                                gridLines: { color: '#f1f5f9' }
                            }],
                            xAxes: [{
                                gridLines: { color: '#f1f5f9' }
                            }]
                        },
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                fontSize: 11
                            }
                        },
                        tooltips: {
                            backgroundColor: '#1e293b',
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    var label = data.datasets[tooltipItem.datasetIndex].label || '';
                                    return label + ': ' + tooltipItem.yLabel + ' / 100';
                                }
                            }
                        }
                    }
                });
            }

            // Toolbar Filter Tahun: High-End Segmented Pills & Dropdown
            function initYearFilterToolbar() {
                var totalYears = allAvailableYears.length;

                function applyYearFilter(displayedYears) {
                    // Update Status Badge dengan Rentang Tahun Langsung
                    if (displayedYears.length === 1) {
                        $('#yearFilterStatusText').text(displayedYears[0]);
                    } else if (displayedYears.length > 1) {
                        $('#yearFilterStatusText').text(displayedYears[0] + ' - ' + displayedYears[displayedYears.length - 1]);
                    } else {
                        $('#yearFilterStatusText').text('-');
                    }

                    // Update Table Column Visibility
                    $('.col-year').hide();
                    displayedYears.forEach(function(yr) {
                        $('.col-year-' + yr).show();
                    });

                    // Update Line Chart
                    initYearlyTrendChart(displayedYears);
                }

                // Initial status text
                if (totalYears > 0) {
                    var firstY = allAvailableYears[0];
                    var lastY = allAvailableYears[totalYears - 1];
                    $('#yearFilterStatusText').text(firstY === lastY ? firstY : firstY + ' - ' + lastY);
                }

                // Preset Buttons
                $('.segmented-filter-btn').on('click', function() {
                    $('.segmented-filter-btn').removeClass('active');
                    $(this).addClass('active');
                    $('#selectSingleYearFilter').val('all');

                    var filter = $(this).data('filter');
                    if (filter === 'all') {
                        applyYearFilter(allAvailableYears);
                    } else {
                        var count = parseInt(filter);
                        var sliced = allAvailableYears.slice(-count);
                        applyYearFilter(sliced);
                    }
                });

                // Single Year Dropdown Select
                $('#selectSingleYearFilter').on('change', function() {
                    var val = $(this).val();
                    if (val === 'all') {
                        $('.segmented-filter-btn').removeClass('active');
                        $('.segmented-filter-btn[data-filter="all"]').addClass('active');
                        applyYearFilter(allAvailableYears);
                    } else {
                        $('.segmented-filter-btn').removeClass('active');
                        applyYearFilter([val]);
                    }
                });
            }

            // 2. Chart Komparasi Antar Prodi
            function initFacultyCplChart() {
                var ctx = document.getElementById('facultyCplChart');
                if (!ctx) return;

                if (!allProdiStats || allProdiStats.length === 0) return;

                var labels = allProdiStats.map(function(p) {
                    return p.nama + (p.jenjang ? ' (' + p.jenjang + ')' : '');
                });
                var skorData = allProdiStats.map(function(p) { return p.avg_skor_cpl || 0; });
                var capaianData = allProdiStats.map(function(p) { return p.avg_capaian_cpl || 0; });

                facultyChartInstance = new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Rata-rata Skor CPL (0-100)',
                                data: skorData,
                                backgroundColor: 'rgba(59, 130, 246, 0.75)',
                                borderColor: 'rgba(37, 99, 235, 1)',
                                borderWidth: 1.5,
                                borderRadius: 6
                            },
                            {
                                label: 'Rata-rata Capaian CPL (%)',
                                data: capaianData,
                                backgroundColor: 'rgba(16, 185, 129, 0.75)',
                                borderColor: 'rgba(5, 150, 105, 1)',
                                borderWidth: 1.5,
                                borderRadius: 6
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    max: 100,
                                    stepSize: 20
                                },
                                gridLines: { color: '#f1f5f9' }
                            }],
                            xAxes: [{
                                gridLines: { display: false },
                                ticks: {
                                    autoSkip: false,
                                    maxRotation: 20,
                                    minRotation: 0,
                                    callback: function(val) {
                                        return val.length > 25 ? val.substr(0, 23) + '...' : val;
                                    }
                                }
                            }]
                        },
                        legend: { display: false },
                        tooltips: {
                            backgroundColor: '#1e293b',
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    var datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
                                    var value = tooltipItem.yLabel;
                                    return datasetLabel + ': ' + value + (tooltipItem.datasetIndex === 0 ? ' / 100' : '%');
                                }
                            }
                        }
                    }
                });
            }

            // 3. Program Studi Selector & Inline Rincian CPL
            function initProdiSelector() {
                if (!allProdiStats || allProdiStats.length === 0) return;

                var $select = $('#selectProgramStudiInline');

                // Initial selection
                var initId = $select.val() || allProdiStats[0].id;
                renderInlineProdiDetail(initId);

                $select.on('change', function() {
                    var pId = $(this).val();
                    renderInlineProdiDetail(pId);
                });

                // Button "Rincian CPL" on comparison table
                $('.btn-select-prodi-detail').on('click', function() {
                    var pId = $(this).data('prodi-id');
                    $select.val(pId).trigger('change');

                    $('html, body').animate({
                        scrollTop: $('#sectionRincianCpl').offset().top - 80
                    }, 500);
                });

                // Aspek Filter
                $('#inlineSelectAspek').on('change', function() {
                    activeInlineAspekFilter = $(this).val();
                    renderInlineCplTable();
                });

                // Search Filter
                $('#inlineSearchCpl').on('input', function() {
                    activeInlineSearchQuery = $(this).val().toLowerCase().trim();
                    renderInlineCplTable();
                });
            }

            function renderInlineProdiDetail(prodiId) {
                var prodi = allProdiStats.find(function(p) { return p.id == prodiId; });
                if (!prodi) return;

                currentActiveProdiData = prodi;
                selectedProdiId = prodiId;
                $('#inputProdiIdInline').val(prodiId);

                $('#inlineProdiSkorBadge').text(parseFloat(prodi.avg_skor_cpl || 0).toFixed(1) + ' / 100');
                $('#inlineProdiCapaianBadge').text(parseFloat(prodi.avg_capaian_cpl || 0).toFixed(1) + '%');
                $('#inlineProdiTotalCplBadge').text((prodi.total_cpl || 0) + ' CPL');
                $('#inlineProdiMhsBadge').text((prodi.total_mhs || 0) + ' Mahasiswa Dinilai');

                // Build Aspek Options
                var cpls = prodi.cpl_details || [];
                var countAll = cpls.length;
                var aspekCounts = {};
                cpls.forEach(function(c) {
                    var a = c.aspek || 'Umum';
                    aspekCounts[a] = (aspekCounts[a] || 0) + 1;
                });

                var aspekOptionsHtml = '<option value="all">Semua Aspek (' + countAll + ')</option>';
                Object.keys(aspekCounts).forEach(function(aspName) {
                    aspekOptionsHtml += '<option value="' + aspName + '">' + aspName + ' (' + aspekCounts[aspName] + ')</option>';
                });
                $('#inlineSelectAspek').html(aspekOptionsHtml).val('all');
                activeInlineAspekFilter = 'all';
                activeInlineSearchQuery = '';
                $('#inlineSearchCpl').val('');

                renderInlineCplTable();
            }

            function renderInlineCplTable() {
                var tbody = $('#inlineCplTableBody');
                tbody.empty();

                if (!currentActiveProdiData || !currentActiveProdiData.cpl_details || currentActiveProdiData.cpl_details.length === 0) {
                    tbody.append('<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data butir CPL pada program studi ini.</td></tr>');
                    return;
                }

                var filtered = currentActiveProdiData.cpl_details.filter(function(cpl) {
                    var matchAspek = true;
                    if (activeInlineAspekFilter !== 'all') {
                        matchAspek = (cpl.aspek === activeInlineAspekFilter);
                    }

                    var matchSearch = true;
                    if (activeInlineSearchQuery !== '') {
                        var kode = (cpl.kode || '').toLowerCase();
                        var judul = (cpl.judul || '').toLowerCase();
                        var aspek = (cpl.aspek || '').toLowerCase();
                        matchSearch = kode.includes(activeInlineSearchQuery) || judul.includes(activeInlineSearchQuery) || aspek.includes(activeInlineSearchQuery);
                    }

                    return matchAspek && matchSearch;
                });

                if (filtered.length === 0) {
                    tbody.append('<tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada butir CPL yang cocok dengan filter.</td></tr>');
                    return;
                }

                filtered.forEach(function(cpl, idx) {
                    var row = '<tr>' +
                        '<td class="text-center fw-bold text-muted">' + (idx + 1) + '</td>' +
                        '<td style="white-space: nowrap;"><span class="badge bg-primary bg-opacity-10 text-primary fw-bold font-monospace px-2.5 py-1.5" style="border-radius:6px; font-size: 0.85rem;">' + cpl.kode + '</span></td>' +
                        '<td style="white-space: nowrap;"><span class="badge bg-light text-dark border px-2.5 py-1" style="border-radius:6px; font-size: 0.75rem;">' + (cpl.aspek || '-') + '</span></td>' +
                        '<td class="small text-secondary" style="min-width: 320px; line-height: 1.45;">' + (cpl.judul || '-') + '</td>' +
                        '<td style="white-space: nowrap; min-width: 190px;">' +
                            '<div class="d-flex justify-content-between align-items-center small fw-bold mb-1">' +
                                '<span>' + parseFloat(cpl.avg_skor).toFixed(1) + ' <small class="text-muted font-monospace">/ 100</small></span>' +
                            '</div>' +
                            '<div class="progress-custom-bar"><div class="progress-fill" style="width:' + Math.min(100, cpl.avg_skor) + '%; background-color:#3b82f6;"></div></div>' +
                        '</td>' +
                        '<td style="white-space: nowrap; min-width: 170px;">' +
                            '<div class="d-flex justify-content-between align-items-center small fw-bold mb-1">' +
                                '<span>' + parseFloat(cpl.avg_capaian).toFixed(1) + '%</span>' +
                            '</div>' +
                            '<div class="progress-custom-bar"><div class="progress-fill" style="width:' + Math.min(100, cpl.avg_capaian) + '%; background-color:#10b981;"></div></div>' +
                        '</td>' +
                    '</tr>';
                    tbody.append(row);
                });
            }

            // PDF Print Handler
            function initPdfPrintHandler() {
                $('#btnPrintPdf').on('click', function() {
                    var c1 = document.getElementById('facultyCplChart');
                    if (c1) {
                        try { $('#inputChartImg').val(c1.toDataURL('image/png')); } catch (e) {}
                    }
                    var c2 = document.getElementById('yearlyTrendChart');
                    if (c2) {
                        try { $('#inputTrendChartImg').val(c2.toDataURL('image/png')); } catch (e) {}
                    }

                    $('#formCetakPdfFakultas').submit();
                });
            }
        </script>
    @endpush
@endsection
