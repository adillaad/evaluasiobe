{{-- @php
    $currentPrefix = auth()->user()->otoritas->otoritas
        ? str_replace(' ', '-', strtolower(auth()->user()->otoritas->otoritas)) . '.'
        : 'admin.';
@endphp --}}
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
        ol {
            max-height: 200px;
            overflow-y: auto;
            padding-left: 20px;
        }

        ol li {
            margin: 5px 0;
        }
    </style>
    <h3 class="px-4 pb-4 fw-bold text-center">Halaman Visualisasi CPL Per Angkatan</h3>
    <h6 class="px-4 pb-4 fw-bold text-center">Silahkan masukan data Angkatan</h6>
    <div class="form-group stretch-card" id="tugas">
        <div class="card">
            <div class="card-body">
                @if (in_array($userOtoritas, ['Dosen', 'Kepala Program Studi', 'Penjamin Mutu Program Studi']))
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip"
                        data-bs-placement="left"
                        title="Hasil dari form ini adalah visualisasi CPL per angkatan mahasiswa. Mohon memilih angkatan untuk menampilkan hasil visualisasi CPL per angkatan mahasiswa serta informasi ketercapaian CPL per angkatan mahasiswa"
                        style="float:right;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                            <path
                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                        </svg>
                    </button>
                @else
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip"
                        data-bs-placement="left"
                        title="Hasil dari form ini adalah visualisasi CPL per angkatan mahasiswa. Mohon memilih prodi serta angkatan untuk menampilkan hasil visualisasi CPL per angkatan mahasiswa serta informasi ketercapaian CPL per angkatan mahasiswa"
                        style="float:right;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                            <path
                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                        </svg>
                    </button>
                @endif
                <h6 class="pb-4 ">Visualisasi CPL Per Angkatan</h6>
                <form id="hasilVisual" method="POST" action="hasilvisual-mahasiswaAngkatan" enctype="multipart/form-data">
                    @csrf
                    <div id="dynamicAddRemoveTugas">
                        <div class="form-group row">
                            <div class="col-6">
                                <div class="form-group">
                                    <input type="hidden" name="universitas" id="universitas" value="{{ $universitas->id }}">
                                    <label id="prodiLabel">Prodi<span class="text-danger">*</span></label>
                                    <select id="prodiForm" class="form-control" name="prodi" required>
                                        <option value="">Silahkan Pilih Terlebih Dahulu</option>
                                        @foreach ($prodi as $p)
                                            <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                        @endforeach
                                    </select>
                                    <label>Angkatan<span class="text-danger">*</span></label>
                                    <select id="angkatanForm" class="form-control" name="angkatan">
                                        {{-- Opsi ditampilkan pake ajax --}}
                                    </select>
                                </div>
                                <input type="submit" class="btn btn-primary" value="Submit">
                                <button id="btnPrintPdf" type="button" class="btn btn-primary" disabled>Print PDF</button>
                            </div>
                            <div class="col-6">
                                @if ($universitas->img)
                                    <img id="universitas-img" src="{{ asset($universitas->img) }}"
                                        class="img img-responsive" style="max-width: 35%; margin-left: 100px;" />
                                    <input type="hidden" id="universitas-img-path"
                                        value="{{ asset($universitas->img) }}" />
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Hasil visualisasi --}}
    <div id="visualContainer" style="display:none;">
        <div class="form-group stretch-card" id="tugas">
            <div class="card">
                <div class="card-body">
                    <div class="container">
                        {{-- Card 2 --}}
                        <div class="card mt-3">
                            <div class="card-body">
                                <button type="button" class="btn btn-secondary btn-sm"
                                    data-bs-toggle="tooltip" data-bs-placement="left"
                                    title="Hasil dari form ini adalah visualisasi CPMK per angkatan mahasiswa. Mohon pilih mata kuliah untuk menampilkan hasil visualisasi CPMK per angkatan mahasiswa serta informasi ketercapaian CPMK per angkatan mahasiswa"
                                    style="float:right;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-info-circle-fill"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                                    </svg>
                                </button>
                                <h5 class="card-title">Lihat CPMK Angkatan</h5>
                                <form method="POST" id="hasilvisualcpmk-angkatan"
                                    action="hasilvisualcpmk-angkatan" enctype="multipart/form-data">
                                    @csrf
                                    <div id="dynamicAddRemoveTugas">
                                        <div class="form-group row">
                                            <div class="col-5">
                                                <div class="form-group">
                                                    <label>Course <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="course"
                                                        id="courseSelect">
                                                        <!-- Dari ajax -->
                                                    </select>
                                                    <input type="text" name="angkatan"
                                                        class="visually-hidden" value="">
                                                    <input type="text" name="prodi"
                                                        class="visually-hidden" id="prodiHidden"
                                                        value="">
                                                    <input type="text" name="universitasImg"
                                                        class="visually-hidden" value="">
                                                    <input type="text" name="universitasCPMK"
                                                        class="visually-hidden" value="">
                                                    {{-- <input type="text" name="allNpm" class="visually-hidden" value=""> --}}
                                                    {{-- <input type="text" name="allAngkatan" class="visually-hidden" value="">     --}}
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
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">CPL Achievement Percentage (%) (Batch)</h5>
                                            <canvas id="radarChartAngkatan"></canvas>
                                            {{-- <h6 class="mt-4" style="text-align:justify">Summary :</h6> --}}
                                            <p id="summary"></p>
                                            <h6 class="keterangan mt-4">Pemetaan CPL :</h6>
                                            <div class="card">
                                                <div class="card-body">
                                                    <label>Pilih CPL <span class="text-danger mt-2">*</span></label>
                                                    <select id="selectPemetaanCpl" class="form-control"
                                                        name="selectPemetaanCpl">
                                                    </select>
                                                    <div id="kontenPemetaanCpl">
                                                    </div>
                                                </div>
                                            </div>
                                            <h6 class="keterangan mt-3">Descriptions :</h6>
                                            {{-- Dari AJAX --}}
                                            <ol id="labelContainer" class= "overflow-auto"
                                                style="max-height: 200px; overflow: auto;">
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                                {{-- KIRI --}}
                                <div class="col-sm-6">
                                    {{-- CARD 3 --}}
                                    <div class="card mt-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Questions <span class="text-lowercase">with</span> the
                                                Lowest Average CPL :</h5>
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
                                </div>
                                {{-- Kanan --}}
                                <div class="col-sm-6">
                                    {{-- Card 1 --}}
                                    <div class="card mt-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Data :</h5>
                                            <ol>
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
                                            <h6 class="fw-bold">CPL Calculation Courses :</h6>
                                            <ol id="courseList">
                                                {{-- Dari ajax --}}
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Card Mahasiswa Angkatan --}}
                        <div class="card mt-3">
                            <div class="card-body">
                                <h5 id="mahasiswaAngkatanTitle" class="card-title">Mahasiswa Angkatan </h5>
                                <div class="table-responsive overflow-auto" style="max-height: 300px; overflow: auto;">
                                    <table class="table table-bordered" id="mahasiswaAngkatanTable">
                                        <thead>
                                            <tr>
                                                <th width="50px">No</th>
                                                <th>NPM</th>
                                                <th>Nama Mahasiswa</th>
                                                {{-- CPL headers will be dynamically added here --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Table body will be filled dynamically --}}
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {{-- AWAL FORM --}}
    {{-- <script>
        $(document).ready(function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

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
                    url: "{{ route($currentPrefix . 'getProdiByUniversitas') }}", // route untuk kirim ke kontroler
                    method: 'GET',
                    data: {
                        universitas: universitas
                    },
                    success: function(data) {
                        console.log(data);
                        $('#prodiForm').html(data);
                    }
                });
            });
        });
    </script> --}}
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
    {{-- BUAT ANGKATAN YG CPMKNYA ADA DI MUTUS --}}
    {{-- <script>
        $(document).ready(function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            $('#courseSelect').on('change', function() {
                var course = $(this).val();
                var prodi = $('#prodiHidden').val();
                var universitas = $('#universitas').val();
                console.log(universitas);
                $.ajax({
                    url: "{{ route('getAllAngkatanCpmk') }}", // route untuk kirim ke kontroler
                    method: 'GET',
                    data: {
                        course: course,
                        prodi: prodi,
                        universitas: universitas
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('AJAX suksess mantul!');
                        var allAngkatan = response.result.allAngkatan;
                        console.log(response);
                        $('input[name="allAngkatan"]').val(JSON.stringify(allAngkatan));

                    }
                });
            });
        });
    </script> --}}
    {{-- MILIH PEMETAAN CPL buat kelihat persebaran mk --}}
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
        document.getElementById("btnPrintPdf").addEventListener("click",
            function() {
                let radarChartAngkatan = document.getElementById("radarChartAngkatan");
                let radarChartAngkatanImg = radarChartAngkatan ? radarChartAngkatan.toDataURL() : null;

                let descriptions = [];
                document.querySelectorAll("#labelContainer li").forEach(
                    li => {
                        descriptions.push(li.textContent.trim());
                    }
                );

                let soalTerendah = [];
                document.querySelectorAll("#soalTerendahTable tbody tr")
                    .forEach(row => {
                        let cols = row.querySelectorAll("td");
                        soalTerendah.push({
                            no: cols[0].textContent.trim(),
                            course_name: cols[1].textContent.trim(),
                            types_of_assessment: cols[2].textContent.trim(),
                            question: cols[3].textContent.trim(),
                        });
                    });

                let mahasiswaAngkatan = [];
                let cplCodes = [];
                
                document.querySelectorAll("#mahasiswaAngkatanTable thead th").forEach((th, index) => {
                    // Skip 3 kolom pertama (No, NPM, Nama)
                    if (index > 2) {
                        cplCodes.push(th.textContent.trim());
                    }
                });
                
                // Proses setiap baris di tabel
                document.querySelectorAll("#mahasiswaAngkatanTable tbody tr").forEach((row, index) => {
                    let cols = row.querySelectorAll("td");
                    let studentData = {
                        no: cols[0].textContent.trim(),
                        npm: cols[1].textContent.trim(),
                        nama_mhs: cols[2].textContent.trim(),
                        cpls: []
                    };
                    
                    // Mulai dari idex 3 untuk skip kolom No, NPM, dan nama
                    for (let i = 3; i < cols.length; i++) {
                        studentData.cpls.push({
                            kode: cplCodes[i-3],  // menggunakan CPL Code dari header
                            nilai: cols[i].textContent.trim()
                        });
                    }
                    
                    mahasiswaAngkatan.push(studentData);
                });

                let angkatan = document.getElementById("angkatanData").textContent.trim().replace(/^Angkatan[:\s]*/, "");
                let prodi = document.getElementById("prodi").textContent.trim().replace(/^Prodi[:\s]*/, "");
                let universitas = document.getElementById("universitasData").textContent.trim().replace(/^Universitas[:\s]*/, "");

                let courseList = [];
                document.querySelectorAll("#courseList li").forEach(li => {
                    courseList.push(li.textContent.trim());
                });

                fetch("{{ route($currentPrefix . 'generate-pdfVisualAngkatan') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        },
                        body: JSON.stringify({
                            radarChartAngkatanImg,
                            descriptions,
                            soalTerendah,
                            mahasiswaAngkatan,
                            cplCodes,
                            angkatan,
                            prodi,
                            universitas,
                            courseList,
                        }),
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                throw new Error(
                                    `HTTP ${response.status}: ${text}`
                                );
                            });
                        }
                        return response.blob();
                    })
                    .then(blob => {
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `Laporan Visualisasi Angkatan - ${angkatan}.pdf`;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        URL.revokeObjectURL(url);
                    })
                    .catch(error => {
                        console.error("Error:", error.message);
                    });
            });
    </script>

    <script>
        $(document).ready(function() {
            $('#hasilVisual').on('submit', function(event) {
                event.preventDefault();
                // console.log('Form submitted!');
                var universitas = $('#universitas').val();
                var prodi = $('#prodiForm').val();
                var angkatan = $('#angkatanForm').val();
                var imgSrc = $('#universitas-img').attr('src');
                // console.log(imgSrc);
                var formData = $(this).serialize();
                if ($('#prodiForm').prop('disabled')) {
                    formData += '&prodi=' + encodeURIComponent(prodi);
                }
                if (imgSrc) {
                    formData += '&imgSrc=' + encodeURIComponent(imgSrc);
                }
                // Kirim permintaan AJAX
                $.ajax({
                    url: "{{ route($currentPrefix . 'hasilvisual-mahasiswaAngkatan') }}",
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        console.log('AJAX success!');
                        console.log(response);

                        // Ambil data dari respons
                        var angkatan = response.result.angkatan;
                        var prodi = response.result.prodi;
                        var imgSrc = response.result.imgSrc;
                        var universitas = response.result.universitas;
                        // console.log("ini:"+imgSrc);
                        // REVISI
                        var labelCpl = response.result.labelCpl;
                        var soalTerendah = response.result.soalTerendah;
                        var allCplPerAngkatan = response.result.allCplPerAngkatan;
                        var gabunganAkhirMk = response.result.gabunganAkhirMk;
                        var cplResultsAll = response.result.cplResultsAll;

                        var mahasiswaAngkatan = response.result.mahasiswaAngkatan;
                        // SUMMARY
                        // var minSummary = response.result.minSummary;
                        // var maxSummary = response.result.maxSummary;
                        // var minAvgSummary = response.result.minAvgSummary;
                        // var maxAvgSummary = response.result.maxAvgSummary;
                        // // console.log(minSummary);
                        // var min_codeAvg = Object.keys(minSummary)[0];
                        // var min_value = minSummary[min_codeAvg];
                        // var max_code = Object.keys(maxSummary)[0];
                        // var max_value = maxSummary[max_code];
                        // var min_codeAvg_avg = Object.keys(minAvgSummary)[0];
                        // var min_valueAvg = minAvgSummary[min_codeAvg_avg];
                        // var max_codeAvg = Object.keys(maxAvgSummary)[0];
                        // var max_valueAvg = maxAvgSummary[max_codeAvg];

                        // console.log(min_codeAvg);

                        // SUMMARY
                        // var summaryParagraph = document.getElementById("summary");
                        // summaryParagraph.innerHTML = "Based on the generated CPL, the " + angkatan + " batch has:<br>" +
                        //     "- The lowest value at: " + generateSummary(minSummary) + "<br>" +
                        //     "- The highest value at: " + generateSummary(maxSummary) + "<br>" +
                        //     "- The lowest average value at: " + generateSummary(minAvgSummary) + "<br>" +
                        //     "- The highest average value at: " + generateSummary(maxAvgSummary);

                        // function generateSummary(summary) {
                        //     var summaryText = "";
                        //     for (var key in summary) {
                        //         summaryText += key + " with a value of " + summary[key] + ", ";
                        //     }
                        //     return summaryText.slice(0, -2); // Remove the last comma and space
                        // }

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

                        // Tampilkan data di halaman jika perlu

                        $('#angkatanData').text('Angkatan: ' + angkatan);
                        $('#prodi').text('Prodi: ' + prodi[0].nama);
                        $('#universitasData').text('Universitas: ' + universitas);

                        // // set di input hidden
                        $('input[name="angkatan"]').val(angkatan);
                        $('input[name="prodi"]').val(prodi[0].nama);
                        $('input[name="universitasImg"]').val(imgSrc);
                        $('input[name="universitasCPMK"]').val(universitas);


                        // isi label container
                        var labelContainer = $('#labelContainer');

                        labelContainer
                            .empty(); // Mengosongkan kontainer sebelum menambahkan elemen baru

                        labelCpl.forEach(function(item) {
                            var listItem = $('<li>').text(item.kode + ': ' + item
                                .judul);
                            labelContainer.append(listItem);
                        });

                        // TABEL SOAL TERENDAH START
                        var tableSoalBody = $('#soalTerendahTable tbody');
                        tableSoalBody.html('');
                        for (var i = 0; i < soalTerendah.length; i++) {
                            var rowHtml = '<tr>' +
                                '<td>' + (i + 1) + '</td>' +
                                '<td>' + soalTerendah[i].namaCourse + '</td>' +
                                '<td>' + soalTerendah[i].Jenis + '</td>';

                            if (soalTerendah[i].idSoal) {
                                rowHtml +=
                                    '<td><a href="http://127.0.0.1:8000/dosen/soal/cetakSoal/' +
                                    soalTerendah[i].idSoal + '" target="_blank">' +
                                    soalTerendah[i].soal + '</a></td>';
                            } else {
                                rowHtml += '<td>' + soalTerendah[i].soal + '</td>';
                            }

                            rowHtml += '</tr>';
                            tableSoalBody.append(rowHtml);
                        }
                        // TABEL SOAL TERENDAH END

                        // TABEL MAHASISWA ANGKATAN START
                        function roundToTwo(num) {
                            return Math.round(num * 100) / 100;
                        }

                        $('#mahasiswaAngkatanTitle').text('Mahasiswa Angkatan ' + angkatan);
                        var tableMahasiswaBody = $('#mahasiswaAngkatanTable tbody');
                        var tableMahasiswaHead = $('#mahasiswaAngkatanTable thead tr');

                        // Clear existing table
                        tableMahasiswaBody.html('');
                        tableMahasiswaHead.html('');

                        // First, collect all unique CPL codes to create headers
                        var uniqueCplCodes = [];
                        for (var i = 0; i < mahasiswaAngkatan.length; i++) {
                            var cplCode = mahasiswaAngkatan[i].kode;
                            if (uniqueCplCodes.indexOf(cplCode) === -1) {
                                uniqueCplCodes.push(cplCode);
                            }
                        }

                        // Sort CPL codes alphanumerically
                        uniqueCplCodes.sort(function(a, b) {
                            // Extract numeric parts from CPL codes (e.g., "CPL-01" -> 1)
                            var numA = parseInt(a.replace(/\D/g, ''));
                            var numB = parseInt(b.replace(/\D/g, ''));
                            return numA - numB;
                        });

                        // Rebuild the table header
                        tableMahasiswaHead.append('<th width="50px">No</th>');
                        tableMahasiswaHead.append('<th>NPM</th>');
                        tableMahasiswaHead.append('<th>Nama Mahasiswa</th>');
                        // Add a column for each CPL code
                        for (var i = 0; i < uniqueCplCodes.length; i++) {
                            tableMahasiswaHead.append('<th>' + uniqueCplCodes[i] + '</th>');
                        }

                        // Mengelompokkan data berdasarkan NPM
                        var groupedData = {};
                        for (var i = 0; i < mahasiswaAngkatan.length; i++) {
                            var npm = mahasiswaAngkatan[i].npm;
                            if (!groupedData[npm]) {
                                groupedData[npm] = {
                                    npm: npm,
                                    nama_mhs: mahasiswaAngkatan[i].nama_mhs,
                                    cpls: {}
                                };
                            }
                            // Store CPL scores by code for easy lookup
                            groupedData[npm].cpls[mahasiswaAngkatan[i].kode] = mahasiswaAngkatan[i].hasil;
                        }

                        // Mengubah objek menjadi array untuk diiterasi
                        var groupedArray = Object.values(groupedData);
                        var rowCounter = 1;

                        // Membuat tabel dengan format baru
                        for (var i = 0; i < groupedArray.length; i++) {
                            var student = groupedArray[i];
                            
                            // Start a new row
                            var rowHtml = '<tr>' +
                                '<td>' + rowCounter + '</td>' +
                                '<td>' + student.npm + '</td>' +
                                '<td>' + student.nama_mhs + '</td>';
                            
                            // Add cells for each CPL
                            for (var j = 0; j < uniqueCplCodes.length; j++) {
                                var cplCode = uniqueCplCodes[j];
                                var score = student.cpls[cplCode] ? roundToTwo(student.cpls[cplCode]) : 0;
                                rowHtml += '<td>' + score + '</td>';
                            }
                            
                            rowHtml += '</tr>';
                            tableMahasiswaBody.append(rowHtml);
                            
                            rowCounter++;
                        }
                        // TABEL MAHASISWA ANGKATAN END

                        // course list ajax 
                        var olElement = $('#courseList');
                        olElement.empty();
                        for (var kode in gabunganAkhirMk) {
                            var namaMk = gabunganAkhirMk[kode];
                            var listItemHtml = '<li>' + kode + ' - ' + namaMk + '</li>';
                            olElement.append(listItemHtml);
                        }

                        // Untuk di form optional untuk pilih cpmk
                        // Menambahkan opsi ke elemen <select>
                        var selectElement = $('#courseSelect');
                        selectElement.empty();

                        // Tambahkan default option
                        var defaultOptionHtml = '<option value="">Select Course</option>';
                        selectElement.append(defaultOptionHtml);

                        // Iterasi melalui objek gabunganAkhirMk
                        for (var course in gabunganAkhirMk) {
                            var optionHtml = '<option value="' + course + '">' + course +
                                ' - ' + gabunganAkhirMk[course] + '</option>';
                            selectElement.append(optionHtml);
                        }

                        // Tampilkan konten
                        $('#visualContainer').show();

                        // AJAX dalam skrip Chart.js
                        // RADAR SEBELAH KANAN
                        var labelsCapaianCpl = Object.keys(allCplPerAngkatan.avg_cpl);
                        var dataMinCpl = Object.values(allCplPerAngkatan.min);
                        var dataAvgCpl = Object.values(allCplPerAngkatan.avg_cpl);
                        var dataMaxCpl = Object.values(allCplPerAngkatan.max);
                        var canvas = document.getElementById('radarChartAngkatan');
                        var ctx = canvas.getContext('2d');
                        var radarChart = new Chart(ctx, {
                            type: 'radar',
                            data: {
                                labels: labelsCapaianCpl,
                                datasets: [{
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
                        // Mengaktifkan tombol print setelah visualisasi tampil
                        $('#btnPrintPdf').prop('disabled', false);
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
        $(document).ready(function() {
            // Validasi CPL
            $('#hasilVisual').on('submit', function(event) {
                // Validasi select "course"
                var angkatanForm = $('#angkatanForm').val();
                var prodiForm = $('#prodiForm').val();

                if (!angkatanForm || !prodiForm) {
                    alert('Mohon pilih/isi field yang kosong!');
                    event.preventDefault();
                }
            });

            // Validasi CPMK
            $('#hasilvisualcpmk-angkatan').on('submit', function(event) {
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
