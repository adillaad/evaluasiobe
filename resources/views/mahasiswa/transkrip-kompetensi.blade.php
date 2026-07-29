@extends('mahasiswa.template')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/template/css/transkrip.css') }}?v={{ time() }}">
@endpush

@section('content')
    <div class="transcript-container">
        <div class="card">

            {{-- Header Informasi Mahasiswa --}}
            @include('components.transkrip.header-info', [
                'mahasiswaData' => $mahasiswaData,
                'prodi' => $prodi,
            ])

            {{-- Loading Section --}}
            @include('components.transkrip.loading-spinner')

            {{-- Summary CPL --}}
            @include('components.transkrip.kkl-cpl')

            {{-- Transkrip Akademik Table --}}
            @include('components.transkrip.tableakademik')

            {{-- Detail Kompetensi --}}
            @include('components.transkrip.detailcapaiankompetensi')

        </div>

        {{-- Action Buttons --}}
        <div class="actions">
            <a id="printBtn" class="btn btn-primary disabled" target="_blank"
                href="{{ route('mahasiswa.transkrip-kompetensi.print') }}">
                <i class="bi bi-printer"></i> Cetak
            </a>

            <a id="pdfBtn" class="btn btn-danger disabled" target="_blank"
                href="{{ route('mahasiswa.transkrip-kompetensi.pdf') }}">
                <i class="bi bi-file-pdf"></i> Download PDF
            </a>
        </div>
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
            const cplContainer = document.getElementById('cplContainer');
            const coursesContainer = document.getElementById('coursesContainer');
            const simpleTranscriptBody = document.getElementById('simpleTranscriptBody');
            const cplTableBody = document.getElementById('cplTableBody');
            const printBtn = document.getElementById('printBtn');
            const pdfBtn = document.getElementById('pdfBtn');

            // ─── Show Loading ────────────────────────────────────────────────────────
            loadingSection.style.display = 'flex';

            // ─── Fetch Data ──────────────────────────────────────────────────────────
            fetch("{{ route('mahasiswa.get-competency-data') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: new URLSearchParams({
                        npm
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadingSection.style.display = 'none';
                        summarySection.style.display = 'block';
                        simpleTranscriptSection.style.display = 'block';
                        competencySection.style.display = 'block';

                        renderSummary(data.data);
                        renderCplTable(data.data.cpls);
                        renderSimpleTranscript(data.data.courses);
                        renderCompetencyData(data.data);

                        printBtn.classList.remove('disabled');
                        pdfBtn.classList.remove('disabled');
                    } else {
                        showError(data.message || 'Gagal memuat data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Terjadi kesalahan saat memuat data');
                });

            // ─── Wong Color Palette ──────────────────────────────────────────────────
            // ≤50%  → #D55E00 (merah kecoklatan)
            // ≤74%  → #E69F00 (oranye kekuningan)
            // >74%  → #0072B2 (biru)

            function getWongColor(nilai) {
                if (nilai === null || nilai === undefined || nilai === '-') return '';
                const n = parseFloat(nilai);
                if (isNaN(n)) return '';
                if (n <= 50) return '#D55E00';
                if (n <= 74) return '#E69F00';
                return '#0072B2';
            }

            function getWongStyle(nilai) {
                const color = getWongColor(nilai);
                return color ?
                    `style="background-color:${color}; color:#fff; border:none;"` :
                    '';
            }

            // Status teks tanpa background — hanya warna teks (wong palette)
            function getLulusStyle(status) {
                if (status === 'Lulus')
                    return `style="color:#0072B2; font-weight:700; font-size:0.8rem;"`;
                if (status === 'Tidak Lulus')
                    return `style="color:#D55E00; font-weight:700; font-size:0.8rem;"`;
                return '';
            }

            // ─── Render Summary ──────────────────────────────────────────────────────
            function renderSummary(data) {
                const avgCpl = data.avg_cpl;
                const color = getWongColor(avgCpl);

                // Nilai persentase
                const avgEl = document.getElementById('avgCpl');
                avgEl.textContent = `${avgCpl.toFixed(2)}%`;
                avgEl.style.color = color;

                // Status teks (tanpa badge background)
                const statusEl = document.getElementById('cplStatus');
                statusEl.textContent = data.cpl_status;
                statusEl.setAttribute('style',
                    `color:${color}; font-weight:700; font-size:1rem;`
                );

                // Progress bar
                const bar = document.getElementById('progressBar');
                bar.style.width = `${Math.min(avgCpl, 100)}%`;
                bar.style.backgroundColor = color;
            }

            // ─── Render CPL Table ────────────────────────────────────────────────────
            function renderCplTable(cpls) {
                if (!cpls || cpls.length === 0) {
                    cplTableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">
                        <i class="bi bi-info-circle me-2"></i>Belum ada data CPL
                    </td>
                </tr>`;
                    return;
                }

                cplTableBody.innerHTML = cpls.map(cpl => `
            <tr>
                <td><strong>${cpl.kode}</strong></td>
                <td>${cpl.deskripsi}</td>
                <td class="text-center">
                    <span class="badge" ${getWongStyle(cpl.nilai)}>
                        ${cpl.nilai.toFixed(2)}%
                    </span>
                </td>
                <td class="text-center">
                    <span ${getLulusStyle(cpl.status)}>${cpl.status}</span>
                </td>
            </tr>
        `).join('');
            }

            // ─── Render Simple Transcript ────────────────────────────────────────────
            function renderSimpleTranscript(courses) {
                if (!courses || courses.length === 0) {
                    simpleTranscriptBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="bi bi-info-circle me-2"></i>
                        Belum ada data mata kuliah
                    </td>
                </tr>`;
                    return;
                }

                simpleTranscriptBody.innerHTML = courses.map((course, i) => {
                    const nilaiAkhir = course.nilai_akhir ? course.nilai_akhir.toFixed(2) : null;
                    const wongStyle = nilaiAkhir ? getWongStyle(nilaiAkhir) : '';

                    return `
                <tr>
                    <td class="text-center fw-bold">${i + 1}</td>
                    <td class="text-center">${course.kurikulum || '-'}</td>
                    <td class="fw-bold text-primary">${course.kode}</td>
                    <td>${course.nama || '-'}</td>
                    <td class="text-center">${course.semester || '-'}</td>
                    <td class="text-center fw-bold">${course.sks || '-'}</td>
                    <td class="text-center">
                        ${nilaiAkhir
                            ? `<span class="badge" ${wongStyle}>${nilaiAkhir}</span>`
                            : '-'}
                    </td>
                    <td class="text-center fw-bold ${course.grade_color}">
                        ${course.grade_huruf || '-'}
                    </td>
                </tr>`;
                }).join('');
            }

            // ─── Render Competency Data ──────────────────────────────────────────────
            function renderCompetencyData(data) {

                // CPL Summary List
                if (data.cpls && data.cpls.length > 0) {
                    cplContainer.innerHTML = data.cpls.map(cpl => `
                <div class="competency-row">
                    <div class="competency-label">
                        <strong>${cpl.kode}</strong>: ${cpl.deskripsi}
                    </div>
                    <div class="competency-score">
                        <span class="badge" ${getWongStyle(cpl.nilai)}>
                            ${cpl.nilai.toFixed(2)}%
                        </span>
                        <span ${getLulusStyle(cpl.status)}>${cpl.status}</span>
                    </div>
                </div>
            `).join('');
                } else {
                    cplContainer.innerHTML = `<div class="no-data">Belum ada data CPL</div>`;
                }

                // Courses Detail
                if (data.courses && data.courses.length > 0) {
                    coursesContainer.innerHTML = data.courses.map(course => {

                        // CPMK per mata kuliah
                        const cpmkContent = course.cpmks && course.cpmks.length > 0 ?
                            `<div class="cpmk-list">` +
                            course.cpmks.map(c => `
                            <div class="competency-row" style="padding:0.35rem 0;">
                                <div class="competency-label" style="font-size:0.85rem; line-height:1.3;">
                                    <strong>${c.kode}</strong> ${c.deskripsi}
                                </div>
                                <div class="competency-score" style="font-size:0.85rem;">
                                    <span class="badge" ${getWongStyle(c.nilai)}>
                                        ${c.nilai.toFixed(2)}%
                                    </span>
                                    <span ${getLulusStyle(c.status)}>${c.status}</span>
                                </div>
                            </div>
                        `).join('') +
                            `</div>` :
                            `<div class="no-data text-center py-1" style="font-size:0.8rem;">
                           <small>Belum ada data CPMK</small>
                       </div>`;

                        // CPL per mata kuliah
                        const cplCourseContent = course.cpls && course.cpls.length > 0 ?
                            course.cpls.map(c => `
                        <div class="competency-row" style="padding:0.35rem 0;">
                            <div class="competency-label" style="font-size:0.85rem; line-height:1.3;">
                                <strong>${c.kode}</strong> ${c.deskripsi}
                            </div>
                            <div class="competency-score" style="font-size:0.85rem;">
                                <span class="badge" ${getWongStyle(c.nilai)}>
                                    ${c.nilai.toFixed(2)}%
                                </span>
                                <span ${getLulusStyle(c.status)}>${c.status}</span>
                            </div>
                        </div>
                    `).join('') :
                            `<div class="no-data text-center py-1" style="font-size:0.8rem;">
                           <small>Belum ada data CPL</small>
                       </div>`;

                        return `
                    <div class="course-card-compact">
                        <div class="course-header-compact">
                            <h5>${course.kode} - ${course.nama} | ${course.tahun} | SKS: ${course.sks}</h5>
                            <div class="course-meta-compact">
                                <span class="badge ${course.grade_color}">${course.grade_huruf}</span>
                                <span ${getLulusStyle(course.status)}>${course.status}</span>
                            </div>
                        </div>
                        <div class="course-content-compact">
                            <div class="course-section-compact">
                                <h6>Jumlah CPL Terkait (${course.cpls?.length || 0})</h6>
                                ${cplCourseContent}
                            </div>
                            <div class="course-section-compact">
                                <h6>Jumlah CPMK Terkait (${course.cpmks?.length || 0})</h6>
                                ${cpmkContent}
                            </div>
                        </div>
                    </div>`;
                    }).join('');
                } else {
                    coursesContainer.innerHTML = `<div class="no-data">Belum ada data mata kuliah</div>`;
                }
            }

            // ─── Show Error ──────────────────────────────────────────────────────────
            function showError(message) {
                loadingSection.innerHTML = `
            <div class="alert alert-danger text-center p-4">
                <i class="bi bi-exclamation-triangle fs-1 mb-3"></i>
                <h5>Gagal Memuat Data</h5>
                <p>${message}</p>
            </div>`;
            }

        });
    </script>
@endpush
