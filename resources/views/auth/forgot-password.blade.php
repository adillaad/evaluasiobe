<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Lupa Password - SMMP</title>
    <link rel="stylesheet" href="{{ asset('/assets/template/css/vertical-layout-light/style.css') }}">
</head>
<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class=" auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="fw-bold text-center text-gray-600">
                                {{ __('Masukkan alamat email Anda untuk mengatur ulang kata sandi Anda') }}
                            </div>
                            <form method="POST" action="{{ route('password.email') }}" class="pt-3">
                                @csrf
                                <div class="form-group">
                                    <input id="email" class="form-control form-control-lg" placeholder="Email..."
                                    type="email" name="email" value="{{ old('email') }}" required autofocus />
                                </div>
                                @error('email')
                                    <div class="alert alert-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <x-auth-session-status class="alert alert-success" :status="session('status')" />
                                <div class="mt-3 d-flex justify-content-between align-items-center">
                                    <a href="{{ route('login') }}" class="text-primary">Kembali</a>
                                    <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">
                                        {{ __('Kirim') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>