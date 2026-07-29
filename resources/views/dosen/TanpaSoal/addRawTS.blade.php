{{-- @php
    $currentPrefix = auth()->user()->otoritas->otoritas
        ? str_replace(' ', '-', strtolower(auth()->user()->otoritas->otoritas)) . '.'
        : 'admin.';
@endphp --}}
@extends('dosen.template')
@section('content')
    <style>
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
                    <h3>Tambah Instrumen Penilaian Tanpa Soal</h3>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="left"
                        title="Form ini digunakan untuk membuat instrumen penilaian non-soal (Presentasi, Laporan, Praktikum, dll)"
                        style="float:right;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                            <path
                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="card-body" id="tambah-tanpa-soal">

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

                <form action="{{ route($currentPrefix . 'storeRawTS') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="templateTanpaSoal">

                        <!-- Kurikulum -->
                        <div class="form-floating tooltipa">
                            <select id="kurikulum" name="kurikulum" class="form-select form-control-lg" required>
                                <option selected="true" value="" disabled>-</option>
                                @foreach ($kurikulum as $kur)
                                    <option value="{{ $kur->id }}"
                                        {{ old('kurikulum') == $kur->id ? 'selected' : '' }}>
                                        {{ $kur->tahun }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="tooltiptext">Pilih sesuai dengan kurikulum</span>
                            <label for="kurikulum"> Pilih Kurikulum <span class="text-danger"> *</span></label>
                            <div class="form-text mb-3"></div>
                        </div>

                        <!-- Prodi -->
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

                        <!-- Mata Kuliah -->
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

                        <!-- CPL (AJAX) -->
                        <div class="form-floating tooltipa mb-3">
                            <span class="tooltiptext">Pilih CPL yang sesuai</span>
                            <select class="form-control form-control-lg" name="cpl[]" id="cpls" required disabled>
                                <option value="">-- Pilih MK dulu --</option>
                            </select>
                            <label>Pilih CPL <span class="text-danger">*</span></label>
                        </div>

                        <!-- CPMK (AJAX) -->
                        <div class="form-floating tooltipa">
                            <span class="tooltiptext">Pilih CPMK yang sesuai</span>
                            <select name="cpmk[]" id="cpmks" class="form-select form-control-lg" required disabled>
                                <option value="">-- Pilih CPL dulu --</option>
                            </select>
                            <label for="cpmks"> Pilih CPMK <span class="text-danger"> *</span></label>
                            <div class="form-text mb-3"></div>
                        </div>

                        <!-- JENIS / METODE PENILAIAN -->
                        <div class="form-floating tooltipa">
                            <select id="jenis" name="jenis" class="form-select form-control-lg"
                                aria-label="select jenis" required disabled>
                                <option selected="true" value="" selected>-</option>
                            </select>
                            <span class="tooltiptext">Pilih jenis penilaian (Kuis, UTS, UAS, dll)</span>
                            <label for="jenis"> Pilih Jenis <span class="text-danger"> *</span></label>
                            <div class="form-text mb-3"></div>
                        </div>

                        <!-- Bobot (readonly, informatif saja) -->
                        <div class="form-floating tooltipa">
                            <input name="bobot_info" id="bobot" type="number" min="0" max="100"
                                class="form-control" placeholder="Bobot" readonly>
                            <span class="tooltiptext">Bobot metode untuk CPMK ini (otomatis dari mapping)</span>
                            <label>Bobot Metode pada CPMK (%)</label>
                            <small class="text-muted">
                                Otomatis dari mapping. Pembagian bobot per instrumen dihitung saat download template di
                                halaman Download Template Penilaian.
                            </small>
                            <div class="form-text mb-3"></div>
                        </div>

                    </div>

                    <!-- Tombol Submit -->
                    <div class="row mt-3">
                        <div class="col-5">
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" value="Simpan Instrumen">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Preview Instrumen yang Sudah Disimpan -->
        <div class="card mt-4">
            <div class="card-header">
                <div class="fw-bold">
                    <h3 class="mt-3">Data Instrumen Tanpa Soal yang Sudah Disimpan</h3>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip"
                        data-bs-placement="left" title="Bagian ini menampilkan instrumen tanpa soal yang sudah disimpan"
                        style="float:right;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                            <path
                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="card-body" id="info-tanpa-soal">
                <div class="table-responsive">
                    <table class="table table-hover" id="tanpaSoalTable">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Instrumen</th>
                                <th>CPL</th>
                                <th>CPMK</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="tanpaSoalTableBody">
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    Pilih Mata Kuliah dan Jenis untuk melihat instrumen yang sudah disimpan.
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
                $('#jenis').html('<option value="" selected>-</option>').prop('disabled', true);
                $('#bobot').val('');
                resetInstrumenTable();
            });

            $('#cpls').on('change', function() {
                loadCPMK($(this).val());
                $('#jenis').html('<option value="" selected>-</option>').prop('disabled', true);
                $('#bobot').val('');
            });

            $('#cpmks').on('change', function() {
                var cplId = $('#cpls').val();
                var cpmkId = $(this).val();
                if (cplId && cpmkId) {
                    loadJenis(cplId, cpmkId);
                }
                $('#bobot').val('');
            });

            $('#jenis').on('change', function() {
                var bobot = $(this).find(':selected').data('bobot') || 0;
                $('#bobot').val(bobot);
                loadInstrumenTable();
            });

            // =====================
            // LOAD TABEL INSTRUMEN BAWAH (preview)
            // =====================
            function loadInstrumenTable() {
                var kode_mk = $('#kode_mk').val();
                var metode_id = $('#jenis').val();
                if (!kode_mk || !metode_id) return;

                $('#tanpaSoalTableBody').html(
                    '<tr><td colspan="5" class="text-center"><span class="spinner-border spinner-border-sm"></span> Memuat...</td></tr>'
                );

                $.get("{{ route($currentPrefix . 'getInstrumenTanpaSoal') }}", {
                        kode_mk: kode_mk,
                        jenis: metode_id
                    })
                    .done(function(data) {
                        var items = data.data || [];
                        if (items.length === 0) {
                            $('#tanpaSoalTableBody').html(
                                '<tr><td colspan="5" class="text-center text-muted">Belum ada instrumen untuk MK dan jenis ini.</td></tr>'
                            );
                            return;
                        }
                        var rows = '';
                        items.forEach(function(item, i) {
                            rows += '<tr>' +
                                '<td>' + (i + 1) + '</td>' +
                                '<td>' + (item.nama_instrumen || '-') + '</td>' +
                                '<td>' + (item.cpl_kode || '-') + '</td>' +
                                '<td>' + (item.cpmk_kode || '-') + '</td>' +
                                '<td><span class="badge bg-secondary">Draft</span></td>' +
                                '</tr>';
                        });
                        $('#tanpaSoalTableBody').html(rows);
                    })
                    .fail(function() {
                        $('#tanpaSoalTableBody').html(
                            '<tr><td colspan="5" class="text-center text-danger">Gagal memuat data instrumen.</td></tr>'
                        );
                    });
            }

            function resetInstrumenTable() {
                $('#tanpaSoalTableBody').html(
                    '<tr><td colspan="5" class="text-center text-muted">Pilih Mata Kuliah dan Jenis untuk melihat instrumen yang sudah disimpan.</td></tr>'
                );
            }

            // =====================
            // CHAIN DROPDOWN FUNCTIONS
            // =====================
            function loadCPL(kode_mk) {
                var cplSelect = $('#cpls');
                cplSelect.html('<option>Loading...</option>').prop('disabled', true);

                $.get("{{ route($currentPrefix . 'getCPLBykode_mk') }}", {
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

                $.get("{{ url('dosen/getCPMKBykode_mk') }}/" + cplId, {
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

            function loadJenis(cplId, cpmkId) {
                var kode_mk = $('#kode_mk').val();
                var jenisSelect = $('#jenis');
                jenisSelect.html('<option value="">Loading...</option>').prop('disabled', true);

                $.ajax({
                    url: '/dosen/getJenisKriteria/' + cplId,
                    method: 'GET',
                    data: {
                        kode_mk: kode_mk,
                        cpmkId: cpmkId
                    },
                    success: function(data) {
                        if (data.dataKriteria && data.dataKriteria.length > 0) {
                            var options = '<option value="">-- Pilih Jenis Penilaian --</option>';
                            data.dataKriteria.forEach(function(item) {
                                options += '<option value="' + item.metode_id +
                                    '" data-bobot="' + item.bobot + '">' +
                                    item.jenis + ' (' + item.bobot + '%)</option>';
                            });
                            jenisSelect.html(options).prop('disabled', false);
                        } else {
                            jenisSelect.html(
                                '<option value="">-- Tidak ada jenis penilaian --</option>').prop(
                                'disabled', true);
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert('Gagal memuat jenis penilaian.');
                    }
                });
            }
        });
    </script>
@endsection
