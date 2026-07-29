@extends(auth()->user()->otoritas->otoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
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
    <h3 class="px-4 pb-4 fw-bold text-center">Halaman CPL-BK-MK</h3>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                {{-- <div id="CPL-BK-MK"></div> --}}
                <h4 class="card-title">List Pemetaan CPL-BK-MK</h4>
                <div class="table-responsive">
                    <table class="table table-hover dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th style="position: relative; width: 100px; height: 30px;">
                                    <span class="double-table-head-data-top-right">CPL</span>
                                    <span class="double-table-head-data-bottom-left">BK</span>
                                    <span class="diagonal-line"></span>
                                </th>
                                @foreach ($cpls as $cpl)
                                    <th style="vertical-align: top">{{ $cpl->kode }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bks as $bk)
                                <tr>
                                    <td class="bg-light fw-bold">{{ $bk->kode }}</td>
                                    @foreach ($cpls as $cpl)
                                        <td>
                                            @if ($bk->cpl->contains('id', $cpl->id))
                                                @foreach ($bk->mk as $mk)
                                                    @if ($mk->cpl->contains($cpl->id))
                                                        {{ $mk->kode }}<br>
                                                    @endif
                                                @endforeach
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
        {{-- Card Deskripsi --}}
        <div class="card mt-4">
            <div class="card-body">
                <h4 class="card-title">Deskripsi CPL, BK, dan MK</h4>
                <div class="scrollable-descriptions" style="max-height: 400px; overflow-y: auto;">
                    <h5>Deskripsi CPL</h5>
                    @foreach ($cpls as $cpl)
                        <div class="mb-3">
                            <p><strong>{{ $cpl->kode }}</strong>: {{ $cpl->judul }}</p>
                        </div>
                    @endforeach
                    <hr>
                    <h5>Deskripsi BK</h5>
                    @foreach ($bks as $bk)
                        <div class="mb-3">
                            <p><strong>{{ $bk->kode }}</strong>: {{ $bk->nama }}</p>
                        </div>
                    @endforeach
                    <hr>
                    <h5>Deskripsi MK</h5>
                    @foreach ($bks as $bk)
                        @foreach ($bk->mk as $mk)
                            <div class="mb-3">
                                <p><strong>{{ $mk->kode }}</strong>: {{ $mk->nama }}</p>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
