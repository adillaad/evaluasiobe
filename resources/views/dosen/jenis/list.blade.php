@extends('dosen.template')
@section('content')

    {{-- Form Tambah Kriteria Penilaian --}}
    @if ($userOtoritas == 'Dosen')
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h6 class="fw-bold mb-0">
                    <i class="ti ti-plus me-2 text-primary"></i>Tambah Kriteria Penilaian
                </h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route($currentPrefix . 'Jenis-store') }}" method="POST">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8 col-lg-6">
                            <label for="jenis" class="form-label fw-semibold small mb-1">
                                Nama Kriteria <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-sm" id="jenis" name="jenis" placeholder="Contoh: Tugas / Quiz / UTS" required>
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                                <i class="ti ti-check me-1"></i> Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Tabel Data Kriteria Penilaian --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom py-3 px-4">
            <h6 class="fw-bold mb-0">
                <i class="ti ti-list me-2"></i>Daftar Kriteria Penilaian
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 8%" class="text-center">No</th>
                            <th>Nama Kriteria Penilaian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jenis as $item)
                            <tr>
                                <td class="text-center small">{{ $loop->iteration }}</td>
                                <td class="small fw-semibold">{{ $item->nama_kriteria }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center py-4 text-muted">
                                    <i class="ti ti-folder me-2"></i>Belum ada data kriteria penilaian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var jenisInput = document.getElementById('jenis');
            if (jenisInput) {
                jenisInput.addEventListener('input', function() {
                    var words = this.value.split(' ');
                    for (var i = 0; i < words.length; i++) {
                        if (words[i].length > 0) {
                            words[i] = words[i].charAt(0).toUpperCase() + words[i].slice(1);
                        }
                    }
                    this.value = words.join(' ');
                });
            }
        });
    </script>
@endsection
