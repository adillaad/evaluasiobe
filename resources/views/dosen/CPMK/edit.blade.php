@php
    $userOtoritas = auth()->user()->otoritas->otoritas ?? 'Dosen';
@endphp
@extends('dosen.template')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header py-3">
            <h4 class="mb-0 fw-bold">Edit CPMK ({{ $cpmk->kode }})</h4>
        </div>

        <div class="card-body">
            @if ($userOtoritas === 'Dosen')
                <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-warning-emphasis p-3 rounded-3 mb-4 d-flex align-items-center gap-3">
                    <i class="ti-alert fs-4 text-warning"></i>
                    <div style="font-size: 0.875rem;">
                        <strong>Peringatan Penting:</strong> Mengubah CPL atau rincian CPMK ini akan berdampak pada seluruh modul pemetaan CPLMK, RPS, dan visualisasi penilaian di tingkat Program Studi (termasuk tampilan Kaprodi & Penjamin Mutu).
                    </div>
                </div>
            @endif

            <form action="{{ route('dosen.cpmk-update', $cpmk->id) }}" method="post">
                @csrf
                @method('put')

                <div class="mb-3">
                    <label for="cpl" class="form-label fw-bold">Pilih CPL Prodi <span class="text-danger">*</span></label>
                    <select id="cpl" name="cpl" class="form-select" required>
                        @foreach ($cpls as $cpl)
                            <option value="{{ $cpl->id }}" {{ $cpmk->cpl_id == $cpl->id ? 'selected' : '' }}>
                                {{ $cpl->tahun_kurikulum }} – {{ $cpl->kode }} – {{ $cpl->judul }}
                            </option>
                        @endforeach
                    </select>
                    @error('cpl')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="judul" class="form-label fw-bold">Rincian CPMK <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="judul" id="judul" rows="3" required placeholder="Contoh: Mampu merancang database...">{!! old('judul', $cpmk->judul) !!}</textarea>
                    @error('judul')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="border-top pt-3 d-flex align-items-center justify-content-between">
                    <a href="{{ route('dosen.cpmk-list') }}" class="btn btn-light border px-4">Batal</a>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
