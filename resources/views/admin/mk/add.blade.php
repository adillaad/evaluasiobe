@extends('admin.template')
@section('content')
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">Tambah Mata Kuliah</h4>

            @php
                $user = auth()->user();
                $otoritas = $user->otoritas->otoritas;
                $allowedRoles = ['Admin Universitas', 'Penjamin Mutu Universitas'];
                $isUnivLevel = in_array($otoritas, $allowedRoles);
            @endphp

            @if($isUnivLevel)
            <ul class="nav nav-tabs mb-4" id="mkTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold" id="mk-univ-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#mk-univ" data-target="#mk-univ" type="button" role="tab">
                        MK Universitas
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="mk-prodi-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#mk-prodi" data-target="#mk-prodi" type="button" role="tab">
                        MK Reguler / Prodi
                    </button>
                </li>
            </ul>
            @endif

            <div class="tab-content" id="mkTabContent">
                @if($isUnivLevel)
                {{-- TAB 1: MK UNIVERSITAS --}}
                <div class="tab-pane fade show active" id="mk-univ" role="tabpanel">
                    <form method="POST" action="{{ route('admin-universitas.add-mk') }}">
                        @csrf
                        <input type="hidden" name="id_fakultas" value="">
                        <input type="hidden" name="id_prodi" value="">
                        <input type="hidden" name="id_kurikulum" value="">

                        <div class="alert alert-info py-2 mb-3">
                            <strong>Mata Kuliah Universitas</strong>: Tidak memerlukan Fakultas, Prodi, dan Tahun Kurikulum. MK ini berlaku untuk seluruh universitas dan kurikulumnya diatur di tingkat prodi masing-masing.
                        </div>

                        <div class="form-group">
                            <label>Kode MK <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="kode" placeholder="Contoh: MKU101" value="{{ old('kode') }}" autocomplete="off" required>
                            @error('kode') <div class="alert alert-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label>Nama MK <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama" placeholder="Contoh: Pancasila" value="{{ old('nama') }}" autocomplete="off" required>
                            @error('nama') <div class="alert alert-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label>Nama MK (English)</label>
                            <input type="text" class="form-control" name="nama_eng" placeholder="Contoh: Pancasila Education" value="{{ old('nama_eng') }}" autocomplete="off">
                            @error('nama_eng') <div class="alert alert-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label>Semester <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-3 mt-1 p-3 border rounded bg-light">
                                @php
                                    $rawOldSem = old('semester', []);
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
                            @error('semester') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label>Rumpun <span class="text-danger">*</span></label>
                            <div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="rumpun" value="MKWK" {{ old('rumpun', 'MKWK') == 'MKWK' ? 'checked' : '' }}> MKWK</label></div>
                            <div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="rumpun" value="Wajib" {{ old('rumpun') == 'Wajib' ? 'checked' : '' }}> Wajib</label></div>
                            <div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="rumpun" value="Peminatan" {{ old('rumpun') == 'Peminatan' ? 'checked' : '' }}> Peminatan</label></div>
                            @error('rumpun') <div class="alert alert-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="row">
                            <div class="col-6 form-group">
                                <label>Bobot teori (sks) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="bobot_teori" placeholder="Bobot teori" value="{{ old('bobot_teori', 2) }}" autocomplete="off" required>
                                @error('bobot_teori') <div class="alert alert-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-6 form-group">
                                <label>Bobot praktikum (sks)</label>
                                <input type="number" class="form-control" name="bobot_praktikum" placeholder="Bobot praktikum" value="{{ old('bobot_praktikum', 0) }}" autocomplete="off">
                                @error('bobot_praktikum') <div class="alert alert-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="deskripsi" placeholder="Deskripsi MK Universitas" style="height: 100px" autocomplete="off" required>{{ old('deskripsi') }}</textarea>
                            @error('deskripsi') <div class="alert alert-danger">{{ $message }}</div> @enderror
                        </div>
                        <input type="submit" class="btn btn-primary me-2" value="Simpan MK Universitas">
                    </form>
                </div>
                @endif

                {{-- TAB 2: MK REGULER / PRODI --}}
                <div class="tab-pane fade {{ !$isUnivLevel ? 'show active' : '' }}" id="mk-prodi" role="tabpanel">
                    <form method="POST" action="{{ route('admin-universitas.add-mk') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fakultas <span class="text-danger">*</span></label>
                                    <select class="form-control" name="id_fakultas" id="fakultas-select" required>
                                        <option value="" disabled selected>Pilih Fakultas...</option>
                                        @foreach($fakultas as $f)
                                        <option value="{{ $f->id }}" {{ old('id_fakultas') == $f->id ? 'selected' : '' }}>
                                            {{ $f->nama }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Program Studi <span class="text-danger">*</span></label>
                                    <select class="form-control" name="id_prodi" id="prodi-select" disabled required>
                                        <option value="" disabled selected>Pilih Fakultas Dulu...</option>
                                    </select>
                                    @error('id_prodi') <div class="alert alert-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="form-group">
                            <label>Tahun kurikulum <span class="text-danger">*</span></label>
                            <select class="form-control" name="id_kurikulum" id="kurikulum-select" disabled required>
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

                        @if(isset($mksUniv) && count($mksUniv) > 0)
                        <div class="card bg-light border-primary border-opacity-25 mb-4 p-3 rounded-3 shadow-sm">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary text-white p-2 rounded-2"><i class="ti-layers fs-6"></i></span>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">Pilih / Adopsi Mata Kuliah Universitas</h6>
                                        <small class="text-muted">Pilih dari MK Universitas yang sudah ada untuk dimasukkan ke Kurikulum Prodi.</small>
                                    </div>
                                </div>
                                <button type="button" id="btn-reset-mk-univ" class="btn btn-outline-secondary btn-sm" style="display:none;">
                                    <i class="ti-reload me-1"></i> Reset / Buat MK Baru
                                </button>
                            </div>

                            <select id="select-mk-univ-dropdown" class="form-select border-primary fw-semibold text-dark">
                                <option value="">-- Pilih MK Universitas yang Ingin Diadopsi (Atau Biarkan Kosong Untuk Membuat MK Baru) --</option>
                                @foreach($mksUniv as $uMk)
                                    <option value="{{ $uMk->kode }}" 
                                        data-kode="{{ $uMk->kode }}"
                                        data-nama="{{ $uMk->nama }}"
                                        data-nama_eng="{{ $uMk->nama_eng }}"
                                        data-rumpun="{{ $uMk->rumpun }}"
                                        data-bobot_teori="{{ $uMk->bobot_teori }}"
                                        data-bobot_praktikum="{{ $uMk->bobot_praktikum }}"
                                        data-batas_mhs="{{ $uMk->batas_kelulusan_mhs }}"
                                        data-batas_mk="{{ $uMk->batas_kelulusan_mk }}"
                                        data-deskripsi="{{ $uMk->deskripsi }}"
                                        data-semester="{{ $uMk->semester }}">
                                        [{{ $uMk->kode }}] {{ $uMk->nama }} ({{ ($uMk->bobot_teori ?? 0) + ($uMk->bobot_praktikum ?? 0) }} SKS - {{ $uMk->rumpun }})
                                    </option>
                                @endforeach
                            </select>
                            <div id="mk-univ-badge-notice" class="mt-2 text-success small fw-bold d-none">
                                <i class="ti-check-box me-1"></i> MK Universitas berhasil dipilih! Semua data MK telah terisi otomatis. Silakan tentukan <strong>Fakultas, Prodi, Kurikulum</strong>, dan <strong>Semester</strong>, lalu klik Simpan.
                            </div>
                        </div>
                        @endif

                        <div class="form-group">
                            <label>Tahun kurikulum <span class="text-danger">*</span></label>
                            <select class="form-control" name="id_kurikulum" id="kurikulum-select" disabled required>
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

                        <div class="form-group position-relative">
                            <label>Kode MK <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="kode" id="kode-mk-prodi-input" placeholder="Ketik Kode MK baru atau pilih MK Universitas..." value="{{ old('kode') }}" autocomplete="off" required>
                            <small class="text-muted">Ketik Kode MK baru langsung di kolom ini, atau pilih dari daftar <strong>Pilih / Adopsi Mata Kuliah Universitas</strong> di atas.</small>
                            @error('kode') <div class="alert alert-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label>Nama MK <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama" placeholder="Nama MK" value="{{ old('nama') }}" autocomplete="off" required>
                            @error('nama') <div class="alert alert-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label>Nama MK (English)</label>
                            <input type="text" class="form-control" name="nama_eng" value="{{ old('nama_eng') }}" placeholder="English course name">
                            @error('nama_eng') <div class="alert alert-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label>Semester <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-3 mt-1 p-3 border rounded bg-light">
                                @php
                                    $rawOldSem = old('semester', []);
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
                            @error('semester') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label>Rumpun <span class="text-danger">*</span></label>
                            <div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="rumpun" value="Wajib" {{ old('rumpun') == 'Wajib' ? 'checked' : '' }}> Wajib</label></div>
                            <div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="rumpun" value="Peminatan" {{ old('rumpun') == 'Peminatan' ? 'checked' : '' }}> Peminatan</label></div>
                            <div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="rumpun" value="MKWK" {{ old('rumpun') == 'MKWK' ? 'checked' : '' }}> MKWK</label></div>
                            @error('rumpun') <div class="alert alert-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="row">
                            <div class="col-6 form-group">
                                <label>Bobot teori (sks) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="bobot_teori" placeholder="Bobot teori" value="{{ old('bobot_teori') }}" autocomplete="off" required>
                                @error('bobot_teori') <div class="alert alert-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-6 form-group">
                                <label>Bobot praktikum (sks)</label>
                                <input type="number" class="form-control" name="bobot_praktikum" placeholder="Bobot praktikum" value="{{ old('bobot_praktikum') }}" autocomplete="off">
                                @error('bobot_praktikum') <div class="alert alert-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="deskripsi" placeholder="Deskripsi" style="height: 100px" autocomplete="off" required>{{ old('deskripsi') }}</textarea>
                            @error('deskripsi') <div class="alert alert-danger">{{ $message }}</div> @enderror
                        </div>
                        <input type="submit" class="btn btn-primary me-2" value="Simpan MK Prodi">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    $('#mkTab button, #mkTab a').on('click', function (e) {
        e.preventDefault();
        const target = $(this).attr('data-bs-target') || $(this).attr('data-target');
        $('#mkTab button, #mkTab a').removeClass('active');
        $(this).addClass('active');
        $('.tab-pane').removeClass('show active');
        $(target).addClass('show active');
    });

    const fakultasSelect = $('#fakultas-select');
    const prodiSelect = $('#prodi-select');
    const kurikulumSelect = $('#kurikulum-select');
    const prasyaratSelect = $('#prasyarat-select');

    fakultasSelect.on('change', function() {
        const fakultasId = $(this).val();
        prodiSelect.html('<option value="" disabled selected>Pilih Fakultas Dulu...</option>').prop('disabled', true);
        kurikulumSelect.html('<option value="" disabled selected>Pilih Prodi Dulu...</option>').prop('disabled', true);
        prasyaratSelect.html('<option value="" selected>Tidak ada</option>').prop('disabled', true);

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
        kurikulumSelect.html('<option value="" disabled selected>Loading...</option>').prop('disabled', false);
        prasyaratSelect.html('<option value="" selected>Tidak ada</option><option value="" disabled>Loading...</option>').prop('disabled', false);

        if (prodiId) {
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

            $.ajax({
                url: `/admin-universitas/get-mk-prasyarat/${prodiId}`,
                success: function(data) {
                    let options = '<option value="" selected>Tidak ada</option>';
                    data.forEach(function(mk) {
                        options += `<option value="${mk.nama}">${mk.nama}</option>`;
                    });
                    prasyaratSelect.html(options);
                }
            });
        }
    });

    $('#select-mk-univ-dropdown').on('change', function() {
        const selectedOpt = $(this).find('option:selected');
        const kode = selectedOpt.data('kode');

        if (kode) {
            $('#kode-mk-prodi-input').val(kode);
            $('#mk-prodi input[name="nama"]').val(selectedOpt.data('nama') || '');
            $('#mk-prodi input[name="nama_eng"]').val(selectedOpt.data('nama_eng') || selectedOpt.data('nama') || '');
            
            const rumpun = selectedOpt.data('rumpun');
            if (rumpun) {
                $(`#mk-prodi input[name="rumpun"][value="${rumpun}"]`).prop('checked', true);
            }

            const bt = selectedOpt.data('bobot_teori');
            if (bt !== undefined && bt !== null) $('#mk-prodi input[name="bobot_teori"]').val(bt);

            const bp = selectedOpt.data('bobot_praktikum');
            if (bp !== undefined && bp !== null) $('#mk-prodi input[name="bobot_praktikum"]').val(bp);

            const bm = selectedOpt.data('batas_mhs');
            if (bm !== undefined && bm !== null) $('#mk-prodi input[name="batas_kelulusan_mhs"]').val(bm);

            const bmk = selectedOpt.data('batas_mk');
            if (bmk !== undefined && bmk !== null) $('#mk-prodi input[name="batas_kelulusan_mk"]').val(bmk);

            const desk = selectedOpt.data('deskripsi');
            if (desk) $('#mk-prodi textarea[name="deskripsi"]').val(desk);

            const semStr = (selectedOpt.data('semester') || '').toString();
            if (semStr) {
                const semArr = semStr.split(',').map(s => s.trim());
                $('#mk-prodi input[name="semester[]"]').each(function() {
                    $(this).prop('checked', semArr.includes($(this).val()));
                });
            }

            $('#mk-univ-badge-notice').removeClass('d-none');
            $('#btn-reset-mk-univ').show();
        } else {
            resetMkUnivSelection();
        }
    });

    $('#btn-reset-mk-univ').on('click', function() {
        $('#select-mk-univ-dropdown').val('').trigger('change');
    });

    function resetMkUnivSelection() {
        $('#kode-mk-prodi-input').val('');
        $('#mk-prodi input[name="nama"]').val('');
        $('#mk-prodi input[name="nama_eng"]').val('');
        $('#mk-prodi input[name="rumpun"]').prop('checked', false);
        $('#mk-prodi input[name="bobot_teori"]').val('');
        $('#mk-prodi input[name="bobot_praktikum"]').val('');
        $('#mk-prodi input[name="batas_kelulusan_mhs"]').val('');
        $('#mk-prodi input[name="batas_kelulusan_mk"]').val('');
        $('#mk-prodi textarea[name="deskripsi"]').val('');
        $('#mk-prodi input[name="semester[]"]').prop('checked', false);
        $('#mk-univ-badge-notice').addClass('d-none');
        $('#btn-reset-mk-univ').hide();
    }
});
</script>
@endsection