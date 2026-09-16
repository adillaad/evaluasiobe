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
    <style>
        /* Modern Clean Styling matching Visualisasi Mata Kuliah */
        .modern-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            background: #ffffff;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .modern-card-header {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 14px 20px;
        }

        .section-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title i {
            color: #1f3bb3;
        }

        /* Ensure layout allows sticky scrolling */
        html,
        body,
        .container-scroller,
        .page-body-wrapper,
        .content-wrapper,
        .content-wrapper > .row {
            overflow: visible !important;
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
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06) !important;
        }

        .course-label-tag {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #1f3bb3;
            margin-bottom: 4px;
        }

        .course-name-text {
            font-size: 1.35rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
            line-height: 1.3;
        }

        .course-meta-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px 12px;
            font-size: 0.85rem;
        }

        .meta-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .meta-label {
            color: #64748b;
            font-weight: 500;
        }

        .meta-value {
            color: #1e293b;
            font-weight: 700;
        }

        .meta-pipe {
            color: #cbd5e1;
            font-weight: 300;
        }

        /* Modern Action Buttons */
        .modern-btn-primary {
            height: 36px;
            padding: 0 16px;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            background: #1f3bb3;
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(31, 59, 179, 0.2);
            text-decoration: none;
            cursor: pointer;
        }

        .modern-btn-primary:hover {
            background: #172d88;
            box-shadow: 0 4px 10px rgba(31, 59, 179, 0.3);
            color: #ffffff;
        }

        .modern-btn-outline {
            height: 36px;
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
            text-decoration: none;
            cursor: pointer;
        }

        .modern-btn-outline:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #1e293b;
        }

        /* Modern Table */
        .modern-table-container {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }

        .modern-table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: collapse;
        }

        .modern-table thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 14px;
            border-bottom: 1px solid #e2e8f0;
            border-top: none;
        }

        .modern-table tbody td {
            padding: 10px 14px;
            font-size: 0.85rem;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .modern-table tbody tr:last-child td {
            border-bottom: none;
        }

        .modern-table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Modern Guide Box */
        .modern-guide-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
        }

        /* Structured CPMK Description Cards */
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

        /* Form Controls */
        .modern-label {
            font-size: 0.84rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
            display: block;
        }

        .modern-select {
            height: 40px !important;
            border-radius: 8px !important;
            border: 1px solid #cbd5e1 !important;
            font-size: 0.9rem !important;
            padding: 8px 14px !important;
            background-color: #ffffff !important;
            color: #1e293b !important;
            transition: all 0.2s ease;
        }

        .modern-select:focus {
            border-color: #1f3bb3 !important;
            box-shadow: 0 0 0 3px rgba(31, 59, 179, 0.12) !important;
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
            max-height: 240px;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f8fafc;
            -webkit-overflow-scrolling: touch;
        }
        .combobox-option {
            padding: 9px 14px;
            cursor: pointer;
            font-size: 0.86rem;
            color: #1e293b;
            transition: background 0.15s ease, color 0.15s ease;
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
        .combobox-empty-state {
            padding: 12px 14px;
            text-align: center;
            color: #94a3b8;
            font-size: 0.85rem;
            white-space: normal;
        }
    </style>

    @if (session()->has('failed'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div><i class="bi bi-exclamation-octagon me-1"></i> {{ session('failed') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div><i class="bi bi-check-circle me-1"></i> {{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <input type="hidden" id="title" data-course="{{ $completeCourseFormat }}">

    {{-- ========================================================================= --}}
    {{-- 1. HERO PROFILE CARD (Course, Prodi, Angkatan, Universitas) --}}
    {{-- ========================================================================= --}}
    <div class="course-profile-hero">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="course-label-tag">VISUALISASI PER ANGKATAN</div>
                <h3 class="course-name-text">{{ $completeCourseFormat }}</h3>
                <div class="course-meta-row">
                    <div class="meta-item">
                        <span class="meta-label">Program Studi:</span>
                        <span class="meta-value">{{ $prodi }}</span>
                    </div>
                    <span class="meta-pipe">|</span>
                    <div class="meta-item">
                        <span class="meta-label">Angkatan:</span>
                        <span class="meta-value">{{ $angkatan }}</span>
                    </div>
                    <span class="meta-pipe">|</span>
                    <div class="meta-item">
                        <span class="meta-label">Universitas:</span>
                        <span class="meta-value">{{ $universitas }}</span>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex align-items-center flex-wrap gap-2">
                <button type="button" class="modern-btn-outline" onclick="window.history.back();" title="Kembali ke halaman sebelumnya">
                    <i class="bi bi-arrow-left"></i> Kembali
                </button>
                <button id="btnPrintPdf" type="button" class="modern-btn-primary">
                    <i class="bi bi-file-earmark-pdf"></i> Unduh PDF
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- 2. CARD 1: CAPAIAN CPMK ANGKATAN (Radar Chart & Tabel Skor) --}}
    {{-- ========================================================================= --}}
    <div class="card modern-card mb-3">
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
                        <li><span class="badge" style="background-color: #c06e4b; color:#fff;">Average CPMK</span>: Rata-rata nilai capaian CPMK seluruh mahasiswa di angkatan tersebut (skala 0 - 100).</li>
                        <li><span class="badge" style="background-color: #21d85f; color:#fff;">Max CPMK</span>: Capaian CPMK tertinggi di angkatan tersebut.</li>
                        <li><span class="badge" style="background-color: #d82121; color:#fff;">Min CPMK</span>: Capaian CPMK terendah di angkatan tersebut.</li>
                    </ul>
                </div>
            </div>

            {{-- 3 Executive Metric Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4 col-sm-12">
                    <div class="p-3 bg-light rounded-3 border text-center">
                        <div class="text-muted small fw-semibold">Rata-rata Skor CPMK</div>
                        <div class="h4 mb-0 fw-bold text-primary mt-1">{{ $rataRataAngkatan }} <span class="small fs-6 text-muted">/ 100</span></div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="p-3 bg-light rounded-3 border text-center">
                        <div class="text-muted small fw-semibold">CPMK Tertinggi</div>
                        <div class="h4 mb-0 fw-bold text-success mt-1">
                            {{ $kodeMaxAvg ?: '-' }}
                            @if (!empty($kodeMaxAvg))
                                <span class="small fs-6 text-muted">({{ $maxAvg }})</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="p-3 bg-light rounded-3 border text-center">
                        <div class="text-muted small fw-semibold">CPMK Terendah</div>
                        <div class="h4 mb-0 fw-bold text-danger mt-1">
                            {{ $kodeMinAvg ?: '-' }}
                            @if (!empty($kodeMinAvg))
                                <span class="small fs-6 text-muted">({{ $minAvg }})</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Diagram Radar Canvas & Tabel Rincian Capaian CPMK (Bersebelahan 50:50) --}}
            <div class="row g-4 align-items-center mb-4">
                <div class="col-lg-6 col-md-12">
                    <div class="d-flex justify-content-center align-items-center p-2">
                        <div style="width: 100%; max-width: 540px;">
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
                                        @forelse ($cpmkTableList as $row)
                                            <tr>
                                                <td class="text-center">
                                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-2 py-1" style="font-size: 0.82rem;">
                                                        {{ $row['kode'] }}
                                                    </span>
                                                </td>
                                                <td class="text-center fw-semibold text-dark" style="font-size: 0.875rem;">
                                                    {{ $row['avg_angkatan'] }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center text-muted py-3">Data CPMK tidak ditemukan untuk mata kuliah ini.</td>
                                            </tr>
                                        @endforelse
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
                    <h6 class="keterangan fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-card-text text-primary"></i> Descriptions (Deskripsi CPMK)
                        <span class="badge bg-light text-secondary border ms-1" style="font-size: 0.75rem;">{{ count($cpmkResultAll ?? $cpmkTableList ?? []) }} CPMK</span>
                    </h6>
                    <button class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center justify-content-center" type="button" id="btnToggleCpmkDesc" style="border-radius: 6px; font-size: 13px; width: 32px; height: 30px; padding: 0;" title="Buka / Tutup Deskripsi CPMK">
                        <i class="bi bi-chevron-down" id="iconCpmkDescCollapse"></i>
                    </button>
                </div>
                <div class="mt-2" id="collapseCpmkDescriptions" style="display: none;">
                    <div id="labelContainer" class="cpl-desc-grid">
                        @foreach ($cpmkResultAll as $itemCpmk)
                            <div class="cpl-desc-card">
                                <span class="badge bg-primary text-white cpl-desc-badge">{{ $itemCpmk->kode }}</span>
                                <span class="cpl-desc-text">{{ $itemCpmk->judul }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- 3. BOTTOM ROW: QUESTIONS WITH LOWEST SCORE & CEK ANGKATAN LAINNYA (50:50) --}}
    {{-- ========================================================================= --}}
    <div class="row g-3 mb-4">
        {{-- Card: Questions with Lowest CPMK --}}
        <div class="col-lg-6 col-md-12">
            <div class="card modern-card h-100 mb-0">
                <div class="modern-card-header d-flex justify-content-between align-items-center">
                    <h5 class="section-title mb-0">
                        <i class="bi bi-patch-question text-primary"></i> Questions with Lowest CPMK
                    </h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-3">
                        Soal-soal penilaian dengan rata-rata perolehan skor CPMK terendah di angkatan ini:
                    </p>
                    <div class="modern-table-container">
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table modern-table mb-0" id="soalTerendahTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 45px;">No</th>
                                        <th class="text-center" style="width: 120px;">Asesmen</th>
                                        <th>Soal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($soalTerendah as $index => $item)
                                        @php
                                            $soalText = (!empty($item['soal']) && $item['soal'] !== 'null') ? $item['soal'] : '-';
                                            $jenisText = $item['Jenis'] ?? '-';
                                        @endphp
                                        <tr>
                                            <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                                            <td class="text-center">
                                                <span class="badge" style="background:#f1f5f9; color:#334155; border:1px solid #e2e8f0; font-size:12px; font-weight:500;">
                                                    {{ $jenisText }}
                                                </span>
                                            </td>
                                            <td>
                                                @if (!empty($item['idSoal']))
                                                    <a href="{{ route($currentPrefix . 'cetakSoal', ['id' => $item['idSoal']]) }}" target="_blank" class="text-primary text-decoration-none fw-semibold">
                                                        {{ $soalText }} <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                                                    </a>
                                                @else
                                                    <span class="text-dark fw-medium">{{ $soalText }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">Tidak ada data soal</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Cek Angkatan Lainnya --}}
        <div class="col-lg-6 col-md-12">
            <div class="card modern-card h-100 mb-0">
                <div class="modern-card-header">
                    <h5 class="section-title mb-0">
                        <i class="bi bi-calendar3 text-primary"></i> Cek Angkatan Lainnya
                    </h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-3">
                        Pilih angkatan lain untuk menampilkan evaluasi CPMK pada mata kuliah <strong>{{ $completeCourseFormat }}</strong>:
                    </p>
                    <form id="visualCpmkAngkatan" method="POST" action="hasilvisualcpmk-angkatan" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="course" value="{{ $course }}">
                        <input type="hidden" name="prodi" value="{{ $prodi }}">
                        <input type="hidden" name="universitasCPMK" value="{{ $universitas }}">
                        <input type="hidden" name="universitasImg" value="{{ $universitasImg }}">
                        <input type="hidden" name="allNpm" class="visually-hidden">

                        <div class="mb-3">
                            <label class="modern-label" for="angkatanDisplayInput">
                                <i class="bi bi-calendar-event text-primary me-1"></i> Pilih Angkatan <span class="text-danger">*</span>
                            </label>
                            <div class="combobox-wrapper" id="angkatanComboboxWrapper">
                                <div class="combobox-input-group">
                                    <input type="text" 
                                           id="angkatanDisplayInput" 
                                           class="form-control modern-select combobox-input" 
                                           placeholder="Pilih / Ketik Angkatan..." 
                                           value="{{ $angkatan ? 'Angkatan ' . $angkatan : '' }}"
                                           autocomplete="off">
                                    <input type="hidden" id="angkatan" name="angkatan" value="{{ $angkatan }}">
                                    <button type="button" class="combobox-toggle-btn" tabindex="-1" id="angkatanToggleBtn" title="Tampilkan daftar angkatan">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                </div>
                                <div class="combobox-dropdown-menu" id="angkatanDropdownMenu" style="display: none;">
                                    <div class="combobox-options-list" id="angkatanOptionsList">
                                        @foreach ($allAngkatan as $a)
                                            @php
                                                $angkatanVal = is_object($a) ? ($a->angkatan ?? '') : (is_array($a) ? ($a['angkatan'] ?? '') : $a);
                                            @endphp
                                            @if (!empty($angkatanVal))
                                                <div class="combobox-option {{ (string)$angkatanVal === (string)$angkatan ? 'is-selected' : '' }}" data-value="{{ $angkatanVal }}" data-text="Angkatan {{ $angkatanVal }}">
                                                    Angkatan {{ $angkatanVal }}
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="modern-btn-primary" style="height: 34px; font-size: 0.82rem; padding: 0 14px;">
                                <i class="bi bi-search"></i> Tampilkan CPMK Angkatan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Interaktivitas Guide Banner & Deskripsi CPMK Collapse --}}
    <script>
        $(document).ready(function() {
            // Toggle Petunjuk Membaca Diagram
            $('#btnToggleGuideCpmkBatch').on('click', function() {
                $('#guideCpmkBatch').slideToggle(200);
            });

            // Toggle Deskripsi CPMK via Seluruh Header Row
            $('#headerToggleCpmkDesc').on('click', function() {
                var $target = $('#collapseCpmkDescriptions');
                var $icon = $('#iconCpmkDescCollapse');
                if ($target.is(':visible')) {
                    $target.slideUp(200);
                    $icon.removeClass('bi-chevron-up').addClass('bi-chevron-down');
                } else {
                    $target.slideDown(200);
                    $icon.removeClass('bi-chevron-down').addClass('bi-chevron-up');
                }
            });

            // Prevent button click from double firing header click
            $('#btnToggleCpmkDesc').on('click', function(e) {
                e.stopPropagation();
                $('#headerToggleCpmkDesc').trigger('click');
            });

            // Validasi Form Pilih Angkatan Lainnya
            $('#visualCpmkAngkatan').on('submit', function(event) {
                var angkatanVal = $('#angkatan').val();
                if (!angkatanVal) {
                    alert('Mohon pilih angkatan terlebih dahulu!');
                    event.preventDefault();
                }
            });

            // Fetch NPM saat angkatan berganti
            function handleAngkatanChange(angkatanVal) {
                var prodiVal = $('input[name=prodi]').val();
                if (!angkatanVal) return;
                $.ajax({
                    url: "{{ route($currentPrefix . 'visualisasi.getAllNpmByAngkatan') }}",
                    method: 'GET',
                    data: {
                        angkatan: angkatanVal,
                        prodi: prodiVal
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.result && response.result.allNpm) {
                            var allNpmString = JSON.stringify(response.result.allNpm);
                            $('input[name=allNpm]').val(allNpmString);
                        }
                    }
                });
            }

            // Angkatan Combobox Logic for Cek Angkatan Lainnya
            var angkatanOptionsData = [];
            var activeAngkatanIndex = -1;

            $('#angkatanOptionsList .combobox-option').each(function() {
                var val = $(this).data('value');
                var txt = $(this).data('text') || $(this).text().trim();
                if (val) {
                    angkatanOptionsData.push({ value: String(val).trim(), text: txt });
                }
            });

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
                    return (item.text && item.text.toLowerCase().indexOf(query) !== -1) ||
                           (item.value && item.value.toLowerCase().indexOf(query) !== -1);
                });

                if (filtered.length === 0) {
                    $list.html('<div class="combobox-empty-state">Tidak ada angkatan yang cocok dengan "' + filterText + '"</div>');
                    return;
                }

                var currentVal = $('#angkatan').val();

                filtered.forEach(function(item, idx) {
                    var isSelected = (String(item.value).trim() === String(currentVal).trim());
                    var $opt = $('<div>')
                        .addClass('combobox-option' + (isSelected ? ' is-selected' : ''))
                        .attr('data-value', item.value)
                        .attr('data-index', idx)
                        .text(item.text);

                    $opt.on('mousedown', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        selectAngkatanOption(item.value, item.text);
                    });

                    $list.append($opt);
                });
            }

            function selectAngkatanOption(val, text) {
                $('#angkatan').val(val);
                $('#angkatanDisplayInput').val(text);
                $('#angkatanDropdownMenu').hide();
                $('#angkatanComboboxWrapper').removeClass('is-open');
                activeAngkatanIndex = -1;
                handleAngkatanChange(val);
            }

            function openAngkatanCombobox() {
                var currentQuery = $('#angkatanDisplayInput').val().trim();
                var currentVal = $('#angkatan').val();
                var currentSelectedObj = angkatanOptionsData.find(function(s) { return s.value === currentVal; });

                if (currentSelectedObj && currentSelectedObj.text === currentQuery) {
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

                var currentVal = $('#angkatan').val();
                var currentText = $('#angkatanDisplayInput').val().trim();

                if (currentVal) {
                    var found = angkatanOptionsData.find(function(s) { return s.value === currentVal; });
                    if (found) {
                        $('#angkatanDisplayInput').val(found.text);
                        return;
                    }
                }

                if (currentText && angkatanOptionsData.length > 0) {
                    var lowerText = currentText.toLowerCase();
                    var exact = angkatanOptionsData.find(function(s) { return s.text.toLowerCase() === lowerText || s.value.toLowerCase() === lowerText; });
                    if (exact) {
                        selectAngkatanOption(exact.value, exact.text);
                        return;
                    }
                }
            }

            $('#angkatanDisplayInput').on('focus', function() {
                $(this).select();
                openAngkatanCombobox();
            });

            $('#angkatanDisplayInput').on('click', function(e) {
                openAngkatanCombobox();
            });

            $('#angkatanDisplayInput').on('input', function() {
                var query = $(this).val().trim();
                var lowerQuery = query.toLowerCase();

                var exactMatch = angkatanOptionsData.find(function(s) {
                    return s.value.toLowerCase() === lowerQuery || s.text.toLowerCase() === lowerQuery;
                });

                if (exactMatch) {
                    $('#angkatan').val(exactMatch.value);
                    handleAngkatanChange(exactMatch.value);
                }

                renderAngkatanOptions(query);
                $('#angkatanComboboxWrapper').addClass('is-open');
                $('#angkatanDropdownMenu').show();
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

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#angkatanComboboxWrapper').length) {
                    if ($('#angkatanDropdownMenu').is(':visible')) {
                        closeAngkatanCombobox();
                    }
                }
            });

            // Radar Chart Initialization
            var cpmkTmp = @json($cpmkTmp ?? []);
            var radarLabels = Object.values(cpmkTmp).map(item => item[4]);
            var radarAvg = Object.values(cpmkTmp).map(item => Math.round(item[1] * 100) / 100);
            var radarMin = Object.values(cpmkTmp).map(item => Math.round(item[2] * 100) / 100);
            var radarMax = Object.values(cpmkTmp).map(item => Math.round(item[3] * 100) / 100);

            var ctx = document.getElementById('radarChartAngkatan').getContext('2d');
            var radarChart = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: radarLabels,
                    datasets: [
                        {
                            label: 'Average CPMK',
                            data: radarAvg,
                            backgroundColor: 'rgba(192, 110, 75, 0.2)',
                            borderColor: '#c06e4b',
                            borderWidth: 2,
                            pointBackgroundColor: '#c06e4b',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Max CPMK',
                            data: radarMax,
                            backgroundColor: 'rgba(33, 216, 95, 0.05)',
                            borderColor: '#21d85f',
                            borderWidth: 1.5,
                            borderDash: [4, 4],
                            pointBackgroundColor: '#21d85f',
                            pointRadius: 3,
                            pointHoverRadius: 5
                        },
                        {
                            label: 'Min CPMK',
                            data: radarMin,
                            backgroundColor: 'rgba(216, 33, 33, 0.05)',
                            borderColor: '#d82121',
                            borderWidth: 1.5,
                            borderDash: [4, 4],
                            pointBackgroundColor: '#d82121',
                            pointRadius: 3,
                            pointHoverRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        r: {
                            angleLines: {
                                color: '#e2e8f0'
                            },
                            grid: {
                                color: '#f1f5f9'
                            },
                            pointLabels: {
                                font: {
                                    size: 11,
                                    weight: '600'
                                },
                                color: '#334155'
                            },
                            suggestedMin: 0,
                            suggestedMax: 100,
                            ticks: {
                                stepSize: 20,
                                backdropColor: 'transparent',
                                font: {
                                    size: 10
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 16,
                                font: {
                                    size: 12,
                                    weight: '500'
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.formattedValue;
                                }
                            }
                        }
                    }
                }
            });

            // PDF Generation Event Handler
            document.getElementById('btnPrintPdf').addEventListener('click', function() {
                let course = document.getElementById('title').getAttribute('data-course');
                let radarChartAngkatan = document.getElementById('radarChartAngkatan');
                let radarChartAngkatanImg = radarChartAngkatan ? radarChartAngkatan.toDataURL() : null;

                let descriptions = [];
                document.querySelectorAll("#labelContainer .cpl-desc-card").forEach(card => {
                    let badge = card.querySelector('.cpl-desc-badge') ? card.querySelector('.cpl-desc-badge').textContent.trim() : '';
                    let text = card.querySelector('.cpl-desc-text') ? card.querySelector('.cpl-desc-text').textContent.trim() : '';
                    if (badge && text) {
                        descriptions.push(badge + ': ' + text);
                    } else if (text) {
                        descriptions.push(text);
                    }
                });

                let soalTerendah = [];
                document.querySelectorAll("#soalTerendahTable tbody tr").forEach(row => {
                    let cols = row.querySelectorAll("td");
                    if (cols.length >= 3 && !row.querySelector("td[colspan]")) {
                        soalTerendah.push({
                            no: cols[0].textContent.trim(),
                            types_of_assessment: cols[1].textContent.trim(),
                            question: cols[2].textContent.trim(),
                        });
                    }
                });

                let angkatan = "{{ $angkatan }}";
                let prodi = "{{ $prodi }}";
                let universitas = "{{ $universitas }}";

                let originalBtnHtml = this.innerHTML;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Mengunduh...';
                this.disabled = true;

                fetch("{{ route($currentPrefix . 'visualisasi.generate-pdfVisualCPMKAngkatan') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    body: JSON.stringify({
                        course,
                        radarChartAngkatanImg,
                        summary: '',
                        descriptions,
                        soalTerendah,
                        angkatan,
                        prodi,
                        universitas,
                    }),
                }).then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`HTTP ${response.status}: ${text}`);
                        });
                    }
                    return response.blob();
                })
                .then(blob => {
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    let cleanCourse = (course || 'MK').replace(/[/\\?%*:|"<>]/g, '-');
                    let cleanAngkatan = (angkatan || '').replace(/[/\\?%*:|"<>]/g, '-');
                    a.download = `Laporan Visualisasi CPMK ${cleanCourse} Angkatan - ${cleanAngkatan}.pdf`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                })
                .catch(error => {
                    console.error("Error:", error.message);
                    alert("Gagal mengunduh laporan PDF: " + error.message);
                })
                .finally(() => {
                    this.innerHTML = originalBtnHtml;
                    this.disabled = false;
                });
            });
        });
    </script>
@endsection
