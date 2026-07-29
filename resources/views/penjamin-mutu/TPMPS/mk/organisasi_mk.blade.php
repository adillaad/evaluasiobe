@extends(auth()->user()->otoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
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

<h3 class="px-4 pb-4 fw-bold text-center">Halaman Organisasi Mata Kuliah</h3>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div id="Organisasi_MK"></div>
        </div>
    </div>
</div>

<script>
    Highcharts.chart('Organisasi_MK', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Organisasi MK'
        },
        xAxis: {
            categories: ['VIII', 'VII', 'VI', 'V', 'IV', 'III', 'II', 'I'],
            crosshair: true,
            accessibility: {
                description: 'MK'
            }
        },
        yAxis: {
            min: 0,
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
                name: 'SKS',
                data: [9, 19, 19, 20, 19, 20, 20, 18]
            },
            {
                name: 'JML MK',
                data: [3, 7, 7, 6, 6, 7, 7, 7]
            },
        ]
    });
</script>
@endsection
