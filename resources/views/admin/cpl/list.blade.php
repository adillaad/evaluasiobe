@extends('admin.template')
@section('content')
    <h3 class="px-4 pb-4 fw-bold text-center">Daftar CPL Program Studi</h3>
    <div class="stretch-card">
        <div class="card">
            <div class="card-body">
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
                                <th>Tahun Kurikulum</th>
                                <th>Aspek</th>
                                <th>Kode</th>
                                <th>Judul</th>
                                <th>Prodi</th>
                                <th>Fakultas</th>
                                @if (auth()->user()->otoritas->otoritas != 'Admin Universitas')
                                    <th>Universitas</th>
                                @endif
                                @if (auth()->user()->otoritas->otoritas != 'Admin')
                                    <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Loop langsung ke variabel $cpls --}}
                            @foreach ($cpls as $cpl)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $cpl->tahun }}</td>
                                    <td>{{ $cpl->aspek }}</td>
                                    <td>{{ $cpl->kode }}</td>
                                    <td>
                                        <div class="text-wrap lh-base" style="width: 300px">
                                            {{ $cpl->judul }}
                                        </div>
                                    </td>
                                    <td>{{ $cpl->prodi->nama }}</td>
                                    <td>{{ $cpl->prodi->fakultas->nama }}</td>
                                    @if (auth()->user()->otoritas->otoritas != 'Admin Universitas')
                                        <td>{{ $cpl->prodi->fakultas->universitas->nama }}</td>
                                    @endif
                                     @if (auth()->user()->otoritas->otoritas != 'Admin')
                                         <td>
                                             <div class="d-flex align-items-center gap-1">
                                                 <a href="{{ route('admin-universitas.edit-cpl', encrypt($cpl->id)) }}"
                                                     class="btn btn-warning btn-icons" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                     <i class="ti-pencil"></i>
                                                 </a>
                                                 <form action="{{ route('admin-universitas.delete-cpl', encrypt($cpl->id)) }}"
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
