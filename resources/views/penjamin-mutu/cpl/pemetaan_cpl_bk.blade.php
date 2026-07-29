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
        <div>{{session('failed')}}</div>
    </div>
@elseif (session()->has('success'))
    <div class="alert greenAdd" role="alert" id="box">
        <div>{{session('success')}}</div>
    </div>
@endif

<h3 class="px-4 pb-4 fw-bold text-center">Halaman CPL-BK</h3>
@if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Pemetaan CPL ke Bahan Kajian</h4>

                <form action="{{ route($currentPrefix.'cpl.cpl-bk-store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="cpl_id">Pilih CPL:</label>
                        <select class="form-control" id="cpl_id" name="cpl_id" required onchange="getSelectedValue();">
                            <option value="" selected disabled>Pilih CPL</option>
                            @foreach ($cpls as $cpl)
                                <option value="{{ $cpl->id }}">{{ $cpl->kode }} - {{ $cpl->judul }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="bk_ids">Pilih Bahan Kajian yang Terkait:</label>
                        <div class="border ps-5" style="max-height: 200px; overflow-y: auto;">
                            @foreach ($bks as $bk)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="bk_ids[]" value="{{ $bk->id }}" id="bk_{{ $bk->id }}">
                                    <label class="form-check-label" for="bk_{{ $bk->id }}">
                                        {{ $bk->kode }} - {{ $bk->nama }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Tambah CPL-BK</button>
                </form>
            </div>
        </div>
    </div>
@endif

<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-body">
            {{-- <div id="CPL-BK"></div> --}}
            <h4 class="card-title">List Pemetaan CPL-BK</h4>
            {{-- <a href="{{ route( $currentPrefix. 'cpl.cpl-bk-add') }}" class="btn btn-primary">Tambah CPL-BK</a> --}}
            <div class="table-responsive mt-4">
                <table class="table table-hover dataTable ">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Kode BK</th>
                            @foreach ($cpls as $cpl)
                                <th>{{ $cpl->kode }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($bks as $index=>$bk)
                            <tr>
                                <td class="py-4">{{ $index+1 }}</td>
                                <td>{{ $bk->kode }}</td>    
                                @foreach ($cpls as $cpl)
                                    <td style="text-align:center; color:#1d3cb4;">
                                        @if ($bk->cpl->contains('id',$cpl->id))
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
                @foreach($bks as $bk)
                    <div class="mb-3">
                        <h6><strong>{{ $bk->kode }} :</strong> {{ $bk->nama }}</h6>
                        <p><strong>CPL:</strong></p>
                        @if ($bk->cpl->isNotEmpty())
                            <ul>
                                @foreach ($bk->cpl as $cpl)
                                    <li>{{ $cpl->kode }} - {{ $cpl->judul }}</li>
                                @endforeach
                            </ul>
                        @else
                            <em>Tidak ada CPL terkait.</em>
                        @endif
                        <hr>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection