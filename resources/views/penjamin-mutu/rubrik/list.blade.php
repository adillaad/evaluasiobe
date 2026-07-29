@php
    $userOtoritas = auth()->user()->otoritas->otoritas ?? '';

    if ($userOtoritas === 'Penjamin Mutu Universitas') {
        $currentPrefix = 'penjamin-mutu.universitas.';
    } elseif ($userOtoritas === 'Penjamin Mutu Fakultas') {
        $currentPrefix = 'penjamin-mutu.fakultas.';
    } elseif ($userOtoritas === 'Penjamin Mutu Program Studi') {
        $currentPrefix = 'penjamin-mutu.program-studi.';
    } elseif ($userOtoritas === 'Kepala Program Studi') {
        $currentPrefix = 'kepala-program-studi.';
    } else {
        $currentPrefix = 'admin.';
    }

    $showUniversitas = false;
    $showFakultas = false;
    $showProdi = false;

    if ($userOtoritas === 'Penjamin Mutu Universitas') {
        $showFakultas = true;
        $showProdi = true;
    } elseif ($userOtoritas === 'Penjamin Mutu Fakultas') {
        $showProdi = true;
    }
@endphp

@extends('penjamin-mutu.template')

@section('content')
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h4 class="card-title mb-1">Daftar Rubrik</h4> 
                </div>
            </div>

            @if(in_array($userOtoritas, ['Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas']))
                <div class="mb-4">
                    <x-filter-form
                        :universities="$universities ?? collect()"
                        :faculties="$faculties ?? collect()"
                        :programs="$programs ?? collect()"
                        :showUniversitas="$showUniversitas"
                        :showFakultas="$showFakultas"
                        :showProdi="$showProdi"
                    />
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover dataTable">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Kode MK</th>
                            <th>Mata Kuliah</th>
                            <th>Jenis Rubrik</th>
                            <th>Dosen Pengampu</th>

                            @if(in_array($userOtoritas, ['Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas']))
                                <th>Prodi</th>
                            @endif

                            @if($userOtoritas === 'Penjamin Mutu Universitas')
                                <th>Fakultas</th>
                            @endif

                            <th>Terakhir Diperbarui</th>
                            <th class="text-center">Unduh</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($rubriks as $rubrik)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $rubrik->mk->kode ?? '-' }}</td>
                                <td>{{ $rubrik->mk->nama ?? '-' }}</td>
                                <td>{{ $rubrik->jenis_rubrik ?? '-' }}</td>
                                <td>{{ $rubrik->user->name ?? '-' }}</td>

                                @if(in_array($userOtoritas, ['Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas']))
                                    <td>{{ $rubrik->mk->prodi->nama ?? '-' }}</td>
                                @endif

                                @if($userOtoritas === 'Penjamin Mutu Universitas')
                                    <td>{{ $rubrik->mk->prodi->fakultas->nama ?? '-' }}</td>
                                @endif

                                <td>{{ $rubrik->updated_at ? $rubrik->updated_at->format('d M Y') : '-' }}</td>

                                <td class="text-center">
                                    <a href="{{ route($currentPrefix . 'rubrik-download', $rubrik->id) }}"
                                       class="btn btn-success btn-icon-text p-2"
                                       title="Unduh Rubrik">
                                        <i class="ti-download btn-icon"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $userOtoritas === 'Penjamin Mutu Universitas' ? 8 : (in_array($userOtoritas, ['Penjamin Mutu Fakultas']) ? 7 : 6) }}"
                                    class="text-center text-muted py-4">
                                    Belum ada data rubrik.
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