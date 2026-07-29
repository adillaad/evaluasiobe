<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Kesalahan Server</title>
    <link rel="stylesheet" href="{{ asset('/assets/template/css/vertical-layout-light/style.css') }}">
</head>

<body>
    <div class="container">
        <div class="row min-vh-100 align-items-center justify-content-center">
            <div class="col-md-6 text-center">
                <h1 class="display-1 fw-bold">500</h1>
                <p class="fs-3">Kesalahan Server Internal</p>
                <p class="lead">Maaf, telah terjadi kesalahan pada server kami. Tim teknis kami sedang menangani
                    masalah ini.</p>
                <a href="{{ route('home') }}" class="btn btn-primary">Kembali ke Halaman Utama</a>
            </div>
        </div>
    </div>
</body>

</html>
