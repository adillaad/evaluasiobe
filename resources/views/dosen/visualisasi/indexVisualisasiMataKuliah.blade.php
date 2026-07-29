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
    <h3 class="px-4 pb-4 fw-bold text-center">Halaman Visualisasi CPMK Per Mata Kuliah</h3>
    <h6 class="px-4 pb-4 fw-bold text-center">Silahkan masukan data Mata Kuliah</h6>
    <div class="form-group stretch-card" id="tugas">
        <div class="card">
            <div class="card-body">
                @if (in_array($userOtoritas, ['Dosen', 'Kepala Program Studi', 'Penjamin Mutu Program Studi']))
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="left"
                        title="Hasil dari form ini adalah visualisasi CPMK per angkatan mahasiswa. Mohon memilih angkatan serta mata kuliah untuk menampilkan hasil visualisasi CPMK per angkatan mahasiswa serta informasi ketercapaian CPMK per angkatan mahasiswa"
                        style="float:right;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                            <path
                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                        </svg>
                    </button>
                @else
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="left"
                        title="Hasil dari form ini adalah visualisasi CPMK per angkatan mahasiswa. Mohon memilih prodi diikuti dengan angkatan serta mata kuliah untuk menampilkan hasil visualisasi CPMK per angkatan mahasiswa serta informasi ketercapaian CPMK per angkatan mahasiswa"
                        style="float:right;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                            <path
                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                        </svg>
                    </button>
                @endif
                <h6 class="pb-4 ">Visualisasi CPMK Per Mata Kuliah</h6>
                <form id="hasilVisual" method="POST" action="hasilvisual-mahasiswaMataKuliah"
                    enctype="multipart/form-data">
                    @csrf
                    <div id="dynamicAddRemoveTugas" class="row">
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
                                <label>Angkatan<span class="text-danger mt-2">*</span></label>
                                <select id="angkatanForm" class="form-control" name="angkatan">
                                    {{-- Opsi ditampilkan pake ajax --}}
                                </select>
                                <label>Course<span class="text-danger mt-3">*</span></label>
                                <select id="courseSelect" class="form-control" name="course">
                                    {{-- Opsi ditampilkan pake ajax  --}}
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
                                    title="Hasil dari form ini adalah visualisasi CPMK per mahasiswa. Mohon pilih npm untuk menampilkan hasil visualisasi CPMK per  mahasiswa serta informasi ketercapaian CPMK per mahasiswa"
                                    style="float:right;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-info-circle-fill"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                                    </svg>
                                </button>
                                <h5 class="card-title">Lihat CPMK Individu</h5>
                                <form id="hasilvisualcpmk-mahasiswa" method="POST"
                                    action="hasilvisualcpmk-mahasiswa" enctype="multipart/form-data">
                                    @csrf
                                    <div id="dynamicAddRemoveTugas">
                                        <div class="form-group row">
                                            <div class="col-5">
                                                <div class="form-group">
                                                    <label>NPM <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="npm" id="npmSelect">
                                                        <!-- Dari ajax -->
                                                    </select>
                                                    <input type="text" name="nama" class="visually-hidden" value="">
                                                    <input type="text" name="angkatan" class="visually-hidden" value="">
                                                    <input type="text" name="prodi" class="visually-hidden" value="">
                                                    <input type="text" name="course" class="visually-hidden" value="">
                                                    <input type="text" name="universitasImg" class="visually-hidden" value="">
                                                    <input type="text" name="universitasCPMK" class="visually-hidden" value="">
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
                                            <h5 id="judulChart" class="card-title">CPMK Batch</h5>
                                            <canvas id="radarChartAngkatan"></canvas>
                                            <h6 class="mt-4" style="text-align:justify">Summary :</h6>
                                            <p id="summary"></p>
                                            <h6 class="keterangan mt-3">Descriptions :</h6>
                                            {{-- Dari AJAX --}}
                                            <ol id="labelContainer" class="overflow-auto"
                                                style="max-height: 200px; overflow: auto;">
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="card mt-3">
                                        <div class="card-body">
                                            <h6 class="fw-bold">Questions of CPMK Batch with the Lowest Average :</h6>
                                            <div class="table-responsive" class="table-responsive overflow-auto"
                                                style="max-height: 300px; overflow: auto;">
                                                <table class="table table-bordered" id="soalTerendahTable">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
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
                                                    <h6 id="universitasText" class="card-subtitle mt-2 text-black"></h6>
                                                </li>
                                                <li>
                                                    <h6 id="course" class="card-subtitle mt-2 text-black"></h6>
                                                </li>
                                            </ol>
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
    {{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
    {{-- Buat nangkep mata kuliah berdasarkan prodi --}}
    {{-- script select option --}}
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
                        // console.log("prodi:", prodi);
                        // console.log("universitas:", universitas);
                        $('#angkatanForm').html(data);
                    }
                });
            }
            
            $('#prodiForm').on('change', function() {
                var prodi = $(this).val();
                var universitas = $('#universitas').val(); // Ambil nilai dari elemen #universitas
                loadAngkatan(prodi, universitas)
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

            $('#angkatanForm').change(function() {
                var prodi = $('#prodiForm').val();
                var angkatan = $('#angkatanForm').val();
                var universitas = $('#universitas').val();
                console.log(prodi, angkatan, universitas);

                $.ajax({
                    url: "{{ route($currentPrefix . 'getCourseByProdi') }}",
                    type: 'GET',
                    data: {
                        '_token': $('input[name=_token]').val(),
                        'prodi': prodi,
                        'angkatan': angkatan,
                        'universitas': universitas
                    },
                    success: function(data) {
                        console.log("Ajax sukses");
                        // Mengisi pilihan mata kuliah
                        console.log(data);
                        $('#courseSelect').html(data);
                    }
                });
            });

        });
    </script>

    {{-- Script cek perubahan npm untuk lihat cpmk individu --}}
    <script>
        $(document).ready(function() {
            $('#npmSelect').on('change', function() {
                var npm = $(this).val();
                // console.log(npm);
                $.ajax({
                    url: "{{ route($currentPrefix . 'getNamaByNpm') }}", // route untuk kirim ke kontroler
                    method: 'GET',
                    data: {
                        npm: npm
                    },
                    dataType: 'json',
                    success: function(response) {
                        // console.log('AJAX success yg nama!');

                        var nama = response.result.namaData;
                        // console.log(nama);
                        $('input[name="nama"]').val(nama);
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#hasilVisual').on('submit', function(event) {
                event.preventDefault();
                // console.log('Form submitted!');
                var prodi = $('#prodiForm').val();
                var angkatan = $('#angkatanForm').val();
                var universitas = $('#universitas').val();
                var imgSrc = $('#universitas-img').attr('src');
                var formData = $(this).serialize();
                if ($('#prodiForm').prop('disabled')) {
                    formData += '&prodi=' + encodeURIComponent(prodi);
                }
                if (imgSrc) {
                    formData += '&imgSrc=' + encodeURIComponent(imgSrc);
                }
                // console.log(formData);
                // Kirim permintaan AJAX
                $.ajax({
                    url: "{{ route($currentPrefix . 'hasilvisual-mahasiswaMataKuliah') }}",
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
                        // console.log("ini:"+imgSrc);
                        var universitas = response.result.universitas;
                        var soalTerendah = response.result.soalTerendah;

                        var allNpm = response.result.allNpm;
                        // var npmData = response.result.npmData;
                        var completeCourseFormat = response.result.completeCourseFormat;
                        var cpmkTmp = response.result.cpmkTmp;
                        var cpmkResultAll = response.result.cpmkResultAll;
                        console.log(cpmkResultAll);
                        var cpmkTmp = response.result.cpmkTmp;
                        var kodeMinAvgAngkatan = response.result.kodeMinAvgAngkatan;
                        var kodeMaxAvgAngkatan = response.result.kodeMaxAvgAngkatan;
                        // // Tampilkan data di halaman jika perlu
                        // $('#nama').text('Nama: ' + nama);
                        // $('#npmData').text('NPM: ' + npm);
                        $('#angkatanData').text('Angkatan: ' + angkatan);
                        $('#prodi').text('Prodi: ' + prodi);
                        $('#universitasText').text('Universitas: ' + universitas);
                        $('#course').text('Course: ' + completeCourseFormat);

                        // // set di input hidden
                        $('input[name="angkatan"]').val(angkatan);
                        $('input[name="prodi"]').val(prodi);
                        $('input[name="course"]').val(completeCourseFormat);
                        $('input[name="universitasImg"]').val(imgSrc);
                        $('input[name="universitasCPMK"]').val(universitas);
                        // $('input[name="allNpm"]').val(JSON.stringify(allNpm));
                        // isi label container
                        var labelContainer = $('#labelContainer');
                        labelContainer.empty();
                        $.each(cpmkResultAll, function(index, itemCpmk) {
                            labelContainer.append('<li class="ms-2">' + itemCpmk.kode + ': ' +
                                itemCpmk.judul + '</li>');
                        });
                        // Isi tabel
                        var tableBody = $('#soalTerendahTable tbody');
                        tableBody.html('');
                        for (var i = 0; i < soalTerendah.length; i++) {
                            var rowHtml = '<tr>' +
                                '<td>' + (i + 1) + '</td>' +
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
                            tableBody.append(rowHtml);
                        }

                        // TODO:summary 
                        var summaryText =
                            "Based on the calculation of CPMK, the following results were obtained:<br>" +
                            "- " + kodeMinAvgAngkatan[3] +
                            " has the lowest average CPMK value of " + Number(
                                kodeMinAvgAngkatan[0]).toFixed(2) + "<br>" +
                            "- " + kodeMaxAvgAngkatan[3] +
                            " has the highest average CPMK value of " + Number(
                                kodeMaxAvgAngkatan[0]).toFixed(2);

                        $('#summary').html(summaryText);

                        // Tampilkan konten
                        $('#visualContainer').show();

                        // AJAX dalam skrip Chart.js

                        // Select cpmk individu
                        var npmSelect = $('#npmSelect');
                        npmSelect.empty();
                        npmSelect.append(
                            '<option value="" disabled selected>Select NPM</option>');
                        $.each(allNpm, function(index, item) {
                            npmSelect.append('<option value="' + item.npm + '">' + item
                                .npm + ' - ' + item.nama_mhs + '</option>');
                        });

                        // RADAR SEBELAH kiri
                        var dataCapaianCpmkAvg = Object.values(cpmkTmp).map(item => parseFloat(
                            item[0]).toFixed(2));
                        var dataCapaianCpmkMin = Object.values(cpmkTmp).map(item => parseFloat(
                            item[1]).toFixed(2));
                        var dataCapaianCpmkMax = Object.values(cpmkTmp).map(item => parseFloat(
                            item[2]).toFixed(2));
                        var labelsCapaianCpmk = Object.values(cpmkTmp).map(item => item[3]);
                        var canvas = document.getElementById('radarChartAngkatan');
                        var ctx = canvas.getContext('2d');
                        var radarChart = new Chart(ctx, {
                            type: 'radar',
                            data: {
                                labels: labelsCapaianCpmk,
                                datasets: [{
                                        label: 'Average CPMK',
                                        data: dataCapaianCpmkAvg,
                                        backgroundColor: 'rgba(192, 110, 75, 0)',
                                        borderColor: 'rgba(192, 110, 75, 0.3)',
                                        borderWidth: 3,
                                        pointBackgroundColor: 'rgba(192, 110, 75, 0.4)'
                                    },
                                    {
                                        label: 'Min CPMK',
                                        data: dataCapaianCpmkMin,
                                        backgroundColor: 'rgba(126, 0, 0, 0.2)',
                                        borderColor: 'rgba(216, 33, 33, 0.27)',
                                        borderWidth: 3,
                                        pointBackgroundColor: 'rgba(126, 0, 0, 0.4)'
                                    },
                                    {
                                        label: 'Max CPMK',
                                        data: dataCapaianCpmkMax,
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

                        document.getElementById("btnPrintPdf").addEventListener("click",
                            function() {
                                let radarChartAngkatan = document.getElementById(
                                    'radarChartAngkatan');
                                let radarChartAngkatanImg = radarChartAngkatan ?
                                    radarChartAngkatan.toDataURL() : null;

                                let summary = document.getElementById('summary').innerHTML
                                    .trim();

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
                                            types_of_assessment: cols[1]
                                                .textContent.trim(),
                                            question: cols[2]?.textContent
                                                .trim(),
                                        });
                                    });

                                let angkatan = document.getElementById('angkatanData')
                                    .textContent.trim().replace(/^Angkatan[:\s]*/, "");
                                let prodi = document.getElementById('prodi').textContent
                                    .trim().replace(/^Prodi[:\s]*/, "");
                                let universitas = document.getElementById('universitasText')
                                    .textContent.trim().replace(/^Universitas[:\s]*/, "");
                                let course = document.getElementById('course').textContent
                                    .trim().replace(/^Course[:\s]*/, "");

                                fetch("{{ route($currentPrefix . 'generate-pdfVisualMataKuliah') }}", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/json",
                                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                        },
                                        body: JSON.stringify({
                                            radarChartAngkatanImg,
                                            summary,
                                            descriptions,
                                            soalTerendah,
                                            angkatan,
                                            prodi,
                                            universitas,
                                            course,
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
                                    })
                                    .then(blob => {
                                        const url = URL.createObjectURL(blob);
                                        const a = document.createElement('a');
                                        a.href = url;
                                        a.download =
                                            `Laporan Visualisasi Mata Kuliah - ${course}.pdf`;
                                        document.body.appendChild(a);
                                        a.click();
                                        document.body.removeChild(a);
                                        URL.revokeObjectURL(url);
                                    })
                                    .catch(error => {
                                        console.error("Error:", error.message);
                                    });
                            })
                    }
                });
            });
        });
    </script>

    <script>
        // Validasi CPMK mata kuliah angkatan
        $(document).ready(function() {
            // Validasi CPMK angkatan
            $('#hasilVisual').on('submit', function(event) {
                var courseSelect = $('#courseSelect').val();
                var angkatanSelect = $('#angkatanForm').val();
                var prodiSelect = $('#prodiForm').val();

                if (!courseSelect || !angkatanForm || !prodiForm) {
                    alert('Mohon pilih/isi field yang kosong!');
                    event.preventDefault();
                }
            });

            // Validasi cpmk individu
            $('#hasilvisualcpmk-mahasiswa').on('submit', function(event) {
                var npmSelect = $('#npmSelect').val();
                var nama = $('input[name="nama"]').val();
                var angkatan = $('input[name="angkatan"]').val();
                var prodi = $('input[name="prodi"]').val();
                var course = $('input[name="course"]').val();
                
                if (!npmSelect) {
                    alert('Mohon pilih NPM terlebih dahulu!');
                    event.preventDefault();
                    return;
                }
                
                if (!nama || !angkatan || !prodi || !course) {
                    alert('Data belum lengkap. Harap tunggu hingga semua data terisi atau pilih NPM kembali.');
                    event.preventDefault();
                    return;
                }
            });
        });
    </script>
@endsection
