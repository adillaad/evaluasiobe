@php
    $routePrefix = [
        'Penjamin Mutu Universitas' => ['prefix' => 'penjamin-mutu.universitas.'],
        'Penjamin Mutu Fakultas' => ['prefix' => 'penjamin-mutu.fakultas.'],
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
        'Dosen' => ['prefix' => 'dosen.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp
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
            <h4 class="card-title font-weight-bold mb-4">Tambah Pemetaan CPL ke Mata Kuliah</h4>

            <form action="{{ route($currentPrefix . 'cpl.cpl-mk-store') }}" method="POST">
                @csrf
                <div class="form-group mb-4">
                    <label for="cpl_id" class="fw-semibold text-dark mb-2">Pilih CPL <span class="text-danger">*</span></label>
                    <select class="form-select border-slate rounded-2" id="cpl_id" name="cpl_id" required style="height: 42px; font-size: 14px;">
                        <option value="" selected disabled>-- Pilih CPL --</option>
                        @foreach ($cpls as $cpl)
                            <option value="{{ $cpl->id }}">{{ $cpl->kode }} - {{ $cpl->judul }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label for="mk_kodes" class="fw-semibold text-dark mb-0">Pilih Mata Kuliah yang Terkait <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-xs btn-outline-primary rounded px-2 py-1" id="btn-select-all">Pilih Semua</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded px-2 py-1" id="btn-deselect-all">Hapus Semua</button>
                        </div>
                    </div>

                    <div id="mk_container" class="border rounded-3 p-3 bg-light" style="max-height: 320px; overflow-y: auto;">
                        @forelse ($mks as $mk)
                            <div class="mk-item-wrapper mb-2">
                                <div class="d-flex align-items-center bg-white border rounded-2 px-3 py-2.5 shadow-xs" style="gap: 12px;">
                                    <input class="form-check-input mk-checkbox m-0 flex-shrink-0" type="checkbox" name="mk_kodes[]" value="{{ $mk->kode }}" id="mk_{{ $loop->index }}" style="width: 18px; height: 18px; cursor: pointer;">
                                    <label class="form-check-label fw-medium text-dark m-0 cursor-pointer w-100 font-14" for="mk_{{ $loop->index }}" style="user-select: none;">
                                        <span class="badge bg-secondary me-2 font-12" style="font-weight: 500;">{{ $mk->kode }}</span>
                                        <span>{{ $mk->nama }}</span>
                                    </label>
                                </div>
                            </div>
                        @empty
                            <span class="text-muted small">Tidak ada mata kuliah yang tersedia.</span>
                        @endforelse
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 mt-4">
                    <button type="submit" class="btn btn-primary px-4 py-2 font-weight-bold rounded-2">Simpan Pemetaan</button>
                    <a href="{{ route($currentPrefix . 'cpl.cpl-mk') }}" class="btn btn-light px-4 py-2 rounded-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.mk-item-wrapper .d-flex {
    transition: background-color 0.15s ease, border-color 0.15s ease;
}
.mk-item-wrapper .d-flex:hover {
    background-color: #f8fafc !important;
    border-color: #cbd5e1 !important;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnSelectAll = document.getElementById('btn-select-all');
    const btnDeselectAll = document.getElementById('btn-deselect-all');
    const checkboxes = document.querySelectorAll('.mk-checkbox');

    if (btnSelectAll) {
        btnSelectAll.addEventListener('click', function() {
            checkboxes.forEach(cb => cb.checked = true);
        });
    }

    if (btnDeselectAll) {
        btnDeselectAll.addEventListener('click', function() {
            checkboxes.forEach(cb => cb.checked = false);
        });
    }
});
</script>
@endpush
@endsection