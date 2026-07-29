@extends('penjamin-mutu.template')

@section('content')
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Daftar RPS</h4>
            <p class="card-description">
                Daftar RPS yang telah dipublikasikan.
            </p>

            <x-filter-form
                :universities="$universities ?? collect()"
                :faculties="$faculties ?? collect()"
                :programs="$programs ?? collect()"
                :showUniversitas="$showUniversitas ?? false"
                :showFakultas="$showFakultas ?? false"
                :showProdi="$showProdi ?? false"
            />

            <div class="table-responsive mt-4">
                <table class="table table-hover dataTable">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Mata Kuliah</th>
                            <th>Semester</th>
                            <th>Pengembang RPS</th>

                            @if(in_array($otoritas ?? auth()->user()->otoritas->otoritas, ['Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas']))
                                <th>Prodi</th>
                            @endif

                            @if(in_array($otoritas ?? auth()->user()->otoritas->otoritas, ['Penjamin Mutu Universitas']))
                                <th>Fakultas</th>
                            @endif

                            <th>Tgl. Validasi</th>
                            <th>Status</th>
                            <th class="text-center">Cetak</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($rpss as $rps)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $rps->mk->kode ?? '-' }}</td>
                            <td>{{ $rps->mk->nama ?? '-' }}</td>
                            <td>{{ $rps->semester ?? '-' }}</td>
                            <td>{{ $rps->pengembang ?? '-' }}</td>

                            @if(in_array($otoritas ?? auth()->user()->otoritas->otoritas, ['Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas']))
                                <td>{{ $rps->mk->prodi->nama ?? '-' }}</td>
                            @endif

                            @if(in_array($otoritas ?? auth()->user()->otoritas->otoritas, ['Penjamin Mutu Universitas']))
                                <td>{{ $rps->mk->prodi->fakultas->nama ?? '-' }}</td>
                            @endif

                            <td>{{ $rps->latestValidation ? $rps->latestValidation->created_at->format('d M Y') : '-' }}</td>
                            <td><span class="badge badge-success">Published</span></td>

                            <td class="text-center">
                                <a href="/admin/print-rps/{{ encrypt($rps->id) }}" target="_blank"
                                   class="btn btn-info btn-icon-text p-2" title="Lihat & Cetak RPS">
                                    <i class="ti-printer btn-icon"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center">
                                Belum ada RPS yang dipublikasikan.
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
<script src="{{ asset('assets/js/dynamic-filters.js') }}"></script>
@endpush