@extends('admin.template')
@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Detail Registrasi Universitas</h4>
                <hr>
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('failed'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('failed') }}
                    </div>
                @endif
                @if (session('warning'))
                    <div class="alert alert-warning" role="alert">
                        {{ session('warning') }}
                    </div>
                @endif
                <div class="row">
                    <!-- Personal Information -->
                    <div class="col-md-6 mb-4">
                        <div class="h-100">
                            <h5 class="card-title mb-3">Data PIC Prodi</h5>
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th class="p-0">Nama Lengkap</th>
                                        <td>: {{ $register->gelar_depan }} {{ $register->nama_lengkap }},
                                            {{ $register->gelar_belakang }}</td>
                                    </tr>
                                    <tr>
                                        <th class="p-0">Jenis Kelamin</th>
                                        <td>: {{ $register->jenis_kelamin }}</td>
                                    </tr>
                                    <tr>
                                        <th class="p-0">Nomor Telepon</th>
                                        <td>: {{ $register->nomor_telepon }}</td>
                                    </tr>
                                    <tr>
                                        <th class="p-0">Email</th>
                                        <td>: {{ $register->email }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- University Information -->
                    <div class="col-md-6 mb-4">
                        <div class="h-100 border-0">
                            <h5 class="card-title mb-0">Informasi Universitas</h5>
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th class="px-0">Universitas</th>
                                        <td class="pe-1">:</td>
                                        <td class="px-0">{{ $register->nama_universitas }}</td>
                                    </tr>
                                    <tr>
                                        <th class="px-0">Fakultas</th>
                                        <td class="pe-1">:</td>
                                        <td class="px-0">{{ $register->nama_fakultas }}</td>
                                    </tr>
                                    <tr>
                                        <th class="px-0">Program Studi</th>
                                        <td class="pe-1">:</td>
                                        <td class="px-0">{{ $register->nama_prodi }}</td>
                                    </tr>
                                    <tr>
                                        <th class="px-0">Status Aptikom</th>
                                        <td class="pe-1">:</td>
                                        <td class="px-0">
                                            <x-aptikom-badge :value="(bool) $register->is_aptikom" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="px-0">Posisi</th>
                                        <td class="pe-1">:</td>
                                        <td class="px-0">{{ $register->posisi }}</td>
                                    </tr>
                                    <tr>
                                        <th class="px-0">Nomor Telepon Universitas</th>
                                        <td class="pe-1">:</td>
                                        <td class="px-0">{{ $register->nomor_telepon_universitas }}</td>
                                    </tr>
                                    <tr>
                                        <th class="px-0">Alamat Kontak Universitas</th>
                                        <td class="pe-1">:</td>
                                        <td class="px-0 text-wrap">{{ $register->alamat_kontak_universitas }}</td>
                                    </tr>
                                    <tr>
                                        <th class="px-0">Website</th>
                                        <td class="pe-1">:</td>
                                        <td class="px-0">{{ $register->website }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Document Section -->
                    <div class="col-12">
                        <div class="border-0">
                            <h5 class="card-title mb-0">Dokumen</h5>
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <strong>Surat Tugas:</strong>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf text-danger me-2 fs-4"></i>
                                        <span>{{ basename($register->surat_tugas) }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('admin.view.pdf', encrypt($register->id)) }}" class="btn btn-info p-2"
                                        target="_blank">
                                        <i class="mdi mdi-file-pdf"></i> Preview PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4 text-end">
                    <a href="{{ route('admin.list-registrasi-universitas') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    @if ($register->status == 'pending')
                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                            data-bs-target="#approveModal">
                            <i class="mdi mdi-check"></i> Approve
                        </button>

                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="mdi mdi-close"></i> Reject
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Approve -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">Konfirmasi Persetujuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.register-universitas.approve', encrypt($register->id)) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menyetujui pendaftaran universitas ini?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Ya, Setujui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Reject -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Tolak Pendaftaran Universitas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.register-universitas.reject', encrypt($register->id)) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reason" class="form-label">Alasan Penolakan</label>
                            <textarea name="reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
