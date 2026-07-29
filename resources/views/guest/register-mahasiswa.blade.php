@extends('guest.template')
@section('content')
    <div class="container-fluid" style="padding-top: 100px;">
        <div class="row justify-content-center align-items-center" style="min-height: calc(100vh - 140px);">
            <div class="p-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title py-3">Aktivasi Akun Mahasiswa</h4>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <hr class="bg-dark border-2 border-top border-dark" />

                        <form action="{{ route('register-mahasiswa.store') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <div class="row gap-5">
                                    <div class="col">
                                        <h4 class="card-title">Data Mahasiswa</h4>

                                        <div class="form-group">
                                            <label for="npm" class="form-label">NPM <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   class="form-control"
                                                   id="npm"
                                                   name="npm"
                                                   placeholder="Masukkan NPM Anda..."
                                                   value="{{ old('npm') }}"
                                                   required
                                                   autocomplete="off" />
                                            @error('npm')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email"
                                                   class="form-control"
                                                   id="email"
                                                   name="email"
                                                   placeholder="Masukkan email aktif..."
                                                   value="{{ old('email') }}"
                                                   required
                                                   autocomplete="off" />
                                            @error('email')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="password" class="form-label">Kata Sandi <span class="text-danger">*</span></label>
                                            <input type="password"
                                                   class="form-control"
                                                   id="password"
                                                   name="password"
                                                   placeholder="Minimal 8 karakter..."
                                                   required
                                                   autocomplete="new-password" />
                                            @error('password')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                                            <input type="password"
                                                   class="form-control"
                                                   id="password_confirmation"
                                                   name="password_confirmation"
                                                   placeholder="Ulangi kata sandi..."
                                                   required
                                                   autocomplete="new-password" />
                                            @error('password_confirmation')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col d-flex align-items-end">
                                        <div>
                                            <div class="alert alert-info">
                                                <strong>Perhatian:</strong><br>
                                                - NPM harus terdaftar di sistem.<br>
                                                - Akun ini hanya untuk aktivasi mahasiswa.<br>
                                                - Setelah aktivasi, Anda bisa login menggunakan email dan kata sandi.
                                            </div>
                                            <button type="submit" class="btn btn-primary">{{ __('Aktivasi Akun') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection