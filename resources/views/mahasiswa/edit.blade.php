@extends($template)
@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Mahasiswa</h4>
                <form action="@if ($userOtoritas == 'Dosen') {{ route('dosen.mahasiswa.update', $mahasiswa->id) }}
                    @elseif($userOtoritas == 'Penjamin Mutu Program Studi')
                        {{ route('penjamin-mutu.program-studi.mahasiswa.update', $mahasiswa->id) }} @endif" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="npm">NPM</label>
                        <input value="{{ old('npm', $mahasiswa->NPM) }}" type="text" class="form-control" name="npm" placeholder="NPM" autocomplete="off" autofocus>
                    </div>
                    @error('npm')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input value="{{ old('nama', $mahasiswa->Nama) }}" type="text" class="form-control" name="nama" placeholder="Nama" autocomplete="off">
                    </div>
                    @error('nama')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                    <div class="form-group">
                        <label for="angkatan">Angkatan</label>
                        <input value="{{ old('angkatan', $mahasiswa->angkatan) }}" type="text" class="form-control" name="angkatan" placeholder="Angkatan" autocomplete="off">
                    </div>
                    @error('angkatan')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                    <input type="submit" class="btn btn-primary" value="Update">
                </form>
            </div>
        </div>
    </div>
@endsection
