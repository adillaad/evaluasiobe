{{-- @php
    $routePrefix = [
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp --}}
@extends('admin.template')
@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Mata Kuliah</h4>
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
                                <th>Kode MK</th>
                                <th>Nama MK</th>
                                <th>Semester</th>
                                <th>Rumpun</th>
                                <th>Tahun kurikulum</th>
                                <th>Bobot</th>
                                <th>Prodi</th>
                                <th>Fakultas</th>
                                @if ($userOtoritas != 'Admin Universitas')
                                    <th>Universitas</th>
                                @endif
                                @if ($userOtoritas == 'Admin Universitas')
                                <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mks as $mk)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $mk->kode }}</td>
                                    <td>{{ $mk->nama }}</td>
                                    <td>{{ $mk->semester }}</td>
                                    <td>MK {{ $mk->rumpun }}</td>
                                    <td>{{ $mk->kurikulum->tahun }}</td>
                                    <td>{{ $mk->total_sks }} SKS</td>
                                    <td>{{ $mk->prodi->nama }}</td>
                                    <td>{{ $mk->prodi->fakultas->nama }}</td>
                                    @if ($userOtoritas != 'Admin Universitas')
                                        <td>{{ $mk->prodi->fakultas->universitas->nama }}</td>
                                    @endif
                                     @if ($userOtoritas == 'Admin Universitas')
                                     <td>
                                         <div class="d-flex align-items-center gap-1">
                                             <a href="edit-mk/{{ $mk->kode }}"
                                                 class="btn btn-warning btn-icons" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                 <i class="ti-pencil"></i>
                                             </a>
                                             <form action="delete-mk/{{ $mk->kode }}" method="post" class="d-inline m-0 p-0">
                                                 @csrf
                                                 @method('delete')
                                                 <button type="submit" class="btn btn-danger btn-icons"
                                                     data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                                                     onclick="return confirm('Are you sure to delete {{ $mk->nama }}?')">
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
