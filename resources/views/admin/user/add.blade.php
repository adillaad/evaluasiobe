{{-- @php
    $routePrefix = [
        'Admin' => ['prefix' => 'admin.'],
        'Admin Universitas' => ['prefix' => 'admin-universitas.'],
        'Penjamin Mutu Universitas' => ['prefix' => 'penjamin-mutu.universitas.'],
        'Penjamin Mutu Fakultas' => ['prefix' => 'penjamin-mutu.fakultas.'],
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
    ];
    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'admin.';
@endphp --}}

@extends('admin.template')
@section('content')
    <style>
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            color: white !important;
        }

        /* Style untuk select2 yang disabled */
        .select2-container--disabled {
            pointer-events: none !important;
        }

        /* Memperkuat style untuk mencegah interaksi */
        select:disabled+.select2-container {
            opacity: 0.6;
            cursor: not-allowed !important;
            background-color: #e9ecef !important;
        }

        /* Menghilangkan hover effect */
        .select2-container--disabled .select2-selection {
            background-color: #e9ecef !important;
            border-color: #ced4da !important;
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 5px;
        }

        .select2-container--default .select2-selection--multiple {
            min-height: 38px;
        }

        #nama-otoritas-container:empty {
            display: none;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            color: white !important;
            background-color: #007bff;
            border-color: #0069d9;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white !important;
        }
    </style>
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah User</h4>
                <form method="POST" action="{{ route($currentPrefix . 'store-user') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="Name"
                            value="{{ old('name') }}" autofocus autocomplete="off">
                        @error('name')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Email address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" placeholder="Email"
                            value="{{ old('email') }}" autocomplete="off">
                        @error('email')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" placeholder="Password" autocomplete="new-password">
                        @error('password')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label>Otoritas <span class="text-danger">*</span></label>
                        <select class="otoritas-select w-100" name="otoritas[]" id="otoritas" multiple>
                            @if ($userOtoritas != 'Admin Universitas')
                                <option value="Admin Universitas">Admin Universitas</option>
                            @endif
                            <option value="Wakil Rektor">Wakil Rektor</option>
                            <option value="Wakil Dekan">Wakil Dekan</option>
                            <option value="Kepala Program Studi">Kepala Program Studi</option>
                            <option value="Dosen">Dosen</option>
                            <option value="Penjamin Mutu Program Studi">Penjamin Mutu Program Studi</option>
                            <option value="Penjamin Mutu Fakultas">Penjamin Mutu Fakultas</option>
                            <option value="Penjamin Mutu Universitas">Penjamin Mutu Universitas</option>
                        </select>
                        @error('otoritas')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group" id="nama-otoritas-container">
                        <label>Nama Otoritas</label>
                        <div id="nama-otoritas-fields">
                            {{-- Dynamic fields --}}
                        </div>
                    </div>
                    {{-- UNIVERSITAS --}}
                    <div class="form-group" id="universitas-form">
                        <label>Universitas<span class="text-danger">*</span></label>
                        @php
                            $isUniversitasDisabled = in_array($userOtoritas, [
                                'Admin Universitas',
                                'Penjamin Mutu Universitas',
                                'Penjamin Mutu Fakultas',
                                'Penjamin Mutu Prodi',
                            ]);
                        @endphp

                        @if ($isUniversitasDisabled)
                            <input type="hidden" name="universitas" value="{{ auth()->user()->id_universitasUser }}">
                        @endif

                        <select class="single-select w-100" name="{{ $isUniversitasDisabled ? '' : 'universitas' }}"
                            {{ $isUniversitasDisabled ? 'disabled' : '' }}>
                            <option value="" disabled
                                {{ !old('universitas') && $userOtoritas === 'Admin' ? 'selected' : '' }}>
                                Select...</option>
                            @foreach ($universitas as $u)
                                <option value="{{ $u->id }}"
                                    {{ old('universitas') == $u->id || ($isUniversitasDisabled && auth()->user()->id_universitasUser == $u->id)
                                        ? 'selected'
                                        : '' }}>
                                    {{ $u->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('universitas')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- FAKULTAS --}}
                    <div class="form-group" id="fakultas-form">
                        <label>Fakultas<span class="text-danger">*</span></label>
                        @php
                            $isFakultasDisabled = in_array($userOtoritas, [
                                'Penjamin Mutu Fakultas',
                                'Penjamin Mutu Prodi',
                            ]);
                        @endphp

                        @if ($isFakultasDisabled)
                            <input type="hidden" name="fakultas" value="{{ auth()->user()->id_fakultasUser }}">
                        @endif

                        <select class="single-select w-100" name="{{ $isFakultasDisabled ? '' : 'fakultas' }}"
                            {{ $isFakultasDisabled || ($userOtoritas === 'Admin' && !old('universitas')) ? 'disabled' : '' }}>
                            <option value="" disabled selected>Pilih opsi</option>
                            @foreach ($fakultas as $f)
                                <option value="{{ $f->id }}"
                                    {{ old('fakultas') == $f->id || ($isFakultasDisabled && auth()->user()->id_fakultasUser == $f->id)
                                        ? 'selected'
                                        : '' }}>
                                    {{ $f->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('fakultas')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- PRODI --}}
                    <div class="form-group" id="prodi-form">
                        <label>Prodi <span class="text-danger">*</span></label>
                        {{-- DIUBAH: Hapus semua <option> dari sini agar placeholder bisa muncul --}}
                        <select class="prodi-select w-100" name="prodi[]" multiple disabled>
                        </select>
                        @error('prodi') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                        @error('prodi.*') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Profile Picture</label>
                        <input type="file" accept="image/png, image/jpeg" name="img" class="form-control"
                            style="padding-bottom: +27px">
                        @error('img')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <button class="btn btn-primary me-2">{{ __('Register') }}</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            const userOtoritas = "{{ $userOtoritas }}";
            const baseUrl = "{{ $currentPrefix }}".slice(0, -1); // Menghapus titik di akhir

            // Inisialisasi Select2
            $('#otoritas').select2({
                width: '100%',
                placeholder: 'Pilih satu atau lebih otoritas',
                multiple: true,
                closeOnSelect: false
            });
            $('select[name="universitas"], select[name="fakultas"]').select2({
                width: '100%',
                placeholder: 'Pilih salah satu',
            });
            $('.prodi-select').select2({
                width: '100%',
                placeholder: 'Pilih satu atau lebih prodi',
                multiple: true,
                closeOnSelect: false
            });

            function updateNamaOtoritasFields() {
                const selectedOtoritas = $('#otoritas').val();
                const container = $('#nama-otoritas-fields');
                container.empty();
                if (selectedOtoritas && selectedOtoritas.length > 0) {
                    selectedOtoritas.forEach((otoritas) => {
                        container.append(
                            `<div class="mb-2"><label class="small text-muted">Nama untuk ${otoritas}</label><input type="text" class="form-control" name="nama_otoritas[${otoritas}]" placeholder="Nama Otoritas untuk ${otoritas}" autocomplete="off"></div>`
                        );
                    });
                    $('#nama-otoritas-container').show();
                } else {
                    $('#nama-otoritas-container').hide();
                }
            }
            updateNamaOtoritasFields();
            $('#otoritas').on('change', updateNamaOtoritasFields);

            function resetSelect(selectElement) {
                selectElement.empty().prop('disabled', true).trigger('change');
            }

            function loadFakultas(universitasId) {
                const fakultasSelect = $('select[name="fakultas"]');
                $.ajax({
                    url: `/${baseUrl}/get-fakultas/${universitasId}`,
                    type: 'GET',
                    success: function(data) {
                        fakultasSelect.empty().append('<option></option>'); // Kosongkan dan tambahkan option placeholder
                        $.each(data, function(key, value) {
                            fakultasSelect.append(`<option value="${value.id}">${value.nama}</option>`);
                        });
                        fakultasSelect.prop('disabled', false).trigger('change');
                        
                        // Handle old value
                        const oldFakultas = "{{ old('fakultas') }}";
                        if (oldFakultas) {
                            fakultasSelect.val(oldFakultas).trigger('change');
                        }
                    }
                });
            }

            function loadProdi(fakultasId) {
                const prodiSelect = $('.prodi-select');
                $.ajax({
                    url: `/${baseUrl}/get-prodi/${fakultasId}`,
                    type: 'GET',
                    success: function(data) {
                        const oldProdi = @json(old('prodi')) || [];
                        prodiSelect.empty();
                        $.each(data, function(key, value) {
                            const isSelected = oldProdi.includes(String(value.id));
                            prodiSelect.append(`<option value="${value.id}" ${isSelected ? 'selected' : ''}>${value.nama}</option>`);
                        });
                        prodiSelect.prop('disabled', false).trigger('change');
                    }
                });
            }

            // Event handlers
            $('select[name="universitas"]').on('change', function() {
                const universitasId = $(this).val();
                resetSelect($('select[name="fakultas"]'));
                resetSelect($('.prodi-select'));
                if (universitasId) {
                    loadFakultas(universitasId);
                }
            });

            $('select[name="fakultas"]').on('change', function() {
                const fakultasId = $(this).val();
                resetSelect($('.prodi-select'));
                if (fakultasId) {
                    loadProdi(fakultasId);
                }
            });

            // Inisialisasi form jika ada old value atau data dari controller
            function initializeForm() {
                const oldUniversitas = "{{ old('universitas') }}";
                const isUniversitasDisabled = $('select[name="universitas"]').is(':disabled');

                if (oldUniversitas) {
                    $('select[name="universitas"]').val(oldUniversitas).trigger('change');
                } else if (isUniversitasDisabled) {
                    const uniId = $('input[name="universitas"]').val();
                    if(uniId) loadFakultas(uniId);
                }
            }
            
            initializeForm();
        });
    </script>
@endsection
