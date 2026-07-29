{{-- @php
    $routePrefix = [
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp --}}

@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
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

<h3 class="px-4 pb-4 fw-bold text-center">Halaman Tahap Penilaian</h3>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Tahap Penilaian</h4>
            @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
            <a href="{{ route( $currentPrefix . 'asesmen.instrumen-penilaian-add') }}" class="btn btn-primary">Tambah Data Instrumen</a>
            @endif
            <div class="table-responsive mt-4">
                <table class="table table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>CPL</th>
                            <th>MK</th>
                            <th>CPMK</th>
                            <th>Tahap Penilaian</th>
                            <th>Metode Penilaian</th>
                            <th>Instrumen</th>
                            <th>Kriteria</th>
                            <th>Bobot</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penilaian as $row)
                            @if ($metodes->contains('id',$row->id))
                                @php
                                    $kriteria = $instrumens->where('id', $row->id);
                                    $rowspan = max(1, $kriteria->count()); 
                                @endphp
                            <tr>
                                <td rowspan="{{ $rowspan }}">{{ $row->cpl_kode }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $row->mk_kode }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $row->cpmk_kode }}</td>
                                <td rowspan="{{ $rowspan }}">{{ ucfirst($row->tahap_penilaian) }}</td>
                                <td rowspan="{{ $rowspan }}">
                                    @php
                                        $metode = $metodes->where('id', $row->id)->pluck('metode')->implode(', ');
                                    @endphp
                                    {{ $metode }}
                                </td>
                                <td rowspan="{{ $rowspan }}">{{ $row->instrumen }}</td>

                                @if ($kriteria->isNotEmpty())
                                    @php $first = true; @endphp
                                    @foreach ($kriteria as $kriteriaItem)
                                        @if (!$first)
                                            <tr>
                                        @endif
                                        <td>{{ $kriteriaItem->nama_kriteria }}</td>
                                        <td>{{ $kriteriaItem->bobot_metode }}</td>
                                        @if (!$first)
                                            </tr>
                                        @endif
                                        @php $first = false; @endphp
                                    @endforeach
                                @else
                                    <td>-</td>
                                    <td>-</td>
                                @endif
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection