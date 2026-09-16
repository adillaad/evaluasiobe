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
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <h4 class="card-title mb-0 me-auto">CPL Program Studi</h4>
                    @if (in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']))
                        <a href="{{ route($currentPrefix . 'cpl.create') }}" class="btn btn-primary btn-icon-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            <span>Tambah CPL Prodi</span>
                        </a>
                    @endif
                </div>
                <x-filter-form
                    :universities="$universities"
                    :faculties="$faculties"
                    :programs="$programs"
                    :kurikulums="$kurikulums"
                    :showKurikulum="true"
                />
                <style>
                    .table-cpl-prodi {
                        width: 100% !important;
                    }
                    .table-cpl-prodi th {
                        font-weight: 600 !important;
                        font-size: 13px !important;
                    }
                    .table-cpl-prodi td {
                        vertical-align: middle !important;
                        white-space: normal !important;
                        word-wrap: break-word !important;
                        word-break: break-word !important;
                    }
                    .badge-cpl-kode {
                        background-color: #eff6ff;
                        color: #1d4ed8;
                        border: 1px solid #bfdbfe;
                        font-weight: 700;
                        padding: 4px 10px;
                        border-radius: 6px;
                        font-size: 12px;
                        display: inline-block;
                    }
                </style>
                <div class="table-responsive">
                    <table class="table table-hover dataTable table-cpl-prodi align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th style="width: 120px;">Kode CPL</th>
                                <th style="min-width: 300px; max-width: 550px;">Deskripsi CPL</th>
                                @if ($userOtoritas != 'Penjamin Mutu Program Studi')
                                    <th style="width: 200px;">Prodi</th>
                                @endif
                                @if (!in_array($userOtoritas, ['Penjamin Mutu Fakultas', 'Penjamin Mutu Program Studi']))
                                    <th style="width: 200px;">Fakultas</th>
                                @endif
                                @if (in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']))
                                    <th style="width: 100px;">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cpls as $cpl)
                                <tr>
                                    <td class="fw-semibold text-secondary">{{ $loop->iteration }}</td>
                                    <td><span class="badge-cpl-kode">{{ $cpl->kode }}</span></td>
                                    <td style="line-height: 1.5; color: #334155;">{{ $cpl->judul }}</td>
                                    @if ($userOtoritas != 'Penjamin Mutu Program Studi')
                                        <td>{{ $cpl->prodi->nama }}</td>
                                    @endif
                                    @if (!in_array($userOtoritas, ['Penjamin Mutu Fakultas', 'Penjamin Mutu Program Studi']))
                                        <td>{{ $cpl->prodi->fakultas->nama }}</td>
                                    @endif
                                    @if (in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']))
                                     <td>
                                         <div class="d-flex align-items-center gap-1">
                                             <a href="{{ route($currentPrefix . 'cpl.edit', encrypt($cpl->id)) }}"
                                                 class="btn btn-warning btn-icons" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                 <i class="ti-pencil"></i>
                                             </a>
                                             <form action="{{ route($currentPrefix . 'cpl.delete', encrypt($cpl->id)) }}"
                                                 method="post" class="d-inline m-0 p-0">
                                                 @csrf
                                                 @method('delete')
                                                 <button type="submit" class="btn btn-danger btn-icons"
                                                     data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                                                     onclick="return confirm('Hapus CPL {{ $cpl->kode }}?')">
                                                     <i class="ti-trash"></i>
                                                 </button>
                                             </form>
                                         </div>
                                     </td>
                                @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/dynamic-filters.js') }}"></script>
@endsection
