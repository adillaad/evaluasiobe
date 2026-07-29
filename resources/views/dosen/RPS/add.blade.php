@extends('dosen.template')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="fw-bold">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="left"
                        title="Silahkan pilih sesuai dengan data yang tersedia. Keterangan : satu mata kuliah terikat dengan satu RPS"
                        style="float:right;"> Panduan
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
                <form method="POST" action="/dosen/rps/add-rpsStep1" enctype="multipart/form-data">
                    @csrf

                    <div class="form-floating mb-3">
                        <select class="form-select form-control-lg" name="prodi">
                            <option value="" disabled selected></option>
                            @foreach ($prodis as $prodi)
                                <option value={{ $prodi->id }} {{ old('prodi', session('step1_dataRps.prodi')) == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->nama }}</option>
                            @endforeach
                        </select>
                        <label for="prodi">Program studi <span style="color:red">*</span></label>
                        @error('prodi')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-floating mb-3">
                        <select id="matakuliah" name="matakuliah" class="form-select form-control-lg">
                            <option value="" disabled selected></option>
                            @foreach ($mks as $mk)
                                <option value="{{ $mk->kode }}"
                                    {{ old('matakuliah', session('step1_dataRps.matakuliah')) == $mk->kode ? 'selected' : '' }}>
                                    {{ $mk->kode }} - {{ $mk->nama }}</option>
                            @endforeach
                        </select>
                        <label for="matakuliah">Mata kuliah <span style="color:red">*</span></label>
                        @error('matakuliah')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select form-control-lg" name="semester" id="semester">
                            <option selected="true" value="" disabled selected> </option>
                            @for ($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}"
                                    {{ old('semester', session('step1_dataRps.semester')) == $i ? 'selected' : '' }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                        <label>Semester <span class="text-danger">*</span></label>
                        @error('semester')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
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
