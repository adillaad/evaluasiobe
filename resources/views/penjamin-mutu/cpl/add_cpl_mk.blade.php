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
        <div>{{ session('failed') }}</div>
    </div>
@elseif (session()->has('success'))
    <div class="alert alert-success" role="alert" id="box">
        <div>{{ session('success') }}</div>
    </div>
@endif

<h3 class="px-4 pb-4 fw-bold text-center">Halaman Tambah Pemetaan CPL-MK</h3>

<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Tambah Pemetaan CPL ke Mata Kuliah</h4>

            <form action="{{ route($currentPrefix.'cpl.cpl-mk-store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="cpl_id">Pilih CPL:</label>
                    <select class="form-control" id="cpl_id" name="cpl_id" required>
                        <option value="" selected disabled>Pilih CPL</option>
                        @foreach ($cpls as $cpl)
                            <option value="{{ $cpl->id }}">{{ $cpl->kode }} - {{ $cpl->judul }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="bk_kodes">Pilih Mata Kuliah yang Terkait:</label>
                    <div class="border ps-5" style="max-height: 200px; overflow-y: auto;">
                        @foreach ($mks as $mk)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="mk_kodes[]" value="{{ $mk->kode }}" id="mk_{{ $mk->kode }}">
                                <label class="form-check-label" for="mk_{{ $mk->kode }}">
                                    {{ $mk->kode }} - {{ $mk->nama }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>


@endsection