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
                    <h6 class="fw-bold mb-0 text-primary">Mode Edit Matriks Aktif</h6>
                    <small class="text-secondary">Klik sel mana saja pada tabel di bawah untuk mencentang atau menghapus centang pemetaan CPL ke Mata Kuliah.</small>
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
                        <h4 class="card-title fw-bold mb-1">List Pemetaan CPL - MK</h4>
                        <p class="text-muted small mb-0">Matriks keterhubungan antara Capaian Pembelajaran Lulusan (CPL) dan Mata Kuliah.</p>
                    </div>

                    @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas']))
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" id="btn-toggle-matrix-edit" class="btn btn-outline-primary rounded-3 font-weight-bold d-inline-flex align-items-center gap-1 px-3 py-2">
                                <i class="mdi mdi-pencil-box-multiple-outline font-18"></i>
                                <span id="btn-matrix-text">Edit Matriks</span>
                            </button>

                            <a href="{{ route($currentPrefix . 'cpl.cpl-mk-add') }}" class="btn btn-primary rounded-3 font-weight-bold d-inline-flex align-items-center gap-1 px-3 py-2 shadow-sm">
                                <i class="mdi mdi-plus-circle font-18"></i>
                                <span>Tambah CPL - MK</span>
                            </a>
                        </div>
                    @endif
                </div>

                <form id="form-matrix-edit" action="{{ route($currentPrefix . 'cpl.cpl-mk-matrix-update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="kurikulum_id" value="{{ request('kurikulum_id') }}">

                    <div class="table-responsive mt-3">
                        <table class="table table-hover align-middle border-light" id="matrix-table">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 px-3 text-center" style="width: 50px;">No</th>
                                    <th class="py-3 px-3">Kode MK</th>
                                    <th class="py-3 px-3">Nama MK</th>
                                    <th class="py-3 px-3 text-center" style="width: 70px;">SKS</th>
                                    <th class="py-3 px-3 text-center" style="width: 120px;">Kurikulum</th>
                                    @foreach ($cpls as $cpl)
                                        <th class="py-3 px-2 text-center" style="min-width: 85px;" title="{{ $cpl->kode }}: {{ $cpl->judul }}">
                                            <span class="badge bg-primary-soft text-primary font-12 px-2 py-1 rounded-2">{{ $cpl->kode }}</span>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mks as $index => $mk)
                                    <tr>
                                        <td class="text-center font-weight-medium text-muted py-3 px-3">{{ $index + 1 }}</td>
                                        <td class="py-3 px-3 fw-bold text-dark font-13">{{ $mk->kode }}</td>
                                        <td class="py-3 px-3 font-13 text-secondary">{{ $mk->nama }}</td>
                                        <td class="text-center py-3 px-3 font-13">
                                            <span class="badge bg-light text-dark border px-2 py-1 rounded-1">
                                                {{ ($mk->bobot_teori ?? 0) + ($mk->bobot_praktikum ?? 0) }}
                                            </span>
                                        </td>
                                        <td class="text-center py-3 px-3 font-13">
                                            <span class="badge bg-light text-dark border px-2 py-1 rounded-1 fw-semibold">
                                                {{ $mk->kurikulum->tahun ?? $mk->kurikulum ?? '-' }}
                                            </span>
                                        </td>
                                        @foreach ($cpls as $cpl)
                                            @php
                                                $isChecked = $mk->cpl->contains('id', $cpl->id);
                                            @endphp
                                            <td class="text-center matrix-cell {{ $isChecked ? 'is-mapped' : '' }}"
                                                data-mk="{{ $mk->kode }}"
                                                data-cpl="{{ $cpl->id }}">
                                                
                                                <!-- View Mode Icon -->
                                                <div class="cell-view-mode">
                                                    @if ($isChecked)
                                                        <i class="mdi mdi-check-circle text-primary font-20"></i>
                                                    @else
                                                        <span class="text-muted opacity-25 font-16">-</span>
                                                    @endif
                                                </div>

                                                <!-- Edit Mode Toggle Input -->
                                                <div class="cell-edit-mode d-none">
                                                    <input type="checkbox"
                                                           class="form-check-input matrix-checkbox border-slate cursor-pointer"
                                                           name="matrix[{{ $mk->kode }}][]"
                                                           value="{{ $cpl->id }}"
                                                           {{ $isChecked ? 'checked' : '' }}
                                                           style="width: 20px; height: 20px; cursor: pointer;">
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 4 + count($cpls) }}" class="text-center py-4 text-muted">
                                            <i class="mdi mdi-file-table-outline font-32 d-block mb-1"></i>
                                            Belum ada data pemetaan CPL ke Mata Kuliah.
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
                <h4 class="card-title fw-bold mb-3">Deskripsi Pemetaan</h4>
                <div class="scrollable-descriptions pe-2" style="max-height: 400px; overflow-y: auto;">
                    @foreach ($mks as $mk)
                        <div class="mb-4 p-3 bg-light rounded-3 border-light">
                            <h6 class="fw-bold text-primary mb-1">
                                <span class="badge bg-primary text-white me-2">{{ $mk->kode }}</span>
                                {{ $mk->nama }}
                            </h6>
                            <p class="text-muted font-13 mb-2"><strong>Deskripsi:</strong> {{ $mk->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
                            
                            <div class="font-13">
                                <strong class="text-dark d-block mb-1">CPL Terkait:</strong>
                                @if ($mk->cpl->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($mk->cpl as $cpl)
                                            <span class="badge bg-white text-dark border px-2 py-1.5 rounded-2 shadow-xs">
                                                <strong class="text-primary">{{ $cpl->kode }}:</strong> {{ $cpl->judul }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted fst-italic">Belum ada CPL terkait.</span>
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

#matrix-table tbody tr:hover td {
    background-color: rgba(2, 132, 199, 0.03) !important;
}
.bg-primary-soft {
    background-color: rgba(2, 132, 199, 0.12);
}
.matrix-cell {
    position: relative;
    transition: background-color 0.15s ease-in-out;
}
.matrix-cell.is-mapped {
    background-color: rgba(2, 132, 199, 0.04);
}
.editing-active .matrix-cell {
    cursor: pointer;
}
.editing-active .matrix-cell:hover {
    background-color: rgba(2, 132, 199, 0.15) !important;
}
.editing-active .cell-view-mode {
    display: none !important;
}
.editing-active .cell-edit-mode {
    display: flex !important;
    align-items: center;
    justify-content: center;
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
    const matrixCells = document.querySelectorAll('.matrix-cell');

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

    // Make entire cell clickable in edit mode
    matrixCells.forEach(cell => {
        cell.addEventListener('click', function (e) {
            if (!isEditMode) return;

            const checkbox = this.querySelector('.matrix-checkbox');
            if (checkbox && e.target !== checkbox) {
                checkbox.checked = !checkbox.checked;
            }

            if (checkbox.checked) {
                this.classList.add('is-mapped');
            } else {
                this.classList.remove('is-mapped');
            }
        });
    });

    // Also update cell background when checkbox directly clicked
    document.querySelectorAll('.matrix-checkbox').forEach(cb => {
        cb.addEventListener('change', function () {
            const cell = this.closest('.matrix-cell');
            if (this.checked) {
                cell.classList.add('is-mapped');
            } else {
                cell.classList.remove('is-mapped');
            }
        });
    });
});
</script>
@endpush
@endsection