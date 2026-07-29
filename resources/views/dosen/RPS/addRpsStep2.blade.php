@extends('dosen.template')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="fw-bold">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="left"
                        title="Silahkan pilih sesuai dengan data yang tersedia." style="float:right;"> Panduan
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                            <path
                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                        </svg>
                    </button>
                    <h3>Tambah RPS Baru</h3>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="add-rpsStep2" enctype="multipart/form-data">
                    @csrf
                    {{-- <p>Nilai 'step1_dataRps.nomor': {{ session('step1_dataRps.nomor') }}</p> --}}
                    {{-- <p>Nilai 'step1_dataRps.nomor': {{ session('step1_dataRps.nomor') }}</p> --}}
                    <div class="form-floating mb-3">
                        <select name="pengembang" class="form-select form-control-lg">
                            <option value="" disabled selected></option>
                            @foreach ($pengembang as $user)
                                <option value="{{ $user->name }}"
                                    {{ old('pengembang', session('step2_dataRps.pengembang')) == $user->name ? 'selected' : '' }}>
                                    {{ $user->name }}</option>
                            @endforeach
                        </select>
                        <label for="pengembang">Pengembang RPS <span style="color:red">*</span></label>
                        @error('pengembang')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <select name="koordinator" class="form-select form-control-lg">
                            <option value="" disabled selected></option>
                            @foreach ($users as $user)
                                <option value="{{ $user->name }}"
                                    {{ old('koordinator', session('step2_dataRps.koordinator')) == $user->name ? 'selected' : '' }}>
                                    {{ $user->name }}</option>
                            @endforeach
                        </select>
                        <label for="koordinator">Koordinator RMK</label>
                        @error('koordinator')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="border p-3 rounded mb-3">
                    <h5 class="mb-3">Team Teaching</h5>

                    <div class="form-floating mb-3">
                        <select name="dosen" class="form-select form-control-lg">
                            <option value="" disabled selected></option>
                            @foreach ($users as $user)
                                <option value="{{ $user->name }}"
                                    {{ old('dosen', session('step2_dataRps.dosen')) == $user->name ? 'selected' : '' }}>
                                    {{ $user->name }}</option>
                            @endforeach
                        </select>
                        <label for="dosen">Dosen Pengampu<span style="color:red">*</span></label>
                        @error('dosen')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <select name="dosen_anggota1" class="form-select form-control-lg">
                            <option value="" disabled selected></option>
                            @foreach ($users as $user)
                                <option value="{{ $user->name }}"
                                    {{ old('dosen', session('step2_dataRps.dosen_anggota1')) == $user->name ? 'selected' : '' }}>
                                    {{ $user->name }}</option>
                            @endforeach
                        </select>
                        <label for="dosen_anggota1">Dosen Anggota 1<span style="color:red">*</span></label>
                        @error('dosen')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <select name="dosen_anggota2" class="form-select form-control-lg">
                            <option value="" disabled selected></option>
                            @foreach ($users as $user)
                                <option value="{{ $user->name }}"
                                    {{ old('dosen', session('step2_dataRps.dosen_anggota2')) == $user->name ? 'selected' : '' }}>
                                    {{ $user->name }}</option>
                            @endforeach
                        </select>
                        <label for="dosen_anggota2">Dosen Anggota 2</label>
                        @error('dosen')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                    <button type="button" class="btn btn-primary"
                        onclick="location.href='{{ route('dosen.rps-add') }}'">Previous</button>
                    <button type="submit" class="btn btn-primary">Next</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

        });
    </script>
@endsection
