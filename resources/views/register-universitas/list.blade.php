@extends('admin.template')
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
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">List Registrasi Universitas</h4>
                <div class="table-responsive">
                    <table class="table table-hover dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Universitas</th>
                                <th>Nama Kontak PIC</th>
                                <th>Email</th>
                                <th>Nomor Telepon</th>
                                <th>Status Aptikom</th>
                                <th>Surat Tugas</th>
                                <th>Status</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($registerUniversitass as $r)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $r->nama_universitas }}</td>
                                    <td>{{ $r->gelar_depan . ' ' . $r->nama_lengkap . ', ' . $r->gelar_belakang }}</td>
                                    <td>{{ $r->email }}</td>
                                    <td>{{ $r->nomor_telepon }}</td>
                                    <td>
                                        <x-aptikom-badge :value="(bool) $r->is_aptikom" />
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.view.pdf', encrypt($r->id)) }}" class="btn btn-info p-2"
                                            target="_blank">
                                            <i class="mdi mdi-file-pdf"></i> Preview PDF
                                        </a>
                                    </td>
                                    <td>
                                        @if ($r->status == 'pending')
                                            <button type="button" class="btn btn-warning p-2" disabled>Pending</button>
                                        @elseif($r->status == 'approved')
                                            <button type="button" class="btn btn-success p-2" disabled>Approved</button>
                                        @elseif($r->status == 'rejected')
                                            <button type="button" class="btn btn-danger p-2" disabled>Rejected</button>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.show-registrasi-universitas', encrypt($r->id)) }}"
                                            class="btn btn-primary p-2">
                                            <i class="mdi mdi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection