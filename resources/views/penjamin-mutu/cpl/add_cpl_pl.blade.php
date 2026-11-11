@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')
@if (session()->has('failed'))
    <div class="alert alert-danger" role="alert" id="box">
        <div>{{ session('failed') }}</div>
    </div>
@elseif (session()->has('success'))
    <div class="alert alert-success" role="alert" id="box">
        <div>{{ session('success') }}</div>
    </div>
@endif

<div class="container mt-4 mb-5">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4">
            <h4 class="card-title font-weight-bold mb-4">Tambah Pemetaan Profil Lulusan Ke CPL</h4>

            <form action="{{ route($currentPrefix . 'cpl.cpl-pl-store') }}" method="POST">
                @csrf
                <div class="form-group mb-4">
                    <label for="profil_lulusan_id" class="fw-semibold text-dark mb-2">Pilih Profil Lulusan <span class="text-danger">*</span></label>
                    <select class="form-control" id="profil_lulusan_id" name="profil_lulusan_id" required>
                        <option value="" selected disabled>Pilih Profil Lulusan</option>
                        @foreach ($profilLulusans as $profilLulusan)
                            <option value="{{ $profilLulusan->id }}" {{ old('profil_lulusan_id') == $profilLulusan->id ? 'selected' : '' }}>
                                {{ $profilLulusan->kode }} - {{ $profilLulusan->namaProfil }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                        <label for="cpl_ids" class="fw-semibold text-dark mb-0">Pilih CPL yang Terkait <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="text" id="cpl-search-input" class="form-control form-control-sm" placeholder="Cari CPL..." style="width: 170px; height: 32px;">
                            <button type="button" class="btn btn-xs btn-outline-primary rounded px-2 py-1" id="btn-select-all-cpl">Pilih Semua</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded px-2 py-1" id="btn-deselect-all-cpl">Hapus Semua</button>
                        </div>
                    </div>
                    <small class="text-muted d-block mb-3">* Input bobot persentase <strong>(0-100%) opsional</strong>. Jika tidak diisi, bobot akan dihitung <strong>sama rata</strong> secara otomatis (misal: 4 CPL = 25% masing-masing).</small>

                    <div id="cpl_container" class="border rounded-3 p-3 bg-light" style="max-height: 350px; overflow-y: auto;">
                        @forelse ($cpls as $cpl)
                            @php
                                $isChecked = in_array($cpl->id, old('cpl_ids', []));
                            @endphp
                            <div class="cpl-item-wrapper mb-2">
                                <div class="d-flex align-items-center bg-white border rounded-2 px-3 py-2 shadow-xs cpl-row-card {{ $isChecked ? 'border-primary' : '' }}" style="gap: 12px; transition: all 0.2s ease;">
                                    <input class="form-check-input cplId-checkbox m-0 flex-shrink-0" type="checkbox" name="cpl_ids[]"
                                        value="{{ $cpl->id }}" id="cpl_{{ $cpl->id }}"
                                        {{ $isChecked ? 'checked' : '' }}
                                        style="width: 20px; height: 20px; min-width: 20px; cursor: pointer; position: static !important; opacity: 1 !important; margin: 0 !important; display: inline-block !important; border: 1px solid #adb5bd;">

                                    <label class="form-label fw-medium text-dark m-0 flex-grow-1 cursor-pointer font-14" for="cpl_{{ $cpl->id }}" style="cursor: pointer; user-select: none; line-height: 1.4;">
                                        <strong class="text-primary me-1">{{ $cpl->kode }}</strong> - <span>{{ $cpl->judul }}</span>
                                    </label>

                                    <div class="flex-shrink-0 ms-2" style="width: 130px;">
                                        <input class="form-control form-control-sm bobot" type="number" name="bobot[{{ $cpl->id }}]"
                                            placeholder="Bobot (%)" step="any" min="0" max="100"
                                            value="{{ old('bobot.' . $cpl->id) }}"
                                            {{ $isChecked ? '' : 'disabled' }}
                                            style="height: 38px; border-radius: 6px;">
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">Tidak ada CPL yang tersedia.</div>
                        @endforelse
                    </div>

                    <div id="bobot-warning-bar" class="d-none alert alert-danger mt-2 py-2 px-3 small font-weight-bold d-flex align-items-center gap-2">
                        <i class="mdi mdi-alert-circle font-18"></i>
                        <span id="bobot-warning-text"></span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 mt-4">
                    <button type="submit" class="btn btn-primary px-4 py-2 font-weight-bold rounded-2">Simpan</button>
                    <a href="{{ route($currentPrefix . 'cpl.cpl-pl') }}" class="btn btn-light px-4 py-2 rounded-2">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.cpl-row-card {
    transition: background-color 0.15s ease, border-color 0.15s ease;
}
.cpl-row-card:hover {
    background-color: #f8fafc !important;
    border-color: #cbd5e1 !important;
}
.cpl-row-card.border-primary {
    border-color: #2664F5 !important;
    background-color: #f0f7ff !important;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateRowState(checkbox) {
        const isChecked = checkbox.checked;
        const row = checkbox.closest('.cpl-row-card');
        if (row) {
            const bobotInput = row.querySelector('.bobot');
            if (bobotInput) {
                bobotInput.disabled = !isChecked;
                if (!isChecked) {
                    bobotInput.value = '';
                }
            }
            if (isChecked) {
                row.classList.add('border-primary');
                row.style.backgroundColor = '#f0f7ff';
            } else {
                row.classList.remove('border-primary');
                row.style.backgroundColor = '#ffffff';
            }
        }
    }

    const checkboxes = document.querySelectorAll('.cplId-checkbox');
    checkboxes.forEach(function(cb) {
        updateRowState(cb);
        cb.addEventListener('change', function() {
            updateRowState(this);
        });
    });

    const btnSelectAll = document.getElementById('btn-select-all-cpl');
    if (btnSelectAll) {
        btnSelectAll.addEventListener('click', function() {
            checkboxes.forEach(function(cb) {
                const wrapper = cb.closest('.cpl-item-wrapper');
                if (!wrapper || wrapper.style.display !== 'none') {
                    cb.checked = true;
                    updateRowState(cb);
                }
            });
        });
    }

    const btnDeselectAll = document.getElementById('btn-deselect-all-cpl');
    if (btnDeselectAll) {
        btnDeselectAll.addEventListener('click', function() {
            checkboxes.forEach(function(cb) {
                cb.checked = false;
                updateRowState(cb);
            });
        });
    }

    function checkTotalBobot() {
        let total = 0;
        let hasCustom = false;
        document.querySelectorAll('.cpl-row-card').forEach(function(row) {
            const cb = row.querySelector('.cplId-checkbox');
            const bInput = row.querySelector('.bobot');
            if (cb && cb.checked && bInput && bInput.value !== '') {
                const val = parseFloat(bInput.value);
                if (!isNaN(val) && val > 0) {
                    total += val;
                    hasCustom = true;
                }
            }
        });
        total = Math.round(total * 100) / 100;
        const warningBar = document.getElementById('bobot-warning-bar');
        const warningText = document.getElementById('bobot-warning-text');
        if (warningBar && warningText) {
            if (hasCustom && total > 100.0) {
                warningText.textContent = 'Perhatian: Total bobot yang Anda inputkan (' + total + '%) melebihi 100%. Maksimal total bobot adalah 100%.';
                warningBar.classList.remove('d-none');
            } else {
                warningBar.classList.add('d-none');
            }
        }
        return { total, hasCustom };
    }

    document.querySelectorAll('.bobot').forEach(function(input) {
        input.addEventListener('input', checkTotalBobot);
    });

    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const res = checkTotalBobot();
            if (res.hasCustom && res.total > 100.0) {
                e.preventDefault();
                alert('Total bobot yang Anda masukkan (' + res.total + '%) melebihi 100%. Mohon sesuaikan bobot hingga tidak melebihi 100%.');
                return false;
            }
        });
    }

    const searchInput = document.getElementById('cpl-search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const keyword = this.value.toLowerCase().trim();
            document.querySelectorAll('.cpl-item-wrapper').forEach(function(item) {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(keyword) ? '' : 'none';
            });
        });
    }
});
</script>
@endpush
@endsection
