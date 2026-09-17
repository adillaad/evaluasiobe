@php
    $routePrefix = [
        'Penjamin Mutu Universitas' => ['prefix' => 'penjamin-mutu.universitas.'],
        'Penjamin Mutu Fakultas' => ['prefix' => 'penjamin-mutu.fakultas.'],
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp
@extends('penjamin-mutu.template')
@section('title', 'Visualisasi Per Mahasiswa')
@section('page_title', 'Visualisasi Per Mahasiswa')
@section('content')
    <script>
        (function() {
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('npm') && urlParams.get('angkatan')) {
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

        /* Ensure layout ancestors allow sticky / fixed scrolling */
        html,
        body,
        .container-scroller,
        .page-body-wrapper,
        .main-panel,
        .content-wrapper,
        .content-wrapper > .row {
            overflow: visible !important;
        }

        #visualContainer {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 100% !important;
        }

        /* Student Profile Hero Header (Fixed / Sticky when scrolling, Prominent, Neat Alignment) */
        .student-profile-hero {
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

        .table-responsive table td.wrap-content {
            white-space: normal;
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

        .badge-grade-success {
            background: #dcfce7;
            color: #15803d;
            font-weight: 700;
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid #bbf7d0;
            display: inline-block;
        }

        .badge-grade-danger {
            background: #fee2e2;
            color: #b91c1c;
            font-weight: 700;
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid #fecaca;
            display: inline-block;
        }

        /* All Courses Taken Grid Styling */
        .course-grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 10px;
            max-height: 320px;
            overflow-y: auto;
            padding: 2px;
        }

        .course-card-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            transition: all 0.15s ease;
        }

        .course-card-item:hover {
            background: #ffffff;
            border-color: #cbd5e1;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        /* Pemetaan CPL Interactive Stats Bar */
        .pemetaan-stats-bar {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 0.82rem;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 10px;
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
    {{-- 1. INPUT FILTER FORM (Halaman Awal Pemilihan Mahasiswa) --}}
    {{-- ========================================================================= --}}
    <div id="tugas">
        <div class="card modern-card mb-4">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h5 class="section-title mb-1"><i class="bi bi-person-fill"></i> Visualisasi CPL Per Mahasiswa</h5>
                    <p class="text-muted small mb-0">Silahkan pilih data program studi, angkatan, dan mahasiswa untuk menampilkan visualisasi OBE.</p>
                </div>

                <form id="hasilVisual" method="POST" action="hasilvisual-mahasiswa" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="universitas-img-path" name="universitas-img-path" value="{{ $user->universitas->logo ?? '' }}">
                    <input type="hidden" id="universitas" name="universitas" value="{{ $user->universitas->nama ?? '' }}">

                    <div class="row g-3">
                        @if ($userOtoritas == 'Penjamin Mutu Universitas')
                            <div class="col-md-4">
                                <label class="modern-label">
                                    <i class="bi bi-mortarboard text-primary me-1"></i> Program Studi <span class="text-danger">*</span>
                                </label>
                                <select id="prodiForm" class="form-control form-select modern-select" name="prodi">
                                    <option value="">Pilih Program Studi</option>
                                    @foreach (($prodi ?? $prodis ?? []) as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->jenjang }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
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
                            <div class="col-md-4">
                                <label class="modern-label">
                                    <i class="bi bi-person-badge text-primary me-1"></i> Mahasiswa <span class="text-danger">*</span>
                                </label>
                                <div class="combobox-wrapper" id="npmComboboxWrapper">
                                    <div class="combobox-input-group">
                                        <input type="text" 
                                               id="npmDisplayInput" 
                                               class="form-control modern-select combobox-input" 
                                               placeholder="Pilih NPM/Nama" 
                                               autocomplete="off"
                                               disabled>
                                        <input type="hidden" id="npm" name="npm" value="">
                                        <button type="button" class="combobox-toggle-btn" tabindex="-1" id="npmToggleBtn" title="Tampilkan daftar mahasiswa" disabled>
                                            <i class="bi bi-chevron-down"></i>
                                        </button>
                                    </div>
                                    <div class="combobox-dropdown-menu" id="npmDropdownMenu" style="display: none;">
                                        <div class="combobox-options-list" id="npmOptionsList">
                                            <div class="combobox-empty-state">Pilih Angkatan terlebih dahulu</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif ($userOtoritas == 'Penjamin Mutu Fakultas')
                            <div class="col-md-4">
                                <label class="modern-label">
                                    <i class="bi bi-mortarboard text-primary me-1"></i> Program Studi <span class="text-danger">*</span>
                                </label>
                                <select id="prodiForm" class="form-control form-select modern-select" name="prodi">
                                    <option value="">Pilih Program Studi</option>
                                    @foreach (($prodi ?? $prodis ?? []) as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->jenjang }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
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
                            <div class="col-md-4">
                                <label class="modern-label">
                                    <i class="bi bi-person-badge text-primary me-1"></i> Mahasiswa <span class="text-danger">*</span>
                                </label>
                                <div class="combobox-wrapper" id="npmComboboxWrapper">
                                    <div class="combobox-input-group">
                                        <input type="text" 
                                               id="npmDisplayInput" 
                                               class="form-control modern-select combobox-input" 
                                               placeholder="Pilih NPM/Nama" 
                                               autocomplete="off"
                                               disabled>
                                        <input type="hidden" id="npm" name="npm" value="">
                                        <button type="button" class="combobox-toggle-btn" tabindex="-1" id="npmToggleBtn" title="Tampilkan daftar mahasiswa" disabled>
                                            <i class="bi bi-chevron-down"></i>
                                        </button>
                                    </div>
                                    <div class="combobox-dropdown-menu" id="npmDropdownMenu" style="display: none;">
                                        <div class="combobox-options-list" id="npmOptionsList">
                                            <div class="combobox-empty-state">Pilih Angkatan terlebih dahulu</div>
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
                            <div class="col-md-6">
                                <label class="modern-label">
                                    <i class="bi bi-person-badge text-primary me-1"></i> Mahasiswa <span class="text-danger">*</span>
                                </label>
                                <div class="combobox-wrapper" id="npmComboboxWrapper">
                                    <div class="combobox-input-group">
                                        <input type="text" 
                                               id="npmDisplayInput" 
                                               class="form-control modern-select combobox-input" 
                                               placeholder="Pilih NPM/Nama" 
                                               autocomplete="off"
                                               disabled>
                                        <input type="hidden" id="npm" name="npm" value="">
                                        <button type="button" class="combobox-toggle-btn" tabindex="-1" id="npmToggleBtn" title="Tampilkan daftar mahasiswa" disabled>
                                            <i class="bi bi-chevron-down"></i>
                                        </button>
                                    </div>
                                    <div class="combobox-dropdown-menu" id="npmDropdownMenu" style="display: none;">
                                        <div class="combobox-options-list" id="npmOptionsList">
                                            <div class="combobox-empty-state">Pilih Angkatan terlebih dahulu</div>
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
    {{-- 2. DEDICATED RESULTS VIEW (Tampilan Hasil Visualisasi Mahasiswa) --}}
    {{-- ========================================================================= --}}
    <div id="visualContainer" style="display:none;">

        {{-- HERO PROFILE CARD (Sticky, Clean, Solid Alignment) --}}
        <div class="student-profile-hero">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <div class="student-label-tag">PROFIL MAHASISWA</div>
                    <h3 class="student-name-text" id="displayNamaMahasiswa">-</h3>
                    <div class="student-meta-row">
                        <div class="meta-item">
                            <span class="meta-label">NPM:</span>
                            <span class="meta-value" id="npmDataText">-</span>
                        </div>
                        <span class="meta-pipe">|</span>
                        <div class="meta-item">
                            <span class="meta-label">Angkatan:</span>
                            <span class="meta-value" id="angkatanDataText">-</span>
                        </div>
                        <span class="meta-pipe">|</span>
                        <div class="meta-item">
                            <span class="meta-label">Program Studi:</span>
                            <span class="meta-value" id="prodiDataText">-</span>
                        </div>
                    </div>
                </div>

                {{-- Toolbar Tombol Aksi --}}
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <button type="button" class="modern-btn-outline" id="btnBackToFilter">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-arrow-left me-1" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                        </svg>
                        Ganti Mahasiswa
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
                <i class="bi bi-exclamation-triangle-fill text-warning fs-4 me-3"></i>
                <div>
                    <h6 class="alert-heading mb-1 fw-bold text-dark">Data Penilaian Belum Tersedia</h6>
                    <p class="mb-0 text-muted small" id="emptyDataMessage">Tidak ada data penilaian/asesmen mata kuliah untuk mahasiswa yang dipilih.</p>
                </div>
            </div>
        </div>

        {{-- CARD 1: Achievement of CPL Scores (Overview) --}}
        <div class="card modern-card mb-4">
            <div class="modern-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="section-title mb-0 d-flex align-items-center flex-wrap gap-2">
                    <span><i class="bi bi-bar-chart-line"></i> Achievement of CPL Scores</span>
                    <span id="badgeYearSkorCpl" class="badge-interactive-year" style="display: none;" title="Klik untuk menghapus filter dan kembali ke tampilan awal">
                        <i class="bi bi-calendar-event"></i>
                        <span class="badge-year-text">Tahun 2024</span>
                        <i class="bi bi-x-lg badge-close-icon"></i>
                    </span>
                </h5>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#guideCplScores" aria-expanded="false" style="border-radius: 6px; font-size: 12px; padding: 4px 10px;">
                    <i class="bi bi-info-circle me-1"></i> Petunjuk Membaca Diagram
                </button>
            </div>
            <div class="card-body p-4">
                {{-- Guide Banner (Collapse) --}}
                <div class="collapse mb-3" id="guideCplScores">
                    <div class="modern-guide-box small">
                        <h6 class="fw-bold text-primary mb-1"><i class="bi bi-compass me-1"></i> Panduan Membaca Diagram & Tabel Skor CPL:</h6>
                        <ul class="mb-0 ps-3 text-muted">
                            <li><strong>Bentuk Jaring (Radar):</strong> Setiap sudut mewakili satu <strong>CPL (Capaian Pembelajaran Lulusan)</strong>.</li>
                            <li><strong>Skala Nilai (0 - 100):</strong> Titik tengah bernilai 0 dan lingkaran terluar bernilai 100. Semakin jauh titik ke arah luar, semakin tinggi skor ketercapaian kompetensi mahasiswa pada CPL tersebut.</li>
                            <li><strong>Tabel Skor Capaian CPL Per Tahun:</strong> Menampilkan skor capaian kompetensi CPL (skala 0 - 100) yang dihitung secara kumulatif untuk tiap tahun akademik sejak tahun angkatan mahasiswa. <em>Klik pada kolom tahun di tabel untuk melihat diagram pada tahun tersebut.</em></li>
                        </ul>
                    </div>
                </div>

                {{-- Diagram Canvas --}}
                <div class="d-flex justify-content-center mb-4">
                    <div style="width: 100%; max-width: 600px;">
                        <canvas id="radarChart"></canvas>
                    </div>
                </div>

                {{-- Tabel Rincian Skor Capaian CPL Per Tahun --}}
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-table text-primary me-2"></i> Rincian Skor Capaian CPL:</h6>
                    </div>
                    <div class="modern-table-container mb-4">
                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                            <table class="table modern-table mb-0" id="tableRincianCplScores">
                                <thead>
                                    <tr id="theadRincianCplScores">
                                        <th style="min-width: 120px;">Kode CPL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Dinamis via JS --}}
                                </tbody>
                                <tfoot>
                                    {{-- Dinamis summary row via JS --}}
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 2: Achievement of CPL --}}
        <div class="card modern-card mb-4">
            <div class="modern-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="section-title mb-0 d-flex align-items-center flex-wrap gap-2">
                    <span><i class="bi bi-pie-chart"></i> Achievement of CPL</span>
                    <span id="badgeYearKetercapaianCpl" class="badge-interactive-year" style="display: none;" title="Klik untuk menghapus filter dan kembali ke tampilan awal">
                        <i class="bi bi-calendar-event"></i>
                        <span class="badge-year-text">Tahun 2024</span>
                        <i class="bi bi-x-lg badge-close-icon"></i>
                    </span>
                </h5>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#guideCplCourses" aria-expanded="false" style="border-radius: 6px; font-size: 12px; padding: 4px 10px;">
                    <i class="bi bi-info-circle me-1"></i> Petunjuk Membaca Diagram
                </button>
            </div>
            <div class="card-body p-4">
                {{-- Guide Banner (Collapse) --}}
                <div class="collapse mb-3" id="guideCplCourses">
                    <div class="modern-guide-box small">
                        <h6 class="fw-bold text-primary mb-1"><i class="bi bi-compass me-1"></i> Panduan Membaca Diagram & Tabel Ketercapaian CPL (%):</h6>
                        <ul class="mb-0 ps-3 text-muted">
                            <li><span class="badge" style="background-color: #1F3BB3; color:#fff;">CPL (Mahasiswa)</span>: Persentase ketercapaian tiap CPL oleh mahasiswa.</li>
                            <li><span class="badge" style="background-color: #10b981; color:#fff;">Max CPL</span>: Pencapaian tertinggi mahasiswa pada angkatan tersebut.</li>
                            <li><span class="badge" style="background-color: #ef4444; color:#fff;">Min CPL</span>: Pencapaian terendah mahasiswa pada angkatan tersebut.</li>
                            <li><strong>Tabel Ketercapaian CPL Per Tahun (%):</strong> Menampilkan persentase kelulusan mata kuliah pendukung CPL secara kumulatif setiap tahun akademik. <em>Klik pada kolom tahun di tabel untuk melihat diagram pada tahun tersebut.</em></li>
                        </ul>
                    </div>
                </div>

                {{-- Diagram Canvas --}}
                <div class="d-flex justify-content-center mb-4">
                    <div style="width: 100%; max-width: 600px;">
                        <canvas id="radarChartCapaianCpl"></canvas>
                    </div>
                </div>

                {{-- Tabel Rincian Ketercapaian CPL (%) Per Tahun --}}
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-table text-primary me-2"></i> Rincian Ketercapaian CPL (%):</h6>
                    </div>
                    <div class="modern-table-container mb-4">
                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                            <table class="table modern-table mb-0" id="tableKomparasiCplAngkatan">
                                <thead>
                                    <tr id="theadKomparasiCplAngkatan">
                                        <th style="min-width: 120px;">Kode CPL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Dinamis via JS --}}
                                </tbody>
                                <tfoot>
                                    {{-- Dinamis summary row via JS --}}
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    {{-- Status Kelulusan Mata Kuliah --}}
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <div class="p-3 border rounded-3" style="background: #ffffff;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i> Mata Kuliah Lulus
                                    </h6>
                                </div>
                                <div class="modern-table-container">
                                    <div class="table-responsive" id="labelMkLulus" style="max-height: 280px; overflow-x: auto !important; overflow-y: auto !important;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-3 border rounded-3" style="background: #ffffff;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="keteranganTidakLulus fw-bold text-dark mb-0 d-flex align-items-center">
                                        <i class="bi bi-x-circle-fill text-danger me-2"></i> Mata Kuliah Tidak Lulus
                                    </h6>
                                </div>
                                <div class="modern-table-container">
                                    <div class="table-responsive" id="labelMkTidakLulus" style="max-height: 280px; overflow-x: auto !important; overflow-y: auto !important;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pemetaan CPL ke Mata Kuliah --}}
                    <div class="mt-4 pt-3 border-top">
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
        </div>

        {{-- CARD 3: Lihat Capaian CPMK per Mata Kuliah --}}
        <div class="card modern-card mb-4">
            <div class="modern-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="section-title mb-0">
                    <i class="bi bi-journal-check"></i> Lihat Capaian CPMK per Mata Kuliah
                </h5>
            </div>
            <div class="card-body p-4">
                <form id="hasilvisualcpmk-mahasiswa" method="POST" action="hasilvisualcpmk-mahasiswa" enctype="multipart/form-data">
                    @csrf
                    <input type="text" name="npm" class="visually-hidden" value="">
                    <input type="text" name="nama" class="visually-hidden" value="">
                    <input type="text" name="angkatan" class="visually-hidden" value="">
                    <input type="text" name="prodi" class="visually-hidden" value="">
                    <input type="text" name="universitasImg" class="visually-hidden" value="">
                    <input type="text" name="universitasCPMK" class="visually-hidden" value="">
                    <div class="d-flex flex-wrap align-items-end gap-3">
                        <div style="flex: 1; min-width: 280px; max-width: 620px;">
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

        {{-- CARD 4 & 5: Questions with Lowest CPL & All Courses Taken --}}
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card modern-card h-100">
                    <div class="modern-card-header d-flex justify-content-between align-items-center">
                        <h5 class="section-title mb-0">
                            <i class="bi bi-patch-question text-primary"></i> Questions with Lowest CPL
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">Soal-soal penilaian dengan ketercapaian CPL terendah:</p>
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
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card modern-card h-100">
                    <div class="modern-card-header d-flex justify-content-between align-items-center">
                        <h5 class="section-title mb-0">
                            <i class="bi bi-collection"></i> All Courses Taken
                        </h5>
                        <span id="allCoursesCountBadge" class="badge bg-primary text-white rounded-pill px-3 py-1 fw-bold" style="font-size: 11px;">0 MK</span>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">Seluruh mata kuliah yang telah ditempuh oleh mahasiswa:</p>
                        <div id="courseListGrid" class="course-grid-container">
                            {{-- Dinamis via JS --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 6: Career Mapping Graph Based on CPL --}}
        <div class="card modern-card mb-4">
            <div class="modern-card-header">
                <h5 class="section-title mb-0">
                    <i class="bi bi-briefcase"></i> Career Mapping Graph Based on CPL
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <div style="max-width: 500px; margin: 0 auto;">
                            <canvas id="profilChart" height="260"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-list-stars text-primary me-2"></i> Rincian Bobot Profil Lulusan & Karir:</h6>
                        <div class="modern-table-container">
                            <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                                <table id="hasilProfilTable" class="table modern-table mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 35px;" class="text-center">No</th>
                                            <th>Profil Karir</th>
                                            <th>Deskripsi</th>
                                            <th>CPL</th>
                                            <th class="text-center">Bobot</th>
                                            <th class="text-center">Hasil</th>
                                            <th class="text-center">Bobot*Hasil</th>
                                            <th class="text-center">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Script JavaScript Logic --}}
    <script>
        $(document).ready(function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Global store for student evaluation data to support interactive sub-views
            var currentGlobalNilaiMkLulus = {};

            // =========================================================================
            // =========================================================================
            // COMBOBOX CONTROLLER FOR ANGKATAN & MAHASISWA (NPM)
            // =========================================================================
            var angkatanOptionsData = [];
            var studentOptionsData = [];
            var currentLoadedAngkatan = null;
            var isStudentLoading = false;
            var activeNpmIndex = -1;
            var activeAngkatanIndex = -1;

            function parseAngkatanOptionsFromHtml(htmlString) {
                var list = [];
                var $temp = $('<div>').html(htmlString);
                $temp.find('option').each(function() {
                    var val = $(this).val();
                    var txt = $(this).text().trim();
                    if (val && val !== '') {
                        list.push({ value: String(val).trim(), text: txt });
                    }
                });
                return list;
            }

            function parseStudentOptionsFromHtml(htmlString) {
                var list = [];
                var $temp = $('<div>').html(htmlString);
                $temp.find('option').each(function() {
                    var val = $(this).val();
                    var rawTxt = $(this).text().trim();
                    if (val && val !== '') {
                        var cleanVal = String(val).trim();
                        var npm = cleanVal;
                        var nama = rawTxt;
                        if (rawTxt.indexOf('-') !== -1) {
                            var parts = rawTxt.split('-');
                            if (parts.length >= 2) {
                                npm = parts[0].trim();
                                nama = parts.slice(1).join('-').trim();
                            }
                        }
                        var displayText = npm + ' - ' + (nama || 'N/A');
                        list.push({
                            value: cleanVal,
                            npm: npm,
                            nama: nama,
                            text: displayText
                        });
                    }
                });
                return list;
            }

            // ---------------- ANGKATAN COMBOBOX ----------------
            function renderAngkatanOptions(filterText) {
                var $list = $('#angkatanOptionsList');
                $list.empty();
                activeAngkatanIndex = -1;

                if (!angkatanOptionsData || angkatanOptionsData.length === 0) {
                    $list.html('<div class="combobox-empty-state">Tidak ada data angkatan</div>');
                    return;
                }

                var query = (filterText || '').toLowerCase().trim();
                var filtered = angkatanOptionsData.filter(function(item) {
                    if (!query) return true;
                    return item.text.toLowerCase().indexOf(query) !== -1 || item.value.toLowerCase().indexOf(query) !== -1;
                });

                if (filtered.length === 0) {
                    $list.html('<div class="combobox-empty-state">Tidak ada angkatan yang cocok dengan "' + filterText + '"</div>');
                    return;
                }

                var currentVal = $('#angkatanForm').val();

                filtered.forEach(function(item, idx) {
                    var isSelected = (item.value === currentVal);
                    var $opt = $('<div>')
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
                onAngkatanSelected(val);
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
                    disableNpmCombobox();
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
                    if (currentLoadedAngkatan !== match.value) {
                        onAngkatanSelected(match.value);
                    }
                } else if (currentVal) {
                    var prevFound = angkatanOptionsData.find(function(s) { return s.value === currentVal; });
                    if (prevFound) {
                        $('#angkatanDisplayInput').val(prevFound.text);
                    }
                } else {
                    $('#angkatanDisplayInput').val('');
                    $('#angkatanForm').val('');
                    disableNpmCombobox();
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
                    disableNpmCombobox();
                    return;
                }

                var exactMatch = angkatanOptionsData.find(function(item) {
                    return item.value.toLowerCase() === query.toLowerCase() || item.text.toLowerCase() === query.toLowerCase();
                });

                if (exactMatch) {
                    $('#angkatanForm').val(exactMatch.value);
                    if (currentLoadedAngkatan !== exactMatch.value) {
                        onAngkatanSelected(exactMatch.value);
                    }
                } else if (/^\d{4}$/.test(query)) {
                    $('#angkatanForm').val(query);
                    if (currentLoadedAngkatan !== query) {
                        onAngkatanSelected(query);
                    }
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

            // ---------------- NPM (MAHASISWA) COMBOBOX ----------------
            function disableNpmCombobox() {
                $('#npm').val('');
                $('#npmDisplayInput').val('').prop('disabled', true);
                $('#npmToggleBtn').prop('disabled', true);
                studentOptionsData = [];
                currentLoadedAngkatan = null;
                isStudentLoading = false;
                $('#npmOptionsList').html('<div class="combobox-empty-state">Pilih Angkatan terlebih dahulu</div>');
                $('#npmDropdownMenu').hide();
                $('#npmComboboxWrapper').removeClass('is-open');
            }

            function enableNpmCombobox() {
                $('#npmDisplayInput').prop('disabled', false);
                $('#npmToggleBtn').prop('disabled', false);
            }

            function renderNpmOptions(filterText) {
                var $list = $('#npmOptionsList');
                $list.empty();
                activeNpmIndex = -1;

                if (isStudentLoading) {
                    $list.html('<div class="combobox-empty-state"><span class="spinner-border spinner-border-sm me-2 text-primary"></span>Memuat daftar mahasiswa...</div>');
                    return;
                }

                if (!studentOptionsData || studentOptionsData.length === 0) {
                    $list.html('<div class="combobox-empty-state">Tidak ada data mahasiswa pada angkatan ini</div>');
                    return;
                }

                var query = (filterText || '').toLowerCase().trim();
                var filtered = studentOptionsData.filter(function(item) {
                    if (!query) return true;
                    return (item.npm && item.npm.toLowerCase().indexOf(query) !== -1) ||
                           (item.nama && item.nama.toLowerCase().indexOf(query) !== -1) ||
                           (item.text && item.text.toLowerCase().indexOf(query) !== -1);
                });

                if (filtered.length === 0) {
                    $list.html('<div class="combobox-empty-state">Tidak ada mahasiswa yang cocok dengan "' + filterText + '"</div>');
                    return;
                }

                var currentVal = $('#npm').val();

                filtered.forEach(function(item, idx) {
                    var isSelected = (item.value === currentVal);
                    var $opt = $('<div>')
                        .addClass('combobox-option' + (isSelected ? ' is-selected' : ''))
                        .attr('data-value', item.value)
                        .attr('data-index', idx)
                        .attr('title', item.text)
                        .html('<span class="font-monospace fw-semibold me-2">' + item.npm + '</span><span class="text-secondary">-</span> <span class="ms-1">' + item.nama + '</span>');

                    $opt.on('mousedown', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        selectNpmItem(item.value, item.text);
                    });

                    $list.append($opt);
                });
            }

            function selectNpmItem(val, text) {
                $('#npm').val(val);
                $('#npmDisplayInput').val(text).attr('title', text);
                $('#npmDropdownMenu').hide();
                $('#npmComboboxWrapper').removeClass('is-open');
                activeNpmIndex = -1;
            }

            function openNpmCombobox() {
                var currentAngkatan = $('#angkatanForm').val();
                if (!currentAngkatan) {
                    var typedAngkatan = $('#angkatanDisplayInput').val().trim();
                    if (typedAngkatan) {
                        var match = angkatanOptionsData.find(function(s) {
                            return s.value.toLowerCase() === typedAngkatan.toLowerCase() ||
                                   s.text.toLowerCase() === typedAngkatan.toLowerCase();
                        });
                        if (match) {
                            $('#angkatanForm').val(match.value);
                            currentAngkatan = match.value;
                        } else if (/^\d{4}$/.test(typedAngkatan)) {
                            $('#angkatanForm').val(typedAngkatan);
                            currentAngkatan = typedAngkatan;
                        }
                    }
                }

                if (!currentAngkatan) {
                    $('#npmOptionsList').html('<div class="combobox-empty-state">Pilih Angkatan terlebih dahulu</div>');
                    $('#npmComboboxWrapper').addClass('is-open');
                    $('#npmDropdownMenu').show();
                    return;
                }

                enableNpmCombobox();

                if (currentLoadedAngkatan !== currentAngkatan || (!studentOptionsData || studentOptionsData.length === 0)) {
                    if (!isStudentLoading) {
                        onAngkatanSelected(currentAngkatan, true);
                    }
                    $('#npmComboboxWrapper').addClass('is-open');
                    $('#npmDropdownMenu').show();
                    return;
                }

                var currentQuery = $('#npmDisplayInput').val().trim();
                var currentVal = $('#npm').val();
                var currentSelectedObj = studentOptionsData.find(function(s) { return s.value === currentVal; });

                if (currentSelectedObj && (currentSelectedObj.text === currentQuery || currentSelectedObj.value === currentQuery || currentSelectedObj.npm === currentQuery)) {
                    renderNpmOptions('');
                } else {
                    renderNpmOptions(currentQuery);
                }

                $('#npmComboboxWrapper').addClass('is-open');
                $('#npmDropdownMenu').show();
            }

            function closeNpmCombobox() {
                $('#npmDropdownMenu').hide();
                $('#npmComboboxWrapper').removeClass('is-open');
                activeNpmIndex = -1;

                var currentVal = $('#npm').val();
                var currentText = $('#npmDisplayInput').val().trim();

                if (currentVal) {
                    var found = studentOptionsData.find(function(s) { return s.value === currentVal; });
                    if (found) {
                        $('#npmDisplayInput').val(found.text);
                        return;
                    }
                }

                if (currentText && studentOptionsData.length > 0) {
                    var lowerText = currentText.toLowerCase();
                    var exactNpm = studentOptionsData.find(function(s) { return s.npm.toLowerCase() === lowerText; });
                    if (exactNpm) {
                        selectNpmItem(exactNpm.value, exactNpm.text);
                        return;
                    }
                    var exactText = studentOptionsData.find(function(s) { return s.text.toLowerCase() === lowerText; });
                    if (exactText) {
                        selectNpmItem(exactText.value, exactText.text);
                        return;
                    }
                    var exactNama = studentOptionsData.find(function(s) { return s.nama.toLowerCase() === lowerText; });
                    if (exactNama) {
                        selectNpmItem(exactNama.value, exactNama.text);
                        return;
                    }
                } else if (!currentText) {
                    $('#npm').val('');
                    $('#npmDisplayInput').val('');
                }
            }

            $('#npmDisplayInput').on('focus', function() {
                $(this).select();
                openNpmCombobox();
            });

            $('#npmDisplayInput').on('click', function(e) {
                openNpmCombobox();
            });

            $('#npmDisplayInput').on('input', function() {
                var query = $(this).val();
                var lowerQuery = query.toLowerCase().trim();
                
                var exactMatch = studentOptionsData.find(function(s) {
                    return s.npm.toLowerCase() === lowerQuery || s.text.toLowerCase() === lowerQuery;
                });

                if (exactMatch) {
                    $('#npm').val(exactMatch.value);
                } else {
                    $('#npm').val('');
                }

                renderNpmOptions(query);
                $('#npmComboboxWrapper').addClass('is-open');
                $('#npmDropdownMenu').show();
            });

            $('#npmToggleBtn').on('click', function(e) {
                e.preventDefault();
                if ($('#npmDropdownMenu').is(':visible')) {
                    closeNpmCombobox();
                } else {
                    $('#npmDisplayInput').focus();
                    openNpmCombobox();
                }
            });

            $('#npmDisplayInput').on('keydown', function(e) {
                var $options = $('#npmOptionsList .combobox-option');
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (!$('#npmDropdownMenu').is(':visible')) {
                        openNpmCombobox();
                        return;
                    }
                    if (!$options.length) return;
                    activeNpmIndex++;
                    if (activeNpmIndex >= $options.length) activeNpmIndex = 0;
                    $options.removeClass('is-focused');
                    var $active = $options.eq(activeNpmIndex).addClass('is-focused');
                    if ($active.length && $active[0].scrollIntoView) {
                        $active[0].scrollIntoView({ block: 'nearest' });
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (!$('#npmDropdownMenu').is(':visible')) {
                        openNpmCombobox();
                        return;
                    }
                    if (!$options.length) return;
                    activeNpmIndex--;
                    if (activeNpmIndex < 0) activeNpmIndex = $options.length - 1;
                    $options.removeClass('is-focused');
                    var $active = $options.eq(activeNpmIndex).addClass('is-focused');
                    if ($active.length && $active[0].scrollIntoView) {
                        $active[0].scrollIntoView({ block: 'nearest' });
                    }
                } else if (e.key === 'Enter') {
                    if ($('#npmDropdownMenu').is(':visible') && $options.length > 0) {
                        e.preventDefault();
                        if (activeNpmIndex >= 0 && activeNpmIndex < $options.length) {
                            var selectedVal = $options.eq(activeNpmIndex).attr('data-value');
                            var selectedText = $options.eq(activeNpmIndex).text();
                            selectNpmItem(selectedVal, selectedText);
                        } else {
                            var selectedVal = $options.eq(0).attr('data-value');
                            var selectedText = $options.eq(0).text();
                            selectNpmItem(selectedVal, selectedText);
                        }
                    }
                } else if (e.key === 'Escape') {
                    closeNpmCombobox();
                }
            });

            // ---------------- COURSE CPMK COMBOBOX ----------------
            var courseOptionsData = [];
            var activeCourseIndex = -1;

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
                    var $opt = $('<div>')
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

            $('#hasilvisualcpmk-mahasiswa').on('submit', function(e) {
                var courseVal = $('#courseSelect').val();
                var textVal = $('#courseDisplayInput').val().trim();
                if (!courseVal && textVal && courseOptionsData.length > 0) {
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
                        courseVal = match.value;
                    }
                }

                if (!courseVal) {
                    e.preventDefault();
                    alert('Mohon pilih Mata Kuliah terlebih dahulu.');
                    $('#courseDisplayInput').focus();
                    return;
                }
            });

            // Close on click outside for all comboboxes
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#angkatanComboboxWrapper').length) {
                    closeAngkatanCombobox();
                }
                if (!$(e.target).closest('#npmComboboxWrapper').length) {
                    closeNpmCombobox();
                }
                if (!$(e.target).closest('#courseComboboxWrapper').length) {
                    closeCourseCombobox();
                }
            });

            function onAngkatanSelected(angkatan, autoOpenNpm) {
                var universitas = $('#universitas').val();
                var prodi = $('#prodiForm').val() || "{{ auth()->user()->id_prodiUser ?? '' }}";
                
                angkatan = (angkatan || '').toString().trim();
                if (!angkatan) {
                    currentLoadedAngkatan = null;
                    disableNpmCombobox();
                    return;
                }

                enableNpmCombobox();

                if (currentLoadedAngkatan === angkatan && studentOptionsData.length > 0 && !isStudentLoading) {
                    renderNpmOptions($('#npmDisplayInput').val().trim());
                    if (autoOpenNpm || $('#npmDisplayInput').is(':focus') || $('#npmComboboxWrapper').hasClass('is-open')) {
                        $('#npmComboboxWrapper').addClass('is-open');
                        $('#npmDropdownMenu').show();
                    }
                    return;
                }

                if (isStudentLoading && currentLoadedAngkatan === angkatan) {
                    return;
                }

                currentLoadedAngkatan = angkatan;
                isStudentLoading = true;
                $('#npm').val('');
                $('#npmDisplayInput').val('');
                studentOptionsData = [];

                $('#npmOptionsList').html('<div class="combobox-empty-state"><span class="spinner-border spinner-border-sm me-2 text-primary"></span>Memuat daftar mahasiswa...</div>');

                $.ajax({
                    url: "{{ route($currentPrefix . 'visualisasi.getNpmByAngkatan') }}",
                    method: 'GET',
                    data: {
                        angkatan: angkatan,
                        universitas: universitas,
                        prodi: prodi
                    },
                    success: function(data) {
                        isStudentLoading = false;
                        studentOptionsData = parseStudentOptionsFromHtml(data);
                        renderNpmOptions($('#npmDisplayInput').val().trim());
                        if (autoOpenNpm || $('#npmDisplayInput').is(':focus') || $('#npmComboboxWrapper').hasClass('is-open')) {
                            $('#npmComboboxWrapper').addClass('is-open');
                            $('#npmDropdownMenu').show();
                        }
                    },
                    error: function() {
                        isStudentLoading = false;
                        studentOptionsData = [];
                        $('#npmOptionsList').html('<div class="combobox-empty-state text-danger">Gagal memuat data mahasiswa</div>');
                    }
                });
            }

            function loadAngkatan(prodi, universitas, callback) {
                angkatanOptionsData = [];
                $('#angkatanOptionsList').html('<div class="combobox-empty-state">Memuat data angkatan...</div>');

                if (!prodi) {
                    $('#angkatanForm').val('');
                    $('#angkatanDisplayInput').val('');
                    disableNpmCombobox();
                    $('#angkatanOptionsList').html('<div class="combobox-empty-state">Pilih Program Studi terlebih dahulu</div>');
                    return;
                }
                $.ajax({
                    url: "{{ route($currentPrefix . 'visualisasi.getAngkatanByProdiUniversitas') }}",
                    method: 'GET',
                    data: {
                        prodi: prodi,
                        universitas: universitas
                    },
                    success: function(data) {
                        angkatanOptionsData = parseAngkatanOptionsFromHtml(data);
                        renderAngkatanOptions($('#angkatanDisplayInput').val().trim());
                        if (typeof callback === 'function') {
                            callback();
                        }
                    },
                    error: function() {
                        $('#angkatanOptionsList').html('<div class="combobox-empty-state text-danger">Gagal memuat data angkatan</div>');
                    }
                });
            }

            $('#prodiForm').on('change', function() {
                var prodi = $(this).val();
                var universitas = $('#universitas').val();
                $('#angkatanForm').val('');
                $('#angkatanDisplayInput').val('');
                disableNpmCombobox();
                loadAngkatan(prodi, universitas);
            });

            @if ($userOtoritas == 'Kepala Program Studi' || $userOtoritas == 'Penjamin Mutu Program Studi' || $userOtoritas == 'Dosen')
                var userProdi = "{{ auth()->user()->id_prodiUser ?? '' }}";
                if (userProdi) {
                    var universitas = $('#universitas').val();
                    loadAngkatan(userProdi, universitas);
                }
            @endif

            // Pemetaan CPL Change (Sesuai Visualisasi Per Angkatan)
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

            // INSTANCE CHART UNTUK DESTROY
            var radarChartPerSoalInstance = null;
            var radarChartCapaianCplInstance = null;
            var profilChartInstance = null;

            function fetchAndRenderMahasiswa(prodi, angkatan, npm, isAutoLoad) {
                var $btnSubmit = $('#btnSubmitVisual');
                var originalBtnHtml = $btnSubmit.html();
                if (!isAutoLoad) {
                    $btnSubmit.prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memuat Data...'
                    );
                }

                var dataObj = {
                    prodi: prodi,
                    angkatan: angkatan,
                    npm: npm,
                    _token: "{{ csrf_token() }}"
                };

                var imgSrc = $('#universitas-img-path').val();
                if (imgSrc) {
                    dataObj.imgSrc = imgSrc;
                }

                $.ajax({
                    url: "{{ route($currentPrefix . 'visualisasi.hasilvisual-mahasiswa') }}",
                    method: 'POST',
                    data: dataObj,
                    dataType: 'json',
                    success: function(response) {
                        if (!isAutoLoad) {
                            $btnSubmit.prop('disabled', false).html(originalBtnHtml);
                        }
                        if (response.success && response.result) {
                            // Simpan state aktif ke sessionStorage
                            sessionStorage.setItem('active_mhs_npm', npm);
                            sessionStorage.setItem('active_mhs_angkatan', angkatan);
                            if (prodi) sessionStorage.setItem('active_mhs_prodi', prodi);

                            // Update URL query parameters tanpa reload
                            var currentUrl = new URL(window.location.href);
                            currentUrl.searchParams.set('npm', npm);
                            currentUrl.searchParams.set('angkatan', angkatan);
                            if (prodi) currentUrl.searchParams.set('prodi', prodi);
                            window.history.replaceState({}, '', currentUrl.toString());

                            renderVisualisasiData(response);

                            if (isAutoLoad) {
                                $('#tugas').hide();
                                $('#visualContainer').show();
                            } else {
                                $('#tugas').slideUp(300, function() {
                                    $('#visualContainer').fadeIn(350);
                                    $('html, body').animate({
                                        scrollTop: $('#visualContainer').offset().top - 70
                                    }, 300);
                                });
                            }
                        } else {
                            if (isAutoLoad) {
                                $('#visualContainer').hide();
                                $('#tugas').show();
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        if (!isAutoLoad) {
                            $btnSubmit.prop('disabled', false).html(originalBtnHtml);
                            alert('Terjadi kesalahan saat mengambil data evaluasi: ' + (xhr.responseJSON ? xhr.responseJSON.message : error));
                        } else {
                            $('#visualContainer').hide();
                            $('#tugas').show();
                        }
                    }
                });
            }

            // Inisialisasi: Cek parameter URL (mendukung refresh F5 pada halaman output)
            var urlParams = new URLSearchParams(window.location.search);
            var savedNpm = urlParams.get('npm');
            var savedAngkatan = urlParams.get('angkatan');
            var savedProdi = urlParams.get('prodi') || $('#prodiForm').val() || "{{ auth()->user()->id_prodiUser ?? '' }}";

            if (savedNpm && savedAngkatan) {
                $('#tugas').hide();
                $('#visualContainer').show();
                if (savedProdi) $('#prodiForm').val(savedProdi);
                if (savedAngkatan) {
                    $('#angkatanForm').val(savedAngkatan);
                    $('#angkatanDisplayInput').val(savedAngkatan);
                }
                if (savedNpm) {
                    $('#npm').val(savedNpm);
                }
                fetchAndRenderMahasiswa(savedProdi, savedAngkatan, savedNpm, true);
            } else {
                $('#antiFlickerStyle').remove();
                $('#tugas').show();
                $('#visualContainer').hide();
            }

            // TOMBOL KEMBALI / GANTI MAHASISWA
            $('#btnBackToFilter').on('click', function() {
                sessionStorage.removeItem('active_mhs_npm');
                sessionStorage.removeItem('active_mhs_angkatan');
                sessionStorage.removeItem('active_mhs_prodi');
                $('#antiFlickerStyle').remove();

                var cleanUrl = new URL(window.location.href);
                cleanUrl.searchParams.delete('npm');
                cleanUrl.searchParams.delete('angkatan');
                cleanUrl.searchParams.delete('prodi');
                window.history.replaceState({}, '', cleanUrl.toString());

                // Reset field mahasiswa agar bersih dan siap memilih mahasiswa baru
                $('#npm').val('');
                $('#npmDisplayInput').val('');

                $('#visualContainer').fadeOut(250, function() {
                    $('#tugas').slideDown(300);

                    var curAngkatan = $('#angkatanForm').val() || $('#angkatanDisplayInput').val().trim();
                    if (curAngkatan) {
                        enableNpmCombobox();
                        if (!studentOptionsData || studentOptionsData.length === 0 || currentLoadedAngkatan !== curAngkatan) {
                            onAngkatanSelected(curAngkatan);
                        } else {
                            renderNpmOptions('');
                        }
                    }

                    $('html, body').animate({
                        scrollTop: $('#tugas').offset().top - 70
                    }, 300);
                });
            });

            // AJAX FORM SUBMIT
            $('#hasilVisual').on('submit', function(e) {
                e.preventDefault();

                var prodi = $('#prodiForm').val() || "{{ auth()->user()->id_prodiUser ?? '' }}";
                var angkatan = $('#angkatanForm').val();
                var npm = $('#npm').val();

                // Auto-resolve typed angkatan if hidden input empty
                if (!angkatan && $('#angkatanDisplayInput').val().trim()) {
                    var typedAng = $('#angkatanDisplayInput').val().trim();
                    var matchA = angkatanOptionsData.find(function(a) {
                        return a.value.toLowerCase() === typedAng.toLowerCase() || a.text.toLowerCase() === typedAng.toLowerCase();
                    });
                    if (matchA) {
                        angkatan = matchA.value;
                        $('#angkatanForm').val(angkatan);
                    } else if (/^\d{4}$/.test(typedAng)) {
                        angkatan = typedAng;
                        $('#angkatanForm').val(angkatan);
                    }
                }

                // Auto-resolve typed mahasiswa if hidden input empty
                if (!npm && $('#npmDisplayInput').val().trim() && studentOptionsData.length > 0) {
                    var typedMhs = $('#npmDisplayInput').val().trim().toLowerCase();
                    var matchM = studentOptionsData.find(function(s) {
                        return s.npm.toLowerCase() === typedMhs;
                    });
                    if (!matchM) {
                        matchM = studentOptionsData.find(function(s) {
                            return s.text.toLowerCase() === typedMhs;
                        });
                    }
                    if (!matchM) {
                        matchM = studentOptionsData.find(function(s) {
                            return s.nama.toLowerCase() === typedMhs;
                        });
                    }
                    if (!matchM) {
                        matchM = studentOptionsData.find(function(s) {
                            return s.npm.toLowerCase().startsWith(typedMhs) || s.nama.toLowerCase().startsWith(typedMhs);
                        });
                    }
                    if (!matchM) {
                        matchM = studentOptionsData.find(function(s) {
                            return s.text.toLowerCase().indexOf(typedMhs) !== -1;
                        });
                    }
                    if (matchM) {
                        npm = matchM.value;
                        $('#npm').val(npm);
                        $('#npmDisplayInput').val(matchM.text);
                    }
                }

                if (!prodi) {
                    alert('Silakan pilih Program Studi terlebih dahulu.');
                    $('#prodiForm').focus();
                    return;
                }
                if (!angkatan) {
                    alert('Silakan pilih Angkatan terlebih dahulu.');
                    $('#angkatanDisplayInput').focus();
                    return;
                }
                if (!npm) {
                    alert('Silakan pilih Mahasiswa terlebih dahulu.');
                    $('#npmDisplayInput').focus();
                    return;
                }

                fetchAndRenderMahasiswa(prodi, angkatan, npm, false);
            });

            function renderVisualisasiData(response) {
                var labelCpl = response.result.labelCpl || [];
                var soalTerendah = response.result.soalTerendah || [];

                var nama = response.result.nama || '-';
                var npm = response.result.npm || '-';
                var angkatan = response.result.angkatan || '-';
                var imgSrc = response.result.imgSrc || $('#universitas-img-path').val();
                var prodi = response.result.prodi || '-';
                var universitas = response.result.universitas || '-';
                var nilaiMkLulus = response.result.nilaiMkLulus || {};
                var nilaiMkTidakLulus = response.result.nilaiMkTidakLulus || {};
                var cplResultsAll = response.result.cplResultsAll || [];
                var cplTmp = response.result.cplTmp || {};
                var allCplPerAngkatan = response.result.allCplPerAngkatan || {};
                var cplPerSoalWithKodeAll = response.result.cplPerSoalWithKodeAll || [];
                var hasilFinalProfil = response.result.hasilFinalProfil || [];
                var chartDataProfil = response.result.chartDataProfil || [];
                var courseArray = response.result.courseArray || {};
                var courseCpmkRestrict = response.result.courseCpmkRestrict || {};
                var hasData = response.result.has_data;

                // Store for interactive sub-views (like Pemetaan CPL)
                currentGlobalNilaiMkLulus = nilaiMkLulus;

                // Data Tahun & Masa Studi
                var yearsList = response.result.yearsList || [];
                var yearsWithData = response.result.yearsWithData || {};
                var skorCplPerTahun = response.result.skorCplPerTahun || {};
                var ketercapaianCplPerTahun = response.result.ketercapaianCplPerTahun || {};
                var skorCplOverall = response.result.skorCplOverall || {};
                var ketercapaianCplOverall = response.result.ketercapaianCplOverall || {};
                var skorCplYearlyAvg = response.result.skorCplYearlyAvg || {};
                var ketercapaianCplYearlyAvg = response.result.ketercapaianCplYearlyAvg || {};
                var skorCplGrandAvg = response.result.skorCplGrandAvg || 0;
                var ketercapaianCplGrandAvg = response.result.ketercapaianCplGrandAvg || 0;

                // Header Info Mahasiswa
                $('#displayNamaMahasiswa').text(nama);
                $('#npmDataText').text(npm);
                $('#angkatanDataText').text(angkatan);
                $('#prodiDataText').text(prodi);

                // Handle Empty Data Notice Banner
                if (!hasData) {
                    $('#emptyDataMessage').html('Tidak ada data penilaian/asesmen mata kuliah untuk mahasiswa <strong>' + nama + ' (' + npm + ')</strong>.');
                    $('#emptyDataAlert').slideDown();
                } else {
                    $('#emptyDataAlert').slideUp();
                }

                // Set input hidden CPMK (scoping khusus form CPMK agar tidak mengotori form filter utama)
                $('#hasilvisualcpmk-mahasiswa input[name="npm"]').val(npm);
                $('#hasilvisualcpmk-mahasiswa input[name="nama"]').val(nama);
                $('#hasilvisualcpmk-mahasiswa input[name="angkatan"]').val(angkatan);
                $('#hasilvisualcpmk-mahasiswa input[name="prodi"]').val(prodi);
                $('#hasilvisualcpmk-mahasiswa input[name="universitasImg"]').val(imgSrc);
                $('#hasilvisualcpmk-mahasiswa input[name="universitasCPMK"]').val(universitas);

                // Profil Chart & Table
                if (profilChartInstance) {
                    profilChartInstance.destroy();
                    profilChartInstance = null;
                }

                $('#hasilProfilTable tbody').empty();
                var nomor = 1;
                $.each(hasilFinalProfil, function(key, profil) {
                    var totalRows = profil.CPLs.length;
                    var row = '<tr>';
                    row += '<td rowspan="' + totalRows + '" class="text-center text-muted">' + nomor + '</td>';
                    row += '<td rowspan="' + totalRows + '" class="fw-semibold text-dark">' + profil.NamaProfil + '</td>';
                    row += '<td rowspan="' + totalRows + '" class="wrap-content text-secondary small">' + profil.Deskripsi + '</td>';

                    $.each(profil.CPLs, function(index, cpl) {
                        row += '<td class="fw-medium text-dark">' + cpl.CPL + '</td>';
                        row += '<td class="text-center text-secondary">' + cpl.Bobot + '</td>';
                        row += '<td class="text-center text-secondary">' + cpl.HasilCPL + '</td>';
                        row += '<td class="text-center fw-medium text-dark">' + (cpl.Bobot * cpl.HasilCPL).toFixed(3) + '</td>';

                        if (index === 0) {
                            row += '<td rowspan="' + totalRows + '" class="text-center fw-bold text-primary" style="font-size:14px;">' + profil.TotalAkhir.toFixed(3) + '</td>';
                        }
                        row += '</tr>';
                        if (index < totalRows - 1) {
                            row += '<tr>';
                        }
                    });
                    nomor++;
                    $('#hasilProfilTable tbody').append(row);
                });

                var labels = chartDataProfil.map(p => p.label);
                var data = chartDataProfil.map(p => p.data);
                var backgroundColors = chartDataProfil.map((_, index) => {
                    var colors = ['#1F3BB3', '#0284c7', '#059669', '#d97706', '#7c3aed', '#db2777'];
                    return colors[index % colors.length];
                });
                var ctx = document.getElementById('profilChart').getContext('2d');
                profilChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Persentase (%)',
                            data: data,
                            backgroundColor: backgroundColors,
                            borderColor: backgroundColors,
                            borderWidth: 1,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Helper format nilai (hanya angka)
                function formatNilaiBadge(rawScore, isLulus) {
                    var num = parseFloat(rawScore);
                    if (isNaN(num)) return '-';
                    var valStr = (Math.round(num * 100) / 100).toFixed(2);

                    if (isLulus) {
                        return '<span class="badge-grade-success">' + valStr + '</span>';
                    } else {
                        return '<span class="badge-grade-danger">' + valStr + '</span>';
                    }
                }

                // Helper extract score and course name
                function extractCourseData(courseCode, dataArr) {
                    var scoreVal = '-';
                    var courseName = courseCode;
                    if (Array.isArray(dataArr)) {
                        scoreVal = dataArr[0] !== undefined ? dataArr[0] : '-';
                        courseName = dataArr[1] !== undefined ? dataArr[1] : courseCode;
                    } else if (typeof dataArr === 'object' && dataArr !== null) {
                        scoreVal = dataArr.nilai !== undefined ? dataArr.nilai : (dataArr[0] !== undefined ? dataArr[0] : '-');
                        courseName = dataArr.nama !== undefined ? dataArr.nama : (dataArr[1] !== undefined ? dataArr[1] : courseCode);
                    } else if (dataArr !== undefined && dataArr !== null) {
                        scoreVal = dataArr;
                        courseName = courseCode;
                    }
                    return { score: scoreVal, name: courseName };
                }

                // LABEL MK LULUS & TIDAK LULUS (Dengan Kolom Nilai)
                $('#labelMkLulus').html(
                    '<table class="table modern-table mb-0" style="min-width: 440px;"><thead><tr><th width="45px" class="text-center">#</th><th width="110px" class="text-center">Kode MK</th><th>Nama Mata Kuliah</th><th width="120px" class="text-center" style="white-space:nowrap;">Nilai</th></tr></thead><tbody></tbody></table>'
                );

                if (!nilaiMkLulus || Object.keys(nilaiMkLulus).length === 0) {
                    $('#labelMkLulus table tbody').append(
                        '<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada mata kuliah lulus</td></tr>'
                    );
                } else {
                    var indeksMK = 1;
                    $.each(nilaiMkLulus, function(courseCode, dataArr) {
                        var cd = extractCourseData(courseCode, dataArr);
                        $('#labelMkLulus table tbody').append('<tr>' +
                            '<td class="text-center text-muted">' + indeksMK + '</td>' +
                            '<td class="text-center fw-medium text-secondary small font-monospace">' + courseCode + '</td>' +
                            '<td class="fw-medium text-dark">' + cd.name + '</td>' +
                            '<td class="text-center">' + formatNilaiBadge(cd.score, true) + '</td>' +
                            '</tr>');
                        indeksMK += 1;
                    });
                }

                $('#labelMkTidakLulus').html(
                    '<table class="table modern-table mb-0" style="min-width: 440px;"><thead><tr><th width="45px" class="text-center">#</th><th width="110px" class="text-center">Kode MK</th><th>Nama Mata Kuliah</th><th width="120px" class="text-center" style="white-space:nowrap;">Nilai</th></tr></thead><tbody></tbody></table>'
                );

                if (!nilaiMkTidakLulus || Object.keys(nilaiMkTidakLulus).length === 0) {
                    $('#labelMkTidakLulus table tbody').append(
                        '<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada mata kuliah tidak lulus</td></tr>'
                    );
                } else {
                    var indeksMK = 1;
                    $.each(nilaiMkTidakLulus, function(courseCode, dataArr) {
                        var cd = extractCourseData(courseCode, dataArr);
                        $('#labelMkTidakLulus table tbody').append('<tr>' +
                            '<td class="text-center text-muted">' + indeksMK + '</td>' +
                            '<td class="text-center fw-medium text-secondary small font-monospace">' + courseCode + '</td>' +
                            '<td class="fw-medium text-dark">' + cd.name + '</td>' +
                            '<td class="text-center">' + formatNilaiBadge(cd.score, false) + '</td>' +
                            '</tr>');
                        indeksMK += 1;
                    });
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

                // Reset Konten Pemetaan CPL
                $('#kontenPemetaanCpl').html('<span class="text-muted small">Silahkan pilih salah satu CPL di atas untuk melihat daftar mata kuliah pendukung.</span>');

                // Label Container Descriptions (Structured Compact Cards Grid)
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

                $('#collapseCplDescriptions').hide();
                $('#iconCplDescCollapse').removeClass('bi-chevron-up').addClass('bi-chevron-down');

                // Soal Terendah Table
                var tableBody = $('#soalTerendahTable tbody');
                tableBody.html('');
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
                        tableBody.append(rowHtml);
                    }
                } else {
                    tableBody.append('<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada data soal</td></tr>');
                }

                // 5. ALL COURSES TAKEN (Modern Card Grid Layout - Tanpa Nilai)
                var courseCount = Object.keys(courseArray).length;
                $('#allCoursesCountBadge').text(courseCount + ' MK');
                var $courseGrid = $('#courseListGrid');
                $courseGrid.empty();

                if (courseCount === 0) {
                    $courseGrid.html('<div class="p-3 text-center text-muted small w-100">Belum ada mata kuliah yang ditempuh.</div>');
                } else {
                    for (var course in courseArray) {
                        var cardHtml = '<div class="course-card-item">' +
                            '<div class="d-flex align-items-center gap-2 overflow-hidden">' +
                                '<span class="badge bg-primary text-white font-monospace" style="font-size:11px; padding:4px 7px;">' + course + '</span>' +
                                '<span class="fw-semibold text-dark text-truncate small" title="' + courseArray[course] + '">' + courseArray[course] + '</span>' +
                            '</div>' +
                        '</div>';
                        $courseGrid.append(cardHtml);
                    }
                }

                // 6. POPULATE COURSE CPMK COMBOBOX
                courseOptionsData = [];
                for (var course in courseCpmkRestrict) {
                    courseOptionsData.push({
                        code: course,
                        name: courseCpmkRestrict[course],
                        value: course + '-' + courseCpmkRestrict[course],
                        text: course + ' - ' + courseCpmkRestrict[course]
                    });
                }
                $('#courseSelect').val('');
                $('#courseDisplayInput').val('');
                renderCourseOptions('');

                function roundToTwo(num) {
                    return Math.round(num * 100) / 100;
                }

                // State Filter Tahun untuk Diagram (Default: Tahun Terakhir yang Ada Data)
                var defaultYear = response.result.lastActiveYear || (yearsList.length > 0 ? (yearsList.slice().reverse().find(yr => yearsWithData[yr] !== false) || yearsList[0]) : null);
                var activeYearSkorCpl = defaultYear;
                var activeYearKetercapaianCpl = defaultYear;

                // 1. POPULATE TABEL SKOR CAPAIAN CPL PER TAHUN
                var $theadRincian = $('#theadRincianCplScores');
                $theadRincian.empty();
                $theadRincian.append('<th style="min-width: 120px;">Kode CPL</th>');
                $.each(yearsList, function(idx, yr) {
                    var hasData = yearsWithData[yr] !== undefined ? yearsWithData[yr] : true;
                    if (hasData) {
                        $theadRincian.append('<th class="text-center interactive-year-th" data-year="' + yr + '" style="min-width: 80px;" title="Klik untuk melihat diagram tahun ' + yr + '">' + yr + '</th>');
                    } else {
                        $theadRincian.append('<th class="text-center interactive-year-th text-muted bg-light" data-year="' + yr + '" style="min-width: 80px; color: #94a3b8 !important;" title="Belum Ditempuh (Klik untuk melihat diagram tahun ' + yr + ')">' + yr + '</th>');
                    }
                });

                var $tableRincian = $('#tableRincianCplScores tbody');
                $tableRincian.empty();
                if (skorCplPerTahun && Object.keys(skorCplPerTahun).length > 0) {
                    $.each(skorCplPerTahun, function(cplKode, scoresObj) {
                        var rowHtml = '<tr><td class="fw-bold text-dark">' + cplKode + '</td>';
                        $.each(yearsList, function(idx, yr) {
                            var hasData = yearsWithData[yr] !== undefined ? yearsWithData[yr] : true;
                            if (hasData && scoresObj[yr] !== null && scoresObj[yr] !== undefined) {
                                var scoreVal = roundToTwo(scoresObj[yr]);
                                rowHtml += '<td class="text-center fw-semibold text-dark interactive-year-td" data-year="' + yr + '" style="cursor: pointer;" title="Klik tahun ' + yr + '">' + scoreVal + '</td>';
                            } else {
                                rowHtml += '<td class="text-center text-muted bg-light interactive-year-td" data-year="' + yr + '" style="color: #94a3b8 !important; background-color: #f8fafc !important; cursor: pointer;" title="Belum Ditempuh">-</td>';
                            }
                        });
                        rowHtml += '</tr>';
                        $tableRincian.append(rowHtml);
                    });

                    // Summary Row (Rata-rata per Tahun)
                    var $tfootRincian = $('#tableRincianCplScores tfoot');
                    $tfootRincian.empty();
                    var footerHtml = '<tr style="background-color: #ebf5fb;"><th class="text-primary fw-bold" style="color: #1F3BB3 !important;">Rata-rata Skor</th>';
                    $.each(yearsList, function(idx, yr) {
                        var hasData = yearsWithData[yr] !== undefined ? yearsWithData[yr] : true;
                        if (hasData && skorCplYearlyAvg[yr] !== null && skorCplYearlyAvg[yr] !== undefined) {
                            var avgYr = roundToTwo(skorCplYearlyAvg[yr]);
                            footerHtml += '<th class="text-center text-primary fw-bold interactive-year-tf" data-year="' + yr + '" style="cursor: pointer;" title="Klik tahun ' + yr + '">' + avgYr + '</th>';
                        } else {
                            footerHtml += '<th class="text-center text-muted bg-light interactive-year-tf" data-year="' + yr + '" style="cursor: pointer;" title="Belum Ditempuh">-</th>';
                        }
                    });
                    footerHtml += '</tr>';
                    $tfootRincian.append(footerHtml);
                } else {
                    $tableRincian.append('<tr><td colspan="' + (yearsList.length + 1) + '" class="text-center text-muted py-3">Data kosong</td></tr>');
                }

                // 2. POPULATE TABEL KETERAPAIAN CPL (%) PER TAHUN
                var $theadKomparasi = $('#theadKomparasiCplAngkatan');
                $theadKomparasi.empty();
                $theadKomparasi.append('<th style="min-width: 120px;">Kode CPL</th>');
                $.each(yearsList, function(idx, yr) {
                    var hasData = yearsWithData[yr] !== undefined ? yearsWithData[yr] : true;
                    if (hasData) {
                        $theadKomparasi.append('<th class="text-center interactive-year-th" data-year="' + yr + '" style="min-width: 80px;" title="Klik untuk melihat diagram tahun ' + yr + '">' + yr + '</th>');
                    } else {
                        $theadKomparasi.append('<th class="text-center interactive-year-th text-muted bg-light" data-year="' + yr + '" style="min-width: 80px; color: #94a3b8 !important;" title="Belum Ditempuh (Klik untuk melihat diagram tahun ' + yr + ')">' + yr + '</th>');
                    }
                });

                var $tableKomparasi = $('#tableKomparasiCplAngkatan tbody');
                $tableKomparasi.empty();
                if (ketercapaianCplPerTahun && Object.keys(ketercapaianCplPerTahun).length > 0) {
                    $.each(ketercapaianCplPerTahun, function(cplKode, pctObj) {
                        var rowHtml = '<tr><td class="fw-bold text-dark">' + cplKode + '</td>';
                        $.each(yearsList, function(idx, yr) {
                            var hasData = yearsWithData[yr] !== undefined ? yearsWithData[yr] : true;
                            if (hasData && pctObj[yr] !== null && pctObj[yr] !== undefined) {
                                var pctVal = roundToTwo(pctObj[yr]);
                                rowHtml += '<td class="text-center fw-semibold text-dark interactive-year-td" data-year="' + yr + '" style="cursor: pointer;" title="Klik tahun ' + yr + '">' + pctVal + '%</td>';
                            } else {
                                rowHtml += '<td class="text-center text-muted bg-light interactive-year-td" data-year="' + yr + '" style="color: #94a3b8 !important; background-color: #f8fafc !important; cursor: pointer;" title="Belum Ditempuh">-</td>';
                            }
                        });
                        rowHtml += '</tr>';
                        $tableKomparasi.append(rowHtml);
                    });

                    // Summary Row (Rata-rata per Tahun)
                    var $tfootKomparasi = $('#tableKomparasiCplAngkatan tfoot');
                    $tfootKomparasi.empty();
                    var footerPctHtml = '<tr style="background-color: #ebf5fb;"><th class="text-primary fw-bold" style="color: #1F3BB3 !important;">Rata-rata Ketercapaian</th>';
                    $.each(yearsList, function(idx, yr) {
                        var hasData = yearsWithData[yr] !== undefined ? yearsWithData[yr] : true;
                        if (hasData && ketercapaianCplYearlyAvg[yr] !== null && ketercapaianCplYearlyAvg[yr] !== undefined) {
                            var avgPctYr = roundToTwo(ketercapaianCplYearlyAvg[yr]);
                            footerPctHtml += '<th class="text-center text-primary fw-bold interactive-year-tf" data-year="' + yr + '" style="cursor: pointer;" title="Klik tahun ' + yr + '">' + avgPctYr + '%</th>';
                        } else {
                            footerPctHtml += '<th class="text-center text-muted bg-light interactive-year-tf" data-year="' + yr + '" style="cursor: pointer;" title="Belum Ditempuh">-</th>';
                        }
                    });
                    footerPctHtml += '</tr>';
                    $tfootKomparasi.append(footerPctHtml);
                } else {
                    $tableKomparasi.append('<tr><td colspan="' + (yearsList.length + 1) + '" class="text-center text-muted py-3">Data kosong</td></tr>');
                }

                // RADAR CHART PERSENTASE CAPAIAN CPL (TANPA AVERAGE CPL)
                if (radarChartCapaianCplInstance) {
                    radarChartCapaianCplInstance.destroy();
                    radarChartCapaianCplInstance = null;
                }

                var labelsCapaianCpl = Object.values(cplTmp).map(item => item[1]);
                var dataCapaianCpl = Object.values(cplTmp).map(item => roundToTwo(item[0]));
                var dataMinCpl = Object.values(allCplPerAngkatan.min || {}).map(v => roundToTwo(v));
                var dataMaxCpl = Object.values(allCplPerAngkatan.max || {}).map(v => roundToTwo(v));

                var canvas = document.getElementById('radarChartCapaianCpl');
                var ctx = canvas.getContext('2d');
                radarChartCapaianCplInstance = new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: labelsCapaianCpl,
                        datasets: [{
                                label: 'CPL (Mahasiswa)',
                                data: dataCapaianCpl,
                                backgroundColor: 'rgba(31, 59, 179, 0.15)',
                                borderColor: '#1F3BB3',
                                borderWidth: 2.5,
                                pointBackgroundColor: '#1F3BB3',
                                pointRadius: 4,
                                pointHoverRadius: 6
                            },
                            {
                                label: 'Min CPL',
                                data: dataMinCpl,
                                backgroundColor: 'rgba(239, 68, 68, 0.04)',
                                borderColor: 'rgba(239, 68, 68, 0.85)',
                                borderWidth: 1.5,
                                borderDash: [4, 4],
                                pointBackgroundColor: '#ef4444',
                                pointRadius: 3,
                                pointHoverRadius: 5
                            },
                            {
                                label: 'Max CPL',
                                data: dataMaxCpl,
                                backgroundColor: 'rgba(16, 185, 129, 0.04)',
                                borderColor: 'rgba(16, 185, 129, 0.85)',
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

                // RADAR CHART CPL PER SOAL (Achievement of CPL Scores)
                if (radarChartPerSoalInstance) {
                    radarChartPerSoalInstance.destroy();
                    radarChartPerSoalInstance = null;
                }

                var labelsCplPerSoal = Object.values(cplPerSoalWithKodeAll).map(item => item.kode);
                var dataCplPerSoal = Object.values(cplPerSoalWithKodeAll).map(item => roundToTwo(item.hasil));
                var canvas = document.getElementById('radarChart');
                var ctx = canvas.getContext('2d');
                radarChartPerSoalInstance = new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: labelsCplPerSoal,
                        datasets: [{
                            label: 'Skor Capaian CPL',
                            data: dataCplPerSoal,
                            backgroundColor: 'rgba(31, 59, 179, 0.15)',
                            borderColor: '#1F3BB3',
                            borderWidth: 2.5,
                            pointBackgroundColor: '#1F3BB3',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
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
                                    return 'Skor Capaian: ' + tooltipItem.yLabel + ' / 100';
                                }
                            }
                        }
                    }
                });

                // Helper Fungsi Update Diagram Skor CPL per Tahun
                function updateRadarChartSkorCpl(selectedYear) {
                    activeYearSkorCpl = selectedYear;
                    var labels = Object.values(cplPerSoalWithKodeAll).map(item => item.kode);
                    var datasetData = [];

                    if (selectedYear) {
                        datasetData = labels.map(function(k) {
                            if (skorCplPerTahun[k] && skorCplPerTahun[k][selectedYear] !== null && skorCplPerTahun[k][selectedYear] !== undefined) {
                                return roundToTwo(skorCplPerTahun[k][selectedYear]);
                            }
                            return 0;
                        });
                        var hasDataYr = yearsWithData[selectedYear] !== undefined ? yearsWithData[selectedYear] : true;
                        var labelSuffix = !hasDataYr ? ' (Belum Ditempuh)' : '';
                        $('#badgeYearSkorCpl .badge-year-text').text('Tahun ' + selectedYear + labelSuffix);
                        $('#badgeYearSkorCpl').fadeIn(150);

                        // Highlight kolom aktif di Tabel 1
                        $('#theadRincianCplScores th.interactive-year-th').removeClass('active-year-col');
                        $('#tableRincianCplScores tbody td.interactive-year-td').removeClass('active-year-col');
                        $('#tableRincianCplScores tfoot th.interactive-year-tf').removeClass('active-year-col');

                        $('#theadRincianCplScores th[data-year="' + selectedYear + '"]').addClass('active-year-col');
                        $('#tableRincianCplScores tbody td[data-year="' + selectedYear + '"]').addClass('active-year-col');
                        $('#tableRincianCplScores tfoot th[data-year="' + selectedYear + '"]').addClass('active-year-col');
                    } else {
                        // Default state: ambil data tahun terakhir, sembunyikan badge, hilangkan highlight kolom
                        var targetYear = defaultYear;
                        if (targetYear) {
                            datasetData = labels.map(function(k) {
                                if (skorCplPerTahun[k] && skorCplPerTahun[k][targetYear] !== null && skorCplPerTahun[k][targetYear] !== undefined) {
                                    return roundToTwo(skorCplPerTahun[k][targetYear]);
                                }
                                return 0;
                            });
                        } else {
                            datasetData = Object.values(cplPerSoalWithKodeAll).map(item => roundToTwo(item.hasil));
                        }
                        $('#badgeYearSkorCpl').hide();

                        $('#theadRincianCplScores th.interactive-year-th').removeClass('active-year-col');
                        $('#tableRincianCplScores tbody td.interactive-year-td').removeClass('active-year-col');
                        $('#tableRincianCplScores tfoot th.interactive-year-tf').removeClass('active-year-col');
                    }

                    if (radarChartPerSoalInstance) {
                        radarChartPerSoalInstance.data.datasets[0].label = selectedYear ? 'Skor Capaian CPL (Tahun ' + selectedYear + ')' : 'Skor Capaian CPL';
                        radarChartPerSoalInstance.data.datasets[0].data = datasetData;
                        radarChartPerSoalInstance.update();
                    }
                }

                // Helper Fungsi Update Diagram Ketercapaian CPL (%) per Tahun
                function updateRadarChartKetercapaianCpl(selectedYear) {
                    activeYearKetercapaianCpl = selectedYear;
                    var labels = Object.values(cplTmp).map(item => item[1]);
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
                        $('#badgeYearKetercapaianCpl .badge-year-text').text('Tahun ' + selectedYear + labelSuffix);
                        $('#badgeYearKetercapaianCpl').fadeIn(150);

                        // Highlight kolom aktif di Tabel 2
                        $('#theadKomparasiCplAngkatan th.interactive-year-th').removeClass('active-year-col');
                        $('#tableKomparasiCplAngkatan tbody td.interactive-year-td').removeClass('active-year-col');
                        $('#tableKomparasiCplAngkatan tfoot th.interactive-year-tf').removeClass('active-year-col');

                        $('#theadKomparasiCplAngkatan th[data-year="' + selectedYear + '"]').addClass('active-year-col');
                        $('#tableKomparasiCplAngkatan tbody td[data-year="' + selectedYear + '"]').addClass('active-year-col');
                        $('#tableKomparasiCplAngkatan tfoot th[data-year="' + selectedYear + '"]').addClass('active-year-col');
                    } else {
                        // Default state: ambil data tahun terakhir, sembunyikan badge, hilangkan highlight kolom
                        var targetYear = defaultYear;
                        if (targetYear) {
                            datasetData = labels.map(function(k) {
                                if (ketercapaianCplPerTahun[k] && ketercapaianCplPerTahun[k][targetYear] !== null && ketercapaianCplPerTahun[k][targetYear] !== undefined) {
                                    return roundToTwo(ketercapaianCplPerTahun[k][targetYear]);
                                }
                                return 0;
                            });
                        } else {
                            datasetData = Object.values(cplTmp).map(item => roundToTwo(item[0]));
                        }
                        $('#badgeYearKetercapaianCpl').hide();

                        $('#theadKomparasiCplAngkatan th.interactive-year-th').removeClass('active-year-col');
                        $('#tableKomparasiCplAngkatan tbody td.interactive-year-td').removeClass('active-year-col');
                        $('#tableKomparasiCplAngkatan tfoot th.interactive-year-tf').removeClass('active-year-col');
                    }

                    if (radarChartCapaianCplInstance) {
                        radarChartCapaianCplInstance.data.datasets[0].label = selectedYear ? 'CPL (Mahasiswa) - Tahun ' + selectedYear : 'CPL (Mahasiswa)';
                        radarChartCapaianCplInstance.data.datasets[0].data = datasetData;
                        radarChartCapaianCplInstance.update();
                    }
                }

                // Event Listener Klik Tahun pada Tabel 1 (Skor CPL)
                $('#tableRincianCplScores').off('click', '[data-year]').on('click', '[data-year]', function() {
                    var clickedYr = $(this).data('year');
                    if (activeYearSkorCpl == clickedYr) {
                        updateRadarChartSkorCpl(null);
                    } else {
                        updateRadarChartSkorCpl(clickedYr);
                    }
                });

                $('#badgeYearSkorCpl').off('click').on('click', function() {
                    updateRadarChartSkorCpl(null);
                });

                // Event Listener Klik Tahun pada Tabel 2 (Ketercapaian CPL %)
                $('#tableKomparasiCplAngkatan').off('click', '[data-year]').on('click', '[data-year]', function() {
                    var clickedYr = $(this).data('year');
                    if (activeYearKetercapaianCpl == clickedYr) {
                        updateRadarChartKetercapaianCpl(null);
                    } else {
                        updateRadarChartKetercapaianCpl(clickedYr);
                    }
                });

                $('#badgeYearKetercapaianCpl').off('click').on('click', function() {
                    updateRadarChartKetercapaianCpl(null);
                });

                // Set default tampilan awal (data akumulatif/terkini tanpa badge)
                updateRadarChartSkorCpl(null);
                updateRadarChartKetercapaianCpl(null);

                // Tombol Print PDF
                $('#btnPrintPdf').prop('disabled', false);

                $("#btnPrintPdf").off("click").on("click", function() {
                    let currentNama = nama;
                    let currentNpm = npm;
                    let currentAngkatan = angkatan;
                    let currentProdi = prodi;
                    let currentUniversitas = universitas;

                    let courseListArray = [];
                    for (let cCode in courseArray) {
                        courseListArray.push(cCode + ' - ' + courseArray[cCode]);
                    }

                    let radarChartCapaianCpl = document.getElementById("radarChartCapaianCpl");
                    let radarChart = document.getElementById("radarChart");
                    let profilChart = document.getElementById("profilChart");

                    let radarChartCapaianCplImg = radarChartCapaianCpl ? radarChartCapaianCpl.toDataURL() : null;
                    let radarChartImg = radarChart ? radarChart.toDataURL() : null;
                    let profilChartImg = profilChart ? profilChart.toDataURL() : null;

                    let soalTerendahArray = [];
                    document.querySelectorAll("#soalTerendahTable tbody tr").forEach(row => {
                        let cols = row.querySelectorAll("td");
                        if (cols.length === 4 && !row.querySelector("td[colspan]")) {
                            soalTerendahArray.push({
                                no: cols[0].textContent.trim(),
                                course_name: cols[1].textContent.trim(),
                                types_of_assessment: cols[2].textContent.trim(),
                                question: cols[3].textContent.trim(),
                            });
                        }
                    });

                    let hasilProfilArray = [];
                    let currentNoValue = 1;
                    let hasilFinalProfilData = response.result.hasilFinalProfil;

                    Object.values(hasilFinalProfilData).forEach((profil) => {
                        let profilData = {
                            no: currentNoValue,
                            profile_career: profil.NamaProfil,
                            graduate_profile: profil.Deskripsi,
                            total: profil.TotalAkhir.toFixed(3)
                        };

                        profil.CPLs.forEach((cpl, index) => {
                            hasilProfilArray.push({
                                no: profilData.no,
                                profile_career: profilData.profile_career,
                                graduate_profile: profilData.graduate_profile,
                                cpl: cpl.CPL,
                                profile_weight: cpl.Bobot,
                                cpl_result: cpl.HasilCPL,
                                profile_weight_cpl_result: (cpl.Bobot * cpl.HasilCPL).toFixed(3),
                                total: profilData.total,
                                is_first_row: index === 0,
                                total_rows: profil.CPLs.length
                            });
                        });
                        currentNoValue++;
                    });

                    // Mata Kuliah Lulus
                    let mataKuliahLulusArray = [];
                    document.querySelectorAll("#labelMkLulus table tbody tr").forEach(row => {
                        let cols = row.querySelectorAll("td");
                        if (cols.length === 4 && !row.querySelector("td[colspan]")) {
                            mataKuliahLulusArray.push({
                                no: cols[0].textContent.trim(),
                                courseCode: cols[1].textContent.trim(),
                                courseName: cols[2].textContent.trim(),
                                nilai: cols[3].textContent.trim()
                            });
                        }
                    });

                    // Mata Kuliah Tidak Lulus
                    let mataKuliahTidakLulusArray = [];
                    document.querySelectorAll("#labelMkTidakLulus table tbody tr").forEach(row => {
                        let cols = row.querySelectorAll("td");
                        if (cols.length === 4 && !row.querySelector("td[colspan]")) {
                            mataKuliahTidakLulusArray.push({
                                no: cols[0].textContent.trim(),
                                courseCode: cols[1].textContent.trim(),
                                courseName: cols[2].textContent.trim(),
                                nilai: cols[3].textContent.trim()
                            });
                        }
                    });

                    let descriptions = [];
                    labelCpl.forEach(function(item) {
                        descriptions.push(item.kode + ': ' + item.judul);
                    });

                    let form = document.createElement("form");
                    form.method = "POST";
                    form.action = "{{ route($currentPrefix . 'visualisasi.generate-pdfVisualMahasiswa') }}";
                    form.target = "_blank";

                    let csrfInput = document.createElement("input");
                    csrfInput.type = "hidden";
                    csrfInput.name = "_token";
                    csrfInput.value = "{{ csrf_token() }}";
                    form.appendChild(csrfInput);

                    let payload = {
                        nama: currentNama,
                        npm: currentNpm,
                        angkatan: currentAngkatan,
                        prodi: currentProdi,
                        universitas: currentUniversitas,
                        descriptions: descriptions,
                        radarChartCapaianCplImg: radarChartCapaianCplImg,
                        radarChartImg: radarChartImg,
                        profilChartImg: profilChartImg,
                        soalTerendah: soalTerendahArray,
                        hasilFinalProfil: hasilProfilArray,
                        mataKuliahLulus: mataKuliahLulusArray,
                        mataKuliahTidakLulus: mataKuliahTidakLulusArray,
                        yearsList: yearsList,
                        yearsWithData: yearsWithData,
                        skorCplPerTahun: skorCplPerTahun,
                        ketercapaianCplPerTahun: ketercapaianCplPerTahun,
                        skorCplOverall: skorCplOverall,
                        ketercapaianCplOverall: ketercapaianCplOverall,
                        skorCplYearlyAvg: skorCplYearlyAvg,
                        ketercapaianCplYearlyAvg: ketercapaianCplYearlyAvg,
                        skorCplGrandAvg: skorCplGrandAvg,
                        ketercapaianCplGrandAvg: ketercapaianCplGrandAvg,
                        courseList: courseListArray
                    };

                    for (let key in payload) {
                        let input = document.createElement("input");
                        input.type = "hidden";
                        input.name = key;
                        input.value = typeof payload[key] === "object" ? JSON.stringify(payload[key]) : payload[key];
                        form.appendChild(input);
                    }

                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                });
            }

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
