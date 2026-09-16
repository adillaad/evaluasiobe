@extends('penjamin-mutu.template')
@section('content')
    @php
        $isAptikom = auth()->check() && auth()->user()->prodi ? (bool) auth()->user()->prodi->is_aptikom : true;
        $userRole = auth()->user()->otoritas->otoritas ?? '';
        $isUnivLevel = in_array($userRole, ['Penjamin Mutu Universitas', 'Wakil Rektor']);
        $isFacultyLevel = in_array($userRole, ['Penjamin Mutu Fakultas', 'Wakil Dekan']);
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
            font-size: 2.4rem !important;
            font-weight: 800 !important;
            line-height: 1.1 !important;
            margin-top: 10px !important;
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

            .card-accent-3 { border: 1px solid rgba(225, 29, 72, 0.25) !important; border-top: 4px solid #e11d48 !important; }
            .card-accent-3 .card-value { color: #e11d48 !important; }
            .card-accent-3 .icon-badge { background: rgba(225, 29, 72, 0.12) !important; color: #e11d48 !important; }

            .card-accent-4 { border: 1px solid rgba(217, 119, 6, 0.25) !important; border-top: 4px solid #d97706 !important; }
            .card-accent-4 .card-value { color: #d97706 !important; }
            .card-accent-4 .icon-badge { background: rgba(217, 119, 6, 0.12) !important; color: #d97706 !important; }
        @else
            /* Non-Aptikom Color Accents (Gold/Amber Theme) */
            .card-accent-1 { border: 1px solid rgba(217, 119, 6, 0.3) !important; border-top: 4px solid #b45309 !important; }
            .card-accent-1 .card-value { color: #b45309 !important; }
            .card-accent-1 .icon-badge { background: rgba(217, 119, 6, 0.14) !important; color: #b45309 !important; }

            .card-accent-2 { border: 1px solid rgba(16, 185, 129, 0.3) !important; border-top: 4px solid #047857 !important; }
            .card-accent-2 .card-value { color: #047857 !important; }
            .card-accent-2 .icon-badge { background: rgba(16, 185, 129, 0.14) !important; color: #047857 !important; }

            .card-accent-3 { border: 1px solid rgba(225, 29, 72, 0.3) !important; border-top: 4px solid #be123c !important; }
            .card-accent-3 .card-value { color: #be123c !important; }
            .card-accent-3 .icon-badge { background: rgba(225, 29, 72, 0.14) !important; color: #be123c !important; }

            .card-accent-4 { border: 1px solid rgba(234, 88, 12, 0.3) !important; border-top: 4px solid #c2410c !important; }
            .card-accent-4 .card-value { color: #c2410c !important; }
            .card-accent-4 .icon-badge { background: rgba(234, 88, 12, 0.14) !important; color: #c2410c !important; }
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

    {{-- 4 Stat Cards Default Soal --}}
    <div class="container-fluid px-3 py-2">
        <div class="row g-3">
            <div class="col-12 col-sm-6 col-xl-3 mb-3">
                <a class="obe-card-box card-accent-1" href="list-soal">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="card-label">JUMLAH SOAL</div>
                            <div class="card-subtext">Total Soal Evaluasi</div>
                        </div>
                        <div class="icon-badge">
                            <i class="mdi mdi-file-document-box-multiple-outline"></i>
                        </div>
                    </div>
                    <div class="card-value">{{ $soal->count() }}</div>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-xl-3 mb-3">
                <a class="obe-card-box card-accent-2" href="list-soal">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="card-label">SOAL VALID</div>
                            <div class="card-subtext">Sudah Diverifikasi</div>
                        </div>
                        <div class="icon-badge">
                            <i class="mdi mdi-checkbox-marked-circle-outline"></i>
                        </div>
                    </div>
                    <div class="card-value">{{ $valid->count() }}</div>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-xl-3 mb-3">
                <a class="obe-card-box card-accent-3" href="list-soal">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="card-label">SOAL DITOLAK</div>
                            <div class="card-subtext">Perlu Perbaikan</div>
                        </div>
                        <div class="icon-badge">
                            <i class="mdi mdi-close-circle-outline"></i>
                        </div>
                    </div>
                    <div class="card-value">{{ $tolak->count() }}</div>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-xl-3 mb-3">
                <a class="obe-card-box card-accent-4" href="list-soal">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="card-label">BELUM DIPERIKSA</div>
                            <div class="card-subtext">Menunggu Validasi</div>
                        </div>
                        <div class="icon-badge">
                            <i class="mdi mdi-clock-alert-outline"></i>
                        </div>
                    </div>
                    <div class="card-value">{{ $belum->count() }}</div>
                </a>
            </div>
        </div>
    </div>

    {{-- SECTION: CHARTS UNTUK PROGRAM STUDI / DOSEN (Role Prodi Level) --}}
    @if (in_array(auth()->user()->otoritas->otoritas, ['Dosen', 'Penjamin Mutu Program Studi', 'Kepala Program Studi']))
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

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        let baseURL = window.location.origin + window.location.pathname.split('/').slice(0, 2).join('/');
        let otoritas = "{{ auth()->user()->otoritas->otoritas }}".trim()
            .toLowerCase()
            .replace(/penjamin mutu /i, '')
            .replace(/\s+/g, '-');

        $(document).ready(function() {
            @if (in_array(auth()->user()->otoritas->otoritas, ['Dosen', 'Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                showChart();
            @endif
        });

        function showChart(url) {
            const chartUrl = otoritas === "kepala-program-studi" 
                        ? `${baseURL}/dashboard-chart` 
                        : `${baseURL}/${otoritas}/dashboard-chart`;
            $.ajax({
                url: chartUrl,
                type: "GET",
                dataType: "json",
                success: function(dt) {
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
                        var barChart = new Chart(barChartCanvas, {
                            type: 'bar',
                            data: dataP,
                            options: options
                        });
                    }
                    if ($("#barChartK").length) {
                        var barChartCanvas = $("#barChartK").get(0).getContext("2d");
                        var barChart = new Chart(barChartCanvas, {
                            type: 'bar',
                            data: dataK,
                            options: options
                        });
                    }
                }
            });
        }
    </script>
@endsection