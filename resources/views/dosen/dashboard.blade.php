@extends('dosen.template')
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

            .card-accent-3 { border: 1px solid rgba(217, 119, 6, 0.25) !important; border-top: 4px solid #d97706 !important; }
            .card-accent-3 .card-value { color: #d97706 !important; }
            .card-accent-3 .icon-badge { background: rgba(217, 119, 6, 0.12) !important; color: #d97706 !important; }
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
        <div class="row g-3">
            <div class="col-md-6 col-lg-4 mb-3">
                <a class="obe-card-box card-accent-1" href="rps/list-rps">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="card-label">JUMLAH RPS</div>
                            <div class="card-subtext">Rencana Pembelajaran Semester</div>
                        </div>
                        <div class="icon-badge">
                            <i class="mdi mdi-book-open-page-variant"></i>
                        </div>
                    </div>
                    <div class="card-value">{{ $rpss->count() }}</div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-3">
                <a class="obe-card-box card-accent-2" href="cplmk/list-cplmk">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="card-label">JUMLAH CPLMK</div>
                            <div class="card-subtext">Pemetaan CPL Mata Kuliah</div>
                        </div>
                        <div class="icon-badge">
                            <i class="mdi mdi-view-list"></i>
                        </div>
                    </div>
                    <div class="card-value">{{ $cplmks->count() }}</div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-3">
                <a class="obe-card-box card-accent-3" href="cpmk/list-cpmk">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="card-label">JUMLAH CPMK</div>
                            <div class="card-subtext">Capaian Pembelajaran MK</div>
                        </div>
                        <div class="icon-badge">
                            <i class="mdi mdi-chart-donut"></i>
                        </div>
                    </div>
                    <div class="card-value">{{ $cpmks->count() }}</div>
                </a>
            </div>
        </div>
    </div>
    @if (auth()->user()->otoritas->otoritas === 'Dosen')
        <div class="container-fluid my-3 px-3">
            <div class="row g-3">
                <div class="col-lg-6 grid-margin stretch-card">
                    <div class="card chart-box-card w-100">
                        <div class="card-body p-4">
                            <div class="chart-title-header">
                                <i class="mdi mdi-chart-bar text-primary"></i> CPL Pengetahuan Terpakai
                            </div>
                            <canvas id="barChartP"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 grid-margin stretch-card">
                    <div class="card chart-box-card w-100">
                        <div class="card-body p-4">
                            <div class="chart-title-header">
                                <i class="mdi mdi-chart-histogram text-success"></i> CPL Keterampilan Terpakai
                            </div>
                            <canvas id="barChartK"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script>
        $.ajax({
            url: `${window.location.pathname.split('/dashboard')[0]}/dashboard-chart`,
            type: "GET",
            dataType: "json",
            success: function(dt) {
                var dataS = {
                    labels: dt.sikap,
                    datasets: [{
                        label: 'Jumlah MK',
                        data: dt.jumlahs,
                        backgroundColor: dt.warnas,
                        borderColor: dt.borders,
                        borderWidth: 1,
                        fill: false
                    }]
                };
                var dataU = {
                    labels: dt.umum,
                    datasets: [{
                        label: 'Jumlah MK',
                        data: dt.jumlahu,
                        backgroundColor: dt.warnau,
                        borderColor: dt.borderu,
                        borderWidth: 1,
                        fill: false
                    }]
                };
                var dataP = {
                    labels: dt.pengetahuan,
                    datasets: [{
                        label: 'Jumlah MK',
                        data: dt.jumlahp,
                        backgroundColor: dt.warnap,
                        borderColor: dt.borderp,
                        borderWidth: 1,
                        fill: false
                    }]
                };
                var dataK = {
                    labels: dt.keterampilan,
                    datasets: [{
                        label: 'Jumlah MK',
                        data: dt.jumlahk,
                        backgroundColor: dt.warnak,
                        borderColor: dt.borderk,
                        borderWidth: 1,
                        fill: false
                    }]
                };
                var options = {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
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
                    }

                };
                if ($("#barChartP").length) {
                    var barChartCanvas = $("#barChartP").get(0).getContext("2d");
                    // This will get the first returned node in the jQuery collection.
                    var barChart = new Chart(barChartCanvas, {
                        type: 'bar',
                        data: dataP,
                        options: options
                    });
                }
                if ($("#barChartK").length) {
                    var barChartCanvas = $("#barChartK").get(0).getContext("2d");
                    // This will get the first returned node in the jQuery collection.
                    var barChart = new Chart(barChartCanvas, {
                        type: 'bar',
                        data: dataK,
                        options: options
                    });
                }
            }
        });
    </script>
@endsection
