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
                    :showKurikulum="true" 
                />
                <div class="table-responsive">
                    <table class="table table-hover dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Kode CPL</th>
                                <th>Kode CPMK</th>
                                <th>Rincian CPMK</th>
                                <th>Tahun Kurikulum</th>
                                @if (in_array($userOtoritas, [
                                        'Admin',
                                        'Admin Universitas',
                                        'Penjamin Mutu Universitas',
                                        'Penjamin Mutu Fakultas',
                                    ]))
                                    <th>Prodi</th>
                                @endif
                                @if (in_array($userOtoritas, ['Admin', 'Admin Universitas', 'Penjamin Mutu Universitas']))
                                    <th>Fakultas</th>
                                @endif
                                @if (in_array($userOtoritas, ['Admin']))
                                    <th>Universitas</th>
                                @endif
                                @if (in_array($userOtoritas, [
                                        'Admin Universitas',
                                        'Kepala Program Studi',
                                        'Penjamin Mutu Program Studi',
                                    ]))
                                    <th class="text-center">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cpmks as $cpmk)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td> {{ $cpmk->cpl?->kode }}</td>
                                    <td> {{ $cpmk->kode }}</td>
                                    <td>
                                        <div class="text-wrap lh-base" style="width: 300px">
                                            {{ $cpmk->judul }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-wrap lh-base" style="width: 300px">
                                            {{ $cpmk->cpl->kurikulum->tahun }}
                                        </div>
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
                                        <td>
                                            <div class="d-flex gap-2">
                                                <div>
                                                    <a href="{{ route($currentPrefix . 'edit-cpmk', encrypt($cpmk->id)) }}"
                                                        class="btn btn-warning p-2">
                                                        <i class="ti-pencil me-1"></i>
                                                        Edit
                                                    </a>
                                                </div>
                                                <form
                                                    action="{{ route($currentPrefix . 'delete-cpmk', encrypt($cpmk->id)) }}"
                                                    method="post">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-danger p-2"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                        <i class="ti-trash me-1"></i>
                                                        Delete
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
