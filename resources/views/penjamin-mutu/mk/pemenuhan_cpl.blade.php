@php
    $routePrefix = [
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
        'Penjamin Mutu Universitas' => ['prefix' => 'penjamin-mutu.universitas.'],
        'Penjamin Mutu Fakultas' => ['prefix' => 'penjamin-mutu.fakultas.'],
        'Dosen' => ['prefix' => 'dosen.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp
@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')

@section('content')

    <div class="container-fluid px-4 py-3">
        @if(isset($kurikulums))
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body pb-3">
                    <x-filter-form :showKurikulum="true" :kurikulums="$kurikulums" />
                </div>
            </div>
        @endif

        <!-- Notification Banner for Matrix Edit Mode -->
        <div id="matrix-edit-banner" class="alert alert-info border-0 shadow-sm rounded-4 mb-4 p-3 d-none align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="badge bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center">
                    <i class="mdi mdi-pencil font-18 text-white"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-primary">Mode Edit Pemenuhan CPL Aktif</h6>
                    <small class="text-secondary">Centang atau hapus centang Mata Kuliah di tiap Semester untuk mengatur pemenuhan tiap CPL.</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" id="btn-cancel-matrix-banner" class="btn btn-sm btn-light rounded-pill px-3">Batal</button>
                <button type="submit" form="form-matrix-edit" class="btn btn-sm btn-primary rounded-pill px-4 font-weight-bold d-inline-flex align-items-center gap-1 shadow-sm">
                    <i class="mdi mdi-content-save-check font-16"></i>
                    <span>Simpan Matriks</span>
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    <div>
                        <h4 class="card-title fw-bold mb-1">Pemenuhan CPL</h4>
                        <p class="text-muted small mb-0">Pemetaan keterhubungan pemenuhan Capaian Pembelajaran Lulusan (CPL) berdasarkan Mata Kuliah per Semester.</p>
                    </div>

                    @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas']))
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" id="btn-toggle-matrix-edit" class="btn btn-outline-primary rounded-3 font-weight-bold d-inline-flex align-items-center gap-1 px-3 py-2">
                                <i class="mdi mdi-pencil-box-multiple-outline font-18"></i>
                                <span id="btn-matrix-text">Edit Matriks</span>
                            </button>
                        </div>
                    @endif
                </div>

                <form id="form-matrix-edit" action="{{ route($currentPrefix . 'mk.pemenuhan-cpl-matrix-update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="kurikulum_id" value="{{ request('kurikulum_id') }}">

                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-hover align-middle w-100 mb-0" id="matrix-table">
                            <thead class="bg-light">
                                <tr>
                                    <th rowspan="2" class="text-center align-middle" style="width: 130px;">CPL</th>
                                    <th colspan="{{ $maxSemester }}" class="text-center py-2 fw-bold">Semester</th>
                                </tr>
                                <tr>
                                    @foreach (range(1, $maxSemester) as $semester)
                                        <th class="text-center py-2" style="min-width: 110px;">{{ $semester }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cpls as $cpl)
                                    @php
                                        $mappedMks = $cpl->mk;
                                        $mappedMkKodes = $mappedMks->pluck('kode')->toArray();
                                        $mkGroupedBySemester = $mappedMks->groupBy('semester');
                                    @endphp
                                    <tr>
                                        <td class="bg-light fw-bold text-dark text-center align-middle" title="{{ $cpl->kode }}: {{ $cpl->judul }}">
                                            <span class="badge bg-primary-soft text-primary font-13 px-2.5 py-1.5 rounded-2 d-inline-block mb-1">{{ $cpl->kode }}</span>
                                            <div class="font-10 text-muted fw-normal">Kur {{ $cpl->kurikulum->tahun ?? '-' }}</div>
                                        </td>
                                        @foreach (range(1, $maxSemester) as $semester)
                                            @php
                                                $semesterMks = $mksBySemester->get($semester, collect());
                                                $mappedInSemester = $mkGroupedBySemester->get($semester, collect());
                                            @endphp
                                            <td class="align-top p-2 matrix-cell">
                                                <!-- View Mode -->
                                                <div class="cell-view-mode">
                                                    @if ($mappedInSemester->isNotEmpty())
                                                        <div class="d-flex flex-column gap-1">
                                                            @foreach ($mappedInSemester as $mk)
                                                                <span class="badge font-11 px-2 py-1 rounded-2 text-start"
                                                                      style="background-color: @if ($mk->rumpun === 'Wajib') #dcfce7; color: #15803d; border: 1px solid #bbf7d0; @elseif($mk->rumpun === 'Peminatan') #fef3c7; color: #b45309; border: 1px solid #fde68a; @elseif($mk->rumpun === 'Wajib_kurikulum') #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; @else #f1f5f9; color: #334155; border: 1px solid #e2e8f0; @endif"
                                                                      title="{{ $mk->kode }}: {{ $mk->nama }}">
                                                                    {{ $mk->kode }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="text-muted opacity-25 font-14 text-center d-block">-</span>
                                                    @endif
                                                </div>

                                                <!-- Edit Mode -->
                                                <div class="cell-edit-mode d-none flex-column gap-1">
                                                    @forelse ($semesterMks as $mkItem)
                                                        @php
                                                            $isAttached = in_array($mkItem->kode, $mappedMkKodes);
                                                        @endphp
                                                        <label class="form-check-label d-flex align-items-center gap-1.5 p-1 rounded-2 cursor-pointer border hover-bg-light" style="font-size: 11px;">
                                                            <input type="checkbox"
                                                                   class="form-check-input matrix-checkbox m-0"
                                                                   name="matrix[{{ $cpl->id }}][]"
                                                                   value="{{ $mkItem->kode }}"
                                                                   {{ $isAttached ? 'checked' : '' }}>
                                                            <span class="fw-semibold text-dark">{{ $mkItem->kode }}</span>
                                                        </label>
                                                    @empty
                                                        <span class="text-muted font-11 fst-italic">Tidak ada MK</span>
                                                    @endforelse
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 1 + $maxSemester }}" class="text-center py-4 text-muted">
                                            <i class="mdi mdi-file-table-outline font-32 d-block mb-1"></i>
                                            Belum ada data CPL.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Action Bar inside Form for Edit Mode -->
                    <div id="matrix-save-bar" class="d-none justify-content-end gap-2 pt-3 border-top mt-3">
                        <button type="button" id="btn-cancel-matrix-bottom" class="btn btn-light px-4 py-2 rounded-3">Batal Edit</button>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 font-weight-bold d-inline-flex align-items-center gap-2 shadow-sm">
                            <i class="mdi mdi-content-save-check font-18"></i>
                            <span>Simpan Perubahan Matriks</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-4">
                <h4 class="card-title fw-bold mb-3">Deskripsi Pemenuhan CPL</h4>
                <div class="scrollable-descriptions pe-2" style="max-height: 400px; overflow-y: auto;">
                    @foreach ($cpls as $cpl)
                        <div class="mb-4 p-3 bg-light rounded-3 border-light">
                            <h6 class="fw-bold text-primary mb-1">
                                <span class="badge bg-primary text-white me-2">{{ $cpl->kode }}</span>
                                {{ $cpl->judul }}
                            </h6>
                            <div class="font-13 mt-2">
                                <strong class="text-dark d-block mb-1">Mata Kuliah Pemenuh:</strong>
                                @if ($cpl->mk->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($cpl->mk as $mk)
                                            <span class="badge bg-white text-dark border px-2 py-1.5 rounded-2 shadow-xs">
                                                <strong class="text-primary">{{ $mk->kode }}:</strong> {{ $mk->nama }} (Sem {{ $mk->semester }})
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted fst-italic">Belum ada mata kuliah pemenuh.</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

<style>
#matrix-table {
    border-collapse: separate !important;
    border-spacing: 0 !important;
    border: 1px solid #e2e8f0 !important;
}

#matrix-table th {
    border-bottom: 2px solid #e2e8f0 !important;
    border-right: 1px solid rgba(0, 0, 0, 0.04) !important;
}

#matrix-table tbody tr {
    border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
}

#matrix-table tbody td {
    border-top: 1px solid rgba(0, 0, 0, 0.06) !important;
    border-bottom: 1px solid rgba(0, 0, 0, 0.06) !important;
    border-right: 1px solid rgba(0, 0, 0, 0.04) !important;
}

.bg-primary-soft {
    background-color: rgba(2, 132, 199, 0.12);
}
.matrix-cell {
    position: relative;
    transition: background-color 0.15s ease-in-out;
}
.editing-active .cell-view-mode {
    display: none !important;
}
.editing-active .cell-edit-mode {
    display: flex !important;
}
.hover-bg-light:hover {
    background-color: #f8fafc;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnToggleMatrixEdit = document.getElementById('btn-toggle-matrix-edit');
    const btnMatrixText = document.getElementById('btn-matrix-text');
    const matrixBanner = document.getElementById('matrix-edit-banner');
    const matrixSaveBar = document.getElementById('matrix-save-bar');
    const matrixTable = document.getElementById('matrix-table');

    const btnCancelBanner = document.getElementById('btn-cancel-matrix-banner');
    const btnCancelBottom = document.getElementById('btn-cancel-matrix-bottom');

    let isEditMode = false;

    function enableEditMode() {
        isEditMode = true;
        matrixTable.classList.add('editing-active');
        matrixBanner.classList.remove('d-none');
        matrixBanner.classList.add('d-flex');
        matrixSaveBar.classList.remove('d-none');
        matrixSaveBar.classList.add('d-flex');
        
        btnToggleMatrixEdit.classList.remove('btn-outline-primary');
        btnToggleMatrixEdit.classList.add('btn-secondary');
        btnMatrixText.textContent = 'Batal Edit';
    }

    function disableEditMode() {
        isEditMode = false;
        matrixTable.classList.remove('editing-active');
        matrixBanner.classList.add('d-none');
        matrixBanner.classList.remove('d-flex');
        matrixSaveBar.classList.add('d-none');
        matrixSaveBar.classList.remove('d-flex');

        btnToggleMatrixEdit.classList.remove('btn-secondary');
        btnToggleMatrixEdit.classList.add('btn-outline-primary');
        btnMatrixText.textContent = 'Edit Matriks';
    }

    if (btnToggleMatrixEdit) {
        btnToggleMatrixEdit.addEventListener('click', function () {
            if (isEditMode) {
                disableEditMode();
            } else {
                enableEditMode();
            }
        });
    }

    if (btnCancelBanner) btnCancelBanner.addEventListener('click', disableEditMode);
    if (btnCancelBottom) btnCancelBottom.addEventListener('click', disableEditMode);
});
</script>
@endpush
@endsection
