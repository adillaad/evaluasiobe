{{-- @php
    $routePrefix = [
        'Admin Universitas' => ['prefix' => 'admin-universitas.'],
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'admin.';
@endphp --}}
@extends(in_array($userOtoritas, ['Admin Universitas', 'Admin']) ? 'admin.template' : 'penjamin-mutu.template')
@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title mb-0">Daftar CPMK</h4>
                <x-filter-form 
                    :universities="$universities" 
                    :faculties="$faculties" 
                    :programs="$programs" 
                    :kurikulums="$kurikulums" 
                    :show-kurikulum="true" 
                    :show-cpl="true"
                    :cpls-filter="$cplsFilter ?? []"
                />
                <style>
                    .table-cpmk-list {
                        width: 100% !important;
                    }
                    .table-cpmk-list th {
                        font-weight: 600 !important;
                        font-size: 13px !important;
                    }
                    .table-cpmk-list td {
                        vertical-align: middle !important;
                        white-space: normal !important;
                        word-wrap: break-word !important;
                        word-break: break-word !important;
                    }
                    .badge-cpl-code {
                        background-color: #eff6ff;
                        color: #1d4ed8;
                        border: 1px solid #bfdbfe;
                        font-weight: 700;
                        padding: 4px 10px;
                        border-radius: 6px;
                        font-size: 12px;
                        display: inline-block;
                    }
                    .badge-cpmk-code {
                        background-color: #f0f9ff;
                        color: #0284c7;
                        border: 1px solid #bae6fd;
                        font-weight: 700;
                        padding: 4px 10px;
                        border-radius: 6px;
                        font-size: 12px;
                        display: inline-block;
                    }
                    .badge-tahun-kuri {
                        background-color: #f8fafc;
                        color: #475569;
                        border: 1px solid #e2e8f0;
                        font-weight: 600;
                        padding: 4px 10px;
                        border-radius: 6px;
                        font-size: 12px;
                        display: inline-block;
                    }
                </style>
                <div class="table-responsive">
                    <table class="table table-hover dataTable table-cpmk-list align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th style="width: 110px;">Kode CPL</th>
                                <th style="width: 120px;">Kode CPMK</th>
                                <th style="min-width: 320px; max-width: 550px;">Rincian CPMK</th>
                                <th style="width: 140px;" class="text-center">Tahun Kurikulum</th>
                                @if (in_array($userOtoritas, [
                                        'Admin',
                                        'Admin Universitas',
                                        'Penjamin Mutu Universitas',
                                        'Penjamin Mutu Fakultas',
                                    ]))
                                    <th style="width: 180px;">Prodi</th>
                                @endif
                                @if (in_array($userOtoritas, ['Admin', 'Admin Universitas', 'Penjamin Mutu Universitas']))
                                    <th style="width: 180px;">Fakultas</th>
                                @endif
                                @if (in_array($userOtoritas, ['Admin']))
                                    <th style="width: 180px;">Universitas</th>
                                @endif
                                @if (in_array($userOtoritas, [
                                        'Admin Universitas',
                                        'Kepala Program Studi',
                                        'Penjamin Mutu Program Studi',
                                    ]))
                                    <th style="width: 140px;" class="text-center">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cpmks as $cpmk)
                                <tr>
                                    <td class="text-center fw-semibold text-secondary">{{ $loop->iteration }}</td>
                                    <td><span class="badge-cpl-code">{{ $cpmk->cpl?->kode }}</span></td>
                                    <td><span class="badge-cpmk-code">{{ $cpmk->kode }}</span></td>
                                    <td style="line-height: 1.5; color: #334155;">
                                        {{ $cpmk->judul }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-tahun-kuri">{{ $cpmk->cpl->kurikulum->tahun }}</span>
                                    </td>
                                    @if (in_array($userOtoritas, [
                                            'Admin',
                                            'Admin Universitas',
                                            'Penjamin Mutu Universitas',
                                            'Penjamin Mutu Fakultas',
                                        ]))
                                        <td>{{ $cpmk->prodi->nama }}</td>
                                    @endif
                                    @if (in_array($userOtoritas, ['Admin', 'Admin Universitas', 'Penjamin Mutu Universitas']))
                                        <td>{{ $cpmk->prodi->fakultas->nama }}</td>
                                    @endif
                                    @if (in_array($userOtoritas, ['Admin']))
                                        <td>{{ $cpmk->prodi->fakultas->universitas->nama }}</td>
                                    @endif
                                    @if (in_array($userOtoritas, [
                                            'Admin Universitas',
                                            'Kepala Program Studi',
                                            'Penjamin Mutu Program Studi',
                                        ]))
                                         <td class="text-center">
                                             <div class="d-flex justify-content-center align-items-center gap-1">
                                                 <a href="{{ route($currentPrefix . 'edit-cpmk', encrypt($cpmk->id)) }}"
                                                     class="btn btn-warning btn-icons" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                     <i class="ti-pencil"></i>
                                                 </a>
                                                 <form action="{{ route($currentPrefix . 'delete-cpmk', encrypt($cpmk->id)) }}"
                                                     method="post" class="d-inline m-0 p-0">
                                                     @csrf
                                                     @method('delete')
                                                     <button type="submit" class="btn btn-danger btn-icons"
                                                         data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                                                         onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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
