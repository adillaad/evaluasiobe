{{-- header --}}
@include('dosen.layout.header')
{{-- navbar --}}
@include('components.navbar')
{{-- partial --}}
<div class="container-fluid page-body-wrapper">
    @include('dosen.layout.sidebar')
    {{-- partial --}}
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
