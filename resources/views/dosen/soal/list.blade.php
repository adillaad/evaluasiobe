@php
    $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
    $currentPrefix = $userOtoritas
        ? str_replace(' ', '-', strtolower($userOtoritas)) . '.'
        : 'dosen.';
@endphp
@extends('dosen.template')
@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h4 class="fw-bold mb-1">Daftar Soal</h4>
                        <p class="text-muted small mb-0">
                            Buat soal, lalu <strong>ajukan</strong> agar dapat ditinjau oleh Penjamin Mutu.
                        </p>
                    </div>
                    <a href="{{ route($currentPrefix . 'soal-addRaw') }}" class="btn btn-primary btn-sm fw-semibold px-3">
                        <i class="ti-plus me-1"></i> Tambah Soal
                    </a>
                </div>

                {{-- Info status --}}
                <div class="alert alert-info py-2 mb-3 small">
                    <i class="ti-info-alt me-1"></i>
                    <span class="badge bg-secondary">Belum</span> = baru dibuat &nbsp;|&nbsp;
                    <span class="badge bg-primary">Menunggu</span> = sudah diajukan &nbsp;|&nbsp;
                    <span class="badge bg-success">Valid</span> = disetujui &nbsp;|&nbsp;
                    <span class="badge bg-danger">Tolak</span> = ditolak, perlu diperbaiki
                </div>

                {{-- Filter Bar (Mata Kuliah & Metode Penilaian) --}}
                <form method="GET" action="{{ route($currentPrefix . 'soal-list') }}" class="card bg-light border-0 rounded-3 p-3 mb-4">
                    <div class="row g-2 align-items-end">
                        {{-- Filter Mata Kuliah --}}
                        <div class="col-md-5">
                            <label for="kode_mk" class="form-label font-13 fw-semibold text-dark mb-1">Mata Kuliah</label>
                            <select name="kode_mk" id="kode_mk" class="form-select form-select-sm border-slate rounded-2" style="height: 38px; font-size: 13.5px;">
                                <option value="">-- Semua Mata Kuliah --</option>
                                @foreach ($mksFilter ?? [] as $mk)
                                    <option value="{{ $mk->kode }}" {{ request('kode_mk') == $mk->kode ? 'selected' : '' }}>
                                        {{ $mk->kode }} - {{ $mk->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Metode Penilaian --}}
                        <div class="col-md-4">
                            <label for="jenis" class="form-label font-13 fw-semibold text-dark mb-1">Metode Penilaian</label>
                            <select name="jenis" id="jenis" class="form-select form-select-sm border-slate rounded-2" style="height: 38px; font-size: 13.5px;">
                                <option value="">-- Semua Metode Penilaian --</option>
                                @foreach ($metodeFilter ?? [] as $metode)
                                    <option value="{{ $metode->id }}" {{ request('jenis') == $metode->id ? 'selected' : '' }}>
                                        {{ $metode->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold d-inline-flex align-items-center justify-content-center gap-1" style="height: 38px;">
                                    <i class="ti-filter"></i> Filter
                                </button>
                                <a href="{{ route($currentPrefix . 'soal-list') }}" class="btn btn-outline-secondary btn-sm w-100 fw-semibold d-inline-flex align-items-center justify-content-center gap-1" style="height: 38px;">
                                    <i class="ti-reload"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Baris kontrol: per-page (kiri) + info total (kanan) --}}
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">

                    {{-- Dropdown jumlah per halaman --}}
                    <form method="GET" action="{{ request()->url() }}" class="d-flex align-items-center gap-2">
                        {{-- Pertahankan semua parameter filter yang aktif --}}
                        @foreach (request()->except('per_page', 'page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach

                        <label class="text-muted small mb-0 text-nowrap">Tampilkan</label>
                        <select name="per_page" class="form-select form-select-sm" style="width: auto; padding-right: 1.8rem; height: 31px;"
                            onchange="this.form.submit()">
                            @foreach ([10, 25, 50, 100] as $option)
                                <option value="{{ $option }}"
                                    {{ request('per_page', 10) == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-muted small mb-0 text-nowrap">soal per halaman</span>
                    </form>

                    {{-- Info total record --}}
                    <span class="text-muted small">
                        Menampilkan
                        <strong>{{ $soals->firstItem() ?? 0 }}–{{ $soals->lastItem() ?? 0 }}</strong>
                        dari <strong>{{ $soals->total() }}</strong> soal
                    </span>

                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="4%">No</th>
                                <th width="22%">Mata Kuliah</th>
                                <th width="16%">Metode Penilaian</th>
                                <th width="14%">CPL → CPMK</th>
                                <th width="11%" class="text-center">Status</th>
                                <th width="15%">Komentar</th>
                                <th width="18%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($soals as $soal)
                                <tr>
                                    <td class="text-muted small">{{ $soals->firstItem() + $loop->index }}</td>

                                    {{-- Mata Kuliah --}}
                                    <td>
                                        @if ($soal->nama_mk)
                                            <code class="small">{{ $soal->kode_mk }}</code><br>
                                            <strong class="small">{{ $soal->nama_mk }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Metode --}}
                                    <td class="small">{{ $soal->nama_kriteria ?? '-' }}</td>

                                    {{-- CPL → CPMK --}}
                                    <td>
                                        <span
                                            class="badge bg-primary bg-opacity-10 text-primary">{{ $soal->cpl_kode ?? '-' }}</span>
                                        <i class="ti-arrow-right mx-1 small text-muted"></i>
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success">{{ $soal->cpmk_kode ?? '-' }}</span>
                                    </td>

                                    {{-- Status --}}
                                    <td class="text-center">
                                        @php
                                            $statusMap = [
                                                'Valid' => ['bg-success', 'Disetujui'],
                                                'Menunggu' => ['bg-primary', 'Menunggu'],
                                                'Tolak' => ['bg-danger', 'Ditolak'],
                                                'Belum' => ['bg-secondary', 'Belum'],
                                            ];
                                            [$badgeClass, $badgeLabel] = $statusMap[$soal->status] ?? [
                                                'bg-secondary',
                                                $soal->status,
                                            ];
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                    </td>

                                    {{-- Komentar --}}
                                    <td>
                                        @if ($soal->komentar)
                                            <small class="text-danger" data-bs-toggle="tooltip"
                                                title="{{ $soal->komentar }}">
                                                {{ Str::limit($soal->komentar, 35) }}
                                            </small>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-1 flex-nowrap">

                                            {{-- Hidden container for rich HTML pertanyaan --}}
                                            <div id="pertanyaan-content-{{ $soal->id }}" class="d-none">
                                                {!! $soal->pertanyaan ?? '<span class="text-muted fst-italic">Belum ada pertanyaan yang diisi.</span>' !!}
                                            </div>

                                            {{-- Tombol Lihat/View Soal (Tersedia untuk semua status) --}}
                                            <button type="button" class="btn btn-action-table btn-action-outline-blue btn-view-soal"
                                                data-bs-toggle="tooltip" title="Lihat Isi Soal"
                                                data-id="{{ $soal->id }}"
                                                data-mk="{{ $soal->kode_mk }} - {{ $soal->nama_mk }}"
                                                data-metode="{{ $soal->nama_kriteria ?? '-' }}"
                                                data-cpl="{{ $soal->cpl_kode ?? '-' }}"
                                                data-cpl-judul="{{ $soal->cpl_judul ?? '' }}"
                                                data-cpmk="{{ $soal->cpmk_kode ?? '-' }}"
                                                data-cpmk-judul="{{ $soal->cpmk_judul ?? '' }}"
                                                data-minggu="{{ $soal->minggu ?? '-' }}"
                                                data-status="{{ $badgeLabel }}"
                                                data-status-class="{{ $badgeClass }}"
                                                data-komentar="{{ $soal->komentar ?? '' }}"
                                                data-edit-url="{{ in_array($soal->status, ['Belum', 'Tolak']) ? url('/dosen/soal/edit-soal/' . $soal->id) : '' }}">
                                                <i class="ti-eye"></i>
                                            </button>

                                            @if ($soal->status === 'Valid')
                                                <span class="badge bg-success px-2 py-1 ms-1" data-bs-toggle="tooltip"
                                                    title="Soal telah disetujui dan tidak dapat diubah">
                                                    <i class="ti-lock me-1"></i> Terkunci
                                                </span>
                                            @elseif ($soal->status === 'Menunggu')
                                                <button class="btn btn-action-table btn-action-outline-blue" disabled data-bs-toggle="tooltip"
                                                    title="Sedang ditinjau, tidak dapat diedit">
                                                    <i class="ti-pencil"></i>
                                                </button>
                                                <button class="btn btn-action-table btn-action-solid-blue" disabled data-bs-toggle="tooltip"
                                                    title="Sudah diajukan">
                                                    <i class="ti-share"></i>
                                                </button>
                                                <button class="btn btn-action-table btn-action-outline-red" disabled data-bs-toggle="tooltip"
                                                    title="Sedang ditinjau, tidak dapat dihapus">
                                                    <i class="ti-trash"></i>
                                                </button>
                                            @elseif (in_array($soal->status, ['Belum', 'Tolak']))
                                                <a href="/dosen/soal/edit-soal/{{ $soal->id }}"
                                                    class="btn btn-action-table btn-action-outline-blue" data-bs-toggle="tooltip"
                                                    title="Edit Soal">
                                                    <i class="ti-pencil"></i>
                                                </a>

                                                <form action="{{ route($currentPrefix . 'soal-ajukan', $soal->id) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Ajukan soal ini ke Penjamin Mutu?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-action-table btn-action-solid-blue"
                                                        data-bs-toggle="tooltip" title="Ajukan ke Penjamin Mutu">
                                                        <i class="ti-share"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ url('dosen/soal/delete-soal/' . $soal->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Hapus soal ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-action-table btn-action-outline-red"
                                                        data-bs-toggle="tooltip" title="Hapus Soal">
                                                        <i class="ti-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="ti-folder me-2"></i>Belum ada soal. Silakan buat soal terlebih dahulu.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination — tetap membawa per_page dan filter lain --}}
                <div class="mt-2">
                    {{ $soals->appends(request()->query())->links() }}
                </div>

            </div>
        </div>
    </div>

    {{-- Modal View Detail & Pratinjau Soal --}}
    <div class="modal fade" id="modalViewSoal" tabindex="-1" aria-labelledby="modalViewSoalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-primary text-white py-3 px-4 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="ti-eye font-16"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0" id="modalViewSoalLabel">Detail & Pratinjau Soal</h5>
                            <small class="text-white text-opacity-75" id="viewModalMK">-</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span id="viewModalStatusBadge" class="badge bg-secondary">-</span>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>

                <div class="modal-body p-4 bg-light">
                    {{-- Metadata Strip --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="bg-white p-3 rounded-3 border h-100 shadow-sm">
                                <div class="small text-muted fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px; font-size: 0.75rem;">Metode Penilaian</div>
                                <div class="fw-bold text-dark font-15" id="viewModalMetode">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-white p-3 rounded-3 border h-100 shadow-sm">
                                <div class="small text-muted fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px; font-size: 0.75rem;">Waktu Pelaksanaan</div>
                                <div class="fw-bold text-dark font-15">Minggu ke-<span id="viewModalMinggu">-</span></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="bg-white p-3 rounded-3 border shadow-sm">
                                <div class="small text-muted fw-bold text-uppercase mb-2" style="letter-spacing: 0.5px; font-size: 0.75rem;">CPL & CPMK Terkait</div>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3 bg-light border-start border-primary border-3 h-100">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge bg-primary text-white fw-bold font-13" id="viewModalCPLKode">-</span>
                                                <span class="small fw-semibold text-primary">CPL (Capaian Pembelajaran Lulusan)</span>
                                            </div>
                                            <div class="small text-secondary" id="viewModalCPLJudul" style="line-height: 1.5;">-</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3 bg-light border-start border-success border-3 h-100">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge bg-success text-white fw-bold font-13" id="viewModalCPMKKode">-</span>
                                                <span class="small fw-semibold text-success">CPMK (Capaian Pembelajaran MK)</span>
                                            </div>
                                            <div class="small text-secondary" id="viewModalCPMKJudul" style="line-height: 1.5;">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Catatan Revisi / Komentar Penjamin Mutu jika ada --}}
                    <div id="viewModalKomentarBox" class="alert alert-danger d-flex align-items-start gap-2 mb-3 d-none">
                        <i class="ti-comment-alt font-18 mt-1 text-danger"></i>
                        <div>
                            <strong class="d-block mb-1">Catatan Revisi / Komentar Penjamin Mutu:</strong>
                            <span id="viewModalKomentarText"></span>
                        </div>
                    </div>

                    {{-- Kartu Isi Pertanyaan Soal --}}
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                            <span class="fw-bold small text-muted text-uppercase" style="letter-spacing: 0.5px;">
                                <i class="ti-help-alt me-1 text-primary"></i> Isi Pertanyaan Soal
                            </span>
                        </div>
                        <div class="card-body p-4 bg-white soal-content-view" id="viewModalPertanyaan" style="min-height: 140px; font-size: 1rem; line-height: 1.7;">
                            <!-- Rendered soal content -->
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top py-2 px-4 d-flex justify-content-between">
                    <a id="viewModalBtnEdit" href="#" class="btn btn-outline-primary btn-sm px-3 d-none">
                        <i class="ti-pencil me-1"></i> Edit Soal Ini
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm px-4 ms-auto" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        #modalViewSoal {
            z-index: 10600 !important;
        }

        #backdropViewSoal,
        .modal-backdrop {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            background-color: rgba(0, 0, 0, 0.6) !important;
            backdrop-filter: blur(2px) !important;
            -webkit-backdrop-filter: blur(2px) !important;
            z-index: 10550 !important;
            opacity: 0;
            transition: opacity 0.2s ease-in-out !important;
        }

        #backdropViewSoal.show,
        .modal-backdrop.show {
            opacity: 1 !important;
        }

        .soal-content-view img.math-tex,
        .soal-content-view img[src*="latex.codecogs.com"] {
            vertical-align: middle;
            display: inline-block;
            margin: 0 4px;
            max-height: 2.8em;
        }

        .soal-content-view table {
            width: 100% !important;
            border-collapse: collapse;
            margin: 12px 0;
        }

        .soal-content-view table td,
        .soal-content-view table th {
            border: 1px solid #dee2e6;
            padding: 8px 12px;
        }

        .soal-content-view p:last-child {
            margin-bottom: 0;
        }

        /* Action Buttons Sesuai Model Gambar (#006199 & #dc3545) */
        .btn-action-table {
            width: 32px;
            height: 32px;
            min-width: 32px;
            padding: 0 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            font-size: 13.5px;
            line-height: 1;
            border: 1.5px solid transparent;
            transition: all 0.2s ease-in-out;
            text-decoration: none;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .btn-action-table:hover:not(:disabled) {
            transform: translateY(-2px);
        }

        .btn-action-table:active:not(:disabled) {
            transform: translateY(0);
        }

        /* 1. Outline Biru (Detail / Edit) - Background putih, border biru #006199 */
        .btn-action-outline-blue {
            background-color: #ffffff !important;
            border-color: #006199 !important;
            color: #006199 !important;
        }
        .btn-action-outline-blue:hover:not(:disabled),
        .btn-action-outline-blue:focus:not(:disabled) {
            background-color: #006199 !important;
            border-color: #006199 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(0, 97, 153, 0.28) !important;
        }

        /* 2. Solid Biru (Ajukan / Utama) - Background biru #006199, icon putih */
        .btn-action-solid-blue {
            background-color: #006199 !important;
            border-color: #006199 !important;
            color: #ffffff !important;
        }
        .btn-action-solid-blue:hover:not(:disabled),
        .btn-action-solid-blue:focus:not(:disabled) {
            background-color: #004c78 !important;
            border-color: #004c78 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(0, 76, 120, 0.32) !important;
        }

        /* 3. Outline Merah (Hapus) - Background putih, border merah #dc3545 */
        .btn-action-outline-red {
            background-color: #ffffff !important;
            border-color: #dc3545 !important;
            color: #dc3545 !important;
        }
        .btn-action-outline-red:hover:not(:disabled),
        .btn-action-outline-red:focus:not(:disabled) {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.28) !important;
        }

        .btn-action-table:disabled {
            opacity: 0.45;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }
    </style>

    <script>
        (function() {
            function initModalPlacement() {
                const modalEl = document.getElementById('modalViewSoal');
                if (modalEl) {
                    if (modalEl.parentElement !== document.body) {
                        document.body.appendChild(modalEl);
                    }
                    modalEl.addEventListener('hidden.bs.modal', closeViewModal);
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initModalPlacement);
            } else {
                initModalPlacement();
            }

            function closeViewModal() {
                const modalEl = document.getElementById('modalViewSoal');
                if (!modalEl) return;

                if (window.bootstrap && window.bootstrap.Modal) {
                    try {
                        const inst = window.bootstrap.Modal.getInstance(modalEl);
                        if (inst) inst.hide();
                    } catch (e) {}
                }

                if (window.jQuery && typeof window.jQuery(modalEl).modal === 'function') {
                    try {
                        window.jQuery(modalEl).modal('hide');
                    } catch (e) {}
                }

                modalEl.classList.remove('show');
                modalEl.style.display = 'none';
                modalEl.setAttribute('aria-hidden', 'true');
                modalEl.removeAttribute('aria-modal');

                const backdrop = document.getElementById('backdropViewSoal');
                if (backdrop) backdrop.remove();
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
            }

            document.addEventListener('click', function(e) {
                // Tombol Buka Modal View Soal
                const btn = e.target.closest('.btn-view-soal');
                if (btn) {
                    e.preventDefault();
                    e.stopPropagation();

                    const id = btn.getAttribute('data-id');
                    const mk = btn.getAttribute('data-mk');
                    const metode = btn.getAttribute('data-metode');
                    const cpl = btn.getAttribute('data-cpl');
                    const cplJudul = btn.getAttribute('data-cpl-judul');
                    const cpmk = btn.getAttribute('data-cpmk');
                    const cpmkJudul = btn.getAttribute('data-cpmk-judul');
                    const minggu = btn.getAttribute('data-minggu');
                    const status = btn.getAttribute('data-status');
                    const statusClass = btn.getAttribute('data-status-class');
                    const komentar = btn.getAttribute('data-komentar');
                    const editUrl = btn.getAttribute('data-edit-url');

                    const contentEl = document.getElementById('pertanyaan-content-' + id);
                    const content = contentEl ? contentEl.innerHTML : '';

                    const mkEl = document.getElementById('viewModalMK');
                    if (mkEl) mkEl.textContent = mk || '-';

                    const metodeEl = document.getElementById('viewModalMetode');
                    if (metodeEl) metodeEl.textContent = metode || '-';

                    const cplKodeEl = document.getElementById('viewModalCPLKode');
                    if (cplKodeEl) cplKodeEl.textContent = cpl || '-';

                    const cplJudulEl = document.getElementById('viewModalCPLJudul');
                    if (cplJudulEl) cplJudulEl.textContent = cplJudul || 'Tidak ada keterangan tambahan.';

                    const cpmkKodeEl = document.getElementById('viewModalCPMKKode');
                    if (cpmkKodeEl) cpmkKodeEl.textContent = cpmk || '-';

                    const cpmkJudulEl = document.getElementById('viewModalCPMKJudul');
                    if (cpmkJudulEl) cpmkJudulEl.textContent = cpmkJudul || 'Tidak ada keterangan tambahan.';

                    const mingguEl = document.getElementById('viewModalMinggu');
                    if (mingguEl) mingguEl.textContent = minggu || '-';

                    const badgeEl = document.getElementById('viewModalStatusBadge');
                    if (badgeEl) {
                        badgeEl.className = 'badge ' + (statusClass || 'bg-secondary');
                        badgeEl.textContent = status || '-';
                    }

                    const komentarBox = document.getElementById('viewModalKomentarBox');
                    const komentarText = document.getElementById('viewModalKomentarText');
                    if (komentar && String(komentar).trim() !== '') {
                        if (komentarText) komentarText.textContent = komentar;
                        if (komentarBox) komentarBox.classList.remove('d-none');
                    } else {
                        if (komentarBox) komentarBox.classList.add('d-none');
                    }

                    const pertanyaanEl = document.getElementById('viewModalPertanyaan');
                    if (pertanyaanEl) {
                        pertanyaanEl.innerHTML = (content && content.trim() !== '') ? content : '<span class="text-muted fst-italic">Belum ada pertanyaan yang diisi.</span>';
                    }

                    const btnEdit = document.getElementById('viewModalBtnEdit');
                    if (btnEdit) {
                        if (editUrl) {
                            btnEdit.setAttribute('href', editUrl);
                            btnEdit.classList.remove('d-none');
                        } else {
                            btnEdit.classList.add('d-none');
                        }
                    }

                    // Tampilkan modal
                    const modalEl = document.getElementById('modalViewSoal');
                    if (!modalEl) {
                        console.error('Modal #modalViewSoal tidak ditemukan');
                        return;
                    }

                    if (modalEl.parentElement !== document.body) {
                        document.body.appendChild(modalEl);
                    }

                    modalEl.style.removeProperty('display');
                    modalEl.style.zIndex = '10600';

                    const oldBackdrop = document.getElementById('backdropViewSoal');
                    if (oldBackdrop) oldBackdrop.remove();

                    // Fungsi untuk memastikan elemen backdrop hitam selalu aktif
                    function ensureBackdrop() {
                        setTimeout(function() {
                            let bd = document.getElementById('backdropViewSoal') || document.querySelector('.modal-backdrop');
                            if (!bd) {
                                bd = document.createElement('div');
                                bd.id = 'backdropViewSoal';
                                bd.className = 'modal-backdrop fade show';
                                document.body.appendChild(bd);
                            } else {
                                bd.classList.add('show');
                            }
                            document.body.classList.add('modal-open');
                        }, 25);
                    }

                    if (window.bootstrap && window.bootstrap.Modal) {
                        try {
                            window.bootstrap.Modal.getOrCreateInstance(modalEl, { backdrop: true }).show();
                            ensureBackdrop();
                            return;
                        } catch (err) {
                            console.warn('Bootstrap modal error:', err);
                        }
                    }

                    if (window.jQuery && typeof window.jQuery(modalEl).modal === 'function') {
                        try {
                            window.jQuery(modalEl).modal({ backdrop: true, show: true });
                            ensureBackdrop();
                            return;
                        } catch (err) {
                            console.warn('jQuery modal error:', err);
                        }
                    }

                    // Fallback CSS
                    modalEl.classList.add('show');
                    modalEl.style.display = 'block';
                    modalEl.removeAttribute('aria-hidden');
                    modalEl.setAttribute('aria-modal', 'true');
                    ensureBackdrop();
                    return;
                }

                // Tombol Tutup Modal View Soal atau Klik Backdrop
                if (e.target.closest('#modalViewSoal [data-bs-dismiss="modal"]') || 
                    e.target.closest('#modalViewSoal [data-dismiss="modal"]') || 
                    e.target.id === 'modalViewSoal' ||
                    e.target.id === 'backdropViewSoal' ||
                    e.target.classList.contains('modal-backdrop')) {
                    closeViewModal();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeViewModal();
                }
            });
        })();
    </script>
    <script src="{{ asset('assets/js/dynamic-filters.js') }}"></script>
@endsection