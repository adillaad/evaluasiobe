@php
    // $routePrefix = [
    //     'Penjamin Mutu Universitas' => ['prefix' => 'penjamin-mutu.universitas.'],
    //     'Penjamin Mutu Fakultas' => ['prefix' => 'penjamin-mutu.fakultas.'],
    //     'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
    //     'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    // ];

    // $userOtoritas = auth()->user()->otoritas->otoritas;
    // $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';

    $ajaxUrlPath = '';
    switch ($userOtoritas) {
        case 'Penjamin Mutu Universitas':
            $ajaxUrlPath = 'penjamin-mutu/universitas';
            break;
        case 'Penjamin Mutu Fakultas':
            $ajaxUrlPath = 'penjamin-mutu/fakultas';
            break;
        case 'Penjamin Mutu Program Studi':
            $ajaxUrlPath = 'penjamin-mutu/program-studi';
            break;
        case 'Kepala Program Studi':
            $ajaxUrlPath = 'kepala-program-studi';
            break;
        default:
            $ajaxUrlPath = 'penjamin-mutu/program-studi';
            break;
    }
@endphp

@extends('penjamin-mutu.template')
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
                <h4 class="card-title">Tambah User</h4>
                <form method="POST" action="{{ route($currentPrefix . 'store-user') }}" enctype="multipart/form-data">
                    @csrf
                    {{-- Input Name, Email, Password, Otoritas --}}
                    <div class="form-group">
                        <label>Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="Name" value="{{ old('name') }}" autofocus>
                        @error('name') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Email address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" placeholder="Email" value="{{ old('email') }}">
                        @error('email') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" placeholder="Password" autocomplete="new-password">
                        @error('password') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password">
                    </div>
                    <div class="form-group">
                        <label>Otoritas <span class="text-danger">*</span></label>
                        <select class="otoritas-select w-100" name="otoritas[]" id="otoritas" multiple>
                            @if ($userOtoritas == 'Penjamin Mutu Universitas')
                                <option value="Wakil Rektor">Wakil Rektor</option>
                                <option value="Wakil Dekan">Wakil Dekan</option>
                                <option value="Penjamin Mutu Fakultas">Penjamin Mutu Fakultas</option>
                                <option value="Penjamin Mutu Program Studi">Penjamin Mutu Program Studi</option>
                                <option value="Kepala Program Studi">Kepala Program Studi</option>
                                <option value="Dosen">Dosen</option>
                            @elseif ($userOtoritas == 'Penjamin Mutu Fakultas')
                                <option value="Wakil Dekan">Wakil Dekan</option>
                                <option value="Penjamin Mutu Program Studi">Penjamin Mutu Program Studi</option>
                                <option value="Kepala Program Studi">Kepala Program Studi</option>
                                <option value="Dosen">Dosen</option>
                            @elseif (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                                <option value="Kepala Program Studi">Kepala Program Studi</option>
                                <option value="Dosen">Dosen</option>
                            @endif
                        </select>
                        @error('otoritas') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group" id="nama-otoritas-container" style="display:none;">
                        <label>Nama Otoritas</label>
                        <div id="nama-otoritas-fields"></div>
                    </div>

                    {{-- UNIVERSITAS --}}
                    <div class="form-group">
                        <label>Universitas</label>
                        <input type="hidden" name="universitas" value="{{ auth()->user()->id_universitasUser }}">
                        <select class="single-select w-100" disabled>
                            <option selected>{{ auth()->user()->universitas->nama }}</option>
                        </select>
                    </div>

                    {{-- FAKULTAS --}}
                    <div class="form-group">
                        <label>Fakultas <span class="text-danger">*</span></label>
                        @php $isFakultasDisabled = in_array($userOtoritas, ['Penjamin Mutu Fakultas', 'Penjamin Mutu Program Studi', 'Kepala Program Studi']); @endphp
                        @if ($isFakultasDisabled)
                            <input type="hidden" name="fakultas" value="{{ auth()->user()->id_fakultasUser }}">
                        @endif
                        <select class="single-select w-100" name="{{ $isFakultasDisabled ? '' : 'fakultas' }}" {{ $isFakultasDisabled ? 'disabled' : '' }}>
                            <option></option>
                            @foreach ($fakultas as $f)
                                <option value="{{ $f->id }}" {{ old('fakultas', $isFakultasDisabled ? auth()->user()->id_fakultasUser : '') == $f->id ? 'selected' : '' }}>
                                    {{ $f->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('fakultas') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- PRODI --}}
                    <div class="form-group">
                        <label>Prodi<span class="text-danger">*</span></label>
                        {{-- DIUBAH: Tambahkan logika untuk mengunci prodi --}}
                        @php
                            $isProdiLocked = in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']);
                        @endphp
                        
                        {{-- Jika prodi dikunci, kirim nilainya lewat input hidden --}}
                        @if ($isProdiLocked)
                            <input type="hidden" name="prodi[]" value="{{ auth()->user()->id_prodiUser }}">
                        @endif

                        {{-- Atribut 'name' dan 'disabled' sekarang dinamis --}}
                        <select class="prodi-select w-100" name="{{ $isProdiLocked ? '' : 'prodi[]' }}" multiple {{ $isProdiLocked ? 'disabled' : '' }}>
                            @if ($isProdiLocked)
                                {{-- Jika terkunci, hanya tampilkan prodi milik user --}}
                                <option value="{{ auth()->user()->id_prodiUser }}" selected>{{ auth()->user()->prodi->nama }}</option>
                            @elseif(old('fakultas') || in_array($userOtoritas, ['Penjamin Mutu Fakultas']))
                                {{-- Logika untuk menampilkan old value jika ada --}}
                                @foreach($prodi as $p)
                                    <option value="{{ $p->id }}" {{ (is_array(old('prodi')) && in_array($p->id, old('prodi'))) ? 'selected' : '' }}>
                                        {{ $p->nama }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('prodi') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                        @error('prodi.*') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label>Profile Picture</label>
                        <input type="file" accept="image/png, image/jpeg" name="img" class="form-control" style="padding-bottom: 27px;">
                        @error('img') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary me-2">Register</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            const ajaxUrlPath = "{{ $ajaxUrlPath }}";

            // Inisialisasi Select2
            $('.single-select').select2({ width: '100%', placeholder: 'Pilih salah satu' });
            $('.otoritas-select').select2({ width: '100%', placeholder: 'Pilih satu atau lebih', multiple: true, closeOnSelect: false });
            $('.prodi-select').select2({ width: '100%', placeholder: 'Pilih satu atau lebih', multiple: true, closeOnSelect: false });

            // ... (Fungsi untuk Nama Otoritas bisa ditambahkan di sini jika diperlukan) ...

            function resetSelect(selectElement) {
                selectElement.empty().prop('disabled', true).trigger('change');
            }

            function loadProdi(fakultasId) {
                const prodiSelect = $('.prodi-select');
                $.ajax({
                    url: `/${ajaxUrlPath}/get-prodi/${fakultasId}`,
                    type: 'GET',
                    success: function(data) {
                        const oldProdi = @json(old('prodi')) || [];
                        prodiSelect.empty();
                        $.each(data, function(key, value) {
                            const isSelected = oldProdi.includes(String(value.id));
                            prodiSelect.append(`<option value="${value.id}" ${isSelected ? 'selected' : ''}>${value.nama}</option>`);
                        });
                        prodiSelect.prop('disabled', false).trigger('change');
                    },
                    error: function() {
                        resetSelect(prodiSelect);
                        alert('Gagal memuat data prodi.');
                    }
                });
            }

            // Event handler untuk fakultas
            $('select[name="fakultas"]').on('change', function() {
                const fakultasId = $(this).val();
                resetSelect($('.prodi-select'));
                if (fakultasId) {
                    loadProdi(fakultasId);
                }
            });
            
            // Inisialisasi form saat halaman dimuat
            function initializeForm() {
                // Jika ada old value untuk fakultas, trigger change untuk memuat prodi
                const oldFakultas = "{{ old('fakultas') }}";
                if(oldFakultas) {
                    $('select[name="fakultas"]').val(oldFakultas).trigger('change');
                }
                
                // Jika fakultas disabled (sudah terisi dari controller), langsung load prodi
                const fakultasSelect = $('select[name="fakultas"]');
                if(fakultasSelect.is(':disabled') && fakultasSelect.val()){
                    loadProdi(fakultasSelect.val());
                }
            }
            
            initializeForm();
        });
    </script>
@endsection