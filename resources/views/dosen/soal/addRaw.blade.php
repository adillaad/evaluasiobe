{{-- @php
    $currentPrefix = auth()->user()->otoritas->otoritas
        ? str_replace(' ', '-', strtolower(auth()->user()->otoritas->otoritas)) . '.'
        : 'admin.';
@endphp --}}
@extends('dosen.template')
@section('content')
    <style>
        #container {
            width: 900px;
            margin: 20px auto;
        }

        .ck-editor__editable[role="textbox"] {
            min-height: 200px;
        }

        .ck-content .image {
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
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            text-align: center;
            border-radius: 8px;
            padding: 8px;
            position: absolute;
            z-index: 1;
            bottom: 0%;
            left: 90%;
            transform: translateX(-50%);
            transition: visibility 0.3s ease-in-out;
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

                {{-- Flash Messages --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('dosen.rawSoal-store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="templatePenilaian">

                        <div class="form-floating tooltipa">
                            <select id="kurikulum" name="kurikulum" class="form-select form-control-lg" required>
                                <option selected="true" value="" selected>-</option>
                                @foreach ($kurikulum as $kur)
                                    <option value="{{ $kur->id }}"
                                        {{ old('kurikulum') == $kur->id ? 'selected' : '' }}>
                                        {{ $kur->tahun }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="tooltiptext">Pilih sesuai dengan kurikulum soal</span>
                            <label for="kurikulum"> Pilih Kurikulum <span class="text-danger"> *</span></label>
                            <div class="form-text mb-3"></div>
                        </div>

                        <div class="form-floating tooltipa">
                            <select id="prodi" name="prodi" class="form-select form-control-lg"
                                aria-label="select Prodi" required>
                                <option selected="true" value="" selected>-</option>
                                @foreach ($prodi as $p)
                                    <option value="{{ $p->id }}" {{ old('prodi') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="prodi"> Pilih Prodi <span class="text-danger"> *</span></label>
                            <div class="form-text mb-3"></div>
                        </div>

                        <div class="form-floating tooltipa">
                            <select id="kode_mk" name="kode_mk" class="form-select form-control-lg"
                                aria-label="select Mata Kuliah" required>
                                <option selected="true" value="" selected>-</option>
                                @foreach ($rpss as $rps)
                                    <option value="{{ $rps->kode_mk }}"
                                        {{ old('kode_mk') == $rps->kode_mk ? 'selected' : '' }}>
                                        {{ $rps->kode_mk }} - {{ $rps->mk?->nama ?? 'MK tidak ditemukan' }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="mataKuliah"> Pilih Mata Kuliah <span class="text-danger"> *</span></label>
                            <div class="form-text mb-3"></div>
                        </div>

                        <div class="form-floating tooltipa mb-3">
                            <span class="tooltiptext">Pilih CPL yang sesuai dengan soal</span>
                            <select class="form-select form-control-lg" name="cpl" id="cpls" required
                                disabled></select>
                            <label>Pilih CPL <span class="text-danger">*</span></label>
                        </div>

                        <div class="form-floating tooltipa">
                            <span class="tooltiptext">Pilih CPMK yang sesuai dengan soal</span>
                            <select name="cpmk" id="cpmks" class="form-select form-control-lg" required
                                disabled></select>
                            <label for="cpmks"> Pilih CPMK <span class="text-danger"> *</span></label>
                            <div class="form-text mb-3"></div>
                        </div>

                        <!-- JENIS / METODE -->
                        <div class="form-floating mb-3 tooltipa">
                            <select id="metode_id" name="metode_id" class="form-select form-control-lg" required disabled>
                                <option selected value="" disabled>-</option>
                            </select>
                            <span class="tooltiptext">Pilih jenis penilaian (Kuis, UTS, UAS, dll)</span>
                            <label>Jenis <span class="text-danger">*</span></label>
                        </div>

                        <!-- Bobot (readonly, informatif saja) -->
                        <div class="form-floating mb-3 tooltipa">
                            <input name="bobotSoal" id="bobot" type="number" class="form-control" readonly>
                            <span class="tooltiptext">Bobot otomatis terisi dari Jenis yang dipilih</span>
                            <label>Bobot Metode (%)<span class="text-danger">*</span></label>
                            <small class="text-muted">
                                Otomatis dari mapping. Pembagian bobot per soal dihitung saat download template di halaman
                                Download Template Penilaian.
                            </small>
                        </div>

                        <div class="form-floating tooltipa">
                            <input type="number" name="minggu" min="1" max="16" value="{{ old('minggu') }}"
                                class="form-control" placeholder="minggu" autocomplete="off" required>
                            <span class="tooltiptext">Minggu ke berapa soal ini diberikan ke mahasiswa</span>
                            <label for="minggu">Minggu ke- <span class="text-danger">*</span></label>
                            @error('minggu')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                            <div class="form-text mb-3"></div>
                        </div>

                        <div id="container">
                            <textarea name="pertanyaan" id="pertanyaan" class="form-control" placeholder="Masukkan Soal">{{ old('pertanyaan') }}</textarea>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-5">
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" value="Simpan Soal">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel soal yang sudah disimpan untuk MK + jenis ini -->
        <div class="card mt-4">
            <div class="card-header">
                <div class="fw-bold">
                    <h3 class="mt-3">Data Soal yang Sudah Diinputkan</h3>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip"
                        data-bs-placement="left"
                        title="Menampilkan soal yang sudah tersimpan untuk Mata Kuliah dan Jenis Penilaian yang dipilih"
                        style="float:right;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                            <path
                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="card-body" id="info-soal">
                <div class="table-responsive">
                    <table class="table table-hover" id="soalTable">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Jenis Penilaian</th>
                                <th>CPL</th>
                                <th>CPMK</th>
                                <th>Pertanyaan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="soalTableBody">
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Pilih Mata Kuliah dan Jenis Penilaian untuk melihat soal yang sudah diinputkan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('/assets/template/vendors/select2/select2.min.js') }}"></script>
    <script src="{{ asset('/assets/template/js/select2.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>

    <script>
        $(document).ready(function() {

            // =====================
            // CHAIN DROPDOWN
            // =====================
            $('#kode_mk').on('change', function() {
                loadCPL($(this).val());
                $('#cpmks').html('<option value="">-- Pilih CPL dulu --</option>').prop('disabled', true);
                $('#metode_id').html('<option value="" disabled>-</option>').prop('disabled', true);
                $('#bobot').val('');
                resetSoalTable();
            });

            $('#cpls').on('change', function() {
                loadCPMK($(this).val());
                $('#metode_id').html('<option value="" disabled>-</option>').prop('disabled', true);
                $('#bobot').val('');
            });

            $('#cpmks').on('change', function() {
                var cplId = $('#cpls').val();
                var cpmkId = $(this).val();
                if (cplId && cpmkId) {
                    loadMetode(cplId, cpmkId);
                }
                $('#bobot').val('');
            });

            $('#metode_id').on('change', function() {
                var bobot = $(this).find(':selected').data('bobot') || 0;
                $('#bobot').val(bobot);
                loadSoalTable();
            });

            // =====================
            // LOAD TABEL SOAL BAWAH (preview)
            // =====================
            function loadSoalTable() {
                var kode_mk = $('#kode_mk').val();
                var metode_id = $('#metode_id').val();
                if (!kode_mk || !metode_id) return;

                $('#soalTableBody').html(
                    '<tr><td colspan="6" class="text-center"><span class="spinner-border spinner-border-sm"></span> Memuat...</td></tr>'
                );

                $.get('/dosen/getSoalByMkJenis', {
                        kode_mk: kode_mk,
                        jenis: metode_id
                    })
                    .done(function(data) {
                        var soals = data.soals || [];
                        if (soals.length === 0) {
                            $('#soalTableBody').html(
                                '<tr><td colspan="6" class="text-center text-muted">Belum ada soal untuk kombinasi MK dan jenis ini.</td></tr>'
                            );
                            return;
                        }
                        var rows = '';
                        soals.forEach(function(s, i) {
                            var pertanyaan = s.pertanyaan ?
                                s.pertanyaan.replace(/<[^>]*>/g, '').substring(0, 100) + (s.pertanyaan
                                    .length > 100 ? '...' : '') :
                                '-';
                            var statusBadge = s.status === 'Valid' ?
                                '<span class="badge bg-success">Valid</span>' :
                                (s.status === 'Menunggu' ?
                                    '<span class="badge bg-warning text-dark">Menunggu</span>' :
                                    '<span class="badge bg-secondary">Belum</span>');
                            rows += '<tr>' +
                                '<td>' + (i + 1) + '</td>' +
                                '<td>' + (s.nama_metode || '-') + '</td>' +
                                '<td>' + (s.kode_cpl || '-') + '</td>' +
                                '<td>' + (s.kode_cpmk || '-') + '</td>' +
                                '<td>' + pertanyaan + '</td>' +
                                '<td>' + statusBadge + '</td>' +
                                '</tr>';
                        });
                        $('#soalTableBody').html(rows);
                    })
                    .fail(function() {
                        $('#soalTableBody').html(
                            '<tr><td colspan="6" class="text-center text-danger">Gagal memuat data soal.</td></tr>'
                        );
                    });
            }

            function resetSoalTable() {
                $('#soalTableBody').html(
                    '<tr><td colspan="6" class="text-center text-muted">Pilih Mata Kuliah dan Jenis Penilaian untuk melihat soal yang sudah diinputkan.</td></tr>'
                );
            }

            // =====================
            // CHAIN DROPDOWN FUNCTIONS
            // =====================
            function loadCPL(kode_mk) {
                var cplSelect = $('#cpls');
                cplSelect.html('<option>Loading...</option>').prop('disabled', true);

                $.get("{{ route('dosen.getCPLBykode_mk') }}", {
                        kode_mk: kode_mk
                    })
                    .done(function(data) {
                        if (data.cpls && data.cpls.length > 0) {
                            var options = '<option value="">-- Pilih CPL --</option>';
                            data.cpls.forEach(function(cpl) {
                                options += '<option value="' + cpl.id + '">' + cpl.kode + ' - ' + cpl
                                    .judul + '</option>';
                            });
                            cplSelect.html(options).prop('disabled', false);
                        } else {
                            cplSelect.html('<option value="">-- Tidak ada CPL --</option>');
                        }
                    })
                    .fail(function(xhr) {
                        alert('Gagal memuat CPL');
                        console.error(xhr.responseText);
                    });
            }

            function loadCPMK(cplId) {
                var kode_mk = $('#kode_mk').val();
                var cpmkSelect = $('#cpmks');
                cpmkSelect.html('<option>Loading...</option>').prop('disabled', true);

                $.get('/dosen/getCPMKBykode_mk/' + cplId, {
                        kode_mk: kode_mk
                    })
                    .done(function(data) {
                        if (data.cpmks && data.cpmks.length > 0) {
                            var options = '<option value="">-- Pilih CPMK --</option>';
                            data.cpmks.forEach(function(cpmk) {
                                options += '<option value="' + cpmk.id + '">' + cpmk.kode + ' - ' + cpmk
                                    .judul + '</option>';
                            });
                            cpmkSelect.html(options).prop('disabled', false);
                        } else {
                            cpmkSelect.html('<option value="">-- Tidak ada CPMK --</option>');
                        }
                    })
                    .fail(function() {
                        alert('Gagal memuat CPMK');
                    });
            }

            function loadMetode(cplId, cpmkId) {
                var kode_mk = $('#kode_mk').val();
                var metodeSelect = $('#metode_id');
                metodeSelect.html('<option value="">Loading...</option>').prop('disabled', true);

                $.ajax({
                    url: '/dosen/getJenisKriteria/' + cplId,
                    method: 'GET',
                    data: {
                        kode_mk: kode_mk,
                        cpmkId: cpmkId
                    },
                    success: function(data) {
                        if (data.dataKriteria && data.dataKriteria.length > 0) {
                            var options = '<option value="">-- Pilih Metode Penilaian --</option>';
                            data.dataKriteria.forEach(function(item) {
                                options += '<option value="' + item.metode_id +
                                    '" data-bobot="' + item.bobot + '">' +
                                    item.jenis + ' (' + item.bobot + '%)</option>';
                            });
                            metodeSelect.html(options).prop('disabled', false);
                        } else {
                            metodeSelect.html(
                                '<option value="">-- Tidak ada metode penilaian --</option>').prop(
                                'disabled', true);
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert('Gagal memuat metode penilaian.');
                    }
                });
            }
        });
    </script>
@endsection
