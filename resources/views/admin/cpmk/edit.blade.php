@php
    $userOtoritas = auth()->user()->otoritas->otoritas ?? 'Dosen';
    $templateToExtend = in_array($userOtoritas, ['Admin Universitas', 'Admin']) 
        ? 'admin.template' 
        : (in_array($userOtoritas, ['Koordinator Program Studi', 'Kepala Program Studi', 'Penjamin Mutu Program Studi', 'Penjamin Mutu Fakultas', 'Penjamin Mutu Universitas']) 
            ? 'penjamin-mutu.template' 
            : 'dosen.template');

    $routePrefixMap = [
        'Koordinator Program Studi' => 'koordinator-program-studi.',
        'Kepala Program Studi' => 'kepala-program-studi.',
        'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.',
        'Penjamin Mutu Fakultas' => 'penjamin-mutu.fakultas.',
        'Penjamin Mutu Universitas' => 'penjamin-mutu.universitas.',
        'Wakil Dekan' => 'wakil-dekan.',
        'Wakil Rektor' => 'wakil-rektor.',
        'Dosen' => 'dosen.',
        'Admin Universitas' => 'admin-universitas.',
        'Admin' => 'admin.',
    ];
    $currentPrefix = $routePrefixMap[$userOtoritas] ?? 'admin-universitas.';

    $getBackUrl = function() use ($currentPrefix) {
        $name1 = $currentPrefix . 'list-cpmk';
        $name2 = $currentPrefix . 'cpmk-list';
        if (\Illuminate\Support\Facades\Route::has($name1)) return route($name1);
        if (\Illuminate\Support\Facades\Route::has($name2)) return route($name2);
        return route('dosen.cpmk-list');
    };

    $getUpdateUrl = function($id) use ($currentPrefix) {
        $name1 = $currentPrefix . 'cpmk-update';
        $name2 = $currentPrefix . 'update-cpmk';
        if (\Illuminate\Support\Facades\Route::has($name1)) return route($name1, $id);
        if (\Illuminate\Support\Facades\Route::has($name2)) return route($name2, $id);
        return route('dosen.cpmk-update', $id);
    };
@endphp

@extends($templateToExtend)

@section('content')
<style>
    .card-edit-cpmk {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
    }
    .form-label-custom {
        font-weight: 700;
        color: #1e293b;
        font-size: 0.925rem;
        margin-bottom: 8px;
        display: block;
    }
    .custom-input-box {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 12px 16px;
        font-size: 0.95rem;
        color: #1e293b;
        background-color: #ffffff;
        transition: all 0.2s ease;
    }
    .custom-input-box:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        outline: none;
    }
</style>

<div class="container-fluid py-3">
    <div class="card card-edit-cpmk border-0">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-0 fw-bold text-dark">Edit CPMK ({{ $cpmk->kode }})</h4>
                <small class="text-muted">Perbarui data Capaian Pembelajaran Mata Kuliah</small>
            </div>
            <a href="{{ $getBackUrl() }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="ti-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>

        <div class="card-body p-4">
            @if ($userOtoritas === 'Dosen')
                <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-warning-emphasis p-3 rounded-3 mb-4 d-flex align-items-start gap-3">
                    <i class="ti-alert fs-4 text-warning mt-1"></i>
                    <div style="font-size: 0.875rem; line-height: 1.5;">
                        <strong>Peringatan Penting:</strong> Mengubah CPL atau rincian CPMK ini akan berdampak pada seluruh pemetaan Sub-CPMK, RPS, dan visualisasi penilaian di tingkat Program Studi (termasuk tampilan Kaprodi & Penjamin Mutu).
                    </div>
                </div>
            @endif

            <form action="{{ $getUpdateUrl($cpmk->id) }}" method="post">
                @csrf
                @method('put')

                <div class="mb-4">
                    <label for="cpl" class="form-label-custom">Pilih CPL Prodi <span class="text-danger">*</span></label>
                    <select id="cpl" name="cpl" class="form-select custom-input-box" required>
                        @forelse ($cpls as $cpl)
                            <option value="{{ $cpl->id }}" {{ $cpmk->cpl_id == $cpl->id ? 'selected' : '' }}>
                                Kurikulum {{ $cpl->tahun_kurikulum ?? '-' }} – {{ $cpl->kode }} – {{ $cpl->judul }}
                            </option>
                        @empty
                            <option value="{{ $cpmk->cpl_id }}" selected>
                                {{ $cpmk->cpl->kode ?? 'CPL' }} – {{ $cpmk->cpl->judul ?? '-' }}
                            </option>
                        @endforelse
                    </select>
                    @error('cpl')
                        <div class="text-danger small mt-1"><i class="ti-info-alt me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="judul" class="form-label-custom">Rincian CPMK <span class="text-danger">*</span></label>
                    <textarea class="form-control custom-input-box" name="judul" id="judul" rows="4" required placeholder="Tuliskan rincian CPMK di sini...">{!! old('judul', trim($cpmk->judul)) !!}</textarea>
                    @error('judul')
                        <div class="text-danger small mt-1"><i class="ti-info-alt me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                    <a href="{{ $getBackUrl() }}" class="btn btn-light border px-4 fw-semibold">Batal</a>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                        <i class="ti-check me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection