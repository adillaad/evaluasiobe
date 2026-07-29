{{-- @php
    $routePrefix = [
        'Admin Universitas' => ['prefix' => 'admin-universitas.'],
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'admin.';
@endphp --}}
@extends($userOtoritas == 'Admin Universitas' ? 'admin.template' : 'penjamin-mutu.template')
@section('content')

<div class="col-lg-12 grid-margin stretch-card mb-4">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Tambah CPMK</h4>
            <form action="{{ url()->current() }}" method="post">
                @csrf
                <div class="form-group">
                    <div class="form-group">
                        <label for="kurikulum_id">Kurikulum :</label>
                        <select name="kurikulum_id" id="kurikulum_id" class="form-control">
                            <option value="">-- Pilih Kurikulum --</option>
                            @foreach ($kurikulums as $kurikulum)
                                <option value="{{ $kurikulum->id }}" {{ old('kurikulum_id') == $kurikulum->id ? 'selected' : '' }}>{{ $kurikulum->tahun }} - {{ $kurikulum->prodi->nama }} @if ($userOtoritas === 'Admin Universitas') - {{ $kurikulum->prodi->fakultas->nama }}@endif</option>    
                            @endforeach
                        </select>
                        @error('kurikulum_id')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="cpl">Pilih CPL</label>
                        <select id="cplSelect" name="cpl" class="form-control" required disabled>
                            <option value="">-- Pilih CPL --</option>
                        </select>
    
                    </div>
                    @error('cpl')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div id="dynamicAddRemove">
                    {{--
                        Logika ini akan menghandle 2 kondisi:
                        1. Saat form pertama kali dibuka, old('judul', ['']) akan menghasilkan array dengan satu string kosong.
                        2. Saat validasi gagal, old('judul') akan berisi input sebelumnya.
                        Looping ini akan selalu menampilkan field yang benar.
                    --}}
                    @foreach (old('judul', ['']) as $key => $value)
                    <div class="form-group row clone-row">
                        <div class="col-10">
                            @if ($loop->first)
                                <label class="form-label">Judul Rincian CPMK<span class="text-danger">*</span></label>
                            @endif
                            <input type="text" name="judul[]" class="form-control" placeholder="Tuliskan judul rincian CPMK" value="{{ $value }}" autocomplete="off">
                            @error('judul.' . $key)
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-2">
                            @if ($loop->first)
                                <label>Aksi</label>
                                <div class="form-group">
                                    <button type="button" name="add" id="dynamic-ar" class="btn btn-sm btn-primary">Tambah</button>
                                </div>
                            @else
                                <div class="form-group" style="margin-top: 29px;">
                                    <button type="button" class="btn btn-sm btn-danger remove-input-field">Hapus</button>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-primary mt-3">Simpan CPMK</button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script type="text/javascript">
    $('#kurikulum_id').on('change', function() {
        var kurikulumID = $(this).val();
        const otoritas = "{{ $userOtoritas }}";
        if(otoritas === "Kepala Program Studi"){
            urlget = `/kepala-program-studi/get-cpl-by-kurikulum/${kurikulumID}`;
        } else if (otoritas === 'Penjamin Mutu Program Studi'){
            urlget = `/penjamin-mutu/program-studi/get-cpl-by-kurikulum/${kurikulumID}`;
        } else if (otoritas === 'Admin Universitas'){
            urlget = `/admin-universitas/get-cpl-by-kurikulum/${kurikulumID}`;
        }
        var cplSelect = document.getElementById('cplSelect'); 
        if (kurikulumID) {
            cplSelect.innerHTML = '<option value="">Loading...</option>';
            cplSelect.disabled = true ;
            $.ajax({
                url: urlget,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.length > 0) {
                        cplSelect.innerHTML = '<option value="">-- Pilih CPL --</option>';
                        data.forEach(cpl => {
                            const option = document.createElement('option');
                            const maxLength = 100;
                            let judul = cpl.judul;
                            if (judul.length > maxLength) {
                            judul = judul.substring(0, maxLength) + '...';
                            }
                            option.value = cpl.id;
                            option.textContent = `${cpl.kode} - ${judul}`;
                            cplSelect.appendChild(option);
                            // $('#cpl').append('<option value="'+ cpl.id +'">'+ cpl.kode +' - '+ cpl.judul +'</option>');
                    });
                    cplSelect.disabled = false;
                    } else {
                        cplSelect.innerHTML = '<option value="">-- Tidak ada data CPL untuk Kurikulum yang dipilih --</option>';
                    }
                    
                }
            });
        } else {
            console.log("test");
            
            cplSelect.innerHTML = '<option value="">-- Tidak ada data Kurikulum yang dipilih --</option>';
        }
    });

    $("#dynamic-ar").click(function() {
        // Menambahkan baris input baru
        var newRow = `
            <div class="form-group row clone-row">
                <div class="col-10">
                    <input type="text" name="judul[]" class="form-control" placeholder="Tuliskan judul rincian CPMK" autocomplete="off">
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <button type="button" class="btn btn-sm btn-danger remove-input-field">Hapus</button>
                    </div>
                </div>
            </div>`;
        $("#dynamicAddRemove").append(newRow);
    });

    // Fungsi untuk menghapus baris
    $(document).on('click', '.remove-input-field', function() {
        $(this).closest('.clone-row').remove();
    });
</script>
@endsection