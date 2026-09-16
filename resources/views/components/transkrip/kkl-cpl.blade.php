<div id="summarySection" style="display:none;">
    {{-- ── 1. Bagian Atas: Rerata Capaian CPL ─────────────────────────── --}}
    <div class="p-4 rounded-3 border mb-4 text-center"
        style="background: #f8fafc; border-color: #e2e8f0 !important;">
        <div class="row align-items-center justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="text-muted small fw-bold text-uppercase mb-2" style="letter-spacing: 0.05em;">
                    Rata-rata Capaian Pembelajaran Lulusan (CPL)
                </div>
                <div class="fw-bold mb-1" id="avgCpl" style="font-size: 2.8rem; line-height: 1.1;">
                    0.00 <span class="fs-6 fw-normal text-muted">/ 100</span>
                </div>
                <div class="mb-3" id="cplStatus" style="font-weight: 700; font-size: 1.1rem;">-</div>
                <div class="progress mb-3 mx-auto" style="height: 10px; max-width: 480px; border-radius: 99px; background: #e2e8f0;">
                    <div class="progress-bar" id="progressBar" style="width: 0%; border-radius: 99px; transition: width 0.4s ease;"></div>
                </div>
                <p class="text-muted small mb-0" style="font-size: 0.84rem;">
                    Berdasarkan rata-rata capaian CPL pada seluruh mata kuliah yang telah diselesaikan
                </p>
            </div>
        </div>
    </div>

    {{-- ── 2. Bagian Bawah: Rincian & Deskripsi Capaian per CPL ────────── --}}
    <div class="table-responsive border rounded-3 bg-white">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.04em; color: #475569;">
                <tr>
                    <th style="width: 120px;" class="text-center">Kode CPL</th>
                    <th>Deskripsi Capaian Pembelajaran Lulusan</th>
                    <th style="width: 120px;" class="text-center">Skor CPL</th>
                    <th style="width: 140px;" class="text-center">Predikat</th>
                </tr>
            </thead>
            <tbody id="cplTableBody" style="font-size: 0.86rem;">
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                        <span class="ms-2">Memuat data CPL...</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
