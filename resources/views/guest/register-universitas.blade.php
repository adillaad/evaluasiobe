@extends('guest.template')
@section('content')
    <div class="container-fluid" style="padding-top: 100px;">
        <div class="row justify-content-center align-items-center" style="min-height: calc(100vh - 140px);">
            <div class="p-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title py-3">Register Form</h4>
                        @if (session('success'))
                            {{-- Tampilkan Pesan Sukses --}}
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        <hr class="bg-dark border-2 border-top border-dark" />
                        <form action="{{ route('register-universitas.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <div class="row gap-5">
                                    <div class="col">
                                        <h4 class="card-title">Data PIC Prodi</h4>
                                        <div class="form-group">
                                            <label for="gelar_depan" class="form-label">Gelar Depan</label>
                                            <input type="text" class="form-control" id="gelar_depan" name="gelar_depan"
                                                placeholder="Gelar Depan..." autocomplete="off"
                                                value="{{ old('gelar_depan') }}">
                                            @error('gelar_depan')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="nama_lengkap" class="form-label">Nama Lengkap<span
                                                    class="text-danger">
                                                    *</span></label>
                                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                                                placeholder="Nama Lengkap..." autocomplete="off"
                                                value="{{ old('nama_lengkap') }}">
                                            @error('nama_lengkap')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="gelar_belakang" class="form-label">Gelar Belakang<span
                                                    class="text-danger">
                                                    *</span></label>
                                            <input type="text" class="form-control" id="gelar_belakang"
                                                name="gelar_belakang" placeholder="Gelar Belakang..." autocomplete="off"
                                                value="{{ old('gelar_belakang') }}">
                                            @error('gelar_belakang')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Jenis Kelamin</label><span class="text-danger"> *</span>
                                            <div class="d-flex gap-5">
                                                <div>
                                                    <input class="form-check-input" type="radio" id="pria"
                                                        name="jenis_kelamin" value="Pria"
                                                        {{ old('jenis_kelamin') == 'Pria' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="pria">Pria</label>
                                                </div>
                                                <div>
                                                    <input class="form-check-input" type="radio" id="wanita"
                                                        name="jenis_kelamin" value="Wanita"
                                                        {{ old('jenis_kelamin') == 'Wanita' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="wanita">Wanita</label>
                                                </div>
                                            </div>
                                            @error('jenis_kelamin')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="nomor_telepon" class="form-label">Nomor Telepon<span
                                                    class="text-danger">
                                                    *</span></label>
                                            <input type="text" class="form-control" id="nomor_telepon"
                                                name="nomor_telepon" pattern="[0-9]*" inputmode="numeric"
                                                placeholder="Nomor Telepon..." autocomplete="off"
                                                value="{{ old('nomor_telepon') }}">
                                            @error('nomor_telepon')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <h4 class="card-title">Informasi Universitas</h4>
                                        <div class="form-group">
                                            <label for="nama_universitas" class="form-label">Nama Universitas<span
                                                    class="text-danger">
                                                    *</span></label>
                                            <input type="text" class="form-control" id="nama_universitas"
                                                name="nama_universitas" placeholder="Nama Universitas..."
                                                autocomplete="off" value="{{ old('nama_universitas') }}" autofocus>
                                            @error('nama_universitas')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="nama_fakultas" class="form-label">Nama Fakultas<span
                                                    class="text-danger">
                                                    *</span></label>
                                            <input type="text" class="form-control" id="nama_fakultas"
                                                name="nama_fakultas" placeholder="Nama fakultas..."
                                                autocomplete="off" value="{{ old('nama_fakultas') }}" autofocus>
                                            @error('nama_fakultas')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="nama_prodi" class="form-label">Nama prodi<span
                                                    class="text-danger">
                                                    *</span></label>
                                            <input type="text" class="form-control" id="nama_prodi"
                                                name="nama_prodi" placeholder="Nama prodi..."
                                                autocomplete="off" value="{{ old('nama_prodi') }}" autofocus>
                                            @error('nama_prodi')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <x-aptikom-picker name="is_aptikom" id="is_aptikom" :value="old('is_aptikom')" />
                                            @error('is_aptikom')
                                                <div class="alert alert-danger mt-2">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="posisi" class="form-label">Posisi<span class="text-danger">
                                                    *</span></label>
                                            <input type="text" class="form-control" id="posisi" name="posisi"
                                                placeholder="Posisi..." autocomplete="off" value="{{ old('posisi') }}">
                                            @error('posisi')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="nomor_telepon_universitas" class="form-label">Nomor Telepon
                                                Universitas<span class="text-danger">
                                                    *</span></label>
                                            <input type="text" class="form-control" id="nomor_telepon_universitas"
                                                name="nomor_telepon_universitas"
                                                placeholder="Nomor Telepon Universitas..." autocomplete="off"
                                                value="{{ old('nomor_telepon_universitas') }}">
                                            @error('nomor_telepon_universitas')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="alamat_kontak_universitas" class="form-label">Alamat Kontak
                                                Universitas<span class="text-danger">
                                                    *</span></label>
                                            <input type="text" class="form-control" id="alamat_kontak_universitas"
                                                name="alamat_kontak_universitas"
                                                placeholder="Alamat Kontak Universitas..." autocomplete="off"
                                                value="{{ old('alamat_kontak_universitas') }}">
                                            @error('alamat_kontak_universitas')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="website" class="form-label">Website<span class="text-danger">
                                                    *</span></label>
                                            <input type="text" class="form-control" id="website" name="website"
                                                placeholder="Website..." autocomplete="off"
                                                value="{{ old('website') }}">
                                            @error('website')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <hr class="bg-dark border-2 border-top border-dark" />
                                <div class="row gap-5">
                                    <div class="col">
                                        <h4 class="card-title">Data Kredensial</h4>
                                        <div class="form-group">
                                            <label for="email" class="form-label">Email<span class="text-danger">
                                                    *</span></label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                placeholder="Email..." autocomplete="off" value="{{ old('email') }}">
                                            @error('email')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="password" class="form-label">Password<span class="text-danger">
                                                    *</span></label>
                                            <input type="password" class="form-control" id="password" name="password"
                                                placeholder="Password..." autocomplete="off">
                                            @error('password')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="konfirmasi_password" class="form-label">Konfirmasi Password<span
                                                    class="text-danger">
                                                    *</span></label>
                                            <input type="password" class="form-control" id="password_confirmation"
                                                name="password_confirmation" placeholder="Konfirmasi Password..."
                                                autocomplete="off">
                                            @error('konfirmasi_password')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <h4 class="card-title">Surat Tugas</h4>
                                        <div class="form-group">
                                            <label for="surat_tugas" class="form-label">Surat Tugas (PDF)<span
                                                    class="text-danger">
                                                    *</span></label>
                                            <input type="file" class="form-control" id="surat_tugas"
                                                name="surat_tugas" accept="application/pdf" max-size="2048"
                                                style="padding-bottom: +27px">
                                            <small class="text-muted">Maksimal ukuran file: 2MB</small>
                                            @error('surat_tugas')
                                                <div class="alert alert-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-primary">{{ __('Register') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        document.getElementById('nomor_telepon').addEventListener('keypress', function(e) {
            // Memastikan hanya tombol angka dan beberapa tombol kontrol yang diizinkan
            if (e.key.match(/[^0-9]/g) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'ArrowLeft' && e
                .key !== 'ArrowRight') {
                e.preventDefault();
            }
        });
    </script>
@endsection
