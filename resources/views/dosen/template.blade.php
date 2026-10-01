@php
    $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
    $isPenjaminMutuOrKaprodi = in_array($userOtoritas, [
        'Kepala Program Studi',
        'Penjamin Mutu Program Studi',
        'Penjamin Mutu Fakultas',
        'Penjamin Mutu Universitas'
    ]);
@endphp

@if ($isPenjaminMutuOrKaprodi)
    @include('penjamin-mutu.layout.header')
    @include('components.navbar')
    <div class="container-fluid page-body-wrapper">
        @include('penjamin-mutu.layout.sidebar')
        <div class="main-panel">
            <div class="content-wrapper">
                <div class="row">
                    <div class="px-3">
                        @include('components.success')
                    </div>
                    @yield('content')
                </div>
            </div>
            @include('penjamin-mutu.layout.footer')
    @stack('scripts')
@else
    @include('dosen.layout.header')
    @include('components.navbar')
    <div class="container-fluid page-body-wrapper">
        @include('dosen.layout.sidebar')
        <div class="main-panel">
            <div class="content-wrapper">
                <div class="row">
                    <div class="px-3">
                        @include('components.success')
                    </div>
                    @yield('content')
                </div>
            </div>
            @include('dosen.layout.footer')
    @stack('scripts')
@endif
