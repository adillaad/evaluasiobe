{{-- @php
    $currentPrefix = auth()->user()->otoritas->otoritas
        ? str_replace(' ', '-', strtolower(auth()->user()->otoritas->otoritas)) . '.'
        : 'admin.';
@endphp --}}
@extends('admin.template')
@section('content')
    <style>
        .card-sum:hover {
            background-color: rgba(0, 123, 255, .75) !important;
            cursor: pointer;
            transform: scale(.95);
        }

        .card-two:hover {
            cursor: pointer;
            transform: scale(.95);
        }
    </style>
    <div class="form-group">
        <div class="d-grid justify-content-between w-100"
            style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));gap: 1rem;">
            <a class="card card-two mx-1 w-100 text-decoration-none" style="color:black;"
                href="{{ route($currentPrefix . 'list-user') }}">
                <div class="card-body pb-0 row">
                    <div class="col">
                        <h4 class="card-title card-title-dash mb-4">Jumlah User</h4>
                        <p class="status-summary-ight-white mb-1" style="color:black">Dosen & Penjamin Mutu</p>
                        <h2 class="" style="color:gray">{{ $userCount }}</h2>
                    </div>
                    <div class="col text-end">
                        <i class="h1 mdi mdi-account-multiple" style="font-size:100px; opacity:0.4"></i>
                    </div>
                </div>
            </a>
            <a class="card card-two mx-1 w-100 text-decoration-none" style="color: black"
                href="{{ route($currentPrefix . 'list-kurikulum') }}">
                <div class="card-body row pb-4">
                    <div class="col">
                        <h4 class="card-title card-title-dash mb-4" style="color: black">Jumlah Kurikulum</h4>
                        <p class="status-summary-ight-white mb-1" style="color: black">Tahun Kurikulum</p>
                        <h2 class="" style="color:gray">{{ $kurikulums->count() }}</h2>
                    </div>
                    <div class="col text-end">
                        <i class="h1 mdi mdi-folder-multiple" style="font-size:100px; opacity:0.4;"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row justify-content-start">
            <div class="form-group col-3">
                <label for="filter" class="form-label">FILTER KURIKULUM</label>
                <select name="kurikulum" id="filter" class="form-select text-center">
                    <option value="all">Semua</option>
                    @foreach ($kurikulums as $kur)
                        <option value="{{ $kur->id }}" data-tahun="{{ $kur->tahun }}">{{ $kur->tahun }} -
                            {{ $kur->prodi->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="d-grid justify-content-between w-100"
            style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));gap: 1rem;">
            <a class="card card-sum mx-1 w-100 bg-primary text-white text-decoration-none"
                href="{{ route($currentPrefix . 'list-cpl') }}">
                <div class="card-body pb-0">
                    <h4 class="card-title card-title-dash text-white mb-4">Jumlah CPL</h4>
                    <div class="row">
                        <div class="col">
                            <p id="kur-cpl" class="status-summary-ight-white mb-1">Semua Kurikulum</p>
                            <h2 class="text-info" id="jumlah-cpl"></h2>
                        </div>
                        <div class="col text-end p-4">
                            <i class="h1 mdi mdi-view-list"></i>
                        </div>
                    </div>
                </div>
            </a>
            <a class="card card-sum mx-1 w-100 bg-primary text-white text-decoration-none"
                href="{{ route($currentPrefix . 'list-mk') }}">
                <div class="card-body pb-0">
                    <h4 class="card-title card-title-dash text-white mb-4">Jumlah Mata Kuliah</h4>
                    <div class="row">
                        <div class="col">
                            <p id="kur-mk" class="status-summary-ight-white mb-1">Semua Kurikulum</p>
                            <h2 class="text-info" id="jumlah-mk"></h2>
                        </div>
                        <div class="col text-end p-4">
                            <i class="h1 mdi mdi-book-open"></i>
                        </div>
                    </div>
                </div>
            </a>
            <a class="card card-sum mx-1 w-100 bg-primary text-white text-decoration-none"
                href="{{ route($currentPrefix . 'list-rps') }}">
                <div class="card-body pb-0">
                    <h4 class="card-title card-title-dash text-white mb-4">Jumlah RPS</h4>
                    <div class="row">
                        <div class="col">
                            <p id="kur-rps" class="status-summary-ight-white mb-1">Semua Kurikulum</p>
                            <h2 class="text-info" id="jumlah-rps"></h2>
                        </div>
                        <div class="col text-end p-4">
                            <i class="h1 mdi mdi-note-text"></i>
                        </div>
                    </div>
                </div>
            </a>
            <a class="card card-sum mx-1 w-100 bg-primary text-white text-decoration-none"
                href="{{ route($currentPrefix . 'list-soal') }}">
                <div class="card-body pb-0">
                    <h4 class="card-title card-title-dash text-white mb-4">Jumlah Soal</h4>
                    <div class="row">
                        <div class="col">
                            <p id="kur-soal" class="status-summary-ight-white mb-1">Semua Kurikulum</p>
                            <h2 class="text-info" id="jumlah-soal"></h2>
                        </div>
                        <div class="col text-end p-4">
                            <i class="h1 mdi mdi-book"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    @if ($userOtoritas == 'Admin Universitas')
        <div class="form-group">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">CPL Keterampilan Umum</h4>
                            <canvas id="barChartKU"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">CPL Pengetahuan</h4>
                            <canvas id="barChartP"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">CPL Keterampilan Khusus</h4>
                            <canvas id="barChartKK"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        // Variabel untuk menyimpan instance chart agar bisa dihancurkan sebelum digambar ulang
        let chartP, chartKK, chartKU;

        // Mendapatkan base URL dinamis
        var baseURL = window.location.origin;
        const routePrefix = '{{ rtrim($currentPrefix, '.') }}';
        $(document).ready(function() {
            showCard('all');
            showChart('all');
        });

        $('#filter').change(function() {
            const selectedValue = $(this).val();
            const selectedText = $(this).find('option:selected').data('tahun');

            if (selectedValue !== 'all') {
                const kurikulumText = 'Kurikulum ' + selectedText;
                $('#kur-mk, #kur-cpl, #kur-rps, #kur-soal').text(kurikulumText);
            } else {
                $('#kur-mk, #kur-cpl, #kur-rps, #kur-soal').text('Semua Kurikulum');
            }

            showChart(selectedValue);
            showCard(selectedValue);
        });

        function showCard(filter) {
            $.ajax({
                url: `${baseURL}/${routePrefix}/dashboard-card/${filter}`, // Sesuaikan dengan route jika perlu
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $("#jumlah-mk").text(data.sum_mk);
                    $("#jumlah-cpl").text(data.sum_cpl);
                    $("#jumlah-rps").text(data.sum_rps);
                    $("#jumlah-soal").text(data.sum_soal);
                }
            });
        }

        function showChart(filter) {
            $.ajax({
                url: `${baseURL}/${routePrefix}/dashboard-chart/${filter}`, // Sesuaikan dengan route jika perlu
                type: "GET",
                dataType: "json",
                success: function(dt) {

                    var options = {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    callback: function(label) {
                                        if (Math.floor(label) === label) {
                                            return label;
                                        }
                                    }
                                }
                            }]
                        },
                        legend: {
                            display: false
                        },
                        elements: {
                            point: {
                                radius: 0
                            }
                        },
                        // Menonaktifkan semua event agar tidak interaktif
                        events: []
                    };

                    // Fungsi untuk menggambar atau memperbarui chart
                    function renderChart(chartInstance, canvasId, chartData, type = 'bar') {
                        if (chartInstance) {
                            chartInstance.destroy(); // Hancurkan chart lama
                        }
                        if ($(canvasId).length) {
                            var canvas = $(canvasId).get(0).getContext("2d");
                            return new Chart(canvas, {
                                type: type,
                                data: {
                                    labels: chartData.labels,
                                    datasets: [{
                                        data: chartData.counts,
                                        backgroundColor: chartData.colors,
                                        borderColor: chartData.borders,
                                        borderWidth: 1,
                                        fill: false
                                    }]
                                },
                                options: options
                            });
                        }
                    }

                    // Render setiap chart dengan data baru
                    chartP = renderChart(chartP, "#barChartP", dt.pengetahuan);
                    chartKK = renderChart(chartKK, "#barChartKK", dt.keterampilan_khusus); // Diubah
                    chartKU = renderChart(chartKU, "#barChartKU", dt.keterampilan_umum); // Diubah
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error for chart:', status, error, xhr.responseText);
                }
            });
        }
    </script>
@endsection
