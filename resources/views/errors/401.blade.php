<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>401 - Akses Tidak Sah</title>
    <link rel="stylesheet" href="{{ asset('/assets/template/css/vertical-layout-light/style.css') }}">
</head>

<body>
    <div class="container">
        <div class="row min-vh-100 align-items-center justify-content-center">
            <div class="col-md-6 text-center">
                <h1 class="display-1 fw-bold">401</h1>
                <p class="fs-3">Akses Tidak Sah</p>
                <p class="lead">Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. Silakan login terlebih
                    dahulu.</p>
                <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
            </div>
        </div>
    </div>
</body>

</html>
