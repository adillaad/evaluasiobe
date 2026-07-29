@php
    $routePrefix = [
        'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.',
        'Kepala Program Studi' => 'kepala-program-studi.',
    ];
    $userOtoritas = $userOtoritas ?? auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas] ?? 'penjamin-mutu.program-studi.';
@endphp

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

    <h3 class="px-4 pb-4 fw-bold text-center">Halaman Pemetaan Profesi-CPMK</h3>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                {{-- <div id="CPL-PL"></div> --}}
                <h4 class="card-title">List Pemetaan Profesi-CPMK</h4>
                @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                    <a href="{{ route($currentPrefix . 'profesi-cpmk-add') }}" class="btn btn-primary">Tambah
                        Profesi-CPMK</a>
                    <a href="{{ route($currentPrefix . 'generate-pdf-profesi-cpmk', request()->query()) }}"
                        class="btn btn-danger btn-pdf" target="_blank">
                        <i class="mdi mdi-file-pdf-box me-1"></i> Unduh PDF
                    </a>

                    <a href="{{ route($currentPrefix . 'print-profesi-cpmk', array_merge(request()->query(), ['backUrl' => url()->current()])) }}"
                        class="btn btn-success btn-pdf" target="_blank">
                        <i class="mdi mdi-printer me-1"></i> Cetak
                    </a>
                @endif
                <div class="table-responsive mt-3">
                    <table class="table table-hover dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Kode CPMK</th>
                                <th>Deskripsi CPMK</th>
                                <th>Profesi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cpmks as $index => $cpmk)
                                @php
                                    $total = $cpmk->profesis->count();
                                @endphp

                                @if ($total > 0)

                                    @foreach ($cpmk->profesis as $i => $profesi)
                                        <tr
                                            class="
                @if ($total > 1) @if ($i == 0)
                        first-row
                    @elseif($i == $total - 1)
                        last-row
                    @else
                        middle-row @endif
                @endif
            ">

                                            {{-- No --}}
                                            <td>
                                                @if ($i == 0)
                                                    {{ $index + 1 }}
                                                @endif
                                            </td>

                                            {{-- Kode CPMK --}}
                                            <td>
                                                @if ($i == 0)
                                                    {{ $cpmk->kode }}
                                                @endif
                                            </td>

                                            {{-- Deskripsi --}}
                                            <td>
                                                @if ($i == 0)
                                                    {{ $cpmk->judul }}
                                                @endif
                                            </td>

                                            {{-- Profesi --}}
                                            <td>{{ $profesi->nama }}</td>

                                            {{-- Aksi --}}
                                            <td class="d-flex gap-2">
                                                <a href="{{ route($currentPrefix . 'profesi-cpmk-edit', $cpmk->id) }}"
                                                    class="btn btn-sm btn-warning">Edit</a>

                                                <form
                                                    action="{{ route($currentPrefix . 'profesi-cpmk-delete', [$cpmk->id, $profesi->id]) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        onclick="event.preventDefault(); if(confirm('Yakin hapus?')) this.closest('form').submit();"
                                                        class="btn btn-sm btn-danger">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>

                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="no-border-row">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $cpmk->kode }}</td>
                                        <td>{{ $cpmk->judul }}</td>
                                        <td class="text-danger">Belum dipetakan</td>
                                        <td>
                                            <a href="{{ route($currentPrefix . 'profesi-cpmk-add', ['cpmk_id' => $cpmk->id]) }}"
                                                class="btn btn-sm btn-primary">
                                                Tambah
                                            </a>
                                        </td>
                                    </tr>

                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>\
    <style>
        .table td,
        .table th {
            border: none !important;
        }
        table.dataTable tbody tr td {
            border: none !important;
        }
    </style>
@endsection
