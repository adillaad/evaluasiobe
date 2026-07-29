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
    <h3 id="title" data-course="{{ $completeCourseFormat }}" class="px-4 pb-4 fw-bold text-center">Hasil Visualisasi CPMK {{ $completeCourseFormat }} Angkatan {{ $angkatan }}
    </h3>
    {{-- {{dd($allAngkatan)}} --}}
    {{-- card buat ganti ANGKATAN --}}
    <div class="form-group stretch-card" id="tugas">
        <div class="card">
            <div class="card-body">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="left"
                    title="Form ini digunakan untuk mengecek angkatan lainnya. silahkan pilih angkatan yang ingin ditampilkan ketercapaian CPMK"
                    style="float:right;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                        <path
                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                    </svg>
                </button>
                <h6 class="pb-4 ">Cek Angkatan Lainnya :</h6>
                <form id="visualCpmkAngkatan" method="POST" action="hasilvisualcpmk-angkatan"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Angkatan <span class="text-danger">*</span></label>
                                {{-- @dd($allAngkatan) --}}
                                <select id="angkatan" class="form-control" name="angkatan">
                                    <option selected="true" value="" disabled selected>Silahkan Pilih Angkatan
                                    </option>
                                    @foreach ($allAngkatan as $a)
                                        <option value="{{ $a->angkatan }}">{{ $a->angkatan }}</option>
                                    @endforeach
                                    {{-- <input type="hidden" name="allNpm"  class="visually-hidden"> --}}
                                    {{-- <input type="hidden" name="allAngkatan" value="{{ json_encode($allAngkatan) }}"> --}}
                                    <input type="text" name="course" class="visually-hidden" value="{{ $course }}">
                                    <input type="text" name="prodi" class="visually-hidden" value="{{ $prodi }}">
                                    <input type="text" name="universitasCPMK" class="visually-hidden" value="{{ $universitas }}">
                                    <input type="text" name="universitasImg" class="visually-hidden" value="{{ $universitasImg }}">
                                </select>
                            </div>
                            <input type="submit" class="btn btn-primary" value="Submit">
                            <button id="btnPrintPdf" type="button" class="btn btn-primary" disabled>Print PDF</button>
                        </div>
                        <div class="col-6">
                            <img id="universitas-img" src="{{ asset($universitasImg) }}" class="img img-responsive"
                                style="max-width: 35%; margin-left: 100px;" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="form-group stretch-card" id="tugas">
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <div class="mt-4">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">CPMK Batch</h5>
                                        <canvas id="radarChartAngkatan"></canvas>
                                        <h6 class="mt-4" style="text-align:justify">Summary :</h6>
                                        <p id="summary">Based on the CPMK calculations, the following conclusions can be
                                            drawn: <br>
                                            - The highest average CPMK is associated with the code {{ $kodeMaxAvg }} with
                                            a value of {{ $maxAvg }}. <br>
                                            - The lowest average CPMK is associated with the code {{ $kodeMinAvg }} with
                                            a value of {{ $minAvg }}.
                                        </p>
                                        <h6 class="keterangan mt-3">Descriptions :</h6>
                                        <ol id="labelContainer" class= "overflow-auto" style="max-height: 200px; overflow: auto;">
                                            @foreach ($cpmkResultAll as $index => $itemCpmk)
                                                <li>{{ $itemCpmk->kode }}: {{ $itemCpmk->judul }}</li>
                                            @endforeach
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Questions <span class="text-lowercase">with</span> the Lowest
                                            CPMK :</h5>
                                        <div class="table-responsive overflow-auto"
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
                                                    @foreach ($soalTerendah as $index => $item)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $item['Jenis'] }}</td>
                                                            <td>
                                                                @if ($item['idSoal'])
                                                                    <a href="{{ url('/dosen/soal/cetakSoal/' . $item['idSoal']) }}"
                                                                        target="_blank">{{ $item['soal'] }}</a>
                                                                @else
                                                                    {{ $item['soal'] }}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Data :</h5>
                                        <ol>
                                            <li>
                                                <h6 id="angkatanData" class="card-subtitle mt-2 text-black">Angkatan : {{ $angkatan }}
                                                </h6>
                                            </li>
                                            <li>
                                                <h6 id="prodi" class="card-subtitle mt-2 text-black">Prodi : {{ $prodi }}</h6>
                                            </li>
                                            <li>
                                                <h6 id="universitas" class="card-subtitle mt-2 text-black">Universitas : {{ $universitas }}
                                                </h6>
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
    {{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
    {{-- Validasi form pilih angkatan lain form --}}
    <script>
        $(document).ready(function() {
            $('#visualCpmkAngkatan').on('submit', function(event) {

                var angkatan = $('#angkatan').val();
                if (!angkatan) {
                    alert('Mohon pilih/isi field yang kosong!');
                    event.preventDefault();
                }
            });
        });
    </script>
    {{-- Pilih angkatan lain --}}
    <script>
        $(document).ready(function() {
            $('#angkatan').on('change', function() {
                var angkatan = $(this).val();
                var prodi = $('input[name=prodi]').val();
                // console.log("Berhasil: " + angkatan + "prodi: " + prodi);
                $.ajax({
                    url: "{{ route($currentPrefix . 'getAllNpmByAngkatan') }}", // route untuk kirim ke kontroler
                    method: 'GET',
                    data: {
                        angkatan: angkatan,
                        prodi: prodi
                    },
                    dataType: 'json',
                    success: function(response) {
                        // console.log("SUKSES AJAX INI");
                        var allNpm = response.result.allNpm;
                        var allNpmString = JSON.stringify(allNpm);

                        $('input[name=allNpm]').val(allNpmString);
                    }
                });
            });
        });
    </script>

    <script>
        var cpmk = @json($cpmkTmp);

        // var dataCapaianCpmk = Object.values(cpmk).map(item => Number(parseFloat(item[0]).toFixed(2)));
        var dataCapaianCpmkAvg = Object.values(cpmk).map(item => Number(parseFloat(item[0]).toFixed(2)));
        var dataCapaianCpmkMin = Object.values(cpmk).map(item => Number(parseFloat(item[1]).toFixed(2)));
        var dataCapaianCpmkMax = Object.values(cpmk).map(item => Number(parseFloat(item[2]).toFixed(2)));
        var labelsCapaianCpmk = Object.values(cpmk).map(item => item[3]);

        var canvas = document.getElementById('radarChartAngkatan');
        var ctx = canvas.getContext('2d');
        var radarChart = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: labelsCapaianCpmk,
                datasets: [{
                        label: 'CPMK Avg (Batch)',
                        data: dataCapaianCpmkAvg,
                        backgroundColor: 'rgba(192, 110, 75, 0)',
                        borderColor: 'rgba(192, 110, 75, 0.3)',
                        borderWidth: 3,
                        pointBackgroundColor: 'rgba(192, 110, 75, 0.4)'
                    },
                    {
                        label: 'CPMK Min (Batch)',
                        data: dataCapaianCpmkMin,
                        backgroundColor: 'rgba(126, 0, 0, 0.2)',
                        borderColor: 'rgba(216, 33, 33, 0.27)',
                        borderWidth: 3,
                        pointBackgroundColor: 'rgba(126, 0, 0, 0.4)'
                    },
                    {
                        label: 'CPMK Max (Batch)',
                        data: dataCapaianCpmkMax,
                        backgroundColor: 'rgba(177, 255, 184, 0)',
                        borderColor: 'rgba(33, 216, 95, 0.39)',
                        borderWidth: 3,
                        pointBackgroundColor: 'rgba(0, 0, 0, 1)'
                    },
                ]
            },
            options: {
                scales: {
                    r: {
                        suggestedMin: 0,
                        suggestedMax: 100,
                        ticks: {
                            beginAtZero: true,
                            // stepSize: 1 // Optional: Control the step size between ticks
                        }
                    }
                }
            }
        });

        // Mengaktifkan tombol print setelah visualisasi tampil
        $('#btnPrintPdf').prop('disabled', false);

        document.getElementById('btnPrintPdf').addEventListener('click', function() {
            let course = document.getElementById('title').getAttribute('data-course');

            let radarChartAngkatan = document.getElementById('radarChartAngkatan');
            let radarChartAngkatanImg = radarChartAngkatan ? radarChartAngkatan.toDataURL() : null;

            let summary = document.getElementById('summary').innerHTML.trim();
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
                        types_of_assessment: cols[1].textContent.trim(),
                        question: cols[2].textContent.trim(),
                    });
                });

            let angkatan = document.getElementById("angkatanData").textContent.trim().replace(/^Angkatan[:\s]*/, "");
            let prodi = document.getElementById("prodi").textContent.trim().replace(/^Prodi[:\s]*/, "");
            let universitas = document.getElementById("universitas").textContent.trim().replace(/^Universitas[:\s]*/, "");

            fetch("{{ route($currentPrefix . 'generate-pdfVisualCPMKAngkatan') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    body: JSON.stringify({
                        course,
                        radarChartAngkatanImg,
                        summary,
                        descriptions,
                        soalTerendah,
                        angkatan,
                        prodi,
                        universitas,
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
                        `Laporan Visualisasi CPMK ${course} Angkatan - ${angkatan}.pdf`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                })
                .catch(error => console.error("Error:", error.message));
        });
    </script>
@endsection
