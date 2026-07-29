@php
    $currentPrefix = auth()->user()->otoritas->otoritas
        ? str_replace(' ', '-', strtolower(auth()->user()->otoritas->otoritas)) . '.'
        : 'admin.';

    $userOtoritas = auth()->user()->otoritas->otoritas ?? '';

    $showUniversitas = false;
    $showFakultas = false;
    $showProdi = false;

    if ($userOtoritas === 'Wakil Rektor') {
        $showFakultas = true;
        $showProdi = true;
    } elseif ($userOtoritas === 'Wakil Dekan') {
        $showProdi = true;
    }
@endphp

@extends('dosen.template')

@section('content')
<style>
    .action-buttons form{
        display: inline-block;
        margin: 0;
    }
    .full-width-card{
        width: 100%;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card full-width-card">
            <div class="card-body">

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4 class="card-title mb-0">Daftar Rubrik</h4>

                    @if($userOtoritas === 'Dosen')
                        <a href="{{ route($currentPrefix . 'rubrik-add') }}" class="btn btn-primary btn-icon-text">
                            <i class="ti-plus btn-icon-prepend"></i> Tambah Rubrik
                        </a>
                    @endif
                </div>

                @if(in_array($userOtoritas, ['Wakil Rektor', 'Wakil Dekan']))
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
                                <th style="width:60px;">No</th>
                                <th>Kode MK</th>
                                <th>Nama MK</th>
                                <th>Jenis Rubrik</th>
                                <th>Dosen Pengampu</th>
                                @if($userOtoritas === 'Wakil Rektor')
                                    <th>Fakultas</th>
                                    <th>Jurusan</th>
                                @elseif($userOtoritas === 'Wakil Dekan')
                                    <th>Jurusan</th>
                                @endif
                                <th>Terakhir Diperbarui</th>
                                <th class="text-center" style="width:140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rubriks as $r)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $r->mk->kode ?? '-' }}</td>
                                    <td>{{ $r->mk->nama ?? '-' }}</td>
                                    <td>{{ $r->jenis_rubrik ?? '-' }}</td>
                                    <td>{{ $r->user->name ?? '-' }}</td>
                                    @if($userOtoritas === 'Wakil Rektor')
                                        <td>{{ $r->mk->prodi->fakultas->nama ?? '-' }}</td>
                                        <td>{{ $r->mk->prodi->nama ?? '-' }}</td>
                                    @elseif($userOtoritas === 'Wakil Dekan')
                                        <td>{{ $r->mk->prodi->nama ?? '-' }}</td>
                                    @endif
                                    <td>{{ $r->updated_at ? $r->updated_at->format('d-m-Y') : '-' }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2 action-buttons">
                                            <a href="{{ route($currentPrefix.'rubrik-download', $r->id) }}"
                                               class="btn btn-success btn-icon-text p-2"
                                               title="Download Rubrik">
                                                <i class="ti-download btn-icon"></i>
                                            </a>
                
                                            @if($userOtoritas === 'Dosen')
                                                <form action="{{ route($currentPrefix.'rubrik-delete', $r->id) }}"
                                                      method="post"
                                                      onsubmit="return confirm('Yakin ingin menghapus rubrik {{ $r->jenis_rubrik }} pada mata kuliah {{ $r->mk->kode ?? '' }}?')">
                                                    @csrf
                                                    @method('delete')
                
                                                    <button type="submit"
                                                            class="btn btn-danger btn-icon-text p-2"
                                                            title="Hapus Rubrik">
                                                        <i class="ti-trash btn-icon"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="
                                        @if($userOtoritas === 'Wakil Rektor')
                                            9
                                        @elseif($userOtoritas === 'Wakil Dekan')
                                            8
                                        @else
                                            7
                                        @endif
                                    " class="text-center text-muted py-4">
                                        Data rubrik belum tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table> 
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/dynamic-filters.js') }}"></script>
@endpush