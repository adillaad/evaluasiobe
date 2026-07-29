@extends('dosen.template')
@section('content')
    <?php $s = 0; ?>
    <style>
        #container {
            width: 900px;
            margin: 20px auto;
        }

        .ck-editor__editable[role="textbox"] {
            /* Editing area */
            min-height: 200px;
        }

        .ck-content .image {
            /* Block images */
            max-width: 80%;
            margin: 20px auto;
        }

        li.select2-selection__choice {
            color: #646464;
            font-weight: bolder;
        }

        .tooltipa {
            position: relative;
        }

        .tooltiptext {
            visibility: hidden;
            width: 160px;
            /* Increased width for better readability */
            background-color: rgba(0, 0, 0, 0.8);
            /* Use rgba for semi-transparent background */
            color: white;
            text-align: center;
            border-radius: 8px;
            /* Slightly increased border radius for a softer look */
            padding: 8px;
            /* Increased padding for better spacing */
            position: absolute;
            z-index: 1;
            bottom: 0%;
            /* Increased distance from the parent element */
            left: 90%;
            transform: translateX(-50%);
            /* Center the tooltip horizontally */
            transition: visibility 0.3s ease-in-out;
            /* Add smooth transition */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .tooltipa:hover .tooltiptext {
            visibility: visible;
        }
    </style>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="fw-bold">
                    <h3>Tambah Soal Mata Kuliah Baru</h3>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="left"
                        title="Form ini digunakan untuk membuat soal yang akan digunakan untuk membuat template penilaian"
                        style="float:right;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                            <path
                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="card-body" id="tambah-soal">
                <form action="" method="post" enctype="multipart/form-data">
                    @csrf
                    <div id="templatePenilaian">
                        <div class="form-floating tooltipa tooltipa">
                            <select id="kurikulum" name="kurikulum" class="form-select form-control-lg"
                                aria-label="select kurikulum" required readonly>
                                <option>{{ $latestData->kurikulum }}</option>
                                @foreach ($kurikulum as $kur)
                                    <option value="{{ $kur->tahun }}">{{ $kur->tahun }}</option>
                                @endforeach
                            </select>
                            <span class="tooltiptext">Kolom ini terisi otomatis sesuai dengan yang di submit
                                sebelumnnya</span>
                            <label for="kurikulum"> Pilih Kurikulum <span class="text-danger"> *</span></label>
                            <div class="form-text mb-3"></div>
                        </div>

                        <div class="form-floating tooltipa">
                            <select id="prodi" name="prodi" class="form-select form-control-lg"
                                aria-label="select Prodi" required readonly>
                                <option>{{ $latestData->prodi }}</option>
                                @foreach ($prodi as $prodi)
                                    <option value="{{ $prodi->nama }}">{{ $prodi->nama }}</option>
                                @endforeach
                            </select>
                            <span class="tooltiptext">Kolom ini terisi otomatis sesuai dengan yang di submit
                                sebelumnnya</span>
                            <label for="prodi"> Pilih Prodi <span class="text-danger"> *</span></label>
                            <div class="form-text mb-3"></div>
                        </div>

                        <div class="form-floating tooltipa">
                            <select id="kode_mk" name="kode_mk" class="form-select form-control-lg"
                                aria-label="select Mata Kuliah" required readonly>
                                <option>{{ $latestData->kode_mk }}</option>
                                @foreach ($rpss as $rps)
                                    <option value="{{ $rps->kode_mk }}">{{ $rps->mk?->nama ?? $rps->kode_mk }}</option>
                                @endforeach
                            </select>
                            <span class="tooltiptext">Kolom ini terisi otomatis sesuai dengan yang di submit
                                sebelumnnya</span>
                            <label for="mataKuliah"> Pilih Mata Kuliah <span class="text-danger"> *</span></label>
                            <div class="form-text mb-3"></div>
                        </div>

                        <div class="form-floating tooltipa">
                            <select type="number" name="minggu" id="minggu" min="1" max="16"
                                class="form-select form-control-lg" required readonly>
                                <option>{{ $latestData->minggu }}</option>
                            </select>
                            <span class="tooltiptext">Kolom ini terisi otomatis sesuai dengan yang di submit
                                sebelumnnya</span>
                            <label for="minggu">Minggu ke- <span class="text-danger">*</span></label>
                            @error('minggu')
                                <div class="alert alert-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text mb-3"></div>
                        </div>

                        <div class="form-floating tooltipa">
                            <select id="jenis" name="jenis" class="form-select form-control-lg"
                                aria-label="select jenis" required readonly>
                                <option>{{ $latestData->jenis }}</option>
                                @foreach ($komponen as $komponen)
                                    <option value="{{ $komponen->jenis }}">{{ $komponen->jenis }}</option>
                                @endforeach
                            </select>
                            <span class="tooltiptext">Kolom ini terisi otomatis sesuai dengan yang di submit
                                sebelumnnya</span>
                            <label for="jenis"> Pilih Jenis <span class="text-danger"> *</span></label>
                            <div class="form-text mb-3"></div>
                        </div>

                        <div id="container">
                            <textarea name="pertanyaan[]" id="pertanyaan" placeholder="Masukkan Soal" style="height: 100px">{{ old('pertanyaan') }}</textarea>
                        </div>

                        <div class="form-floating tooltipa">
                            <input name="bobotSoal[]" id="bobotSoal" type="number" min="1" max="100"
                                class="form-control" autocomplete="off" placeholder="BobotSoal" required>
                            <span class="tooltiptext">Bobot untuk pertanyaan ini saja</span>
                            <label>Bobot Soal (%)<span class="text-danger ">*</span></label>
                            <div class="form-text mb-3"></div>
                        </div>

                        <div class="form-floating tooltipa">
                            <select class="form-control" name="cpl[]" required>
                                <option selected="true" value="" selected>-</option>
                                @foreach ($cpls as $cpl)
                                    <option value="{{ $cpl->id }}">{{ $cpl->judul }}</option>
                                @endforeach
                            </select>
                            <span class="tooltiptext">Pilih CPL yang sesuai dengan soal</span>
                            <label>Pilih CPL <span class="text-danger">*</span></label>
                            <div class="form-text mb-3"></div>
                        </div>

                        <div class="form-floating tooltipa">
                            Pilih CPMK
                            <span class="tooltiptext">Pilih CPMK yang sesuai dengan soal</span>
                            <select name="cpmk[]" class="js-example-basic-multiple form-select form-control-lg"
                                multiple="multiple" required>
                                @foreach ($rpss as $rps)
                                    @if($rps->mk)
                                    @foreach ($rps->mk->cpmk as $cpmk)
                                        <option value="{{ $cpmk->id }}">{{ $cpmk->judul }}</option>
                                    @endforeach
                                    @endif
                                @endforeach
                            </select>
                            <div class="form-text mb-3"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-5">
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('/assets/template/vendors/select2/select2.min.js') }}"></script>
    <script src="{{ asset('/assets/template/js/select2.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#pertanyaan'))
            .catch(error => {
                console.error(error);
            });
    </script>
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
    <script>
        document.querySelectorAll('#kurikulum, #prodi, #kode_mk, #minggu, #jenis').forEach(function(element) {
            element.addEventListener('mousedown', function(event) {
                event.preventDefault();
            });
        });
    </script>
@endsection
