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

    <div class="container-fluid">
        @if(isset($kurikulums))
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <x-filter-form :showKurikulum="true" :kurikulums="$kurikulums" />
                </div>
            </div>
        @endif
        <div class="card">
            <div class="card-body">
                {{-- <div id="CPL-PL"></div> --}}
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <h4 class="card-title mb-0 me-auto">List Pemetaan Profesi-CPMK</h4>
                    @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                        <a href="{{ route($currentPrefix . 'profesi-cpmk-add') }}" class="btn btn-primary btn-icon-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            <span>Tambah Profesi-CPMK</span>
                        </a>
                        <a href="{{ route($currentPrefix . 'generate-pdf-profesi-cpmk', request()->query()) }}"
                            class="btn btn-danger btn-icon-text" target="_blank">
                            <i class="ti-file me-1"></i> Unduh PDF
                        </a>

                        <a href="{{ route($currentPrefix . 'print-profesi-cpmk', array_merge(request()->query(), ['backUrl' => url()->current()])) }}"
                            class="btn btn-info btn-icon-text" target="_blank">
                            <i class="ti-printer me-1"></i> Cetak
                        </a>
                    @endif
                </div>
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
                                            <td>
                                                <div class="d-flex align-items-center gap-1">
                                                    <a href="{{ route($currentPrefix . 'profesi-cpmk-edit', $cpmk->id) }}"
                                                        class="btn btn-warning btn-icons"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                        <i class="ti-pencil"></i>
                                                    </a>

                                                    <form
                                                        action="{{ route($currentPrefix . 'profesi-cpmk-delete', [$cpmk->id, $profesi->id]) }}"
                                                        method="POST" class="d-inline m-0 p-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            onclick="event.preventDefault(); if(confirm('Yakin hapus?')) this.closest('form').submit();"
                                                            class="btn btn-danger btn-icons"
                                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
                                                            <i class="ti-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
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
                                                class="btn btn-primary btn-icons"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Tambah">
                                                <i class="ti-plus"></i>
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
