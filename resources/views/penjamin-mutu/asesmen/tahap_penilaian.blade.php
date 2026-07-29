@extends(auth()->user()->otoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')
@if (session()->has('failed'))
    <div class="alert alert-danger" role="alert" id="box">
        <div>{{ session('failed') }}</div>
    </div>
@elseif (session()->has('success'))
    <div class="alert greenAdd" role="alert" id="box">
        <div>{{ session('success') }}</div>
    </div>
@endif

<h3 class="px-4 pb-4 fw-bold text-center">Halaman Tahap Penilaian</h3>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div id="Tahap_Penilaian"></div>
        </div>
    </div>
</div>

@endsection