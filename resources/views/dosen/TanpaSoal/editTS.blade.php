@extends('dosen.template')
@section('content')
    <div class="container-fluid">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h5 class="fw-bold mb-0">Ubah Instrumen Penilaian Tanpa Soal</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('dosen.tanpa-soal.update', $instrumen->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">

                        {{-- Mata Kuliah --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mata Kuliah <span class="text-danger">*</span></label>
                            <select name="kode_mk" id="kode_mk" class="form-select" required>
                                <option value="" disabled>-- Pilih Mata Kuliah --</option>
                                @foreach ($rpss as $rps)
                                    <option value="{{ $rps->kode_mk }}"
                                        {{ $instrumen->kode_mk == $rps->kode_mk ? 'selected' : '' }}>
                                        {{ $rps->kode_mk }} - {{ $rps->mk?->nama ?? 'MK tidak ditemukan' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Jenis Penilaian --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jenis Penilaian <span class="text-danger">*</span></label>
                            <select name="metode_id" id="metode_id" class="form-select" required>
                                <option value="" disabled>-- Pilih Jenis --</option>
                                @foreach ($metodes as $metode)
                                    <option value="{{ $metode->id }}"
                                        {{ $instrumen->metode_id == $metode->id ? 'selected' : '' }}>
                                        {{ $metode->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- CPL --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">CPL <span class="text-danger">*</span></label>
                            <select name="cpl_id" id="cpl_id" class="form-select" required>
                                <option value="" disabled>-- Pilih CPL --</option>
                                @foreach ($cpls as $cpl)
                                    <option value="{{ $cpl->id }}"
                                        {{ $instrumen->cpl_id == $cpl->id ? 'selected' : '' }}>
                                        {{ $cpl->kode }} - {{ $cpl->judul }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- CPMK --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">CPMK <span class="text-danger">*</span></label>
                            <select name="cpmk_id" id="cpmk_id" class="form-select" required>
                                <option value="" disabled>-- Pilih CPMK --</option>
                                @foreach ($cpmks as $cpmk)
                                    <option value="{{ $cpmk->id }}"
                                        {{ $instrumen->cpmk_id == $cpmk->id ? 'selected' : '' }}>
                                        {{ $cpmk->kode }} - {{ $cpmk->judul }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Info bobot otomatis --}}
                        <div class="col-12">
                            <div class="alert alert-info py-2 mb-0 small">
                                <i class="ti-info-alt me-1"></i>
                                Bobot dan persentase CPMK akan dihitung otomatis saat download template penilaian.
                            </div>
                        </div>

                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary fw-semibold px-4">
                            <i class="ti-save me-1"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('dosen.tanpa-soal-list') }}" class="btn btn-secondary fw-semibold px-4">
                            <i class="ti-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
