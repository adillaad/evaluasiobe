@extends('dosen.template')
@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="card-header py-3">
                    <h4 class="mb-0 fw-bold"></i>Daftar Pemetaan CPL ke Mata Kuliah</h4>
                </div>
                <x-filter-form
                    :universities="$universities"
                    :faculties="$faculties"
                    :programs="$programs"
                />
                <div class="table-responsive">
                    <table class="table table-hover dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Kode MK</th>
                                <th>Nama MK</th>
                                <th>Tanggal</th>
                                <th>Kode CPL</th>
                                @if (in_array(auth()->user()->otoritas->otoritas, ['Wakil Rektor', 'Wakil Dekan']))
                                    <th>Prodi</th>
                                @endif
                                @if (in_array(auth()->user()->otoritas->otoritas, ['Wakil Rektor']))
                                    <th>Fakultas</th>
                                @endif
                                @if (auth()->user()->otoritas->otoritas == 'Dosen')
                                    <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cplmks as $cplmk)
                                @php
                                    $kode_mk = 0;
                                    $kode_cpl = 0;
                                    $nama_mk = '';
                                    $nama_prodi = '';
                                    $nama_fakultas = '';

                                    // Cari MK yang sesuai
                                    foreach ($mks as $mk) {
                                        if ($cplmk->mk_kode == $mk->kode) {
                                            $kode_mk = $mk->kode;
                                            $nama_mk = $mk->nama;
                                            break;
                                        }
                                    }

                                    // Cari CPL yang sesuai
                                    foreach ($cpls as $cpl) {
                                        if ($cplmk->cpl_id == $cpl->id) {
                                            $kode_cpl = $cpl->kode;
                                            break;
                                        }
                                    }

                                    // Cari RPS yang sesuai untuk mendapatkan prodi dan fakultas
                                    foreach ($rpss as $rps) {
                                        if ($rps->kode_mk == $cplmk->mk_kode) {
                                            $nama_prodi = $rps->prodi->nama;
                                            $nama_fakultas = $rps->prodi->fakultas->nama;
                                            break;
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $kode_mk }}</td>
                                    <td>{{ $nama_mk }}</td>
                                    <td>{{ date('d-m-Y', strtotime($cplmk->created_at)) }}</td>
                                    <td>{{ $kode_cpl }}</td>
                                    @if (in_array(auth()->user()->otoritas->otoritas, ['Wakil Rektor', 'Wakil Dekan']))
                                        <td>{{ $nama_prodi }}</td>
                                    @endif
                                    @if (in_array(auth()->user()->otoritas->otoritas, ['Wakil Rektor']))
                                        <td>{{ $nama_fakultas }}</td>
                                    @endif
                                     @if (auth()->user()->otoritas->otoritas == 'Dosen')
                                         <td>
                                             <form action="{{ route('dosen.cplmk-delete', $cplmk->id) }}" method="post" class="d-inline m-0 p-0">
                                                 @csrf
                                                 @method('delete')
                                                 <button type="submit" class="btn btn-danger btn-icons"
                                                     data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                                                     onclick="return confirm('Are you sure to delete CPL {{ $kode_cpl }} from CPLMK {{ $kode_mk }} ?')">
                                                     <i class="ti-trash"></i>
                                                 </button>
                                             </form>
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
