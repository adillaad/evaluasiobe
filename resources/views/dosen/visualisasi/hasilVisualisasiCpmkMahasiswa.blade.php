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
    <h3 id="title" data-course="{{ $completeCourseFormat }}" class="px-4 pb-4 fw-bold text-center">Hasil Visualisasi CPMK {{ $completeCourseFormat }} Mahasiswa
        {{ $nama }}</h3>
    {{-- card buat ganti npm --}}
    <div class="form-group stretch-card" id="tugas">
        <div class="card">
            <div class="card-body">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="left"
                    title="Form ini digunakan untuk mengecek mahasiswa lainnya. silahkan pilih npm mahasiswa yang ingin ditampilkan ketercapaian CPMK"
                    style="float:right;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                        <path
                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                    </svg>
                </button>
                <h6 class="pb-4 ">Cek Mahasiswa Lainnya :</h6>
                <form id="visualCpmkMahasiswa" method="POST" action="hasilvisualcpmk-mahasiswa"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>NPM <span class="text-danger">*</span></label>
                                <select id="npm" class="form-control" name="npm">
                                    <option value="">Pilih NPM</option>
                                    @foreach ($allNamaNpmData as $data)
                                        <option value="{{ $data->NPM }}">{{ $data->NPM }} - {{ $data->nama_mhs }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="allNpm" value="{{ json_encode($allNpm) }}">
                                <input type="text" name="course" class="visually-hidden" value="{{ $completeCourseFormat }}">
                                <input type="text" name="nama" class="visually-hidden">
                                <input type="text" name="angkatan" class="visually-hidden" value="{{ $angkatan }}">
                                <input type="text" name="universitasCPMK" class="visually-hidden" value="{{ $universitas }}">
                                <input type="text" name="universitasImg" class="visually-hidden" value="{{ $universitasImg }}">
                                <input type="text" name="prodi" class="visually-hidden" value="{{ $prodi }}">
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
                                        <h5 class="card-title">CPMK</h5>
                                        <div
                                            style="display: flex; justify-content: center; align-items: center; height: 100vh; width: 100%;">
                                            <canvas id="radarChart"
                                                style="width: 80vw; height: 80vh; max-width: 600px; max-height: 600px;"></canvas>
                                        </div>
                                        <h6 class="mt-2" style="text-align:justify">Summary :</h6>
                                        <p id="summary">Based on the CPMK calculations, the following conclusions can be
                                            drawn: <br>
                                            - The highest CPMK is associated with the code {{ $kodeMaxCpmk }} with a value
                                            of {{ $maxCpmk }}. <br>
                                            - The lowest CPMK is associated with the code {{ $kodeMinCpmk }} with a value
                                            of {{ $minCpmk }}.
                                        </p>
                                        <h6 class="keterangan mt-3">Descriptions :</h6>
                                        <ol id="labelContainer" class="overflow-auto" style="max-height: 200px; overflow: auto;">
                                            @foreach ($cpmkResultAll as $itemCpmk)
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
                                                <h6 id="nama" class="card-subtitle mt-2 text-black">Nama :
                                                    {{ $nama }}</h6>
                                            </li>
                                            <li>
                                                <h6 id="npmData" class="card-subtitle mt-2 text-black">NPM :
                                                    {{ $npm }} </h6>
                                            </li>
                                            <li>
                                                <h6 id="angkatan" class="card-subtitle mt-2 text-black">Angkatan :
                                                    {{ $angkatan }}
                                                </h6>
                                            </li>
                                            <li>
                                                <h6 id="prodi" class="card-subtitle mt-2 text-black">Prodi:
                                                    {{ $prodi }}</h6>
                                            </li>
                                            <li>
                                                <h6 id="universitas" class="card-subtitle mt-2 text-black">Universitas:
                                                    {{ $universitas }}
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
    {{-- Validasi form mahasiswa lainnya biar npm --}}
    <script>
        $(document).ready(function() {
            $('#visualCpmkMahasiswa').on('submit', function(event) {
                var npm = $('#npm').val();
                var course = $('input[name=course]').val();
                var nama = $('input[name=nama]').val();
                var angkatan = $('input[name=angkatan]').val();
                var prodi = $('input[name=prodi]').val();
                var universitas = $('input[name=universitasCPMK]').val();
                var universitasImg = $('input[name=universitasImg]').val();
                
                if (!npm || !course || !nama || !angkatan || !prodi || !universitas || !universitasImg) {
                    event.preventDefault();
                    if(!npm) {
                        alert('Mohon pilih NPM mahasiswa yang ingin ditampilkan ketercapaian CPMK');
                    } else {
                        alert('Mohon tunggu sebentar hingga semua data lengkap muncul');
                    }
                }
            });
        });
    </script>
    {{-- Pilih mahasiswa lain --}}
    <script>
        $(document).ready(function() {
            $('#npm').on('change', function() {
                var npm = $(this).val();
                // console.log("Berhasil: " + npm);
                $.ajax({
                    url: "{{ route($currentPrefix . 'getNamaByNpm') }}", // route untuk kirim ke kontroler
                    method: 'GET',
                    data: {
                        npm: npm
                    },
                    dataType: 'json',
                    success: function(response) {
                        var nama = response.result.namaData;
                        //  console.log(nama);
                        $('input[name=nama]').val(nama);
                    }
                });
            });
        });
    </script>

    <script>
        var cpmk = @json($cpmkTmp);
        console.log(cpmk);

        var dataCapaianCpmk = Object.values(cpmk).map(item => Number(parseFloat(item[0]).toFixed(2)));
        var dataCapaianCpmkAvg = Object.values(cpmk).map(item => Number(parseFloat(item[1]).toFixed(2)));
        var dataCapaianCpmkMin = Object.values(cpmk).map(item => Number(parseFloat(item[2]).toFixed(2)));
        var dataCapaianCpmkMax = Object.values(cpmk).map(item => Number(parseFloat(item[3]).toFixed(2)));
        var labelsCapaianCpmk = Object.values(cpmk).map(item => item[4]);

        var canvas = document.getElementById('radarChart');
        var ctx = canvas.getContext('2d');
        var radarChart = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: labelsCapaianCpmk,
                datasets: [{
                        label: 'CPMK',
                        data: dataCapaianCpmk,
                        backgroundColor: 'rgba(75, 192, 192, 0)',
                        borderColor: 'rgba(75, 192, 192, 0.3)',
                        borderWidth: 3,
                        pointBackgroundColor: 'rgba(75, 192, 192, 0.4)'
                    },
                    {
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

            let radarChart = document.getElementById('radarChart');
            let radarChartImg = radarChart ? radarChart.toDataURL() : null;

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

            let nama = document.getElementById("nama").textContent.trim().replace(/^Nama[:\s]*/, "");
            let npm = document.getElementById("npmData").textContent.trim().replace(/^NPM[:\s]*/, "");
            let angkatan = document.getElementById("angkatan").textContent.trim().replace(/^Angkatan[:\s]*/, "");
            let prodi = document.getElementById("prodi").textContent.trim().replace(/^Prodi[:\s]*/, "");
            let universitas = document.getElementById("universitas").textContent.trim().replace(/^Universitas[:\s]*/, "");

            fetch("{{ route($currentPrefix . 'generate-pdfVisualCPMKMahasiswa') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    body: JSON.stringify({
                        course,
                        radarChartImg,
                        summary,
                        descriptions,
                        soalTerendah,
                        nama,
                        npm,
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
                    a.download = `Laporan Visualisasi CPMK ${course} Mahasiswa - ${nama}.pdf`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                })
                .catch(error => console.error("Error:", error.message));
        });
    </script>
@endsection
