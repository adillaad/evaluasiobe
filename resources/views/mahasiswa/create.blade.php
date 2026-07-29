@extends($template)
@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Mahasiswa</h4>
                <form
                    action="@if ($userOtoritas == 'Dosen') {{ route('dosen.mahasiswa.store') }}
                    @elseif($userOtoritas == 'Penjamin Mutu Program Studi')
                        {{ route('penjamin-mutu.program-studi.mahasiswa.store') }} @endif"
                    method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="npm">NPM</label>
                        <input value="{{ old('npm') }}" type="text" class="form-control" name="npm"
                            placeholder="NPM" autocomplete="off" autofocus>
                    </div>
                    @error('npm')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input value="{{ old('nama') }}" type="text" class="form-control" name="nama"
                            placeholder="Nama" autocomplete="off">
                    </div>
                    @error('nama')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                    <div class="form-group">
                        <label for="angkatan">Angkatan</label>
                        <input value="{{ old('angkatan') }}" type="text" class="form-control" name="angkatan"
                            placeholder="Angkatan" autocomplete="off">
                    </div>
                    @error('angkatan')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                    <input type="submit" class="btn btn-primary" value="Submit">
                </form>
            </div>
        </div>
    </div>
@endsection
