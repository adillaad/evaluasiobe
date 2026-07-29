@php
    $currentPrefix = auth()->user()->otoritas->otoritas
        ? str_replace(' ', '-', strtolower(auth()->user()->otoritas->otoritas)) . '.'
        : 'admin.';
@endphp
@extends('dosen.template')
@section('content')
    @if (session()->has('failed'))
        <div class="alert alert-danger" role="alert" id="box">
            <div>{{ session('failed') }}</div>
        </div>
    @elseif (session()->has('success'))
        <div class="alert greenAdd" role="alert" id="box">
            <div>{{ session('success') }}</div>
        </div>
    @endif
    <style>
        .table-responsive table td.wrap-content {
            white-space: normal;
        }

        ol {
            max-height: 200px;
            overflow-y: auto;
            padding-left: 20px;
        }

        ol li {
            margin: 5px 0;
        }
    </style>
    <h3 class="px-4 pb-4 fw-bold text-center">Halaman Visualisasi CPL Per Mahasiswa</h3>
    <h6 class="px-4 pb-4 fw-bold text-center">Silahkan masukan data Mahasiswa</h6>
    <div class="form-group stretch-card" id="tugas">
        <div class="card">
            <div class="card-body">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="left"
                    title="Hasil dari form ini adalah visualisasi CPL per mahasiswa. Mohon pilih angkatan serta npm untuk menampilkan hasil visualisasi CPL mahasiswa serta informasi ketercapaian CPL"
                    style="float:right;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                        <path
                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                    </svg>
                </button>
                <h6 class="pb-4">Visualisasi CPL Per Mahasiswa</h6>
                <form id="hasilVisual" method="POST" action="hasilvisual-mahasiswa" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <div class="col-6">
                            <div class="form-group">
                                <input type="hidden" name="universitas" id="universitas" value="{{ $universitas->id }}">
                                <label id="prodiLabel">Prodi <span class="text-danger">*</span></label>
                                    <select id="prodiForm" class="form-control" name="prodi" required>
                                        <option value="">Silahkan Pilih Terlebih Dahulu</option>
                                        @foreach ($prodi as $p)
                                            <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                        @endforeach
                                    </select>
                                <label>Angkatan <span class="text-danger">*</span></label>
                                <select id="angkatanForm" class="form-control" name="angkatan">
                                    {{-- Opsi ditampilkan pake ajax --}}
                                </select>
                                <label>NPM <span class="text-danger mt-2">*</span></label>
                                <select id="npm" class="form-control" name="npm">
                                    {{-- Opsi ditampilkan pake ajax --}}
                                </select>
                            </div>
                            <input type="submit" class="btn btn-primary" value="Submit">
                            <button id="btnPrintPdf" type="button" class="btn btn-primary" disabled>Print PDF</button>
                        </div>
                        <div class="col-6">
                            @if ($universitas->img)
                                <img id="universitas-img" src="{{ asset($universitas->img) }}" class="img img-responsive"
                                    style="max-width: 35%; margin-left: 100px;" />
                                <input type="hidden" id="universitas-img-path" value="{{ asset($universitas->img) }}" />
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Ini div yang mau dihilangkan jika belum ada data --}}
    {{-- Hasil visualisasi --}}
    <div id="visualContainer" style="display:none;">
        <div class="form-group stretch-card" id="tugas">
            <div class="card">
                <div class="card-body">
                    <div class="container">
                        {{-- Card 7 --}}
                        <div class="card mt-3">
                            <div class="card-body">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip"
                                    data-bs-placement="left"
                                    title="Hasil dari form ini adalah visualisasi CPMK per mahasiswa. Mohon pilih mata kuliah untuk menampilkan visualisasi CPMK mahasiswa serta informasi ketercapaian CPMK"
                                    style="float:right;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                                    </svg>
                                </button>
                                <h5 class="card-title">Lihat CPMK</h5>
                                <form id="hasilvisualcpmk-mahasiswa" method="POST" action="hasilvisualcpmk-mahasiswa"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div id="dynamicAddRemoveTugas">
                                        <div class="form-group row">
                                            <div class="col-5">
                                                <div class="form-group">
                                                    <label for="course">Course <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="course" id="courseSelect">
                                                        {{-- Dari ajax --}}
                                                    </select>
                                                    @error('course')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                    <input type="text" name="npm" class="visually-hidden" value="">
                                                    <input type="text" name="nama" class="visually-hidden" value="">
                                                    <input type="text" name="angkatan" class="visually-hidden" value="">
                                                    <input type="text" name="prodi" class="visually-hidden" value="">
                                                    <input type="text" name="universitasImg" class="visually-hidden" value="">
                                                    <input type="text" name="universitasCPMK" class="visually-hidden" value="">
                                                    {{-- <input type="text" name="allNpm" class="visually-hidden" value="">      --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="submit" class="btn btn-primary"
                                        style="margin-top:-4%; margin-bottom:-1%" value="Submit">
                                </form>
                            </div>
                        </div>
                        {{-- kiri --}}
                        <div class="mt-4">
                            <div class="row">
                                <div class="col-sm-12">
                                    {{-- CARD 01 --}}
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Achievement <span
                                                    style="text-transform: lowercase;">of</span> CPL based <span
                                                    style="text-transform: lowercase;">on</span> Courses (%)</h5>
                                            <canvas id="radarChartCapaianCpl"></canvas>
                                            {{-- TODO: --}}
                                            {{-- <h6 class="mt-4" style="text-align:justify">Summary :</h6>
                                        <p id="summary"></p> --}}
                                            <h6 class="keterangan mt-4">Mata Kuliah Lulus :</h6>
                                            <div id="labelMkLulus" style="max-height: 200px; overflow: auto;">
                                                {{-- Dari AJAX --}}
                                            </div>
                                            <h6 class="keteranganTidakLulus mt-4">Mata Kuliah Tidak Lulus :</h6>
                                            <div id="labelMkTidakLulus" style="max-height: 200px; overflow: auto;">
                                                {{-- Dari AJAX --}}
                                            </div>
                                            <h6 class="keterangan mt-4">Pemetaan CPL :</h6>
                                            <div class="card">
                                                <div class="card-body">
                                                    <label>Pilih CPL <span class="text-danger mt-2">*</span></label>
                                                    <select id="selectPemetaanCpl" class="form-control"
                                                        name="selectPemetaanCpl">
                                                        <option value="">Pilih CPL</option>
                                                    </select>
                                                    <div id="kontenPemetaanCpl" class="overflow-auto p-3"
                                                        style="max-height: 200px;  overflow: auto;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- CARD 1 --}}
                                    <div class="card mt-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Achievement <span
                                                    style="text-transform: lowercase;">of</span> CPL Scores</h5>
                                            <canvas id="radarChart"></canvas>
                                            {{-- TODO: --}}
                                            {{-- <h6 class="mt-4" style="text-align:justify">Summary :</h6>
                                        <p id="summary"></p> --}}
                                            <h6 class="keterangan mt-3">Descriptions :</h6>
                                            {{-- Dari AJAX --}}
                                            <ol id="labelContainer" class= "overflow-auto"
                                                style="max-height: 200px; overflow: auto;">
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                                {{-- Kiri bawah --}}
                                {{-- CARD 3 --}}
                                <div class="col-sm-6">
                                    {{-- CARD 3 --}}
                                    <div class="card mt-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Questions <span class="text-lowercase">with</span> the
                                                Lowest CPL :</h5>
                                            <div class="table-responsive overflow-auto"
                                                style="max-height: 300px; overflow: auto;">
                                                <table class="table table-bordered" id="soalTerendahTable">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Course Name</th>
                                                            <th>Types of Assessment</th>
                                                            <th>Questions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {{-- Td dari ajax --}}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Card 5 --}}
                                    <div class="card mt-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Career Mapping Graph Based <span
                                                    class="text-lowercase">on</span> CPL :</h5>
                                            <canvas id="profilChart" height="300"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    {{-- Card 4 --}}
                                    <div class="card mt-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Data :</h5>
                                            <ol>
                                                <li>
                                                    <h6 id="nama" class="card-subtitle mt-2 text-black"></h6>
                                                </li>
                                                <li>
                                                    <h6 id="npmData" class="card-subtitle mt-2 text-black"></h6>
                                                </li>
                                                <li>
                                                    <h6 id="angkatanData" class="card-subtitle mt-2 text-black"></h6>
                                                </li>
                                                <li>
                                                    <h6 id="prodi" class="card-subtitle mt-2 text-black"></h6>
                                                </li>
                                                <li>
                                                    <h6 id="universitasData" class="card-subtitle mt-2 text-black"></h6>
                                                </li>
                                            </ol>
                                            <h6 class="fw-bold">All courses taken :</h6>
                                            <ol id="courseList" class= "overflow-auto"
                                                style="max-height: 200px; overflow: auto;"></ol>
                                        </div>
                                    </div>
                                    {{-- Card 6 --}}
                                    <div class="card mt-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Career Mapping Details Based <span
                                                    class="text-lowercase">on</span> CPL :</h5>
                                            <div class="table-responsive overflow-auto"
                                                style="max-height: 400px; overflow: auto;">
                                                <table id="hasilProfilTable" class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Profile Career</th>
                                                            <th>Graduate Profile</th>
                                                            <th>CPL</th>
                                                            <th>Profile Weight</th>
                                                            <th>CPL Result</th>
                                                            <th>Profile Weight*The CPL Result</th>
                                                            <th>Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {{-- Td dari ajax --}}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Batas div yang mau dihilangkan jika belum ada data --}}
    <!-- Script jQuery -->
    {{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}
    {{-- script select option --}}
    {{-- <script>
        $(document).ready(function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Get the university ID from the hidden input
            var universitas = $('#universitas').val();

            $('#universitas').on('change', function() {
                var universitas = $(this).val();

                var imgSrc = $(this).find(':selected').data('img');
                console.log('Image source:', imgSrc);
                // Update image based on selected universitas
                if (imgSrc) {
                    $('#universitas-img').attr('src', imgSrc).show();
                } else {
                    $('#universitas-img').hide();
                }

                $.ajax({
                    url: "{{ route($currentPrefix . 'getAngkatanByUniversitas') }}", // route untuk kirim ke kontroler
                    method: 'GET',
                    data: {
                        universitas: universitas
                    },
                    success: function(data) {
                        console.log(data);
                        $('#angkatan').html(data);
                    }
                });
            });
        });
    </script> --}}
    {{-- MILIH PEMETAAN CPL --}}
    <script>
        $(document).ready(function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            function loadAngkatan(prodi, universitas) {
                $.ajax({
                    url: "{{ route($currentPrefix . 'getAngkatanByProdiUniversitas') }}",
                    method: 'GET',
                    data: {
                        prodi: prodi,
                        universitas: universitas
                    },
                    success: function(data) {
                        console.log(data);
                        console.log(universitas);
                        $('#angkatanForm').html(data);
                    }
                });
            }

            $('#prodiForm').on('change', function() {
                var prodi = $(this).val();
                var universitas = $('#universitas').val();
                loadAngkatan(prodi, universitas);
            });

            @if ($userOtoritas == 'Kepala Program Studi' || $userOtoritas == 'Penjamin Mutu Program Studi')

                var userProdi = "{{ auth()->user()->id_prodiUser }}";
                $('#prodiForm').val(userProdi);
                $('#prodiForm').prop('disabled', true);
                $('#prodiLabel').prop('hidden', true);
                $('#prodiForm').prop('hidden', true);

                // Manually trigger angkatan loading
                var universitas = $('#universitas').val();
                loadAngkatan(userProdi, universitas);
            @endif
        });
    </script>
    <script>
        $(document).ready(function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            $('#selectPemetaanCpl').on('change', function() {
                var idCpl = $(this).val();
                // console.log("ini:"+idCpl);
                $.ajax({
                    url: "{{ route($currentPrefix . 'getPemetaanCpl') }}", // route untuk kirim ke kontroler
                    method: 'GET',
                    data: {
                        idCpl: idCpl,
                    },
                    success: function(data) {
                        console.log(data);
                        var content = '<ol>';
                        data.forEach(function(item) {
                            content += '<li>' + item.mk_kode + '-' + item.nama +
                                '</li>';
                        });
                        content += '</ol>';
                        $('#kontenPemetaanCpl').html(content);
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            $('#angkatanForm').on('change', function() {
                var angkatan = $(this).val();
                var universitas = $('#universitas').val(); // Ambil nilai dari elemen #universitas
                $.ajax({
                    url: "{{ route($currentPrefix . 'getNpmByAngkatan') }}", // route untuk kirim ke kontroler
                    method: 'GET',
                    data: {
                        angkatan: angkatan,
                        universitas: universitas
                    },
                    success: function(data) {
                        console.log(data);
                        $('#npm').html(data);
                    }
                });
            });
        });
    </script>

    {{-- script hide konten --}}
    <script>
        $(document).ready(function() {
            $('#hasilVisual').on('submit', function(event) {
                event.preventDefault();
                // console.log('Form submitted!');
                var universitas = $('#universitas').val();
                var angkatan = $('#angkatan').val();
                var imgSrc = $('#universitas-img').attr('src');
                var formData = $(this).serialize();
                // console.log(formData);
                if (imgSrc) {
                    formData += '&imgSrc=' + encodeURIComponent(imgSrc);
                }
                // Kirim permintaan AJAX
                $.ajax({
                    url: "{{ route($currentPrefix . 'hasilvisual-mahasiswa') }}",
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        console.log('AJAX success!');
                        console.log(response);

                        // Ambil data dari respons
                        var labelCpl = response.result.labelCpl;
                        var soalTerendah = response.result.soalTerendah;
                        // var allNpm = response.result.allNpm;

                        // ABIS SEMHAS
                        var nama = response.result.nama;
                        var npm = response.result.npm;
                        var angkatan = response.result.angkatan;
                        var imgSrc = response.result.imgSrc;
                        var prodi = response.result.prodi;
                        var universitas = response.result.universitas;
                        var nilaiMkLulus = response.result.nilaiMkLulus;
                        var nilaiMkTidakLulus = response.result.nilaiMkTidakLulus;
                        var persentaseTotalCplCapaian = response.result
                            .persentaseTotalCplCapaian;
                        var cplResultsAll = response.result.cplResultsAll;
                        var cplTmp = response.result.cplTmp;
                        var allCplPerAngkatan = response.result.allCplPerAngkatan;
                        var cplPerSoalWithKodeAll = response.result.cplPerSoalWithKodeAll;
                        var hasilFinalProfil = response.result.hasilFinalProfil;
                        var chartDataProfil = response.result.chartDataProfil;
                        var courseArray = response.result.courseArray;
                        var courseCpmkRestrict = response.result.courseCpmkRestrict;

                        // SUMMARY
                        // Hasil profil
                        $('#hasilProfilTable tbody').empty();
                        // Iterasi hasilFinalProfil
                        var nomor = 1;
                        $.each(hasilFinalProfil, function(key, profil) {
                            var totalRows = profil.CPLs.length;
                            var row = '<tr>';
                            row += '<td rowspan="' + totalRows + '">' + nomor + '</td>';
                            row += '<td rowspan="' + totalRows + '">' + profil
                                .NamaProfil + '</td>';
                            row += '<td rowspan="' + totalRows +
                                '" class="wrap-content">' + profil.Deskripsi +
                                '</td>'; // Tambahkan kelas wrap-content di sini

                            $.each(profil.CPLs, function(index, cpl) {
                                row += '<td>' + cpl.CPL + '</td>';
                                row += '<td>' + cpl.Bobot + '</td>';
                                row += '<td>' + cpl.HasilCPL + '</td>';
                                row += '<td>' + (cpl.Bobot * cpl.HasilCPL)
                                    .toFixed(3) + '</td>';

                                if (index === 0) {
                                    row += '<td rowspan="' + totalRows + '">' +
                                        profil.TotalAkhir.toFixed(3) + '</td>';
                                }

                                row += '</tr>';

                                if (index < totalRows - 1) {
                                    row += '<tr>';
                                }
                            });
                            nomor++;
                            $('#hasilProfilTable tbody').append(row);
                        });
                        // Tampilkan data di halaman 
                        $('#nama').text('Nama: ' + nama);
                        $('#npmData').text('NPM: ' + npm);
                        $('#angkatanData').text('Angkatan: ' + angkatan);
                        $('#prodi').text('Prodi: ' + prodi);
                        $('#universitasData').text('Universitas: ' + universitas);

                        // set di input hidden
                        $('input[name="npm"]').val(npm);
                        $('input[name="nama"]').val(nama);
                        $('input[name="angkatan"]').val(angkatan);
                        $('input[name="prodi"]').val(prodi);
                        $('input[name="universitasImg"]').val(imgSrc);
                        $('input[name="universitasCPMK"]').val(universitas);
                        //$('input[name="allNpm"]').val(JSON.stringify(allNpm));

                        // LABEL MK LULUS & TIDAK LULUS START
                        $('#labelMkLulus').html(
                            '<table class="table table-bordered"><thead><tr><th width="80px">#</th><th>Mata Kuliah</th><th>Kode</th><th>Nilai</th></tr></thead><tbody></tbody></table>'
                            );

                        if (!nilaiMkLulus || Object.keys(nilaiMkLulus).length === 0) {
                            // Show empty state message
                            $('#labelMkLulus table tbody').append(
                                '<tr><td colspan="4" class="text-center">Data Kosong</td></tr>'
                                );
                        } else {
                            var indeksMK = 1;
                            $.each(nilaiMkLulus, function(course, nilai) {
                                // Split course and code from the course variable
                                var courseData = course.split('-');
                                var courseName = courseData[0] || course;
                                var courseCode = nilai[1] || '';

                                $('#labelMkLulus table tbody').append('<tr>' +
                                    '<td>' + indeksMK + '</td>' +
                                    '<td>' + courseName + '</td>' +
                                    '<td>' + courseCode + '</td>' +
                                    '<td><strong>' + nilai[0] + '</strong></td>' +
                                    '</tr>');
                                indeksMK += 1;
                            });
                        }

                        // For labelMkTidakLulus - Always show the table
                        $('.keteranganTidakLulus').show();
                        $('.cardTidakLulus').show();

                        // Create table structure
                        $('#labelMkTidakLulus').html(
                            '<table class="table table-bordered"><thead><tr><th width="80px">#</th><th>Mata Kuliah</th><th>Kode</th><th>Nilai</th></tr></thead><tbody></tbody></table>'
                            );

                        if (!nilaiMkTidakLulus || Object.keys(nilaiMkTidakLulus).length === 0) {
                            // Show empty state message
                            $('#labelMkTidakLulus table tbody').append(
                                '<tr><td colspan="4" class="text-center">Data Kosong</td></tr>'
                                );
                        } else {
                            var indeksMK = 1;
                            $.each(nilaiMkTidakLulus, function(course, nilai) {
                                // Split course and code from the course variable
                                var courseData = course.split('-');
                                var courseName = courseData[0] || course;
                                var courseCode = nilai[1] || '';

                                $('#labelMkTidakLulus table tbody').append('<tr>' +
                                    '<td>' + indeksMK + '</td>' +
                                    '<td>' + courseName + '</td>' +
                                    '<td>' + courseCode + '</td>' +
                                    '<td><strong>' + nilai[0] + '</strong></td>' +
                                    '</tr>');
                                indeksMK += 1;
                            });
                        }
                        // LABEL MK LULUS & TIDAK LULUS END

                        // isi option pemetaan cpl
                        var $select = $('#selectPemetaanCpl');
                        $select.empty();
                        $select.append($('<option>', {
                            value: '',
                            text: 'Pilih CPL'
                        }));
                        $.each(cplResultsAll, function(index, cpl) {
                            $select.append($('<option>', {
                                value: cpl.id,
                                text: cpl.kode
                            }));
                        });

                        // isi label container
                        var labelContainer = $('#labelContainer');

                        labelContainer
                            .empty(); // Mengosongkan kontainer sebelum menambahkan elemen baru

                        labelCpl.forEach(function(item) {
                            var listItem = $('<li>').text(item.kode + ': ' + item
                                .judul);
                            labelContainer.append(listItem);
                        });

                        // Isi tabel
                        var tableBody = $('#soalTerendahTable tbody');
                        tableBody.html('');
                        for (var i = 0; i < soalTerendah.length; i++) {
                            var rowHtml = '<tr>' + '<td>' + (i + 1) + '</td>' + '<td>' +
                                soalTerendah[i].namaCourse + '</td>' + '<td>' + soalTerendah[i]
                                .Jenis + '</td>';

                            // Lakukan pengecekan apakah idSoal ada atau tidak
                            if (soalTerendah[i].idSoal) {
                                rowHtml +=
                                    '<td><a href="http://127.0.0.1:8000/dosen/soal/cetakSoal/' +
                                    soalTerendah[i].idSoal + '" target="_blank">' +
                                    soalTerendah[i].soal + '</a></td>';
                            } else {
                                rowHtml += '<td>' + soalTerendah[i].soal + '</td>';
                            }

                            rowHtml += '</tr>';
                            tableBody.append(rowHtml);
                        }

                        //course list ajax 
                        var courseList = $('#courseList');
                        courseList.html('');
                        for (var course in courseArray) {
                            var listItem = '<li>' + course + ' - ' + courseArray[course] +
                                '</li>';
                            courseList.append(listItem);
                        }

                        // Untuk di form optional untuk pilih cpmk
                        // Menambahkan opsi ke elemen <select>
                        var selectElement = $('#courseSelect');
                        selectElement.html('');

                        var defaultOptionHtml = '<option value="">Select Course</option>';
                        selectElement.append(defaultOptionHtml);

                        for (var course in courseCpmkRestrict) {
                            var optionHtml = '<option value="' + course + '-' +
                                courseCpmkRestrict[course] + '">' + course + ' - ' +
                                courseCpmkRestrict[course] + '</option>';
                            selectElement.append(optionHtml);
                        }

                        // Tampilkan konten
                        $('#visualContainer').show();

                        // CHART PERSENTASE CAPAIAN CPL
                        var labelsCapaianCpl = Object.values(cplTmp).map(item => item[1]);
                        var dataCapaianCpl = Object.values(cplTmp).map(item => item[0]);

                        var dataMinCpl = Object.values(allCplPerAngkatan.min);
                        var dataAvgCpl = Object.values(allCplPerAngkatan.avg_cpl);
                        var dataMaxCpl = Object.values(allCplPerAngkatan.max);

                        // REVISI SEMHAS
                        var canvas = document.getElementById('radarChartCapaianCpl');
                        var ctx = canvas.getContext('2d');
                        var radarChart = new Chart(ctx, {
                            type: 'radar',
                            data: {
                                labels: labelsCapaianCpl,
                                datasets: [{
                                        label: 'CPL',
                                        data: dataCapaianCpl,
                                        backgroundColor: 'rgba(75, 192, 192, 0)',
                                        borderColor: 'rgba(75, 192, 192, 0.3)',
                                        borderWidth: 3,
                                        pointBackgroundColor: 'rgba(75, 192, 192, 0.4)'
                                    },
                                    {
                                        label: 'Average CPL',
                                        data: dataAvgCpl,
                                        backgroundColor: 'rgba(192, 110, 75, 0)',
                                        borderColor: 'rgba(192, 110, 75, 0.3)',
                                        borderWidth: 3,
                                        pointBackgroundColor: 'rgba(192, 110, 75, 0.4)'
                                    },
                                    {
                                        label: 'Min CPL',
                                        data: dataMinCpl,
                                        backgroundColor: 'rgba(126, 0, 0, 0.2)',
                                        borderColor: 'rgba(216, 33, 33, 0.27)',
                                        borderWidth: 3,
                                        pointBackgroundColor: 'rgba(126, 0, 0, 0.4)'
                                    },
                                    {
                                        label: 'Max CPL',
                                        data: dataMaxCpl,
                                        backgroundColor: 'rgba(177, 255, 184, 0)',
                                        borderColor: 'rgba(33, 216, 95, 0.39)',
                                        borderWidth: 3,
                                        pointBackgroundColor: 'rgba(0, 0, 0, 1)'
                                    }
                                ]
                            },
                            options: {
                                scale: {
                                    ticks: {
                                        max: 100,
                                        min: 0
                                    }
                                },
                            }
                        });

                        function roundToTwo(num) {
                            return Math.round(num * 100) / 100;
                        }

                        // RADAR CHART CPL PER SOAL
                        var labelsCplPerSoal = Object.values(cplPerSoalWithKodeAll).map(item =>
                            item.kode);
                        var dataCplPerSoal = Object.values(cplPerSoalWithKodeAll).map(item =>
                            roundToTwo(item.hasil));
                        var canvas = document.getElementById('radarChart');
                        var ctx = canvas.getContext('2d');
                        var radarChart = new Chart(ctx, {
                            type: 'radar',
                            data: {
                                labels: labelsCplPerSoal,
                                datasets: [{
                                    label: 'CPL',
                                    data: dataCplPerSoal,
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scale: {
                                    ticks: {
                                        max: 100,
                                        min: 0
                                    }
                                },
                            }
                        });
                        // Karir chart
                        // Mendapatkan label dan data dari chartData
                        var labels = chartDataProfil.map(function(profil) {
                            return profil.label;
                        });

                        var data = chartDataProfil.map(function(profil) {
                            return profil.data;
                        });
                        var backgroundColors = chartDataProfil.map(function(_, index) {
                            var colors = ['rgba(75, 192, 192, 0.2)',
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(255, 205, 86, 0.2)', 'rgba(54, 162, 235, 0.2)'
                            ];
                            return colors[index % colors.length];
                        });
                        var ctx = document.getElementById('profilChart').getContext('2d');
                        var myChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Persentase',
                                    data: data,
                                    backgroundColor: backgroundColors,
                                    borderColor: backgroundColors.map(color =>
                                        color.replace('0.2', '1')),
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                        // Mengaktifkan tombol print setelah visualisasi tampil
                        $('#btnPrintPdf').prop('disabled', false);

                        document.getElementById("btnPrintPdf").addEventListener("click",
                            function() {
                                let nama = document.getElementById("nama").textContent
                                    .trim().replace(/^Nama[:\s]*/, "");
                                let npm = document.getElementById("npmData").textContent
                                    .trim().replace(/^NPM[:\s]*/, "");
                                let angkatan = document.getElementById("angkatanData")
                                    .textContent.trim().replace(/^Angkatan[:\s]*/, "");
                                let prodi = document.getElementById("prodi").textContent
                                    .trim().replace(/^Prodi[:\s]*/, "");
                                let universitas = document.getElementById("universitasData")
                                    .textContent.trim().replace(/^Universitas[:\s]*/, "");

                                let courseList = [];
                                document.querySelectorAll("#courseList li").forEach(li => {
                                    courseList.push(li.textContent.trim());
                                });

                                let radarChartCapaianCpl = document.getElementById(
                                    "radarChartCapaianCpl");
                                let radarChart = document.getElementById("radarChart");
                                let profilChart = document.getElementById("profilChart");

                                let radarChartCapaianCplImg = radarChartCapaianCpl ?
                                    radarChartCapaianCpl.toDataURL() : null;
                                let radarChartImg = radarChart ? radarChart.toDataURL() :
                                    null;
                                let profilChartImg = profilChart ? profilChart.toDataURL() :
                                    null;

                                let soalTerendah = [];
                                document.querySelectorAll("#soalTerendahTable tbody tr")
                                    .forEach(row => {
                                        let cols = row.querySelectorAll("td");
                                        soalTerendah.push({
                                            no: cols[0].textContent.trim(),
                                            course_name: cols[1].textContent
                                                .trim(),
                                            types_of_assessment: cols[2]
                                                .textContent.trim(),
                                            question: cols[3].textContent
                                                .trim(),
                                        });
                                    });

                                // Ganti kode pengambilan data hasilProfil dengan ini
                                let hasilProfil = [];
                                let currentNoValue = 1;

                                let hasilFinalProfilData = response.result.hasilFinalProfil;

                                Object.values(hasilFinalProfilData).forEach((profil) => {
                                    let profilData = {
                                        no: currentNoValue,
                                        profile_career: profil.NamaProfil,
                                        graduate_profile: profil.Deskripsi,
                                        total: profil.TotalAkhir.toFixed(3)
                                    };

                                    profil.CPLs.forEach((cpl, index) => {
                                        hasilProfil.push({
                                            no: profilData.no,
                                            profile_career: profilData
                                                .profile_career,
                                            graduate_profile: profilData
                                                .graduate_profile,
                                            cpl: cpl.CPL,
                                            profile_weight: cpl
                                                .Bobot,
                                            cpl_result: cpl
                                                .HasilCPL,
                                            profile_weight_cpl_result: (
                                                    cpl.Bobot * cpl
                                                    .HasilCPL)
                                                .toFixed(3),
                                            total: profilData.total,
                                            // Flag untuk menandakan baris pertama dari profile
                                            is_first_row: index ===
                                                0,
                                            // Total banyak CPL dalam profile ini
                                            total_rows: profil.CPLs
                                                .length
                                        });
                                    });

                                    currentNoValue++;
                                });

                                let mataKuliahLulus = [];
                                document.querySelectorAll("#labelMkLulus table tbody tr").forEach(row => {
                                    if (!row.querySelector("td.text-center")) { // Skip "Data Kosong" row
                                        let courseName = row.querySelector("td:nth-child(2)").textContent.trim();
                                        let courseCode = row.querySelector("td:nth-child(3)").textContent.trim();
                                        let nilai = row.querySelector("td:nth-child(4)").textContent.trim();
                                        mataKuliahLulus.push({
                                            courseName: courseName,
                                            courseCode: courseCode,
                                            nilai: nilai
                                        });
                                    }
                                });

                                let mataKuliahTidakLulus = [];
                                document.querySelectorAll("#labelMkTidakLulus table tbody tr").forEach(row => {
                                    if (!row.querySelector("td.text-center")) { // Skip "Data Kosong" row
                                        let courseName = row.querySelector("td:nth-child(2)").textContent.trim();
                                        let courseCode = row.querySelector("td:nth-child(3)").textContent.trim();
                                        let nilai = row.querySelector("td:nth-child(4)").textContent.trim();
                                        mataKuliahTidakLulus.push({
                                            courseName: courseName,
                                            courseCode: courseCode,
                                            nilai: nilai
                                        });
                                    }
                                });

                                let descriptions = [];
                                document.querySelectorAll("#labelContainer li").forEach(
                                    li => {
                                        descriptions.push(li.textContent.trim());
                                    });

                                fetch("{{ route($currentPrefix . 'generate-pdfVisualMahasiswa') }}", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/json",
                                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                        },
                                        body: JSON.stringify({
                                            nama,
                                            npm,
                                            angkatan,
                                            prodi,
                                            universitas,
                                            courseList,
                                            radarChartCapaianCplImg,
                                            radarChartImg,
                                            profilChartImg,
                                            soalTerendah,
                                            hasilProfil,
                                            mataKuliahLulus,
                                            mataKuliahTidakLulus,
                                            descriptions,
                                        }),
                                    }).then(response => {
                                        if (!response.ok) {
                                            return response.text().then(text => {
                                                throw new Error(
                                                    `HTTP ${response.status}: ${text}`
                                                );
                                            });
                                        }
                                        return response.blob();
                                    }).then(blob => {
                                        const url = URL.createObjectURL(blob);
                                        const a = document.createElement('a');
                                        a.href = url;
                                        a.download =
                                            `Laporan Visualisasi Mahasiswa - ${nama}.pdf`;
                                        document.body.appendChild(a);
                                        a.click();
                                        document.body.removeChild(a);
                                        URL.revokeObjectURL(url);
                                    })
                                    .catch(error => console.error("Error:", error.message));
                            });
                    },
                    error: function(xhr, status, error) {
                        console.log('AJAX error:');
                        console.log(xhr.responseText);
                    }
                });
            });
        });
    </script>

    {{-- coba validasi --}}
    <script>
        // Validasi CPL
        $(document).ready(function() {
            $('#hasilVisual').on('submit', function(event) {
                // Validasi select "course"
                var universitas = $('#universitas').val();
                var prodi = $('#prodiForm').val();
                var angkatan = $('#angkatanForm').val();
                var npm = $('#npm').val();

                if (!angkatan || !npm) {
                    alert('Mohon pilih/isi field yang kosong!');
                    event.preventDefault();
                }
            });
        });

        // Validasi CPMK
        $(document).ready(function() {
            $('#hasilvisualcpmk-mahasiswa').on('submit', function(event) {
                // Validasi select "course"
                var selectedCourse = $('#courseSelect').val();

                if (!selectedCourse) {
                    alert('Mohon pilih/isi field yang kosong!');
                    event.preventDefault();
                }
            });
        });
    </script>
@endsection
