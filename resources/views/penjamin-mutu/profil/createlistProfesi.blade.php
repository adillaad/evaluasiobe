@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')
    @if (session()->has('failed'))
        <div class="alert alert-danger" role="alert" id="box">
            <div>{{ session('failed') }}</div>
        </div>
    @elseif (session()->has('success'))
        <div class="alert greenAdd" role="alert" id="box">
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <h3 class="px-4 pb-4 fw-bold text-center">Halaman Add Profesi</h3>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Data Profesi</h4>
                <form action="{{ route($currentPrefix . 'profesi-store') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="kurikulum_id" class="form-label">Kurikulum <span class="text-danger">*</span></label>
                        <select id="kurikulum_id" name="kurikulum_id" class="form-select">
                            <option value="">-- Pilih Kurikulum --</option>
                            @foreach ($kurikulums as $k)
                                <option value="{{ $k->id }}">{{ $k->tahun }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Profesi <span class="text-danger">*</span></label>
                        <input type="text" id="nama" name="nama" class="form-control"
                            placeholder="Masukkan nama profesi">
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" onclick="storeProfesi()">
                            <i class="mdi mdi-content-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
