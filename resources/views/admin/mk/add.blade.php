@extends('admin.template')
@section('content')
<div class="col-12 grild-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Tambah Mata Kuliah</h4>
            <form method="POST" action="{{ route('admin-universitas.add-mk') }}" enctype="multipart/form-data">
                @csrf

                {{-- Otoritas Check --}}
                @php
                    $user = auth()->user();
                    $otoritas = $user->otoritas->otoritas;
                    $allowedRoles = ['Admin Universitas', 'Penjamin Mutu Universitas'];
                    $isSelectionDisabled = !in_array($otoritas, $allowedRoles);
                @endphp

                {{-- Dropdown Fakultas & Prodi --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fakultas <span class="text-danger">*</span></label>
                            <select class="form-control" name="id_fakultas" id="fakultas-select" {{ $isSelectionDisabled ? 'disabled' : '' }}>
                                <option value="" disabled selected>Pilih Fakultas...</option>
                                @foreach($fakultas as $f)
                                <option value="{{ $f->id }}" {{ old('id_fakultas', $isSelectionDisabled ? $user->id_fakultasUser : '') == $f->id ? 'selected' : '' }}>
                                    {{ $f->nama }}
                                </option>
                                @endforeach
                            </select>
                            @if($isSelectionDisabled)
                                <input type="hidden" name="id_fakultas" value="{{ $user->id_fakultasUser }}">
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Program Studi <span class="text-danger">*</span></label>
                            <select class="form-control" name="id_prodi" id="prodi-select" disabled>
                                <option value="" disabled selected>Pilih Fakultas Dulu...</option>
                            </select>
                            @error('id_prodi') <div class="alert alert-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <hr>

                {{-- Form Input Mata Kuliah --}}
                <div class="form-group">
                    <label>Tahun kurikulum <span class="text-danger">*</span></label>
                    <select class="form-control" name="id_kurikulum" id="kurikulum-select" disabled>
                        <option value="" disabled selected>Pilih Prodi Dulu...</option>
                    </select>
                    @error('id_kurikulum') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>MK prasyarat</label>
                    <select class="form-control" name="prasyarat" id="prasyarat-select" disabled>
                        <option value="" disabled selected>Pilih Prodi Dulu...</option>
                    </select>
                    @error('prasyarat') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>

                {{-- Sisa Form (Kode, Nama, dll) --}}
                <div class="form-group">
                    <label>Kode MK <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="kode" placeholder="Kode MK" value="{{ old('kode') }}" autocomplete="off">
                    @error('kode') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Nama MK <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama" placeholder="Nama MK" value="{{ old('nama') }}" autocomplete="off">
                    @error('nama') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Semester <span class="text-danger">*</span></label>
                    <select class="form-control" name="semester">
                        <option value="" disabled selected>Pilih Semester...</option>
                        @for ($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                    @error('semester') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Rumpun <span class="text-danger">*</span></label>
                    <div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="rumpun" value="Wajib" {{ old('rumpun') == 'Wajib' ? 'checked' : '' }}> Wajib</label></div>
                    <div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="rumpun" value="Peminatan" {{ old('rumpun') == 'Peminatan' ? 'checked' : '' }}> Peminatan</label></div>
                    @error('rumpun') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <div class="row">
                    <div class="col-6 form-group">
                        <label>Bobot teori (sks) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="bobot_teori" placeholder="Bobot teori" value="{{ old('bobot_teori') }}" autocomplete="off">
                        @error('bobot_teori') <div class="alert alert-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6 form-group">
                        <label>Bobot praktikum (sks)</label>
                        <input type="text" class="form-control" name="bobot_praktikum" placeholder="Bobot praktikum" value="{{ old('bobot_praktikum') }}" autocomplete="off">
                        @error('bobot_praktikum') <div class="alert alert-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label>Deskripsi <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="deskripsi" placeholder="Deskripsi" style="height: 100px" autocomplete="off">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <input type="submit" class="btn btn-primary me-2" value="Submit">
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    const fakultasSelect = $('#fakultas-select');
    const prodiSelect = $('#prodi-select');
    const kurikulumSelect = $('#kurikulum-select');
    const prasyaratSelect = $('#prasyarat-select');

    function resetProdi() {
        prodiSelect.html('<option value="" disabled selected>Pilih Fakultas Dulu...</option>').prop('disabled', true);
        resetKurikulumAndPrasyarat();
    }

    function resetKurikulumAndPrasyarat() {
        kurikulumSelect.html('<option value="" disabled selected>Pilih Prodi Dulu...</option>').prop('disabled', true);
        prasyaratSelect.html('<option value="" selected>Tidak ada</option><option value="" disabled>Pilih Prodi Dulu...</option>').prop('disabled', true);
    }

    fakultasSelect.on('change', function() {
        const fakultasId = $(this).val();
        resetProdi();

        if (fakultasId) {
            prodiSelect.html('<option value="" disabled selected>Loading...</option>').prop('disabled', false);
            $.ajax({
                url: `/admin-universitas/get-prodi/${fakultasId}`,
                success: function(data) {
                    let options = '<option value="" disabled selected>Pilih Prodi...</option>';
                    data.forEach(function(prodi) {
                        options += `<option value="${prodi.id}">${prodi.nama}</option>`;
                    });
                    prodiSelect.html(options);
                }
            });
        }
    });

    prodiSelect.on('change', function() {
        const prodiId = $(this).val();
        resetKurikulumAndPrasyarat();

        if (prodiId) {
            kurikulumSelect.html('<option value="" disabled selected>Loading...</option>').prop('disabled', false);
            prasyaratSelect.html('<option value="" selected>Tidak ada</option><option value="" disabled>Loading...</option>').prop('disabled', false);

            // Fetch Kurikulum
            $.ajax({
                url: `/admin-universitas/get-kurikulum/${prodiId}`,
                success: function(data) {
                    let options = '<option value="" disabled selected>Pilih Kurikulum...</option>';
                    data.forEach(function(kurikulum) {
                        options += `<option value="${kurikulum.id}">${kurikulum.tahun}</option>`;
                    });
                    kurikulumSelect.html(options);
                }
            });

            // Fetch MK Prasyarat
            $.ajax({
                url: `/admin-universitas/get-mk-prasyarat/${prodiId}`,
                success: function(data) {
                    let options = '<option value="" selected>Tidak ada</option>'; // Opsi default
                    data.forEach(function(mk) {
                        options += `<option value="${mk.nama}">${mk.nama}</option>`;
                    });
                    prasyaratSelect.html(options);
                }
            });
        }
    });
    
    // Trigger change on page load if fakultas is pre-selected (for non-admin roles or validation fail)
    if (fakultasSelect.val()) {
        fakultasSelect.trigger('change');
        
        // Wait for prodi to load, then select old prodi if available
        setTimeout(() => {
            const oldProdi = "{{ old('id_prodi') }}";
            if(oldProdi) {
                prodiSelect.val(oldProdi);
                prodiSelect.trigger('change');

                // Wait for kurikulum/prasyarat to load, then select old values
                setTimeout(() => {
                    const oldKurikulum = "{{ old('id_kurikulum') }}";
                    const oldPrasyarat = "{{ old('prasyarat') }}";
                    if(oldKurikulum) kurikulumSelect.val(oldKurikulum);
                    if(oldPrasyarat) prasyaratSelect.val(oldPrasyarat);
                }, 500);
            }
        }, 500);
    }
});
</script>
@endsection