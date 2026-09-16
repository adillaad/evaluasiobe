@php
    $currentPrefix = 'dosen.';
@endphp
@extends('dosen.template')
@section('title', 'Visualisasi Per Mata Kuliah')
@section('page_title', 'Visualisasi Per Mata Kuliah')
@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    @if (session()->has('failed'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <div><i class="bi bi-exclamation-octagon me-1"></i> {{ session('failed') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <div><i class="bi bi-check-circle me-1"></i> {{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <input type="hidden" id="title" data-course="{{ $completeCourseFormat }}">

    <style>
        /* Modern Dashboard Styling */
        .modern-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease-in-out;
            overflow: visible !important;
            margin-bottom: 14px;
        }

        .modern-card-header {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Section Titles */
        .section-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1e293b;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 0;
        }

        .section-title i {
            color: #1f3bb3;
        }

        /* Student & Course Hero Header */
        .student-profile-hero {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .student-label-tag {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #1f3bb3;
            margin-bottom: 4px;
        }

        .student-name-text {
            font-size: 1.35rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
            line-height: 1.3;
        }

        .student-meta-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px 12px;
            font-size: 0.85rem;
        }

        .student-meta-row .meta-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .student-meta-row .meta-label {
            color: #64748b;
            font-weight: 500;
        }

        .student-meta-row .meta-value {
            color: #1e293b;
            font-weight: 700;
        }

        .student-meta-row .meta-pipe {
            color: #cbd5e1;
            font-weight: 300;
        }

        /* Modern Table Styles */
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
            background-color: #f8fafc;
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

        .modern-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .modern-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Buttons */
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

        .modern-guide-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
        }

        .modern-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 4px;
            display: block;
        }

        .modern-select {
            height: 38px !important;
            border-radius: 8px !important;
            border: 1px solid #cbd5e1 !important;
            font-size: 0.88rem !important;
            padding: 0 12px !important;
            background-color: #ffffff !important;
            color: #1e293b !important;
            transition: all 0.2s ease;
        }

        /* Searchable Combobox Styling */
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
            height: 38px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 0 36px 0 12px !important;
            font-size: 0.88rem !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            background-color: #ffffff !important;
            cursor: text !important;
            transition: all 0.2s ease !important;
            width: 100% !important;
        }
        .combobox-input:focus {
            border-color: #1f3bb3 !important;
            box-shadow: 0 0 0 3px rgba(31, 59, 179, 0.12) !important;
            outline: none !important;
        }
        .combobox-toggle-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            padding: 4px 6px;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            border-radius: 4px;
            transition: color 0.15s ease;
        }
        .combobox-toggle-btn:hover {
            color: #1f3bb3;
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
            padding: 8px 12px;
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
            padding: 3px 7px !important;
            border-radius: 4px !important;
            white-space: nowrap;
            line-height: 1.2;
        }

        .cpl-desc-text {
            font-size: 0.8rem !important;
            line-height: 1.35;
            color: #334155;
            font-weight: 500;
        }
    </style>

    {{-- ========================================================================= --}}
    {{-- 1. HERO STUDENT & COURSE PROFILE CARD --}}
    {{-- ========================================================================= --}}
    <div class="student-profile-hero">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="student-label-tag">CAPAIAN CPMK INDIVIDU</div>
                <h3 class="student-name-text" id="displayNamaMhs">{{ $nama }} <span class="text-muted fw-normal fs-5">({{ $npm }})</span></h3>
                <div class="student-meta-row">
                    <div class="meta-item">
                        <span class="meta-label">Mata Kuliah:</span>
                        <span class="meta-value text-primary" id="courseDataText">{{ $completeCourseFormat }}</span>
                    </div>
                    <span class="meta-pipe">|</span>
                    <div class="meta-item">
                        <span class="meta-label">Program Studi:</span>
                        <span class="meta-value" id="prodiDataText">{{ $prodi }}</span>
                    </div>
                    <span class="meta-pipe">|</span>
                    <div class="meta-item">
                        <span class="meta-label">Angkatan:</span>
                        <span class="meta-value" id="angkatanDataText">{{ $angkatan }}</span>
                    </div>
                    <span class="meta-pipe">|</span>
                    <div class="meta-item">
                        <span class="meta-label">Universitas:</span>
                        <span class="meta-value" id="universitasDataText">{{ $universitas ?: (auth()->user()->universitas->nama ?? auth()->user()->prodi->fakultas->universitas->nama ?? '-') }}</span>
                    </div>
                </div>
            </div>

            {{-- Toolbar Tombol Aksi --}}
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
    {{-- 2. CARD 1: CAPAIAN CPMK MAHASISWA (Radar Chart & Tabel Rincian) --}}
    {{-- ========================================================================= --}}
    <div class="card modern-card mb-3">
        <div class="modern-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="section-title mb-0 d-flex align-items-center flex-wrap gap-2">
                <i class="bi bi-bar-chart-line"></i> Capaian CPMK: {{ $completeCourseFormat }}
            </h5>
            <button class="btn btn-sm btn-outline-secondary" type="button" id="btnToggleGuideCpmk" style="border-radius: 6px; font-size: 12px; padding: 4px 10px;" title="Petunjuk Membaca Diagram">
                <i class="bi bi-info-circle me-1"></i> Petunjuk Membaca Diagram
            </button>
        </div>
        <div class="card-body p-4">
            {{-- Guide Banner (Bisa di Buka Tutup) --}}
            <div class="mb-3" id="guideCpmk" style="display: none;">
                <div class="modern-guide-box small">
                    <h6 class="fw-bold text-primary mb-1"><i class="bi bi-compass me-1"></i> Panduan Membaca Diagram Radar CPMK:</h6>
                    <ul class="mb-0 ps-3 text-muted">
                        <li><strong>Bentuk Jaring (Radar):</strong> Setiap sudut mewakili satu <strong>CPMK (Capaian Pembelajaran Mata Kuliah)</strong>.</li>
                        <li><span class="badge" style="background-color: #1F3BB3; color:#fff;">CPMK (Mahasiswa)</span>: Nilai capaian CPMK mahasiswa yang bersangkutan (skala 0 - 100).</li>
                        <li><span class="badge" style="background-color: #c06e4b; color:#fff;">CPMK Avg (Batch)</span>: Rata-rata nilai capaian CPMK mahasiswa seangkatan pada mata kuliah ini.</li>
                        <li><span class="badge" style="background-color: #21d85f; color:#fff;">CPMK Max (Batch)</span>: Nilai capaian CPMK tertinggi di angkatan tersebut.</li>
                        <li><span class="badge" style="background-color: #d82121; color:#fff;">CPMK Min (Batch)</span>: Nilai capaian CPMK terendah di angkatan tersebut.</li>
                    </ul>
                </div>
            </div>

            {{-- Executive Summary Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4 col-sm-12">
                    <div class="p-3 bg-light rounded-3 border text-center">
                        <div class="text-muted small fw-semibold">Rata-rata Skor CPMK</div>
                        <div class="h4 mb-0 fw-bold text-primary mt-1" id="metricAvgScore">{{ $rataRataMhs }} <span class="small fs-6 text-muted">/ 100</span></div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="p-3 bg-light rounded-3 border text-center">
                        <div class="text-muted small fw-semibold">CPMK Tertinggi</div>
                        <div class="h4 mb-0 fw-bold text-success mt-1" id="metricHighestCpmk">{{ $kodeMaxCpmk ?: '-' }} <span class="small fs-6 text-muted">({{ $maxCpmk }})</span></div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="p-3 bg-light rounded-3 border text-center">
                        <div class="text-muted small fw-semibold">CPMK Terendah</div>
                        <div class="h4 mb-0 fw-bold text-danger mt-1" id="metricLowestCpmk">{{ $kodeMinCpmk ?: '-' }} <span class="small fs-6 text-muted">({{ $minCpmk }})</span></div>
                    </div>
                </div>
            </div>

            {{-- Diagram Radar Canvas & Tabel Rincian Capaian CPMK (Bersebelahan 50:50) --}}
            <div class="row g-4 align-items-center mb-4">
                <div class="col-lg-6 col-md-12">
                    <div class="d-flex justify-content-center align-items-center p-2">
                        <div style="width: 100%; max-width: 540px;">
                            <canvas id="radarChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="d-flex flex-column justify-content-center h-100">
                        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-table text-primary me-2"></i> Rincian Capaian CPMK:</h6>
                        <div class="modern-table-container">
                            <div class="table-responsive" style="max-height: 340px; overflow-y: auto;">
                                <table class="table modern-table mb-0" id="tableRincianCpmk">
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
                                                <td class="text-center fw-semibold text-dark" style="font-size: 0.875rem;">{{ $row['nilai_mhs'] }}</td>
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

            {{-- Deskripsi CPMK (Collapsible Grid Cards) --}}
            <div class="mt-4 pt-3 border-top">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 cpmk-desc-header" id="headerToggleCpmkDesc" style="cursor: pointer; user-select: none;">
                    <h6 class="keterangan fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-card-text text-primary"></i> Descriptions (Deskripsi CPMK)
                        <span class="badge bg-light text-secondary border ms-1" style="font-size: 0.75rem;">{{ count($cpmkResultAll) }} CPMK</span>
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
    {{-- 3. BOTTOM ROW: QUESTIONS WITH LOWEST SCORE & GANTI MAHASISWA (50:50) --}}
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
                    <p class="text-muted small mb-3">Soal-soal penilaian dengan ketercapaian CPMK terendah pada mata kuliah ini:</p>
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
                                                    <a href="{{ route($currentPrefix . 'cetakSoal', ['id' => $item['idSoal']]) }}"
                                                        target="_blank" class="text-decoration-none fw-semibold text-primary">
                                                        {{ $soalText }} <i class="bi bi-box-arrow-up-right small ms-1"></i>
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

        {{-- Card: Ganti Mahasiswa (Pilih Mahasiswa Lainnya) --}}
        <div class="col-lg-6 col-md-12">
            <div class="card modern-card h-100 mb-0">
                <div class="modern-card-header">
                    <h5 class="section-title mb-0">
                        <i class="bi bi-person-check text-primary"></i> Ganti Mahasiswa
                    </h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-3">Pilih mahasiswa lain di angkatan <strong>{{ $angkatan }}</strong> untuk melihat evaluasi CPMK:</p>
                    <form id="visualCpmkMahasiswa" method="POST" action="hasilvisualcpmk-mahasiswa" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="allNpm" value="{{ json_encode($allNpm) }}">
                        <input type="hidden" name="course" value="{{ $completeCourseFormat }}">
                        <input type="hidden" name="nama" id="formHiddenNama" value="{{ $nama }}">
                        <input type="hidden" name="angkatan" value="{{ $angkatan }}">
                        <input type="hidden" name="universitasCPMK" value="{{ $universitas }}">
                        <input type="hidden" name="universitasImg" value="{{ $universitasImg }}">
                        <input type="hidden" name="prodi" value="{{ $prodi }}">

                        <div class="mb-3">
                            <label class="modern-label" for="npmDisplayInput">
                                <i class="bi bi-person text-primary me-1"></i> Pilih Mahasiswa (NPM / Nama) <span class="text-danger">*</span>
                            </label>
                            <div class="combobox-wrapper" id="npmComboboxWrapper">
                                <div class="combobox-input-group">
                                    <input type="text" 
                                           id="npmDisplayInput" 
                                           class="form-control modern-select combobox-input" 
                                           placeholder="Ketik NPM atau Nama Mahasiswa..." 
                                           value="{{ $npm }} - {{ $nama }}"
                                           autocomplete="off">
                                    <input type="hidden" id="npm" name="npm" value="{{ $npm }}">
                                    <button type="button" class="combobox-toggle-btn" tabindex="-1" id="npmToggleBtn" title="Tampilkan daftar mahasiswa">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                </div>
                                <div class="combobox-dropdown-menu" id="npmDropdownMenu" style="display: none;">
                                    <div class="combobox-options-list" id="npmOptionsList">
                                        {{-- Dinamis via JS --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="modern-btn-primary" style="height: 34px; font-size: 0.82rem; padding: 0 14px;">
                                <i class="bi bi-search"></i> Tampilkan CPMK Mahasiswa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function() {
            // Data Mahasiswa List untuk Combobox
            var rawMhsData = @json($allNamaNpmData ?? []);
            var mhsOptionsData = [];
            var activeNpmIndex = -1;

            if (rawMhsData && rawMhsData.length > 0) {
                rawMhsData.forEach(function(item) {
                    var npmVal = String(item.NPM || item.npm || '').trim();
                    var namaVal = String(item.nama_mhs || item.nama || '').trim();
                    mhsOptionsData.push({
                        npm: npmVal,
                        nama: namaVal,
                        value: npmVal,
                        text: npmVal + ' - ' + namaVal
                    });
                });
            }

            function renderNpmOptions(filterText) {
                var $list = $('#npmOptionsList');
                $list.empty();
                activeNpmIndex = -1;

                if (!mhsOptionsData || mhsOptionsData.length === 0) {
                    $list.html('<div class="combobox-empty-state">Tidak ada data mahasiswa</div>');
                    return;
                }

                var query = (filterText || '').toLowerCase().trim();
                var filtered = mhsOptionsData.filter(function(item) {
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
                        selectNpmItem(item.value, item.text, item.nama);
                    });

                    $list.append($opt);
                });
            }

            function selectNpmItem(val, text, nama) {
                $('#npm').val(val);
                $('#npmDisplayInput').val(text).attr('title', text);
                $('#formHiddenNama').val(nama || '');
                $('input[name="nama"]').val(nama || '');
                $('#npmDropdownMenu').hide();
                $('#npmComboboxWrapper').removeClass('is-open');
                activeNpmIndex = -1;
            }

            function openNpmCombobox() {
                var currentQuery = $('#npmDisplayInput').val().trim();
                var currentVal = $('#npm').val();
                var currentSelectedObj = mhsOptionsData.find(function(s) { return s.value === currentVal; });

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
                    var found = mhsOptionsData.find(function(s) { return s.value === currentVal; });
                    if (found) {
                        $('#npmDisplayInput').val(found.text);
                        $('#formHiddenNama').val(found.nama);
                        $('input[name="nama"]').val(found.nama);
                        return;
                    }
                }

                if (currentText && mhsOptionsData.length > 0) {
                    var lowerText = currentText.toLowerCase();
                    var exactNpm = mhsOptionsData.find(function(s) { return s.npm.toLowerCase() === lowerText; });
                    if (exactNpm) {
                        selectNpmItem(exactNpm.value, exactNpm.text, exactNpm.nama);
                        return;
                    }
                    var exactText = mhsOptionsData.find(function(s) { return s.text.toLowerCase() === lowerText; });
                    if (exactText) {
                        selectNpmItem(exactText.value, exactText.text, exactText.nama);
                        return;
                    }
                    var exactNama = mhsOptionsData.find(function(s) { return s.nama.toLowerCase() === lowerText; });
                    if (exactNama) {
                        selectNpmItem(exactNama.value, exactNama.text, exactNama.nama);
                        return;
                    }
                } else if (!currentText) {
                    $('#npm').val('');
                    $('#npmDisplayInput').val('');
                    $('#formHiddenNama').val('');
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

                var exactMatch = mhsOptionsData.find(function(s) {
                    return s.npm.toLowerCase() === lowerQuery || s.text.toLowerCase() === lowerQuery;
                });

                if (exactMatch) {
                    $('#npm').val(exactMatch.value);
                    $('#formHiddenNama').val(exactMatch.nama);
                    $('input[name="nama"]').val(exactMatch.nama);
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
                        var targetIdx = (activeNpmIndex >= 0 && activeNpmIndex < $options.length) ? activeNpmIndex : 0;
                        var selectedVal = $options.eq(targetIdx).attr('data-value');
                        var selectedItem = mhsOptionsData.find(function(s) { return s.value === selectedVal; });
                        if (selectedItem) {
                            selectNpmItem(selectedItem.value, selectedItem.text, selectedItem.nama);
                        }
                    }
                } else if (e.key === 'Escape') {
                    closeNpmCombobox();
                }
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#npmComboboxWrapper').length) {
                    if ($('#npmDropdownMenu').is(':visible')) {
                        closeNpmCombobox();
                    }
                }
            });

            // Toggle Handler untuk Petunjuk Membaca Diagram
            $('#btnToggleGuideCpmk').on('click', function(e) {
                e.preventDefault();
                $('#guideCpmk').slideToggle(200);
            });

            // Toggle Handler untuk Deskripsi CPMK
            $('#headerToggleCpmkDesc').on('click', function(e) {
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

            $('#btnToggleCpmkDesc').on('click', function(e) {
                e.stopPropagation();
                $('#headerToggleCpmkDesc').trigger('click');
            });

            // Validasi Form Ganti Mahasiswa Submit
            $('#visualCpmkMahasiswa').on('submit', function(event) {
                var npm = $('#npm').val();
                if (!npm) {
                    event.preventDefault();
                    alert('Mohon pilih Mahasiswa (NPM) terlebih dahulu!');
                    $('#npmDisplayInput').focus();
                    return;
                }
            });

            // RADAR CHART CPMK INDIVIDU (Chart.js 3+)
            var cpmk = @json($cpmkTmp ?? []);
            var dataCapaianCpmk = Object.values(cpmk).map(item => Number(parseFloat(item[0]).toFixed(2)));
            var dataCapaianCpmkAvg = Object.values(cpmk).map(item => Number(parseFloat(item[1]).toFixed(2)));
            var dataCapaianCpmkMin = Object.values(cpmk).map(item => Number(parseFloat(item[2]).toFixed(2)));
            var dataCapaianCpmkMax = Object.values(cpmk).map(item => Number(parseFloat(item[3]).toFixed(2)));
            var labelsCapaianCpmk = Object.values(cpmk).map(item => item[4]);

            var canvas = document.getElementById('radarChart');
            if (canvas) {
                var ctx = canvas.getContext('2d');
                var radarChart = new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: labelsCapaianCpmk,
                        datasets: [
                            {
                                label: 'CPMK (Mahasiswa)',
                                data: dataCapaianCpmk,
                                backgroundColor: 'rgba(31, 59, 179, 0.2)',
                                borderColor: '#1f3bb3',
                                borderWidth: 2.5,
                                pointBackgroundColor: '#1f3bb3',
                                pointBorderColor: '#fff',
                                pointHoverBackgroundColor: '#fff',
                                pointHoverBorderColor: '#1f3bb3',
                                pointRadius: 4,
                                pointHoverRadius: 6
                            },
                            {
                                label: 'CPMK Avg (Batch)',
                                data: dataCapaianCpmkAvg,
                                backgroundColor: 'rgba(192, 110, 75, 0.05)',
                                borderColor: 'rgba(192, 110, 75, 0.9)',
                                borderWidth: 2,
                                pointBackgroundColor: 'rgba(192, 110, 75, 1)',
                                pointRadius: 3,
                                pointHoverRadius: 5
                            },
                            {
                                label: 'CPMK Min (Batch)',
                                data: dataCapaianCpmkMin,
                                backgroundColor: 'rgba(239, 68, 68, 0.05)',
                                borderColor: '#ef4444',
                                borderWidth: 1.5,
                                borderDash: [4, 4],
                                pointBackgroundColor: '#ef4444',
                                pointRadius: 3,
                                pointHoverRadius: 5
                            },
                            {
                                label: 'CPMK Max (Batch)',
                                data: dataCapaianCpmkMax,
                                backgroundColor: 'rgba(16, 185, 129, 0.05)',
                                borderColor: '#10b981',
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
                        scales: {
                            r: {
                                angleLines: {
                                    color: '#e2e8f0'
                                },
                                grid: {
                                    color: '#e2e8f0'
                                },
                                pointLabels: {
                                    font: {
                                        size: 12,
                                        weight: '600'
                                    },
                                    color: '#1e293b'
                                },
                                min: 0,
                                max: 100,
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
            }

            // PDF Download Handler
            $('#btnPrintPdf').on('click', function() {
                let course = document.getElementById('title').getAttribute('data-course');
                let radarChart = document.getElementById('radarChart');
                let radarChartImg = radarChart ? radarChart.toDataURL() : null;

                let descriptions = [];
                document.querySelectorAll("#labelContainer .cpl-desc-card").forEach(card => {
                    let badge = card.querySelector(".cpl-desc-badge")?.textContent.trim() || '';
                    let text = card.querySelector(".cpl-desc-text")?.textContent.trim() || '';
                    if (badge && text) {
                        descriptions.push(badge + ': ' + text);
                    } else if (text) {
                        descriptions.push(text);
                    }
                });

                let cpmkScores = [];
                document.querySelectorAll("#tableRincianCpmk tbody tr").forEach(row => {
                    let cols = row.querySelectorAll("td");
                    if (cols.length >= 2 && !row.querySelector("td[colspan]")) {
                        cpmkScores.push({
                            kode: cols[0].textContent.trim(),
                            skor: cols[1].textContent.trim()
                        });
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

                let nama = "{{ $nama }}";
                let npm = "{{ $npm }}";
                let angkatan = "{{ $angkatan }}";
                let prodi = "{{ $prodi }}";
                let universitas = "{{ $universitas }}";
                let avgScore = "{{ $rataRataMhs }}";
                let highestCpmk = "{{ $kodeMaxCpmk ?: '-' }} ({{ $maxCpmk }})";
                let lowestCpmk = "{{ $kodeMinCpmk ?: '-' }} ({{ $minCpmk }})";

                let originalBtnHtml = this.innerHTML;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Mengunduh...';
                this.disabled = true;

                fetch("{{ route($currentPrefix . 'generate-pdfVisualCPMKMahasiswa') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    body: JSON.stringify({
                        course,
                        radarChartImg,
                        cpmkScores,
                        avgScore,
                        highestCpmk,
                        lowestCpmk,
                        descriptions,
                        soalTerendah,
                        nama,
                        npm,
                        angkatan,
                        prodi,
                        universitas,
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
                    let cleanCourse = (course || 'MK').replace(/[/\\?%*:|"<>]/g, '-');
                    let cleanNama = (nama || 'Mahasiswa').replace(/[/\\?%*:|"<>]/g, '-');
                    a.download = `Laporan Visualisasi CPMK ${cleanCourse} - ${cleanNama}.pdf`;
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
