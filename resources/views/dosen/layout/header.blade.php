<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- Required meta tags --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Evaluasi OBE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    {{-- plugins:css --}}
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- endinject --}}
    {{-- inject:css --}}
    <link rel="stylesheet" href="{{ asset('/assets/template/css/vertical-layout-light/style.css') }}?v={{ time() }}">
    {{-- endinject --}}
    <style>
        body, html, input, button, select, textarea, p, h1, h2, h3, h4, h5, h6, a, span, table, td, th, div, label, li, ul, ol {
            font-family: 'Poppins', sans-serif !important;
        }
        span[class*="mdi-"], i[class*="mdi-"], span[class*="mdi"], i[class*="mdi"], .mdi { font-family: "Material Design Icons" !important; }
        span[class*="feather-"], i[class*="feather-"], span[class*="feather"], i[class*="feather"], .feather { font-family: "feather" !important; }
        span[class*="ti-"], i[class*="ti-"], span[class*="ti"], i[class*="ti"], .ti { font-family: "themify" !important; }
        i[class*="ti-trash"], span[class*="ti-trash"], .ti-trash, .btn-icons i.ti-trash, .btn i.ti-trash, .btn-danger i { font-family: "Material Design Icons" !important; }
        i[class*="ti-trash"]:before, span[class*="ti-trash"]:before, .ti-trash:before, .btn-icons i.ti-trash:before, .btn i.ti-trash:before, .btn-danger i:before { content: "\F1C0" !important; font-family: "Material Design Icons" !important; }
        span[class*="icon-"], i[class*="icon-"] { font-family: "Simple-Line-Icons" !important; }
        span[class*="bi-"], i[class*="bi-"], span[class*="bi"], i[class*="bi"], .bi { font-family: "bootstrap-icons" !important; }
        span[class*="fa-"], i[class*="fa-"], span[class*="fa"], i[class*="fa"], .fa { font-family: FontAwesome !important; }
        .icon-badge::before, .icon-badge:before { content: none !important; display: none !important; }

        /* UNIFIED GLOBAL BUTTON SYSTEM */
        .btn {
            font-family: 'Poppins', sans-serif !important;
            border-radius: 6px !important;
            font-weight: 500 !important;
            transition: all 0.2s ease-in-out !important;
        }

        /* Regular Buttons (e.g., Primary Add, Submit, Filter) */
        .btn:not(.btn-sm):not(.btn-xs):not(.btn-icons) {
            height: 38px !important;
            padding: 8px 16px !important;
            font-size: 14px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            line-height: 1 !important;
        }

        /* Small Buttons (.btn-sm) */
        .btn-sm {
            height: 32px !important;
            padding: 6px 12px !important;
            font-size: 13px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 5px !important;
            line-height: 1 !important;
        }

        /* Square Table Action Icon Buttons (.btn-icons) */
        .btn-icons {
            width: 35px !important;
            height: 35px !important;
            min-width: 35px !important;
            min-height: 35px !important;
            padding: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 6px !important;
        }
        .btn-icons i, .btn-icons svg {
            font-size: 15px !important;
            color: #ffffff !important;
            stroke: #ffffff !important;
            margin: 0 !important;
            line-height: 1 !important;
        }

        /* Icon Text Buttons (.btn-icon-text) */
        .btn-icon-text {
            height: 38px !important;
            padding: 8px 16px !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            border-radius: 8px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            line-height: 1 !important;
        }
        .btn-icon-text i, .btn-icon-text .btn-icon-prepend {
            font-size: 15px !important;
            margin-right: 2px !important;
            line-height: 1 !important;
        }
        .btn-icon-text svg {
            stroke: #ffffff !important;
        }

        /* Fix DataTables Controls Layout & Structure */
        .dataTables_wrapper {
            position: relative !important;
            width: 100% !important;
            clear: both !important;
        }
        .dataTables_wrapper > .table-responsive {
            width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            margin-bottom: 1rem !important;
            clear: both !important;
            border: none !important;
        }
        .dataTables_wrapper > .table-responsive > table.dataTable {
            width: 100% !important;
            margin-bottom: 0 !important;
        }
        .dataTables_wrapper .dataTables_length {
            float: left !important;
            margin-bottom: 0.75rem !important;
        }
        .dataTables_wrapper .dataTables_length label {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            white-space: nowrap !important;
            font-size: 13.5px !important;
            color: #475569 !important;
            margin-bottom: 0 !important;
        }
        .dataTables_wrapper .dataTables_length select {
            width: auto !important;
            display: inline-block !important;
            padding: 4px 10px !important;
            height: 34px !important;
            border-radius: 6px !important;
            border: 1px solid #cbd5e1 !important;
            margin: 0 4px !important;
        }
        .dataTables_wrapper .dataTables_filter {
            float: right !important;
            text-align: right !important;
            margin-bottom: 0.75rem !important;
        }
        .dataTables_wrapper .dataTables_filter label {
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            white-space: nowrap !important;
            font-size: 13.5px !important;
            color: #475569 !important;
            margin-bottom: 0 !important;
        }
        .dataTables_wrapper .dataTables_filter input {
            height: 34px !important;
            border-radius: 6px !important;
            border: 1px solid #cbd5e1 !important;
            padding: 4px 10px !important;
        }
        .dataTables_wrapper .dataTables_info {
            float: left !important;
            margin-top: 0.75rem !important;
            font-size: 13.5px !important;
            color: #475569 !important;
        }
        .dataTables_wrapper .dataTables_paginate {
            float: right !important;
            text-align: right !important;
            margin-top: 0.75rem !important;
        }

        /* UNIFIED GLOBAL FORM INPUT SYSTEM */
        .form-control,
        .form-select,
        select.form-control,
        input[type="text"].form-control,
        input[type="email"].form-control,
        input[type="password"].form-control,
        input[type="number"].form-control,
        input[type="date"].form-control,
        input[type="url"].form-control,
        input[type="search"].form-control,
        .select2-container--default .select2-selection--single,
        .select2-container--bootstrap .select2-selection--single {
            height: 40px !important;
            min-height: 40px !important;
            padding: 8px 14px !important;
            font-size: 14px !important;
            font-family: 'Poppins', sans-serif !important;
            color: #1e293b !important;
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            box-shadow: none !important;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
            line-height: 1.4 !important;
        }

        /* Select2 Single Alignment Fixes */
        .select2-container--default .select2-selection--single .select2-selection__rendered,
        .select2-container--bootstrap .select2-selection--single .select2-selection__rendered {
            line-height: 22px !important;
            padding-left: 0 !important;
            color: #1e293b !important;
            font-size: 14px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow,
        .select2-container--bootstrap .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
            top: 0 !important;
            right: 8px !important;
        }

        /* Select2 Multiple Alignment Fixes */
        .select2-container--default .select2-selection--multiple,
        .select2-container--bootstrap .select2-selection--multiple {
            min-height: 40px !important;
            height: auto !important;
            padding: 4px 8px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            font-size: 14px !important;
            background-color: #ffffff !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #e2e8f0 !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 4px !important;
            padding: 2px 8px !important;
            font-size: 13px !important;
            color: #1e293b !important;
            margin-top: 3px !important;
        }

        /* Focus State for all inputs & Select2 */
        .form-control:focus,
        .form-select:focus,
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--multiple,
        .select2-container--open .select2-selection--single,
        .select2-container--open .select2-selection--multiple {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
            outline: 0 !important;
        }

        /* Form Labels Uniformity */
        .form-label, label {
            font-size: 14px !important;
            font-weight: 500 !important;
            color: #334155 !important;
            margin-bottom: 6px !important;
        }

        /* Textarea Uniformity */
        textarea.form-control {
            height: auto !important;
            min-height: 90px !important;
            padding: 10px 14px !important;
            line-height: 1.5 !important;
        }

        /* Small Form Inputs (.form-control-sm, .form-select-sm) */
        .form-control-sm {
            height: 34px !important;
            min-height: 34px !important;
            padding: 4px 10px !important;
            font-size: 13px !important;
            border-radius: 6px !important;
        }
        .form-select-sm {
            height: 34px !important;
            min-height: 34px !important;
            padding: 4px 2.2rem 4px 10px !important;
            font-size: 13px !important;
            border-radius: 6px !important;
            background-position: right 0.65rem center !important;
        }

        /* Fix Navbar Date Picker Layout */
        .navbar-date-picker {
            display: inline-flex !important;
            align-items: center !important;
            background-color: #ffffff !important;
            border-radius: 8px !important;
            padding: 0 12px !important;
            height: 38px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
            overflow: hidden !important;
        }
        .navbar-date-picker .input-group-addon,
        .navbar-date-picker .input-group-prepend,
        .navbar-date-picker .input-group-text,
        .navbar-date-picker .calendar-icon {
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
            margin-right: 8px !important;
            color: #3b82f6 !important;
            font-size: 15px !important;
            display: flex !important;
            align-items: center !important;
        }
        .navbar-date-picker input.form-control,
        .navbar-date-picker input {
            border: none !important;
            background: transparent !important;
            padding: 0 !important;
            height: 100% !important;
            min-height: unset !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            box-shadow: none !important;
            width: 95px !important;
            cursor: default !important;
        }

        /* FIX GLOBAL MODAL POSITIONING (ALWAYS CENTERED & NO SCROLL) */
        .modal {
            padding-right: 0 !important;
        }
        .modal.fade .modal-dialog {
            transform: scale(0.95) !important;
            transition: transform 0.2s ease-out !important;
        }
        .modal.show .modal-dialog {
            transform: scale(1) !important;
        }
        .modal-dialog-centered {
            display: flex !important;
            align-items: center !important;
            min-height: calc(100% - 3.5rem) !important;
            margin: 1.75rem auto !important;
        }
        .modal-dialog-centered .modal-content {
            margin: auto !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2) !important;
            border-radius: 12px !important;
            overflow: hidden !important;
        }

        /* GLOBAL STICKY FOOTER ENFORCEMENT */
        html, body {
            min-height: 100% !important;
        }
        .container-scroller {
            min-height: 100vh !important;
            display: flex !important;
            flex-direction: column !important;
        }
        .page-body-wrapper {
            flex: 1 0 auto !important;
            display: flex !important;
            align-items: stretch !important;
            min-height: calc(100vh - 60px) !important;
        }
        .main-panel {
            min-height: calc(100vh - 60px) !important;
            display: flex !important;
            flex-direction: column !important;
            flex: 1 1 auto !important;
        }
        .content-wrapper {
            flex: 1 0 auto !important;
            padding-bottom: 2.5rem !important;
        }
        footer.footer {
            margin-top: auto !important;
            width: 100% !important;
        }
    </style>
    <link rel="shortcut icon" href="{{ asset('/assets/img/eval-obe-logo.png') }}" />
    {{-- CDN CHART JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- CDN LABELS CHART JS --}}
    <script src="https://unpkg.com/chart.js-plugin-labels-dv/dist/chartjs-plugin-labels.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body data-user-role="{{ auth()->user()->otoritas->otoritas }}">
    <div class="container-scroller">
