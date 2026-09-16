{{-- @php
    $routePrefix = [
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'admin.';
@endphp --}}
@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')

<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Tambah Instrumen Penilaian</h4>

            <form action="{{ route($currentPrefix . 'asesmen.instrumen-penilaian-store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="nama_kriteria" class="form-label">Nama Kriteria Penilaian</label>
                    <input type="text" class="form-control @error('nama_kriteria') is-invalid @enderror" id="nama_kriteria" name="nama_kriteria" value="{{ old('nama_kriteria') }}" required min="0" max="100">
                    @error('nama_kriteria')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>

@endsection