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

<h3 class="px-4 pb-4 fw-bold text-center">Halaman Add Asesmen</h3>

<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Tambah Asesmen</h4>

            <form action="{{ route($currentPrefix . 'asesmen.asesmen-store') }}" method="POST">
                @csrf
                <!-- Select mk -->
                <div class="form-group">
                    <label for="mk_kode">Pilih Mata Kuliah:</label>
                    <select class="form-control" id="mk_kode" name="mk_kode" required >
                        <option value="" selected disabled>Pilih Mata Kuliah</option>
                        @foreach ($mks as $mk)
                            <option value="{{ $mk->kode }}">{{ $mk->kode }} - {{ $mk->nama }}</option>  
                        @endforeach
                    </select>
                </div>

                <!-- Select CPL -->
                <div class="form-group">
                    <label for="cpl_id">Pilih CPL</label>
                    <select id="cpl_id" name="cpl_id" class="form-control" required disabled>
                        <option value="">-- Pilih CPL --</option>
                    </select>

                </div>

                <!-- Select CPMK -->
                <div class="form-group">
                    <label for="cpmk-select">Pilih CPMK</label>
                    <select id="cpmk-select" name="cpmk_id" class="form-control" required disabled>
                        <option value="">-- Pilih CPMK --</option>
                    </select>
                </div>

                <!-- Select tahap penilaian -->
                <div class="form-group">
                    <label for="tahap-penilaian-select">Pilih Tahap Penilaian</label>
                    <div id="tahap_penilaian" class="border ps-5 pt-2 pb-2" style="max-height: 200px; overflow-y: auto;">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tahap_penilaians[]" value="Akhir Semester" id="tahap_akhir_semester">
                            <label class="form-check-label" for="tahap_akhir_semester">
                                Akhir Semester
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tahap_penilaians[]" value="Tengah Semester" id="tahap_tengah_semester">
                            <label class="form-check-label" for="tahap_tengah_semester">
                                Tengah Semester
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tahap_penilaians[]" value="Perkuliahan" id="tahap_perkuliahan">
                            <label class="form-check-label" for="tahap_perkuliahan">
                                Perkuliahan
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Select instrumen -->
                <div class="form-group">
                    <label for="instrumen-select">Pilih Instrumen</label>
                    <select id="instrumen-select" name="instrumen" class="form-control" required >
                        <option value=""disabled selected>-- Pilih Instrumen --</option>
                        <option value="Rubrik">Rubrik</option>
                        <option value="Panduan Proyek Akhir">Panduan Proyek Akhir</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="kriteria-penilaian">Pilih Kriteria Penilaian</label>
                    <input type="text" id="search-kriteria" class="form-control" placeholder="Cari Kriteria...">
                    <div id="kriteria-penilaian" class="border ps-5 pt-1 pb-2" style="max-height: 200px; overflow-y: auto;">
                        @foreach ($instrumenPenilaians as $instrumenPenilaian)
                            <div class="form-check d-flex align-items-center mb-2 kriteria-item" >
                                <input class="form-check-input kriteria-checkbox me-2" type="checkbox" name="kriteria_penilaian[]" value="{{ $instrumenPenilaian->id }}" id="kriteria_{{ $instrumenPenilaian->id }}">
                                <label class="form-check-label me-3" for="kriteria_{{ $instrumenPenilaian->id }}">{{ $instrumenPenilaian->nama_kriteria }}</label>
                                <input type="number" step="0.01" class="form-control form-control-sm kriteria-bobot" name="bobot_kriteria[{{ $instrumenPenilaian->id }}]" placeholder="Bobot (%)" disabled style="width: 80px;" min="0">
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label for="metode-penilaian">Pilih Metode Penilaian</label>
                    <input type="text" id="search-metode" class="form-control" placeholder="Cari Metode...">
                    <div id="metode-penilaian" class="border ps-5 pt-2 pb-2" style="max-height: 200px; overflow-y: auto;">
                        @foreach ($metodepenilaians as $metodepenilaian)
                        <div class="form-check d-flex align-items-center mb-2 metode-item">
                            <input class="form-check-input metode-checkbox me-2" type="checkbox" name="metode_penilaian[]" value="{{ $metodepenilaian->id }}" id="metode_{{ $metodepenilaian->id }}">
                            <label class="form-check-label me-3" for="metode_{{ $metodepenilaian->id }}">{{ $metodepenilaian->nama }}</label>
                            <input type="number" step="0.01" class="form-control form-control-sm metode-bobot" name="bobot[{{ $metodepenilaian->id }}]" placeholder="Bobot (%)" disabled style="width: 80px;" max="100" min="0">
                        </div>
                        @endforeach
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
    function getSelectedCPLValue() {
        var mkKode = document.getElementById("mk_kode").value;
        var cplSelect = document.getElementById('cpl_id'); 
        // console.log(mkKode);
        const otoritas = "{{ $userOtoritas }}";
        if(otoritas === "Kepala Program Studi"){
            urlget = `/kepala-program-studi/asesmen/get-cpl-by-mk/${mkKode}`;
        } else if (otoritas === 'Penjamin Mutu Program Studi'){
            urlget = `/penjamin-mutu/program-studi/asesmen/get-cpl-by-mk/${mkKode}`;
        }
        if(mkKode){
            cplSelect.innerHTML = '<option value="">Loading...</option>';
            cplSelect.disabled = true ;
            $.ajax({
                url: urlget,
                method: 'GET',
                success: function(data) {
                    console.log(data);
                    if (data.cpls.length > 0) {
                        cplSelect.innerHTML = '<option value="">-- Pilih CPL --</option>';
                        data.cpls.forEach(cpl => {
                            const option = document.createElement('option');
                            option.value = cpl.id;
                            option.textContent = `${cpl.kode} - ${cpl.judul}`;
                            cplSelect.appendChild(option);
                        });
                        cplSelect.disabled = false;
                    } else {
                        cplSelect.innerHTML = '<option value="">-- Tidak ada data CPL untuk MK yang dipilih --</option>';
                    }
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'Gagal memuat data CPL.';
    
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
            cplSelect.innerHTML = '<option value="">-- Pilih CPL --</option>';
            cplSelect.disabled = true;
        }

    }

    function getSelectedCpmkValue() {
        var cplId = document.getElementById("cpl_id").value;
        var mkKode = document.getElementById("mk_kode").value;
        var cpmkSelect = document.getElementById('cpmk-select');  
        // console.log(cplId);
        const otoritas = "{{ $userOtoritas }}";
        if(otoritas === "Kepala Program Studi"){
            urlget = `/kepala-program-studi/asesmen/get-cpmk-by-cpl/${cplId}?mk_kode=${mkKode}`;
        } else if (otoritas === 'Penjamin Mutu Program Studi'){
            urlget = `/penjamin-mutu/program-studi/asesmen/get-cpmk-by-cpl/${cplId}?mk_kode=${mkKode}`;
        }
        if(cplId){
            cpmkSelect.innerHTML = '<option value="">Loading...</option>';
            cpmkSelect.disabled = true ;
            $.ajax({
                url: urlget,
                method: 'GET',
                success: function(data) {
                    if (data.cpmks.length > 0) {
                        cpmkSelect.innerHTML = '<option value="">-- Pilih CPMK --</option>';
                    data.cpmks.forEach(cpmk => {
                        const option = document.createElement('option');
                        option.value = cpmk.id;
                        option.textContent = `${cpmk.kode} - ${cpmk.judul}`;
                        cpmkSelect.appendChild(option);
                    });
                        cpmkSelect.disabled = false;
                    } else {
                        cpmkSelect.innerHTML = '<option value="">-- Tidak ada data CPMK untuk MK yang dipilih --</option>';
                    }
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'Gagal memuat data CPMK.';
    
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
            cpmkSelect.innerHTML = '<option value="">-- Pilih MK --</option>';
            cpmkSelect.disabled = true;
        }

    }

    //_____________________kode untuk cek max total bobot kriteria yang dapat diinput______________
   
    var maxKriteriaBobotMK = 0;
    // Fungsi untuk mengambil maxKriteriaBobot dari server

    function getMaxKriteriaBobotMK(){
         var mkKode = document.getElementById("mk_kode").value;

        if(!mkKode) return;
        const otoritas = "{{ $userOtoritas }}";
        if(otoritas === "Kepala Program Studi"){
            urlget = `/kepala-program-studi/asesmen/getMaxBobotMK/${mkKode}`;
        } else if (otoritas === 'Penjamin Mutu Program Studi'){
            urlget = `/penjamin-mutu/program-studi/asesmen/getMaxBobotMK/${mkKode}`;
        }
        $.ajax({
            url: urlget,
            method: 'GET',
            success: function(data){
                if(data.maxKriteriaBobotMK !== undefined){
                    maxKriteriaBobotMK = data.maxKriteriaBobotMK;
                    console.log(maxKriteriaBobotMK);
                    updateMaxBobotInput()
                }
            },
            error: function(){
                alert("Terjadi kesalahan saat mengambil data bobot.")
            }
        })
    }

    // Fungsi untuk memperbarui max input berdasarkan total yang sudah diisi
    function updateMaxBobotInput(){
        let totalTerpakai = getTotalBobot();

        $(".kriteria-bobot").each(function(){
            let sisaBobot = maxKriteriaBobotMK - (totalTerpakai - (parseFloat($(this).val()) || 0));
            $(this).attr("max", sisaBobot > 0 ? sisaBobot : 0);
        });
    }

  
    // Fungsi untuk menghitung total bobot yang sudah diisi
    function getTotalBobot(){
        let total  = 0;
        $(".kriteria-bobot:enabled").each(function(){
            let value = parseFloat($(this).val()) ||0;
            total += value;
        });
        return total;
    }

    //event listener ketika nilai bobot berubah
    $(document).on("input", ".kriteria-bobot", function() {
        let totalTerpakai = getTotalBobot();

        if (totalTerpakai > maxKriteriaBobotMK) {
            alert("Total bobot melebihi batas yang diperbolehkan!");
            $(this).val(""); // Reset input yang terakhir dimasukkan
        }

        updateMaxBobotInput();
    }); 
    //_____________________kode untuk cek max total bobot kriteria yang dapat diinput______________

    var mkElement = document.getElementById("mk_kode");
    if (mkElement) {
        mkElement.addEventListener("change", function(){
            getSelectedCPLValue();
            getMaxKriteriaBobotMK();
        });        
    }

    var cplElement = document.getElementById("cpl_id");
    if (cplElement) {
        cplElement.addEventListener("change", function(){
            getSelectedCpmkValue();
        });
    }

    $('.metode-checkbox').change(function() {
        const isChecked = $(this).is(':checked');
        $(this).closest('.form-check').find('.metode-bobot').prop('disabled', !isChecked).val('');
    });

    $(".kriteria-checkbox").change(function() {
        let bobotInput = $(this).closest('.form-check').find('.kriteria-bobot');

        if ($(this).is(':checked')) {
            bobotInput.prop("disabled", false);
        } else {
            bobotInput.prop("disabled", true).val(""); // Reset nilai jika uncheck
        }

        updateMaxBobotInput();
    });

    $('form').submit(function(event) {
        let isValidMetode = true;
        let isValidKriteria = true;

        // Periksa setiap metode penilaian yang dipilih
        $('.metode-checkbox:checked').each(function() {
            const bobotInput = $(this).closest('.form-check').find('.metode-bobot');
            if (!bobotInput.val()) {
                isValidMetode = false;
                alert('Harap isi bobot untuk metode penilaian yang dipilih.');
                bobotInput.focus();
                return false; // Hentikan iterasi
            }
        });

        // Periksa setiap kriteria yang dipilih
        $('.kriteria-checkbox:checked').each(function() {
            const bobotInput = $(this).closest('.form-check').find('.kriteria-bobot');
            if (!bobotInput.val()) {
                isValidKriteria = false;
                alert('Harap isi bobot untuk kriteria penilaian yang dipilih.');
                bobotInput.focus();
                return false; // Hentikan iterasi
            }
        });

        // Jika tidak valid, hentikan pengiriman form
        if (!isValidKriteria) {
            event.preventDefault();
        }

        // Jika tidak valid, hentikan pengiriman form
        if (!isValidMetode) {
            event.preventDefault();
        }
    });

    document.getElementById('search-kriteria').addEventListener('input', function() {
    let searchValue = this.value.toLowerCase();
    let container = document.getElementById('kriteria-penilaian');
    let items = document.querySelectorAll('.kriteria-item');

        items.forEach(item => { 
            let label = item.querySelector('label').innerText.toLowerCase();
            
            if (label.includes(searchValue)) {
                item.style.visibility = "visible"; // Tampilkan item yang cocok
                item.style.order - "-1";
                container.prepend(item);
            } else {
                item.style.visibility = "hidden"; // Sembunyikan item yang tidak cocok
            }
        });
    });

    document.getElementById('search-metode').addEventListener('input', function() {
    let searchValue = this.value.toLowerCase();
    let container = document.getElementById('metode-penilaian');
    let items = document.querySelectorAll('.metode-item');

        items.forEach(item => { 
            let label = item.querySelector('label').innerText.toLowerCase();
            
            if (label.includes(searchValue)) {
                item.style.visibility = "visible"; // Tampilkan item yang cocok
                item.style.order - "-1";
                container.prepend(item);
            } else {
                item.style.visibility = "hidden"; // Sembunyikan item yang tidak cocok
            }
        });
    });
    
});
  
</script>

@endsection