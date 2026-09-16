@php
    $jenjang = strtoupper(trim($prodi->jenjang ?? ''));
    $isPascasarjana = in_array($jenjang, ['S2', 'S3', 'SPESIALIS', 'MAGISTER', 'DOKTOR', 'SUB SPESIALIS', 'SP-1', 'SP-2', 'S2 TERAPAN', 'S3 TERAPAN']);
@endphp

<div class="modern-card">
    <div class="modern-card-header">
        <div class="section-title">
            <i class="bi bi-info-circle-fill"></i>
            <span>Petunjuk & Indikator Penilaian</span>
        </div>
        <span class="badge bg-light text-secondary border px-3 py-2" style="font-size: 0.8rem;">
            Jenjang: <strong>{{ $prodi->jenjang ?? 'S1 / Sarjana' }}</strong>
        </span>
    </div>

    <div class="p-4">
        <div class="row g-4">
            {{-- Kolom 1: Indikator Ketercapaian Kompetensi OBE (CPL & CPMK) --}}
            <div class="col-lg-6">
                <div class="p-3 rounded-3 border h-100" style="background: #f8fafc;">
                    <div class="fw-bold text-primary mb-2 d-flex align-items-center gap-2" style="font-size: 0.95rem;">
                        <i class="bi bi-award-fill"></i>
                        <span>1. Standar Predikat Kompetensi OBE (CPL & CPMK)</span>
                    </div>
                    <p class="text-muted small mb-3" style="font-size: 0.82rem; line-height: 1.4;">
                        Digunakan untuk mengukur tingkat capaian outcome pada <strong>CPL</strong> dan <strong>CPMK</strong> (Skala 0 - 100):
                    </p>
                    <div class="table-responsive bg-white rounded border">
                        <table class="table table-sm table-bordered mb-0" style="font-size: 0.82rem;">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width: 28%;">Rentang Skor</th>
                                    <th style="width: 28%;">Predikat Mutu</th>
                                    <th>Keterangan Capaian</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center fw-bold" style="color: #0072B2;">&ge; 75.00</td>
                                    <td class="text-center">
                                        <span class="badge text-white" style="background-color: #0072B2;">Sangat Baik</span>
                                    </td>
                                    <td>Memenuhi standar kompetensi unggul</td>
                                </tr>
                                <tr>
                                    <td class="text-center fw-bold" style="color: #E69F00;">51.00 - 74.00</td>
                                    <td class="text-center">
                                        <span class="badge text-white" style="background-color: #E69F00;">Cukup</span>
                                    </td>
                                    <td>Memenuhi standar minimal kompetensi</td>
                                </tr>
                                <tr>
                                    <td class="text-center fw-bold" style="color: #D55E00;">&le; 50.00</td>
                                    <td class="text-center">
                                        <span class="badge text-white" style="background-color: #D55E00;">Perlu Peningkatan</span>
                                    </td>
                                    <td>Belum memenuhi standar minimal capaian</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Kolom 2: Rentang Nilai Mutu Mata Kuliah & Kelulusan --}}
            <div class="col-lg-6">
                <div class="p-3 rounded-3 border h-100" style="background: #f8fafc;">
                    <div class="fw-bold text-success mb-2 d-flex align-items-center gap-2" style="font-size: 0.95rem;">
                        <i class="bi bi-mortarboard-fill"></i>
                        <span>2. Rentang Nilai Mutu Mata Kuliah & Kelulusan</span>
                    </div>
                    <p class="text-muted small mb-3" style="font-size: 0.82rem; line-height: 1.4;">
                        Standar konversi nilai akhir mata kuliah untuk program <strong>{{ $isPascasarjana ? 'Pascasarjana / Spesialis' : 'Diploma / Sarjana / Profesi' }}</strong>:
                    </p>
                    <div class="table-responsive bg-white rounded border">
                        <table class="table table-sm table-bordered mb-0" style="font-size: 0.82rem;">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Nilai Akhir</th>
                                    <th>Huruf Mutu</th>
                                    <th>Angka Mutu</th>
                                    <th>Status Kelulusan</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @if ($isPascasarjana)
                                    <tr>
                                        <td class="fw-bold">&ge; 81.00</td>
                                        <td class="fw-bold text-success">A</td>
                                        <td>4.00</td>
                                        <td><span class="badge bg-success-subtle text-success">Lulus</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">75.00 - &lt; 81.00</td>
                                        <td class="fw-bold text-success">B+</td>
                                        <td>3.50</td>
                                        <td><span class="badge bg-success-subtle text-success">Lulus</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">70.00 - &lt; 75.00</td>
                                        <td class="fw-bold text-success">B</td>
                                        <td>3.00</td>
                                        <td><span class="badge bg-success-subtle text-success">Lulus</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">65.00 - &lt; 70.00</td>
                                        <td class="fw-bold text-success">C+</td>
                                        <td>2.50</td>
                                        <td><span class="badge bg-success-subtle text-success">Lulus (Min.)</span></td>
                                    </tr>
                                    <tr class="table-danger-subtle">
                                        <td class="fw-bold text-danger">&lt; 65.00</td>
                                        <td class="fw-bold text-danger">E</td>
                                        <td>0.00</td>
                                        <td><span class="badge bg-danger-subtle text-danger">Tidak Lulus</span></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td class="fw-bold">&ge; 76.00</td>
                                        <td class="fw-bold text-success">A</td>
                                        <td>4.00</td>
                                        <td><span class="badge bg-success-subtle text-success">Lulus</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">71.00 - &lt; 76.00</td>
                                        <td class="fw-bold text-success">B+</td>
                                        <td>3.50</td>
                                        <td><span class="badge bg-success-subtle text-success">Lulus</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">66.00 - &lt; 71.00</td>
                                        <td class="fw-bold text-success">B</td>
                                        <td>3.00</td>
                                        <td><span class="badge bg-success-subtle text-success">Lulus</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">61.00 - &lt; 66.00</td>
                                        <td class="fw-bold text-success">C+</td>
                                        <td>2.50</td>
                                        <td><span class="badge bg-success-subtle text-success">Lulus</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">56.00 - &lt; 61.00</td>
                                        <td class="fw-bold text-success">C</td>
                                        <td>2.00</td>
                                        <td><span class="badge bg-success-subtle text-success">Lulus</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">50.00 - &lt; 56.00</td>
                                        <td class="fw-bold text-warning">D</td>
                                        <td>1.00</td>
                                        <td><span class="badge bg-warning-subtle text-warning">Lulus (Min.)</span></td>
                                    </tr>
                                    <tr class="table-danger-subtle">
                                        <td class="fw-bold text-danger">&lt; 50.00</td>
                                        <td class="fw-bold text-danger">E</td>
                                        <td>0.00</td>
                                        <td><span class="badge bg-danger-subtle text-danger">Tidak Lulus</span></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
