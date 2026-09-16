@extends('mahasiswa.template')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Modern Consistency Styles */
        .modern-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease-in-out;
            margin-bottom: 20px;
        }

        .modern-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
        }

        .modern-card-header {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top-left-radius: 14px;
            border-top-right-radius: 14px;
        }

        .page-header-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
            padding: 18px 24px;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1F3BB3;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            letter-spacing: -0.01em;
            margin-bottom: 0;
        }

        .section-title i {
            font-size: 1.15rem;
            color: #1F3BB3;
        }

        .modern-btn-primary {
            height: 36px;
            padding: 0 16px;
            font-size: 0.84rem;
            font-weight: 600;
            border-radius: 8px;
            background: #1F3BB3;
            color: #ffffff;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
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
            height: 36px;
            padding: 0 14px;
            font-size: 0.84rem;
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
            text-decoration: none;
            white-space: nowrap;
        }

        .modern-btn-outline:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #1e293b;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table th {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #475569;
            background-color: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 14px;
            vertical-align: middle;
        }

        .table td {
            padding: 11px 14px;
            vertical-align: middle;
            font-size: 0.86rem;
            color: #1e293b;
            border-bottom: 1px solid #f1f5f9;
        }

        .course-competency-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
            overflow: hidden;
        }

        .course-competency-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
        }

        .course-card-head {
            background: #fafbfc;
            border-bottom: 1px solid #f1f5f9;
            padding: 12px 18px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-2 px-md-4">

        {{-- ── 1. Page Header Bar (No Info Card) ─────────────────────────── --}}
        <div class="page-header-box">
            <div class="row align-items-center">
                <div class="col-lg-8 mb-3 mb-lg-0">
                    <h4 class="fw-bold mb-1 text-dark">
                        <i class="bi bi-file-earmark-text text-primary me-2"></i>Transkrip Akademik Berbasis Kompetensi
                    </h4>
                    <p class="text-muted small mb-0">
                        Rincian Capaian Pembelajaran Lulusan (CPL) dan Capaian Pembelajaran Mata Kuliah (CPMK) secara kumulatif
                    </p>
                </div>

                <div class="col-lg-4 text-lg-end">
                    <div class="d-flex align-items-center justify-content-lg-end gap-2 flex-wrap">
                        <a id="printBtn" class="modern-btn-primary" target="_blank"
                            href="{{ route('mahasiswa.transkrip-kompetensi.print') }}">
                            <i class="bi bi-printer"></i> Cetak
                        </a>
                        <a id="pdfBtn" class="modern-btn-outline" target="_blank"
                            href="{{ route('mahasiswa.transkrip-kompetensi.pdf') }}">
                            <i class="bi bi-file-earmark-pdf text-danger"></i> Unduh PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Loading Spinner --}}
        @include('components.transkrip.loading-spinner')

        {{-- ── 2. Summary & Rincian CPL ───────────────────────────────────── --}}
        <div class="modern-card" id="summaryCard">
            <div class="modern-card-header">
                <div class="section-title">
                    <i class="bi bi-award-fill"></i>
                    <span>Capaian Pembelajaran Lulusan (CPL)</span>
                </div>
            </div>
            <div class="p-4">
                @include('components.transkrip.kkl-cpl')
            </div>
        </div>

        {{-- ── 3. Transkrip Akademik Table ─────────────────────────────────── --}}
        <div class="modern-card" id="transcriptTableCard">
            <div class="modern-card-header">
                <div class="section-title">
                    <i class="bi bi-table"></i>
                    <span>Transkrip Akademik Mata Kuliah</span>
                </div>
                <div id="transcriptStatsBadge"></div>
            </div>
            <div class="p-0">
                @include('components.transkrip.tableakademik')
            </div>
        </div>

        {{-- ── 4. Detail Capaian Kompetensi ─────────────────────────────────── --}}
        <div class="modern-card" id="competencyDetailCard">
            <div class="modern-card-header">
                <div class="section-title">
                    <i class="bi bi-list-check"></i>
                    <span>Rincian Capaian Kompetensi (CPMK & CPL per Mata Kuliah)</span>
                </div>
            </div>
            <div class="p-4">
                @include('components.transkrip.detailcapaiankompetensi')
            </div>
        </div>

        {{-- ── 5. Petunjuk & Indikator Penilaian ───────────────────────────── --}}
        @include('components.transkrip.petunjuk-penilaian', ['prodi' => $prodi])

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const npm = '{{ $mahasiswaData->npm }}';

            // ─── DOM Elements ────────────────────────────────────────────────────────
            const loadingSection = document.getElementById('loadingSection');
            const summarySection = document.getElementById('summarySection');
            const simpleTranscriptSection = document.getElementById('simpleTranscriptSection');
            const competencySection = document.getElementById('competencySection');
            const coursesContainer = document.getElementById('coursesContainer');
            const simpleTranscriptBody = document.getElementById('simpleTranscriptBody');
            const cplTableBody = document.getElementById('cplTableBody');
            const transcriptStatsBadge = document.getElementById('transcriptStatsBadge');
            const printBtn = document.getElementById('printBtn');
            const pdfBtn = document.getElementById('pdfBtn');

            // ─── Fetch Data ──────────────────────────────────────────────────────────
            function fetchCompetencyData(tahun, semester) {
                if (loadingSection) loadingSection.style.display = 'block';
                if (summarySection) summarySection.style.opacity = '0.4';
                if (simpleTranscriptSection) simpleTranscriptSection.style.opacity = '0.4';
                if (competencySection) competencySection.style.opacity = '0.4';

                if (printBtn) printBtn.classList.add('disabled');
                if (pdfBtn) pdfBtn.classList.add('disabled');

                fetch("{{ route('mahasiswa.get-competency-data') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: new URLSearchParams({
                            npm: npm,
                            tahun: tahun || 'all',
                            semester: semester || 'all'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (loadingSection) loadingSection.style.display = 'none';
                        if (summarySection) summarySection.style.opacity = '1';
                        if (simpleTranscriptSection) simpleTranscriptSection.style.opacity = '1';
                        if (competencySection) competencySection.style.opacity = '1';

                        if (data.success) {
                            if (summarySection) summarySection.style.display = 'block';
                            if (simpleTranscriptSection) simpleTranscriptSection.style.display = 'block';
                            if (competencySection) competencySection.style.display = 'block';

                            renderSummary(data.data);
                            renderCplTable(data.data.cpls);
                            renderSimpleTranscript(data.data.courses, data.data);
                            renderCompetencyData(data.data);

                            const printBase = "{{ route('mahasiswa.transkrip-kompetensi.print') }}";
                            const pdfBase = "{{ route('mahasiswa.transkrip-kompetensi.pdf') }}";

                            if (printBtn) {
                                printBtn.href = printBase + "?tahun=all&semester=all";
                                printBtn.classList.remove('disabled');
                            }
                            if (pdfBtn) {
                                pdfBtn.href = pdfBase + "?tahun=all&semester=all";
                                pdfBtn.classList.remove('disabled');
                            }
                        } else {
                            showError(data.message || 'Gagal memuat data');
                        }
                    })
                    .catch(error => {
                        if (loadingSection) loadingSection.style.display = 'none';
                        console.error('Error:', error);
                        showError('Terjadi kesalahan saat memuat data transkrip kompetensi.');
                    });
            }

            // Initial Load (Kumulatif)
            fetchCompetencyData('all', 'all');

            // ─── Wong Color Palette Helpers ──────────────────────────────────────────
            function getWongColor(nilai) {
                if (nilai === null || nilai === undefined || nilai === '-') return '#64748b';
                const n = parseFloat(nilai);
                if (isNaN(n)) return '#64748b';
                if (n <= 50) return '#D55E00';
                if (n <= 74) return '#E69F00';
                return '#0072B2';
            }

            function getWongBg(nilai) {
                const color = getWongColor(nilai);
                return color + '1A';
            }

            // ─── Render Summary CPL ──────────────────────────────────────────────────
            function renderSummary(data) {
                const avgCpl = data.avg_cpl || 0;
                const color = getWongColor(avgCpl);

                const avgEl = document.getElementById('avgCpl');
                if (avgEl) {
                    avgEl.innerHTML = `${avgCpl.toFixed(2)} <span class="fs-6 fw-normal text-muted">/ 100</span>`;
                    avgEl.style.color = color;
                }

                const statusEl = document.getElementById('cplStatus');
                if (statusEl) {
                    statusEl.textContent = data.cpl_status || '-';
                    statusEl.style.color = color;
                }

                const bar = document.getElementById('progressBar');
                if (bar) {
                    bar.style.width = `${Math.min(avgCpl, 100)}%`;
                    bar.style.backgroundColor = color;
                }
            }

            // ─── Render CPL Table ────────────────────────────────────────────────────
            function renderCplTable(cpls) {
                if (!cplTableBody) return;
                if (!cpls || cpls.length === 0) {
                    cplTableBody.innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="bi bi-info-circle me-2"></i>Belum ada data capaian CPL
                            </td>
                        </tr>`;
                    return;
                }

                cplTableBody.innerHTML = cpls.map(cpl => {
                    const c = getWongColor(cpl.nilai);
                    const bg = getWongBg(cpl.nilai);

                    return `
                        <tr>
                            <td class="text-center">
                                <code class="fw-bold px-2 py-1 rounded" style="color: ${c}; background: ${bg}; font-size: 0.8rem;">
                                    ${cpl.kode}
                                </code>
                            </td>
                            <td>
                                <div class="fw-medium text-dark">${cpl.deskripsi}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge text-white px-2 py-1" style="background-color: ${c}; font-size: 0.82rem;">
                                    ${cpl.nilai.toFixed(2)}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold small" style="color: ${c};">
                                    ${cpl.status}
                                </span>
                            </td>
                        </tr>`;
                }).join('');
            }

            // ─── Render Simple Transcript ────────────────────────────────────
            function renderSimpleTranscript(courses, fullData) {
                if (!simpleTranscriptBody) return;

                if (transcriptStatsBadge && fullData) {
                    const totalSks = fullData.total_sks || 0;
                    const ipk = fullData.ipk ? fullData.ipk.toFixed(2) : '0.00';
                    transcriptStatsBadge.innerHTML = `
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 fw-semibold">
                                Total: ${courses.length} MK (${totalSks} SKS)
                            </span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 fw-semibold">
                                IPK: ${ipk}
                            </span>
                        </div>`;
                }

                if (!courses || courses.length === 0) {
                    simpleTranscriptBody.innerHTML = `
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-info-circle me-2"></i>Belum ada data mata kuliah yang diambil.
                            </td>
                        </tr>`;
                    return;
                }

                simpleTranscriptBody.innerHTML = courses.map((course, i) => {
                    const nilaiAkhir = (course.nilai_akhir !== null && course.nilai_akhir !== undefined)
                        ? course.nilai_akhir.toFixed(2)
                        : null;
                    const wongColor = nilaiAkhir ? getWongColor(nilaiAkhir) : '#64748b';
                    const wongBg = nilaiAkhir ? getWongBg(nilaiAkhir) : '#f1f5f9';

                    const isLulus = (course.status || '').toLowerCase().includes('lulus') &&
                        !(course.status || '').toLowerCase().includes('tidak');
                    const statusBadge = isLulus
                        ? `<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Lulus</span>`
                        : `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">${course.status || 'Tidak Lulus'}</span>`;

                    return `
                        <tr>
                            <td class="text-center text-muted fw-bold">${i + 1}</td>
                            <td class="text-center"><span class="badge bg-light text-secondary border">${course.kurikulum || '-'}</span></td>
                            <td class="text-center">
                                <code class="fw-bold px-2 py-1 rounded" style="background:#f1f5f9; color:#334155;">
                                    ${course.kode}
                                </code>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">${course.nama || '-'}</div>
                            </td>
                            <td class="text-center">${course.semester || '-'}</td>
                            <td class="text-center fw-bold">${course.sks || '-'}</td>
                            <td class="text-center">
                                ${nilaiAkhir
                                    ? `<span class="badge px-2 py-1 text-white" style="background-color:${wongColor}; font-size:0.82rem;">${nilaiAkhir}</span>`
                                    : '-'}
                            </td>
                            <td class="text-center fw-bold fs-6 ${course.grade_color}">
                                ${course.grade_huruf || '-'}
                            </td>
                            <td class="text-center">${statusBadge}</td>
                        </tr>`;
                }).join('');
            }

            // ─── Render Competency Data Detail ──────────────────────────────
            function renderCompetencyData(data) {
                if (!coursesContainer) return;

                if (data.courses && data.courses.length > 0) {
                    coursesContainer.innerHTML = data.courses.map((course, idx) => {
                        const isLulus = (course.status || '').toLowerCase().includes('lulus') &&
                            !(course.status || '').toLowerCase().includes('tidak');
                        const statusBadge = isLulus
                            ? `<span class="badge bg-success-subtle text-success border border-success-subtle">Lulus</span>`
                            : `<span class="badge bg-danger-subtle text-danger border border-danger-subtle">${course.status || 'Tidak Lulus'}</span>`;

                        const nilaiAkhir = (course.nilai_akhir !== null && course.nilai_akhir !== undefined)
                            ? course.nilai_akhir.toFixed(2)
                            : '-';
                        const wColor = nilaiAkhir !== '-' ? getWongColor(nilaiAkhir) : '#64748b';

                        // CPMK List
                        const cpmkContent = (course.cpmks && course.cpmks.length > 0)
                            ? course.cpmks.map(c => {
                                const cc = getWongColor(c.nilai);
                                const cbg = getWongBg(c.nilai);
                                return `
                                    <div class="d-flex align-items-center gap-2 p-2 rounded mb-2 border" style="background: ${cbg}; border-color: ${cc}22 !important;">
                                        <div style="min-width: 75px; flex-shrink: 0;">
                                            <code class="fw-bold" style="color: ${cc}; font-size: 0.78rem;">${c.kode}</code>
                                        </div>
                                        <div class="flex-grow-1" style="min-width: 0;">
                                            <div class="small text-muted text-truncate mb-1" title="${c.deskripsi}">${c.deskripsi}</div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="flex-grow-1" style="height: 5px; border-radius: 99px; background: #e2e8f0;">
                                                    <div style="width: ${Math.min(c.nilai, 100)}%; height: 100%; border-radius: 99px; background: ${cc};"></div>
                                                </div>
                                                <span class="fw-bold small" style="color: ${cc}; min-width: 38px; text-align: right;">${c.nilai.toFixed(1)}</span>
                                            </div>
                                        </div>
                                    </div>`;
                            }).join('')
                            : `<div class="text-center py-2 text-muted small"><i class="bi bi-dash-circle me-1"></i>Belum ada data CPMK</div>`;

                        // CPL List
                        const cplContent = (course.cpls && course.cpls.length > 0)
                            ? course.cpls.map(c => {
                                const cc = getWongColor(c.nilai);
                                const cbg = getWongBg(c.nilai);
                                return `
                                    <div class="d-flex align-items-center gap-2 p-2 rounded mb-2 border" style="background: ${cbg}; border-color: ${cc}22 !important;">
                                        <div style="min-width: 70px; flex-shrink: 0;">
                                            <code class="fw-bold" style="color: ${cc}; font-size: 0.78rem;">${c.kode}</code>
                                        </div>
                                        <div class="flex-grow-1" style="min-width: 0;">
                                            <div class="small text-muted text-truncate mb-1" title="${c.deskripsi}">${c.deskripsi}</div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="flex-grow-1" style="height: 5px; border-radius: 99px; background: #e2e8f0;">
                                                    <div style="width: ${Math.min(c.nilai, 100)}%; height: 100%; border-radius: 99px; background: ${cc};"></div>
                                                </div>
                                                <span class="fw-bold small" style="color: ${cc}; min-width: 38px; text-align: right;">${c.nilai.toFixed(1)}</span>
                                            </div>
                                        </div>
                                    </div>`;
                            }).join('')
                            : `<div class="text-center py-2 text-muted small"><i class="bi bi-dash-circle me-1"></i>Belum ada data CPL</div>`;

                        return `
                            <div class="course-competency-card">
                                <div class="course-card-head d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge bg-primary text-white px-2 py-1">#${idx + 1}</span>
                                        <code class="fw-bold px-2 py-1 rounded" style="background: #e2e8f0; color: #1e293b; font-size: 0.82rem;">${course.kode}</code>
                                        <strong class="text-dark fs-6">${course.nama}</strong>
                                        <span class="badge bg-light text-secondary border">${course.sks} SKS</span>
                                        <span class="badge bg-light text-secondary border">Smt ${course.semester || '-'}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="small text-muted">Nilai:</span>
                                        <span class="badge text-white px-2 py-1" style="background: ${wColor};">${nilaiAkhir}</span>
                                        <span class="badge bg-secondary px-2 py-1 fw-bold ${course.grade_color}">${course.grade_huruf}</span>
                                        ${statusBadge}
                                    </div>
                                </div>
                                <div class="p-3">
                                    <div class="row g-3">
                                        <div class="col-lg-6">
                                            <div class="p-2 rounded bg-light border h-100">
                                                <div class="text-muted small fw-bold text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.04em;">
                                                    <i class="bi bi-award me-1"></i>CPL Terkait (${course.cpls?.length || 0})
                                                </div>
                                                ${cplContent}
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="p-2 rounded bg-light border h-100">
                                                <div class="text-muted small fw-bold text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.04em;">
                                                    <i class="bi bi-list-check me-1"></i>CPMK Terkait (${course.cpmks?.length || 0})
                                                </div>
                                                ${cpmkContent}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                    }).join('');
                } else {
                    coursesContainer.innerHTML = `<div class="text-center py-4 text-muted"><i class="bi bi-info-circle me-1"></i>Belum ada data rincian kompetensi mata kuliah</div>`;
                }
            }

            // ─── Show Error ──────────────────────────────────────────────────
            function showError(message) {
                if (loadingSection) {
                    loadingSection.innerHTML = `
                        <div class="alert alert-danger text-center p-4 border-0 rounded-3">
                            <i class="bi bi-exclamation-triangle fs-1 mb-2 d-block"></i>
                            <h6 class="fw-bold mb-1">Gagal Memuat Data</h6>
                            <p class="small mb-0 text-muted">${message}</p>
                        </div>`;
                }
            }
        });
    </script>
@endpush
