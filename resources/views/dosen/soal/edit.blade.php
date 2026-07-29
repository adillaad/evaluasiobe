@extends('dosen.template')
@section('content')
    <div class="container-fluid">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h5 class="fw-bold mb-0">Ubah Soal Mata Kuliah</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ url('dosen/soal/edit-soal/' . $soal->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">

                        {{-- Kurikulum --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kurikulum <span class="text-danger">*</span></label>
                            <select name="kurikulum" class="form-select" required>
                                <option value="">-- Pilih Kurikulum --</option>
                                @foreach ($kurikulum as $k)
                                    <option value="{{ $k->id }}"
                                        {{ $soal->kurikulumId == $k->id ? 'selected' : '' }}>
                                        {{ $k->tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Mata Kuliah --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mata Kuliah <span class="text-danger">*</span></label>
                            <select name="kode_mk" class="form-select" required>
                                <option value="" disabled>-- Pilih Mata Kuliah --</option>
                                @foreach ($rpss as $rps)
                                    <option value="{{ $rps->kode_mk }}"
                                        {{ $soal->kode_mk == $rps->kode_mk ? 'selected' : '' }}>
                                        {{ $rps->nama_mk ?? $rps->kode_mk }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Minggu --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Minggu <span class="text-danger">*</span></label>
                            <input type="number" name="minggu" min="1" max="16" value="{{ $soal->minggu }}"
                                class="form-control" required>
                        </div>

                        {{-- Jenis / Metode Penilaian --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jenis / Metode Penilaian <span
                                    class="text-danger">*</span></label>
                            <select name="jenis" class="form-select" required>
                                <option value="" disabled>-- Pilih Jenis --</option>
                                @foreach ($metodes as $metode)
                                    <option value="{{ $metode->id }}" {{ $soal->jenis == $metode->id ? 'selected' : '' }}>
                                        {{ $metode->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pertanyaan --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Pertanyaan <span class="text-danger">*</span></label>
                            <textarea name="pertanyaan" id="pertanyaan" class="form-control" rows="5" required>{{ $soal->pertanyaan }}</textarea>
                            <div class="form-text text-muted">
                                <i class="ti-info-alt me-1"></i>
                                Bobot soal akan dihitung otomatis saat download template penilaian.
                            </div>
                        </div>

                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary fw-semibold px-4">
                            <i class="ti-save me-1"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('dosen.soal-list') }}" class="btn btn-secondary fw-semibold px-4">
                            <i class="ti-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        ClassicEditor.create(document.querySelector('#pertanyaan')).catch(error => console.error(error));
    </script>
@endsection
