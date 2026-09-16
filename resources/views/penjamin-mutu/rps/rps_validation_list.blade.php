@extends('penjamin-mutu.template')

@php
    $routePrefix = [ 
        'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.',
        'Kepala Program Studi' => 'kepala-program-studi.',
    ];
    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas] ?? 'penjamin-mutu.program-studi.';
@endphp

@section('content')
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Validasi RPS</h4>
            <p class="card-description">
                Daftar RPS yang memerlukan validasi Anda untuk dapat dipublikasikan.
            </p>
            
            <div class="table-responsive mt-4">
                <table class="table table-hover dataTable">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Mata Kuliah</th>
                            <th>Semester</th>
                            <th>Pengembang RPS</th>
                            <th>Tgl. Pengajuan</th>
                            <th>Status</th>
                            <th>Aksi Validasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rpss as $rps)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $rps->mk->kode ?? '-' }}</td>
                            <td>{{ $rps->mk->nama ?? '-' }}</td>
                            <td>{{ $rps->mk->semester ?? '-' }}</td>
                            <td>{{ $rps->pengembang ?? '-' }}</td>
                            <td>{{ $rps->submitted_at->format('d M Y') }}</td>
                            <td>
                                <span class="badge badge-warning">Pending</span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center align-items-center" style="gap: 4px;">
                                    <a href="/admin/print-rps/{{ encrypt($rps->id) }}" target="_blank" 
                                       class="btn btn-icons btn-info" 
                                       data-bs-toggle="tooltip" title="Lihat & Cetak RPS">
                                        <i class="ti-printer"></i>
                                    </a>
                                    
                                    <form action="{{ route($currentPrefix . 'rps.validation.approve', $rps->id) }}" method="post" class="d-inline m-0 p-0">
                                        @csrf
                                        <button type="submit" class="btn btn-icons btn-success" 
                                            data-bs-toggle="tooltip" title="Setujui RPS" 
                                            onclick="return confirm('Apakah Anda yakin ingin MENYETUJUI RPS ini?')">
                                            <i class="ti-check"></i>
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-icons btn-danger" 
                                        data-bs-toggle="modal" data-bs-target="#rejectRpsModal{{ $rps->id }}" 
                                        data-bs-toggle="tooltip" title="Tolak RPS">
                                        <i class="ti-close"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                Tidak ada RPS yang menunggu validasi saat ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@foreach ($rpss as $rps)
<div class="modal fade" id="rejectRpsModal{{ $rps->id }}" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"> 
        <h5 class="modal-title" id="rejectModalLabel">Tolak RPS: {{ $rps->mk->nama ?? 'Tanpa Nama' }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route($currentPrefix . 'rps.validation.reject', $rps->id) }}" method="post">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label for="catatan" class="form-label">Catatan / Alasan Penolakan <span class="text-danger">*</span></label>
              <textarea class="form-control" name="catatan" rows="4" required placeholder="Jelaskan bagian yang perlu diperbaiki oleh dosen..."></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger">Kirim Penolakan</button>
          </div>
      </form>
    </div>
  </div>
</div>
@endforeach
@endpush