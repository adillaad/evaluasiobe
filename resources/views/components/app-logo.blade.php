@props([
    'dark' => true,
])

@once
    <style>
        .navbar-app-logo {
            height: 34px;
            width: auto;
            max-width: 160px;
            object-fit: contain;
            vertical-align: middle;
        }

        .navbar-app-logo--dark {
            filter: brightness(0) saturate(100%);
        }
    </style>
@endonce

<img src="{{ asset('assets/img/eval-obe-logo.png') }}" alt="Evaluasi OBE"
    {{ $attributes->merge(['class' => 'navbar-app-logo' . ($dark ? '' : ' navbar-app-logo--dark')]) }}>
