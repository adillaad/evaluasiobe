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
    <h3 class="px-4 pb-4 fw-bold text-center">Halaman CPL-MK-CPMK</h3>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                {{-- <div id="CPL-MK-CPMK"></div> --}}
                <h4 class="card-title">List Pemetaan CPL-MK-CPMK</h4>
                <div class="table-responsive">
                    <table class="table table-hover dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th style="position: relative; width: 30px; height: 10px;">
                                    <span class="double-table-head-data-top-right">CPL</span>
                                    <span class="double-table-head-data-bottom-left">MK</span>
                                    <span class="diagonal-line"></span>
                                </th>
                                @foreach ($cpls as $cpl)
                                    <th style="vertical-align: top">{{ $cpl->kode }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mks as $mk)
                                <tr>
                                    <td class="bg-light fw-bold">{{ $mk->kode }}</td>
                                    @foreach ($cpls as $cpl)
                                        <td>
                                            @if ($mk->cpl->contains('id', $cpl->id))
                                                @foreach ($mk->cpmks as $cpmk)
                                                    @if ($cpmk->cpl && $cpmk->cpl->id == $cpl->id)
                                                        {{ $cpmk->kode }}<br>
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
        <div class="card mt-4">
            <div class="card-body">
                <h4 class="card-title">Deskripsi</h4>
                <div class="scrollable-descriptions" style="max-height: 400px; overflow-y: auto;">
                    @foreach ($mks as $mk)
                        <div class="mb-3">
                            <h5><strong>{{ $mk->kode }} - {{ $mk->nama }}</strong></h5>
                            <p><strong>CPMK:</strong></p>
                            <ul>
                                @forelse ($mk->cpmks as $cpmk)
                                    <li>
                                        {{ $cpmk->kode }} - {{ $cpmk->judul }}
                                        {{-- <ul>
                                    <li><strong>CPL Terkait:</strong>
                                        @if ($cpmk->cpl->count())
                                          @foreach ($cpmk->cpl as $cpl)
                                            {{ $cpl->kode }} - {{ $cpl->judul }} ,
                                          @endforeach
                                        @else
                                            Tidak ada CPL terkait
                                        @endif
                                    </li>
                                </ul> --}}
                                    </li>
                                @empty
                                    <li>CPMK tidak tersedia</li>
                                @endforelse
                            </ul>
                            <p><strong>CPL:</strong></p>
                            <ul>
                                @forelse ($mk->cpl as $cpl)
                                    <li>
                                        {{ $cpl->kode }} - {{ $cpl->judul }}
                                        {{-- <ul>
                                    <li><strong>CPL Terkait:</strong>
                                        @if ($cpmk->cpl->count())
                                          @foreach ($cpmk->cpl as $cpl)
                                            {{ $cpl->kode }} - {{ $cpl->judul }} ,
                                          @endforeach
                                        @else
                                            Tidak ada CPL terkait
                                        @endif
                                    </li>
                                </ul> --}}
                                    </li>
                                @empty
                                    <li>CPL tidak tersedia</li>
                                @endforelse
                            </ul>
                            <hr>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
