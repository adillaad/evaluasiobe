<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Evaluasi OBE — Sistem Penjamin Mutu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/assets/template/css/vertical-layout-light/style.css')}}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/typicons/typicons.css')}}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/simple-line-icons/css/simple-line-icons.css')}}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/css/vendor.bundle.base.css')}}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/select2/select2.min.css')}}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{ asset('css/icons/bootstrap-icons.css') }}">
    <link rel="shortcut icon" href="{{ asset('/assets/img/eval-obe-logo.png') }}" />
    <style>
        :root {
            --lp-navy: #0b2d5c;
            --lp-navy-deep: #071f40;
            --lp-blue: #1a56a8;
            --lp-blue-soft: #e8f0fb;
            --lp-ink: #152033;
            --lp-muted: #5b677a;
            --lp-line: #e6ebf2;
            --lp-surface: #f5f7fb;
            --lp-white: #ffffff;
            --lp-radius: 18px;
            --lp-shadow: 0 12px 40px rgba(11, 45, 92, 0.08);
            --lp-font: "Poppins", system-ui, sans-serif;
        }

        body {
            font-family: var(--lp-font);
            color: var(--lp-ink);
            background: var(--lp-white);
        }

        .lp-container {
            width: min(1120px, calc(100% - 2rem));
            margin-inline: auto;
        }
    </style>
</head>
<body>
