{{-- @php
    $routePrefix = [
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
        'Penjamin Mutu Universitas' => ['prefix' => 'penjamin-mutu.universitas.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp --}}

@extends('penjamin-mutu.template')
@section('content')
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">Tambah Mata Kuliah</h4>

            @php
                $user = auth()->user();
                $otoritas = $user->otoritas->otoritas;
                $allowedRoles = ['Admin Universitas', 'Penjamin Mutu Universitas', 'Wakil Rektor'];
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
                    <form method="POST" action="{{ route($currentPrefix . 'mk.store') }}" enctype="multipart/form-data">
                        @csrf
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
                            <label>Nama MK (English) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_eng" value="{{ old('nama_eng') }}" placeholder="English course name" required>
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
                        <div class="row">
                            <div class="col-6 form-group">
                                <label>Ambang Kelulusan Mahasiswa <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" max="100" class="form-control" name="batas_kelulusan_mhs" placeholder="Contoh : 50" value="{{ old('batas_kelulusan_mhs', 50) }}" required>
                            </div>
                            <div class="col-6 form-group">
                                <label>Ambang Kelulusan MK (%) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" max="100"  class="form-control" name="batas_kelulusan_mk" placeholder="Contoh : 75" value="{{ old('batas_kelulusan_mk', 75) }}" required>
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
                    <form method="POST" action="{{ route($currentPrefix . 'mk.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if ($isUnivLevel)
                        <div class="form-group">
                            <label>Program Studi <span class="text-danger">*</span></label>
                            <select class="form-control" name="id_prodi" required>
                                <option value="" disabled selected>Pilih Program Studi...</option>
                                @foreach(\App\Models\Prodi::whereHas('fakultas', function($q){ $q->where('id_universitas', auth()->user()->id_universitasUser); })->get() as $p)
                                    <option value="{{ $p->id }}" {{ old('id_prodi') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="form-group">
                            <label>Tahun kurikulum <span class="text-danger">*</span></label>
                            <select class="form-control" name="id_kurikulum" id="kurikulum-select" required>
                                <option value="" disabled selected>Pilih Tahun Kurikulum...</option>
                                @foreach($kurikulums as $k)
                                    <option value="{{ $k->id }}" {{ old('id_kurikulum') == $k->id ? 'selected' : '' }}>{{ $k->tahun }}</option>
                                @endforeach
                            </select>
                            @error('id_kurikulum') <div class="alert alert-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label>MK prasyarat</label>
                            <select class="form-control" name="prasyarat" id="prasyarat-select">
                                <option value="" selected>Tidak ada</option>
                                @foreach($mks as $mk)
                                    <option value="{{ $mk->nama }}">{{ $mk->kode }} - {{ $mk->nama }}</option>
                                @endforeach
                            </select>
                            @error('prasyarat') <div class="alert alert-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group position-relative">
                            <label>Kode MK <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="kode" id="kode-mk-prodi-input" placeholder="Ketik Kode MK baru atau pilih MK Universitas..." value="{{ old('kode') }}" autocomplete="off" required>
                            @if(isset($mksUniv) && count($mksUniv) > 0)
                            <div id="mk-univ-dropdown-menu" class="dropdown-menu w-100 shadow" style="display: none; max-height: 220px; overflow-y: auto; position: absolute; top: 100%; left: 0; z-index: 1050;">
                                @foreach($mksUniv as $uMk)
                                    <a href="#" class="dropdown-item py-2 border-bottom mk-univ-item d-flex justify-content-between align-items-center" data-kode="{{ $uMk->kode }}">
                                        <div>
                                            <strong class="text-primary">{{ $uMk->kode }}</strong> - {{ $uMk->nama }}
                                        </div>
                                        <span class="badge bg-secondary text-white ms-2" style="font-size: 10px;">MK Universitas</span>
                                    </a>
                                @endforeach
                            </div>
                            @endif
                            <small class="text-muted">Ketik Kode MK baru langsung di kolom ini, atau pilih dari daftar MK Universitas jika ingin mengadopsinya.</small>
                            @error('kode') <div class="alert alert-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label>Nama MK <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama" placeholder="Nama MK" value="{{ old('nama') }}" autocomplete="off" required>
                            @error('nama') <div class="alert alert-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label>Nama MK (English) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_eng" value="{{ old('nama_eng') }}" placeholder="English course name" required>
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
                        <div class="row">
                            <div class="col-6 form-group">
                                <label>Ambang Kelulusan Mahasiswa <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" max="100" class="form-control" name="batas_kelulusan_mhs" placeholder="Contoh : 50" value="{{ old('batas_kelulusan_mhs') }}" required>
                            </div>
                            <div class="col-6 form-group">
                                <label>Ambang Kelulusan MK (%) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" max="100"  class="form-control" name="batas_kelulusan_mk" placeholder="Contoh : 75" value="{{ old('batas_kelulusan_mk') }}" required>
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

    if ($.fn.select2) {
        $('.select2-tags').select2({
            tags: true,
            placeholder: "Ketik Kode MK baru atau pilih MK Universitas...",
            allowClear: true,
            width: '100%'
        });
    }

    const mksUnivData = @json($mksUniv ?? []);
    const $input = $('#kode-mk-prodi-input');
    const $dropdown = $('#mk-univ-dropdown-menu');

    function filterAndShowDropdown() {
        if (!$dropdown.length) return;
        const query = $input.val().toUpperCase().trim();
        let hasMatches = false;

        $dropdown.find('.mk-univ-item').each(function() {
            const itemKode = ($(this).data('kode') || '').toString().toUpperCase();
            const itemText = $(this).text().toUpperCase();
            if (!query || itemKode.includes(query) || itemText.includes(query)) {
                $(this).show();
                hasMatches = true;
            } else {
                $(this).hide();
            }
        });

        if (hasMatches && mksUnivData.length > 0) {
            $dropdown.addClass('show').show();
        } else {
            $dropdown.removeClass('show').hide();
        }
    }

    $input.on('focus input', function() {
        filterAndShowDropdown();
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.position-relative').length) {
            $dropdown.removeClass('show').hide();
        }
    });

    $dropdown.on('click', '.mk-univ-item', function(e) {
        e.preventDefault();
        const kode = $(this).data('kode');
        $input.val(kode);
        $dropdown.removeClass('show').hide();
        triggerAutoFill(kode);
    });

    $input.on('input change', function() {
        const kode = $input.val().toUpperCase().trim();
        triggerAutoFill(kode);
    });

    function triggerAutoFill(kode) {
        if (!kode) return;
        const found = mksUnivData.find(item => item.kode.toUpperCase() === kode.toUpperCase());
        if (found) {
            $('#mk-prodi input[name="nama"]').val(found.nama);
            $('#mk-prodi input[name="nama_eng"]').val(found.nama_eng || found.nama);
            if (found.rumpun) {
                $(`#mk-prodi input[name="rumpun"][value="${found.rumpun}"]`).prop('checked', true);
            }
            if (found.bobot_teori !== undefined) $('#mk-prodi input[name="bobot_teori"]').val(found.bobot_teori);
            if (found.bobot_praktikum !== undefined) $('#mk-prodi input[name="bobot_praktikum"]').val(found.bobot_praktikum);
            if (found.batas_kelulusan_mhs !== undefined) $('#mk-prodi input[name="batas_kelulusan_mhs"]').val(found.batas_kelulusan_mhs);
            if (found.batas_kelulusan_mk !== undefined) $('#mk-prodi input[name="batas_kelulusan_mk"]').val(found.batas_kelulusan_mk);
            if (found.deskripsi) $('#mk-prodi textarea[name="deskripsi"]').val(found.deskripsi);
        }
    }
});
</script>
@endsection