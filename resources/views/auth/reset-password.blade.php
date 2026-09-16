<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- Required meta tags --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Lupa Password - Evaluasi OBE</title>
    <link rel="shortcut icon" href="{{ asset('/assets/img/eval-obe-logo.png') }}" />
    {{-- plugins:css --}}
    {{-- <link rel="stylesheet" href="{{ asset('/assets/template/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/simple-line-icons/css/simple-line-icons.css') }}"> --}}
    {{-- <link rel="stylesheet" href="{{ asset('/assets/template/vendors/css/vendor.bundle.base.css') }}"> --}}
    {{-- endinject --}}
    {{-- Plugin css for this page --}}
    {{-- End plugin css for this page --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    {{-- inject:css --}}
    <link rel="stylesheet" href="{{ asset('/assets/template/css/vertical-layout-light/style.css') }}?v={{ time() }}">
    {{-- endinject --}}
    <style>
        body, html, input, button, select, textarea, p, h1, h2, h3, h4, h5, h6, a, span, table, td, th, div, label, li, ul, ol {
            font-family: 'Poppins', sans-serif !important;
        }
    </style>
</head>
<body>
    {{-- Session Status --}}
    {{-- <x-auth-session-status class="mb-4" :status="session('status')" /> --}}
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class=" auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="fw-bold text-center text-gray-600">
                                {{ __('Masukkan kata sandi baru Anda, konfirmasi dan kirim') }}
                            </div>
                            <form method="POST" action="{{ route('password.store') }}" class="pt-3">
                                @csrf
                                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                                <div class="form-group">
                                    <input id="email" class="form-control form-control-lg" placeholder="Email..."
                                        type="email" name="email" value="{{ old('email', $request->email) }}"
                                        required autofocus />
                                    {{-- <x-input-error :messages="$errors->get('email')" class="mt-2" /> --}}
                                </div>
                                <div class="form-group">
                                    <input id="password" class="form-control form-control-lg" placeholder="Password..."
                                        type="password" name="password" required autocomplete="new-password" />
                                    @error('password')
                                        <div class="alert alert-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <input id="password_confimation" class="form-control form-control-lg" placeholder="Confirm Password..."
                                        type="password" name="password_confirmation" required autocomplete="new-password" />
                                    {{-- <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" /> --}}
                                </div>
                                <div class="mt-3">
                                    <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">
                                        {{ __('Reset Password') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <script src="{{ asset('/assets/template/vendors/js/vendor.bundle.base.js') }}"></script> --}}
        {{-- endinject --}}
        {{-- Plugin js for this page --}}
        {{-- <script src="{{ asset('/assets/template/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script> --}}
        {{-- End plugin js for this page --}}
        {{-- inject:js --}}
        {{-- <script src="{{ asset('/assets/template/js/off-canvas.js') }}"></script> --}}
        {{-- <script src="{{ asset('/assets/template/js/settings.js') }}"></script> --}}
        {{-- <script src="{{ asset('/assets/template/js/todolist.js') }}"></script> --}}
        {{-- endinject --}}
</body>
</html>