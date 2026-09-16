@include('mahasiswa.layout.header')
@include('components.navbar')

<div class="container-fluid page-body-wrapper">
    @include('mahasiswa.layout.sidebar')
    <div class="main-panel">
        <div class="content-wrapper" style="padding-bottom: 60px;">
            <div class="row">
                <div class="px-3">
                    @include('components.success')
                </div>
                @yield('content')
            </div>
        </div>
        @include('mahasiswa.layout.footer')
@stack('scripts')
