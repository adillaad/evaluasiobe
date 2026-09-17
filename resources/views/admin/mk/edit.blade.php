@extends('admin.template')
@section('content')
<div class="stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="card-title m-0">Edit Mata Kuliah</h4>
                @if(is_null($mk->id_prodi))
                    <span class="badge bg-primary px-3 py-2 text-white">MK Universitas</span>
                @else
                    <span class="badge bg-info px-3 py-2 text-white">MK Reguler / Prodi</span>
                @endif
            </div>

            <form method="POST" action="{{ route('admin-universitas.update-mk', ['kode' => $mk->kode]) }}">
                @csrf
                @method('put')

                @php
                    $user = auth()->user();
                    $otoritas = $user->otoritas->otoritas;
                    $allowedRoles = ['Admin Universitas', 'Penjamin Mutu Universitas'];
                    $isSelectionDisabled = !in_array($otoritas, $allowedRoles);
                    $isUnivMk = is_null(old('id_prodi', $mk->id_prodi));
                @endphp

                <div class="form-group mb-4 p-3 bg-light border rounded">
                    <label class="font-weight-bold d-block">Tipe Mata Kuliah:</label>
                    <div class="d-flex flex-wrap gap-4 mt-2">
                        <div class="form-check me-4 mb-0">
                            <label class="form-check-label font-weight-bold" style="cursor: pointer;">
                                <input type="radio" class="form-check-input" name="mk_type_toggle" id="type-univ" value="univ" {{ $isUnivMk ? 'checked' : '' }}>
                                MK Universitas
                            </label>
                        </div>
                        <div class="form-check mb-0">
                            <label class="form-check-label font-weight-bold" style="cursor: pointer;">
                                <input type="radio" class="form-check-input" name="mk_type_toggle" id="type-prodi" value="prodi" {{ !$isUnivMk ? 'checked' : '' }}>
                                MK Reguler / Prodi
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Section Opsi Prodi & Kurikulum (Hanya tampil jika Tipe MK Reguler/Prodi) --}}
                <div id="prodi-kurikulum-section" style="{{ $isUnivMk ? 'display: none;' : '' }}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fakultas <span class="text-danger">*</span></label>
                                <select class="form-control" name="id_fakultas" id="fakultas-select" {{ $isSelectionDisabled ? 'disabled' : '' }}>
                                    <option value="" {{ !$selectedFakultas ? 'selected' : '' }}>-- Pilih Fakultas --</option>
                                    @foreach($allFakultas as $fakultas)
                                    <option value="{{ $fakultas->id }}" {{ $selectedFakultas && $fakultas->id == $selectedFakultas->id ? 'selected' : '' }}>{{ $fakultas->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Program Studi <span class="text-danger">*</span></label>
                                <select class="form-control" name="id_prodi" id="prodi-select">
                                    <option value="" {{ !$selectedProdi ? 'selected' : '' }}>-- Pilih Program Studi --</option>
                                    @foreach($allProdi as $prodi)
                                    <option value="{{ $prodi->id }}" {{ $selectedProdi && $prodi->id == $selectedProdi->id ? 'selected' : '' }}>{{ $prodi->nama }}</option>
                                    @endforeach
                                </select>
                                @error('id_prodi') <div class="alert alert-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tahun Kurikulum <span class="text-danger">*</span></label>
                        <select class="form-control" name="id_kurikulum" id="kurikulum-select">
                            <option value="">-- Pilih Kurikulum --</option>
                            @foreach($allKurikulum as $kurikulum)
                            <option value="{{ $kurikulum->id }}" {{ $selectedKurikulum && $kurikulum->id == $selectedKurikulum->id ? 'selected' : '' }}>{{ $kurikulum->tahun }}</option>
                            @endforeach
                        </select>
                        @error('id_kurikulum') <div class="alert alert-danger">{{ $message }}</div> @enderror
                    </div>
                    <hr>
                </div>

                {{-- Form Detail MK --}}
                <div class="form-group">
                    <label>MK Prasyarat</label>
                    <select class="form-control" name="prasyarat" id="prasyarat-select">
                        <option value="">Tidak ada</option>
                        @foreach($allMkPrasyarat as $prasyarat)
                        <option value="{{ $prasyarat->nama }}" {{ $prasyarat->nama == $mk->prasyarat ? 'selected' : '' }}>{{ $prasyarat->nama }} ({{$prasyarat->kode}})</option>
                        @endforeach
                    </select>
                    @error('prasyarat') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>

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
                    <div class="d-flex flex-wrap gap-3 mt-1 p-3 border rounded bg-light">
                        @php
                            $rawOldSem = old('semester', $mk->semester);
                            $selectedSemesters = is_array($rawOldSem) ? array_map('strval', $rawOldSem) : array_map('trim', explode(',', (string)$rawOldSem));
                        @endphp
                        @for ($i = 1; $i <= 8; $i++)
                            <div class="form-check me-3 mb-1">
                                <label class="form-check-label font-weight-normal" style="cursor: pointer;">
                                    <input type="checkbox" class="form-check-input" name="semester[]" value="{{ $i }}" {{ in_array((string)$i, $selectedSemesters) ? 'checked' : '' }}>
                                    Semester {{ $i }}
                                </label>
                            </div>
                        @endfor
                    </div>
                    <small class="form-text text-muted">Centang satu atau lebih semester jika mata kuliah dapat diambil di beberapa semester.</small>
                    @error('semester') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Rumpun <span class="text-danger">*</span></label>
                    <div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="rumpun" value="Wajib" {{ old('rumpun', $mk->rumpun) == 'Wajib' ? 'checked' : '' }}> Wajib</label></div>
                    <div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="rumpun" value="Peminatan" {{ old('rumpun', $mk->rumpun) == 'Peminatan' ? 'checked' : '' }}> Peminatan</label></div>
                    <div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="rumpun" value="MKWK" {{ old('rumpun', $mk->rumpun) == 'MKWK' ? 'checked' : '' }}> MKWK</label></div>
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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    const fakultasSelect = $('#fakultas-select');
    const prodiSelect = $('#prodi-select');
    const kurikulumSelect = $('#kurikulum-select');
    const prasyaratSelect = $('#prasyarat-select');
    const sectionProdiKurikulum = $('#prodi-kurikulum-section');
    const currentMkKode = "{{ $mk->kode }}";

    function toggleType(type) {
        if (type === 'univ') {
            sectionProdiKurikulum.slideUp(200);
            fakultasSelect.val('').prop('required', false);
            prodiSelect.val('').prop('required', false);
            kurikulumSelect.val('').prop('required', false);
        } else {
            sectionProdiKurikulum.slideDown(200);
            prodiSelect.prop('required', true);
            kurikulumSelect.prop('required', true);
        }
    }

    $('input[name="mk_type_toggle"]').on('change', function() {
        toggleType($(this).val());
    });

    fakultasSelect.on('change', function() {
        const fakultasId = $(this).val();
        if (fakultasId) {
            prodiSelect.html('<option value="" disabled selected>Memuat...</option>');
            $.ajax({
                url: `/admin-universitas/get-prodi/${fakultasId}`,
                success: function(data) {
                    let options = '<option value="">-- Pilih Program Studi --</option>';
                    data.forEach(function(prodi) {
                        options += `<option value="${prodi.id}">${prodi.nama}</option>`;
                    });
                    prodiSelect.html(options);
                }
            });
        } else {
            prodiSelect.html('<option value="">-- Pilih Program Studi --</option>');
        }
    });

    prodiSelect.on('change', function() {
        const prodiId = $(this).val();
        if (prodiId) {
            kurikulumSelect.html('<option value="" disabled selected>Memuat...</option>');
            $.ajax({
                url: `/admin-universitas/get-kurikulum/${prodiId}`,
                success: function(data) {
                    let options = '<option value="">-- Pilih Kurikulum --</option>';
                    data.forEach(function(kurikulum) {
                        options += `<option value="${kurikulum.id}">${kurikulum.tahun}</option>`;
                    });
                    kurikulumSelect.html(options);
                }
            });
        }
    });
});
</script>
@endsection