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
@section('title', 'Visualisasi Per Mata Kuliah')
@section('page_title', 'Visualisasi Per Mata Kuliah')
@section('content')
    <script>
        (function() {
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('course') && urlParams.get('angkatan')) {
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

        #visualContainer .modern-card,
        #visualContainer .card-body,
        #tugas .modern-card,
        #tugas .card-body {
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

        /* Section Titles */
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
            width: 100%;
            padding-right: 36px !important;
            cursor: text;
            background-color: #ffffff !important;
        }
        .combobox-input:focus {
            border-color: #1F3BB3 !important;
            box-shadow: 0 0 0 3px rgba(31, 59, 179, 0.12) !important;
            outline: none !important;
        }
        .combobox-input:disabled {
            background-color: #f1f5f9 !important;
            cursor: not-allowed;
            color: #94a3b8 !important;
        }
        .combobox-toggle-btn {
            position: absolute;
            right: 1px;
            top: 1px;
            bottom: 1px;
            width: 36px;
            background: transparent;
            border: none;
            border-radius: 0 8px 8px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            cursor: pointer;
            font-size: 0.85rem;
            transition: color 0.15s ease;
        }
        .combobox-toggle-btn:hover:not(:disabled) {
            color: #1F3BB3;
        }
        .combobox-toggle-btn:disabled {
            cursor: not-allowed;
            color: #cbd5e1;
        }
        .combobox-dropdown-menu {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
            z-index: 99999 !important;
            overflow: hidden !important;
        }
        .combobox-options-list {
            max-height: 260px;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f8fafc;
            -webkit-overflow-scrolling: touch;
        }
        .combobox-options-list::-webkit-scrollbar {
            width: 6px;
        }
        .combobox-options-list::-webkit-scrollbar-track {
            background: #f8fafc;
            border-radius: 4px;
        }
        .combobox-options-list::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 4px;
        }
        .combobox-options-list::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
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

        #visualContainer {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 100% !important;
        }

        /* Course Profile Hero Header */
        .course-profile-hero {
            position: -webkit-sticky !important;
            position: sticky !important;
            top: 70px !important;
            z-index: 1020 !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0;
            border-left: 5px solid #1F3BB3 !important;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
            padding: 18px 24px !important;
            margin-bottom: 24px !important;
            backdrop-filter: blur(10px);
        }

        .course-label-tag {
            display: inline-flex;
            align-items: center;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #1F3BB3;
            background: #eef2ff;
            padding: 2px 10px;
            border-radius: 6px;
            margin-bottom: 6px;
        }

        .course-name-text {
            font-size: 1.35rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
            line-height: 1.25;
        }

        .course-meta-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 0.85rem;
            color: #475569;
        }

        .course-meta-row .meta-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .course-meta-row .meta-label {
            color: #64748b;
            font-weight: 500;
        }

        .course-meta-row .meta-value {
            font-weight: 700;
            color: #1e293b;
        }

        .course-meta-row .meta-pipe {
            color: #cbd5e1;
            font-size: 0.8rem;
        }

        /* Modern Table Styles */
        .modern-table-container {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 0;
            font-size: 0.86rem;
        }

        .modern-table thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-weight: 700 !important;
            font-size: 0.8rem !important;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 12px 16px !important;
            border-bottom: 1px solid #e2e8f0 !important;
            border-top: none !important;
            vertical-align: middle;
        }

        .modern-table tbody td {
            padding: 11px 16px !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #f1f5f9 !important;
            color: #334155;
            font-size: 0.86rem;
        }

        .modern-table tbody tr:last-child td {
            border-bottom: none !important;
        }

        .modern-table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Modern Guide Box */
        .modern-guide-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 16px;
        }

        .modern-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
            display: block;
        }

        .modern-select {
            height: 42px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 8px 14px !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            background-color: #ffffff !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
        }

        .modern-select:focus {
            border-color: #1F3BB3 !important;
            box-shadow: 0 0 0 3px rgba(31, 59, 179, 0.12) !important;
            outline: none !important;
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

        /* Structured CPMK Description Cards (Compact & Smaller) */
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
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
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
    </style>



    {{-- Anti-Flicker Script: Jika browser di-refresh pada halaman output (URL memiliki parameter), tampilkan langsung visualContainer --}}
    <script>
        (function() {
            try {
                var urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('course') && urlParams.get('angkatan')) {
                    document.write('<style id="antiFlickerStyle">#tugas { display: none !important; } #visualContainer { display: block !important; }</style>');
                }
            } catch(e) {}
        })();
    </script>

    {{-- ========================================================================= --}}
    {{-- 1. INPUT FILTER FORM (Halaman Awal Pemilihan Mata Kuliah) --}}
    {{-- ========================================================================= --}}
    <div id="tugas">
        <div class="card modern-card mb-4">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h5 class="section-title mb-1"><i class="bi bi-book-half"></i> Visualisasi CPMK Per Mata Kuliah</h5>
                    <p class="text-muted small mb-0">Silahkan pilih data program studi, angkatan, dan mata kuliah untuk menampilkan visualisasi OBE.</p>
                </div>

                <form id="hasilVisual" method="POST" action="hasilvisual-mahasiswaMataKuliah" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="universitas" id="universitas" value="{{ $universitas->id }}">
                    <input type="hidden" id="universitas-img-path" name="universitas-img-path" value="{{ asset($universitas->img ?? '') }}">
                    @if ($universitas->img)
                        <img id="universitas-img" src="{{ asset($universitas->img) }}" style="display: none;" />
                    @endif

                    <div class="row g-3">
                        @if (in_array($userOtoritas, ['Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas', 'Wakil Rektor', 'Wakil Dekan']))
                            <div class="col-md-4">
                                <label class="modern-label">
                                    <i class="bi bi-mortarboard text-primary me-1"></i> Program Studi <span class="text-danger">*</span>
                                </label>
                                <div class="combobox-wrapper" id="prodiComboboxWrapper">
                                    <div class="combobox-input-group">
                                        <input type="text" 
                                               id="prodiDisplayInput" 
                                               class="form-control modern-select combobox-input" 
                                               placeholder="Pilih / Ketik Program Studi" 
                                               autocomplete="off">
                                        <input type="hidden" id="prodiForm" name="prodi" value="">
                                        <button type="button" class="combobox-toggle-btn" tabindex="-1" id="prodiToggleBtn" title="Tampilkan daftar program studi">
                                            <i class="bi bi-chevron-down"></i>
                                        </button>
                                    </div>
                                    <div class="combobox-dropdown-menu" id="prodiDropdownMenu" style="display: none;">
                                        <div class="combobox-options-list" id="prodiOptionsList">
                                            @foreach ($prodi as $p)
                                                <div class="combobox-option" data-value="{{ $p->id }}" data-text="{{ $p->nama }}">{{ $p->nama }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
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
                                               placeholder="Pilih / Ketik Angkatan" 
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
                                    <i class="bi bi-journal-text text-primary me-1"></i> Mata Kuliah <span class="text-danger">*</span>
                                </label>
                                <div class="combobox-wrapper" id="courseComboboxWrapper">
                                    <div class="combobox-input-group">
                                        <input type="text" 
                                               id="courseDisplayInput" 
                                               class="form-control modern-select combobox-input" 
                                               placeholder="Pilih / Ketik Mata Kuliah" 
                                               autocomplete="off"
                                               disabled>
                                        <input type="hidden" id="courseSelect" name="course" value="">
                                        <button type="button" class="combobox-toggle-btn" tabindex="-1" id="courseToggleBtn" title="Tampilkan daftar mata kuliah" disabled>
                                            <i class="bi bi-chevron-down"></i>
                                        </button>
                                    </div>
                                    <div class="combobox-dropdown-menu" id="courseDropdownMenu" style="display: none;">
                                        <div class="combobox-options-list" id="courseOptionsList">
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
                                               placeholder="Pilih / Ketik Angkatan" 
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
                                    <i class="bi bi-journal-text text-primary me-1"></i> Mata Kuliah <span class="text-danger">*</span>
                                </label>
                                <div class="combobox-wrapper" id="courseComboboxWrapper">
                                    <div class="combobox-input-group">
                                        <input type="text" 
                                               id="courseDisplayInput" 
                                               class="form-control modern-select combobox-input" 
                                               placeholder="Pilih / Ketik Mata Kuliah" 
                                               autocomplete="off"
                                               disabled>
                                        <input type="hidden" id="courseSelect" name="course" value="">
                                        <button type="button" class="combobox-toggle-btn" tabindex="-1" id="courseToggleBtn" title="Tampilkan daftar mata kuliah" disabled>
                                            <i class="bi bi-chevron-down"></i>
                                        </button>
                                    </div>
                                    <div class="combobox-dropdown-menu" id="courseDropdownMenu" style="display: none;">
                                        <div class="combobox-options-list" id="courseOptionsList">
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
    {{-- 2. DEDICATED RESULTS VIEW (Tampilan Hasil Visualisasi Mata Kuliah) --}}
    {{-- ========================================================================= --}}
    <div id="visualContainer" style="display:none;">

        {{-- HERO COURSE CARD (Sticky, Clean, Solid Alignment) --}}
        <div class="course-profile-hero">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <div class="course-label-tag">PROFIL MATA KULIAH</div>
                    <h3 class="course-name-text" id="displayNamaCourse">Memuat Data...</h3>
                    <div class="course-meta-row">
                        <div class="meta-item">
                            <span class="meta-label">Program Studi:</span>
                            <span class="meta-value" id="prodiDataText">-</span>
                        </div>
                        <span class="meta-pipe">|</span>
                        <div class="meta-item">
                            <span class="meta-label">Angkatan:</span>
                            <span class="meta-value" id="angkatanDataText">-</span>
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
                        Ganti Mata Kuliah
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
                    <p class="mb-0 text-muted small" id="emptyDataMessage">Tidak ada data penilaian/asesmen CPMK untuk mata kuliah dan angkatan yang dipilih.</p>
                </div>
            </div>
        </div>

        {{-- CARD 1: Capaian CPMK Angkatan --}}
        <div class="card modern-card mb-4">
            <div class="modern-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="section-title mb-0 d-flex align-items-center flex-wrap gap-2">
                    <i class="bi bi-bar-chart-line"></i> Capaian CPMK Angkatan
                </h5>
                <button class="btn btn-sm btn-outline-secondary" type="button" id="btnToggleGuideCpmkBatch" style="border-radius: 6px; font-size: 12px; padding: 4px 10px;" title="Petunjuk Membaca Diagram">
                    <i class="bi bi-info-circle me-1"></i> Petunjuk Membaca Diagram
                </button>
            </div>
            <div class="card-body p-4">
                {{-- Guide Banner (Bisa di Buka Tutup) --}}
                <div class="mb-3" id="guideCpmkBatch" style="display: none;">
                    <div class="modern-guide-box small">
                        <h6 class="fw-bold text-primary mb-1"><i class="bi bi-compass me-1"></i> Panduan Membaca Diagram Radar CPMK Angkatan:</h6>
                        <ul class="mb-0 ps-3 text-muted">
                            <li><strong>Bentuk Jaring (Radar):</strong> Setiap sudut mewakili satu <strong>CPMK (Capaian Pembelajaran Mata Kuliah)</strong>.</li>
                            <li><span class="badge" style="background-color: #c06e4b; color:#fff;">Skor CPMK</span>: Rata-rata nilai capaian CPMK seluruh mahasiswa di angkatan tersebut (skala 0 - 100).</li>
                            <li><span class="badge" style="background-color: #21d85f; color:#fff;">Max CPMK</span>: Capaian CPMK tertinggi di angkatan tersebut.</li>
                            <li><span class="badge" style="background-color: #d82121; color:#fff;">Min CPMK</span>: Capaian CPMK terendah di angkatan tersebut.</li>
                        </ul>
                    </div>
                </div>

                {{-- Executive Summary Cards --}}
                <div class="row g-3 mb-4" id="cpmkBatchMetricCards">
                    <div class="col-md-4 col-sm-12">
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <div class="text-muted small fw-semibold">Rata-rata Skor CPMK</div>
                            <div class="h4 mb-0 fw-bold text-primary mt-1" id="metricAvgScore">-</div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <div class="text-muted small fw-semibold">CPMK Tertinggi</div>
                            <div class="h4 mb-0 fw-bold text-success mt-1" id="metricHighestCpmk">-</div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <div class="text-muted small fw-semibold">CPMK Terendah</div>
                            <div class="h4 mb-0 fw-bold text-danger mt-1" id="metricLowestCpmk">-</div>
                        </div>
                    </div>
                </div>

                {{-- Diagram Radar Canvas & Tabel Rincian Capaian CPMK (Bersebelahan) --}}
                <div class="row g-4 align-items-center mb-4">
                    <div class="col-lg-6 col-md-12">
                        <div class="d-flex justify-content-center align-items-center p-2">
                            <div style="width: 100%; max-width: 580px;">
                                <canvas id="radarChartAngkatan"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="d-flex flex-column justify-content-center h-100">
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-table text-primary me-2"></i> Rincian Capaian CPMK:</h6>
                            <div class="modern-table-container">
                                <div class="table-responsive" style="max-height: 340px; overflow-y: auto;">
                                    <table class="table modern-table mb-0" id="tableRincianCpmkAngkatan">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 40%;">Kode CPMK</th>
                                                <th class="text-center" style="width: 60%;">Skor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Dinamis via AJAX --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Deskripsi CPMK (Bisa di Buka Tutup via Header & Tombol) --}}
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 cpmk-desc-header" id="headerToggleCpmkDesc" style="cursor: pointer; user-select: none;">
                        <div class="d-flex align-items-center">
                            <h6 class="keterangan fw-bold text-dark mb-0"><i class="bi bi-card-text text-primary me-2"></i> Descriptions (Deskripsi CPMK) :</h6>
                            <span id="cpmkBadgeCount" class="badge bg-light text-secondary border ms-1" style="font-size: 0.75rem; display: none;"></span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center justify-content-center" type="button" id="btnToggleCpmkDesc" style="border-radius: 6px; font-size: 13px; width: 32px; height: 30px; padding: 0;" title="Buka / Tutup Deskripsi CPMK">
                            <i class="bi bi-chevron-down" id="iconCpmkDescCollapse"></i>
                        </button>
                    </div>
                    <div class="mt-2" id="collapseCpmkDescriptions" style="display: none;">
                        <div id="labelContainer" class="cpl-desc-grid">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW: Lihat CPMK Individu & Questions with Lowest Score (Bersebelahan, Sama Besar 50:50) --}}
        <div class="row g-4 mb-4">
            {{-- CARD 2: Lihat Capaian CPMK Individu Mahasiswa --}}
            <div class="col-lg-6 col-md-12">
                <div class="card modern-card h-100 mb-0">
                    <div class="modern-card-header">
                        <h5 class="section-title mb-0">
                            <i class="bi bi-person-check"></i> Lihat CPMK Individu Mahasiswa
                        </h5>
                    </div>
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <p class="text-muted small mb-3">Pilih mahasiswa untuk menampilkan visualisasi capaian CPMK individu pada mata kuliah ini:</p>
                            <form id="hasilvisualcpmk-mahasiswa" method="POST" action="hasilvisualcpmk-mahasiswa" enctype="multipart/form-data">
                                @csrf
                                <input type="text" name="nama" class="visually-hidden" value="">
                                <input type="text" name="angkatan" class="visually-hidden" value="">
                                <input type="text" name="prodi" class="visually-hidden" value="">
                                <input type="text" name="course" class="visually-hidden" value="">
                                <input type="text" name="universitasImg" class="visually-hidden" value="">
                                <input type="text" name="universitasCPMK" class="visually-hidden" value="">
                                <div class="mb-3">
                                    <label class="modern-label mb-1">
                                        <i class="bi bi-person text-primary me-1"></i> Pilih Mahasiswa <span class="text-danger">*</span>
                                    </label>
                                    <div class="combobox-wrapper" id="npmComboboxWrapper">
                                        <div class="combobox-input-group">
                                            <input type="text" 
                                                   id="npmDisplayInput" 
                                                   class="form-control modern-select combobox-input" 
                                                   placeholder="Ketik NPM atau Nama Mahasiswa..." 
                                                   autocomplete="off">
                                            <input type="hidden" id="npmSelect" name="npm" value="">
                                            <button type="button" class="combobox-toggle-btn" tabindex="-1" id="npmToggleBtn" title="Tampilkan daftar mahasiswa">
                                                <i class="bi bi-chevron-down"></i>
                                            </button>
                                        </div>
                                        <div class="combobox-dropdown-menu" id="npmDropdownMenu" style="display: none;">
                                            <div class="combobox-options-list" id="npmOptionsList">
                                                <div class="combobox-empty-state">Pilih Mahasiswa</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="modern-btn-primary w-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                    </svg>
                                    Tampilkan CPMK
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 3: Questions with Lowest CPMK --}}
            <div class="col-lg-6 col-md-12">
                <div class="card modern-card h-100 mb-0">
                    <div class="modern-card-header d-flex justify-content-between align-items-center">
                        <h5 class="section-title mb-0">
                            <i class="bi bi-patch-question text-primary"></i> Questions with Lowest CPMK
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">Soal-soal penilaian dengan perolehan skor CPMK terendah pada mata kuliah ini:</p>
                        <div class="modern-table-container">
                            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                <table class="table modern-table mb-0" id="soalTerendahTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 45px;" class="text-center">No</th>
                                            <th style="width: 120px;" class="text-center">Asesmen</th>
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
        </div>

    </div>

    {{-- ChartJS CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

    <script>
        $(document).ready(function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            var originalBtnHtml = $('#btnSubmitVisual').html();
            var windowRadarChartInstance = null;

            function parseOptionsFromHtml(htmlString) {
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

            // ---------------- PRODI COMBOBOX ----------------
            var prodiOptionsData = [];
            var activeProdiIndex = -1;

            @if (in_array($userOtoritas, ['Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas', 'Wakil Rektor', 'Wakil Dekan']))
                $('#prodiOptionsList .combobox-option').each(function() {
                    var val = $(this).data('value');
                    var txt = $(this).data('text') || $(this).text().trim();
                    if (val) {
                        prodiOptionsData.push({ value: String(val).trim(), text: txt });
                    }
                });

                function renderProdiOptions(filterText) {
                    var $list = $('#prodiOptionsList');
                    $list.empty();
                    activeProdiIndex = -1;
                    if (!prodiOptionsData.length) {
                        $list.html('<div class="combobox-empty-state">Tidak ada data program studi</div>');
                        return;
                    }
                    var query = (filterText || '').toLowerCase().trim();
                    var filtered = prodiOptionsData.filter(function(item) {
                        if (!query) return true;
                        return item.text.toLowerCase().indexOf(query) !== -1 || item.value.toLowerCase().indexOf(query) !== -1;
                    });
                    if (filtered.length === 0) {
                        $list.html('<div class="combobox-empty-state">Tidak ada program studi yang cocok</div>');
                        return;
                    }
                    var currentVal = $('#prodiForm').val();
                    filtered.forEach(function(item, idx) {
                        var isSelected = (item.value === currentVal);
                        var $opt = $('<div>')
                            .addClass('combobox-option' + (isSelected ? ' is-selected' : ''))
                            .attr('data-value', item.value)
                            .text(item.text);
                        $opt.on('mousedown', function(e) {
                            e.preventDefault();
                            selectProdiItem(item.value, item.text);
                        });
                        $list.append($opt);
                    });
                }

                function selectProdiItem(val, text) {
                    $('#prodiForm').val(val);
                    $('#prodiDisplayInput').val(text);
                    $('#prodiDropdownMenu').hide();
                    $('#prodiComboboxWrapper').removeClass('is-open');
                    var universitas = $('#universitas').val();
                    disableCourseCombobox();
                    loadAngkatan(val, universitas);
                }

                $('#prodiDisplayInput').on('focus click', function() {
                    var currentVal = $('#prodiForm').val();
                    var currentObj = prodiOptionsData.find(function(s) { return s.value === currentVal; });
                    renderProdiOptions(currentObj && currentObj.text === $(this).val().trim() ? '' : $(this).val().trim());
                    $('#prodiComboboxWrapper').addClass('is-open');
                    $('#prodiDropdownMenu').show();
                });

                $('#prodiDisplayInput').on('input', function() {
                    var query = $(this).val().trim();
                    renderProdiOptions(query);
                    $('#prodiComboboxWrapper').addClass('is-open');
                    $('#prodiDropdownMenu').show();
                    var match = prodiOptionsData.find(function(item) {
                        return item.text.toLowerCase() === query.toLowerCase() || item.value.toLowerCase() === query.toLowerCase();
                    });
                    if (match) {
                        $('#prodiForm').val(match.value);
                        var universitas = $('#universitas').val();
                        disableCourseCombobox();
                        loadAngkatan(match.value, universitas);
                    } else {
                        $('#prodiForm').val('');
                    }
                });

                $('#prodiToggleBtn').on('click', function(e) {
                    e.preventDefault();
                    if ($('#prodiDropdownMenu').is(':visible')) {
                        $('#prodiDropdownMenu').hide();
                        $('#prodiComboboxWrapper').removeClass('is-open');
                    } else {
                        $('#prodiDisplayInput').focus();
                    }
                });
            @endif

            // ---------------- ANGKATAN COMBOBOX ----------------
            var angkatanOptionsData = [];
            var activeAngkatanIndex = -1;

            function renderAngkatanOptions(filterText) {
                var $list = $('#angkatanOptionsList');
                $list.empty();
                activeAngkatanIndex = -1;
                if (!angkatanOptionsData.length) {
                    $list.html('<div class="combobox-empty-state">Pilih Program Studi terlebih dahulu</div>');
                    return;
                }
                var query = (filterText || '').toLowerCase().trim();
                var filtered = angkatanOptionsData.filter(function(item) {
                    if (!query) return true;
                    return item.text.toLowerCase().indexOf(query) !== -1 || item.value.toLowerCase().indexOf(query) !== -1;
                });
                if (filtered.length === 0) {
                    $list.html('<div class="combobox-empty-state">Tidak ada angkatan yang cocok</div>');
                    return;
                }
                var currentVal = $('#angkatanForm').val();
                filtered.forEach(function(item, idx) {
                    var isSelected = (item.value === currentVal);
                    var $opt = $('<div>')
                        .addClass('combobox-option' + (isSelected ? ' is-selected' : ''))
                        .attr('data-value', item.value)
                        .text(item.text);
                    $opt.on('mousedown', function(e) {
                        e.preventDefault();
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
                var prodi = $('#prodiForm').val() || "{{ auth()->user()->id_prodiUser ?? '' }}";
                var universitas = $('#universitas').val();
                loadCourses(prodi, val, universitas);
            }

            $('#angkatanDisplayInput').on('focus click', function() {
                var currentVal = $('#angkatanForm').val();
                var currentObj = angkatanOptionsData.find(function(s) { return s.value === currentVal; });
                renderAngkatanOptions(currentObj && currentObj.text === $(this).val().trim() ? '' : $(this).val().trim());
                $('#angkatanComboboxWrapper').addClass('is-open');
                $('#angkatanDropdownMenu').show();
            });

            $('#angkatanDisplayInput').on('input', function() {
                var query = $(this).val().trim();
                renderAngkatanOptions(query);
                $('#angkatanComboboxWrapper').addClass('is-open');
                $('#angkatanDropdownMenu').show();
                var match = angkatanOptionsData.find(function(item) {
                    return item.text.toLowerCase() === query.toLowerCase() || item.value.toLowerCase() === query.toLowerCase();
                });
                if (match) {
                    $('#angkatanForm').val(match.value);
                    var prodi = $('#prodiForm').val() || "{{ auth()->user()->id_prodiUser ?? '' }}";
                    var universitas = $('#universitas').val();
                    loadCourses(prodi, match.value, universitas);
                } else if (/^\d{4}$/.test(query)) {
                    $('#angkatanForm').val(query);
                    var prodi = $('#prodiForm').val() || "{{ auth()->user()->id_prodiUser ?? '' }}";
                    var universitas = $('#universitas').val();
                    loadCourses(prodi, query, universitas);
                } else {
                    $('#angkatanForm').val('');
                }
            });

            $('#angkatanToggleBtn').on('click', function(e) {
                e.preventDefault();
                if ($('#angkatanDropdownMenu').is(':visible')) {
                    $('#angkatanDropdownMenu').hide();
                    $('#angkatanComboboxWrapper').removeClass('is-open');
                } else {
                    $('#angkatanDisplayInput').focus();
                }
            });

            // ---------------- COURSE COMBOBOX ----------------
            var courseOptionsData = [];
            var activeCourseIndex = -1;

            function disableCourseCombobox() {
                $('#courseSelect').val('');
                $('#courseDisplayInput').val('').prop('disabled', true);
                $('#courseToggleBtn').prop('disabled', true);
                courseOptionsData = [];
                $('#courseOptionsList').html('<div class="combobox-empty-state">Pilih Angkatan terlebih dahulu</div>');
                $('#courseDropdownMenu').hide();
                $('#courseComboboxWrapper').removeClass('is-open');
            }

            function enableCourseCombobox() {
                $('#courseDisplayInput').prop('disabled', false);
                $('#courseToggleBtn').prop('disabled', false);
            }

            function renderCourseOptions(filterText) {
                var $list = $('#courseOptionsList');
                $list.empty();
                activeCourseIndex = -1;
                if (!courseOptionsData.length) {
                    $list.html('<div class="combobox-empty-state">Tidak ada mata kuliah pada angkatan ini</div>');
                    return;
                }
                var query = (filterText || '').toLowerCase().trim();
                var filtered = courseOptionsData.filter(function(item) {
                    if (!query) return true;
                    return item.text.toLowerCase().indexOf(query) !== -1 || item.value.toLowerCase().indexOf(query) !== -1;
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
                        .attr('title', item.text)
                        .text(item.text);
                    $opt.on('mousedown', function(e) {
                        e.preventDefault();
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
            }

            $('#courseDisplayInput').on('focus click', function() {
                if (!courseOptionsData.length) return;
                var currentVal = $('#courseSelect').val();
                var currentObj = courseOptionsData.find(function(s) { return s.value === currentVal; });
                renderCourseOptions(currentObj && currentObj.text === $(this).val().trim() ? '' : $(this).val().trim());
                $('#courseComboboxWrapper').addClass('is-open');
                $('#courseDropdownMenu').show();
            });

            $('#courseDisplayInput').on('input', function() {
                var query = $(this).val().trim();
                renderCourseOptions(query);
                $('#courseComboboxWrapper').addClass('is-open');
                $('#courseDropdownMenu').show();
                var match = courseOptionsData.find(function(item) {
                    return item.text.toLowerCase() === query.toLowerCase() || item.value.toLowerCase() === query.toLowerCase();
                });
                if (match) {
                    $('#courseSelect').val(match.value);
                } else {
                    $('#courseSelect').val('');
                }
            });

            $('#courseToggleBtn').on('click', function(e) {
                e.preventDefault();
                if ($('#courseDropdownMenu').is(':visible')) {
                    $('#courseDropdownMenu').hide();
                    $('#courseComboboxWrapper').removeClass('is-open');
                } else {
                    $('#courseDisplayInput').focus();
                }
            });

            // Close all comboboxes when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#prodiComboboxWrapper').length) {
                    $('#prodiDropdownMenu').hide();
                    $('#prodiComboboxWrapper').removeClass('is-open');
                }
                if (!$(e.target).closest('#angkatanComboboxWrapper').length) {
                    $('#angkatanDropdownMenu').hide();
                    $('#angkatanComboboxWrapper').removeClass('is-open');
                }
                if (!$(e.target).closest('#courseComboboxWrapper').length) {
                    $('#courseDropdownMenu').hide();
                    $('#courseComboboxWrapper').removeClass('is-open');
                }
            });

            // Load Angkatan based on Prodi
            function loadAngkatan(prodi, universitas, callback, preserveVal) {
                if (!preserveVal) {
                    angkatanOptionsData = [];
                    $('#angkatanForm').val('');
                    $('#angkatanDisplayInput').val('');
                    disableCourseCombobox();
                }
                if (!prodi) {
                    $('#angkatanOptionsList').html('<div class="combobox-empty-state">Pilih Program Studi terlebih dahulu</div>');
                    return;
                }
                if (!preserveVal) {
                    $('#angkatanOptionsList').html('<div class="combobox-empty-state">Memuat data angkatan...</div>');
                }
                $.ajax({
                    url: "{{ route($currentPrefix . 'visualisasi.getAngkatanByProdiUniversitas') }}",
                    method: 'GET',
                    data: {
                        prodi: prodi,
                        universitas: universitas
                    },
                    success: function(data) {
                        angkatanOptionsData = parseOptionsFromHtml(data);
                        var currentAngkatan = $('#angkatanForm').val();
                        renderAngkatanOptions('');
                        if (currentAngkatan) {
                            var match = angkatanOptionsData.find(function(a) { return String(a.value).trim() === String(currentAngkatan).trim(); });
                            if (match) {
                                $('#angkatanDisplayInput').val(match.text);
                            }
                        }
                        if (callback) callback();
                    },
                    error: function() {
                        $('#angkatanOptionsList').html('<div class="combobox-empty-state text-danger">Gagal memuat data angkatan</div>');
                    }
                });
            }

            // Load Courses based on Prodi & Angkatan
            function loadCourses(prodi, angkatan, universitas, callback, preserveVal) {
                if (!preserveVal) {
                    courseOptionsData = [];
                    $('#courseSelect').val('');
                    $('#courseDisplayInput').val('');
                }
                if (!angkatan || !prodi) {
                    disableCourseCombobox();
                    return;
                }
                enableCourseCombobox();
                if (!preserveVal) {
                    $('#courseOptionsList').html('<div class="combobox-empty-state"><span class="spinner-border spinner-border-sm me-2 text-primary"></span>Memuat daftar mata kuliah...</div>');
                }
                $.ajax({
                    url: "{{ route($currentPrefix . 'visualisasi.getCourseByProdi') }}",
                    type: 'GET',
                    data: {
                        '_token': $('input[name=_token]').val(),
                        'prodi': prodi,
                        'angkatan': angkatan,
                        'universitas': universitas
                    },
                    success: function(data) {
                        courseOptionsData = parseOptionsFromHtml(data);
                        var currentCourse = $('#courseSelect').val();
                        renderCourseOptions('');
                        if (currentCourse) {
                            var match = courseOptionsData.find(function(c) { return String(c.value).trim() === String(currentCourse).trim(); });
                            if (match) {
                                $('#courseDisplayInput').val(match.text);
                            }
                        }
                        if (callback) callback();
                    },
                    error: function() {
                        $('#courseOptionsList').html('<div class="combobox-empty-state text-danger">Gagal memuat mata kuliah</div>');
                    }
                });
            }

            @if ($userOtoritas == 'Dosen' || $userOtoritas == 'Kepala Program Studi' || $userOtoritas == 'Penjamin Mutu Program Studi')
                var userProdi = "{{ auth()->user()->id_prodiUser ?? '' }}";
                var universitas = $('#universitas').val();
                if (userProdi) {
                    loadAngkatan(userProdi, universitas);
                }
            @endif

            // ---------------- SEARCHABLE COMBOBOX UNTUK PILIH MAHASISWA (CPMK INDIVIDU) ----------------
            var cpmkMahasiswaOptionsData = [];
            var activeNpmIndex = -1;

            function disableNpmCombobox() {
                $('#npmSelect').val('');
                $('#npmDisplayInput').val('').prop('disabled', true);
                $('#npmToggleBtn').prop('disabled', true);
                $('input[name="nama"]').val('');
                cpmkMahasiswaOptionsData = [];
                $('#npmOptionsList').html('<div class="combobox-empty-state">Data mahasiswa tidak tersedia</div>');
                $('#npmDropdownMenu').hide();
                $('#npmComboboxWrapper').removeClass('is-open');
            }

            function enableNpmCombobox() {
                $('#npmDisplayInput').prop('disabled', false);
                $('#npmToggleBtn').prop('disabled', false);
            }

            function renderNpmComboboxOptions(filterText) {
                var $list = $('#npmOptionsList');
                $list.empty();
                activeNpmIndex = -1;

                if (!cpmkMahasiswaOptionsData || cpmkMahasiswaOptionsData.length === 0) {
                    $list.html('<div class="combobox-empty-state">Tidak ada data mahasiswa pada mata kuliah ini</div>');
                    return;
                }

                var query = (filterText || '').toLowerCase().trim();
                var filtered = cpmkMahasiswaOptionsData.filter(function(item) {
                    if (!query) return true;
                    return (item.npm && item.npm.toLowerCase().indexOf(query) !== -1) ||
                           (item.nama && item.nama.toLowerCase().indexOf(query) !== -1) ||
                           (item.text && item.text.toLowerCase().indexOf(query) !== -1);
                });

                if (filtered.length === 0) {
                    $list.html('<div class="combobox-empty-state">Tidak ada mahasiswa yang cocok dengan "' + filterText + '"</div>');
                    return;
                }

                var currentVal = $('#npmSelect').val();

                filtered.forEach(function(item, idx) {
                    var isSelected = (item.value === currentVal);
                    var $opt = $('<div>')
                        .addClass('combobox-option' + (isSelected ? ' is-selected' : ''))
                        .attr('data-value', item.value)
                        .attr('data-index', idx)
                        .html('<span class="font-monospace fw-semibold me-2">' + item.npm + '</span><span class="text-secondary">-</span> <span class="ms-1">' + item.nama + '</span>');

                    $opt.on('mousedown', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        selectNpmOption(item.value, item.text, item.nama);
                    });

                    $list.append($opt);
                });
            }

            function selectNpmOption(val, text, nama) {
                $('#npmSelect').val(val);
                $('#npmDisplayInput').val(text);
                $('input[name="nama"]').val(nama || '');
                $('#npmDropdownMenu').hide();
                $('#npmComboboxWrapper').removeClass('is-open');
                activeNpmIndex = -1;
            }

            function openNpmCombobox() {
                if (!cpmkMahasiswaOptionsData || cpmkMahasiswaOptionsData.length === 0) {
                    $('#npmOptionsList').html('<div class="combobox-empty-state">Tidak ada data mahasiswa</div>');
                    $('#npmComboboxWrapper').addClass('is-open');
                    $('#npmDropdownMenu').show();
                    return;
                }

                enableNpmCombobox();

                var currentQuery = $('#npmDisplayInput').val().trim();
                var currentVal = $('#npmSelect').val();
                var currentSelectedObj = cpmkMahasiswaOptionsData.find(function(s) { return s.value === currentVal; });

                if (currentSelectedObj && (currentSelectedObj.text === currentQuery || currentSelectedObj.value === currentQuery || currentSelectedObj.npm === currentQuery)) {
                    renderNpmComboboxOptions('');
                } else {
                    renderNpmComboboxOptions(currentQuery);
                }

                $('#npmComboboxWrapper').addClass('is-open');
                $('#npmDropdownMenu').show();
            }

            function closeNpmCombobox() {
                $('#npmDropdownMenu').hide();
                $('#npmComboboxWrapper').removeClass('is-open');
                activeNpmIndex = -1;

                var currentVal = $('#npmSelect').val();
                var currentText = $('#npmDisplayInput').val().trim();

                if (currentVal) {
                    var found = cpmkMahasiswaOptionsData.find(function(s) { return s.value === currentVal; });
                    if (found) {
                        $('#npmDisplayInput').val(found.text);
                        $('input[name="nama"]').val(found.nama);
                        return;
                    }
                }

                if (currentText && cpmkMahasiswaOptionsData.length > 0) {
                    var lowerText = currentText.toLowerCase();
                    var exactNpm = cpmkMahasiswaOptionsData.find(function(s) { return s.npm.toLowerCase() === lowerText; });
                    if (exactNpm) {
                        selectNpmOption(exactNpm.value, exactNpm.text, exactNpm.nama);
                        return;
                    }
                    var exactText = cpmkMahasiswaOptionsData.find(function(s) { return s.text.toLowerCase() === lowerText; });
                    if (exactText) {
                        selectNpmOption(exactText.value, exactText.text, exactText.nama);
                        return;
                    }
                    var exactNama = cpmkMahasiswaOptionsData.find(function(s) { return s.nama.toLowerCase() === lowerText; });
                    if (exactNama) {
                        selectNpmOption(exactNama.value, exactNama.text, exactNama.nama);
                        return;
                    }
                } else if (!currentText) {
                    $('#npmSelect').val('');
                    $('#npmDisplayInput').val('');
                    $('input[name="nama"]').val('');
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

                var exactMatch = cpmkMahasiswaOptionsData.find(function(s) {
                    return s.npm.toLowerCase() === lowerQuery || s.text.toLowerCase() === lowerQuery;
                });

                if (exactMatch) {
                    $('#npmSelect').val(exactMatch.value);
                    $('input[name="nama"]').val(exactMatch.nama);
                } else {
                    $('#npmSelect').val('');
                }

                renderNpmComboboxOptions(query);
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
                        var targetIdx = (activeNpmIndex >= 0 && activeNpmIndex < $options.length) ? activeNpmIndex : 0;
                        var selectedVal = $options.eq(targetIdx).attr('data-value');
                        var selectedItem = cpmkMahasiswaOptionsData.find(function(s) { return s.value === selectedVal; });
                        if (selectedItem) {
                            selectNpmOption(selectedItem.value, selectedItem.text, selectedItem.nama);
                        }
                    }
                } else if (e.key === 'Escape') {
                    closeNpmCombobox();
                }
            });

            // Klik di luar dropdown untuk menutup
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#npmComboboxWrapper').length) {
                    if ($('#npmDropdownMenu').is(':visible')) {
                        closeNpmCombobox();
                    }
                }
            });

            // Core Render function
            function renderMataKuliahData(result) {
                var angkatan = result.angkatan || '-';
                var prodi = result.prodi || '-';
                var completeCourseFormat = result.completeCourseFormat || '-';
                var imgSrc = result.imgSrc || '';
                var universitas = result.universitas || '-';
                var soalTerendah = result.soalTerendah || [];
                var allNpm = result.allNpm || [];
                var cpmkTmp = result.cpmkTmp || {};
                var cpmkResultAll = result.cpmkResultAll || [];
                var kodeMinAvgAngkatan = result.kodeMinAvgAngkatan;
                var kodeMaxAvgAngkatan = result.kodeMaxAvgAngkatan;

                // Update Hero Header
                $('#displayNamaCourse').text(completeCourseFormat);
                $('#prodiDataText').text(prodi);
                $('#angkatanDataText').text(angkatan);
                $('#universitasDataText').text(universitas);

                // Hidden input setup for Individu form
                $('#hasilvisualcpmk-mahasiswa input[name="angkatan"]').val(angkatan);
                $('#hasilvisualcpmk-mahasiswa input[name="prodi"]').val(prodi);
                $('#hasilvisualcpmk-mahasiswa input[name="course"]').val(completeCourseFormat);
                $('#hasilvisualcpmk-mahasiswa input[name="universitasImg"]').val(imgSrc);
                $('#hasilvisualcpmk-mahasiswa input[name="universitasCPMK"]').val(universitas);

                // Populate Combobox Options for Individu CPMK
                cpmkMahasiswaOptionsData = [];
                $('#npmSelect').val('');
                $('#npmDisplayInput').val('');
                $('input[name="nama"]').val('');

                if (allNpm && allNpm.length > 0) {
                    allNpm.forEach(function(item) {
                        var npmStr = String(item.npm || '').trim();
                        var namaStr = String(item.nama_mhs || '').trim();
                        cpmkMahasiswaOptionsData.push({
                            npm: npmStr,
                            nama: namaStr,
                            value: npmStr,
                            text: npmStr + ' - ' + namaStr
                        });
                    });
                    enableNpmCombobox();
                    renderNpmComboboxOptions('');
                } else {
                    disableNpmCombobox();
                }

                // Table Rincian Capaian CPMK Angkatan (Hanya Kode CPMK dan Skor)
                var $tableBody = $('#tableRincianCpmkAngkatan tbody');
                $tableBody.empty();

                var totalCpmk = cpmkResultAll.length;
                var sumAvg = 0;
                var countAvg = 0;

                if (cpmkResultAll && cpmkResultAll.length > 0) {
                    $.each(cpmkResultAll, function(index, itemCpmk) {
                        var cpmkId = itemCpmk.id;
                        var avgVal = (cpmkTmp[cpmkId] && cpmkTmp[cpmkId][0] !== undefined) ? Number(parseFloat(cpmkTmp[cpmkId][0]).toFixed(2)) : 0;
                        var cpmkKode = itemCpmk.kode;

                        if (avgVal > 0) {
                            sumAvg += avgVal;
                            countAvg++;
                        }

                        $tableBody.append(
                            '<tr>' +
                            '<td class="text-center fw-bold text-primary font-monospace">' + cpmkKode + '</td>' +
                            '<td class="text-center fw-semibold text-dark" style="font-size: 0.875rem;">' + avgVal + '</td>' +
                            '</tr>'
                        );
                    });
                } else {
                    $tableBody.append('<tr><td colspan="2" class="text-center text-muted py-3">Tidak ada data CPMK</td></tr>');
                }

                var overallAvg = countAvg > 0 ? Number((sumAvg / countAvg).toFixed(2)) : 0;
                $('#metricAvgScore').text(overallAvg + ' / 100');

                var maxKode = (kodeMaxAvgAngkatan && kodeMaxAvgAngkatan[3]) ? kodeMaxAvgAngkatan[3] : '-';
                var maxScore = (kodeMaxAvgAngkatan && kodeMaxAvgAngkatan[0] !== undefined) ? Number(parseFloat(kodeMaxAvgAngkatan[0]).toFixed(2)) : 0;
                $('#metricHighestCpmk').html(maxKode + ' <span class="small fs-6 text-muted">(' + maxScore + ')</span>');

                var minKode = (kodeMinAvgAngkatan && kodeMinAvgAngkatan[3]) ? kodeMinAvgAngkatan[3] : '-';
                var minScore = (kodeMinAvgAngkatan && kodeMinAvgAngkatan[0] !== undefined) ? Number(parseFloat(kodeMinAvgAngkatan[0]).toFixed(2)) : 0;
                $('#metricLowestCpmk').html(minKode + ' <span class="small fs-6 text-muted">(' + minScore + ')</span>');

                // Deskripsi CPMK (Structured Cards)
                var labelContainer = $('#labelContainer');
                labelContainer.empty();
                if (cpmkResultAll && cpmkResultAll.length > 0) {
                    $('#cpmkBadgeCount').text(cpmkResultAll.length + ' CPMK').show();
                    cpmkResultAll.forEach(function(item) {
                        var itemHtml = 
                            '<div class="cpl-desc-card">' +
                                '<span class="badge bg-primary text-white cpl-desc-badge">' + item.kode + '</span>' +
                                '<span class="cpl-desc-text">' + item.judul + '</span>' +
                            '</div>';
                        labelContainer.append(itemHtml);
                    });
                } else {
                    $('#cpmkBadgeCount').hide();
                    labelContainer.append('<div class="text-muted small">Tidak ada deskripsi CPMK yang tersedia.</div>');
                }

                $('#collapseCpmkDescriptions').hide();
                $('#iconCpmkDescCollapse').removeClass('bi-chevron-up').addClass('bi-chevron-down');

                // Soal Terendah Table
                var tableSoalBody = $('#soalTerendahTable tbody');
                tableSoalBody.empty();
                if (soalTerendah && soalTerendah.length > 0) {
                    for (var i = 0; i < soalTerendah.length; i++) {
                        var soalText = (soalTerendah[i].soal && soalTerendah[i].soal !== 'null') ? soalTerendah[i].soal : '-';
                        var rowHtml = '<tr>' +
                            '<td class="text-center text-muted fw-semibold">' + (i + 1) + '</td>' +
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
                    tableSoalBody.append('<tr><td colspan="3" class="text-center text-muted py-3">Tidak ada data soal</td></tr>');
                }

                // RADAR CPMK Angkatan Chart
                var dataCapaianCpmkAvg = Object.values(cpmkTmp).map(item => Number(parseFloat(item[0]).toFixed(2)));
                var dataCapaianCpmkMin = Object.values(cpmkTmp).map(item => Number(parseFloat(item[1]).toFixed(2)));
                var dataCapaianCpmkMax = Object.values(cpmkTmp).map(item => Number(parseFloat(item[2]).toFixed(2)));
                var labelsCapaianCpmk = Object.values(cpmkTmp).map(item => item[3]);

                var canvas = document.getElementById('radarChartAngkatan');
                if (canvas) {
                    var ctx = canvas.getContext('2d');
                    if (windowRadarChartInstance) {
                        windowRadarChartInstance.destroy();
                    }

                    windowRadarChartInstance = new Chart(ctx, {
                        type: 'radar',
                        data: {
                            labels: labelsCapaianCpmk,
                            datasets: [{
                                    label: 'Skor CPMK',
                                    data: dataCapaianCpmkAvg,
                                    backgroundColor: 'rgba(31, 59, 179, 0.2)',
                                    borderColor: 'rgba(31, 59, 179, 1)',
                                    borderWidth: 2.5,
                                    pointBackgroundColor: 'rgba(31, 59, 179, 1)',
                                    pointRadius: 4,
                                    pointHoverRadius: 6
                                },
                                {
                                    label: 'Min CPMK',
                                    data: dataCapaianCpmkMin,
                                    backgroundColor: 'rgba(216, 33, 33, 0.05)',
                                    borderColor: 'rgba(216, 33, 33, 0.8)',
                                    borderWidth: 1.5,
                                    borderDash: [4, 4],
                                    pointBackgroundColor: 'rgba(216, 33, 33, 1)',
                                    pointRadius: 3,
                                    pointHoverRadius: 5
                                },
                                {
                                    label: 'Max CPMK',
                                    data: dataCapaianCpmkMax,
                                    backgroundColor: 'rgba(33, 216, 95, 0.05)',
                                    borderColor: 'rgba(33, 216, 95, 0.8)',
                                    borderWidth: 1.5,
                                    borderDash: [4, 4],
                                    pointBackgroundColor: 'rgba(33, 216, 95, 1)',
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
                            }
                        }
                    });
                }

                $('#btnPrintPdf').prop('disabled', false);
            }

            // AJAX Fetch & Render wrapper
            function fetchAndRenderMataKuliah(prodi, angkatan, course, isAutoLoad) {
                var $btnSubmit = $('#btnSubmitVisual');
                if (!isAutoLoad) {
                    $btnSubmit.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memuat Data...');
                }

                var universitas = $('#universitas').val();
                var imgSrc = $('#universitas-img-path').val();

                $.ajax({
                    url: "{{ route($currentPrefix . 'visualisasi.hasilvisual-mahasiswaMataKuliah') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        prodi: prodi,
                        angkatan: angkatan,
                        course: course,
                        universitas: universitas,
                        imgSrc: imgSrc
                    },
                    dataType: 'json',
                    success: function(response) {
                        $btnSubmit.prop('disabled', false).html(originalBtnHtml);

                        if (response.success && response.result) {
                            // Simpan state aktif ke sessionStorage & URL
                            sessionStorage.setItem('active_mk_course', course);
                            sessionStorage.setItem('active_mk_angkatan', angkatan);
                            if (prodi) sessionStorage.setItem('active_mk_prodi', prodi);

                            var newUrl = new URL(window.location.href);
                            newUrl.searchParams.set('course', course);
                            newUrl.searchParams.set('angkatan', angkatan);
                            if (prodi) newUrl.searchParams.set('prodi', prodi);
                            window.history.replaceState({}, '', newUrl.toString());

                            $('#tugas').hide();
                            $('#visualContainer').show();

                            renderMataKuliahData(response.result);

                            if (!isAutoLoad) {
                                $('html, body').animate({
                                    scrollTop: $('#visualContainer').offset().top - 80
                                }, 300);
                            }
                        } else {
                            if (isAutoLoad) {
                                $('#visualContainer').hide();
                                $('#tugas').show();
                            } else {
                                alert(response.message || 'Tidak ada data yang ditemukan untuk mata kuliah ini.');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        $btnSubmit.prop('disabled', false).html(originalBtnHtml);
                        if (!isAutoLoad) {
                            alert('Terjadi kesalahan saat memuat data mata kuliah: ' + (xhr.responseJSON ? xhr.responseJSON.message : error));
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
                var course = $('#courseSelect').val();

                if (!course || !angkatan || !prodi) {
                    alert('Mohon lengkapi pilihan Program Studi, Angkatan, dan Mata Kuliah!');
                    return;
                }

                fetchAndRenderMataKuliah(prodi, angkatan, course, false);
            });

            // Helper untuk mensinkronisasi data dropdown form filter di background
            function syncFilterFormInputs(prodi, angkatan, course) {
                var universitas = $('#universitas').val();
                if (prodi) {
                    $('#prodiForm').val(prodi);
                    var selectedProdiObj = prodiOptionsData.find(function(p) { return String(p.value).trim() === String(prodi).trim(); });
                    if (selectedProdiObj) {
                        $('#prodiDisplayInput').val(selectedProdiObj.text);
                    }
                    if (angkatan) {
                        $('#angkatanForm').val(angkatan);
                        var selectedAngkatanObj = angkatanOptionsData.find(function(a) { return String(a.value).trim() === String(angkatan).trim(); });
                        if (selectedAngkatanObj) {
                            $('#angkatanDisplayInput').val(selectedAngkatanObj.text);
                        }
                    }
                    if (course) {
                        $('#courseSelect').val(course);
                        enableCourseCombobox();
                        var selectedCourseObj = courseOptionsData.find(function(c) { return String(c.value).trim() === String(course).trim(); });
                        if (selectedCourseObj) {
                            $('#courseDisplayInput').val(selectedCourseObj.text);
                        }
                    }
                    loadAngkatan(prodi, universitas, function() {
                        if (angkatan) {
                            $('#angkatanForm').val(angkatan);
                            var selectedAngkatanObj = angkatanOptionsData.find(function(a) { return String(a.value).trim() === String(angkatan).trim(); });
                            if (selectedAngkatanObj) {
                                $('#angkatanDisplayInput').val(selectedAngkatanObj.text);
                            }
                            loadCourses(prodi, angkatan, universitas, function() {
                                if (course) {
                                    $('#courseSelect').val(course);
                                    enableCourseCombobox();
                                    var selectedCourseObj = courseOptionsData.find(function(c) { return String(c.value).trim() === String(course).trim(); });
                                    if (selectedCourseObj) {
                                        $('#courseDisplayInput').val(selectedCourseObj.text);
                                    }
                                }
                            }, true);
                        }
                    }, true);
                }
            }

            // Inisialisasi: Cek parameter URL (mendukung refresh F5 pada halaman output)
            var urlParams = new URLSearchParams(window.location.search);
            var savedCourse = urlParams.get('course');
            var savedAngkatan = urlParams.get('angkatan');
            var savedProdi = urlParams.get('prodi') || $('#prodiForm').val() || "{{ auth()->user()->id_prodiUser ?? '' }}";

            if (savedCourse && savedAngkatan) {
                $('#tugas').hide();
                $('#visualContainer').show();
                syncFilterFormInputs(savedProdi, savedAngkatan, savedCourse);
                fetchAndRenderMataKuliah(savedProdi, savedAngkatan, savedCourse, true);
            } else {
                $('#antiFlickerStyle').remove();
                $('#tugas').show();
                $('#visualContainer').hide();
            }

            // Back to filter button (Ganti Mata Kuliah)
            $('#btnBackToFilter').on('click', function() {
                var activeProdi = sessionStorage.getItem('active_mk_prodi') || $('#prodiForm').val() || "{{ auth()->user()->id_prodiUser ?? '' }}";
                var activeAngkatan = sessionStorage.getItem('active_mk_angkatan') || $('#angkatanForm').val() || '';
                var activeCourse = sessionStorage.getItem('active_mk_course') || $('#courseSelect').val() || '';

                $('#antiFlickerStyle').remove();

                var cleanUrl = new URL(window.location.href);
                cleanUrl.searchParams.delete('course');
                cleanUrl.searchParams.delete('angkatan');
                cleanUrl.searchParams.delete('prodi');
                window.history.replaceState({}, '', cleanUrl.toString());

                // Pastikan form filter terisi lengkap dengan opsi yang relevan
                if (activeProdi) {
                    syncFilterFormInputs(activeProdi, activeAngkatan, activeCourse);
                }

                $('#visualContainer').fadeOut(250, function() {
                    $('#tugas').fadeIn(250);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            });

            // Toggle Handler untuk Petunjuk Membaca Diagram
            $('#btnToggleGuideCpmkBatch').off('click').on('click', function(e) {
                e.preventDefault();
                $('#guideCpmkBatch').slideToggle(200);
            });

            // Toggle Handler untuk Deskripsi CPMK (Bisa via Header bar & Tombol)
            $('#headerToggleCpmkDesc').off('click').on('click', function(e) {
                e.preventDefault();
                $('#collapseCpmkDescriptions').slideToggle(200, function() {
                    if ($(this).is(':visible')) {
                        $('#iconCpmkDescCollapse').removeClass('bi-chevron-down').addClass('bi-chevron-up');
                    } else {
                        $('#iconCpmkDescCollapse').removeClass('bi-chevron-up').addClass('bi-chevron-down');
                    }
                });
            });

            // Validasi Individu Form
            $('#hasilvisualcpmk-mahasiswa').on('submit', function(event) {
                var npmSelect = $('#npmSelect').val();
                if (!npmSelect) {
                    alert('Mohon pilih Mahasiswa (NPM) terlebih dahulu!');
                    event.preventDefault();
                    return;
                }
            });

            // PDF Download Handler
            $('#btnPrintPdf').on('click', function() {
                let radarChartAngkatan = document.getElementById('radarChartAngkatan');
                let radarChartAngkatanImg = radarChartAngkatan ? radarChartAngkatan.toDataURL() : null;

                let descriptions = [];
                document.querySelectorAll("#labelContainer .cpl-desc-card").forEach(card => {
                    let badge = card.querySelector(".cpl-desc-badge")?.textContent.trim() || '';
                    let text = card.querySelector(".cpl-desc-text")?.textContent.trim() || '';
                    descriptions.push(badge + ': ' + text);
                });

                let soalTerendah = [];
                document.querySelectorAll("#soalTerendahTable tbody tr").forEach(row => {
                    let cols = row.querySelectorAll("td");
                    if (cols.length >= 3 && !row.querySelector("td[colspan]")) {
                        soalTerendah.push({
                            no: cols[0].textContent.trim(),
                            types_of_assessment: cols[1].textContent.trim(),
                            question: cols[2]?.textContent.trim(),
                        });
                    }
                });

                let rincianCpmkAngkatanArr = [];
                document.querySelectorAll("#tableRincianCpmkAngkatan tbody tr").forEach(row => {
                    let cols = row.querySelectorAll("td");
                    if (cols.length >= 2 && !row.querySelector("td[colspan]")) {
                        rincianCpmkAngkatanArr.push({
                            kode: cols[0].textContent.trim(),
                            avgScore: cols[1].textContent.trim()
                        });
                    }
                });

                let angkatan = $('#angkatanDataText').text().trim();
                let prodi = $('#prodiDataText').text().trim();
                let universitas = $('#universitasDataText').text().trim() || "{{ $universitas->nama ?? '' }}";
                let course = $('#displayNamaCourse').text().trim() || $('input[name="course"]').val().trim();

                fetch("{{ route($currentPrefix . 'visualisasi.generate-pdfVisualMataKuliah') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    body: JSON.stringify({
                        radarChartAngkatanImg,
                        rincianCpmkAngkatan: rincianCpmkAngkatanArr,
                        descriptions,
                        soalTerendah,
                        angkatan,
                        prodi,
                        universitas,
                        course,
                    }),
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => { throw new Error(`HTTP ${response.status}: ${text}`); });
                    }
                    return response.blob();
                })
                .then(blob => {
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `Laporan Visualisasi Mata Kuliah - ${course}.pdf`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                })
                .catch(error => {
                    console.error("Error:", error.message);
                });
            });
        });
    </script>
@endsection
