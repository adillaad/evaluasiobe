<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- Required meta tags --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - Evaluasi OBE</title>
    <link rel="shortcut icon" href="{{ asset('/assets/img/eval-obe-logo.png') }}" />
    {{-- plugins:css --}}
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/css/vendor.bundle.base.css') }}">
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
        span[class*="mdi-"], i[class*="mdi"], .mdi { font-family: "Material Design Icons" !important; }
        span[class*="feather-"], i[class*="feather"], .feather { font-family: "feather" !important; }
        span[class*="ti-"], i[class*="ti"], .ti { font-family: "themify" !important; }
        span[class*="icon-"], i[class*="icon"] { font-family: "Simple-Line-Icons" !important; }
        span[class*="bi-"], i[class*="bi"], .bi { font-family: "bootstrap-icons" !important; }
        span[class*="fa-"], i[class*="fa"], .fa { font-family: FontAwesome !important; }
    </style>
</head>

<body>
    {{-- Session Status --}}
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo text-center">
                                <x-app-logo :dark="false" style="height: 48px; max-width: 220px;" />
                            </div>
                            <h4>Selamat Datang!</h4>
                            <h6 class="fw-light">Silahkan masuk atau <a href="{{ route('home') }}"
                                    class="text-primary text-decoration-none">kembali ke beranda</a></h6>
                            <form method="POST" action="{{ route('login') }}" class="pt-3">
                                @csrf
                                {{-- Email Address --}}
                                <div class="form-group">
                                    <input id="email" class="form-control form-control-lg" placeholder="Email..."
                                        type="email" name="email" value="{{ old('email') }}"
                                        autocomplete="username" required autofocus />
                                </div>
                                {{-- Password --}}
                                <div class="form-group" style="position: relative;">
                                    <input id="password" class="form-control form-control-lg"
                                        placeholder="Kata Sandi..." type="password" name="password" required
                                        autocomplete="current-password" />
                                    {{-- Tambahkan ikon ini --}}
                                    <span toggle="#password" class="mdi mdi-eye-outline field-icon toggle-password"
                                        style="position: absolute; right: 15px; top: 18px; cursor: pointer;"></span>
                                </div>
                                {{-- Validation Errors --}}
                                <x-auth-session-status class="alert alert-success" :status="session('status')" />
                                <x-auth-validation-errors :errors="$errors" />
                                <h6 class="fw-light text-end">
                                    <a class="text-primary text-decoration-none"
                                        href="{{ route('password.request') }}">
                                        {{ __(key: 'Lupa Kata Sandi?') }}
                                    </a>
                                </h6>
                                {{-- Submit --}}
                                <div class="d-flex flex-column">
                                    <button class="btn btn-block btn-primary btn-lg fw-bold auth-form-btn">
                                        {{ __('Masuk') }}
                                    </button>
                                </div>
                                <div class="mt-3 d-flex align-items-center justify-content-center">
                                    <h6 class="fw-light me-2 mb-0">Belum memiliki akun?</h6>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                            id="registerDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            Buat Akun
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="registerDropdown">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('register-universitas.index') }}">
                                                    <i class="mdi mdi-school me-2"></i>Daftar sebagai Universitas
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('register-mahasiswa.index') }}">
                                                    <i class="mdi mdi-account me-2"></i>Aktivasi sebagai Mahasiswa
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ asset('/assets/template/vendors/js/vendor.bundle.base.js') }}"></script>
        {{-- endinject --}}
        {{-- Plugin js for this page --}}
        <script src="{{ asset('/assets/template/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
        {{-- End plugin js for this page --}}
        {{-- inject:js --}}
        <script src="{{ asset('/assets/template/js/off-canvas.js') }}"></script>
        <script src="{{ asset('/assets/template/js/settings.js') }}"></script>
        <script src="{{ asset('/assets/template/js/todolist.js') }}"></script>
        <script>
            document.querySelector(".toggle-password").addEventListener("click", function() {
                const passwordInput = document.querySelector(this.getAttribute("toggle"));
                // Ganti ikonnya
                this.classList.toggle("mdi-eye-outline");
                this.classList.toggle("mdi-eye-off-outline");

                // Ganti tipe input dari password ke text atau sebaliknya
                if (passwordInput.type === "password") {
                    passwordInput.type = "text";
                } else {
                    passwordInput.type = "password";
                }
            });
        </script>
        {{-- endinject --}}
</body>

</html>
