@extends('admin.template')
@section('content')
<div class="stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Edit Mata Kuliah</h4>
            <form method="POST" action="{{ route('admin-universitas.update-mk', ['kode' => $mk->kode]) }}">
                @csrf
                @method('put')

                {{-- Pengecekan Otoritas & Dropdown Fakultas, Prodi --}}
                @php
                    $user = auth()->user();
                    $otoritas = $user->otoritas->otoritas;
                    $allowedRoles = ['Admin Universitas', 'Penjamin Mutu Universitas'];
                    $isSelectionDisabled = !in_array($otoritas, $allowedRoles);
                @endphp
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fakultas <span class="text-danger">*</span></label>
                            <select class="form-control" name="id_fakultas" id="fakultas-select" {{ $isSelectionDisabled ? 'disabled' : '' }}>
                                @foreach($allFakultas as $fakultas)
                                <option value="{{ $fakultas->id }}" {{ $fakultas->id == $selectedFakultas->id ? 'selected' : '' }}>{{ $fakultas->nama }}</option>
                                @endforeach
                            </select>
                            @if($isSelectionDisabled)
                                <input type="hidden" name="id_fakultas" value="{{ $selectedFakultas->id }}">
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Program Studi <span class="text-danger">*</span></label>
                            <select class="form-control" name="id_prodi" id="prodi-select">
                                {{-- Opsi prodi akan diisi oleh Javascript --}}
                                @foreach($allProdi as $prodi)
                                <option value="{{ $prodi->id }}" {{ $prodi->id == $selectedProdi->id ? 'selected' : '' }}>{{ $prodi->nama }}</option>
                                @endforeach
                            </select>
                             @error('id_prodi') <div class="alert alert-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <hr>

                {{-- Form Edit Utama MK --}}
                 <div class="form-group">
                    <label>Tahun Kurikulum <span class="text-danger">*</span></label>
                    <select class="form-control" name="id_kurikulum" id="kurikulum-select">
                         {{-- Opsi kurikulum akan diisi oleh Javascript --}}
                        @foreach($allKurikulum as $kurikulum)
                        <option value="{{ $kurikulum->id }}" {{ $kurikulum->id == $selectedKurikulum->id ? 'selected' : '' }}>{{ $kurikulum->tahun }}</option>
                        @endforeach
                    </select>
                    @error('id_kurikulum') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>MK Prasyarat</label>
                    <select class="form-control" name="prasyarat" id="prasyarat-select">
                         {{-- Opsi prasyarat akan diisi oleh Javascript --}}
                         <option value="">Tidak ada</option>
                        @foreach($allMkPrasyarat as $prasyarat)
                        <option value="{{ $prasyarat->nama }}" {{ $prasyarat->nama == $mk->prasyarat ? 'selected' : '' }}>{{ $prasyarat->nama }} ({{$prasyarat->kode}})</option>
                        @endforeach
                    </select>
                    @error('prasyarat') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>

                {{-- Input Fields untuk Detail MK --}}
                <div class="form-group">
                    <label>Kode MK <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="kode" placeholder="Kode MK" value="{{ old('kode', $mk->kode) }}" autocomplete="off">
                    @error('kode') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Nama MK <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama" placeholder="Nama MK" value="{{ old('nama', $mk->nama) }}" autocomplete="off">
                    @error('nama') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Semester <span class="text-danger">*</span></label>
                    <select class="form-control" name="semester">
                        <option value="" disabled>Pilih Semester...</option>
                        @for ($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ old('semester', $mk->semester) == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                    @error('semester') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Rumpun <span class="text-danger">*</span></label>
                    <div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="rumpun" value="Wajib" {{ old('rumpun', $mk->rumpun) == 'Wajib' ? 'checked' : '' }}> Wajib</label></div>
                    <div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="rumpun" value="Peminatan" {{ old('rumpun', $mk->rumpun) == 'Peminatan' ? 'checked' : '' }}> Peminatan</label></div>
                    @error('rumpun') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <div class="row">
                    <div class="col-6 form-group">
                        <label>Bobot teori (sks) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="bobot_teori" placeholder="Bobot teori" value="{{ old('bobot_teori', $mk->bobot_teori) }}" autocomplete="off">
                        @error('bobot_teori') <div class="alert alert-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6 form-group">
                        <label>Bobot praktikum (sks)</label>
                        <input type="number" class="form-control" name="bobot_praktikum" placeholder="Bobot praktikum" value="{{ old('bobot_praktikum', $mk->bobot_praktikum) }}" autocomplete="off">
                        @error('bobot_praktikum') <div class="alert alert-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label>Deskripsi <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="deskripsi" placeholder="Deskripsi" style="height: 100px" autocomplete="off">{{ old('deskripsi', $mk->deskripsi) }}</textarea>
                    @error('deskripsi') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                
                {{-- Tombol Aksi --}}
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    <a href="{{ route('admin-universitas.list-mk') }}" class="btn btn-light">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Tambahkan jQuery jika belum ada di template utama --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    const fakultasSelect = $('#fakultas-select');
    const prodiSelect = $('#prodi-select');
    const kurikulumSelect = $('#kurikulum-select');
    const prasyaratSelect = $('#prasyarat-select');
    const currentMkKode = "{{ $mk->kode }}";

    function resetProdi() {
        prodiSelect.html('<option value="" disabled selected>Pilih Fakultas Dulu...</option>').prop('disabled', true);
        resetKurikulumAndPrasyarat();
    }

    function resetKurikulumAndPrasyarat() {
        kurikulumSelect.html('<option value="" disabled selected>Pilih Prodi Dulu...</option>').prop('disabled', true);
        prasyaratSelect.html('<option value="">Tidak ada</option>').prop('disabled', true);
    }

    fakultasSelect.on('change', function() {
        const fakultasId = $(this).val();
        resetProdi();

        if (fakultasId) {
            prodiSelect.html('<option value="" disabled selected>Memuat...</option>').prop('disabled', false);
            $.ajax({
                url: `/admin-universitas/get-prodi/${fakultasId}`, // Pastikan route ini benar
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
            kurikulumSelect.html('<option value="" disabled selected>Memuat...</option>').prop('disabled', false);
            prasyaratSelect.html('<option value="" disabled selected>Memuat...</option>').prop('disabled', false);

            // Fetch Kurikulum
            $.ajax({
                url: `/admin-universitas/get-kurikulum/${prodiId}`, // Pastikan route ini benar
                success: function(data) {
                    let options;
                    if (data.length > 0) {
                        options = '<option value="" disabled selected>Pilih Kurikulum...</option>';
                         data.forEach(function(kurikulum) {
                            options += `<option value="${kurikulum.id}">${kurikulum.tahun}</option>`;
                        });
                    } else {
                        options = '<option value="" disabled selected>Kurikulum tidak ditemukan</option>';
                    }
                    kurikulumSelect.html(options);
                }
            });

            // Fetch MK Prasyarat
            $.ajax({
                url: `/admin-universitas/get-mk-prasyarat/${prodiId}`, // Pastikan route ini benar
                success: function(data) {
                    let options = '<option value="">Tidak ada</option>';
                    data.forEach(function(mk) {
                         if(mk.kode !== currentMkKode){ // Jangan tampilkan MK itu sendiri sebagai prasyarat
                            options += `<option value="${mk.nama}">${mk.nama} (${mk.kode})</option>`;
                         }
                    });
                    prasyaratSelect.html(options);
                }
            });
        }
    });
});
</script>
@endsection