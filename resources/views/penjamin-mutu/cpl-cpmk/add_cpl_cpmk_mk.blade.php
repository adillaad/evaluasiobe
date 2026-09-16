{{-- @php
    $routePrefix = [
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'admin.';
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
            <h4 class="card-title">Tambah Pemetaan Mata Kuliah ke Capaian Pembelajaran Mata Kuliah</h4>

            <form action="{{ route($currentPrefix . 'cpl-cpmk.cpl-cpmk-mk-store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="mk_kode">Pilih Mata Kuliah:</label>
                    <select class="form-control" id="mk_kode" name="mk_kode" required >
                        <option value="" selected disabled>Pilih Mata Kuliah</option>
                        @foreach ($mks as $mk)
                            <option value="{{ $mk->kode }}">{{ $mk->kode }} - {{ $mk->nama }}</option>  
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="cpmk_id">Pilih CPMK</label>
                    <div id="cpmk_id" class="border ps-5" style="max-height: 200px; overflow-y: auto;">
                        
                    </div>

                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
    function getSelectedValue() {
        var mkKode = document.getElementById("mk_kode").value;
        console.log(mkKode);
        const otoritas = "{{ $userOtoritas }}";
        if(otoritas === "Kepala Program Studi"){
            urlget = `/kepala-program-studi/cpl-cpmk/get-cpmk-by-mk/${mkKode}`;
        } else if (otoritas === 'Penjamin Mutu Program Studi'){
            urlget = `/penjamin-mutu/program-studi/cpl-cpmk/get-cpmk-by-mk/${mkKode}`;
        }
        if(mkKode){
            $.ajax({
                url: urlget,
                method: 'GET',
                success: function(data) {
                    let cpmkOptions = '';
                    if (data.cpmks.length > 0) {
                    data.cpmks.forEach(cpmk => {
                        cpmkOptions += `<div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="cpmk_ids[]" value="${cpmk.id}" id="cpmk_${cpmk.id}">
                                            <label class="form-check-label" for="cpmk_${cpmk.id}">
                                                ${cpmk.kode} - ${cpmk.judul}
                                            </label>
                                        </div>`;
                    });
                    } else {
                        cpmkOptions = `<label class="text-muted">Tidak ada data CPMK untuk MK yang dipilih.</label>`;
                    }
                    $('#cpmk_id').html(cpmkOptions);
                },
                error: function(xhr, status, error) {
                    alert('Gagal memuat data MK.');
                }
            });
        }else {
            $('#cpmk_id').html('');
        }

    }

    var mkElement = document.getElementById("mk_kode");
    if (mkElement) {
        mkElement.addEventListener("change", getSelectedValue);
    }
    
});
  
</script>

@endsection