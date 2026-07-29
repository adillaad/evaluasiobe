<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Evaluasi OBE - Mahasiswa</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/template/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/template/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/template/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/template/vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/template/vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/template/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/template/vendors/select2/select2.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/template/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/template/css/vertical-layout-light/style.css') }}">
    @stack('styles')

    <link rel="shortcut icon" href="{{ asset('assets/img/eval-obe-logo.png') }}" />


    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body data-user-role="Mahasiswa">
