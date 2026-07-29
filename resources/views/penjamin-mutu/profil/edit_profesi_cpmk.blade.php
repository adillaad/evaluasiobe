{{-- @php
    $routePrefix = [
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp --}}

@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')

@section('content')
    @if (session()->has('failed'))
        <div class="alert alert-danger" role="alert" id="box">
            {{ session('failed') }}
        </div>
    @elseif (session()->has('success'))
        <div class="alert greenAdd" role="alert" id="box">
            {{ session('success') }}
        </div>
    @endif

    <h3 class="px-4 pb-4 fw-bold text-center">
        Halaman Edit Pemetaan Profesi-CPMK
    </h3>

    <div class="container mt-5">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">
                    Edit Pemetaan Profesi ke CPMK
                </h4>

                <div class="mb-3">
                    <strong>
                        {{ $cpmk->kode }} - {{ $cpmk->judul }}
                    </strong>
                </div>

                <form action="{{ route($currentPrefix . 'profesi-cpmk-update', $cpmk->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group mt-3">
                        <label>Pilih Profesi</label>

                        <div class="border p-5" style="max-height: 200px; overflow-y: auto;">
                            @foreach ($profesis as $profesi)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="profesis[]"
                                        value="{{ $profesi->id }}"
                                        {{ in_array($profesi->id, $selected) ? 'checked' : '' }}>

                                    <label class="form-check-label">
                                        {{ $profesi->nama }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <a href="{{ route($currentPrefix . 'indexPemetaanCPMKProf') }}" class="btn btn-secondary">
                            Kembali
                        </a>

                        <button type="submit" class="btn btn-primary">
                            Update
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
