{{-- @php
    $routePrefix = [
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp --}}
@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')
@if (session()->has('failed'))
    <div class="alert alert-danger" role="alert" id="box">
        <div>{{ session('failed') }}</div>
    </div>
@elseif (session()->has('success'))
    <div class="alert greenAdd" role="alert" id="box">
        
    </div>
@endif

<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Tambah Data Bahan Kajian</h4>

            <form action="{{ route($currentPrefix. 'bk.bk-store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="nama">Bahan Kajian :</label>
                    <input type="text" value="{{ old('nama') }}" name="nama" id="nama" class="form-control"
                        placeholder="Nama Bahan Kajian">
                    @error('nama')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="kurikulum_id">Kurikulum :</label>
                    <select name="kurikulum_id" id="kurikulum_id" class="form-control">
                        <option value="">-- Pilih Kurikulum --</option>
                        @foreach ($kurikulums as $kurikulum)
                            <option value="{{ $kurikulum->id }}" {{ old('kurikulum_id') == $kurikulum->id ? 'selected' : '' }}>{{ $kurikulum->tahun }}</option>    
                        @endforeach
                    </select>
                    @error('kurikulum_id')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="rumpun">Rumpun BK :</label>
                    <textarea name="rumpun" id="rumpun" class="form-control" style="height: 100px" placeholder="Rumpun Bahan Kajian">{{ old('rumpun') }}</textarea>
                    @error('rumpun')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>


@endsection