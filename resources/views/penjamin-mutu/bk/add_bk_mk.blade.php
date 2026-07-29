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

<h3 class="px-4 pb-4 fw-bold text-center">Halaman Tambah Pemetaan BK-MK</h3>

<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Tambah Pemetaan Bahan Kajian ke Mata Kuliah</h4>

            <form action="{{ route($currentPrefix . 'bk.bk-mk-store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="bk_id">Pilih Bahan Kajian:</label>
                    <select class="form-control" id="bk_id" name="bk_id" required >
                        <option value="" selected disabled>Pilih Bahan Kajian</option>
                        @foreach ($bks as $bk)
                            <option value="{{ $bk->id }}">{{ $bk->kode }} - {{ $bk->nama }}</option>  
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="mk_id">Pilih MK</label>
                    <div id="mk_id" class="border ps-5" style="max-height: 200px; overflow-y: auto;">
                        
                    </div>

                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
    // Mendefinisikan fungsi di dalam DOMContentLoaded
    function getSelectedValue() {
        var bkId = document.getElementById("bk_id").value;
        console.log(bkId); // Pastikan menggunakan console.log, bukan console.console
        const otoritas = "{{ $userOtoritas }}";
        if(otoritas === "Kepala Program Studi"){
            urlget = `/kepala-program-studi/bk/get-mk-by-bk/${bkId}`;
        } else if (otoritas === 'Penjamin Mutu Program Studi'){
            urlget = `/penjamin-mutu/program-studi/bk/get-mk-by-bk/${bkId}`;
        }
        if(bkId){
            $.ajax({
                url: urlget,
                method: 'GET',
                success: function(data) {
                    let mkOptions = '';
                    if (data.mks.length > 0) {
                        data.mks.forEach(mk => {
                        mkOptions += `<div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="mk_kodes[]" value="${mk.kode}" id="mk_${mk.kode}">
                                        <label class="form-check-label" for="mk_${mk.kode}">
                                            ${mk.kode} - ${mk.nama}
                                        </label>
                                    </div>`;
                       
                    });
                    } else {
                        mkOptions += `<label class="text-muted">Tidak ada data MK untuk BK yang dipilih.</label>`;
                    }
                    $('#mk_id').html(mkOptions);
                },
                error: function(xhr, status, error) {
                    alert('Gagal memuat data MK.');
                }
            });
        }else {
            $('#mk_id').html('');
        }

    }

    // Menambahkan event listener untuk onchange
    var bkElement = document.getElementById("bk_id");
    if (bkElement) {
        bkElement.addEventListener("change", getSelectedValue);
    }
    
});
  
</script>

@endsection