@extends('dosen.template')
@section('content')
<?php $s = 0; ?>

@if (session()->has('failed'))
    <div class="alert alert-danger" role="alert" id="box">
        <div>{{session('failed')}}</div>
    </div>
@elseif (session()->has('success'))
    <div class="alert greenAdd" role="alert" id="box">
        <div>{{session('success')}}</div>
    </div>
@endif

<h3 class="px-4 pb-4 fw-bold text-center">Hasil Visualisasi Mahasiswa Angkatan {{$angkatan}}</h3>

<div class="form-group stretch-card" id="tugas">
    <div class="card">
        <div class="card-body">
                <div class="container">
                    {{-- kiri --}}
                    <div class="mt-4">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Cpl</h5>
                                    <canvas id="radarChart" ></canvas>
                                    <h6 class="keterangan">Keterangan:</h6>
                                    @foreach($labelCpl as $index => $label)
                                        <p>{{ $loop->iteration }}.{{ $label }}: {{ $judulCpl[$index] }}</p>
                                    @endforeach
                                </div>
                              
                            </div>
                            <div class="card mt-3">
                                    <div class="card-body">
                                        
                                        <h6 class="fw-bold">Soal dengan CPL Terendah :</h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Nama Course</th>
                                                        <th>Jenis</th>
                                                        <th>Soal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($soalTerendah as $key => $soalTerendah)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $soalTerendah['namaCourse'] }}</td>
                                                        <td>{{ $soalTerendah['Jenis'] }}</td>
                                                        <td>{{ $soalTerendah['soal'] }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            
                        </div>
                        {{-- Kanan --}}
                        <div class="col-sm-6">
                            {{-- Card 1 --}}
                            <div class="card">
                                <div class="card-body">

                                    <h6 class="fw-bold">Mata Kuliah Perhitungan CPL Angkatan {{$angkatan}}:</h6>
                                    <ol>
                                       @foreach($courseArray as $course => $namaCourse)
                                            <li>{{ $course }} - {{ $namaCourse }}</li>
                                        @endforeach
                                    </ol>
                                </div>
                            </div>
                            {{-- Card 2 --}}
                            <div class="card mt-3">
                                <div class="card-body">

                                    <h5 class="card-title">Lihat CPMK Angkatan</h5>
                                    <form method="POST" action="hasilvisualcpmk-mahasiswa" enctype="multipart/form-data">
                                        @csrf
                                        <div id="dynamicAddRemoveTugas">
                                            <div class="form-group row">
                                                <div class="col-5">
                                                    <div class="form-group">
                                                        <label>Course<span class="text-danger">*</span></label>
                                                        <select class="form-control" name="course">

                                                            {{-- @foreach($courseArray as $course => $namaCourse)
                                                            <option selected="true"  value="{{ $course . '-' . $namaCourse }}">{{ $course }} - {{ $namaCourse }}</option>
                                                            @endforeach --}}
                                                        </select>
                                                        <input type="text" name="angkatan" class="visually-hidden" value="{{$angkatan}}">
                                                             

                                                    </div>
                                                </div>
                                            
                                            </div>
                                        </div>
                                        <input type="submit" class="btn btn-primary" style="margin-top:-4%; margin-bottom:-1%" value="Submit">
                                    </form>
                                
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
              
          
        </div>
    </div>
</div>
<script>
        // console.log(<?php echo json_encode($labelCpl); ?>);
        var ctx = document.getElementById('radarChart').getContext('2d');
        var radarChart = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: <?php echo json_encode($labelCpl); ?>,
                datasets: [{
                    label: 'CPL',
                    data: <?php echo json_encode($countDataCpl); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scale: {
                     r: {
                        max: 100,
                        min: 0,
                        ticks: {
                            // stepSize: 0.5
                        }
                    }
                }
            }
        });
</script>

// <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> 

@endsection