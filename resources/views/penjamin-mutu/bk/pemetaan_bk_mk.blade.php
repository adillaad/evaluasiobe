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
    <h3 class="px-4 pb-4 fw-bold text-center">Halaman BK-MK</h3>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                {{-- <div id="BK-MK"></div> --}}
                <h4 class="card-title">List Pemetaan BK-MK</h4>
                @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                <a href="{{ route($currentPrefix . 'bk.bk-mk-add') }}" class="btn btn-primary">Tambah BK-MK</a>
                @endif
                <div class="table-responsive mt-4">
                    <table class="table table-hover dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Kode MK</th>
                                <th>Nama MK</th>
                                <th>SKS</th>
                                @foreach ($bks as $bk)
                                    <th>{{ $bk->kode }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mks as $index => $mk)
                                <tr>
                                    <td class="py-4">{{ $index + 1 }}</td>
                                    <td>{{ $mk->kode }}</td>
                                    <td>{{ $mk->nama }}</td>
                                    <td>{{ $mk->bobot_teori + $mk->bobot_praktikum }}</td>
                                    @foreach ($bks as $bk)
                                        <td style="text-align:center; color:#1d3cb4;">
                                            @if ($mk->bk->contains('id', $bk->id))
                                                ✔
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-body">
                <h4 class="card-title">Deskripsi</h4>
                <div class="scrollable-descriptions" style="max-height: 400px; overflow-y: auto;">
                    @foreach ($mks as $mk)
                        <div class="mb-3">
                            <p><strong>{{ $mk->kode }} :</strong> {{ $mk->nama }}</p>
                            <p><strong>Deskripsi : </strong>{{ $mk->deskripsi }}</p>
                            <p><strong>BK :</strong></p>
                            @if ($mk->bk->isNotEmpty())
                                <ul>
                                    @foreach ($mk->bk as $bk)
                                        <li>{{ $bk->kode }} - {{ $bk->nama }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <em>Tidak ada Bahan Kajian terkait.</em>
                            @endif
                            <hr>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
