@extends('admin.template')
@section('content')
    @php
        $isAptikom = auth()->check() && auth()->user()->prodi ? (bool) auth()->user()->prodi->is_aptikom : true;
    @endphp
    <style>
        .obe-card-box {
            background: #ffffff !important;
            border-radius: 16px !important;
            padding: 1.25rem 1.5rem !important;
            text-decoration: none !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            min-height: 130px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04) !important;
            position: relative !important;
            overflow: hidden !important;
        }

        .obe-card-box:hover {
            transform: translateY(-4px) !important;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.09) !important;
        }

        .obe-card-box .card-label {
            font-size: 0.8rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.5px !important;
            text-transform: uppercase !important;
            color: #475569 !important;
        }

        .obe-card-box .card-subtext {
            font-size: 0.75rem !important;
            color: #64748b !important;
        }

        .obe-card-box .card-value {
            font-size: 2.6rem !important;
            font-weight: 800 !important;
            line-height: 1 !important;
            margin-top: 12px !important;
        }

        .obe-card-box .icon-badge {
            width: 48px !important;
            height: 48px !important;
            border-radius: 12px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 24px !important;
            flex-shrink: 0 !important;
        }

        .icon-badge::before,
        .icon-badge:before {
            content: none !important;
            display: none !important;
        }

        @if ($isAptikom)
            /* Aptikom Color Accents (Blue Theme) */
            .card-accent-1 { border: 1px solid rgba(2, 132, 199, 0.25) !important; border-top: 4px solid #0284c7 !important; }
            .card-accent-1 .card-value { color: #0284c7 !important; }
            .card-accent-1 .icon-badge { background: rgba(2, 132, 199, 0.12) !important; color: #0284c7 !important; }

            .card-accent-2 { border: 1px solid rgba(16, 185, 129, 0.25) !important; border-top: 4px solid #10b981 !important; }
            .card-accent-2 .card-value { color: #059669 !important; }
            .card-accent-2 .icon-badge { background: rgba(16, 185, 129, 0.12) !important; color: #10b981 !important; }

            .card-accent-3 { border: 1px solid rgba(124, 58, 237, 0.25) !important; border-top: 4px solid #7c3aed !important; }
            .card-accent-3 .card-value { color: #7c3aed !important; }
            .card-accent-3 .icon-badge { background: rgba(124, 58, 237, 0.12) !important; color: #7c3aed !important; }

            .card-accent-4 { border: 1px solid rgba(217, 119, 6, 0.25) !important; border-top: 4px solid #d97706 !important; }
            .card-accent-4 .card-value { color: #d97706 !important; }
            .card-accent-4 .icon-badge { background: rgba(217, 119, 6, 0.12) !important; color: #d97706 !important; }

            .card-accent-5 { border: 1px solid rgba(79, 70, 229, 0.25) !important; border-top: 4px solid #4f46e5 !important; }
            .card-accent-5 .card-value { color: #4f46e5 !important; }
            .card-accent-5 .icon-badge { background: rgba(79, 70, 229, 0.12) !important; color: #4f46e5 !important; }

            .card-accent-6 { border: 1px solid rgba(225, 29, 72, 0.25) !important; border-top: 4px solid #e11d48 !important; }
            .card-accent-6 .card-value { color: #e11d48 !important; }
            .card-accent-6 .icon-badge { background: rgba(225, 29, 72, 0.12) !important; color: #e11d48 !important; }
        @else
            /* Non-Aptikom Color Accents (Gold/Amber Theme) */
            .card-accent-1 { border: 1px solid rgba(217, 119, 6, 0.3) !important; border-top: 4px solid #b45309 !important; }
            .card-accent-1 .card-value { color: #b45309 !important; }
            .card-accent-1 .icon-badge { background: rgba(217, 119, 6, 0.14) !important; color: #b45309 !important; }

            .card-accent-2 { border: 1px solid rgba(16, 185, 129, 0.3) !important; border-top: 4px solid #047857 !important; }
            .card-accent-2 .card-value { color: #047857 !important; }
            .card-accent-2 .icon-badge { background: rgba(16, 185, 129, 0.14) !important; color: #047857 !important; }

            .card-accent-3 { border: 1px solid rgba(234, 88, 12, 0.3) !important; border-top: 4px solid #c2410c !important; }
            .card-accent-3 .card-value { color: #c2410c !important; }
            .card-accent-3 .icon-badge { background: rgba(234, 88, 12, 0.14) !important; color: #c2410c !important; }

            .card-accent-4 { border: 1px solid rgba(225, 29, 72, 0.3) !important; border-top: 4px solid #be123c !important; }
            .card-accent-4 .card-value { color: #be123c !important; }
            .card-accent-4 .icon-badge { background: rgba(225, 29, 72, 0.14) !important; color: #be123c !important; }

            .card-accent-5 { border: 1px solid rgba(79, 70, 229, 0.3) !important; border-top: 4px solid #4338ca !important; }
            .card-accent-5 .card-value { color: #4338ca !important; }
            .card-accent-5 .icon-badge { background: rgba(79, 70, 229, 0.14) !important; color: #4338ca !important; }

            .card-accent-6 { border: 1px solid rgba(13, 148, 136, 0.3) !important; border-top: 4px solid #0f766e !important; }
            .card-accent-6 .card-value { color: #0f766e !important; }
            .card-accent-6 .icon-badge { background: rgba(13, 148, 136, 0.14) !important; color: #0f766e !important; }
        @endif

        .chart-box-card {
            border-radius: 16px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03) !important;
            background: #ffffff;
            transition: box-shadow 0.2s ease;
        }

        .chart-box-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06) !important;
        }

        .chart-title-header {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-bottom: 3px solid {{ $isAptikom ? '#007bff' : '#d97706' }};
            padding-bottom: 6px;
            margin-bottom: 1.25rem;
        }
    </style>

    <div class="container-fluid px-3 py-2">
        <div class="row g-3 mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <a class="obe-card-box card-accent-1" href="{{ route($currentPrefix . 'list-user') }}">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="card-label">JUMLAH USER</div>
                            <div class="card-subtext">Dosen & Penjamin Mutu</div>
                        </div>
                        <div class="icon-badge">
                            <i class="mdi mdi-account-multiple"></i>
                        </div>
                    </div>
                    <div class="card-value">{{ $userCount }}</div>
                </a>
            </div>
            <div class="col-md-6">
                <a class="obe-card-box card-accent-2" href="{{ route($currentPrefix . 'list-kurikulum') }}">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="card-label">JUMLAH KURIKULUM</div>
                            <div class="card-subtext">Tahun Kurikulum Aktif</div>
                        </div>
                        <div class="icon-badge">
                            <i class="mdi mdi-folder-multiple"></i>
                        </div>
                    </div>
                    <div class="card-value">{{ $kurikulums->count() }}</div>
                </a>
            </div>
        </div>

        <div class="row align-items-center mb-3">
            <div class="col-md-4 col-lg-3">
                <label for="filter" class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Filter Kurikulum</label>
                <select name="kurikulum" id="filter" class="form-select rounded-3 shadow-sm border-1">
                    <option value="all">Semua Kurikulum</option>
                    @foreach ($kurikulums as $kur)
                        <option value="{{ $kur->id }}" data-tahun="{{ $kur->tahun }}">{{ $kur->tahun }} - {{ $kur->prodi->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3 mb-3">
                <a class="obe-card-box card-accent-3" href="{{ route($currentPrefix . 'list-cpl') }}">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="card-label">JUMLAH CPL</div>
                            <div id="kur-cpl" class="card-subtext">Semua Kurikulum</div>
                        </div>
                        <div class="icon-badge">
                            <i class="mdi mdi-view-list"></i>
                        </div>
                    </div>
                    <div class="card-value" id="jumlah-cpl">-</div>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-xl-3 mb-3">
                <a class="obe-card-box card-accent-4" href="{{ route($currentPrefix . 'list-mk') }}">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="card-label">JUMLAH MATA KULIAH</div>
                            <div id="kur-mk" class="card-subtext">Semua Kurikulum</div>
                        </div>
                        <div class="icon-badge">
                            <i class="mdi mdi-book-open-page-variant"></i>
                        </div>
                    </div>
                    <div class="card-value" id="jumlah-mk">-</div>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-xl-3 mb-3">
                <a class="obe-card-box card-accent-5" href="{{ route($currentPrefix . 'list-rps') }}">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="card-label">JUMLAH RPS</div>
                            <div id="kur-rps" class="card-subtext">Semua Kurikulum</div>
                        </div>
                        <div class="icon-badge">
                            <i class="mdi mdi-note-text"></i>
                        </div>
                    </div>
                    <div class="card-value" id="jumlah-rps">-</div>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-xl-3 mb-3">
                <a class="obe-card-box card-accent-6" href="{{ route($currentPrefix . 'list-soal') }}">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="card-label">JUMLAH SOAL</div>
                            <div id="kur-soal" class="card-subtext">Semua Kurikulum</div>
                        </div>
                        <div class="icon-badge">
                            <i class="mdi mdi-book-multiple"></i>
                        </div>
                    </div>
                    <div class="card-value" id="jumlah-soal">-</div>
                </a>
            </div>
        </div>
    </div>

    @if ($userOtoritas == 'Admin Universitas')
        <div class="container-fluid my-3 px-3">
            <div class="row g-3">
                <div class="col-lg-12 mb-3">
                    <div class="card chart-box-card w-100">
                        <div class="card-body p-4">
                            <div class="chart-title-header">
                                <i class="mdi mdi-chart-line text-primary"></i> CPL Keterampilan Umum
                            </div>
                            <canvas id="barChartKU"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 mb-3">
                    <div class="card chart-box-card w-100">
                        <div class="card-body p-4">
                            <div class="chart-title-header">
                                <i class="mdi mdi-chart-bar text-success"></i> CPL Pengetahuan
                            </div>
                            <canvas id="barChartP"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 mb-3">
                    <div class="card chart-box-card w-100">
                        <div class="card-body p-4">
                            <div class="chart-title-header">
                                <i class="mdi mdi-chart-histogram text-warning"></i> CPL Keterampilan Khusus
                            </div>
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
