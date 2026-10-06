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
            height: 35px !important;
            padding: 0 14px !important;
            font-size: 11.5px !important;
            font-weight: 500 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            line-height: 1 !important;
        }

        /* Small Buttons (.btn-sm) */
        .btn-sm {
            height: 32px !important;
            padding: 0 10px !important;
            font-size: 11px !important;
            font-weight: 500 !important;
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
            margin: 0 !important;
            line-height: 1 !important;
        }

        /* UNIFIED GLOBAL BUTTON COLOR STYLES & OUTLINE ICON STATES */
        .btn-primary, a.btn-primary, button.btn-primary {
            background-color: #0284c7 !important;
            border-color: #0284c7 !important;
            color: #ffffff !important;
        }
        .btn-primary i, .btn-primary i::before, .btn-primary span, .btn-primary svg {
            color: #ffffff !important;
            stroke: #ffffff !important;
            fill: #ffffff !important;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active,
        a.btn-primary:hover, a.btn-primary:focus, a.btn-primary:active {
            background-color: #0369a1 !important;
            border-color: #0369a1 !important;
            color: #ffffff !important;
        }

        .btn-outline-primary, a.btn-outline-primary, button.btn-outline-primary {
            border: 1px solid #0284c7 !important;
            background-color: transparent !important;
            color: #0284c7 !important;
        }
        .btn-outline-primary i, .btn-outline-primary i::before, .btn-outline-primary span, .btn-outline-primary svg {
            color: #0284c7 !important;
            stroke: #0284c7 !important;
        }
        .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary:active, .btn-outline-primary.active,
        a.btn-outline-primary:hover, a.btn-outline-primary:focus, a.btn-outline-primary:active, a.btn-outline-primary.active {
            background-color: #0284c7 !important;
            border-color: #0284c7 !important;
            color: #ffffff !important;
        }
        .btn-outline-primary:hover *, .btn-outline-primary:focus *,
        .btn-outline-primary:active *, .btn-outline-primary.active *,
        .btn-outline-primary:hover i, .btn-outline-primary:hover i::before,
        .btn-outline-primary:focus i, .btn-outline-primary:focus i::before,
        .btn-outline-primary:active i, .btn-outline-primary:active i::before,
        .btn-outline-primary.active i, .btn-outline-primary.active i::before,
        .btn-outline-primary:hover span, .btn-outline-primary:focus span,
        .btn-outline-primary:active span, .btn-outline-primary.active span,
        .btn-outline-primary:hover svg, .btn-outline-primary:focus svg,
        .btn-outline-primary:active svg, .btn-outline-primary.active svg {
            color: #ffffff !important;
            stroke: #ffffff !important;
            fill: #ffffff !important;
        }

        .btn-danger, a.btn-danger, button.btn-danger {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: #ffffff !important;
        }
        .btn-danger i, .btn-danger i::before, .btn-danger span, .btn-danger svg {
            color: #ffffff !important;
            stroke: #ffffff !important;
            fill: #ffffff !important;
        }
        .btn-danger:hover, .btn-danger:focus, .btn-danger:active,
        a.btn-danger:hover, a.btn-danger:focus, a.btn-danger:active {
            background-color: #bb2d3b !important;
            border-color: #bb2d3b !important;
            color: #ffffff !important;
        }

        .btn-outline-danger, a.btn-outline-danger, button.btn-outline-danger {
            border: 1px solid #dc3545 !important;
            background-color: transparent !important;
            color: #dc3545 !important;
        }
        .btn-outline-danger i, .btn-outline-danger i::before, .btn-outline-danger span, .btn-outline-danger svg {
            color: #dc3545 !important;
            stroke: #dc3545 !important;
        }
        .btn-outline-danger:hover, .btn-outline-danger:focus, .btn-outline-danger:active, .btn-outline-danger.active,
        a.btn-outline-danger:hover, a.btn-outline-danger:focus, a.btn-outline-danger:active, a.btn-outline-danger.active {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: #ffffff !important;
        }
        .btn-outline-danger:hover i, .btn-outline-danger:hover i::before,
        .btn-outline-danger:focus i, .btn-outline-danger:focus i::before,
        .btn-outline-danger:active i, .btn-outline-danger:active i::before,
        .btn-outline-danger.active i, .btn-outline-danger.active i::before,
        .btn-outline-danger:hover svg, .btn-outline-danger:focus svg,
        .btn-outline-danger:active svg, .btn-outline-danger.active svg {
            color: #ffffff !important;
            stroke: #ffffff !important;
            fill: #ffffff !important;
        }

        /* Icon Text Buttons (.btn-icon-text) */
        .btn-icon-text {
            height: 35px !important;
            padding: 0 14px !important;
            font-size: 11.5px !important;
            font-weight: 500 !important;
            border-radius: 6px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            line-height: 1 !important;
        }
        .btn-icon-text i, .btn-icon-text .btn-icon-prepend {
            font-size: 12.5px !important;
            margin-right: 2px !important;
            line-height: 1 !important;
        }
        .btn-icon-text svg {
            stroke: #ffffff !important;
        }

        /* UNIFIED MATRIX EDIT BANNER STYLES - LIGHT BLUE, HIGH CONTRAST & LEGIBLE */
        #matrix-edit-banner, .matrix-banner-style {
            background-color: #f0f9ff !important;
            border: 1px solid #bae6fd !important;
            border-left: 5px solid #0284c7 !important;
            color: #1e293b !important;
        }
        #matrix-edit-banner h6, #matrix-edit-banner .text-primary, .matrix-banner-style h6, .matrix-title-style {
            color: #0369a1 !important;
            font-weight: 700 !important;
        }
        #matrix-edit-banner small, #matrix-edit-banner .text-secondary, .matrix-banner-style small {
            color: #475569 !important;
            font-weight: 500 !important;
        }
        #matrix-edit-banner .badge, .matrix-badge-style {
            background-color: #0284c7 !important;
            color: #ffffff !important;
        }
        #matrix-edit-banner .btn,
        #matrix-edit-banner button,
        .matrix-banner-style .btn,
        .matrix-banner-style button {
            height: 36px !important;
            min-height: 36px !important;
            max-height: 36px !important;
            padding: 0 20px !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            line-height: 1 !important;
            border-radius: 20px !important;
            white-space: nowrap !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            box-sizing: border-box !important;
            margin: 0 !important;
            text-align: center !important;
        }
        #matrix-edit-banner .btn *,
        .matrix-banner-style .btn * {
            white-space: nowrap !important;
            line-height: 1 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
        }
        #matrix-edit-banner .btn-light, .matrix-banner-style .btn-light {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #334155 !important;
            font-weight: 600 !important;
        }
        #matrix-edit-banner .btn-light:hover, .matrix-banner-style .btn-light:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }
        #matrix-edit-banner .btn-primary, .matrix-banner-style .btn-primary {
            background-color: #0284c7 !important;
            border-color: #0284c7 !important;
            color: #ffffff !important;
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
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            float: right !important;
            margin-top: 0.75rem !important;
        }

        /* UNIFIED COMPACT PAGINATION BUTTONS */
        .pagination {
            display: inline-flex !important;
            gap: 4px !important;
            border-radius: 8px !important;
        }
        .pagination .page-item .page-link,
        .page-link,
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            height: 32px !important;
            min-width: 32px !important;
            padding: 0 10px !important;
            margin: 0 !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            border-radius: 6px !important;
            border: 1px solid #cbd5e1 !important;
            background: #ffffff !important;
            background-image: none !important;
            color: #475569 !important;
            cursor: pointer !important;
            box-shadow: none !important;
            transition: all 0.15s ease-in-out !important;
            text-decoration: none !important;
            line-height: 1 !important;
        }
        .pagination .page-item .page-link:hover,
        .page-link:hover,
        .pagination .page-item .page-link:focus,
        .page-link:focus,
        .pagination .page-item .page-link:active,
        .page-link:active,
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover,
        .dataTables_wrapper .dataTables_paginate .paginate_button:focus,
        .dataTables_wrapper .dataTables_paginate .paginate_button:active {
            color: #0f172a !important;
            background: #e2e8f0 !important;
            background-image: none !important;
            border-color: #cbd5e1 !important;
            box-shadow: none !important;
        }
        .pagination .page-item.active .page-link,
        .page-link.active,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:focus,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:active {
            background-color: #0284c7 !important;
            background: #0284c7 !important;
            background-image: none !important;
            border-color: #0284c7 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3) !important;
        }
        .pagination .page-item.disabled .page-link,
        .page-link.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:focus,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:active {
            color: #94a3b8 !important;
            background: #f8fafc !important;
            background-image: none !important;
            border-color: #e2e8f0 !important;
            cursor: not-allowed !important;
            opacity: 0.7 !important;
            box-shadow: none !important;
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
        .form-control-sm,
        .form-select-sm {
            height: 34px !important;
            min-height: 34px !important;
            padding: 4px 10px !important;
            font-size: 13px !important;
            border-radius: 6px !important;
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
    </style>
    <link rel="shortcut icon" href="{{ asset('/assets/img/eval-obe-logo.png') }}" />
</head>
<body data-user-role="{{ auth()->user()->otoritas->otoritas }}">
    <div class="container-scroller">