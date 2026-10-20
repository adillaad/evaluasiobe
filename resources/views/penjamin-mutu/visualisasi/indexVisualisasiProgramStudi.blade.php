@php
    $routePrefix = [
        'Penjamin Mutu Universitas' => ['prefix' => 'penjamin-mutu.universitas.'],
        'Penjamin Mutu Fakultas' => ['prefix' => 'penjamin-mutu.fakultas.'],
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Koordinator Program Studi' => ['prefix' => 'koordinator-program-studi.'],
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
        /* Smooth layout fixes */
        html,
        body,
        .container-scroller,
        .page-body-wrapper,
        .main-panel,
        .content-wrapper,
        .content-wrapper > .row {
            overflow: visible !important;
        }

        /* Modern Dashboard Cards */
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

        /* Section Titles & Subtitles */
        .section-title {
            font-size: 1.0rem !important;
            font-weight: 700 !important;
            color: #1F3BB3;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            letter-spacing: -0.01em;
            margin-bottom: 0;
        }

        .section-title i {
            font-size: 1.1rem !important;
            color: #1F3BB3;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .section-subtitle {
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            color: #1e293b;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            letter-spacing: -0.01em;
            margin-bottom: 0;
        }

        .section-subtitle i {
            font-size: 1.0rem !important;
        }

        /* Hero Profile Card (Compact & Sleek) */
        .student-profile-hero {
            position: relative !important;
            top: auto !important;
            z-index: 1 !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #1F3BB3 !important;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
            padding: 10px 18px;
            margin-bottom: 16px;
        }

        .student-label-tag {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #1F3BB3;
            margin-bottom: 2px;
        }

        .student-name-text {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
            margin-bottom: 3px;
            letter-spacing: -0.01em;
        }

        .student-meta-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px 12px;
        }

        .meta-item {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.78rem;
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

        /* Interactive Year Badge */
        .badge-interactive-year {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 600;
            background: #eff6ff;
            color: #1F3BB3;
            border: 1px solid #bfdbfe;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .badge-interactive-year:hover {
            background: #dbeafe;
        }

        .badge-close-icon {
            font-size: 0.75rem;
            opacity: 0.7;
        }

        .badge-interactive-year:hover .badge-close-icon {
            opacity: 1;
            color: #dc2626;
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
            font-weight: 700;
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
            font-size: 0.85rem;
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

        /* Interactive Year Column in Tables (Mirip Gambar) */
        .interactive-year-th {
            cursor: pointer;
            transition: all 0.15s ease;
            user-select: none;
        }

        .interactive-year-th:hover {
            background-color: #e0e7ff !important;
            color: #1F3BB3 !important;
        }

        .interactive-year-th.interactive-year-active,
        .interactive-year-th.active-year-col {
            background-color: #1F3BB3 !important;
            color: #ffffff !important;
        }

        .interactive-year-td {
            transition: all 0.15s ease;
            cursor: pointer;
            color: #1e293b;
        }

        .interactive-year-td.is-zero-cell {
            color: #64748b;
        }

        .interactive-year-td:hover {
            background-color: #eff6ff !important;
        }

        .interactive-year-td.interactive-year-active,
        .interactive-year-td.active-year-col {
            background-color: #e0e7ff !important;
            color: #1F3BB3 !important;
            font-weight: 700 !important;
        }

        .interactive-year-tf {
            cursor: pointer;
            transition: all 0.15s ease;
            color: #1F3BB3;
        }

        .interactive-year-tf.is-zero-cell {
            color: #64748b;
        }

        .interactive-year-tf:hover {
            background-color: #dbeafe !important;
        }

        .interactive-year-tf.interactive-year-active,
        .interactive-year-tf.active-year-col {
            background-color: #1F3BB3 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
        }

        /* Guide Banner */
        .modern-guide-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #1F3BB3;
            border-radius: 8px;
            padding: 14px 18px;
            line-height: 1.5;
        }

        /* Structured CPL Description Cards (Grid Mirip Gambar) */
        .cpl-desc-container {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #ffffff;
            padding: 10px;
            max-height: 230px;
            overflow-y: auto;
        }

        .cpl-desc-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            padding: 2px;
        }

        @media (max-width: 1199px) {
            .cpl-desc-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 767px) {
            .cpl-desc-grid {
                grid-template-columns: 1fr;
            }
        }

        .cpl-desc-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 12px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            transition: all 0.15s ease;
        }

        .cpl-desc-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .cpl-desc-badge {
            background-color: #004c87 !important;
            color: #ffffff !important;
            font-size: 0.72rem !important;
            font-weight: 700;
            border-radius: 4px !important;
            padding: 3px 7px !important;
            white-space: nowrap;
            letter-spacing: 0.02em;
            line-height: 1.2;
        }

        .cpl-desc-text {
            font-size: 0.78rem !important;
            line-height: 1.4;
            color: #334155;
            font-weight: 500;
        }

        .badge-cpl-count {
            display: inline-flex;
            align-items: center;
            padding: 2px 10px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748b;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 9999px;
        }

        .btn-toggle-cpl-desc {
            width: 32px;
            height: 30px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-toggle-cpl-desc:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #1e293b;
        }

        /* Form Controls & Buttons (Consistent with Visualisasi Angkatan) */
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

    </style>

    <div class="container-fluid px-3 my-2">
        @if (isset($fakultasCplData) && $fakultasCplData)

            {{-- ========================================================================= --}}
            {{-- 1. INPUT FILTER FORM (Halaman Awal Pemilihan Program Studi) --}}
            {{-- ========================================================================= --}}
            <div id="tugas">
                <div class="card modern-card mb-4">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <h5 class="section-title mb-1">
                                <i class="bi bi-mortarboard-fill"></i> Visualisasi CPL Per Program Studi
                            </h5>
                            <p class="text-muted small mb-0">Silahkan pilih data program studi untuk menampilkan visualisasi OBE.</p>
                        </div>

                        <form id="hasilVisual" method="POST" action="javascript:void(0);">
                            @csrf
                            <input type="hidden" name="universitas" id="universitas" value="{{ $universitas->id ?? '' }}">

                            <div class="row g-3">
                                @if ($isUnivLevel && !empty($allFakultas) && count($allFakultas) > 1)
                                    <div class="col-md-6">
                                        <label class="modern-label">
                                            <i class="bi bi-building text-primary me-1"></i> Fakultas <span class="text-danger">*</span>
                                        </label>
                                        <select id="fakultasSelect" class="form-control form-select modern-select" onchange="window.location.href='{{ url()->current() }}?fakultas_id=' + this.value;">
                                            @foreach ($allFakultas as $fak)
                                                <option value="{{ $fak->id }}" {{ (string)$fakultasId === (string)$fak->id ? 'selected' : '' }}>{{ $fak->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="modern-label">
                                            <i class="bi bi-mortarboard text-primary me-1"></i> Program Studi <span class="text-danger">*</span>
                                        </label>
                                        <select id="prodiForm" class="form-control form-select modern-select" name="prodi" required>
                                            <option value="">Pilih Program Studi</option>
                                            @foreach ($prodiList as $p)
                                                <option value="{{ $p['id'] }}">
                                                    {{ $p['nama'] }} ({{ $p['jenjang'] ?? 'S1' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <div class="col-md-6">
                                        <label class="modern-label">
                                            <i class="bi bi-mortarboard text-primary me-1"></i> Program Studi <span class="text-danger">*</span>
                                        </label>
                                        <select id="prodiForm" class="form-control form-select modern-select" name="prodi" required>
                                            <option value="">Pilih Program Studi</option>
                                            @foreach ($prodiList as $p)
                                                <option value="{{ $p['id'] }}">
                                                    {{ $p['nama'] }} ({{ $p['jenjang'] ?? 'S1' }})
                                                </option>
                                            @endforeach
                                        </select>
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
            {{-- 2. DEDICATED RESULTS VIEW (Tampilan Hasil Visualisasi Program Studi) --}}
            {{-- ========================================================================= --}}
            <div id="visualContainer" style="display: none;">

                {{-- HERO PROFILE CARD (Compact & Sleek) --}}
                <div class="student-profile-hero">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="me-auto">
                            <div class="student-label-tag">VISUALISASI CPL PER PROGRAM STUDI</div>
                            <h4 class="student-name-text" id="displayNamaProdi">-</h4>
                            <div class="student-meta-row">
                                <div class="meta-item">
                                    <span class="meta-label">Jenjang:</span>
                                    <span class="meta-value font-monospace text-primary fw-bold" id="displayJenjangProdi">-</span>
                                </div>
                                <span class="meta-pipe">|</span>
                                <div class="meta-item">
                                    <span class="meta-label">Fakultas:</span>
                                    <span class="meta-value text-dark" id="displayFakultasProdi">{{ $fakultasCplData['fakultas']->nama ?? 'Fakultas' }}</span>
                                </div>
                                <span class="meta-pipe">|</span>
                                <div class="meta-item">
                                    <span class="meta-label">Universitas:</span>
                                    <span class="meta-value text-secondary" id="displayUniversitasProdi">{{ $universitas->nama ?? 'Universitas Lampung' }}</span>
                                </div>
                                <span class="meta-pipe">|</span>
                                <div class="meta-item">
                                    <span class="meta-label">Kategori Kurikulum:</span>
                                    <span class="meta-value text-dark" id="displayKategoriProdi">-</span>
                                </div>
                                <span class="meta-pipe">|</span>
                                <div class="meta-item">
                                    <span class="meta-label">Mahasiswa Dinilai:</span>
                                    <span class="meta-value text-dark" id="displayMhsProdi">-</span>
                                </div>
                            </div>
                        </div>

                        {{-- Toolbar Tombol Aksi --}}
                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            <button type="button" class="modern-btn-outline btn-sm px-3" id="btnBackToFilter" style="height: 32px; font-size: 0.82rem;">
                                <i class="bi bi-arrow-left me-1"></i> Ganti Program Studi
                            </button>
                            <button id="btnPrintPdf" type="button" class="modern-btn-primary btn-sm px-3" style="height: 32px; font-size: 0.82rem;">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Unduh PDF
                            </button>
                        </div>
                    </div>
                </div>

                {{-- CARD 1: Achievement of CPL Scores (Skor Capaian CPL Disusun Per Tahun) --}}
                <div class="card modern-card mb-4">
                    <div class="modern-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="section-title mb-0 d-flex align-items-center flex-wrap gap-2">
                            <span><i class="bi bi-bar-chart-line"></i> Achievement of CPL Scores</span>
                            <span id="badgeYearSkorCpl" class="badge-interactive-year" style="display: none;" title="Klik untuk menghapus filter dan kembali ke tampilan awal">
                                <i class="bi bi-calendar-event"></i>
                                <span class="badge-year-text">2024</span>
                                <i class="bi bi-x-lg badge-close-icon"></i>
                            </span>
                        </h5>
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#guideCplScores" aria-expanded="false" style="border-radius: 6px; font-size: 12px; padding: 4px 10px;">
                            <i class="bi bi-info-circle me-1"></i> Petunjuk Membaca Diagram
                        </button>
                    </div>
                    <div class="card-body p-4">
                        {{-- Petunjuk Collapse --}}
                        <div class="collapse mb-3" id="guideCplScores">
                            <div class="modern-guide-box small">
                                <h6 class="fw-bold text-primary mb-1"><i class="bi bi-compass me-1"></i> Panduan Membaca Diagram & Tabel Skor CPL:</h6>
                                <ul class="mb-0 ps-3 text-muted">
                                    <li><strong>Bentuk Jaring (Radar):</strong> Setiap sudut mewakili satu <strong>CPL (Capaian Pembelajaran Lulusan)</strong> pada kurikulum program studi.</li>
                                    <li><span class="badge" style="background-color: #1F3BB3; color:#fff;">Skor Capaian CPL</span>: Skor rata-rata perolehan asesmen mahasiswa (skala 0 &ndash; 100).</li>
                                    <li><span class="badge" style="background-color: #10b981; color:#fff;">Max Skor CPL</span>: Skor capaian tertinggi mahasiswa pada butir CPL di program studi.</li>
                                    <li><span class="badge" style="background-color: #ef4444; color:#fff;">Min Skor CPL</span>: Skor capaian terendah mahasiswa pada butir CPL di program studi.</li>
                                    <li><strong>Tabel Skor Capaian CPL Per Tahun:</strong> Menampilkan skor capaian kompetensi CPL yang disusun per tahun akademik. <em>Klik pada kolom tahun di tabel untuk melihat diagram radar pada tahun tersebut.</em></li>
                                </ul>
                            </div>
                        </div>

                        {{-- Diagram Radar Canvas --}}
                        <div class="d-flex justify-content-center mb-4">
                            <div style="width: 100%; max-width: 600px;">
                                <canvas id="radarChart"></canvas>
                            </div>
                        </div>

                        {{-- Tabel Rincian Skor Capaian CPL Per Tahun --}}
                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                <h6 class="section-subtitle mb-0"><i class="bi bi-table text-primary me-2"></i> Rincian Skor Capaian CPL:</h6>
                            </div>
                            <div class="modern-table-container mb-4">
                                <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                                    <table class="table modern-table mb-0" id="tableRincianCplScores">
                                        <thead>
                                            <tr id="theadRincianCplScores">
                                                <th style="min-width: 120px;">Kode CPL</th>
                                                @foreach ($availableYears as $yr)
                                                    <th class="text-center interactive-year-th" data-year="{{ $yr }}" style="min-width: 85px;" title="Klik untuk melihat diagram {{ $yr }}">{{ $yr }}</th>
                                                @endforeach
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

                            {{-- Deskripsi CPL (Bisa di Buka Tutup Mirip Gambar) --}}
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-4 pt-3 border-top" id="headerToggleCplDesc" style="cursor: pointer; user-select: none;">
                                <h6 class="keterangan fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size: 0.95rem;">
                                    <i class="bi bi-card-text text-primary" style="font-size: 1.15rem;"></i> Descriptions (Deskripsi CPL)
                                    <span id="cplBadgeCount" class="badge-cpl-count ms-1"></span>
                                </h6>
                                <button class="btn-toggle-cpl-desc" type="button" id="btnToggleCplDesc" title="Buka / Tutup Deskripsi CPL">
                                    <i class="bi bi-chevron-up" id="iconCplDescCollapse"></i>
                                </button>
                            </div>
                            <div class="mt-2" id="collapseCplDescriptions">
                                <div class="cpl-desc-container">
                                    <div id="labelContainer" class="cpl-desc-grid">
                                        {{-- Dinamis via JS --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: Achievement of CPL (Persentase Ketercapaian CPL Disusun Per Tahun) --}}
                <div class="card modern-card mb-4">
                    <div class="modern-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="section-title mb-0 d-flex align-items-center flex-wrap gap-2">
                            <span><i class="bi bi-pie-chart"></i> Achievement of CPL</span>
                            <span id="badgeYearKetercapaianCpl" class="badge-interactive-year" style="display: none;" title="Klik untuk menghapus filter dan kembali ke tampilan awal">
                                <i class="bi bi-calendar-event"></i>
                                <span class="badge-year-text">2024</span>
                                <i class="bi bi-x-lg badge-close-icon"></i>
                            </span>
                        </h5>
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#guideCplCourses" aria-expanded="false" style="border-radius: 6px; font-size: 12px; padding: 4px 10px;">
                            <i class="bi bi-info-circle me-1"></i> Petunjuk Membaca Diagram
                        </button>
                    </div>
                    <div class="card-body p-4">
                        {{-- Petunjuk Collapse --}}
                        <div class="collapse mb-3" id="guideCplCourses">
                            <div class="modern-guide-box small">
                                <h6 class="fw-bold text-primary mb-1"><i class="bi bi-compass me-1"></i> Panduan Membaca Diagram & Tabel Ketercapaian CPL (%):</h6>
                                <ul class="mb-0 ps-3 text-muted">
                                    <li><span class="badge" style="background-color: #1F3BB3; color:#fff;">CPL (Program Studi)</span>: Persentase ketercapaian per butir CPL berdasarkan kelulusan mata kuliah pendukung.</li>
                                    <li><span class="badge" style="background-color: #10b981; color:#fff;">Max CPL</span>: Persentase ketercapaian tertinggi mahasiswa pada butir CPL di program studi.</li>
                                    <li><span class="badge" style="background-color: #ef4444; color:#fff;">Min CPL</span>: Persentase ketercapaian terendah mahasiswa pada butir CPL di program studi.</li>
                                    <li><strong>Tabel Ketercapaian CPL Per Tahun (%):</strong> Menampilkan persentase kelulusan mata kuliah pendukung CPL secara kumulatif setiap tahun akademik. <em>Klik pada kolom tahun di tabel untuk melihat diagram pada tahun tersebut.</em></li>
                                </ul>
                            </div>
                        </div>

                        {{-- Diagram Radar Canvas --}}
                        <div class="d-flex justify-content-center mb-4">
                            <div style="width: 100%; max-width: 600px;">
                                <canvas id="radarChartCapaianCpl"></canvas>
                            </div>
                        </div>

                        {{-- Tabel Rincian Ketercapaian CPL (%) Per Tahun --}}
                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                <h6 class="section-subtitle mb-0"><i class="bi bi-table text-primary me-2"></i> Rincian Ketercapaian CPL (%):</h6>
                            </div>
                            <div class="modern-table-container mb-4">
                                <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                                    <table class="table modern-table mb-0" id="tableKomparasiCplAngkatan">
                                        <thead>
                                            <tr id="theadKomparasiCplAngkatan">
                                                <th style="min-width: 120px;">Kode CPL</th>
                                                @foreach ($availableYears as $yr)
                                                    <th class="text-center interactive-year-th" data-year="{{ $yr }}" style="min-width: 85px;" title="Klik untuk melihat diagram {{ $yr }}">{{ $yr }}</th>
                                                @endforeach
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
            var allAvailableYears = @json($availableYears ?? []);
            var allProdiStats = @json($fakultasCplData['prodi_stats'] ?? []);
            var prodiCplYearlyData = @json($prodiCplYearlyData ?? []);

            var currentActiveProdi = null;
            var currentActiveCplData = null;
            var currentProdiId = null;

            var radarChartPerSoalInstance = null;
            var radarChartCapaianCplInstance = null;

            var activeYearSkorCpl = null;
            var activeYearKetercapaianCpl = null;

            $(function() {
                initSelectionForm();
                initInteractiveYearHandlers();
                initCplDescriptionToggle();
                initPdfPrintHandler();

                // Cek URL Query Parameter (untuk F5 refresh dan bookmark)
                var urlParams = new URLSearchParams(window.location.search);
                var savedProdi = urlParams.get('prodi') || urlParams.get('prodi_id');

                if (savedProdi && prodiCplYearlyData[savedProdi]) {
                    $('#prodiForm').val(savedProdi);
                    renderVisualisasiProdi(savedProdi, true);
                } else {
                    $('#tugas').show();
                    $('#visualContainer').hide();
                }
            });

            // ─── 1. FORM SUBMIT & NAVIGATION ───
            function initSelectionForm() {
                $('#hasilVisual').on('submit', function(e) {
                    e.preventDefault();
                    var pId = $('#prodiForm').val();
                    if (!pId) {
                        alert('Silakan pilih Program Studi terlebih dahulu.');
                        $('#prodiForm').focus();
                        return;
                    }
                    renderVisualisasiProdi(pId, false);
                });

                // Tombol Kembali / Ganti Program Studi
                $('#btnBackToFilter').on('click', function() {
                    $('#navVisualisasiSlot').empty();

                    var cleanUrl = new URL(window.location.href);
                    cleanUrl.searchParams.delete('prodi');
                    cleanUrl.searchParams.delete('prodi_id');
                    window.history.replaceState({}, '', cleanUrl.toString());

                    $('#visualContainer').fadeOut(250, function() {
                        $('#tugas').slideDown(300);
                        $('html, body').animate({
                            scrollTop: $('#tugas').offset().top - 70
                        }, 300);
                    });
                });
            }

            function mountNavbarControls() {
                if ($('#navVisualisasiSlot').length) {
                    if (!$('#navbarVisualisasiControls').length) {
                        var currentText = currentActiveProdi ? currentActiveProdi.nama : 'Pilih Program Studi';
                        var html = '' +
                            '<div class="d-flex align-items-center gap-2" id="navbarVisualisasiControls">' +
                            '    <div class="nav-combobox-wrapper" id="navProdiComboboxWrapper" style="width: 210px;">' +
                            '        <input type="text" id="navProdiDisplayInput" class="form-control nav-combobox-input" placeholder="Program Studi" autocomplete="off" title="Pilih Program Studi" style="cursor: pointer;" value="' + currentText + '">' +
                            '        <input type="hidden" id="navProdiValue" value="' + (currentProdiId || '') + '">' +
                            '        <button type="button" class="nav-combobox-toggle-btn" tabindex="-1" id="navProdiToggleBtn" title="Daftar Program Studi"><i class="bi bi-chevron-down"></i></button>' +
                            '        <div class="nav-combobox-dropdown-menu" id="navProdiDropdownMenu" style="display: none; min-width: 230px;">' +
                            '            <div class="nav-combobox-options-list" id="navProdiOptionsList"></div>' +
                            '        </div>' +
                            '    </div>' +
                            '</div>';
                        $('#navVisualisasiSlot').html(html);
                    }
                    syncNavbarProdiOptions('');
                }
            }

            function syncNavbarProdiOptions(filterText) {
                if ($('#navProdiDisplayInput').length) {
                    var currentText = currentActiveProdi ? currentActiveProdi.nama : '';
                    var currentVal = currentProdiId || '';
                    $('#navProdiValue').val(currentVal);
                    if (filterText === undefined || filterText === '') {
                        $('#navProdiDisplayInput').val(currentText).attr('title', currentText);
                    }

                    var $list = $('#navProdiOptionsList');
                    $list.empty();

                    var q = (filterText || '').toLowerCase().trim();
                    var filtered = allProdiStats.filter(function(item) {
                        if (!q) return true;
                        return item.nama.toLowerCase().indexOf(q) !== -1 ||
                               (item.jenjang && item.jenjang.toLowerCase().indexOf(q) !== -1);
                    });

                    if (filtered.length === 0) {
                        $list.html('<div class="combobox-empty-state text-muted p-2" style="font-size: 0.8rem;">Tidak ada program studi</div>');
                        return;
                    }

                    allProdiStats.forEach(function(item) {
                        if (q && item.nama.toLowerCase().indexOf(q) === -1 && (!item.jenjang || item.jenjang.toLowerCase().indexOf(q) === -1)) {
                            return;
                        }
                        var isSelected = (String(item.id).trim() === String(currentVal).trim());
                        var displayName = item.nama + ' (' + (item.jenjang || 'S1') + ')';
                        var $opt = $('<div>')
                            .addClass('nav-combobox-option' + (isSelected ? ' is-selected' : ''))
                            .attr('data-value', item.id)
                            .attr('title', displayName)
                            .text(displayName);

                        $opt.on('mousedown', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            selectNavProdiItem(item.id, item.nama);
                        });

                        $list.append($opt);
                    });
                }
            }

            function selectNavProdiItem(id, nama) {
                $('#navProdiValue').val(id);
                $('#navProdiDisplayInput').val(nama).attr('title', nama);
                $('#prodiForm').val(id);
                $('#navProdiDropdownMenu').hide();
                renderVisualisasiProdi(id, false);
            }

            $(document).on('focus click', '#navProdiDisplayInput', function() {
                syncNavbarProdiOptions('');
                $('#navProdiDropdownMenu').show();
            }).on('input', '#navProdiDisplayInput', function() {
                var q = $(this).val();
                syncNavbarProdiOptions(q);
                $('#navProdiDropdownMenu').show();
            });

            $(document).on('click', '#navProdiToggleBtn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if ($('#navProdiDropdownMenu').is(':visible')) {
                    $('#navProdiDropdownMenu').hide();
                } else {
                    syncNavbarProdiOptions('');
                    $('#navProdiDropdownMenu').show();
                    $('#navProdiDisplayInput').focus();
                }
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#navProdiComboboxWrapper').length) {
                    $('#navProdiDropdownMenu').hide();
                }
            });

            // ─── 2. RENDER VISUALISASI DATA PROGRAM STUDI ───
            function renderVisualisasiProdi(prodiId, isAutoLoad) {
                currentProdiId = prodiId;
                currentActiveProdi = allProdiStats.find(function(p) { return p.id == prodiId; });
                currentActiveCplData = prodiCplYearlyData[prodiId] || null;

                if (!currentActiveProdi || !currentActiveCplData) {
                    alert('Data evaluasi untuk program studi terpilih tidak ditemukan.');
                    return;
                }

                // Update URL query parameters tanpa reload
                var currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('prodi', prodiId);
                window.history.replaceState({}, '', currentUrl.toString());

                // Reset filter tahun aktif ke default (akumulatif/keseluruhan)
                activeYearSkorCpl = null;
                activeYearKetercapaianCpl = null;
                $('#badgeYearSkorCpl').hide();
                $('#badgeYearKetercapaianCpl').hide();

                // 1. Update Hero Profile Card
                $('#displayNamaProdi').text(currentActiveProdi.nama);
                $('#displayJenjangProdi').text(currentActiveProdi.jenjang || 'S1');
                $('#displayKategoriProdi').text(currentActiveProdi.is_aptikom ? 'Aptikom' : 'Non-Aptikom');
                $('#displayMhsProdi').text((currentActiveProdi.total_mhs || 0) + ' Mahasiswa');

                mountNavbarControls();

                // 2. Render Tabel Skor & Radar Chart 1
                renderTableSkorPerTahun();
                renderRadarChartSkor(null);

                // 3. Render Tabel Ketercapaian & Radar Chart 2
                renderTableKetercapaianPerTahun();
                renderRadarChartKetercapaian(null);

                // 4. Render Deskripsi Butir CPL
                renderDeskripsiCpl();

                // Transisi Tampilan
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
            }

            // ─── 3. CARD 1: SKOR CAPAIAN CPL (TABEL & RADAR) ───
            function renderTableSkorPerTahun() {
                var $tbody = $('#tableRincianCplScores tbody');
                var $tfoot = $('#tableRincianCplScores tfoot');
                $tbody.empty();
                $tfoot.empty();

                var cplMeta = currentActiveCplData.cpl_meta || {};
                var cplScores = currentActiveCplData.cpl_scores || {};
                var yearlyAvgSkor = currentActiveCplData.yearly_avg_skor || {};

                Object.keys(cplMeta).forEach(function(kode) {
                    var meta = cplMeta[kode];
                    var rowHtml = '<tr><td class="fw-bold text-dark">' + kode + '</td>';

                    allAvailableYears.forEach(function(yr) {
                        var scoreVal = (cplScores[kode] && cplScores[kode][yr] !== undefined) ? cplScores[kode][yr] : 0;
                        var displayVal = (scoreVal !== null && scoreVal !== undefined && parseFloat(scoreVal) > 0) ? parseFloat(scoreVal).toFixed(2) : '0';
                        var isZeroClass = (parseFloat(displayVal) === 0) ? ' is-zero-cell' : '';
                        rowHtml += '<td class="text-center fw-semibold interactive-year-td td-year-skor-' + yr + isZeroClass + '" data-year="' + yr + '" style="cursor: pointer;" title="' + yr + '">' + displayVal + '</td>';
                    });

                    rowHtml += '</tr>';
                    $tbody.append(rowHtml);
                });

                // Footer Row: Rata-rata Skor per Tahun
                var footerHtml = '<tr style="background-color: #ebf5fb;"><th class="text-primary fw-bold" style="color: #1F3BB3 !important;">Rata-rata Skor</th>';
                allAvailableYears.forEach(function(yr) {
                    var avgYr = (yearlyAvgSkor[yr] !== undefined && parseFloat(yearlyAvgSkor[yr]) > 0) ? parseFloat(yearlyAvgSkor[yr]).toFixed(2) : '0';
                    var isZeroClass = (parseFloat(avgYr) === 0) ? ' is-zero-cell' : '';
                    footerHtml += '<th class="text-center interactive-year-tf tf-year-skor-' + yr + isZeroClass + '" data-year="' + yr + '" style="cursor: pointer;" title="' + yr + '">' + avgYr + '</th>';
                });
                footerHtml += '</tr>';
                $tfoot.append(footerHtml);
            }

            function renderRadarChartSkor(filterYear) {
                if (radarChartPerSoalInstance) {
                    radarChartPerSoalInstance.destroy();
                    radarChartPerSoalInstance = null;
                }

                var cplMeta = currentActiveCplData.cpl_meta || {};
                var cplScores = currentActiveCplData.cpl_scores || {};
                var labels = Object.keys(cplMeta);

                var skorValues = labels.map(function(k) {
                    if (filterYear) {
                        return (cplScores[k] && cplScores[k][filterYear] !== undefined) ? parseFloat(cplScores[k][filterYear]).toFixed(2) : 0;
                    }
                    return parseFloat(cplMeta[k].overall_skor || 0).toFixed(2);
                });

                var minSkorValues = labels.map(function(k) {
                    if (filterYear && cplMeta[k].yearly_min_skor && cplMeta[k].yearly_min_skor[filterYear] !== undefined) {
                        return parseFloat(cplMeta[k].yearly_min_skor[filterYear]).toFixed(2);
                    }
                    return parseFloat(cplMeta[k].min_skor !== undefined ? cplMeta[k].min_skor : 0).toFixed(2);
                });

                var maxSkorValues = labels.map(function(k) {
                    if (filterYear && cplMeta[k].yearly_max_skor && cplMeta[k].yearly_max_skor[filterYear] !== undefined) {
                        return parseFloat(cplMeta[k].yearly_max_skor[filterYear]).toFixed(2);
                    }
                    return parseFloat(cplMeta[k].max_skor !== undefined ? cplMeta[k].max_skor : 0).toFixed(2);
                });

                var canvas = document.getElementById('radarChart');
                var ctx = canvas.getContext('2d');
                radarChartPerSoalInstance = new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Skor Capaian CPL' + (filterYear ? ' (' + filterYear + ')' : ''),
                                data: skorValues,
                                backgroundColor: 'rgba(31, 59, 179, 0.15)',
                                borderColor: '#1F3BB3',
                                borderWidth: 2.5,
                                pointBackgroundColor: '#1F3BB3',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 1.5,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointHitRadius: 10
                            },
                            {
                                label: 'Min Skor CPL',
                                data: minSkorValues,
                                backgroundColor: 'rgba(239, 68, 68, 0.05)',
                                borderColor: '#ef4444',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                pointBackgroundColor: '#ef4444',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 1.5,
                                pointRadius: 4.5,
                                pointHoverRadius: 6.5,
                                pointHitRadius: 10
                            },
                            {
                                label: 'Max Skor CPL',
                                data: maxSkorValues,
                                backgroundColor: 'rgba(16, 185, 129, 0.05)',
                                borderColor: '#10b981',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                pointBackgroundColor: '#10b981',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 1.5,
                                pointRadius: 4.5,
                                pointHoverRadius: 6.5,
                                pointHitRadius: 10
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
                            mode: 'index',
                            intersect: true,
                            callbacks: {
                                title: function(tooltipItems, data) {
                                    return data.labels[tooltipItems[0].index] || '';
                                },
                                label: function(tooltipItem, data) {
                                    var datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
                                    return datasetLabel + ': ' + tooltipItem.yLabel + ' / 100';
                                }
                            }
                        }
                    }
                });
            }

            // ─── 4. CARD 2: KETERCAPAIAN CPL (%) (TABEL & RADAR) ───
            function renderTableKetercapaianPerTahun() {
                var $tbody = $('#tableKomparasiCplAngkatan tbody');
                var $tfoot = $('#tableKomparasiCplAngkatan tfoot');
                $tbody.empty();
                $tfoot.empty();

                var cplMeta = currentActiveCplData.cpl_meta || {};
                var cplCapaians = currentActiveCplData.cpl_capaians || {};
                var yearlyAvgCapaian = currentActiveCplData.yearly_avg_capaian || {};

                Object.keys(cplMeta).forEach(function(kode) {
                    var meta = cplMeta[kode];
                    var rowHtml = '<tr><td class="fw-bold text-dark">' + kode + '</td>';

                    allAvailableYears.forEach(function(yr) {
                        var pctVal = (cplCapaians[kode] && cplCapaians[kode][yr] !== undefined) ? cplCapaians[kode][yr] : 0;
                        var displayPct = (pctVal !== null && pctVal !== undefined && parseFloat(pctVal) > 0) ? parseFloat(pctVal).toFixed(2) + '%' : '0%';
                        var isZeroClass = (parseFloat(pctVal) === 0) ? ' is-zero-cell' : '';
                        rowHtml += '<td class="text-center fw-semibold interactive-year-td td-year-pct-' + yr + isZeroClass + '" data-year="' + yr + '" style="cursor: pointer;" title="' + yr + '">' + displayPct + '</td>';
                    });

                    rowHtml += '</tr>';
                    $tbody.append(rowHtml);
                });

                // Footer Row: Rata-rata Capaian per Tahun
                var footerPctHtml = '<tr style="background-color: #ebf5fb;"><th class="text-primary fw-bold" style="color: #1F3BB3 !important;">Rata-rata Ketercapaian</th>';
                allAvailableYears.forEach(function(yr) {
                    var avgPctYr = (yearlyAvgCapaian[yr] !== undefined && parseFloat(yearlyAvgCapaian[yr]) > 0) ? parseFloat(yearlyAvgCapaian[yr]).toFixed(2) + '%' : '0%';
                    var isZeroClass = (parseFloat(avgPctYr) === 0) ? ' is-zero-cell' : '';
                    footerPctHtml += '<th class="text-center interactive-year-tf tf-year-pct-' + yr + isZeroClass + '" data-year="' + yr + '" style="cursor: pointer;" title="' + yr + '">' + avgPctYr + '</th>';
                });
                footerPctHtml += '</tr>';
                $tfoot.append(footerPctHtml);
            }

            function renderRadarChartKetercapaian(filterYear) {
                if (radarChartCapaianCplInstance) {
                    radarChartCapaianCplInstance.destroy();
                    radarChartCapaianCplInstance = null;
                }

                var cplMeta = currentActiveCplData.cpl_meta || {};
                var cplCapaians = currentActiveCplData.cpl_capaians || {};
                var labels = Object.keys(cplMeta);

                var capaianValues = labels.map(function(k) {
                    if (filterYear) {
                        return (cplCapaians[k] && cplCapaians[k][filterYear] !== undefined) ? parseFloat(cplCapaians[k][filterYear]).toFixed(2) : 0;
                    }
                    return parseFloat(cplMeta[k].overall_capaian || 0).toFixed(2);
                });

                var minCplValues = labels.map(function(k) {
                    if (filterYear && cplMeta[k].yearly_min_capaian && cplMeta[k].yearly_min_capaian[filterYear] !== undefined) {
                        return parseFloat(cplMeta[k].yearly_min_capaian[filterYear]).toFixed(2);
                    }
                    return parseFloat(cplMeta[k].min_capaian !== undefined ? cplMeta[k].min_capaian : 0).toFixed(2);
                });

                var maxCplValues = labels.map(function(k) {
                    if (filterYear && cplMeta[k].yearly_max_capaian && cplMeta[k].yearly_max_capaian[filterYear] !== undefined) {
                        return parseFloat(cplMeta[k].yearly_max_capaian[filterYear]).toFixed(2);
                    }
                    return parseFloat(cplMeta[k].max_capaian !== undefined ? cplMeta[k].max_capaian : 0).toFixed(2);
                });

                var canvas = document.getElementById('radarChartCapaianCpl');
                var ctx = canvas.getContext('2d');
                radarChartCapaianCplInstance = new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'CPL (Program Studi)' + (filterYear ? ' (' + filterYear + ')' : ''),
                                data: capaianValues,
                                backgroundColor: 'rgba(31, 59, 179, 0.15)',
                                borderColor: '#1F3BB3',
                                borderWidth: 2.5,
                                pointBackgroundColor: '#1F3BB3',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 1.5,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointHitRadius: 10
                            },
                            {
                                label: 'Min CPL',
                                data: minCplValues,
                                backgroundColor: 'rgba(239, 68, 68, 0.05)',
                                borderColor: '#ef4444',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                pointBackgroundColor: '#ef4444',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 1.5,
                                pointRadius: 4.5,
                                pointHoverRadius: 6.5,
                                pointHitRadius: 10
                            },
                            {
                                label: 'Max CPL',
                                data: maxCplValues,
                                backgroundColor: 'rgba(16, 185, 129, 0.05)',
                                borderColor: '#10b981',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                pointBackgroundColor: '#10b981',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 1.5,
                                pointRadius: 4.5,
                                pointHoverRadius: 6.5,
                                pointHitRadius: 10
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
                            mode: 'index',
                            intersect: true,
                            callbacks: {
                                title: function(tooltipItems, data) {
                                    return data.labels[tooltipItems[0].index] || '';
                                },
                                label: function(tooltipItem, data) {
                                    var datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
                                    return datasetLabel + ': ' + tooltipItem.yLabel + '%';
                                }
                            }
                        }
                    }
                });
            }

            // ─── 5. INTERACTIVE YEAR CLICKING (MATCHING MAHASISWA & ANGKATAN) ───
            function initInteractiveYearHandlers() {
                // Table 1 (Skor CPL)
                $(document).on('click', '#tableRincianCplScores [data-year]', function() {
                    var clickedYear = $(this).data('year');
                    if (!clickedYear) return;

                    if (activeYearSkorCpl === clickedYear) {
                        // Reset jika diklik ulang
                        activeYearSkorCpl = null;
                        $('#badgeYearSkorCpl').hide();
                        $('#tableRincianCplScores .interactive-year-active').removeClass('interactive-year-active');
                        renderRadarChartSkor(null);
                    } else {
                        activeYearSkorCpl = clickedYear;
                        $('#badgeYearSkorCpl .badge-year-text').text(clickedYear);
                        $('#badgeYearSkorCpl').show();

                        $('#tableRincianCplScores .interactive-year-active').removeClass('interactive-year-active');
                        $('#tableRincianCplScores [data-year="' + clickedYear + '"]').addClass('interactive-year-active');

                        renderRadarChartSkor(clickedYear);
                    }
                });

                // Close Badge Skor CPL
                $('#badgeYearSkorCpl').on('click', function() {
                    activeYearSkorCpl = null;
                    $(this).hide();
                    $('#tableRincianCplScores .interactive-year-active').removeClass('interactive-year-active');
                    renderRadarChartSkor(null);
                });

                // Table 2 (Ketercapaian CPL)
                $(document).on('click', '#tableKomparasiCplAngkatan [data-year]', function() {
                    var clickedYear = $(this).data('year');
                    if (!clickedYear) return;

                    if (activeYearKetercapaianCpl === clickedYear) {
                        activeYearKetercapaianCpl = null;
                        $('#badgeYearKetercapaianCpl').hide();
                        $('#tableKomparasiCplAngkatan .interactive-year-active').removeClass('interactive-year-active');
                        renderRadarChartKetercapaian(null);
                    } else {
                        activeYearKetercapaianCpl = clickedYear;
                        $('#badgeYearKetercapaianCpl .badge-year-text').text(clickedYear);
                        $('#badgeYearKetercapaianCpl').show();

                        $('#tableKomparasiCplAngkatan .interactive-year-active').removeClass('interactive-year-active');
                        $('#tableKomparasiCplAngkatan [data-year="' + clickedYear + '"]').addClass('interactive-year-active');

                        renderRadarChartKetercapaian(clickedYear);
                    }
                });

                // Close Badge Ketercapaian CPL
                $('#badgeYearKetercapaianCpl').on('click', function() {
                    activeYearKetercapaianCpl = null;
                    $(this).hide();
                    $('#tableKomparasiCplAngkatan .interactive-year-active').removeClass('interactive-year-active');
                    renderRadarChartKetercapaian(null);
                });
            }

            // ─── 6. DESKRIPSI CPL (GRID DENGAN BADGE & TOGGLE) ───
            function renderDeskripsiCpl() {
                var $container = $('#labelContainer');
                $container.empty();

                var cplMeta = currentActiveCplData.cpl_meta || {};
                var keys = Object.keys(cplMeta);

                if (keys.length === 0) {
                    $('#cplBadgeCount').hide();
                    $container.append('<div class="text-muted small p-2">Tidak ada deskripsi CPL yang tersedia.</div>');
                    return;
                }

                $('#cplBadgeCount').text(keys.length + ' CPL').show();

                keys.forEach(function(kode) {
                    var meta = cplMeta[kode];
                    var cardHtml = 
                        '<div class="cpl-desc-card">' +
                        '  <span class="cpl-desc-badge">' + kode + '</span>' +
                        '  <span class="cpl-desc-text">' + (meta.judul || '-') + '</span>' +
                        '</div>';
                    $container.append(cardHtml);
                });
            }

            function initCplDescriptionToggle() {
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
            }

            // ─── 7. PDF PRINT HANDLER (FORMAL STYLE) ───
            function initPdfPrintHandler() {
                $('#btnPrintPdf').on('click', function(e) {
                    e.preventDefault();

                    var radarSkorImg = null;
                    var radarCapaianImg = null;

                    var c1 = document.getElementById('radarChart');
                    if (c1) {
                        try { radarSkorImg = c1.toDataURL('image/png'); } catch (err) {}
                    }
                    var c2 = document.getElementById('radarChartCapaianCpl');
                    if (c2) {
                        try { radarCapaianImg = c2.toDataURL('image/png'); } catch (err) {}
                    }

                    // Buat dynamic hidden form untuk submit PDF
                    var form = document.createElement("form");
                    form.method = "POST";
                    form.action = "{{ route($currentPrefix . 'visualisasi.generate-pdfVisualProgramStudi') }}";
                    form.target = "_blank";

                    var csrfInput = document.createElement("input");
                    csrfInput.type = "hidden";
                    csrfInput.name = "_token";
                    csrfInput.value = "{{ csrf_token() }}";
                    form.appendChild(csrfInput);

                    var pInput = document.createElement("input");
                    pInput.type = "hidden";
                    pInput.name = "prodi_id";
                    pInput.value = currentProdiId;
                    form.appendChild(pInput);

                    var fakInput = document.createElement("input");
                    fakInput.type = "hidden";
                    fakInput.name = "fakultas_id";
                    fakInput.value = "{{ $fakultasId }}";
                    form.appendChild(fakInput);

                    if (radarSkorImg) {
                        var img1 = document.createElement("input");
                        img1.type = "hidden";
                        img1.name = "radarChartImg";
                        img1.value = radarSkorImg;
                        form.appendChild(img1);
                    }

                    if (radarCapaianImg) {
                        var img2 = document.createElement("input");
                        img2.type = "hidden";
                        img2.name = "radarChartCapaianCplImg";
                        img2.value = radarCapaianImg;
                        form.appendChild(img2);
                    }

                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                });
            }
        </script>
    @endpush
@endsection
