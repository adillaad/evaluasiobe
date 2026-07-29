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
            background-color: #007bff;
            border-color: #0069d9;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: rgba(255,255,255,0.7) !important;
        }
        .select2-container--default .select2-selection--single { height: 38px; padding: 5px; }
        .select2-container--default .select2-selection--multiple { min-height: 38px; }
        #nama-otoritas-container:empty { display: none; }
    </style>
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit User: {{ $user->name }}</h4>
                <form method="POST" action="{{ route($currentPrefix . 'update-user', ['id' => encrypt($user->id)]) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $user->name) }}" autofocus>
                        @error('name') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $user->email) }}">
                        @error('email') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label>Otoritas <span class="text-danger">*</span></label>
                        <select class="otoritas-select w-100" name="otoritas[]" id="otoritas" multiple>
                            @php $currentOtoritas = old('otoritas', $user->otoritas()->pluck('otoritas')->toArray()); @endphp
                            <option value="Admin Universitas" {{ in_array('Admin Universitas', $currentOtoritas) ? 'selected' : '' }}>Admin Universitas</option>
                            <option value="Wakil Rektor" {{ in_array('Wakil Rektor', $currentOtoritas) ? 'selected' : '' }}>Wakil Rektor</option>
                            <option value="Wakil Dekan" {{ in_array('Wakil Dekan', $currentOtoritas) ? 'selected' : '' }}>Wakil Dekan</option>
                            <option value="Kepala Program Studi" {{ in_array('Kepala Program Studi', $currentOtoritas) ? 'selected' : '' }}>Kepala Program Studi</option>
                            <option value="Dosen" {{ in_array('Dosen', $currentOtoritas) ? 'selected' : '' }}>Dosen</option>
                            <option value="Penjamin Mutu Program Studi" {{ in_array('Penjamin Mutu Program Studi', $currentOtoritas) ? 'selected' : '' }}>Penjamin Mutu Program Studi</option>
                            <option value="Penjamin Mutu Fakultas" {{ in_array('Penjamin Mutu Fakultas', $currentOtoritas) ? 'selected' : '' }}>Penjamin Mutu Fakultas</option>
                            <option value="Penjamin Mutu Universitas" {{ in_array('Penjamin Mutu Universitas', $currentOtoritas) ? 'selected' : '' }}>Penjamin Mutu Universitas</option>
                        </select>
                        @error('otoritas') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="form-group" id="nama-otoritas-container" style="display:none;">
                        <label>Nama Otoritas</label>
                        <div id="nama-otoritas-fields"></div>
                    </div>

                    {{-- FILTER UNIVERSITAS --}}
                    <div class="form-group">
                        <label>Universitas (Filter)</label>
                        <select class="single-select w-100" name="universitas_filter">
                            <option></option>
                            @foreach ($allUniversitas as $universitas)
                                <option value="{{ $universitas->id }}" {{ $selectedUniversitas->id == $universitas->id ? 'selected' : '' }}>
                                    {{ $universitas->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- FILTER FAKULTAS --}}
                    <div class="form-group">
                        <label>Fakultas (Filter)</label>
                        <select class="single-select w-100" name="fakultas_filter">
                            <option></option>
                            @foreach ($allFakultas as $fakultas)
                                <option value="{{ $fakultas->id }}" {{ $selectedFakultas->id == $fakultas->id ? 'selected' : '' }}>
                                    {{ $fakultas->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- PILIHAN PRODI (MULTI-SELECT) --}}
                    <div class="form-group" id="prodi-form">
                        <label>Prodi <span class="text-danger">*</span></label>
                        <select class="prodi-select w-100" name="prodi[]" multiple>
                            @foreach ($allProdi as $prodi)
                                <option value="{{ $prodi->id }}" {{ in_array($prodi->id, old('prodi', $userProdiIds)) ? 'selected' : '' }}>
                                    {{ $prodi->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('prodi') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                        @error('prodi.*') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label>Profile Picture</label>
                        <input type="file" accept="image/png, image/jpeg" name="img" class="form-control" style="padding-bottom: 27px;">
                        @error('img') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary me-2">Update</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            const baseUrl = "{{ $currentPrefix }}".slice(0, -1);
            const userProdiIds = @json(old('prodi', $userProdiIds)); // Ambil old value jika ada

            // Inisialisasi Select2
            $('#otoritas').select2({ width: '100%', placeholder: 'Pilih satu atau lebih otoritas', multiple: true, closeOnSelect: false });
            $('select[name="universitas_filter"], select[name="fakultas_filter"]').select2({ width: '100%', placeholder: 'Pilih untuk memfilter...' });
            $('.prodi-select').select2({ width: '100%', placeholder: 'Pilih satu atau lebih prodi', multiple: true, closeOnSelect: false });

            // ... (Fungsi untuk Nama Otoritas bisa ditambahkan di sini jika diperlukan) ...

            function resetSelect(selectElement) {
                selectElement.empty().trigger('change');
            }

            function loadFakultas(universitasId) {
                const fakultasSelect = $('select[name="fakultas_filter"]');
                $.ajax({
                    url: `/${baseUrl}/get-fakultas/${universitasId}`,
                    type: 'GET',
                    success: function(data) {
                        fakultasSelect.empty().append('<option></option>');
                        $.each(data, function(key, value) {
                            fakultasSelect.append(`<option value="${value.id}">${value.nama}</option>`);
                        });
                    }
                });
            }

            function loadProdi(fakultasId) {
                const prodiSelect = $('.prodi-select');
                $.ajax({
                    url: `/${baseUrl}/get-prodi/${fakultasId}`,
                    type: 'GET',
                    success: function(data) {
                        prodiSelect.empty();
                        $.each(data, function(key, value) {
                            // Cek apakah prodi ini ada di daftar ID milik user
                            const isSelected = userProdiIds.includes(value.id);
                            prodiSelect.append(`<option value="${value.id}" ${isSelected ? 'selected' : ''}>${value.nama}</option>`);
                        });
                        prodiSelect.trigger('change');
                    }
                });
            }

            // Event handler untuk filter
            $('select[name="universitas_filter"]').on('change', function() {
                const universitasId = $(this).val();
                resetSelect($('select[name="fakultas_filter"]'));
                resetSelect($('.prodi-select'));
                if (universitasId) loadFakultas(universitasId);
            });

            $('select[name="fakultas_filter"]').on('change', function() {
                const fakultasId = $(this).val();
                // Jangan reset. Biarkan user memilih dari daftar prodi baru
                if (fakultasId) loadProdi(fakultasId);
            });
        });
    </script>
@endsection