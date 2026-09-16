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
                        <div class="mb-3">
                            <a href="{{ route($currentPrefix . 'rubrik-add') }}" class="btn btn-primary btn-icon-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                <span>Tambah Rubrik</span>
                            </a>
                        </div>
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
                                        <div class="d-flex justify-content-center align-items-center" style="gap: 4px;">
                                            <a href="{{ route($currentPrefix.'rubrik-download', $r->id) }}"
                                               class="btn btn-icons btn-success"
                                               data-bs-toggle="tooltip" title="Download Rubrik">
                                                <i class="ti-download"></i>
                                            </a>
                
                                            @if($userOtoritas === 'Dosen')
                                                <form action="{{ route($currentPrefix.'rubrik-delete', $r->id) }}"
                                                      method="post"
                                                      class="d-inline m-0 p-0"
                                                      onsubmit="return confirm('Yakin ingin menghapus rubrik {{ $r->jenis_rubrik }} pada mata kuliah {{ $r->mk->kode ?? '' }}?')">
                                                    @csrf
                                                    @method('delete')
                
                                                    <button type="submit"
                                                            class="btn btn-icons btn-danger"
                                                            data-bs-toggle="tooltip" title="Hapus Rubrik">
                                                        <i class="ti-trash"></i>
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