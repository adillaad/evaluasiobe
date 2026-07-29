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
            $ajaxUrlPath = 'penjamin-mutu/program-studi'; // Fallback default
            break;
    }
@endphp

@extends('penjamin-mutu.template')
@section('content')
    {{-- Notifikasi --}}
    @if (session()->has('error'))
        <div class="alert alert-danger" role="alert" id="box">{{ session('error') }}</div>
    @elseif (session()->has('success'))
        <div class="alert alert-success" role="alert" id="box">{{ session('success') }}</div>
    @endif

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

                    {{-- Name, Email, Otoritas --}}
                    <div class="form-group">
                        <label>Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}">
                        @error('name') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Email address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}">
                        @error('email') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Otoritas <span class="text-danger">*</span></label>
                        <select class="otoritas-select w-100" name="otoritas[]" id="otoritas" multiple>
                            @php $currentOtoritas = old('otoritas', $user->otoritas()->pluck('otoritas')->toArray()); @endphp
                            @if ($userOtoritas == 'Penjamin Mutu Universitas')
                                <option value="Wakil Rektor" {{ in_array('Wakil Rektor', $currentOtoritas) ? 'selected' : '' }}>Wakil Rektor</option>
                                <option value="Wakil Dekan" {{ in_array('Wakil Dekan', $currentOtoritas) ? 'selected' : '' }}>Wakil Dekan</option>
                                <option value="Penjamin Mutu Fakultas" {{ in_array('Penjamin Mutu Fakultas', $currentOtoritas) ? 'selected' : '' }}>Penjamin Mutu Fakultas</option>
                                <option value="Penjamin Mutu Program Studi" {{ in_array('Penjamin Mutu Program Studi', $currentOtoritas) ? 'selected' : '' }}>Penjamin Mutu Program Studi</option>
                                <option value="Kepala Program Studi" {{ in_array('Kepala Program Studi', $currentOtoritas) ? 'selected' : '' }}>Kepala Program Studi</option>
                                <option value="Dosen" {{ in_array('Dosen', $currentOtoritas) ? 'selected' : '' }}>Dosen</option>
                            @elseif ($userOtoritas == 'Penjamin Mutu Fakultas')
                                <option value="Wakil Dekan" {{ in_array('Wakil Dekan', $currentOtoritas) ? 'selected' : '' }}>Wakil Dekan</option>
                                <option value="Penjamin Mutu Program Studi" {{ in_array('Penjamin Mutu Program Studi', $currentOtoritas) ? 'selected' : '' }}>Penjamin Mutu Program Studi</option>
                                <option value="Kepala Program Studi" {{ in_array('Kepala Program Studi', $currentOtoritas) ? 'selected' : '' }}>Kepala Program Studi</option>
                                <option value="Dosen" {{ in_array('Dosen', $currentOtoritas) ? 'selected' : '' }}>Dosen</option>
                            @elseif (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                                <option value="Kepala Program Studi" {{ in_array('Kepala Program Studi', $currentOtoritas) ? 'selected' : '' }}>Kepala Program Studi</option>
                                <option value="Dosen" {{ in_array('Dosen', $currentOtoritas) ? 'selected' : '' }}>Dosen</option>
                            @endif
                        </select>
                        @error('otoritas') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group" id="nama-otoritas-container" style="display: none;">
                        <label>Nama Otoritas</label>
                        <div id="nama-otoritas-fields"></div>
                    </div>

                    {{-- UNIVERSITAS --}}
                    <div class="form-group">
                        <label>Universitas</label>
                        <select class="single-select w-100" disabled>
                            <option selected>{{ $selectedUniversitas->nama }}</option>
                        </select>
                    </div>

                    {{-- FAKULTAS --}}
                    <div class="form-group">
                        <label>Fakultas</label>
                         @php $isFakultasDisabled = in_array($userOtoritas, ['Penjamin Mutu Fakultas', 'Penjamin Mutu Program Studi', 'Kepala Program Studi']); @endphp
                        <select class="single-select w-100" name="fakultas_filter" {{ $isFakultasDisabled ? 'disabled' : '' }}>
                            <option></option>
                            @foreach ($allFakultas as $fakultas)
                                <option value="{{ $fakultas->id }}" {{ $selectedFakultas->id == $fakultas->id ? 'selected' : '' }}>
                                    {{ $fakultas->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- PRODI --}}
                    <div class="form-group" id="prodi-form">
                        <label>Prodi <span class="text-danger">*</span></label>
                        @php $isProdiLocked = in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']); @endphp
                        
                        @if ($isProdiLocked)
                            <input type="hidden" name="prodi[]" value="{{ $user->id_prodiUser }}">
                        @endif

                        <select class="prodi-select w-100" name="{{ $isProdiLocked ? '' : 'prodi[]' }}" multiple {{ $isProdiLocked ? 'disabled' : '' }}>
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
            const ajaxUrlPath = "{{ $ajaxUrlPath }}";
            const userProdiIds = @json(old('prodi', $userProdiIds));

            // Inisialisasi Select2
            $('.single-select').select2({ width: '100%', placeholder: 'Pilih untuk memfilter...' });
            $('.otoritas-select').select2({ width: '100%', placeholder: 'Pilih satu atau lebih', multiple: true, closeOnSelect: false });
            $('.prodi-select').select2({ width: '100%', placeholder: 'Pilih satu atau lebih', multiple: true, closeOnSelect: false });

            // ... (Fungsi untuk Nama Otoritas, jika diperlukan) ...

            function resetSelect(selectElement) {
                selectElement.empty().trigger('change');
            }

            function loadProdi(fakultasId) {
                const prodiSelect = $('.prodi-select');
                $.ajax({
                    url: `/${ajaxUrlPath}/get-prodi/${fakultasId}`,
                    type: 'GET',
                    success: function(data) {
                        prodiSelect.empty();
                        $.each(data, function(key, value) {
                            const isSelected = userProdiIds.includes(value.id);
                            prodiSelect.append(`<option value="${value.id}" ${isSelected ? 'selected' : ''}>${value.nama}</option>`);
                        });
                        prodiSelect.trigger('change');
                    }
                });
            }

            // Event handler untuk filter fakultas
            $('select[name="fakultas_filter"]').on('change', function() {
                const fakultasId = $(this).val();
                resetSelect($('.prodi-select'));
                if (fakultasId) {
                    loadProdi(fakultasId);
                }
            });
        });
    </script>
@endsection