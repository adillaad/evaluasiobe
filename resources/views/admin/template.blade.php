{{-- header --}}
@include('admin.layout.header')
{{-- navbar --}}
@include('components.navbar')
{{-- partial --}}
<div class="container-fluid page-body-wrapper">
    @include('admin.layout.sidebar')
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
    </div>
</div>
@include('admin.layout.footer')
@stack('scripts')
