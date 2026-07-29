@extends(auth()->user()->otoritas->otoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')
@if (session()->has('failed'))
    <div class="alert alert-danger" role="alert" id="box">
        <div>{{ session('failed') }}</div>
    </div>
@elseif (session()->has('success'))
    <div class="alert greenAdd" role="alert" id="box">
        
    </div>
@endif

<h3 class="px-4 pb-4 fw-bold text-center">Halaman Tambah Pemetaan MK-CPMK-Sub CPMK</h3>

<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Tambah Pemetaan Mata Kuliah ke Sub Capaian Pembelajaran Mata Kuliah</h4>

            <form action="{{ route($currentPrefix . 'cpl-cpmk.cpmk-mk-subcpmk-store') }}" method="POST">
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
                    <label for="sub_cpmk_id">Pilih Sub CPMK</label>
                    <div id="sub_cpmk_id" class="border ps-5" style="max-height: 200px; overflow-y: auto;">
                        
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
    function getSelectedValue() {
        var mkKode = document.getElementById("mk_kode").value;
        console.log(mkKode); 
        const otoritas = "{{ $userOtoritas }}";
        if(otoritas === "Kepala Program Studi"){
            urlget = `/kepala-program-studi/cpl-cpmk/get-subcpmk-by-mk/${mkKode}`;
        } else if (otoritas === 'Penjamin Mutu Program Studi'){
            urlget = `/penjamin-mutu/program-studi/cpl-cpmk/get-subcpmk-by-mk/${mkKode}`;
        }
        if(mkKode){
            $.ajax({
                // url: `/penjamin-mutu/program-studi/cpl-cpmk/get-subcpmk-by-mk/${mkKode}`,
                url: urlget,
                method: 'GET',
                success: function(data) {
                    let subCpmkOptions = '';
                    if (data.subcpmks.length > 0) {
                    data.subcpmks.forEach(subcpmk => {
                        subCpmkOptions += `<div class="form-check">
                            <input class="form-check-input" type="checkbox" name="sub_cpmk_ids[]" value="${subcpmk.id}" id="sub_cpmk_${subcpmk.id}">
                            <label class="form-check-label" for="sub_cpmk_${subcpmk.id}">
                                ${subcpmk.kode} - ${subcpmk.uraian}
                            </label>
                        </div>`;
                    });
                    } else {
                        subCpmkOptions = `<label class="text-muted">Tidak ada data Sub CPMK untuk MK yang dipilih.</label>`;
                    }
                    $('#sub_cpmk_id').html(subCpmkOptions);
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'Gagal memuat data MK.';
    
                    // Jika server mengembalikan pesan error, tampilkan pesannya
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage += `\nDetail: ${xhr.responseJSON.message}`;
                    } else if (xhr.responseText) {
                        errorMessage += `\nDetail: ${xhr.responseText}`;
                    } else {
                        errorMessage += `\nStatus: ${status}, Error: ${error}`;
                    }

                    // Tampilkan pesan error dalam alert
                    alert(errorMessage);
                                }
            });
        }else {
            $('#sub_cpmk_id').html('');
        }

    }

    var mkElement = document.getElementById("mk_kode");
    if (mkElement) {
        mkElement.addEventListener("change", getSelectedValue);
    }
    
});
  
</script>

@endsection