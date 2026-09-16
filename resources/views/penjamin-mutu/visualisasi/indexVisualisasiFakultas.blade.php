@php
    $routePrefix = [
        'Penjamin Mutu Universitas' => ['prefix' => 'penjamin-mutu.universitas.'],
        'Penjamin Mutu Fakultas' => ['prefix' => 'penjamin-mutu.fakultas.'],
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.universitas.';
    $isUnivLevel = in_array($userOtoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor', 'Admin Universitas']);
@endphp
@extends('penjamin-mutu.template')
@section('title', 'Visualisasi CPL Per Fakultas')
@section('page_title', 'Visualisasi CPL Per Fakultas')

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
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
            height: 100%;
        }

        .summary-box-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
        }

        .summary-box-label {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
            margin-bottom: 4px;
        }

        .summary-box-val {
            font-size: 1.65rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .summary-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        /* Buttons & Badges */
        .modern-btn-primary {
            background: #1F3BB3 !important;
            color: #ffffff !important;
            border: none !important;
            font-weight: 600 !important;
            font-size: 0.84rem !important;
            padding: 8px 18px !important;
            border-radius: 8px !important;
            display: inline-flex !important;
            align-items: center !important;
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

        /* Smooth Custom Scrollbar & Seamless Sticky Columns (No, Fakultas, Total Prodi) */
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

        /* Sticky Column 2: Fakultas */
        .col-sticky-fakultas {
            position: sticky !important;
            left: 50px !important;
            width: 240px !important;
            min-width: 240px !important;
            max-width: 240px !important;
            box-sizing: border-box !important;
            z-index: 5 !important;
        }

        /* Sticky Column 3: Total Prodi (Seamless border) */
        .col-sticky-prodi-count {
            position: sticky !important;
            left: 290px !important;
            width: 110px !important;
            min-width: 110px !important;
            max-width: 110px !important;
            box-sizing: border-box !important;
            z-index: 5 !important;
            text-align: center !important;
            border-right: 2px solid #cbd5e1 !important;
            box-shadow: 3px 0 6px -2px rgba(0, 0, 0, 0.08);
        }

        .modern-table thead th.col-sticky-no,
        .modern-table thead th.col-sticky-fakultas,
        .modern-table thead th.col-sticky-prodi-count {
            background-color: #f8fafc !important;
            padding: 10px 8px !important;
        }

        .modern-table tbody td.col-sticky-no,
        .modern-table tbody td.col-sticky-fakultas,
        .modern-table tbody td.col-sticky-prodi-count {
            background-color: #ffffff !important;
            padding: 10px 8px !important;
        }

        .modern-table tbody tr:hover td.col-sticky-no,
        .modern-table tbody tr:hover td.col-sticky-fakultas,
        .modern-table tbody tr:hover td.col-sticky-prodi-count {
            background-color: #f8fafc !important;
        }

        .modern-table tfoot th.col-sticky-no,
        .modern-table tfoot th.col-sticky-fakultas,
        .modern-table tfoot th.col-sticky-prodi-count {
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
        @if (isset($universitasCplData) && $universitasCplData && !empty($universitasCplData['fakultas_stats']))
            {{-- ========================================================================= --}}
            {{-- 1. HERO HEADER PROFILE (Fixed / Sticky saat scroll ke bawah) --}}
            {{-- ========================================================================= --}}
            <div class="faculty-profile-hero" id="facultyHeroCard">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <div class="faculty-label-tag">EVALUASI CAPAIAN PEMBELAJARAN LULUSAN (CPL) TINGKAT UNIVERSITAS</div>
                        <h3 class="faculty-name-text">
                            {{ $universitas->nama ?? 'Universitas' }}
                        </h3>
                        <div class="faculty-meta-row">
                            <div class="meta-item">
                                <span class="meta-label">Total Fakultas:</span>
                                <span class="meta-value">{{ $universitasCplData['summary']['total_fakultas_count'] }} Fakultas</span>
                            </div>
                            <span class="meta-pipe">|</span>
                            <div class="meta-item">
                                <span class="meta-label">Total Program Studi:</span>
                                <span class="meta-value">{{ $universitasCplData['summary']['total_prodi_count'] }} Program Studi</span>
                            </div>
                            <span class="meta-pipe">|</span>
                            <div class="meta-item">
                                <span class="meta-label">Mahasiswa Dinilai:</span>
                                <span class="meta-value">
                                    {{ $universitasCplData['summary']['total_mhs_evaluated'] }} Mahasiswa
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Toolbar Tombol Aksi --}}
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <form id="formCetakPdfUniv" method="POST" action="{{ route($currentPrefix . 'visualisasi.generate-pdfVisualFakultas') }}" target="_blank">
                            @csrf
                            <input type="hidden" name="universitas_id" value="{{ $universitasId }}">
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
                                {{ number_format($universitasCplData['summary']['univ_avg_skor'], 1) }}
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
                                {{ number_format($universitasCplData['summary']['univ_avg_capaian'], 1) }}%
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
                            <div class="summary-box-label">Total Fakultas</div>
                            <div class="summary-box-val" style="color: #7c3aed;">
                                {{ $universitasCplData['summary']['total_fakultas_count'] }}
                                <span style="font-size: 0.9rem; font-weight: 500; color: #64748b;">Fakultas</span>
                            </div>
                        </div>
                        <div class="summary-icon-wrapper" style="background: rgba(139, 92, 246, 0.1); color: #7c3aed;">
                            <i class="bi bi-buildings-fill"></i>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="summary-box-card" style="border-top: 3px solid #f59e0b;">
                        <div>
                            <div class="summary-box-label">Total Program Studi</div>
                            <div class="summary-box-val" style="color: #d97706;">
                                {{ $universitasCplData['summary']['total_prodi_count'] }}
                                <span style="font-size: 0.9rem; font-weight: 500; color: #64748b;">Prodi</span>
                            </div>
                        </div>
                        <div class="summary-icon-wrapper" style="background: rgba(245, 158, 11, 0.1); color: #d97706;">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- 3. CARD 1: PERKEMBANGAN SKOR CPL ANTAR TAHUN (TINGKAT FAKULTAS) --}}
            {{-- ========================================================================= --}}
            <div class="card modern-card mb-4">
                <div class="modern-card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h5 class="section-title mb-1">
                            <i class="bi bi-graph-up-arrow"></i> Perkembangan Skor CPL Antar Tahun
                        </h5>
                        <p class="text-muted small mb-0">Pemantauan riwayat perkembangan skor evaluasi CPL seluruh fakultas pada {{ $universitas->nama ?? 'Universitas' }} lintas tahun.</p>
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

                    {{-- Tabel Perkembangan Per Tahun (Seamless Sticky Columns: No, Fakultas, Total Prodi) --}}
                    <div class="table-scrollable-years">
                        <table class="table modern-table align-middle" id="tableYearlyProgression" style="border-collapse: separate !important; border-spacing: 0 !important; margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th class="text-center col-sticky-no">No</th>
                                    <th class="col-sticky-fakultas">Fakultas</th>
                                    <th class="text-center col-sticky-prodi-count">Total Prodi</th>
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
                                        <td class="col-sticky-fakultas">
                                            <div class="fw-bold text-dark text-truncate" title="{{ $yp['nama'] }}">{{ $yp['nama'] }}</div>
                                        </td>
                                        <td class="text-center col-sticky-prodi-count"><span class="badge bg-light text-secondary border">{{ $yp['total_prodi'] }} Prodi</span></td>
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
                                    <th class="text-end fw-bold col-sticky-fakultas">Rata-rata Skor Universitas:</th>
                                    <th class="text-center col-sticky-prodi-count"></th>
                                    @foreach ($availableYears as $yr)
                                        <th class="text-center fw-bold text-primary col-year col-year-{{ $yr }}">
                                            {{ number_format($yearlyAverages[$yr]['avg_skor'] ?? 0, 1) }}
                                        </th>
                                    @endforeach
                                    <th class="text-center fw-bold text-primary">
                                        {{ number_format($universitasCplData['summary']['univ_avg_skor'], 1) }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- 4. CARD 2: KOMPARASI EVALUASI CPL SELURUH FAKULTAS --}}
            {{-- ========================================================================= --}}
            <div class="card modern-card mb-4">
                <div class="modern-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="section-title mb-0">
                        <i class="bi bi-bar-chart-line-fill"></i> Komparasi Evaluasi CPL Seluruh Fakultas
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

                    {{-- Tabel Komparasi Seluruh Fakultas --}}
                    <div class="modern-table-container">
                        <div class="table-responsive">
                            <table class="table modern-table align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;" class="text-center">No</th>
                                        <th style="min-width: 220px;">Fakultas</th>
                                        <th class="text-center" style="width: 130px;">Program Studi</th>
                                        <th class="text-center" style="width: 130px;">Mahasiswa Dinilai</th>
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
                                    @forelse ($universitasCplData['fakultas_stats'] as $index => $fakItem)
                                        <tr>
                                            <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $fakItem['nama'] }}</div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-secondary border fw-bold px-2.5 py-1.5" style="font-size: 0.8rem; border-radius: 8px;">
                                                    {{ $fakItem['total_prodi'] }} Prodi
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if ($fakItem['total_mhs'] > 0)
                                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-2.5 py-1.5" style="font-size: 0.8rem; border-radius: 8px;">
                                                        <i class="bi bi-person-fill me-1"></i>{{ $fakItem['total_mhs'] }} Mhs
                                                    </span>
                                                @else
                                                    <span class="text-muted small">0 Mhs</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($fakItem['avg_skor_cpl'] > 0 || $fakItem['avg_capaian_cpl'] > 0)
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="fw-bold text-dark small">{{ number_format($fakItem['avg_skor_cpl'], 1) }} <span class="text-muted font-monospace">/ 100</span></span>
                                                    </div>
                                                    <div class="progress-custom-bar">
                                                        <div class="progress-fill" style="width: {{ min(100, $fakItem['avg_skor_cpl']) }}%; background-color: #3b82f6;"></div>
                                                    </div>
                                                @else
                                                    <span class="text-muted small">Belum Ada Data</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($fakItem['avg_skor_cpl'] > 0 || $fakItem['avg_capaian_cpl'] > 0)
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="fw-bold text-dark small">{{ number_format($fakItem['avg_capaian_cpl'], 1) }}%</span>
                                                    </div>
                                                    <div class="progress-custom-bar">
                                                        <div class="progress-fill" style="width: {{ min(100, $fakItem['avg_capaian_cpl']) }}%; background-color: #10b981;"></div>
                                                    </div>
                                                @else
                                                    <span class="text-muted small">Belum Ada Data</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn-detail-action btn-select-fakultas-detail" 
                                                    data-fakultas-id="{{ $fakItem['id'] }}"
                                                    title="Lihat Rincian Fakultas pada Halaman Ini">
                                                    <i class="bi bi-eye"></i> Rincian CPL
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data fakultas.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- 5. CARD 3: RINCIAN CAPAIAN CPL PER FAKULTAS (Full Inline Section with Reliable Select) --}}
            {{-- ========================================================================= --}}
            <div class="card modern-card mb-4" id="sectionRincianFakultas">
                <div class="modern-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="section-title mb-1">
                            <i class="bi bi-list-check"></i> Rincian Capaian CPL Per Fakultas
                        </h5>
                        <p class="text-muted small mb-0">Evaluasi komparasi program studi dan perolehan skor asesmen per butir CPL pada fakultas yang dipilih</p>
                    </div>

                    {{-- Form Cetak PDF Fakultas Aktif --}}
                    <form id="formCetakPdfFakultasAktif" method="POST" action="{{ route($currentPrefix . 'visualisasi.generate-pdfVisualFakultas') }}" target="_blank">
                        @csrf
                        <input type="hidden" name="universitas_id" value="{{ $universitasId }}">
                        <input type="hidden" name="fakultas_id" id="inputFakultasIdInline" value="">
                        <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1.5" style="border-radius: 8px; font-weight: 600; font-size: 0.8rem; padding: 6px 14px;">
                            <i class="bi bi-file-earmark-pdf"></i> Unduh PDF
                        </button>
                    </form>
                </div>
                <div class="card-body p-4">
                    {{-- Standard, Solid & Searchable Fakultas Selector --}}
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6 col-lg-5">
                            <label for="selectFakultasInline" class="small fw-bold text-muted mb-1.5 d-block text-uppercase" style="letter-spacing: 0.5px;">
                                <i class="bi bi-buildings-fill text-primary me-1"></i> Pilih Fakultas:
                            </label>
                            <select id="selectFakultasInline" class="form-select" style="height: 42px; border-radius: 8px; font-weight: 600; font-size: 0.88rem; border: 1px solid #cbd5e1; background-color: #ffffff; color: #1e293b; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                                @foreach ($universitasCplData['fakultas_stats'] as $fOption)
                                    <option value="{{ $fOption['id'] }}">
                                        {{ $fOption['nama'] }} ({{ $fOption['total_prodi'] }} Prodi) {{ $fOption['total_mhs'] == 0 ? '- [Belum Ada Mahasiswa]' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- 3 Metric Highlight Box untuk Fakultas yang Aktif --}}
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-4">
                            <div class="p-3 bg-light border rounded-3 text-center">
                                <div class="text-muted small fw-bold text-uppercase">Rata-rata Skor CPL</div>
                                <div class="fs-4 fw-bold text-primary mt-1" id="inlineFakultasSkorBadge">0 / 100</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="p-3 bg-light border rounded-3 text-center">
                                <div class="text-muted small fw-bold text-uppercase">Rata-rata Capaian CPL</div>
                                <div class="fs-4 fw-bold text-success mt-1" id="inlineFakultasCapaianBadge">0%</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="p-3 bg-light border rounded-3 text-center">
                                <div class="text-muted small fw-bold text-uppercase">Total Program Studi</div>
                                <div class="fs-4 fw-bold text-dark mt-1" id="inlineFakultasTotalProdiBadge">0 Prodi</div>
                                <div class="text-muted small" style="font-size: 0.72rem;" id="inlineFakultasMhsBadge">0 Mahasiswa Dinilai</div>
                            </div>
                        </div>
                    </div>

                    {{-- Tabel Program Studi dalam Fakultas Tersebut --}}
                    <h6 class="fw-bold text-dark mb-2.5"><i class="bi bi-mortarboard-fill text-primary me-1.5"></i>Program Studi pada Fakultas Ini:</h6>
                    <div class="modern-table-container mb-4">
                        <div class="table-responsive">
                            <table class="table modern-table align-middle mb-0" style="min-width: 700px; width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;" class="text-center">No</th>
                                        <th style="min-width: 200px;">Program Studi</th>
                                        <th class="text-center" style="width: 100px;">Jenjang</th>
                                        <th class="text-center" style="width: 120px;">Mahasiswa</th>
                                        <th class="text-center" style="width: 100px;">Total CPL</th>
                                        <th style="width: 160px;">Rata-rata Skor</th>
                                        <th style="width: 160px;">Capaian (%)</th>
                                    </tr>
                                </thead>
                                <tbody id="inlineProdiTableBody">
                                    {{-- Rendered dynamically via JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Filter Aspek & Search Bar untuk Butir CPL --}}
                    <h6 class="fw-bold text-dark mb-2.5"><i class="bi bi-check2-circle text-success me-1.5"></i>Rincian Butir CPL Fakultas:</h6>
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
                                        <th style="width: 110px;">Kode CPL</th>
                                        <th style="width: 160px;">Program Studi</th>
                                        <th style="width: 140px;">Aspek</th>
                                        <th style="min-width: 280px;">Deskripsi / Judul CPL</th>
                                        <th style="width: 170px;">Rata-rata Skor</th>
                                        <th style="width: 150px;">Capaian (%)</th>
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
                <h5 class="fw-bold text-dark">Data Universitas Tidak Ditemukan</h5>
                <p class="text-muted small mb-0">Pastikan akun Anda terasosiasi dengan data Universitas yang valid untuk mengakses visualisasi evaluasi CPL per fakultas.</p>
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
            var allFakultasStats = @json($universitasCplData['fakultas_stats'] ?? []);

            var selectedFakultasId = (allFakultasStats && allFakultasStats.length > 0) ? allFakultasStats[0].id : null;
            var activeInlineAspekFilter = 'all';
            var activeInlineSearchQuery = '';
            var currentActiveFakultasData = null;

            $(function() {
                @if (isset($universitasCplData) && $universitasCplData && !empty($universitasCplData['fakultas_stats']))
                    initYearlyTrendChart(allAvailableYears);
                    initFacultyCplChart();
                @endif
                initYearFilterToolbar();
                initFakultasSelector();
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

            // 2. Chart Komparasi Antar Fakultas
            function initFacultyCplChart() {
                var ctx = document.getElementById('facultyCplChart');
                if (!ctx) return;

                if (!allFakultasStats || allFakultasStats.length === 0) return;

                var labels = allFakultasStats.map(function(f) { return f.nama; });
                var skorData = allFakultasStats.map(function(f) { return f.avg_skor_cpl || 0; });
                var capaianData = allFakultasStats.map(function(f) { return f.avg_capaian_cpl || 0; });

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
                                    fontSize: 11,
                                    callback: function(val) {
                                        return val.length > 22 ? val.substr(0, 20) + '...' : val;
                                    }
                                }
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
                                    var unit = tooltipItem.datasetIndex === 0 ? ' / 100' : '%';
                                    return label + ': ' + tooltipItem.yLabel + unit;
                                }
                            }
                        }
                    }
                });
            }

            // 3. Inline Fakultas Selector & Rincian CPL
            function initFakultasSelector() {
                if (!allFakultasStats || allFakultasStats.length === 0) return;

                // Bind change event on Select dropdown
                $('#selectFakultasInline').on('change', function() {
                    var fId = $(this).val();
                    loadFakultasDetails(fId);
                });

                // Bind click event on table "Rincian CPL" button
                $(document).on('click', '.btn-select-fakultas-detail', function() {
                    var fId = $(this).data('fakultas-id');
                    $('#selectFakultasInline').val(fId).trigger('change');
                    
                    // Smooth scroll to inline detail section
                    var target = document.getElementById('sectionRincianFakultas');
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });

                // Filter Aspek & Search Input
                $('#inlineSelectAspek').on('change', function() {
                    activeInlineAspekFilter = $(this).val();
                    renderInlineCplTable();
                });

                $('#inlineSearchCpl').on('input', function() {
                    activeInlineSearchQuery = $(this).val().toLowerCase().trim();
                    renderInlineCplTable();
                });

                // Initial Load for first fakultas
                if (selectedFakultasId) {
                    loadFakultasDetails(selectedFakultasId);
                }
            }

            function loadFakultasDetails(fakultasId) {
                var found = allFakultasStats.find(function(f) {
                    return String(f.id) === String(fakultasId);
                });

                if (!found) return;

                currentActiveFakultasData = found;
                selectedFakultasId = found.id;
                $('#inputFakultasIdInline').val(found.id);

                // Update 3 Metrics
                $('#inlineFakultasSkorBadge').text((found.avg_skor_cpl || 0).toFixed(1) + ' / 100');
                $('#inlineFakultasCapaianBadge').text((found.avg_capaian_cpl || 0).toFixed(1) + '%');
                $('#inlineFakultasTotalProdiBadge').text((found.total_prodi || 0) + ' Prodi');
                $('#inlineFakultasMhsBadge').text((found.total_mhs || 0) + ' Mahasiswa Dinilai');

                // Render Program Studi List in this Fakultas
                renderInlineProdiTable(found.prodi_stats || []);

                // Collect all CPL items across prodis in this fakultas
                var allCplItems = [];
                var aspekSet = new Set();

                (found.prodi_stats || []).forEach(function(p) {
                    (p.cpl_details || []).forEach(function(c) {
                        allCplItems.push(Object.assign({}, c, { prodi_nama: p.nama }));
                        if (c.aspek) {
                            aspekSet.add(c.aspek);
                        }
                    });
                });

                currentActiveFakultasData.all_cpl_items = allCplItems;

                // Populate Aspek dropdown
                var $aspekSelect = $('#inlineSelectAspek');
                $aspekSelect.empty().append('<option value="all">Semua Aspek</option>');
                aspekSet.forEach(function(asp) {
                    $aspekSelect.append('<option value="' + asp + '">' + asp + '</option>');
                });
                activeInlineAspekFilter = 'all';
                $aspekSelect.val('all');

                renderInlineCplTable();
            }

            function renderInlineProdiTable(prodiList) {
                var $tbody = $('#inlineProdiTableBody');
                $tbody.empty();

                if (!prodiList || prodiList.length === 0) {
                    $tbody.append('<tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada program studi terdaftar pada fakultas ini.</td></tr>');
                    return;
                }

                prodiList.forEach(function(p, idx) {
                    var skor = parseFloat(p.avg_skor_cpl) || 0;
                    var capaian = parseFloat(p.avg_capaian_cpl) || 0;

                    var html = '<tr>' +
                        '<td class="text-center fw-bold text-muted">' + (idx + 1) + '</td>' +
                        '<td><div class="fw-bold text-dark">' + p.nama + '</div></td>' +
                        '<td class="text-center"><span class="badge bg-light text-secondary border">' + (p.jenjang || '-') + '</span></td>' +
                        '<td class="text-center"><span class="badge bg-primary bg-opacity-10 text-primary fw-bold">' + (p.total_mhs || 0) + ' Mhs</span></td>' +
                        '<td class="text-center"><span class="badge bg-light text-dark fw-bold border">' + (p.total_cpl || 0) + ' CPL</span></td>' +
                        '<td>' +
                            '<div class="d-flex justify-content-between align-items-center mb-1">' +
                                '<span class="fw-bold text-dark small">' + skor.toFixed(1) + ' <span class="text-muted font-monospace">/ 100</span></span>' +
                            '</div>' +
                            '<div class="progress-custom-bar">' +
                                '<div class="progress-fill" style="width: ' + Math.min(100, skor) + '%; background-color: #3b82f6;"></div>' +
                            '</div>' +
                        '</td>' +
                        '<td>' +
                            '<div class="d-flex justify-content-between align-items-center mb-1">' +
                                '<span class="fw-bold text-dark small">' + capaian.toFixed(1) + '%</span>' +
                            '</div>' +
                            '<div class="progress-custom-bar">' +
                                '<div class="progress-fill" style="width: ' + Math.min(100, capaian) + '%; background-color: #10b981;"></div>' +
                            '</div>' +
                        '</td>' +
                    '</tr>';

                    $tbody.append(html);
                });
            }

            function renderInlineCplTable() {
                var $tbody = $('#inlineCplTableBody');
                $tbody.empty();

                if (!currentActiveFakultasData || !currentActiveFakultasData.all_cpl_items || currentActiveFakultasData.all_cpl_items.length === 0) {
                    $tbody.append('<tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data butir CPL untuk fakultas ini.</td></tr>');
                    return;
                }

                var filtered = currentActiveFakultasData.all_cpl_items.filter(function(item) {
                    // Filter Aspek
                    if (activeInlineAspekFilter !== 'all' && item.aspek !== activeInlineAspekFilter) {
                        return false;
                    }
                    // Filter Search
                    if (activeInlineSearchQuery) {
                        var kode = (item.kode || '').toLowerCase();
                        var judul = (item.judul || '').toLowerCase();
                        var prodi = (item.prodi_nama || '').toLowerCase();
                        if (!kode.includes(activeInlineSearchQuery) && !judul.includes(activeInlineSearchQuery) && !prodi.includes(activeInlineSearchQuery)) {
                            return false;
                        }
                    }
                    return true;
                });

                if (filtered.length === 0) {
                    $tbody.append('<tr><td colspan="7" class="text-center py-4 text-muted">Tidak ditemukan butir CPL yang cocok dengan filter.</td></tr>');
                    return;
                }

                filtered.forEach(function(cpl, idx) {
                    var skor = parseFloat(cpl.avg_skor) || 0;
                    var capaian = parseFloat(cpl.avg_capaian) || 0;

                    var badgeColor = '#64748b';
                    if (cpl.aspek === 'Sikap') badgeColor = '#ef4444';
                    else if (cpl.aspek === 'Pengetahuan') badgeColor = '#3b82f6';
                    else if (cpl.aspek === 'Keterampilan Umum') badgeColor = '#10b981';
                    else if (cpl.aspek === 'Keterampilan Khusus') badgeColor = '#f59e0b';

                    var html = '<tr>' +
                        '<td class="text-center fw-bold text-muted">' + (idx + 1) + '</td>' +
                        '<td><span class="badge bg-primary bg-opacity-10 text-primary fw-bold" style="font-size: 0.8rem; border-radius: 6px;">' + (cpl.kode || '-') + '</span></td>' +
                        '<td><div class="fw-semibold text-dark text-truncate" style="max-width: 150px;" title="' + (cpl.prodi_nama || '') + '">' + (cpl.prodi_nama || '-') + '</div></td>' +
                        '<td><span class="badge" style="background-color: ' + badgeColor + '15; color: ' + badgeColor + '; border: 1px solid ' + badgeColor + '30; font-size: 0.75rem; border-radius: 6px;">' + (cpl.aspek || 'Lainnya') + '</span></td>' +
                        '<td><div class="text-dark small" style="line-height: 1.4;">' + (cpl.judul || '-') + '</div></td>' +
                        '<td>' +
                            '<div class="d-flex justify-content-between align-items-center mb-1">' +
                                '<span class="fw-bold text-dark small">' + skor.toFixed(1) + ' <span class="text-muted font-monospace">/ 100</span></span>' +
                            '</div>' +
                            '<div class="progress-custom-bar">' +
                                '<div class="progress-fill" style="width: ' + Math.min(100, skor) + '%; background-color: #3b82f6;"></div>' +
                            '</div>' +
                        '</td>' +
                        '<td>' +
                            '<div class="d-flex justify-content-between align-items-center mb-1">' +
                                '<span class="fw-bold text-dark small">' + capaian.toFixed(1) + '%</span>' +
                            '</div>' +
                            '<div class="progress-custom-bar">' +
                                '<div class="progress-fill" style="width: ' + Math.min(100, capaian) + '%; background-color: #10b981;"></div>' +
                            '</div>' +
                        '</td>' +
                    '</tr>';

                    $tbody.append(html);
                });
            }

            // 4. PDF Print Handler
            function initPdfPrintHandler() {
                $('#btnPrintPdf').on('click', function() {
                    var barCanvas = document.getElementById('facultyCplChart');
                    var trendCanvas = document.getElementById('yearlyTrendChart');

                    if (barCanvas) {
                        $('#inputChartImg').val(barCanvas.toDataURL('image/png'));
                    }
                    if (trendCanvas) {
                        $('#inputTrendChartImg').val(trendCanvas.toDataURL('image/png'));
                    }

                    $('#formCetakPdfUniv').submit();
                });
            }
        </script>
    @endpush
@endsection
