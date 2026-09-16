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
    <div class="alert greenAdd" role="alert" id="box">
        
    </div>
@endif

<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Tambah Pemetaan CPL ke Bahan Kajian</h4>

            <form action="{{ route($currentPrefix.'cpl.cpl-bk-store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="cpl_id">Pilih CPL:</label>
                    <select class="form-control" id="cpl_id" name="cpl_id" required onchange="getSelectedValue();">
                        <option value="" selected disabled>Pilih CPL</option>
                        @foreach ($cpls as $cpl)
                            <option value="{{ $cpl->id }}">{{ $cpl->kode }} - {{ $cpl->judul }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="bk_ids">Pilih Bahan Kajian yang Terkait:</label>
                    <div class="border ps-5" style="max-height: 200px; overflow-y: auto;">
                        @foreach ($bks as $bk)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="bk_ids[]" value="{{ $bk->id }}" id="bk_{{ $bk->id }}">
                                <label class="form-check-label" for="bk_{{ $bk->id }}">
                                    {{ $bk->kode }} - {{ $bk->nama }}
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


<script>
    document.addEventListener("DOMContentLoaded", function() {
    // Mendefinisikan fungsi di dalam DOMContentLoaded
    function getSelectedValue() {
        var selectedValue = document.getElementById("cpl_id").value;
        console.log(selectedValue); // Pastikan menggunakan console.log, bukan console.console
    }

    // Menambahkan event listener untuk onchange
    var cplElement = document.getElementById("cpl_id");
    if (cplElement) {
        cplElement.addEventListener("change", getSelectedValue);
    }
});
</script>

@endsection