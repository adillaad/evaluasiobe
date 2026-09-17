@extends('dosen.template')

@section('content')
<style>
/* Larger Checkboxes */
.form-check-input-lg {
    width: 1.3em !important;
    height: 1.3em !important;
    margin-top: 0.1em !important;
    cursor: pointer;
}
.form-check-label {
    cursor: pointer;
}

/* Modern Modal Styles */
.modal-content.modern-modal {
    border: none !important;
    border-radius: 1rem !important;
    overflow: hidden;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
}

.modern-modal .modal-header {
    border-bottom: 1px solid #f1f5f9;
    padding: 1.25rem 1.5rem;
    background: #ffffff;
}

.modern-modal .modal-body {
    padding: 1.5rem;
}

.modern-modal .modal-footer {
    border-top: 1px solid #f1f5f9;
    padding: 1rem 1.5rem;
    background: #f8fafc;
}

/* Modern Selection Cards */
.modern-option-card {
    display: block;
    border: 2px solid #e2e8f0 !important;
    border-radius: 0.75rem !important;
    padding: 1.25rem !important;
    background-color: #ffffff;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    margin-bottom: 0;
}

.modern-option-card:hover {
    border-color: #94a3b8 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
}

.modern-option-card.active,
.modern-option-card:has(input[type="radio"]:checked) {
    border-color: #10b981 !important;
    background-color: #f0fdf4 !important;
    box-shadow: 0 4px 14px 0 rgba(16, 185, 129, 0.15) !important;
}

.upload-modal .modern-option-card.active,
.upload-modal .modern-option-card:has(input[type="radio"]:checked) {
    border-color: #2563eb !important;
    background-color: #eff6ff !important;
    box-shadow: 0 4px 14px 0 rgba(37, 99, 235, 0.15) !important;
}

.modern-option-card .form-check-input {
    width: 1.2em;
    height: 1.2em;
    cursor: pointer;
}

.modern-option-card .option-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #0f172a;
}

.modern-option-card .option-desc {
    font-size: 0.85rem;
    color: #64748b;
    margin-top: 0.25rem;
}

.modern-code-badge {
    display: inline-block;
    background: #f1f5f9;
    color: #334155;
    font-family: 'Fira Code', 'Courier New', monospace;
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
    border-radius: 0.5rem;
    border: 1px solid #e2e8f0;
    margin-top: 0.5rem;
    word-break: break-all;
}

.modern-option-card.active .modern-code-badge,
.modern-option-card:has(input[type="radio"]:checked) .modern-code-badge {
    background: #ffffff;
    border-color: #a7f3d0;
    color: #065f46;
}

.upload-modal .modern-option-card.active .modern-code-badge,
.upload-modal .modern-option-card:has(input[type="radio"]:checked) .modern-code-badge {
    background: #ffffff;
    border-color: #bfdbfe;
    color: #1e40af;
}

/* Category Headers */
.category-header {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 0.6rem 1rem;
    font-weight: 700;
    font-size: 0.8rem;
    letter-spacing: 0.025em;
    color: #334155;
    margin-bottom: 0.75rem;
}
</style>

<div class="row">
    <div class="col-md-12">

        {{-- Header MK Box --}}
        <div class="card mb-3 border-0 shadow-sm rounded-4">
                <div class="card-body p-3 p-md-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <div class="mb-2">
                            <a href="{{ route('dosen.konversi-nilai.index') }}" class="text-decoration-none text-secondary small d-inline-flex align-items-center">
                                <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Daftar Konversi
                            </a>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1 text-dark">
                                {{ $konversi->mk_kode }} &mdash; {{ $konversi->mk->nama ?? 'Mata Kuliah' }}
                            </h4>
                            <div class="text-muted small">
                                <i class="mdi mdi-grid-large me-1"></i>{{ $konversi->mk->prodi->nama ?? '-' }}
                                @if ($konversi->mk->prodi->fakultas ?? null) &mdash; {{ $konversi->mk->prodi->fakultas->nama }} @endif
                                &nbsp;|&nbsp; SKS: {{ $konversi->mk->total_sks ?? '-' }}
                                &nbsp;|&nbsp; Tahun Ajaran: {{ $konversi->tahunAjaran->tahun ?? '-' }} ({{ $konversi->tahunAjaran->jenis_semester ?? '-' }})
                                @if ($konversi->kurikulum) &nbsp;|&nbsp; Kurikulum {{ $konversi->kurikulum->tahun }} @endif
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons (Download & Upload Excel & Lihat Nilai) --}}
                    <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                        <a href="{{ route('dosen.konversi-nilai.detail', $konversi->id) }}"
                           class="btn btn-info text-white btn-sm px-3 shadow-sm d-inline-flex align-items-center">
                            <i class="mdi mdi-eye fs-6 me-1"></i> Lihat Nilai
                        </a>
                        <button type="button"
                                id="btn-download-ab"
                                class="btn btn-success text-white border-0 btn-sm px-3 shadow-sm d-inline-flex align-items-center {{ $isComplete ? '' : 'disabled' }}"
                                data-bs-toggle="modal"
                                data-bs-target="#downloadTemplateModalAB"
                                {{ $isComplete ? '' : 'disabled' }}>
                            <i class="mdi mdi-download fs-6 me-1"></i> Download Template Nilai Per Metode
                        </button>
                        <button type="button"
                                id="btn-download-c"
                                class="btn btn-outline-success btn-sm px-3 shadow-sm d-inline-flex align-items-center d-none {{ $isComplete ? '' : 'disabled' }}"
                                data-bs-toggle="modal"
                                data-bs-target="#downloadTemplateModalC"
                                {{ $isComplete ? '' : 'disabled' }}>
                            <i class="mdi mdi-download fs-6 me-1"></i> Download Template Nilai Per Soal
                        </button>
                        <button type="button"
                                id="btn-upload-excel"
                                class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center {{ $isComplete ? '' : 'disabled' }}"
                                data-bs-toggle="modal"
                                data-bs-target="#uploadExcelModal"
                                {{ $isComplete ? '' : 'disabled' }}>
                            <i class="mdi mdi-upload fs-6 me-1"></i> Upload Excel Nilai
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Progress Tracker --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3 bg-light rounded">
                <div class="row align-items-center g-3">
                    <div class="col-md-6">
                        <div class="small fw-semibold text-muted">Status Total Bobot Metode:</div>
                        <div id="status-bobot-display" class="fs-5 fw-bold {{ round($totalBobotSum, 2) == 100 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($totalBobotSum, 2) }}% / 100%
                            @if (round($totalBobotSum, 2) == 100)
                                <i class="mdi mdi-check-circle-outline ms-1"></i>
                            @else
                                <span class="small text-danger ms-1">(Harus 100%)</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="small fw-semibold text-muted">Completeness Pemetaan CPMK:</div>
                        <div id="status-completeness-display" class="fs-5 fw-bold {{ $mappedCpmkCount >= $totalCpmkCount && $totalCpmkCount > 0 ? 'text-success' : 'text-warning text-dark' }}">
                            {{ $mappedCpmkCount }} dari {{ $totalCpmkCount }} CPMK terpetakan
                            @if ($mappedCpmkCount >= $totalCpmkCount && $totalCpmkCount > 0)
                                <i class="mdi mdi-check-circle-outline ms-1"></i>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Pemilihan Metode & Pemetaan CPMK --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-2 border-bottom">
                <ul class="nav nav-pills card-header-pills" id="modeTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold small py-2 px-3 me-2" id="tab-ab-mode" type="button">
                            <i class="mdi mdi-buffer me-1"></i> Nilai Per Metode
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold small py-2 px-3 text-secondary" id="tab-c-mode" type="button">
                            <i class="mdi mdi-format-list-bulleted me-1"></i> Nilai Per Soal
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0 fs-6 fw-bold text-primary" id="form-mode-title">
                        <i class="mdi mdi-checkbox-marked-outline me-1"></i> Pilih Metode, Bobot, dan Pemetaan CPMK (Nilai Per Metode)
                    </h5>
                    <button type="button" class="btn-add-metode btn btn-sm btn-outline-primary shadow-sm">
                        <i class="mdi mdi-plus me-1"></i> Tambah Metode Penilaian
                    </button>
                </div>
                <form id="form-metode-cpmk" action="{{ route('dosen.konversi-nilai.store-metode', $konversi->id) }}" method="POST">
                    @csrf

                    <div id="metode-container" class="mb-4">
                        @php
                            $hasExisting = $existingMetodes->isNotEmpty();
                        @endphp

                        @if ($hasExisting)
                            @foreach ($existingMetodes as $existingKm)
                                @php
                                    $metodeId = $existingKm->metode_id;
                                    $bobotVal = $existingKm->bobot;
                                    $existingCpmkMap = $existingKm->cpmkMetode->groupBy('cpmk_id');
                                @endphp
                                <div class="border rounded p-3 mb-3 bg-white shadow-sm metode-row" data-metode-id="{{ $metodeId }}">
                                     <div class="row align-items-center g-2">
                                         <div class="col-md-5">
                                             <label class="form-label small fw-bold mb-1">Metode Penilaian <span class="text-danger">*</span></label>
                                             <select class="form-select form-select-sm metode-select" name="metode_ids[]" required>
                                                 <option value="">-- Pilih Metode Penilaian --</option>
                                                 @foreach ($masterMetodes as $metode)
                                                     <option value="{{ $metode->id }}" {{ $metode->id == $metodeId ? 'selected' : '' }}>
                                                         {{ $metode->nama }}
                                                     </option>
                                                 @endforeach
                                                 <option value="__add_new__" class="fw-bold text-primary">+ Tambah Master Metode Baru...</option>
                                             </select>
                                         </div>
                                         <div class="col-md-4">
                                             <label class="form-label small fw-bold mb-1">Bobot <span class="text-danger">*</span></label>
                                             <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm metode-bobot-input"
                                                    name="bobot[{{ $metodeId }}]" value="{{ $bobotVal }}" placeholder="0 - 100" required>
                                         </div>
                                         <div class="col-md-3 d-flex align-items-center justify-content-end gap-2 pt-2 pt-md-0">
                                             <button type="button" class="btn btn-sm btn-outline-danger remove-metode-btn py-1 px-2" title="Hapus Metode">
                                                 <i class="mdi mdi-trash-can-outline me-1"></i> Hapus
                                             </button>
                                             <button type="button" class="btn btn-sm text-secondary border-0 shadow-none toggle-cpmk-btn p-1 fs-5" title="Sembunyikan CPMK">
                                                 <i class="mdi mdi-chevron-up toggle-icon"></i>
                                             </button>
                                         </div>
                                     </div>

                                    {{-- CPMK Section --}}
                                    <div class="cpmk-section mt-3 pt-3 border-top">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="small fw-bold text-muted mb-0">
                                                <i class="mdi mdi-link-variant me-1"></i> Centang CPMK ini:
                                            </div>
                                            <span class="badge bg-light text-primary border cpmk-summary-badge" style="font-size: 0.75rem;">
                                                <i class="mdi mdi-check-circle-outline me-1"></i> <span class="selected-cpmk-count">0</span> CPMK Dipilih
                                            </span>
                                        </div>
                                        <div class="row g-2">
                                            @foreach ($cpmks as $cpmk)
                                                @php
                                                    $isCpmkChecked = $existingCpmkMap->has($cpmk->id);
                                                @endphp
                                                <div class="col-md-6">
                                                    <div class="border rounded p-2 bg-light h-100 cpmk-card" data-cpmk-id="{{ $cpmk->id }}">
                                                        <div class="form-check mb-1 d-flex align-items-start ps-0">
                                                            <input class="form-check-input form-check-input-lg cpmk-check me-2 ms-0 mt-1 flex-shrink-0" type="checkbox"
                                                                   name="cpmk_mapping[{{ $metodeId }}][{{ $cpmk->id }}][]"
                                                                   value="0"
                                                                   data-cpmk-id="{{ $cpmk->id }}"
                                                                   id="cpmk_{{ $metodeId }}_{{ $cpmk->id }}"
                                                                   {{ $isCpmkChecked ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-semibold text-dark small mb-0 ms-1 text-wrap" for="cpmk_{{ $metodeId }}_{{ $cpmk->id }}">
                                                                <span class="badge bg-success me-1" style="font-size: 0.7rem; padding: 3px 6px;">{{ $cpmk->kode }}</span>
                                                                <span style="font-size: 0.82rem;">{{ $cpmk->judul ?? $cpmk->kode }}</span>
                                                            </label>
                                                        </div>

                                                        {{-- Breakdown Soal / Instrumen Section --}}
                                                        <div class="soal-breakdown-wrapper mt-2 pt-2 border-top {{ $isCpmkChecked ? '' : 'd-none' }}">
                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <span class="small fw-bold text-secondary" style="font-size: 0.75rem;">
                                                                    <i class="mdi mdi-format-list-numbered me-1"></i> Breakdown Soal
                                                                </span>
                                                                <button type="button" class="btn btn-xs btn-link text-primary p-0 text-decoration-none btn-add-soal" style="font-size: 0.75rem;">
                                                                    <i class="mdi mdi-plus-circle me-1"></i> Tambah Soal
                                                                </button>
                                                            </div>

                                                            <div class="soal-container space-y-1">
                                                                @php
                                                                    $rawExistingSoals = $existingCpmkMap->has($cpmk->id) ? $existingCpmkMap->get($cpmk->id)->whereNotNull('nama_soal')->unique('nama_soal') : collect();
                                                                    $sumStoredBobot = $rawExistingSoals->sum('bobot_soal');
                                                                    $numCheckedCpmks = $existingCpmkMap->keys()->count() ?: 1;
                                                                    $cpmkPortionVal = $bobotVal / $numCheckedCpmks;
                                                                @endphp
                                                                @if ($rawExistingSoals->isNotEmpty())
                                                                    @foreach ($rawExistingSoals as $sIdx => $exSoal)
                                                                        @php
                                                                            $displayBobot = $exSoal->bobot_soal;
                                                                            // Jika data lama tersimpan sebagai bobot efektif (misal 2.5%), konversikan kembali ke % kontribusi asli (misal 50%)
                                                                            if ($cpmkPortionVal > 0 && $sumStoredBobot > 0 && abs($sumStoredBobot - $cpmkPortionVal) < 0.05) {
                                                                                $displayBobot = round(($exSoal->bobot_soal / $cpmkPortionVal) * 100, 2);
                                                                            }
                                                                        @endphp
                                                                        <div class="soal-item-row d-flex align-items-center gap-1 mb-1">
                                                                            <input type="text" class="form-control form-control-sm soal-nama-input"
                                                                                   name="cpmk_soal[{{ $metodeId }}][{{ $cpmk->id }}][{{ $sIdx }}][nama_soal]"
                                                                                   value="{{ $exSoal->nama_soal }}" placeholder="Nama Soal (ex: Soal #1)" style="font-size: 0.75rem; height: 30px;">
                                                                             <div class="input-group input-group-sm" style="max-width: 140px;">
                                                                                 <input type="number" step="0.01" min="0.01" max="100" class="form-control form-control-sm soal-bobot-input px-2 text-end fw-semibold"
                                                                                        name="cpmk_soal[{{ $metodeId }}][{{ $cpmk->id }}][{{ $sIdx }}][bobot_soal]"
                                                                                        value="{{ $displayBobot }}" placeholder="Bobot" style="font-size: 0.8rem; height: 30px;">
                                                                                 <span class="input-group-text px-2 bg-light text-dark fw-bold" style="font-size: 0.75rem;">%</span>
                                                                             </div>
                                                                            <button type="button" class="btn btn-sm text-danger p-0 border-0 remove-soal-btn"><i class="mdi mdi-close-circle fs-5"></i></button>
                                                                        </div>
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="d-flex justify-content-between align-items-center border-top pt-3 flex-wrap gap-2">
                        <button type="button" class="btn-add-metode btn btn-sm btn-outline-primary">
                            <i class="mdi mdi-plus me-1"></i> Tambah Metode Penilaian
                        </button>
                        <button type="submit" id="btn-submit-metode" class="btn btn-primary btn-sm px-4">
                            <i class="mdi mdi-content-save me-1"></i> Simpan Metode & Pemetaan CPMK
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

{{-- Template Hidden HTML untuk Row Metode Baru --}}
<template id="metode-row-template">
    <div class="border rounded p-3 mb-3 bg-white shadow-sm metode-row" data-metode-id="">
        <div class="row align-items-center g-2">
            <div class="col-md-5">
                <label class="form-label small fw-bold mb-1">Metode Penilaian <span class="text-danger">*</span></label>
                <select class="form-select form-select-sm metode-select" name="metode_ids[]" required>
                    <option value="">-- Pilih Metode Penilaian --</option>
                    @foreach ($masterMetodes as $metode)
                        <option value="{{ $metode->id }}">{{ $metode->nama }}</option>
                    @endforeach
                    <option value="__add_new__" class="fw-bold text-primary">+ Tambah Master Metode Baru...</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold mb-1">Bobot <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm metode-bobot-input"
                       placeholder="0 - 100" required disabled>
            </div>
            <div class="col-md-3 d-flex align-items-center justify-content-end gap-2 pt-2 pt-md-0">
                <button type="button" class="btn btn-sm btn-outline-danger remove-metode-btn py-1 px-2" title="Hapus Metode">
                    <i class="mdi mdi-trash-can-outline me-1"></i> Hapus
                </button>
                <button type="button" class="btn btn-sm text-secondary border-0 shadow-none toggle-cpmk-btn p-1 fs-5 d-none" title="Sembunyikan CPMK">
                    <i class="mdi mdi-chevron-up toggle-icon"></i>
                </button>
            </div>
        </div>

        {{-- CPMK Section (Hidden until method is selected) --}}
        <div class="cpmk-section mt-3 pt-3 border-top d-none">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="small fw-bold text-muted mb-0">
                    <i class="mdi mdi-link-variant me-1"></i> Centang CPMK ini:
                </div>
                <span class="badge bg-light text-primary border cpmk-summary-badge" style="font-size: 0.75rem;">
                    <i class="mdi mdi-check-circle-outline me-1"></i> <span class="selected-cpmk-count">0</span> CPMK Dipilih
                </span>
            </div>
            <div class="row g-2">
                @foreach ($cpmks as $cpmk)
                    <div class="col-md-6">
                        <div class="border rounded p-2 bg-light h-100 cpmk-card" data-cpmk-id="{{ $cpmk->id }}">
                            <div class="form-check mb-1 d-flex align-items-start ps-0">
                                <input class="form-check-input form-check-input-lg cpmk-check me-2 ms-0 mt-1 flex-shrink-0" type="checkbox"
                                       value="0"
                                       data-cpmk-id="{{ $cpmk->id }}">
                                <label class="form-check-label fw-semibold text-dark small mb-0 ms-1 text-wrap cpmk-label">
                                    <span class="badge bg-success me-1" style="font-size: 0.7rem; padding: 3px 6px;">{{ $cpmk->kode }}</span>
                                    <span style="font-size: 0.82rem;">{{ $cpmk->judul ?? $cpmk->kode }}</span>
                                </label>
                            </div>

                            {{-- Breakdown Soal / Instrumen Section --}}
                            <div class="soal-breakdown-wrapper mt-2 pt-2 border-top d-none">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-bold text-secondary" style="font-size: 0.75rem;">
                                        <i class="mdi mdi-format-list-numbered me-1"></i> Breakdown Soal
                                    </span>
                                    <button type="button" class="btn btn-xs btn-link text-primary p-0 text-decoration-none btn-add-soal" style="font-size: 0.75rem;">
                                        <i class="mdi mdi-plus-circle me-1"></i> Tambah Soal
                                    </button>
                                </div>

                                <div class="soal-container space-y-1">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</template>

{{-- Modal Quick Add Master Metode Penilaian --}}
<div class="modal fade" id="addMasterMetodeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content modern-modal border-0">
            <div class="modal-header py-3 px-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary-subtle text-primary p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="mdi mdi-plus-circle fs-5"></i>
                    </div>
                    <h5 class="modal-title fs-6 fw-bold mb-0 text-dark">Tambah Master Metode</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <form id="form-quick-metode" onsubmit="return false;">
                    <div class="mb-3">
                        <label for="nama_metode_baru" class="form-label small fw-bold text-dark mb-1">Nama Metode Penilaian Baru <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="nama_metode_baru" placeholder="Contoh: Kuis 1, Portofolio, Seminar" required>
                    </div>
                    <div id="quick-metode-alert" class="alert alert-danger py-1 px-2 small d-none mb-0"></div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-between align-items-center py-2 px-3">
                <button type="button" class="btn btn-light btn-sm px-3 fw-semibold text-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btn-save-quick-metode" class="btn btn-primary btn-sm px-3 fw-bold">
                    <i class="mdi mdi-content-save me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Download Template Excel (Nilai Per Metode) --}}
<div class="modal fade" id="downloadTemplateModalAB" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modern-modal border-0">
            <form action="{{ route('dosen.konversi-nilai.download-template', $konversi->id) }}" method="GET">
                <div class="modal-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-success-subtle text-success p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="mdi mdi-microsoft-excel fs-4"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fs-6 fw-bold mb-0 text-dark">Download Template Excel</h5>
                            <span class="text-muted small">Format Nilai Per Metode Penilaian</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-secondary fw-semibold mb-3">Pilih format jenis template Excel yang ingin diunduh:</p>
                    
                    <div class="d-flex flex-column gap-3">
                        {{-- Opsi A --}}
                        <label for="dl_format_standar" class="modern-option-card">
                            <div class="d-flex align-items-start gap-3">
                                <input class="form-check-input mt-1 flex-shrink-0" type="radio" name="format_type" id="dl_format_standar" value="standar" checked>
                                <div class="flex-grow-1">
                                    <div class="option-title">Nilai Akhir Per Metode Penilaian</div>
                                    <div class="option-desc">Mengimpor nilai akhir <strong>seluruh metode</strong> sekaligus dalam 1 file.</div>
                                    <div class="modern-code-badge">
                                        <i class="mdi mdi-code-tags me-1"></i>Contoh Kolom: Tahun Ajaran | Nama MK | Angkatan | NPM | Nama | UTS | UAS | Tugas
                                    </div>
                                </div>
                            </div>
                        </label>

                        {{-- Opsi B --}}
                        <div class="modern-option-card p-3 rounded-3 border">
                            <label for="dl_format_cpmk" class="d-flex align-items-start gap-3 w-100 mb-0" style="cursor: pointer;">
                                <input class="form-check-input mt-1 flex-shrink-0" type="radio" name="format_type" id="dl_format_cpmk" value="metode_cpmk">
                                <div class="flex-grow-1">
                                    <div class="option-title">Nilai Akhir Per Metode & CPMK</div>
                                    <div class="option-desc">Mengimpor nilai akhir <strong>metode & CPMK</strong>.</div>
                                    <div class="modern-code-badge">
                                        <i class="mdi mdi-code-tags me-1"></i>Contoh Kolom: Tahun Ajaran | Nama MK | Angkatan | NPM | Nama | Nilai Metode UTS | UTS – CPMK 1 | UTS – CPMK 2
                                    </div>
                                </div>
                            </label>

                            {{-- Sub Options for Opsi B --}}
                            <div id="cpmk-scope-wrapper" class="mt-3 pt-3 border-top ms-4 d-none">
                                <div class="fw-bold text-dark mb-2">
                                    <i class="mdi mdi-tune-vertical me-1 text-primary"></i>Pilihan Cakupan Metode Template:
                                </div>
                                <div class="d-flex flex-column gap-2 mb-3">
                                    <label for="scope_all" class="d-flex align-items-center gap-2 cursor-pointer mb-0">
                                        <input class="form-check-input mt-0 flex-shrink-0" type="radio" name="cpmk_scope" id="scope_all" value="all" checked>
                                        <span class=" fw-semibold text-dark">
                                            Seluruh Metode Sekaligus <span class="text-muted fw-normal">(1 template memuat seluruh metode & CPMK)</span>
                                        </span>
                                    </label>

                                    <label for="scope_single" class="d-flex align-items-center gap-2 cursor-pointer mb-0">
                                        <input class="form-check-input mt-0 flex-shrink-0" type="radio" name="cpmk_scope" id="scope_single" value="single">
                                        <span class=" fw-semibold text-dark">
                                            Satu per Satu Metode <span class="text-muted fw-normal">(Template khusus 1 metode & CPMK)</span>
                                        </span>
                                    </label>
                                </div>

                                <div id="cpmk-single-select-container" class="mt-2 ms-4 d-none" style="max-width: 360px;">
                                    <label class="form-label small text-secondary fw-semibold mb-1">Pilih Metode Penilaian <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm" id="dl_metode_id_select">
                                        @foreach ($konversi->konversiMetode as $km)
                                            <option value="{{ $km->metode_id }}">
                                                {{ $km->metodePenilaian->nama ?? 'Metode ' . $km->metode_id }} (Bobot {{ $km->bobot }}%)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-light btn-sm px-3 fw-semibold text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-bold shadow-sm d-inline-flex align-items-center">
                        <i class="mdi mdi-download me-1 fs-6"></i> Download Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Download Template Excel (Nilai Per Soal) --}}
<div class="modal fade" id="downloadTemplateModalC" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modern-modal border-0">
            <form action="{{ route('dosen.konversi-nilai.download-template', $konversi->id) }}" method="GET">
                <input type="hidden" name="format_type" value="breakdown_soal">
                <div class="modal-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-success-subtle text-success p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="mdi mdi-format-list-numbered fs-4"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fs-6 fw-bold mb-0 text-dark">Download Template Nilai Per Soal</h5>
                            <span class="text-muted small">Rincian Butir Soal Per CPMK</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Pilih Metode Penilaian <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" name="metode_id" required>
                            @foreach ($konversi->konversiMetode as $km)
                                <option value="{{ $km->metode_id }}">
                                    {{ $km->metodePenilaian->nama ?? 'Metode ' . $km->metode_id }}
                                </option>
                            @endforeach
                        </select>
                        <div class="alert alert-info py-2 px-3 mt-3 mb-0 small border-0 bg-info-subtle text-info-emphasis rounded-3">
                            <i class="mdi mdi-information-outline me-1"></i> File Excel yang dihasilkan akan berisi kolom Nilai Metode serta rincian butir soal per CPMK khusus untuk metode yang dipilih.
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-light btn-sm px-3 fw-semibold text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-bold shadow-sm d-inline-flex align-items-center">
                        <i class="mdi mdi-download me-1 fs-6"></i> Download Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Upload Excel --}}
<div class="modal fade upload-modal" id="uploadExcelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
        <div class="modal-content modern-modal border-0">
            <form action="{{ route('dosen.konversi-nilai.upload-excel', $konversi->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header py-2 px-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-primary-subtle text-primary p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="mdi mdi-upload-lock fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fs-6 fw-bold mb-0 text-dark">Upload Excel Nilai Konversi</h5>
                            <span class="text-muted small">Impor & Proses Otomatis Nilai Mahasiswa</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <p class="small text-secondary fw-semibold mb-2">Pilih format jenis file Excel yang akan di-upload:</p>

                    {{-- Kategori 1 --}}
                    <div class="category-header py-1 px-3 mb-2 small fw-bold">
                        <i class="mdi mdi-buffer me-1 text-primary"></i> KATEGORI 1: Nilai Per Metode
                    </div>
                    <div class="d-flex flex-column gap-2 mb-2">
                        <label for="up_format_standar" class="modern-option-card rounded-3" style="padding: 0.6rem 0.85rem !important;">
                            <div class="d-flex align-items-start gap-2">
                                <input class="form-check-input option-upload-type mt-1 flex-shrink-0" type="radio" name="format_type" id="up_format_standar" value="standar" checked>
                                <div class="flex-grow-1">
                                    <div class="option-title small fw-bold">Nilai Akhir Per Metode Penilaian</div>
                                    <div class="option-desc text-muted small">Kolom Excel memuat nama metode (`UTS`, `UAS`, `Tugas`).</div>
                                </div>
                            </div>
                        </label>

                        <label for="up_format_cpmk" class="modern-option-card rounded-3" style="padding: 0.6rem 0.85rem !important;">
                            <div class="d-flex align-items-start gap-2">
                                <input class="form-check-input option-upload-type mt-1 flex-shrink-0" type="radio" name="format_type" id="up_format_cpmk" value="metode_cpmk">
                                <div class="flex-grow-1">
                                    <div class="option-title small fw-bold">Nilai Akhir Per Metode & CPMK</div>
                                    <div class="option-desc text-muted small">Kolom Excel memuat kombinasi metode dan CPMK (`UAS – CPMK 1`, `UAS – CPMK 2`).</div>
                                </div>
                            </div>
                        </label>
                    </div>

                    {{-- Kategori 2 --}}
                    <div class="category-header py-1 px-3 mb-2 small fw-bold">
                        <i class="mdi mdi-format-list-bulleted me-1 text-primary"></i> KATEGORI 2: Nilai Per Soal
                    </div>
                    <div class="mb-2">
                        <label for="up_format_soal" class="modern-option-card rounded-3" style="padding: 0.6rem 0.85rem !important;">
                            <div class="d-flex align-items-start gap-2">
                                <input class="form-check-input option-upload-type mt-1 flex-shrink-0" type="radio" name="format_type" id="up_format_soal" value="breakdown_soal">
                                <div class="flex-grow-1">
                                    <div class="option-title small fw-bold">Nilai Per Soal</div>
                                    <div class="option-desc text-muted small">Kolom Excel memuat Nilai Metode serta rincian nilai butir soal per CPMK.</div>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="pt-2 border-top mt-2">
                        <label for="excel_file" class="form-label fw-bold text-dark small mb-1">Pilih File Excel (.xlsx, .xls, .csv) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control form-control-sm" id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;"><i class="mdi mdi-information-outline me-1"></i> Pastikan format header kolom pada file Excel sudah sesuai dengan template yang Anda pilih.</small>
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-light btn-sm px-3 fw-semibold text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm d-inline-flex align-items-center">
                        <i class="mdi mdi-upload me-1 fs-6"></i> Upload & Process
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal / Alert Konfirmasi NPM Unregistered --}}
@if (session('warning_unregistered'))
    @php
        $unregisteredList = session('unregistered_mhs', []);
        $parsedRowsJson = json_encode(session('parsed_rows', []));
        $metodeMapJson = json_encode(session('temp_metode_map', []));
    @endphp
    <div class="modal fade show" id="unregisteredModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('dosen.konversi-nilai.upload-excel', $konversi->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="parsed_rows_data" value="{{ $parsedRowsJson }}">
                    <input type="hidden" name="metode_column_map_data" value="{{ $metodeMapJson }}">
                    <div class="modal-header py-2 px-3 bg-warning text-dark">
                        <h5 class="modal-title fs-6 fw-bold">
                            <i class="mdi mdi-alert-circle me-1"></i> Perhatian: {{ count($unregisteredList) }} NPM Belum Terdaftar
                        </h5>
                    </div>
                    <div class="modal-body p-3" style="max-height: 50vh; overflow-y: auto;">
                        <p class="small mb-2">Terdapat beberapa NPM dalam file Excel yang belum terdaftar di basis data mahasiswa:</p>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Baris</th>
                                        <th>Angkatan</th>
                                        <th>NPM</th>
                                        <th>Nama</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($unregisteredList as $unreg)
                                        <tr>
                                            <td>Baris {{ $unreg['line'] }}</td>
                                            <td>{{ $unreg['angkatan'] ?? '-' }}</td>
                                            <td><code>{{ $unreg['npm'] }}</code></td>
                                            <td>{{ $unreg['nama'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-info py-2 mt-2 mb-0 small">
                            Silakan pilih tindakan yang akan diambil untuk data mahasiswa di atas:
                        </div>
                    </div>
                    <div class="modal-footer py-2 px-3 bg-light justify-content-between">
                        <button type="submit" name="action_option" value="skip" class="btn btn-outline-secondary btn-sm">
                            <i class="mdi mdi-skip-next me-1"></i> Lanjutkan & Skip Baris Ini
                        </button>
                        <button type="submit" name="action_option" value="register" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-account-plus me-1"></i> Tambahkan Data Mahasiswa Otomatis
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

    </div> {{-- End col-md-12 --}}
</div> {{-- End row --}}

@push('scripts')
<script>
(function($) {
    $(document).ready(function() {
        const totalCpmkCount = {{ $totalCpmkCount }};
        let rowCounter = 0;
        let currentMode = 'AB'; // Default Mode A & B

        $(document).on('change', 'input[type="radio"]', function() {
            let $modal = $(this).closest('.modal-body');
            if ($modal.length) {
                $modal.find('.modern-option-card').removeClass('active');
                $(this).closest('.modern-option-card').addClass('active');
            }
        });

        function applyModeUI(mode) {
            currentMode = mode;
            if (mode === 'AB') {
                $('#tab-ab-mode').addClass('active').removeClass('text-secondary');
                $('#tab-c-mode').removeClass('active').addClass('text-secondary');
                $('#form-mode-title').html('<i class="mdi mdi-checkbox-marked-outline me-1"></i> Pilih Metode, Bobot, dan Pemetaan CPMK (Nilai Per Metode)');
                
                // Sembunyikan bagian breakdown soal
                $('.soal-breakdown-wrapper').addClass('d-none');
                
                // TAMPILKAN HANYA TOMBOL AB
                $('#btn-download-ab').attr('style', 'display: inline-flex !important;').removeClass('d-none');
                $('#btn-download-c').attr('style', 'display: none !important;').addClass('d-none');
            } else {
                $('#tab-c-mode').addClass('active').removeClass('text-secondary');
                $('#tab-ab-mode').removeClass('active').addClass('text-secondary');
                $('#form-mode-title').html('<i class="mdi mdi-format-list-bulleted me-1"></i> Pilih Metode, Bobot, Pemetaan CPMK & Breakdown Soal (Nilai Per Soal)');
                
                // Tampilkan bagian breakdown soal pada CPMK card yang ter-centang
                $('.cpmk-card').each(function() {
                    let $cpmkCheck = $(this).find('.cpmk-check');
                    if ($cpmkCheck.is(':checked')) {
                        $(this).find('.soal-breakdown-wrapper').removeClass('d-none');
                    }
                });

                // TAMPILKAN HANYA TOMBOL C
                $('#btn-download-c').attr('style', 'display: inline-flex !important;').removeClass('d-none');
                $('#btn-download-ab').attr('style', 'display: none !important;').addClass('d-none');
            }
        }

        $(document).on('click', '#tab-ab-mode', function(e) {
            e.preventDefault();
            applyModeUI('AB');
        });

        $(document).on('click', '#tab-c-mode', function(e) {
            e.preventDefault();
            applyModeUI('C');
        });

        function updateSelectOptions() {
            let selectedMetodeIds = [];
            $('.metode-select').each(function() {
                let val = $(this).val();
                if (val) selectedMetodeIds.push(val);
            });

            $('.metode-select').each(function() {
                let currentVal = $(this).val();
                $(this).find('option').each(function() {
                    let optVal = $(this).val();
                    if (optVal && optVal !== currentVal && selectedMetodeIds.includes(optVal)) {
                        $(this).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                });
            });
        }

        function recalculateStatus() {
            let totalBobot = 0;
            let mappedCpmkIds = new Set();
            let isAllSoalBreakdownValid = true;

            $('.metode-row').each(function() {
                let mId = $(this).attr('data-metode-id');
                if (!mId) return;

                let bobotVal = parseFloat($(this).find('.metode-bobot-input').val()) || 0;
                totalBobot += bobotVal;

                $(this).find('.cpmk-section').find('.cpmk-check:checked').each(function() {
                    let cpmkId = $(this).data('cpmk-id');
                    if (cpmkId) {
                        mappedCpmkIds.add(cpmkId);
                    }
                });

                // Update ringkasan jumlah CPMK yang dipilih pada metode row ini
                let selectedCountInRow = $(this).find('.cpmk-check:checked').length;
                let $summaryBadge = $(this).find('.cpmk-summary-badge');
                let $toggleBtn = $(this).find('.toggle-cpmk-btn');
                
                $(this).find('.selected-cpmk-count').text(selectedCountInRow);
                if (selectedCountInRow > 0) {
                    $summaryBadge.removeClass('bg-light text-primary').addClass('bg-success text-white');
                } else {
                    $summaryBadge.removeClass('bg-success text-white').addClass('bg-light text-primary');
                }

                if (mId) {
                    $toggleBtn.removeClass('d-none');
                } else {
                    $toggleBtn.addClass('d-none');
                }

                $(this).find('.cpmk-card').each(function() {
                    let $card = $(this);
                    let $soalRows = $card.find('.soal-item-row');
                    
                    if ($soalRows.length > 1) {
                        let sumSoalBobot = 0;
                        $soalRows.each(function() {
                            sumSoalBobot += parseFloat($(this).find('.soal-bobot-input').val()) || 0;
                        });
                        
                        let roundedSum = Math.round(sumSoalBobot * 100) / 100;
                        let $alertEl = $card.find('.soal-sum-alert');

                        if (roundedSum !== 100) {
                            isAllSoalBreakdownValid = false;
                            if ($alertEl.length === 0) {
                                $card.find('.soal-breakdown-wrapper').prepend(`
                                    <div class="soal-sum-alert alert alert-danger py-1 px-2 mb-1 border-0 rounded" style="font-size: 0.72rem;">
                                        <i class="mdi mdi-alert-circle me-1"></i> Total kontribusi soal harus 100% (saat ini: <b>${roundedSum}%</b>)
                                    </div>
                                `);
                            } else {
                                $alertEl.html(`<i class="mdi mdi-alert-circle me-1"></i> Total kontribusi soal harus 100% (saat ini: <b>${roundedSum}%</b>)`).removeClass('d-none');
                            }
                        } else {
                            if ($alertEl.length) {
                                $alertEl.addClass('d-none');
                            }
                        }
                    } else {
                        $card.find('.soal-sum-alert').remove();
                    }
                });
            });

            let $bobotDisplay = $('#status-bobot-display');
            let isBobotValid = Math.round(totalBobot * 100) / 100 === 100;
            if (isBobotValid) {
                $bobotDisplay.removeClass('text-danger').addClass('text-success').html(totalBobot.toFixed(2) + '% / 100% <i class="mdi mdi-check-circle-outline ms-1"></i>');
            } else {
                $bobotDisplay.removeClass('text-success').addClass('text-danger').html(totalBobot.toFixed(2) + '% / 100% <span class="small text-danger ms-1">(Harus 100%)</span>');
            }

            let mappedCount = mappedCpmkIds.size;
            let isCompletenessValid = mappedCount >= totalCpmkCount && totalCpmkCount > 0;
            let $completenessDisplay = $('#status-completeness-display');
            if (isCompletenessValid) {
                $completenessDisplay.removeClass('text-warning text-dark').addClass('text-success').html(mappedCount + ' dari ' + totalCpmkCount + ' CPMK terpetakan <i class="mdi mdi-check-circle-outline ms-1"></i>');
            } else {
                $completenessDisplay.removeClass('text-success').addClass('text-warning text-dark').html(mappedCount + ' dari ' + totalCpmkCount + ' CPMK terpetakan');
            }

            let isFullyComplete = isBobotValid && isCompletenessValid && isAllSoalBreakdownValid;
            let $btnDownloadAB = $('#btn-download-ab');
            let $btnDownloadC = $('#btn-download-c');
            let $btnUpload = $('#btn-upload-excel');
            let $btnSubmit = $('#btn-submit-metode');

            if (isFullyComplete) {
                $btnDownloadAB.removeClass('disabled btn-secondary').addClass('btn-success').removeAttr('style');
                $btnDownloadC.removeClass('disabled btn-secondary').addClass('btn-outline-success').removeAttr('disabled');
                $btnUpload.removeClass('disabled btn-secondary').addClass('btn-primary').removeAttr('disabled');
                $btnSubmit.removeAttr('disabled');
            } else {
                $btnDownloadAB.addClass('disabled btn-secondary').removeClass('btn-success');
                $btnDownloadC.addClass('disabled btn-secondary').removeClass('btn-outline-success').attr('disabled', 'disabled');
                $btnUpload.addClass('disabled btn-secondary').removeClass('btn-primary').attr('disabled', 'disabled');
                if (!isAllSoalBreakdownValid) {
                    $btnSubmit.attr('disabled', 'disabled');
                } else {
                    $btnSubmit.removeAttr('disabled');
                }
            }

            // Selalu kunci ulang tampilan tombol berdasarkan mode aktif
            applyModeUI(currentMode);
        }

        function createNewRow() {
            rowCounter++;
            let templateContent = document.getElementById('metode-row-template').content.cloneNode(true);
            let $row = $(templateContent).find('.metode-row');
            $row.attr('data-row-index', rowCounter);

            $('#metode-container').append($row);
            updateSelectOptions();
            recalculateStatus();
        }

        $(document).on('click', '.btn-add-metode', function() {
            createNewRow();
        });

        let targetSelectForNewMetode = null;

        $(document).on('change', '.metode-select', function() {
            let $select = $(this);
            let $row = $select.closest('.metode-row');
            let newMetodeId = $select.val();

            if (newMetodeId === '__add_new__') {
                targetSelectForNewMetode = $select;
                $select.val('');
                $('#nama_metode_baru').val('');
                $('#quick-metode-alert').addClass('d-none').text('');
                let modalEl = document.getElementById('addMasterMetodeModal');
                let modalObj = new bootstrap.Modal(modalEl);
                modalObj.show();
                return;
            }

            $row.attr('data-metode-id', newMetodeId);

            let $bobotInput = $row.find('.metode-bobot-input');
            let $cpmkSection = $row.find('.cpmk-section');

            if (!newMetodeId) {
                $bobotInput.prop('disabled', true).val('').removeAttr('name');
                $cpmkSection.addClass('d-none');
                $cpmkSection.find('input[type="checkbox"]').prop('checked', false).removeAttr('name');
            } else {
                $bobotInput.prop('disabled', false).attr('name', 'bobot[' + newMetodeId + ']');
                $cpmkSection.removeClass('d-none');

                $cpmkSection.find('.cpmk-check').each(function() {
                    let cpmkId = $(this).data('cpmk-id');
                    let uniqueId = 'cpmk_' + newMetodeId + '_' + cpmkId + '_' + rowCounter;
                    $(this).attr('id', uniqueId);
                    $(this).attr('name', 'cpmk_mapping[' + newMetodeId + '][' + cpmkId + '][]');
                    $(this).closest('.form-check').find('.cpmk-label').attr('for', uniqueId);
                });

                $cpmkSection.find('.sub-cpmk-check').each(function() {
                    let cpmkId = $(this).closest('.border').find('.cpmk-check').data('cpmk-id');
                    let subId = $(this).data('sub-id');
                    let uniqueId = 'sub_' + newMetodeId + '_' + subId + '_' + rowCounter;
                    $(this).attr('id', uniqueId);
                    $(this).attr('name', 'cpmk_mapping[' + newMetodeId + '][' + cpmkId + '][]');
                    $(this).closest('.form-check').find('.sub-cpmk-label').attr('for', uniqueId);
                });
            }

            updateSelectOptions();
            recalculateStatus();
        });

        // Handler Toggle Sembunyikan / Tampilkan Daftar CPMK
        $(document).on('click', '.toggle-cpmk-btn', function(e) {
            e.preventDefault();
            let $btn = $(this);
            let $row = $btn.closest('.metode-row');
            let $cpmkSection = $row.find('.cpmk-section');
            let $icon = $btn.find('.toggle-icon');

            if ($cpmkSection.is(':visible')) {
                $cpmkSection.slideUp(200);
                $icon.removeClass('mdi-chevron-up').addClass('mdi-chevron-down');
                $btn.attr('title', 'Tampilkan CPMK');
            } else {
                $cpmkSection.slideDown(200);
                $icon.removeClass('mdi-chevron-down').addClass('mdi-chevron-up');
                $btn.attr('title', 'Sembunyikan CPMK');
            }
        });

        $(document).on('click', '#btn-save-quick-metode', function() {
            let nama = $('#nama_metode_baru').val().trim();
            if (!nama) {
                $('#quick-metode-alert').removeClass('d-none alert-success').addClass('alert-danger').text('Nama metode penilaian tidak boleh kosong.');
                return;
            }

            let $btn = $(this);
            $btn.prop('disabled', true);

            $.ajax({
                url: "{{ route('dosen.konversi-nilai.store-quick-metode') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    nama: nama
                },
                success: function(res) {
                    $btn.prop('disabled', false);
                    let newId = res.id || (res.metode ? res.metode.id : null);
                    let newNama = res.nama || (res.metode ? res.metode.nama : null);

                    if (res.success && newId) {
                        let newOptHtml = `<option value="${newId}">${newNama}</option>`;
                        $('.metode-select').each(function() {
                            let $addOption = $(this).find('option[value="__add_new__"]');
                            if ($addOption.length) {
                                $addOption.before(newOptHtml);
                            } else {
                                $(this).append(newOptHtml);
                            }
                        });

                        let templateEl = document.getElementById('metode-row-template');
                        if (templateEl && templateEl.content) {
                            $(templateEl.content).find('.metode-select option[value="__add_new__"]').before(newOptHtml);
                        }

                        if (targetSelectForNewMetode && targetSelectForNewMetode.length) {
                            targetSelectForNewMetode.val(newId).trigger('change');
                        }

                        // Close Modal robustly
                        let modalEl = document.getElementById('addMasterMetodeModal');
                        if (window.bootstrap && bootstrap.Modal) {
                            let modalObj = bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal.getOrCreateInstance(modalEl);
                            if (modalObj) modalObj.hide();
                        }
                        $('#addMasterMetodeModal').modal('hide');
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open').css('overflow', '');

                        // Reset input and clear any modal alert
                        $('#nama_metode_baru').val('');
                        $('#quick-metode-alert').addClass('d-none').text('');

                        // Show success alert toast at top of form
                        $('.quick-metode-toast').remove();
                        $('#form-metode-cpmk').before(`
                            <div class="alert alert-success alert-dismissible fade show shadow-sm py-2 px-3 mb-3 quick-metode-toast" role="alert">
                                <i class="mdi mdi-check-circle-outline me-1"></i> Metode penilaian <strong>${newNama}</strong> berhasil ditambahkan!
                                <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `);

                        targetSelectForNewMetode = null;
                    } else {
                        $('#quick-metode-alert').removeClass('d-none alert-success').addClass('alert-danger').text(res.message || 'Gagal menyimpan.');
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false);
                    let msg = 'Terjadi kesalahan saat menyimpan data.';
                    if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.nama) {
                        msg = xhr.responseJSON.errors.nama[0];
                    }
                    $('#quick-metode-alert').removeClass('d-none alert-success').addClass('alert-danger').text(msg);
                }
            });
        });

        $(document).on('click', '.remove-metode-btn', function() {
            $(this).closest('.metode-row').remove();
            updateSelectOptions();
            recalculateStatus();
        });

        $(document).on('input change', '.metode-bobot-input', function() {
            recalculateStatus();
        });

        $(document).on('change', '.cpmk-check', function() {
            let isChecked = $(this).is(':checked');
            let $card = $(this).closest('.cpmk-card');
            let $wrapper = $card.find('.soal-breakdown-wrapper');

            if (isChecked && currentMode === 'C') {
                $wrapper.removeClass('d-none');
            } else {
                $wrapper.addClass('d-none');
                if (!isChecked) {
                    $wrapper.find('.soal-container').empty();
                }
            }
            recalculateStatus();
        });

        $(document).on('change', '.sub-cpmk-check', function() {
            if ($(this).is(':checked')) {
                $(this).closest('.border').find('.cpmk-check').prop('checked', true).trigger('change');
            }
            recalculateStatus();
        });

        function calculateKontribusiForCpmkCard($cpmkCard) {
            let $rows = $cpmkCard.find('.soal-item-row');
            if ($rows.length === 0) return;

            let $metodeRow = $cpmkCard.closest('.metode-row');
            let bobotMetode = parseFloat($metodeRow.find('.metode-bobot-input').val()) || 0;
            let checkedCpmksCount = $metodeRow.find('.cpmk-check:checked').length || 1;
            
            let bobotCpmkPortion = bobotMetode / checkedCpmksCount;

            let totalKontribusiInput = 0;
            $rows.each(function() {
                let bVal = parseFloat($(this).find('.soal-bobot-input').val()) || 0;
                totalKontribusiInput += bVal;
            });

            $rows.each(function() {
                let bVal = parseFloat($(this).find('.soal-bobot-input').val()) || 0;
                let rasioSoal = totalKontribusiInput > 0 ? (bVal / totalKontribusiInput) : (1 / $rows.length);
                let bobotSoalEfektif = bobotCpmkPortion * rasioSoal;

                $(this).find('.soal-kontribusi-badge')
                    .text('Bobot Soal: ' + bobotSoalEfektif.toFixed(2) + '%')
                    .attr('title', `Hasil Bobot Soal Efektif: ${bobotSoalEfektif.toFixed(2)}% (dari Kontribusi ${bVal}% dari Total ${totalKontribusiInput}%)`);
            });
        }

        $(document).on('click', '.btn-add-soal', function() {
            let $card = $(this).closest('.cpmk-card');
            let $metodeRow = $card.closest('.metode-row');
            let mId = $metodeRow.attr('data-metode-id');
            let cpmkId = $card.attr('data-cpmk-id') || $card.find('.cpmk-check').data('cpmk-id');
            let $container = $card.find('.soal-container');

            if (!mId) {
                alert('Silakan pilih Metode Penilaian terlebih dahulu.');
                return;
            }

            let sIdx = $container.find('.soal-item-row').length;
            let defaultNama = 'Soal #' + (sIdx + 1);

            let rowHtml = `
                <div class="soal-item-row d-flex align-items-center gap-1 mb-1">
                    <input type="text" class="form-control form-control-sm soal-nama-input"
                           name="cpmk_soal[${mId}][${cpmkId}][${sIdx}][nama_soal]"
                           value="${defaultNama}" placeholder="Nama Soal (ex: Soal #1)" style="font-size: 0.75rem; height: 30px;">
                    <div class="input-group input-group-sm" style="max-width: 140px;">
                        <input type="number" step="0.01" min="0.01" max="100" class="form-control form-control-sm soal-bobot-input px-2 text-end fw-semibold"
                               name="cpmk_soal[${mId}][${cpmkId}][${sIdx}][bobot_soal]"
                               value="" placeholder="Bobot" style="font-size: 0.8rem; height: 30px;">
                        <span class="input-group-text px-2 bg-light text-dark fw-bold" style="font-size: 0.75rem;">%</span>
                    </div>
                    <button type="button" class="btn btn-sm text-danger p-0 border-0 remove-soal-btn"><i class="mdi mdi-close-circle fs-5"></i></button>
                </div>
            `;
            $container.append(rowHtml);
            recalculateStatus();
        });

        $(document).on('click', '.remove-soal-btn', function() {
            let $card = $(this).closest('.cpmk-card');
            $(this).closest('.soal-item-row').remove();
            calculateKontribusiForCpmkCard($card);
            recalculateStatus();
        });

        $(document).on('input change keyup paste', '.soal-bobot-input', function() {
            let $card = $(this).closest('.cpmk-card');
            calculateKontribusiForCpmkCard($card);
            recalculateStatus();
        });

        $('.cpmk-card').each(function() {
            calculateKontribusiForCpmkCard($(this));
        });

        if ($('#metode-container .metode-row').length === 0) {
            createNewRow();
        } else {
            updateSelectOptions();
            recalculateStatus();
        }

        // Handler Toggle Sub-Opsi Download Template Metode CPMK
        $(document).on('change', 'input[name="format_type"]', function() {
            let val = $(this).val();
            if (val === 'metode_cpmk') {
                $('#cpmk-scope-wrapper').removeClass('d-none');
            } else {
                $('#cpmk-scope-wrapper').addClass('d-none');
                $('#dl_metode_id_select').removeAttr('name');
            }
        });

        $(document).on('change', 'input[name="cpmk_scope"]', function() {
            let scope = $(this).val();
            if (scope === 'single') {
                $('#cpmk-single-select-container').removeClass('d-none');
                $('#dl_metode_id_select').attr('name', 'metode_id');
            } else {
                $('#cpmk-single-select-container').addClass('d-none');
                $('#dl_metode_id_select').removeAttr('name');
            }
        });

        // Terapkan Mode AB secara tegas saat pertama kali dimuat
        applyModeUI('AB');
    });
})(jQuery);
</script>
@endpush
@endsection
