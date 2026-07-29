<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 - Layanan Tidak Tersedia</title>
    <link rel="stylesheet" href="{{ asset('/assets/template/css/vertical-layout-light/style.css') }}">
</head>

<body>
    <div class="container">
        <div class="row min-vh-100 align-items-center justify-content-center">
            <div class="col-md-6 text-center">
                <h1 class="display-1 fw-bold">503</h1>
                <p class="fs-3">Layanan Tidak Tersedia</p>
                <p class="lead">Maaf, layanan sedang dalam pemeliharaan. Silakan coba lagi beberapa saat.</p>
                <a href="{{ route('home') }}" class="btn btn-primary">Kembali ke Halaman Utama</a>
            </div>
        </div>
    </div>
</body>

</html>
