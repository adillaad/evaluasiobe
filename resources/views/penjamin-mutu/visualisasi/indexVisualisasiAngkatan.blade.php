@php
    $routePrefix = [
        'Penjamin Mutu Universitas' => ['prefix' => 'penjamin-mutu.universitas.'],
        'Penjamin Mutu Fakultas' => ['prefix' => 'penjamin-mutu.fakultas.'],
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp
@extends('penjamin-mutu.template')
@section('title', 'Visualisasi Per Angkatan')
@section('page_title', 'Visualisasi Per Angkatan')
@section('content')
    <script>
        (function() {
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('angkatan')) {
                document.write('<style id="antiFlickerStyle">#tugas{display:none !important;}#visualContainer{display:block !important;}</style>');
            }
        })();
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @if (session()->has('failed'))
        <div class="alert alert-danger shadow-sm border-0 mb-3" role="alert" id="box">
            <div>{{ session('failed') }}</div>
        </div>
    @elseif (session()->has('success'))
        <div class="alert greenAdd shadow-sm border-0 mb-3" role="alert" id="box">
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <style>
        /* Modern Dashboard Styling */
        .modern-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
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
        }

        /* Section Titles with Icon and Modern Typography */
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

        /* Ensure layout allows sticky scrolling & sticky footer */
        html,
        body,
        .container-scroller,
        .page-body-wrapper,
        .content-wrapper,
        .content-wrapper > .row {
            overflow: visible !important;
        }

        /* Sticky Footer Layout Fix */
        .main-panel {
            min-height: calc(100vh - 60px) !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .content-wrapper {
            flex: 1 0 auto !important;
            min-height: calc(100vh - 140px) !important;
            padding-bottom: 3.5rem !important;
            overflow: visible !important;
        }

        footer.footer {
            margin-top: auto !important;
        }

        #visualContainer {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 100% !important;
        }

        /* Batch Profile Hero Header (Sticky, Prominent, Neat Alignment) */
        .student-profile-hero {
            position: -webkit-sticky !important;
            position: sticky !important;
            top: 70px !important;
            z-index: 1020 !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0;
            border-left: 5px solid #1F3BB3 !important;
            border-radius: 14px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            padding: 20px 28px;
            margin-bottom: 24px;
        }

        .student-label-tag {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #1F3BB3;
            margin-bottom: 4px;
        }

        .student-name-text {
            font-size: 1.65rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
            margin-bottom: 10px;
            letter-spacing: -0.02em;
        }

        .student-meta-row {
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

        /* Modern Tables */
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
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
            border-top: none;
            vertical-align: middle;
        }

        .modern-table tfoot th,
        .modern-table tfoot td {
            background: #f8fafc;
            color: #1e293b;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 12px 16px;
            border-top: 2px solid #cbd5e1;
            vertical-align: middle;
        }

        .modern-table tbody td {
            padding: 12px 16px;
            font-size: 0.86rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
        }

        .modern-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .modern-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Guide Collapse Box */
        .modern-guide-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 18px;
        }

        /* Form Controls */
        .modern-label {
            font-size: 0.84rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
            display: block;
        }

        .modern-select {
            height: 42px !important;
            border-radius: 8px !important;
            border: 1px solid #cbd5e1 !important;
            font-size: 0.9rem !important;
            padding: 8px 14px !important;
            background-color: #ffffff !important;
            color: #1e293b !important;
            transition: all 0.2s ease;
        }

        .modern-select:focus {
            border-color: #1F3BB3 !important;
            box-shadow: 0 0 0 3px rgba(31, 59, 179, 0.12) !important;
        }

        .modern-btn-primary {
            height: 38px;
            padding: 0 18px;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 8px;
            background: #1F3BB3;
            color: #ffffff;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            box-shadow: 0 2px 6px rgba(31, 59, 179, 0.2);
            transition: all 0.2s ease;
            text-decoration: none;
            white-space: nowrap;
        }

        .modern-btn-primary:hover {
            background: #172d88;
            box-shadow: 0 4px 10px rgba(31, 59, 179, 0.3);
            color: #ffffff;
        }

        .modern-btn-outline {
            height: 38px;
            padding: 0 16px;
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .modern-btn-outline:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #1e293b;
        }

        /* Interactive Table Year Selection */
        .interactive-year-th {
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }
        .interactive-year-th:hover {
            background-color: #e0e7ff !important;
            color: #1F3BB3 !important;
        }
        .interactive-year-th.active-year-col {
            background-color: #1F3BB3 !important;
            color: #ffffff !important;
        }
        .interactive-year-th.active-year-col i {
            color: #ffffff !important;
        }
        .interactive-year-td {
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .interactive-year-td:hover {
            background-color: #eff6ff !important;
        }
        .interactive-year-td.active-year-col {
            background-color: #e0e7ff !important;
            color: #1F3BB3 !important;
            font-weight: 700 !important;
        }
        .interactive-year-tf {
            cursor: pointer;
            transition: background-color 0.2s ease;
            color: #1F3BB3;
        }
        .interactive-year-tf:hover {
            background-color: #dbeafe !important;
        }
        .interactive-year-tf.active-year-col {
            background-color: #1F3BB3 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
        }
        .badge-interactive-year {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.2;
            background: #eff6ff;
            color: #1F3BB3;
            border: 1px solid #bfdbfe;
            box-shadow: 0 2px 6px rgba(31, 59, 179, 0.08);
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
            vertical-align: middle;
        }
        .badge-interactive-year:hover {
            background: #dbeafe;
            border-color: #93c5fd;
            color: #1e40af;
            box-shadow: 0 4px 10px rgba(31, 59, 179, 0.15);
            transform: translateY(-1px);
        }
        .badge-interactive-year i.bi-calendar-event {
            font-size: 13px;
            color: #1F3BB3;
            display: inline-flex;
            align-items: center;
        }
        .badge-interactive-year .badge-close-icon {
            font-size: 11px;
            color: #64748b;
            margin-left: 4px;
            display: inline-flex;
            align-items: center;
            transition: color 0.15s ease;
        }
        .badge-interactive-year:hover .badge-close-icon {
            color: #ef4444;
        }

        /* CPL Calculation Courses Grid Styling (Vertical ke Bawah) */
        .course-grid-container {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-height: 300px;
            overflow-y: auto;
            padding: 2px;
        }

        .course-card-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s ease;
        }

        .course-card-item:hover {
            background: #ffffff;
            border-color: #cbd5e1;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }

        /* Structured CPL Description Cards (Compact & Smaller) */
        .cpl-desc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 8px;
            max-height: 240px;
            overflow-y: auto;
            padding: 2px;
        }

        .cpl-desc-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 6px 10px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            transition: all 0.15s ease;
        }

        .cpl-desc-card:hover {
            background: #ffffff;
            border-color: #cbd5e1;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        }

        .cpl-desc-badge {
            font-size: 0.72rem !important;
            font-weight: 700;
            padding: 2px 6px !important;
            border-radius: 4px !important;
            white-space: nowrap;
            line-height: 1.2;
        }

        .cpl-desc-text {
            font-size: 0.78rem !important;
            line-height: 1.35;
            color: #334155;
            font-weight: 500;
        }

        #tugas .modern-card,
        #tugas .card-body,
        #visualContainer .modern-card,
        #visualContainer .card-body {
            overflow: visible !important;
        }

        /* Combobox / Direct Typeable Select Styling */
        .combobox-wrapper {
            position: relative;
            width: 100%;
        }
        .combobox-wrapper.is-open {
            z-index: 1050;
        }
        .combobox-input-group {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }
        .combobox-input {
            height: 42px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 8px 36px 8px 14px !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            background-color: #ffffff !important;
            cursor: text !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03) !important;
            width: 100% !important;
        }
        .combobox-input:focus {
            border-color: #1F3BB3 !important;
            box-shadow: 0 0 0 3px rgba(31, 59, 179, 0.12) !important;
            outline: none !important;
        }
        .combobox-input:disabled {
            background-color: #f8fafc !important;
            color: #94a3b8 !important;
            cursor: not-allowed !important;
            border-color: #e2e8f0 !important;
        }
        .combobox-toggle-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            padding: 6px 8px;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            border-radius: 6px;
            transition: color 0.15s ease;
        }
        .combobox-toggle-btn:hover:not(:disabled) {
            color: #1F3BB3;
        }
        .combobox-toggle-btn:disabled {
            color: #cbd5e1 !important;
            cursor: not-allowed !important;
        }
        .combobox-dropdown-menu {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            min-width: 100%;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            z-index: 99999 !important;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
            overflow: hidden !important;
        }
        .combobox-options-list {
            max-height: 260px;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f8fafc;
            padding: 4px 0;
        }
        .combobox-option {
            padding: 9px 14px !important;
            font-size: 0.865rem !important;
            line-height: 1.45 !important;
            color: #334155 !important;
            cursor: pointer !important;
            white-space: normal !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            transition: background-color 0.12s ease, color 0.12s ease !important;
            user-select: none !important;
            display: block !important;
        }
        .combobox-option span {
            white-space: normal !important;
            word-break: break-word !important;
        }
        .combobox-option span.font-monospace {
            white-space: nowrap !important;
            display: inline-block !important;
        }
        .combobox-option:hover,
        .combobox-option.is-focused {
            background-color: #f1f5f9 !important;
            color: #1F3BB3 !important;
        }
        .combobox-option.is-selected {
            background-color: #eff6ff !important;
            color: #1F3BB3 !important;
            font-weight: 700 !important;
            border-left: 3px solid #1F3BB3 !important;
        }
        .combobox-option.is-selected:hover {
            background-color: #dbeafe !important;
            color: #1e40af !important;
        }
        .combobox-option.is-selected span {
            color: #1F3BB3 !important;
        }
        .combobox-option.is-selected span.font-monospace {
            color: #1e40af !important;
        }
        .combobox-empty-state {
            padding: 12px 14px;
            text-align: center;
            color: #94a3b8;
            font-size: 0.85rem;
            white-space: normal;
        }
    </style>

    {{-- ========================================================================= --}}
    {{-- 1. INPUT FILTER FORM (Halaman Awal Pemilihan Angkatan) --}}
    {{-- ========================================================================= --}}
    <div id="tugas">
        <div class="card modern-card mb-4">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h5 class="section-title mb-1"><i class="bi bi-people-fill"></i> Visualisasi CPL Per Angkatan</h5>
                    <p class="text-muted small mb-0">Silahkan pilih data program studi dan angkatan untuk menampilkan visualisasi OBE.</p>
                </div>

                <form id="hasilVisual" method="POST" action="hasilvisual-mahasiswaAngkatan" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="universitas" id="universitas" value="{{ $universitas->id }}">
                    <input type="hidden" id="universitas-img-path" name="universitas-img-path" value="{{ asset($universitas->img ?? '') }}">
                    @if ($universitas->img)
                        <img id="universitas-img" src="{{ asset($universitas->img) }}" style="display: none;" />
                    @endif

                    <div class="row g-3">
                        @if (in_array($userOtoritas, ['Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas', 'Wakil Rektor', 'Wakil Dekan']))
                            <div class="col-md-6">
                                <label class="modern-label">
                                    <i class="bi bi-mortarboard text-primary me-1"></i> Program Studi <span class="text-danger">*</span>
                                </label>
                                <select id="prodiForm" class="form-control form-select modern-select" name="prodi" required>
                                    <option value="">Pilih Program Studi</option>
                                    @foreach ($prodi as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->jenjang ?? 'S1' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="modern-label">
                                    <i class="bi bi-calendar3 text-primary me-1"></i> Angkatan <span class="text-danger">*</span>
                                </label>
                                <div class="combobox-wrapper" id="angkatanComboboxWrapper">
                                    <div class="combobox-input-group">
                                        <input type="text" 
                                               id="angkatanDisplayInput" 
                                               class="form-control modern-select combobox-input" 
                                               placeholder="Pilih Angkatan" 
                                               autocomplete="off">
                                        <input type="hidden" id="angkatanForm" name="angkatan" value="">
                                        <button type="button" class="combobox-toggle-btn" tabindex="-1" id="angkatanToggleBtn" title="Tampilkan daftar angkatan">
                                            <i class="bi bi-chevron-down"></i>
                                        </button>
                                    </div>
                                    <div class="combobox-dropdown-menu" id="angkatanDropdownMenu" style="display: none;">
                                        <div class="combobox-options-list" id="angkatanOptionsList">
                                            <div class="combobox-empty-state">Pilih Program Studi terlebih dahulu</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <input type="hidden" id="prodiForm" name="prodi" value="{{ auth()->user()->id_prodiUser }}">
                            <div class="col-md-6">
                                <label class="modern-label">
                                    <i class="bi bi-calendar3 text-primary me-1"></i> Angkatan <span class="text-danger">*</span>
                                </label>
                                <div class="combobox-wrapper" id="angkatanComboboxWrapper">
                                    <div class="combobox-input-group">
                                        <input type="text" 
                                               id="angkatanDisplayInput" 
                                               class="form-control modern-select combobox-input" 
                                               placeholder="Pilih Angkatan" 
                                               autocomplete="off">
                                        <input type="hidden" id="angkatanForm" name="angkatan" value="">
                                        <button type="button" class="combobox-toggle-btn" tabindex="-1" id="angkatanToggleBtn" title="Tampilkan daftar angkatan">
                                            <i class="bi bi-chevron-down"></i>
                                        </button>
                                    </div>
                                    <div class="combobox-dropdown-menu" id="angkatanDropdownMenu" style="display: none;">
                                        <div class="combobox-options-list" id="angkatanOptionsList">
                                            <div class="combobox-empty-state">Memuat data angkatan...</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-2 mt-4">
                        <button type="submit" id="btnSubmitVisual" class="modern-btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                            </svg>
                            Tampilkan Visualisasi OBE
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- 2. DEDICATED RESULTS VIEW (Tampilan Hasil Visualisasi Angkatan) --}}
    {{-- ========================================================================= --}}
    <div id="visualContainer" style="display:none;">

        {{-- HERO PROFILE CARD (Sticky, Clean, Solid Alignment) --}}
        <div class="student-profile-hero">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <div class="student-label-tag">VISUALISASI CPL PER ANGKATAN</div>
                    <h3 class="student-name-text" id="displayInfoAngkatan">-</h3>
                    <div class="student-meta-row">
                        <div class="meta-item">
                            <span class="meta-label">Angkatan:</span>
                            <span class="meta-value" id="angkatanDataText">-</span>
                        </div>
                        <span class="meta-pipe">|</span>
                        <div class="meta-item">
                            <span class="meta-label">Program Studi:</span>
                            <span class="meta-value" id="prodiDataText">-</span>
                        </div>
                        <span class="meta-pipe">|</span>
                        <div class="meta-item">
                            <span class="meta-label">Universitas:</span>
                            <span class="meta-value" id="universitasDataText">-</span>
                        </div>
                    </div>
                </div>

                {{-- Toolbar Tombol Aksi --}}
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <button type="button" class="modern-btn-outline" id="btnBackToFilter">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-arrow-left me-1" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                        </svg>
                        Ganti Angkatan
                    </button>
                    <button id="btnPrintPdf" type="button" class="modern-btn-primary" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-pdf me-1" viewBox="0 0 16 16">
                            <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                            <path d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.143.244-.427.721-.867 1.482-1.347.165-.104.34-.207.525-.308a10.98 10.98 0 0 1-.965-1.776 19.78 19.78 0 0 1-.5-1.783c-.153-.746-.118-1.338.106-1.774.225-.436.643-.655 1.254-.655.672 0 1.13.344 1.373.91.244.566.19 1.34-.162 2.32-.083.232-.18.473-.291.722a20.08 20.08 0 0 0 2.855.772c.49-.428.972-.888 1.446-1.38.614-.64.97-1.206 1.068-1.698.147-.738-.086-1.347-.7-1.826a.614.614 0 0 0-.441-.122c-.22.02-.375.14-.465.361-.09.22-.058.497.096.83.155.334.398.67.729 1.011zm-.75-2.02c-.23.23-.393.473-.489.73-.095.257-.087.48.024.67.112.19.294.286.547.286.253 0 .524-.096.813-.288-.344-.45-.643-.917-.895-1.398zm3.567-4.228c-.143-.377-.323-.566-.54-.566-.217 0-.377.126-.48.378-.103.252-.093.585.03 1 .123.415.305.85.546 1.306.126-.415.226-.789.3-1.122.074-.333.12-.665.144-.996z"/>
                        </svg>
                        Unduh PDF
                    </button>
                </div>
            </div>
        </div>

        {{-- Alert Notice jika data kosong --}}
        <div id="emptyDataAlert" class="alert alert-warning border-0 shadow-sm mb-4 py-3 px-4" style="display: none; background: #fffbeb; border-left: 4px solid #f59e0b !important; border-radius: 10px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-info-circle-fill text-warning fs-4 me-3"></i>
                <div>
                    <h6 class="alert-heading mb-1 fw-bold text-dark">Data Penilaian Belum Tersedia</h6>
                    <p class="mb-0 text-muted small" id="emptyDataMessage">Tidak ada data penilaian/asesmen mata kuliah untuk angkatan yang dipilih.</p>
                </div>
            </div>
        </div>

        {{-- CARD 1: CPL Achievement Percentage (%) (Batch) --}}
        <div class="card modern-card mb-4">
            <div class="modern-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="section-title mb-0 d-flex align-items-center flex-wrap gap-2">
                    <span><i class="bi bi-bar-chart-line"></i> CPL Achievement Percentage (%) (Batch)</span>
                    <span id="badgeYearAngkatan" class="badge-interactive-year" style="display: none;" title="Klik untuk menghapus filter dan kembali ke tampilan awal">
                        <i class="bi bi-calendar-event"></i>
                        <span class="badge-year-text">Tahun 2024</span>
                        <i class="bi bi-x-lg badge-close-icon"></i>
                    </span>
                </h5>
                <button class="btn btn-sm btn-outline-secondary" type="button" id="btnToggleGuideCplBatch" style="border-radius: 6px; font-size: 12px; padding: 4px 10px;" title="Petunjuk Membaca Diagram">
                    <i class="bi bi-info-circle me-1"></i> Petunjuk Membaca Diagram
                </button>
            </div>
            <div class="card-body p-4">
                {{-- Guide Banner (Bisa di Buka Tutup) --}}
                <div class="mb-3" id="guideCplBatch" style="display: none;">
                    <div class="modern-guide-box small">
                        <h6 class="fw-bold text-primary mb-1"><i class="bi bi-compass me-1"></i> Panduan Membaca Diagram Radar Capaian CPL Angkatan:</h6>
                        <ul class="mb-0 ps-3 text-muted">
                            <li><strong>Bentuk Jaring (Radar):</strong> Setiap sudut mewakili satu <strong>CPL (Capaian Pembelajaran Lulusan)</strong>.</li>
                            <li><span class="badge" style="background-color: #c06e4b; color:#fff;">Average CPL</span>: Rata-rata persentase ketercapaian tiap CPL oleh seluruh mahasiswa dalam satu angkatan.</li>
                            <li><span class="badge" style="background-color: #21d85f; color:#fff;">Max CPL</span>: Batas pencapaian tertinggi mahasiswa pada CPL tersebut di angkatan yang dipilih.</li>
                            <li><span class="badge" style="background-color: #d82121; color:#fff;">Min CPL</span>: Batas pencapaian terendah mahasiswa pada CPL tersebut di angkatan yang dipilih.</li>
                            <li><strong>Tabel Rincian Ketercapaian CPL (%):</strong> Menampilkan persentase kelulusan mata kuliah pendukung CPL secara kumulatif setiap tahun akademik. <em>Klik pada kolom tahun di tabel untuk melihat diagram pada tahun tersebut.</em></li>
                        </ul>
                    </div>
                </div>

                {{-- Executive Summary Cards --}}
                <div class="row g-3 mb-4" id="cplBatchMetricCards">
                    <div class="col-md-4 col-sm-12">
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <div class="text-muted small fw-semibold">Rata-rata Ketercapaian CPL</div>
                            <div class="h4 mb-0 fw-bold text-primary mt-1" id="cardAvgCplAngkatan">-</div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <div class="text-muted small fw-semibold">CPL Tertinggi</div>
                            <div class="h4 mb-0 fw-bold text-success mt-1" id="cardMaxCplAngkatan">-</div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <div class="text-muted small fw-semibold">CPL Terendah</div>
                            <div class="h4 mb-0 fw-bold text-danger mt-1" id="cardMinCplAngkatan">-</div>
                        </div>
                    </div>
                </div>

                {{-- Diagram Canvas --}}
                <div class="d-flex justify-content-center mb-4">
                    <div style="width: 100%; max-width: 600px;">
                        <canvas id="radarChartAngkatan"></canvas>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    {{-- Tabel Rincian Capaian CPL Angkatan --}}
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-table text-primary me-2"></i> Rincian Ketercapaian CPL:</h6>
                    </div>
                    <div class="modern-table-container mb-4">
                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                            <table class="table modern-table mb-0" id="tableRincianCplScoresAngkatan">
                                <thead>
                                    <tr id="theadRincianCplScoresAngkatan">
                                        <th style="min-width: 120px;" class="text-start">Kode CPL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Dinamis via JS --}}
                                </tbody>
                                <tfoot>
                                    {{-- Dinamis via JS --}}
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    {{-- Pemetaan CPL ke Mata Kuliah --}}
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-diagram-2 text-primary me-2"></i> Pemetaan CPL ke Mata Kuliah :</h6>
                    <div class="modern-guide-box mb-4">
                        <div class="row align-items-center g-3 mb-3">
                            <div class="col-md-4 col-sm-6">
                                <select id="selectPemetaanCpl" class="form-control form-select modern-select" name="selectPemetaanCpl">
                                    <option value="">Pilih CPL</option>
                                </select>
                            </div>
                        </div>
                        <div id="kontenPemetaanCpl">
                            <span class="text-muted small">Silahkan pilih salah satu CPL di atas untuk melihat daftar mata kuliah pendukung.</span>
                        </div>
                    </div>

                    {{-- Deskripsi CPL (Bisa di Buka Tutup) --}}
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-4 pt-3 border-top" id="headerToggleCplDesc" style="cursor: pointer; user-select: none;">
                        <h6 class="keterangan fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-card-text text-primary"></i> Descriptions (Deskripsi CPL)
                            <span id="cplBadgeCount" class="badge bg-light text-secondary border ms-1" style="font-size: 0.75rem; display: none;"></span>
                        </h6>
                        <button class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center justify-content-center" type="button" id="btnToggleCplDesc" style="border-radius: 6px; font-size: 13px; width: 32px; height: 30px; padding: 0; pointer-events: none;" title="Buka / Tutup Deskripsi CPL">
                            <i class="bi bi-chevron-down" id="iconCplDescCollapse"></i>
                        </button>
                    </div>
                    <div class="mt-2" id="collapseCplDescriptions" style="display: none;">
                        <div id="labelContainer" class="cpl-desc-grid">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 2 & 3: Lowest Questions & CPL Calculation Courses --}}
        <div class="row">
            {{-- Kiri: Questions with the Lowest Average CPL --}}
            <div class="col-lg-6 mb-4">
                <div class="card modern-card h-100">
                    <div class="modern-card-header d-flex justify-content-between align-items-center">
                        <h5 class="section-title mb-0">
                            <i class="bi bi-patch-question text-primary"></i> Questions with Lowest CPL
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">Soal-soal penilaian dengan rata-rata ketercapaian CPL terendah pada angkatan ini:</p>
                        <div class="modern-table-container">
                            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                <table class="table modern-table mb-0" id="soalTerendahTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 45px;" class="text-center">No</th>
                                            <th>Mata Kuliah</th>
                                            <th class="text-center" style="width: 100px;">Asesmen</th>
                                            <th>Soal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Dinamis via JS --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kanan: CPL Calculation Courses (Tampilan Mirip All Courses Taken) --}}
            <div class="col-lg-6 mb-4">
                <div class="card modern-card h-100">
                    <div class="modern-card-header d-flex justify-content-between align-items-center">
                        <h5 class="section-title mb-0">
                            <i class="bi bi-collection"></i> CPL Calculation Courses
                        </h5>
                        <span id="cplCoursesCountBadge" class="badge bg-primary text-white rounded-pill px-3 py-1 fw-bold" style="font-size: 11px;">0 MK</span>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">Mata kuliah yang berkontribusi pada perhitungan CPL angkatan ini:</p>
                        <div id="cplCoursesGrid" class="course-grid-container">
                            {{-- Dinamis via JS --}}
                        </div>
                        <ol id="courseList" style="display: none;"></ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 4: Mahasiswa Angkatan (Matrix Table with Search, Toggle Switch & Pagination) --}}
        <div class="card modern-card mb-4">
            <div class="modern-card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 id="mahasiswaAngkatanTitle" class="section-title mb-0"><i class="bi bi-people"></i> Mahasiswa Angkatan</h5>
                    <div class="text-muted small mt-1" id="mahasiswaAngkatanSubtitle" style="font-size: 0.82rem; line-height: 1.3;">Matriks capaian nilai asesmen dan persentase ketercapaian per mahasiswa</div>
                </div>
                <div class="d-flex align-items-center justify-content-end gap-2 flex-wrap ms-auto">
                    {{-- Counter Badge --}}
                    <span id="mahasiswaCountInfo" class="badge bg-light text-dark border small fw-semibold py-2 px-3" style="border-radius: 8px;">Memuat...</span>

                    {{-- Sleek Live Search Input --}}
                    <div class="position-relative" style="min-width: 200px; max-width: 240px;">
                        <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted small" style="left: 12px; pointer-events: none; z-index: 5;"></i>
                        <input type="text" id="searchMahasiswaAngkatan" class="form-control form-control-sm" placeholder="Cari Nama / NPM..." style="padding-left: 36px !important; padding-right: 34px !important; border-radius: 8px; height: 36px; font-size: 0.84rem; border: 1px solid #cbd5e1; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                        <button class="btn btn-sm btn-link position-absolute top-50 translate-middle-y text-secondary p-0" type="button" id="btnClearSearchMahasiswa" style="right: 10px; display: none; text-decoration: none; z-index: 5;" title="Hapus pencarian">
                            <i class="bi bi-x-circle-fill small"></i>
                        </button>
                    </div>

                    {{-- Toggle Mode Group --}}
                    <div class="d-flex align-items-center gap-1">
                        <span class="small text-secondary fw-bold me-1">Mode:</span>
                        <div class="btn-group btn-group-sm" role="group" id="toggleTableModeGroup" style="height: 36px;">
                            <button type="button" class="btn btn-sm btn-primary active btn-toggle-mode d-inline-flex align-items-center" data-mode="skor" id="btnModeSkor" style="border-radius: 8px 0 0 8px; font-size: 0.82rem; font-weight: 600;">
                                <i class="bi bi-award me-1"></i> Skor Nilai
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary btn-toggle-mode d-inline-flex align-items-center" data-mode="persen" id="btnModePersen" style="border-radius: 0 8px 8px 0; font-size: 0.82rem; font-weight: 600;">
                                <i class="bi bi-percent me-1"></i> Ketercapaian
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="modern-table-container">
                    <div class="table-responsive" style="max-height: 460px; overflow-y: auto;">
                        <table class="table modern-table text-center mb-0" id="mahasiswaAngkatanTable">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th style="width: 120px;">NPM</th>
                                    <th class="text-start">Nama Mahasiswa</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Dinamis via JS --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination & Rows-Per-Page Footer Bar --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3 pt-2" id="mahasiswaPaginationBar">
                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted mb-0 fw-semibold" for="selectPageSizeMahasiswa">Tampilkan:</label>
                        <select id="selectPageSizeMahasiswa" class="form-select form-select-sm" style="width: 90px; border-radius: 6px; font-size: 0.82rem; height: 32px; padding: 2px 24px 2px 8px;">
                            <option value="10">10</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="all">Semua</option>
                        </select>
                        <span class="small text-muted ms-1" id="pageInfoMahasiswa">-</span>
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0 gap-1" id="paginationControlsMahasiswa">
                            {{-- Dinamis via JS --}}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        {{-- CARD 5: Lihat CPMK Angkatan --}}
        <div class="card modern-card mb-4">
            <div class="modern-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="section-title mb-0">
                    <i class="bi bi-journal-check"></i> Lihat Capaian CPMK Angkatan
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" id="hasilvisualcpmk-angkatan" action="hasilvisualcpmk-angkatan" enctype="multipart/form-data">
                    @csrf
                    <input type="text" name="angkatan" class="visually-hidden" value="">
                    <input type="text" name="prodi" class="visually-hidden" id="prodiHidden" value="">
                    <input type="text" name="universitasImg" class="visually-hidden" value="">
                    <input type="text" name="universitasCPMK" class="visually-hidden" value="">
                    <input type="text" name="tahun" class="visually-hidden" value="all">
                    <input type="text" name="semester" class="visually-hidden" value="all">
                    
                    <div class="d-flex flex-wrap align-items-end gap-3">
                        <div style="flex: 1; min-width: 260px; max-width: 620px;">
                            <label class="modern-label mb-1">
                                <i class="bi bi-journal-text text-primary me-1"></i> Pilih Mata Kuliah <span class="text-danger">*</span>
                            </label>
                            <div class="combobox-wrapper" id="courseComboboxWrapper">
                                <div class="combobox-input-group">
                                    <input type="text" 
                                           id="courseDisplayInput" 
                                           class="form-control modern-select combobox-input" 
                                           placeholder="Ketik Kode atau Nama Mata Kuliah..." 
                                           autocomplete="off">
                                    <input type="hidden" id="courseSelect" name="course" value="">
                                    <button type="button" class="combobox-toggle-btn" tabindex="-1" id="courseToggleBtn" title="Tampilkan daftar mata kuliah">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                </div>
                                <div class="combobox-dropdown-menu" id="courseDropdownMenu" style="display: none;">
                                    <div class="combobox-options-list" id="courseOptionsList">
                                        {{-- Dinamis via JS --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button type="submit" class="modern-btn-primary" style="height: 38px; font-size: 0.85rem; padding: 0 16px;">
                                <i class="bi bi-search"></i> Tampilkan CPMK
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            var radarChartAngkatanInstance = null;
            window.currentBatchStudents = [];
            window.currentUniqueCplCodes = [];
            window.currentBatchTableMode = 'skor';
            window.currentBatchPage = 1;
            window.currentBatchPageSize = 25;

            var angkatanOptionsData = [];
            var activeAngkatanIndex = -1;

            var courseOptionsData = [];
            var activeCourseIndex = -1;

            // Helper for rounding
            function roundToTwo(num) {
                return Math.round(num * 100) / 100;
            }

            // ---------------- ANGKATAN COMBOBOX LOGIC ----------------
            function renderAngkatanOptions(filterText) {
                var $list = $('#angkatanOptionsList');
                $list.empty();
                activeAngkatanIndex = -1;

                var filtered = angkatanOptionsData;
                if (filterText && filterText.trim() !== '') {
                    var lower = filterText.trim().toLowerCase();
                    filtered = angkatanOptionsData.filter(function(item) {
                        return item.text.toLowerCase().indexOf(lower) !== -1 || item.value.toLowerCase().indexOf(lower) !== -1;
                    });
                }

                if (filtered.length === 0) {
                    $list.html('<div class="combobox-empty-state">Tidak ada angkatan yang cocok</div>');
                    return;
                }

                var currentSelected = $('#angkatanForm').val();
                filtered.forEach(function(item, idx) {
                    var isSelected = (item.value === currentSelected);
                    var $opt = $('<div></div>')
                        .addClass('combobox-option' + (isSelected ? ' is-selected' : ''))
                        .attr('data-value', item.value)
                        .attr('data-index', idx)
                        .text(item.text);

                    $opt.on('mousedown', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        selectAngkatanItem(item.value, item.text);
                    });

                    $list.append($opt);
                });
            }

            function selectAngkatanItem(val, text) {
                $('#angkatanForm').val(val);
                $('#angkatanDisplayInput').val(text);
                $('#angkatanDropdownMenu').hide();
                $('#angkatanComboboxWrapper').removeClass('is-open');
                activeAngkatanIndex = -1;
            }

            function openAngkatanCombobox() {
                var currentQuery = $('#angkatanDisplayInput').val().trim();
                var currentVal = $('#angkatanForm').val();
                var currentObj = angkatanOptionsData.find(function(s) { return s.value === currentVal; });
                if (currentObj && currentObj.text === currentQuery) {
                    renderAngkatanOptions('');
                } else {
                    renderAngkatanOptions(currentQuery);
                }
                $('#angkatanComboboxWrapper').addClass('is-open');
                $('#angkatanDropdownMenu').show();
            }

            function closeAngkatanCombobox() {
                $('#angkatanDropdownMenu').hide();
                $('#angkatanComboboxWrapper').removeClass('is-open');
                activeAngkatanIndex = -1;

                var currentText = $('#angkatanDisplayInput').val().trim();
                var currentVal = $('#angkatanForm').val();

                if (!currentText) {
                    $('#angkatanDisplayInput').val('');
                    $('#angkatanForm').val('');
                    return;
                }

                var match = angkatanOptionsData.find(function(s) { 
                    return s.value.toLowerCase() === currentText.toLowerCase() || 
                           s.text.toLowerCase() === currentText.toLowerCase(); 
                });

                if (!match && /^\d{4}$/.test(currentText)) {
                    match = { value: currentText, text: currentText };
                }

                if (match) {
                    $('#angkatanForm').val(match.value);
                    $('#angkatanDisplayInput').val(match.text);
                } else if (currentVal) {
                    var prevFound = angkatanOptionsData.find(function(s) { return s.value === currentVal; });
                    if (prevFound) {
                        $('#angkatanDisplayInput').val(prevFound.text);
                    }
                } else {
                    $('#angkatanDisplayInput').val('');
                    $('#angkatanForm').val('');
                }
            }

            $('#angkatanDisplayInput').on('focus', function() {
                $(this).select();
                openAngkatanCombobox();
            });

            $('#angkatanDisplayInput').on('click', function() {
                openAngkatanCombobox();
            });

            $('#angkatanDisplayInput').on('input', function() {
                var query = $(this).val().trim();
                renderAngkatanOptions(query);
                $('#angkatanComboboxWrapper').addClass('is-open');
                $('#angkatanDropdownMenu').show();

                if (query === '') {
                    $('#angkatanForm').val('');
                    return;
                }

                var exactMatch = angkatanOptionsData.find(function(item) {
                    return item.value.toLowerCase() === query.toLowerCase() || item.text.toLowerCase() === query.toLowerCase();
                });

                if (exactMatch) {
                    $('#angkatanForm').val(exactMatch.value);
                } else if (/^\d{4}$/.test(query)) {
                    $('#angkatanForm').val(query);
                }
            });

            $('#angkatanToggleBtn').on('click', function(e) {
                e.preventDefault();
                if ($('#angkatanDropdownMenu').is(':visible')) {
                    closeAngkatanCombobox();
                } else {
                    $('#angkatanDisplayInput').focus();
                    openAngkatanCombobox();
                }
            });

            $('#angkatanDisplayInput').on('keydown', function(e) {
                var $options = $('#angkatanOptionsList .combobox-option');
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (!$('#angkatanDropdownMenu').is(':visible')) {
                        openAngkatanCombobox();
                        return;
                    }
                    if (!$options.length) return;
                    activeAngkatanIndex++;
                    if (activeAngkatanIndex >= $options.length) activeAngkatanIndex = 0;
                    $options.removeClass('is-focused');
                    var $active = $options.eq(activeAngkatanIndex).addClass('is-focused');
                    if ($active.length && $active[0].scrollIntoView) {
                        $active[0].scrollIntoView({ block: 'nearest' });
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (!$('#angkatanDropdownMenu').is(':visible')) {
                        openAngkatanCombobox();
                        return;
                    }
                    if (!$options.length) return;
                    activeAngkatanIndex--;
                    if (activeAngkatanIndex < 0) activeAngkatanIndex = $options.length - 1;
                    $options.removeClass('is-focused');
                    var $active = $options.eq(activeAngkatanIndex).addClass('is-focused');
                    if ($active.length && $active[0].scrollIntoView) {
                        $active[0].scrollIntoView({ block: 'nearest' });
                    }
                } else if (e.key === 'Enter') {
                    if ($('#angkatanDropdownMenu').is(':visible') && $options.length > 0) {
                        e.preventDefault();
                        if (activeAngkatanIndex >= 0 && activeAngkatanIndex < $options.length) {
                            var selectedVal = $options.eq(activeAngkatanIndex).attr('data-value');
                            var selectedText = $options.eq(activeAngkatanIndex).text();
                            selectAngkatanItem(selectedVal, selectedText);
                        } else {
                            var selectedVal = $options.eq(0).attr('data-value');
                            var selectedText = $options.eq(0).text();
                            selectAngkatanItem(selectedVal, selectedText);
                        }
                    }
                } else if (e.key === 'Escape') {
                    closeAngkatanCombobox();
                }
            });

            // ---------------- COURSE CPMK COMBOBOX LOGIC ----------------
            function renderCourseOptions(filterText) {
                var $list = $('#courseOptionsList');
                $list.empty();
                activeCourseIndex = -1;

                if (!courseOptionsData || courseOptionsData.length === 0) {
                    $list.html('<div class="combobox-empty-state">Tidak ada mata kuliah yang tersedia</div>');
                    return;
                }

                var query = (filterText || '').toLowerCase().trim();
                var filtered = courseOptionsData.filter(function(item) {
                    if (!query) return true;
                    return (item.code && item.code.toLowerCase().indexOf(query) !== -1) ||
                           (item.name && item.name.toLowerCase().indexOf(query) !== -1) ||
                           (item.text && item.text.toLowerCase().indexOf(query) !== -1);
                });

                if (filtered.length === 0) {
                    $list.html('<div class="combobox-empty-state">Tidak ada mata kuliah yang cocok dengan "' + filterText + '"</div>');
                    return;
                }

                var currentVal = $('#courseSelect').val();

                filtered.forEach(function(item, idx) {
                    var isSelected = (item.value === currentVal);
                    var $opt = $('<div></div>')
                        .addClass('combobox-option' + (isSelected ? ' is-selected' : ''))
                        .attr('data-value', item.value)
                        .attr('data-index', idx)
                        .attr('title', item.text)
                        .html('<span class="font-monospace fw-semibold me-2">' + item.code + '</span><span class="text-secondary">-</span> <span class="ms-1">' + item.name + '</span>');

                    $opt.on('mousedown', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        selectCourseItem(item.value, item.text);
                    });

                    $list.append($opt);
                });
            }

            function selectCourseItem(val, text) {
                $('#courseSelect').val(val);
                $('#courseDisplayInput').val(text).attr('title', text);
                $('#courseDropdownMenu').hide();
                $('#courseComboboxWrapper').removeClass('is-open');
                activeCourseIndex = -1;
            }

            function openCourseCombobox() {
                var currentQuery = $('#courseDisplayInput').val().trim();
                var currentVal = $('#courseSelect').val();
                var currentObj = courseOptionsData.find(function(s) { return s.value === currentVal; });
                if (currentObj && (currentObj.text === currentQuery || currentObj.value === currentQuery || currentObj.code === currentQuery)) {
                    renderCourseOptions('');
                } else {
                    renderCourseOptions(currentQuery);
                }
                $('#courseComboboxWrapper').addClass('is-open');
                $('#courseDropdownMenu').show();
            }

            function closeCourseCombobox() {
                $('#courseDropdownMenu').hide();
                $('#courseComboboxWrapper').removeClass('is-open');
                activeCourseIndex = -1;

                var currentVal = $('#courseSelect').val();
                var currentText = $('#courseDisplayInput').val().trim();

                if (currentVal) {
                    var found = courseOptionsData.find(function(s) { return s.value === currentVal; });
                    if (found) {
                        $('#courseDisplayInput').val(found.text);
                        return;
                    }
                }

                if (currentText && courseOptionsData.length > 0) {
                    var lowerText = currentText.toLowerCase();
                    var exactMatch = courseOptionsData.find(function(s) {
                        return s.code.toLowerCase() === lowerText || s.text.toLowerCase() === lowerText || s.name.toLowerCase() === lowerText;
                    });
                    if (exactMatch) {
                        selectCourseItem(exactMatch.value, exactMatch.text);
                        return;
                    }
                } else if (!currentText) {
                    $('#courseSelect').val('');
                    $('#courseDisplayInput').val('');
                }
            }

            $('#courseDisplayInput').on('focus', function() {
                $(this).select();
                openCourseCombobox();
            });

            $('#courseDisplayInput').on('click', function() {
                openCourseCombobox();
            });

            $('#courseDisplayInput').on('input', function() {
                var query = $(this).val().trim();
                renderCourseOptions(query);
                $('#courseComboboxWrapper').addClass('is-open');
                $('#courseDropdownMenu').show();

                if (query === '') {
                    $('#courseSelect').val('');
                    return;
                }

                var lowerQuery = query.toLowerCase();
                var exactMatch = courseOptionsData.find(function(item) {
                    return item.code.toLowerCase() === lowerQuery || item.text.toLowerCase() === lowerQuery;
                });

                if (exactMatch) {
                    $('#courseSelect').val(exactMatch.value);
                }
            });

            $('#courseToggleBtn').on('click', function(e) {
                e.preventDefault();
                if ($('#courseDropdownMenu').is(':visible')) {
                    closeCourseCombobox();
                } else {
                    $('#courseDisplayInput').focus();
                    openCourseCombobox();
                }
            });

            $('#courseDisplayInput').on('keydown', function(e) {
                var $options = $('#courseOptionsList .combobox-option');
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (!$('#courseDropdownMenu').is(':visible')) {
                        openCourseCombobox();
                        return;
                    }
                    if (!$options.length) return;
                    activeCourseIndex++;
                    if (activeCourseIndex >= $options.length) activeCourseIndex = 0;
                    $options.removeClass('is-focused');
                    var $active = $options.eq(activeCourseIndex).addClass('is-focused');
                    if ($active.length && $active[0].scrollIntoView) {
                        $active[0].scrollIntoView({ block: 'nearest' });
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (!$('#courseDropdownMenu').is(':visible')) {
                        openCourseCombobox();
                        return;
                    }
                    if (!$options.length) return;
                    activeCourseIndex--;
                    if (activeCourseIndex < 0) activeCourseIndex = $options.length - 1;
                    $options.removeClass('is-focused');
                    var $active = $options.eq(activeCourseIndex).addClass('is-focused');
                    if ($active.length && $active[0].scrollIntoView) {
                        $active[0].scrollIntoView({ block: 'nearest' });
                    }
                } else if (e.key === 'Enter') {
                    if ($('#courseDropdownMenu').is(':visible') && $options.length > 0) {
                        e.preventDefault();
                        var targetIdx = (activeCourseIndex >= 0 && activeCourseIndex < $options.length) ? activeCourseIndex : 0;
                        var selectedVal = $options.eq(targetIdx).attr('data-value');
                        var selectedItem = courseOptionsData.find(function(s) { return s.value === selectedVal; });
                        if (selectedItem) {
                            selectCourseItem(selectedItem.value, selectedItem.text);
                        }
                    }
                } else if (e.key === 'Escape') {
                    closeCourseCombobox();
                }
            });

            $(document).on('mousedown', function(e) {
                if (!$(e.target).closest('#angkatanComboboxWrapper').length) {
                    if ($('#angkatanDropdownMenu').is(':visible')) {
                        closeAngkatanCombobox();
                    }
                }
                if (!$(e.target).closest('#courseComboboxWrapper').length) {
                    if ($('#courseDropdownMenu').is(':visible')) {
                        closeCourseCombobox();
                    }
                }
            });

            function loadAngkatan(prodi, universitas) {
                $('#angkatanOptionsList').html('<div class="combobox-empty-state"><span class="spinner-border spinner-border-sm me-2 text-primary"></span>Memuat angkatan...</div>');
                $.ajax({
                    url: "{{ route($currentPrefix . 'visualisasi.getAngkatanByProdiUniversitas') }}",
                    method: 'GET',
                    data: {
                        prodi: prodi,
                        universitas: universitas
                    },
                    success: function(data) {
                        angkatanOptionsData = [];
                        var $temp = $('<div></div>').html(data);
                        $temp.find('option').each(function() {
                            var val = $(this).val();
                            var txt = $(this).text();
                            if (val && val !== '') {
                                angkatanOptionsData.push({ value: val, text: txt });
                            }
                        });

                        if (angkatanOptionsData.length > 0) {
                            renderAngkatanOptions('');
                        } else {
                            $('#angkatanOptionsList').html('<div class="combobox-empty-state">Tidak ada data angkatan</div>');
                        }
                    },
                    error: function() {
                        $('#angkatanOptionsList').html('<div class="combobox-empty-state text-danger">Gagal memuat angkatan</div>');
                    }
                });
            }

            $('#prodiForm').on('change', function() {
                var prodi = $(this).val();
                var universitas = $('#universitas').val();
                $('#angkatanForm').val('');
                $('#angkatanDisplayInput').val('');
                if (prodi) {
                    loadAngkatan(prodi, universitas);
                } else {
                    angkatanOptionsData = [];
                    $('#angkatanOptionsList').html('<div class="combobox-empty-state">Pilih Program Studi terlebih dahulu</div>');
                }
            });

            @if (in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']))
                var userProdi = "{{ auth()->user()->id_prodiUser }}";
                var universitas = $('#universitas').val();
                loadAngkatan(userProdi, universitas);
            @endif

            function fetchAndRenderAngkatan(prodi, angkatan, isAutoLoad) {
                var universitas = $('#universitas').val();
                var imgSrc = $('#universitas-img').attr('src') || $('#universitas-img-path').val();

                if (!prodi || !angkatan) {
                    if (!isAutoLoad) {
                        alert('Mohon pilih Program Studi dan Angkatan terlebih dahulu!');
                    }
                    return;
                }

                var $submitBtn = $('#btnSubmitVisual');
                var originalBtnHtml = $submitBtn.html();
                if (!isAutoLoad) {
                    $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memuat Data...');
                }

                var dataObj = {
                    universitas: universitas,
                    prodi: prodi,
                    angkatan: angkatan,
                    tahun: 'all',
                    semester: 'all',
                    _token: "{{ csrf_token() }}"
                };
                if (imgSrc) {
                    dataObj.imgSrc = imgSrc;
                }

                $.ajax({
                    url: "{{ route($currentPrefix . 'visualisasi.hasilvisual-mahasiswaAngkatan') }}",
                    method: 'POST',
                    data: dataObj,
                    dataType: 'json',
                    success: function(response) {
                        if (!isAutoLoad) {
                            $submitBtn.prop('disabled', false).html(originalBtnHtml);
                        }
                        if (response.success && response.result) {
                            // Simpan state aktif ke sessionStorage
                            sessionStorage.setItem('active_angkatan_angkatan', angkatan);
                            if (prodi) sessionStorage.setItem('active_angkatan_prodi', prodi);

                            // Update URL query parameters tanpa reload
                            var currentUrl = new URL(window.location.href);
                            currentUrl.searchParams.set('angkatan', angkatan);
                            if (prodi) currentUrl.searchParams.set('prodi', prodi);
                            window.history.replaceState({}, '', currentUrl.toString());

                            renderVisualisasiAngkatanData(response);

                            if (isAutoLoad) {
                                $('#tugas').hide();
                                $('#visualContainer').show();
                            } else {
                                $('#tugas').slideUp(300, function() {
                                    $('#visualContainer').fadeIn(350);
                                    window.scrollTo({ top: 0, behavior: 'smooth' });
                                });
                            }
                        } else {
                            if (isAutoLoad) {
                                $('#visualContainer').hide();
                                $('#tugas').show();
                            } else {
                                alert('Gagal memuat visualisasi angkatan: ' + (response.message || 'Data tidak ditemukan'));
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        if (!isAutoLoad) {
                            $submitBtn.prop('disabled', false).html(originalBtnHtml);
                            console.error('AJAX error:', xhr.responseText);
                            alert('Terjadi kesalahan saat memuat visualisasi angkatan.');
                        } else {
                            $('#visualContainer').hide();
                            $('#tugas').show();
                        }
                    }
                });
            }

            // Main Form Submit
            $('#hasilVisual').on('submit', function(event) {
                event.preventDefault();
                var prodi = $('#prodiForm').val() || "{{ auth()->user()->id_prodiUser ?? '' }}";
                var angkatan = $('#angkatanForm').val();
                fetchAndRenderAngkatan(prodi, angkatan, false);
            });

            // Inisialisasi: Cek parameter URL (mendukung refresh F5 pada halaman output)
            var urlParams = new URLSearchParams(window.location.search);
            var savedAngkatan = urlParams.get('angkatan');
            var savedProdi = urlParams.get('prodi') || $('#prodiForm').val() || "{{ auth()->user()->id_prodiUser ?? '' }}";

            if (savedAngkatan) {
                $('#tugas').hide();
                $('#visualContainer').show();
                if (savedProdi) $('#prodiForm').val(savedProdi);
                $('#angkatanForm').val(savedAngkatan);
                $('#angkatanDisplayInput').val(savedAngkatan);
                fetchAndRenderAngkatan(savedProdi, savedAngkatan, true);
            } else {
                $('#antiFlickerStyle').remove();
                $('#tugas').show();
                $('#visualContainer').hide();
            }

            // Back to filter button (Ganti Angkatan)
            $('#btnBackToFilter').on('click', function() {
                sessionStorage.removeItem('active_angkatan_angkatan');
                sessionStorage.removeItem('active_angkatan_prodi');
                $('#antiFlickerStyle').remove();

                var cleanUrl = new URL(window.location.href);
                cleanUrl.searchParams.delete('angkatan');
                cleanUrl.searchParams.delete('prodi');
                window.history.replaceState({}, '', cleanUrl.toString());

                $('#visualContainer').fadeOut(250, function() {
                    $('#tugas').fadeIn(250);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            });

            // Live Search Handlers for Mahasiswa Angkatan Table
            $('#searchMahasiswaAngkatan').on('input', function() {
                window.currentBatchPage = 1;
                renderMahasiswaAngkatanTable();
            });

            $('#btnClearSearchMahasiswa').on('click', function() {
                $('#searchMahasiswaAngkatan').val('');
                window.currentBatchPage = 1;
                renderMahasiswaAngkatanTable();
                $('#searchMahasiswaAngkatan').focus();
            });

            // Page Size Change Handler
            $('#selectPageSizeMahasiswa').on('change', function() {
                var val = $(this).val();
                window.currentBatchPageSize = (val === 'all') ? 'all' : parseInt(val, 10);
                window.currentBatchPage = 1;
                renderMahasiswaAngkatanTable();
            });

            // Toggle Mode Button Click (Skor 0-100 vs Persentase %)
            $(document).on('click', '.btn-toggle-mode', function() {
                var selectedMode = $(this).data('mode');
                window.currentBatchTableMode = selectedMode;

                $('.btn-toggle-mode').removeClass('active btn-primary').addClass('btn-outline-primary');
                $(this).addClass('active btn-primary').removeClass('btn-outline-primary');

                renderMahasiswaAngkatanTable();
            });

            function renderMahasiswaPaginationControls(totalPages, currentPage) {
                var $container = $('#paginationControlsMahasiswa');
                $container.empty();

                if (totalPages <= 1) {
                    return;
                }

                // Prev Button
                var prevDisabled = (currentPage <= 1);
                var $prevLi = $('<li class="page-item' + (prevDisabled ? ' disabled' : '') + '"><a class="page-link" href="#" data-page="' + (currentPage - 1) + '" style="font-size: 0.8rem; border-radius: 6px; padding: 4px 10px;"><i class="bi bi-chevron-left"></i></a></li>');
                $container.append($prevLi);

                // Page window calculation
                var startPage = Math.max(1, currentPage - 2);
                var endPage = Math.min(totalPages, currentPage + 2);

                if (startPage > 1) {
                    $container.append('<li class="page-item"><a class="page-link" href="#" data-page="1" style="font-size: 0.8rem; border-radius: 6px; padding: 4px 10px;">1</a></li>');
                    if (startPage > 2) {
                        $container.append('<li class="page-item disabled"><span class="page-link" style="font-size: 0.8rem; border-radius: 6px; padding: 4px 10px;">...</span></li>');
                    }
                }

                for (var p = startPage; p <= endPage; p++) {
                    var isActive = (p === currentPage);
                    var $pLi = $('<li class="page-item' + (isActive ? ' active' : '') + '"><a class="page-link" href="#" data-page="' + p + '" style="font-size: 0.8rem; border-radius: 6px; padding: 4px 10px;">' + p + '</a></li>');
                    $container.append($pLi);
                }

                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) {
                        $container.append('<li class="page-item disabled"><span class="page-link" style="font-size: 0.8rem; border-radius: 6px; padding: 4px 10px;">...</span></li>');
                    }
                    $container.append('<li class="page-item"><a class="page-link" href="#" data-page="' + totalPages + '" style="font-size: 0.8rem; border-radius: 6px; padding: 4px 10px;">' + totalPages + '</a></li>');
                }

                // Next Button
                var nextDisabled = (currentPage >= totalPages);
                var $nextLi = $('<li class="page-item' + (nextDisabled ? ' disabled' : '') + '"><a class="page-link" href="#" data-page="' + (currentPage + 1) + '" style="font-size: 0.8rem; border-radius: 6px; padding: 4px 10px;"><i class="bi bi-chevron-right"></i></a></li>');
                $container.append($nextLi);

                // Click event
                $container.find('a.page-link').on('click', function(e) {
                    e.preventDefault();
                    var targetPage = parseInt($(this).attr('data-page'), 10);
                    if (!isNaN(targetPage) && targetPage >= 1 && targetPage <= totalPages && targetPage !== currentPage) {
                        window.currentBatchPage = targetPage;
                        renderMahasiswaAngkatanTable();
                    }
                });
            }

            function renderMahasiswaAngkatanTable() {
                var mode = window.currentBatchTableMode || 'skor';
                var students = window.currentBatchStudents || [];
                var uniqueCplCodes = window.currentUniqueCplCodes || [];
                var searchQuery = ($('#searchMahasiswaAngkatan').val() || '').trim().toLowerCase();

                if (searchQuery.length > 0) {
                    $('#btnClearSearchMahasiswa').show();
                } else {
                    $('#btnClearSearchMahasiswa').hide();
                }

                var filteredStudents = students;
                if (searchQuery.length > 0) {
                    filteredStudents = students.filter(function(st) {
                        var npm = String(st.npm || '').toLowerCase();
                        var nama = String(st.nama_mhs || '').toLowerCase();
                        return npm.includes(searchQuery) || nama.includes(searchQuery);
                    });
                }

                var totalItems = filteredStudents.length;
                var pageSize = window.currentBatchPageSize;
                var totalPages = 1;
                if (pageSize !== 'all') {
                    totalPages = Math.ceil(totalItems / pageSize) || 1;
                    if (window.currentBatchPage > totalPages) window.currentBatchPage = totalPages;
                    if (window.currentBatchPage < 1) window.currentBatchPage = 1;
                }

                var startIndex = (pageSize === 'all') ? 0 : (window.currentBatchPage - 1) * pageSize;
                var endIndex = (pageSize === 'all') ? totalItems : Math.min(startIndex + pageSize, totalItems);
                var pagedStudents = filteredStudents.slice(startIndex, endIndex);

                if (students.length === 0) {
                    $('#mahasiswaCountInfo').text('0 Mahasiswa');
                    $('#pageInfoMahasiswa').text('Menampilkan 0 data');
                } else if (searchQuery.length > 0) {
                    $('#mahasiswaCountInfo').html('<strong>' + filteredStudents.length + '</strong> / ' + students.length + ' Mahasiswa');
                    $('#pageInfoMahasiswa').text('Menampilkan ' + (totalItems > 0 ? (startIndex + 1) : 0) + ' - ' + endIndex + ' dari ' + totalItems + ' hasil pencarian');
                } else {
                    $('#mahasiswaCountInfo').html('Total: <strong>' + students.length + '</strong> Mahasiswa');
                    if (pageSize === 'all') {
                        $('#pageInfoMahasiswa').text('Menampilkan semua (' + totalItems + ') mahasiswa');
                    } else {
                        $('#pageInfoMahasiswa').text('Menampilkan ' + (totalItems > 0 ? (startIndex + 1) : 0) + ' - ' + endIndex + ' dari ' + totalItems + ' mahasiswa');
                    }
                }

                renderMahasiswaPaginationControls(totalPages, window.currentBatchPage);

                var tableMahasiswaBody = $('#mahasiswaAngkatanTable tbody');
                var tableMahasiswaHead = $('#mahasiswaAngkatanTable thead tr');

                tableMahasiswaBody.empty();
                tableMahasiswaHead.empty();

                // Headers
                tableMahasiswaHead.append('<th style="width: 50px;" class="align-middle text-center">No</th>');
                tableMahasiswaHead.append('<th class="align-middle text-center" style="width: 120px;">NPM</th>');
                tableMahasiswaHead.append('<th class="align-middle text-start" style="min-width: 180px;">Nama Mahasiswa</th>');
                for (var i = 0; i < uniqueCplCodes.length; i++) {
                    var headerLabel = (mode === 'persen') ? uniqueCplCodes[i] + ' (%)' : uniqueCplCodes[i];
                    tableMahasiswaHead.append('<th class="align-middle text-center" style="min-width: 85px;">' + headerLabel + '</th>');
                }

                // Rows
                if (pagedStudents.length > 0) {
                    for (var i = 0; i < pagedStudents.length; i++) {
                        var student = pagedStudents[i];
                        var rowNum = startIndex + i + 1;
                        var rowHtml = '<tr>' +
                            '<td class="text-center text-muted" style="font-size: 0.85rem;">' + rowNum + '</td>' +
                            '<td class="text-center fw-medium font-monospace text-dark" style="font-size: 0.85rem;">' + student.npm + '</td>' +
                            '<td class="text-start fw-semibold text-dark text-truncate" style="max-width: 260px; font-size: 0.85rem;" title="' + student.nama_mhs + '">' + student.nama_mhs + '</td>';

                        for (var j = 0; j < uniqueCplCodes.length; j++) {
                            var cplCode = uniqueCplCodes[j];
                            var cellVal = 0;
                            if (mode === 'persen') {
                                cellVal = (student.persentase && student.persentase[cplCode] !== undefined) ? roundToTwo(student.persentase[cplCode]) : 0;
                            } else {
                                cellVal = (student.cpls && student.cpls[cplCode] !== undefined) ? roundToTwo(student.cpls[cplCode]) : 0;
                            }

                            if (cellVal > 0) {
                                var displayVal = (mode === 'persen') ? cellVal + '%' : cellVal;
                                rowHtml += '<td class="text-center fw-semibold text-dark" style="font-size: 0.85rem;">' + displayVal + '</td>';
                            } else {
                                rowHtml += '<td class="text-center text-muted" style="color: #94a3b8 !important; font-size: 0.85rem;">-</td>';
                            }
                        }

                        rowHtml += '</tr>';
                        tableMahasiswaBody.append(rowHtml);
                    }
                } else {
                    var totalColspan = uniqueCplCodes.length + 3;
                    if (searchQuery.length > 0) {
                        tableMahasiswaBody.append('<tr><td colspan="' + totalColspan + '" class="text-center text-muted py-3"><i class="bi bi-search me-1"></i> Mahasiswa dengan nama/NPM "<strong>' + searchQuery + '</strong>" tidak ditemukan.</td></tr>');
                    } else {
                        tableMahasiswaBody.append('<tr><td colspan="' + totalColspan + '" class="text-center text-muted py-3">Data mahasiswa kosong</td></tr>');
                    }
                }
            }

            function renderVisualisasiAngkatanData(response) {
                var angkatan = response.result.angkatan;
                var prodi = response.result.prodi;
                var imgSrc = response.result.imgSrc;
                var universitas = response.result.universitas;
                var labelCpl = response.result.labelCpl || [];
                var soalTerendah = response.result.soalTerendah || [];
                var allCplPerAngkatan = response.result.allCplPerAngkatan || {};
                var gabunganAkhirMk = response.result.gabunganAkhirMk || {};
                var cplResultsAll = response.result.cplResultsAll || [];
                var mahasiswaAngkatan = response.result.mahasiswaAngkatan || [];
                var hasData = response.result.hasData;

                // Handle Empty Data Notice Banner
                if (!hasData) {
                    $('#emptyDataMessage').html('Tidak ada data penilaian/asesmen mata kuliah angkatan untuk angkatan ' + angkatan + '.');
                    $('#emptyDataAlert').slideDown();
                } else {
                    $('#emptyDataAlert').slideUp();
                }

                // Header Info
                var prodiNama = (prodi && prodi[0] && prodi[0].nama) ? prodi[0].nama : (prodi || '-');
                $('#displayInfoAngkatan').text('Angkatan ' + angkatan + ' - ' + prodiNama);
                $('#angkatanDataText').text(angkatan);
                $('#prodiDataText').text(prodiNama);
                $('#universitasDataText').text(universitas || '-');

                // Hidden fields for CPMK Angkatan Form
                $('input[name="angkatan"]').val(angkatan);
                $('#prodiHidden').val(prodiNama);
                $('input[name="universitasImg"]').val(imgSrc);
                $('input[name="universitasCPMK"]').val(universitas);

                // Table Rincian Capaian CPL Angkatan Per Tahun
                var yearsList = response.result.yearsList || [];
                var yearsWithData = response.result.yearsWithData || {};
                var ketercapaianCplPerTahun = response.result.ketercapaianCplPerTahun || {};
                var ketercapaianCplYearlyAvg = response.result.ketercapaianCplYearlyAvg || {};

                // Cari tahun aktif terakhir (latest year with data)
                var activeYearsWithData = yearsList.filter(function(yr) {
                    return yearsWithData[yr] !== false;
                });
                var latestYear = activeYearsWithData.length > 0 ? activeYearsWithData[activeYearsWithData.length - 1] : (yearsList[0] || null);

                // Rata-rata Ketercapaian CPL menggunakan rata-rata tahun terakhir
                var latestYearAvg = (latestYear && ketercapaianCplYearlyAvg[latestYear] !== null && ketercapaianCplYearlyAvg[latestYear] !== undefined)
                    ? roundToTwo(ketercapaianCplYearlyAvg[latestYear])
                    : 0;

                var maxCplCode = '', maxCplVal = -1;
                var minCplCode = '', minCplVal = 999;

                if (latestYear && ketercapaianCplPerTahun) {
                    $.each(ketercapaianCplPerTahun, function(cpl, pctObj) {
                        if (pctObj[latestYear] !== null && pctObj[latestYear] !== undefined) {
                            var val = roundToTwo(pctObj[latestYear]);
                            if (val > maxCplVal) {
                                maxCplVal = val;
                                maxCplCode = cpl;
                            }
                            if (val > 0 && val < minCplVal) {
                                minCplVal = val;
                                minCplCode = cpl;
                            }
                        }
                    });
                    if (minCplCode === '' || minCplVal === 999) {
                        $.each(ketercapaianCplPerTahun, function(cpl, pctObj) {
                            if (pctObj[latestYear] !== null && pctObj[latestYear] !== undefined) {
                                var val = roundToTwo(pctObj[latestYear]);
                                if (val < minCplVal) {
                                    minCplVal = val;
                                    minCplCode = cpl;
                                }
                            }
                        });
                    }
                } else {
                    $.each(allCplPerAngkatan.avg_cpl || {}, function(cpl, val) {
                        var rVal = roundToTwo(val);
                        if (rVal > maxCplVal) {
                            maxCplVal = rVal;
                            maxCplCode = cpl;
                        }
                        if (rVal > 0 && rVal < minCplVal) {
                            minCplVal = rVal;
                            minCplCode = cpl;
                        }
                    });
                }

                // Executive Cards
                $('#cardAvgCplAngkatan').text(latestYearAvg + '%');

                if (maxCplCode && maxCplVal >= 0) {
                    $('#cardMaxCplAngkatan').html('<span class="text-success">' + maxCplCode + '</span> (' + roundToTwo(maxCplVal) + '%)');
                } else {
                    $('#cardMaxCplAngkatan').text('-');
                }

                if (minCplCode && minCplVal !== 999) {
                    $('#cardMinCplAngkatan').html('<span class="text-danger">' + minCplCode + '</span> (' + roundToTwo(minCplVal) + '%)');
                } else {
                    $('#cardMinCplAngkatan').text('-');
                }

                var $theadRincian = $('#theadRincianCplScoresAngkatan');
                $theadRincian.empty();
                $theadRincian.append('<th style="min-width: 120px;" class="text-start">Kode CPL</th>');

                $.each(yearsList, function(idx, yr) {
                    var hasDataYr = yearsWithData[yr] !== undefined ? yearsWithData[yr] : true;
                    if (hasDataYr) {
                        $theadRincian.append('<th class="text-center interactive-year-th" data-year="' + yr + '" style="min-width: 80px;" title="Klik untuk melihat diagram tahun ' + yr + '">' + yr + '</th>');
                    } else {
                        $theadRincian.append('<th class="text-center interactive-year-th text-muted bg-light" data-year="' + yr + '" style="min-width: 80px; color: #94a3b8 !important;" title="Belum Ditempuh (Klik untuk melihat diagram tahun ' + yr + ')">' + yr + '</th>');
                    }
                });

                var $tableRincian = $('#tableRincianCplScoresAngkatan tbody');
                $tableRincian.empty();

                if (ketercapaianCplPerTahun && Object.keys(ketercapaianCplPerTahun).length > 0) {
                    $.each(ketercapaianCplPerTahun, function(cplKode, pctObj) {
                        var rowHtml = '<tr><td class="fw-bold text-dark text-start">' + cplKode + '</td>';
                        $.each(yearsList, function(idx, yr) {
                            var hasDataYr = yearsWithData[yr] !== undefined ? yearsWithData[yr] : true;
                            if (hasDataYr && pctObj[yr] !== null && pctObj[yr] !== undefined) {
                                var pctVal = roundToTwo(pctObj[yr]);
                                rowHtml += '<td class="text-center fw-semibold text-dark interactive-year-td" data-year="' + yr + '" style="cursor: pointer;" title="Klik tahun ' + yr + '">' + pctVal + '%</td>';
                            } else {
                                rowHtml += '<td class="text-center text-muted bg-light interactive-year-td" data-year="' + yr + '" style="color: #94a3b8 !important; background-color: #f8fafc !important; cursor: pointer;" title="Belum Ditempuh">-</td>';
                            }
                        });
                        rowHtml += '</tr>';
                        $tableRincian.append(rowHtml);
                    });

                    // Summary Row (Rata-rata Ketercapaian per Tahun)
                    var $tfootRincian = $('#tableRincianCplScoresAngkatan tfoot');
                    $tfootRincian.empty();
                    var footerHtml = '<tr style="background-color: #ebf5fb;"><th class="text-primary fw-bold text-start" style="color: #1F3BB3 !important;">Rata-rata Ketercapaian</th>';
                    $.each(yearsList, function(idx, yr) {
                        var hasDataYr = yearsWithData[yr] !== undefined ? yearsWithData[yr] : true;
                        if (hasDataYr && ketercapaianCplYearlyAvg[yr] !== null && ketercapaianCplYearlyAvg[yr] !== undefined) {
                            var avgPctYr = roundToTwo(ketercapaianCplYearlyAvg[yr]);
                            footerHtml += '<th class="text-center text-primary fw-bold interactive-year-tf" data-year="' + yr + '" style="cursor: pointer;" title="Klik tahun ' + yr + '">' + avgPctYr + '%</th>';
                        } else {
                            footerHtml += '<th class="text-center text-muted bg-light interactive-year-tf" data-year="' + yr + '" style="cursor: pointer;" title="Belum Ditempuh">-</th>';
                        }
                    });
                    footerHtml += '</tr>';
                    $tfootRincian.append(footerHtml);
                } else {
                    $tableRincian.append('<tr><td colspan="' + (yearsList.length + 1) + '" class="text-center text-muted py-3">Data kosong</td></tr>');
                }

                // Pemetaan CPL Dropdown
                var $select = $('#selectPemetaanCpl');
                $select.empty();
                $select.append($('<option>', {
                    value: '',
                    text: 'Pilih CPL'
                }));
                $.each(cplResultsAll, function(index, cpl) {
                    $select.append($('<option>', {
                        value: cpl.id,
                        text: cpl.kode
                    }));
                });

                // CPL Descriptions (Structured Compact Cards Grid)
                var labelContainer = $('#labelContainer');
                labelContainer.empty();
                if (labelCpl && labelCpl.length > 0) {
                    $('#cplBadgeCount').text(labelCpl.length + ' CPL').show();
                    labelCpl.forEach(function(item) {
                        var itemHtml = 
                            '<div class="cpl-desc-card">' +
                                '<span class="badge bg-primary text-white cpl-desc-badge">' + item.kode + '</span>' +
                                '<span class="cpl-desc-text">' + item.judul + '</span>' +
                            '</div>';
                        labelContainer.append(itemHtml);
                    });
                } else {
                    $('#cplBadgeCount').hide();
                    labelContainer.append('<div class="text-muted small">Tidak ada deskripsi CPL yang tersedia.</div>');
                }

                // Soal Terendah Table
                var tableSoalBody = $('#soalTerendahTable tbody');
                tableSoalBody.empty();
                if (soalTerendah && soalTerendah.length > 0) {
                    for (var i = 0; i < soalTerendah.length; i++) {
                        var soalText = (soalTerendah[i].soal && soalTerendah[i].soal !== 'null') ? soalTerendah[i].soal : '-';
                        var rowHtml = '<tr>' +
                            '<td class="text-center text-muted fw-semibold">' + (i + 1) + '</td>' +
                            '<td class="fw-medium text-dark">' + (soalTerendah[i].namaCourse || '-') + '</td>' +
                            '<td class="text-center"><span class="badge" style="background:#f1f5f9; color:#334155; border:1px solid #e2e8f0; font-size:12px; font-weight:500;">' + (soalTerendah[i].Jenis || '-') + '</span></td>';

                        if (soalTerendah[i].idSoal) {
                            let baseUrl = "{{ route($currentPrefix . 'cetakSoal', ['id' => '__ID__']) }}";
                            baseUrl = baseUrl.replace('__ID__', soalTerendah[i].idSoal);
                            rowHtml += '<td><a href="' + baseUrl + '" target="_blank" class="text-decoration-none fw-semibold text-primary">' +
                                soalText + ' <i class="bi bi-box-arrow-up-right small"></i></a></td>';
                        } else {
                            rowHtml += '<td><span class="text-dark fw-medium">' + soalText + '</span></td>';
                        }

                        rowHtml += '</tr>';
                        tableSoalBody.append(rowHtml);
                    }
                } else {
                    tableSoalBody.append('<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada data soal</td></tr>');
                }

                // CPL Calculation Courses (Tampilan Mirip All Courses Taken)
                var $courseGrid = $('#cplCoursesGrid');
                var olElement = $('#courseList');
                $courseGrid.empty();
                olElement.empty();

                var courseKeys = Object.keys(gabunganAkhirMk || {});
                $('#cplCoursesCountBadge').text(courseKeys.length + ' MK');

                if (courseKeys.length > 0) {
                    for (var i = 0; i < courseKeys.length; i++) {
                        var kodeMk = courseKeys[i];
                        var namaMk = gabunganAkhirMk[kodeMk];

                        var cardHtml = '<div class="course-card-item">' +
                            '<div class="d-flex align-items-center gap-2 overflow-hidden">' +
                                '<span class="badge bg-primary text-white font-monospace" style="font-size:11px; padding:4px 7px;">' + kodeMk + '</span>' +
                                '<span class="fw-semibold text-dark text-truncate small" title="' + namaMk + '">' + namaMk + '</span>' +
                            '</div>' +
                        '</div>';
                        $courseGrid.append(cardHtml);

                        olElement.append('<li>' + kodeMk + ' - ' + namaMk + '</li>');
                    }
                } else {
                    $courseGrid.html('<div class="p-3 text-center text-muted small w-100">Tidak ada mata kuliah terkait pada angkatan ini.</div>');
                    olElement.append('<li class="text-muted">Tidak ada mata kuliah terkait pada angkatan ini.</li>');
                }

                // Course selection list for CPMK form (menampilkan semua MK di program studi)
                var allCourseList = response.result.allCourses || gabunganAkhirMk;
                courseOptionsData = [];
                for (var courseCode in allCourseList) {
                    courseOptionsData.push({
                        code: courseCode,
                        name: allCourseList[courseCode],
                        value: courseCode,
                        text: courseCode + ' - ' + allCourseList[courseCode]
                    });
                }
                $('#courseSelect').val('');
                $('#courseDisplayInput').val('');
                renderCourseOptions('');

                // Group student data for both Skor and Persentase
                $('#mahasiswaAngkatanTitle').html('<i class="bi bi-people me-1"></i> Mahasiswa Angkatan ' + angkatan);

                var uniqueCplCodes = [];
                for (var i = 0; i < mahasiswaAngkatan.length; i++) {
                    var cplCode = mahasiswaAngkatan[i].kode;
                    if (uniqueCplCodes.indexOf(cplCode) === -1) {
                        uniqueCplCodes.push(cplCode);
                    }
                }

                uniqueCplCodes.sort(function(a, b) {
                    var numA = parseInt(a.replace(/\D/g, '')) || 0;
                    var numB = parseInt(b.replace(/\D/g, '')) || 0;
                    return numA - numB;
                });

                var groupedData = {};
                for (var i = 0; i < mahasiswaAngkatan.length; i++) {
                    var item = mahasiswaAngkatan[i];
                    var npm = item.npm;
                    if (!groupedData[npm]) {
                        groupedData[npm] = {
                            npm: npm,
                            nama_mhs: item.nama_mhs,
                            cpls: {},
                            persentase: {}
                        };
                    }
                    groupedData[npm].cpls[item.kode] = item.hasil;
                    groupedData[npm].persentase[item.kode] = item.persentase !== undefined ? item.persentase : 0;
                }

                window.currentBatchStudents = Object.values(groupedData);
                window.currentUniqueCplCodes = uniqueCplCodes;

                // Render Table with active mode
                renderMahasiswaAngkatanTable();

                // Render Radar Chart
                if (radarChartAngkatanInstance) {
                    radarChartAngkatanInstance.destroy();
                    radarChartAngkatanInstance = null;
                }

                var labelsCapaianCpl = Object.keys(allCplPerAngkatan.avg_cpl || {});
                var dataMinCpl = Object.values(allCplPerAngkatan.min || {}).map(function(v) { return roundToTwo(v); });
                var dataAvgCpl = Object.values(allCplPerAngkatan.avg_cpl || {}).map(function(v) { return roundToTwo(v); });
                var dataMaxCpl = Object.values(allCplPerAngkatan.max || {}).map(function(v) { return roundToTwo(v); });

                var canvas = document.getElementById('radarChartAngkatan');
                var ctx = canvas.getContext('2d');
                radarChartAngkatanInstance = new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: labelsCapaianCpl,
                        datasets: [{
                                label: 'Average CPL',
                                data: dataAvgCpl,
                                backgroundColor: 'rgba(31, 59, 179, 0.12)',
                                borderColor: '#1F3BB3',
                                borderWidth: 2.5,
                                pointBackgroundColor: '#1F3BB3',
                                pointRadius: 4,
                                pointHoverRadius: 6
                            },
                            {
                                label: 'Min CPL',
                                data: dataMinCpl,
                                backgroundColor: 'rgba(239, 68, 68, 0.05)',
                                borderColor: 'rgba(239, 68, 68, 0.8)',
                                borderWidth: 1.5,
                                borderDash: [4, 4],
                                pointBackgroundColor: '#ef4444',
                                pointRadius: 3,
                                pointHoverRadius: 5
                            },
                            {
                                label: 'Max CPL',
                                data: dataMaxCpl,
                                backgroundColor: 'rgba(16, 185, 129, 0.05)',
                                borderColor: 'rgba(16, 185, 129, 0.8)',
                                borderWidth: 1.5,
                                borderDash: [4, 4],
                                pointBackgroundColor: '#10b981',
                                pointRadius: 3,
                                pointHoverRadius: 5
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        scale: {
                            ticks: {
                                max: 100,
                                min: 0,
                                stepSize: 20
                            }
                        },
                        tooltips: {
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    var datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
                                    return datasetLabel + ': ' + tooltipItem.yLabel + '%';
                                }
                            }
                        }
                    }
                });

                // Helper Fungsi Update Diagram Capaian CPL Angkatan (%) per Tahun
                var activeYearAngkatan = null;

                function updateRadarChartAngkatan(selectedYear) {
                    activeYearAngkatan = selectedYear;
                    var labels = Object.keys(allCplPerAngkatan.avg_cpl || {});
                    var datasetData = [];

                    if (selectedYear) {
                        datasetData = labels.map(function(k) {
                            if (ketercapaianCplPerTahun[k] && ketercapaianCplPerTahun[k][selectedYear] !== null && ketercapaianCplPerTahun[k][selectedYear] !== undefined) {
                                return roundToTwo(ketercapaianCplPerTahun[k][selectedYear]);
                            }
                            return 0;
                        });
                        var hasDataYr = yearsWithData[selectedYear] !== undefined ? yearsWithData[selectedYear] : true;
                        var labelSuffix = !hasDataYr ? ' (Belum Ditempuh)' : '';
                        $('#badgeYearAngkatan .badge-year-text').text('Tahun ' + selectedYear + labelSuffix);
                        $('#badgeYearAngkatan').fadeIn(150);

                        // Hitung metrik spesifik untuk tahun yang dipilih
                        var yrAvg = (ketercapaianCplYearlyAvg[selectedYear] !== null && ketercapaianCplYearlyAvg[selectedYear] !== undefined)
                            ? roundToTwo(ketercapaianCplYearlyAvg[selectedYear])
                            : 0;

                        var yrMaxCode = '', yrMaxVal = -1;
                        var yrMinCode = '', yrMinVal = 999;

                        $.each(ketercapaianCplPerTahun || {}, function(cplKode, pctObj) {
                            if (pctObj[selectedYear] !== null && pctObj[selectedYear] !== undefined) {
                                var val = roundToTwo(pctObj[selectedYear]);
                                if (val > yrMaxVal) {
                                    yrMaxVal = val;
                                    yrMaxCode = cplKode;
                                }
                                if (val > 0 && val < yrMinVal) {
                                    yrMinVal = val;
                                    yrMinCode = cplKode;
                                }
                            }
                        });

                        if (yrMinCode === '' || yrMinVal === 999) {
                            $.each(ketercapaianCplPerTahun || {}, function(cplKode, pctObj) {
                                if (pctObj[selectedYear] !== null && pctObj[selectedYear] !== undefined) {
                                    var val = roundToTwo(pctObj[selectedYear]);
                                    if (val < yrMinVal) {
                                        yrMinVal = val;
                                        yrMinCode = cplKode;
                                    }
                                }
                            });
                        }

                        // Perbarui kartu ringkasan eksekutif untuk tahun yang dipilih
                        $('#cardAvgCplAngkatan').text(yrAvg + '%');
                        if (yrMaxCode && yrMaxVal >= 0) {
                            $('#cardMaxCplAngkatan').html('<span class="text-success">' + yrMaxCode + '</span> (' + yrMaxVal + '%)');
                        } else {
                            $('#cardMaxCplAngkatan').text('-');
                        }
                        if (yrMinCode && yrMinVal !== 999) {
                            $('#cardMinCplAngkatan').html('<span class="text-danger">' + yrMinCode + '</span> (' + yrMinVal + '%)');
                        } else {
                            $('#cardMinCplAngkatan').text('-');
                        }

                        // Highlight kolom aktif di Tabel
                        $('#theadRincianCplScoresAngkatan th.interactive-year-th').removeClass('active-year-col');
                        $('#tableRincianCplScoresAngkatan tbody td.interactive-year-td').removeClass('active-year-col');
                        $('#tableRincianCplScoresAngkatan tfoot th.interactive-year-tf').removeClass('active-year-col');

                        $('#theadRincianCplScoresAngkatan th[data-year="' + selectedYear + '"]').addClass('active-year-col');
                        $('#tableRincianCplScoresAngkatan tbody td[data-year="' + selectedYear + '"]').addClass('active-year-col');
                        $('#tableRincianCplScoresAngkatan tfoot th[data-year="' + selectedYear + '"]').addClass('active-year-col');
                    } else {
                        datasetData = labels.map(function(k) {
                            return roundToTwo(allCplPerAngkatan.avg_cpl[k] || 0);
                        });
                        $('#badgeYearAngkatan').hide();

                        // Kembalikan ke kartu ringkasan keseluruhan angkatan (berdasarkan tahun terakhir)
                        $('#cardAvgCplAngkatan').text(latestYearAvg + '%');
                        if (maxCplCode && maxCplVal >= 0) {
                            $('#cardMaxCplAngkatan').html('<span class="text-success">' + maxCplCode + '</span> (' + roundToTwo(maxCplVal) + '%)');
                        } else {
                            $('#cardMaxCplAngkatan').text('-');
                        }
                        if (minCplCode && minCplVal !== 999) {
                            $('#cardMinCplAngkatan').html('<span class="text-danger">' + minCplCode + '</span> (' + roundToTwo(minCplVal) + '%)');
                        } else {
                            $('#cardMinCplAngkatan').text('-');
                        }

                        $('#theadRincianCplScoresAngkatan th.interactive-year-th').removeClass('active-year-col');
                        $('#tableRincianCplScoresAngkatan tbody td.interactive-year-td').removeClass('active-year-col');
                        $('#tableRincianCplScoresAngkatan tfoot th.interactive-year-tf').removeClass('active-year-col');
                    }

                    if (radarChartAngkatanInstance) {
                        radarChartAngkatanInstance.data.datasets[0].label = selectedYear ? 'Average CPL (Tahun ' + selectedYear + ')' : 'Average CPL';
                        radarChartAngkatanInstance.data.datasets[0].data = datasetData;
                        radarChartAngkatanInstance.update();
                    }
                }

                // Event Listener Klik Tahun pada Tabel Rincian Ketercapaian CPL
                $('#tableRincianCplScoresAngkatan').off('click', '[data-year]').on('click', '[data-year]', function() {
                    var clickedYr = $(this).data('year');
                    if (activeYearAngkatan == clickedYr) {
                        updateRadarChartAngkatan(null);
                    } else {
                        updateRadarChartAngkatan(clickedYr);
                    }
                });

                $('#badgeYearAngkatan').off('click').on('click', function() {
                    updateRadarChartAngkatan(null);
                });

                updateRadarChartAngkatan(null);

                // Reset Deskripsi CPL ke keadaan tertutup setiap kali memuat data baru
                $('#collapseCplDescriptions').hide();
                $('#iconCplDescCollapse').removeClass('bi-chevron-up').addClass('bi-chevron-down');

                // Enable Print PDF
                $('#btnPrintPdf').prop('disabled', false);

                $('#btnPrintPdf').off('click').on('click', function() {
                    let radarChartAngkatan = document.getElementById("radarChartAngkatan");
                    let radarChartAngkatanImg = radarChartAngkatan ? radarChartAngkatan.toDataURL() : null;

                    let descriptions = [];
                    document.querySelectorAll("#labelContainer .cpl-desc-card").forEach(card => {
                        descriptions.push(card.textContent.trim().replace(/\s+/g, ' '));
                    });

                    let soalTerendahArr = [];
                    document.querySelectorAll("#soalTerendahTable tbody tr").forEach(row => {
                        let cols = row.querySelectorAll("td");
                        if (cols.length >= 4 && !row.querySelector("td.text-muted")) {
                            soalTerendahArr.push({
                                no: cols[0].textContent.trim(),
                                course_name: cols[1].textContent.trim(),
                                types_of_assessment: cols[2].textContent.trim(),
                                question: cols[3].textContent.trim(),
                            });
                        }
                    });

                    let mahasiswaAngkatanArr = [];
                    let cplCodes = window.currentUniqueCplCodes || [];

                    if (window.currentBatchStudents && window.currentBatchStudents.length > 0) {
                        let no = 1;
                        window.currentBatchStudents.forEach(student => {
                            let cplList = [];
                            let totalSkor = 0;
                            let totalPersen = 0;

                            cplCodes.forEach(cplCode => {
                                let skorVal = (student.cpls && student.cpls[cplCode] !== undefined) ? roundToTwo(student.cpls[cplCode]) : 0;
                                let persenVal = (student.persentase && student.persentase[cplCode] !== undefined) ? roundToTwo(student.persentase[cplCode]) : 0;
                                totalSkor += skorVal;
                                totalPersen += persenVal;

                                cplList.push({
                                    kode: cplCode,
                                    skor: skorVal,
                                    persen: persenVal,
                                    status: skorVal >= 65 ? 'Tercapai' : (skorVal > 0 ? 'Perlu Peningkatan' : 'Belum Ada Data')
                                });
                            });

                            let count = cplCodes.length;
                            let avgSkor = count > 0 ? roundToTwo(totalSkor / count) : 0;
                            let avgPersen = count > 0 ? roundToTwo(totalPersen / count) : 0;

                            mahasiswaAngkatanArr.push({
                                no: no++,
                                npm: student.npm,
                                nama_mhs: student.nama_mhs,
                                cpls: cplList,
                                avg_skor: avgSkor,
                                avg_persen: avgPersen,
                                status_overall: avgPersen >= 65 ? 'Tercapai' : (avgPersen > 0 ? 'Perlu Peningkatan' : 'Belum Ada Data')
                            });
                        });
                    }

                    let courseListArr = [];
                    document.querySelectorAll("#cplCoursesGrid .course-card-item").forEach(card => {
                        let kodeEl = card.querySelector(".badge");
                        let namaEl = card.querySelector(".fw-semibold");
                        if (kodeEl && namaEl) {
                            courseListArr.push({
                                kode: kodeEl.textContent.trim(),
                                nama: namaEl.textContent.trim()
                            });
                        }
                    });
                    if (courseListArr.length === 0) {
                        document.querySelectorAll("#courseList li").forEach(li => {
                            if (!li.classList.contains("text-muted")) {
                                let text = li.textContent.trim();
                                let parts = text.split(" - ");
                                if (parts.length >= 2) {
                                    courseListArr.push({
                                        kode: parts[0].trim(),
                                        nama: parts.slice(1).join(" - ").trim()
                                    });
                                } else {
                                    courseListArr.push({
                                        kode: text,
                                        nama: text
                                    });
                                }
                            }
                        });
                    }

                    let summaryHtml = document.getElementById("summary") ? document.getElementById("summary").innerHTML.trim() : "";

                    let yearsHeaderArr = [];
                    document.querySelectorAll("#theadRincianCplScoresAngkatan th").forEach((th, idx) => {
                        if (idx > 0) {
                            yearsHeaderArr.push(th.textContent.trim());
                        }
                    });

                    let rincianCplPerTahunArr = [];
                    document.querySelectorAll("#tableRincianCplScoresAngkatan tbody tr").forEach(row => {
                        let cols = row.querySelectorAll("td");
                        if (cols.length >= 2 && !row.querySelector("td[colspan]")) {
                            let kode = cols[0].textContent.trim();
                            let scores = [];
                            for (let i = 1; i < cols.length; i++) {
                                scores.push(cols[i].textContent.trim());
                            }
                            rincianCplPerTahunArr.push({
                                kode: kode,
                                scores: scores
                            });
                        }
                    });

                    let yearlyAvgArr = [];
                    document.querySelectorAll("#tableRincianCplScoresAngkatan tfoot th").forEach((th, idx) => {
                        if (idx > 0) {
                            yearlyAvgArr.push(th.textContent.trim());
                        }
                    });

                    let rincianCplScoresAngkatanArr = [];
                    document.querySelectorAll("#tableRincianCplScoresAngkatan tbody tr").forEach(row => {
                        let cols = row.querySelectorAll("td");
                        if (cols.length >= 2 && !row.querySelector("td[colspan]")) {
                            rincianCplScoresAngkatanArr.push({
                                kode: cols[0].textContent.trim(),
                                avgScore: cols[cols.length - 1].textContent.trim()
                            });
                        }
                    });

                    let avgCplText = $('#cardAvgCplAngkatan').text().trim();
                    let maxCplText = $('#cardMaxCplAngkatan').text().trim().replace(/\s+/g, ' ');
                    let minCplText = $('#cardMinCplAngkatan').text().trim().replace(/\s+/g, ' ');

                    fetch("{{ route($currentPrefix . 'visualisasi.generate-pdfVisualAngkatan') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        },
                        body: JSON.stringify({
                            angkatan: angkatan,
                            prodi: prodiNama,
                            universitas: universitas,
                            tahun: 'all',
                            semester: 'all',
                            avgCpl: avgCplText,
                            maxCpl: maxCplText,
                            minCpl: minCplText,
                            yearsHeader: yearsHeaderArr,
                            rincianCplPerTahun: rincianCplPerTahunArr,
                            yearlyAvg: yearlyAvgArr,
                            courseList: courseListArr,
                            radarChartAngkatanImg: radarChartAngkatanImg,
                            summary: summaryHtml,
                            rincianCplScoresAngkatan: rincianCplScoresAngkatanArr,
                            descriptions: descriptions,
                            soalTerendah: soalTerendahArr,
                            mahasiswaAngkatan: mahasiswaAngkatanArr,
                            cplCodes: cplCodes,
                        }),
                    })
                    .then(response => response.blob())
                    .then(blob => {
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `Laporan Visualisasi Angkatan - ${angkatan}.pdf`;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        URL.revokeObjectURL(url);
                    })
                    .catch(error => {
                        console.error("Error:", error.message);
                    });
                });
            }

            // Pemetaan CPL Change
            $('#selectPemetaanCpl').on('change', function() {
                var idCpl = $(this).val();
                if (!idCpl) {
                    $('#kontenPemetaanCpl').html('<span class="text-muted small">Silahkan pilih salah satu CPL di atas untuk melihat daftar mata kuliah pendukung.</span>');
                    return;
                }
                $.ajax({
                    url: "{{ route($currentPrefix . 'visualisasi.getPemetaanCpl') }}",
                    method: 'GET',
                    data: {
                        idCpl: idCpl,
                    },
                    success: function(data) {
                        if (data && data.length > 0) {
                            var content = '<div class="d-flex flex-wrap gap-2">';
                            data.forEach(function(item) {
                                content += '<span class="badge bg-white text-dark border p-2 fw-medium shadow-sm"><i class="bi bi-journal-bookmark text-primary me-1"></i><strong>' + item.mk_kode + '</strong> - ' + item.nama + '</span>';
                            });
                            content += '</div>';
                            $('#kontenPemetaanCpl').html(content);
                        } else {
                            $('#kontenPemetaanCpl').html('<span class="text-muted small">Tidak ada pemetaan mata kuliah untuk CPL ini.</span>');
                        }
                    }
                });
            });

            // Validasi CPMK Form
            $('#hasilvisualcpmk-angkatan').on('submit', function(event) {
                var selectedCourse = $('#courseSelect').val();
                var textVal = $('#courseDisplayInput').val().trim();
                if (!selectedCourse && textVal && courseOptionsData.length > 0) {
                    var lowerText = textVal.toLowerCase();
                    var match = courseOptionsData.find(function(s) {
                        return s.code.toLowerCase() === lowerText || s.text.toLowerCase() === lowerText || s.name.toLowerCase() === lowerText;
                    });
                    if (!match) {
                        match = courseOptionsData.find(function(s) {
                            return s.code.toLowerCase().startsWith(lowerText) || s.name.toLowerCase().startsWith(lowerText);
                        });
                    }
                    if (match) {
                        selectCourseItem(match.value, match.text);
                        selectedCourse = match.value;
                    }
                }

                if (!selectedCourse) {
                    event.preventDefault();
                    alert('Mohon pilih Mata Kuliah terlebih dahulu!');
                    $('#courseDisplayInput').focus();
                }
            });

            // Toggle Handler untuk Buka / Tutup Petunjuk Membaca Diagram (Smooth slideToggle)
            $('#btnToggleGuideCplBatch').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $target = $('#guideCplBatch');
                if ($target.is(':visible')) {
                    $target.slideUp(200);
                } else {
                    $target.slideDown(200);
                }
            });

            // Toggle Handler untuk Buka / Tutup Deskripsi CPL (Smooth slideToggle)
            $('#headerToggleCplDesc, #btnToggleCplDesc').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $target = $('#collapseCplDescriptions');
                var $icon = $('#iconCplDescCollapse');
                if ($target.is(':visible')) {
                    $target.slideUp(200);
                    $icon.removeClass('bi-chevron-up').addClass('bi-chevron-down');
                } else {
                    $target.slideDown(200);
                    $icon.removeClass('bi-chevron-down').addClass('bi-chevron-up');
                }
            });
        });
    </script>
@endsection
