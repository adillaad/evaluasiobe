@extends('admin.template')
@section('content')
<div class="stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Edit CPL Program Studi</h4>
            <form method="POST" action="{{ route('admin-universitas.update-cpl', ['id' => encrypt($cpl->id)]) }}">
                @csrf
                @method('put')
                {{-- DROPDOWN STATIS UNTUK FAKULTAS DAN PRODI --}}
                @php
                    $user = auth()->user();
                    $otoritas = $user->otoritas->otoritas;
                    $allowedRoles = ['Admin Universitas', 'Penjamin Mutu Universitas'];
                    $isFakultasDisabled = !in_array($otoritas, $allowedRoles);
                @endphp
                <div class="row">
                    <div class="col-md-6"><div class="form-group">
                        <label>Fakultas <span class="text-danger">*</span></label>
                        <select class="form-control" name="id_fakultas" id="fakultas-select" {{ $isFakultasDisabled ? 'disabled' : '' }}>
                            @foreach($allFakultas as $fakultas)
                            <option value="{{ $fakultas->id }}" {{ $fakultas->id == $selectedFakultas->id ? 'selected' : '' }}>{{ $fakultas->nama }}</option>
                            @endforeach
                        </select>
                        @if($isFakultasDisabled)<input type="hidden" name="id_fakultas" value="{{ $user->id_fakultasUser }}">@endif
                    </div></div>
                    <div class="col-md-6"><div class="form-group">
                        <label>Program Studi <span class="text-danger">*</span></label>
                        <select class="form-control" name="id_prodi" id="prodi-select">
                            @foreach($allProdi as $prodi)
                            <option value="{{ $prodi->id }}" {{ $prodi->id == $selectedProdi->id ? 'selected' : '' }}>{{ $prodi->nama }}</option>
                            @endforeach
                        </select>
                    </div></div>
                </div>
                <hr>
                {{-- FORM EDIT CPL UTAMA --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tahun Kurikulum <span class="text-danger">*</span></label>
                            <select class="form-control" id="kurikulum-select" name="id_kurikulum">
                                @foreach($allKurikulum as $kurikulum)
                                <option value="{{ $kurikulum->id }}" {{ $kurikulum->id == $selectedKurikulum->id ? 'selected' : '' }}>{{ $kurikulum->tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Aspek <span class="text-danger">*</span></label>
                            <select class="form-control" name="aspek">
                                <option value="Sikap" {{ $cpl->aspek == "Sikap" ? 'selected' : '' }}>Sikap</option>
                                <option value="Keterampilan Umum" {{ $cpl->aspek == "Keterampilan Umum" ? 'selected' : '' }}>Keterampilan Umum</option>
                                <option value="Keterampilan Khusus" {{ $cpl->aspek == "Keterampilan Khusus" ? 'selected' : '' }}>Keterampilan Khusus</option>
                                <option value="Pengetahuan" {{ $cpl->aspek == "Pengetahuan" ? 'selected' : '' }}>Pengetahuan</option>
                                <option value="Lainnya" {{ $cpl->aspek == "Lainnya" ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kode <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">CPL</span></div>
                                <input type="text" class="form-control" name="nomor" placeholder="Nomor" value="{{$cpl->nomor}}" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Judul <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="judul" placeholder="Judul" value="{{$cpl->judul}}" autocomplete="off">
                </div>
                {{-- <button type="submit" class="btn btn-primary me-2">Update</button> --}}
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    <a href="{{ route('admin-universitas.list-cpl') }}" class="btn btn-light">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // JavaScript untuk dropdown berantai di halaman edit tidak berubah
        const fakultasSelect = $('#fakultas-select');
        const prodiSelect = $('#prodi-select');
        const kurikulumSelect = $('#kurikulum-select');

        fakultasSelect.on('change', function() {
            const fakultasId = $(this).val();
            prodiSelect.html('<option value="" disabled selected>Loading...</option>').prop('disabled', true);
            kurikulumSelect.html('<option value="" disabled selected>Pilih Prodi Dahulu</option>').prop('disabled', true);

            if (fakultasId) {
                $.ajax({
                    url: `/admin-universitas/get-prodi/${fakultasId}`,
                    type: 'GET',
                    success: function(data) {
                        prodiSelect.html('<option value="" disabled selected>Pilih Prodi...</option>').prop('disabled', false);
                        $.each(data, function(key, value) {
                            prodiSelect.append(`<option value="${value.id}">${value.nama}</option>`);
                        });
                    }
                });
            }
        });

        prodiSelect.on('change', function(){
            const prodiId = $(this).val();
            kurikulumSelect.html('<option value="" disabled selected>Loading...</option>').prop('disabled', true);
            
            if (prodiId) {
                $.ajax({
                    url: `/admin-universitas/get-kurikulum/${prodiId}`,
                    type: 'GET',
                    success: function(data) {
                        kurikulumSelect.html('<option value="" disabled selected>Pilih Kurikulum...</option>').prop('disabled', false);
                        if(data.length > 0) {
                            $.each(data, function(key, value) {
                                kurikulumSelect.append(`<option value="${value.id}">${value.tahun}</option>`);
                            });
                        } else {
                            kurikulumSelect.html('<option value="" disabled selected>Kurikulum tidak ditemukan</option>');
                        }
                    }
                });
            }
        });
    });
</script>
@endsection