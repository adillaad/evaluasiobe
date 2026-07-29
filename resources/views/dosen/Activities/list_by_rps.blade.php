@php
    $currentPrefix = auth()->user()->otoritas->otoritas
        ? str_replace(' ', '-', strtolower(auth()->user()->otoritas->otoritas)) . '.'
        : 'dosen.';
    $isDosen = auth()->user()->otoritas->otoritas == 'Dosen';
@endphp

@extends('dosen.template')

@section('content')


<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="card-title mb-1">Detail Rencana Pembelajaran Semester</h4>
                    <p class="card-description mb-0">Mata Kuliah: <strong>{{ $rps->mk?->nama ?? $rps->kode_mk ?? '-' }}</strong></p>
                </div>
                <a href="{{ route($currentPrefix . 'rps-list') }}" class="btn btn-light">
                    <i class="ti-arrow-left me-2"></i>Kembali
                </a>
            </div>
            <hr>

            <div class="row mt-4">
                <div class="col-md-6">
                    <h5 class="mb-3">Informasi Umum</h5>
                    <dl class="row">
                        <dt class="col-sm-4">Kode MK</dt>
                        <dd class="col-sm-8">: {{ $rps->kode_mk }}</dd>

                        <dt class="col-sm-4">Program Studi</dt>
                        <dd class="col-sm-8">: {{ $rps->prodi->nama }}</dd>

                        <dt class="col-sm-4">Semester</dt>
                        <dd class="col-sm-8">: {{ $rps->semester }}</dd>

                        <dt class="col-sm-4">Bobot SKS</dt>
                        <dd class="col-sm-8">: @if($rps->mk){{ $rps->mk->bobot_teori + $rps->mk->bobot_praktikum }} SKS (T:{{ $rps->mk->bobot_teori }} P:{{ $rps->mk->bobot_praktikum }})@else - @endif</dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <h5 class="mb-3">Penanggung Jawab</h5>
                    <dl class="row">
                        <dt class="col-sm-4">Pengembang RPS</dt>
                        <dd class="col-sm-8">: {{ $rps->pengembang ?? '-' }}</dd>

                        <dt class="col-sm-4">Koordinator RMK</dt>
                        <dd class="col-sm-8">: {{ $rps->koordinator ?? '-' }}</dd>

                        <dt class="col-sm-4">Ketua PRODI</dt>
                        <dd class="col-sm-8">: {{ $rps->kaprodi ?? '-' }}</dd>

                        <dt class="col-sm-4">Dosen Pengampu</dt>
                        @php
                            $pengampu = array_filter([$rps->dosen, $rps->dosen_anggota1, $rps->dosen_anggota2]);
                        @endphp
                        @forelse ($pengampu as $dosen)
                            @if ($loop->first)
                                <dd class="col-sm-8">: {{ $loop->iteration }}. {{ $dosen }}</dd>
                            @else
                                <dd class="col-sm-8 offset-sm-4">: {{ $loop->iteration }}. {{ $dosen }}</dd>
                            @endif
                        @empty
                            <dd class="col-sm-8">: -</dd>
                        @endforelse
                    </dl>
                </div>
            </div>

            @if ($isDosen && isset($validationRps) && !$validationRps['is_complete'])
                <div class="alert alert-warning mt-4" role="alert">
                    <h6 class="mb-2">
                        <i class="ti-alert me-2"></i>
                        Aktivitas mingguan belum lengkap
                    </h6>

                    <p class="mb-2">
                        Mohon lengkapi aktivitas mingguan terlebih dahulu.
                    </p>

                    <ul class="mb-0 ps-3">
                        @if ($validationRps['missing_cpmk']->isNotEmpty())
                            <li>
                                CPMK yang belum dimasukkan:
                                <strong>{{ $validationRps['missing_cpmk']->pluck('kode')->implode(', ') }}</strong>
                            </li>
                        @endif

                        @if ($validationRps['missing_asesmen']->isNotEmpty())
                            <li>
                                Bentuk asesmen yang belum dimasukkan:
                                <strong>{{ $validationRps['missing_asesmen']->implode(', ') }}</strong>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif
 
            <div class="d-flex justify-content-between align-items-center mt-4">
                 <h4 class="card-title">Daftar Aktivitas Mingguan</h4>
                 @if ($isDosen)
                    <button type="button" class="btn btn-primary btn-icon-text mb-3" data-bs-toggle="modal" data-bs-target="#addActivityModal">
                        <i class="ti-plus btn-icon-prepend"></i>
                        Tambah Aktivitas
                    </button>
                @endif
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered" style="overflow-wrap: break-word;">
                    <thead class="bg-light">
                        <tr class="text-center">
                            <th style="min-width: 80px;">Minggu Ke-</th>
                            <th style="min-width: 100px;">ID CPMK</th>
                            <th style="min-width: 220px;">Deskripsi Sub CPMK</th>
                            <th style="min-width: 220px;">Indikator</th>
                            <th style="min-width: 200px;">Materi</th>
                            <th style="min-width: 130px;">Asesmen</th>
                            <th style="min-width: 200px;">Metode</th>
                            <th style="min-width: 200px;">Kegiatan</th>
                            @if ($isDosen)
                                <th style="min-width: 100px;">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rps->activities->sortBy('minggu') as $activity)
                            <tr class="align-top">
                                {{-- Minggu Ke- --}}
                                <td class="text-center">{{ $activity->minggu }}</td>
                                {{-- ID CPMK --}}
                                <td class="text-center">
                                    @foreach($activity->cpmk_details ?? [] as $cpmk)
                                        <span class="badge bg-primary mb-1">{{ $cpmk['kode'] }}</span>
                                    @endforeach
                                </td>
                                <td style="font-size: 0.875rem; white-space: normal; word-break: break-word;">
                                    <ol type="a" style="padding-left: 1.2rem; margin-bottom: 0;">
                                        @foreach(($activity->sub_cpmk ?? []) as $sub_id)
                                            @if((int)$sub_id === 0)
                                                {{-- fallback pakai CPMK (judul) --}}
                                                @foreach(($activity->id_cpmk ?? []) as $cpmkId)
                                                    <li>
                                                        {{ optional($cpmks->firstWhere('id', (int)$cpmkId))->judul
                                                            ?? 'CPMK ID '.$cpmkId.' tdk ditemukan' }}
                                                    </li>
                                                @endforeach
                                            @else
                                                <li>
                                                    {{ optional($sub_cpmks->firstWhere('id', (int)$sub_id))->uraian
                                                        ?? 'Sub-CPMK ID '.$sub_id.' tdk ditemukan' }}
                                                </li>
                                            @endif
                                
                                        @endforeach
                                    </ol>
                                </td>

                                {{-- Indikator --}}
                                <td style="font-size: 0.875rem; white-space: normal; word-break: break-word;">
                                    <ol type="a" style="padding-left: 1.2rem; margin-bottom: 0;">
                                        @foreach((array) $activity->indikator as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ol>
                                </td>

                                {{-- Materi --}}
                                <td style="font-size: 0.875rem; white-space: normal; word-break: break-word;">
                                    <ul style="padding-left: 1.2rem; margin-bottom: 0;">
                                        @foreach((array) $activity->materi as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                </td>

                                {{-- Asesmen --}} 
                                <td style="font-size: 0.875rem;">
                                    @php
                                        $asesmenCast = $activity->bentuk_asesmen;
                                        $asesmenRaw  = $activity->getRawOriginal('bentuk_asesmen');

                                        if (is_array($asesmenCast) && count($asesmenCast) > 0) {
                                            $asesmenList = array_values(array_filter($asesmenCast));
                                        } elseif (!empty($asesmenRaw)) {
                                            $decoded = json_decode($asesmenRaw, true);
                                            if (is_array($decoded)) {
                                                $asesmenList = array_values(array_filter($decoded));
                                            } else {
                                                $asesmenList = [$asesmenRaw];
                                            }
                                        } else {
                                            $asesmenList = [];
                                        }
                                    @endphp

                                    @if(count($asesmenList) > 0)
                                        <ul style="padding-left: 1rem; margin-bottom:0;">
                                            @foreach($asesmenList as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- Metode --}}
                                <td style="font-size: 0.875rem; white-space: normal; word-break: break-word;">
                                    {{-- HR di atas asesmen dihapus --}}
                                    @foreach((array) ($activity->metode['detail_metode'] ?? []) as $met)
                                        <div class="mb-2">
                                            {{ $met['deskripsi'] }}
                                            <br>
                                            <small>[{{ $met['kategori'] }}: {{ $met['waktu'] }}]</small>
                                        </div>
                                    @endforeach
                                    
                                    @if(!empty($activity->metode['pustaka']))
                                        <hr class="my-2">
                                        <strong>Pustaka:</strong>
                                        <br>
                                        @foreach((array) ($activity->metode['pustaka'] ?? []) as $pus)
                                            {{ $pus }}<br>
                                        @endforeach
                                    @endif
                                </td>

                                {{-- Kegiatan --}}
                                <td style="font-size: 0.875rem; white-space: normal; word-break: break-word;">
                                    <strong>Luring:</strong>
                                    @if(empty(array_filter((array) $activity->kegiatan_luring)))
                                        <p class="mb-0 ms-2">--</p>
                                    @else
                                        <ol style="padding-left: 1.2rem; margin-bottom: 0;">
                                            @foreach(array_filter((array) $activity->kegiatan_luring) as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ol>
                                    @endif
                                    <hr class="my-2">
                                    <strong>Daring:</strong>
                                    @if(empty(array_filter((array) $activity->kegiatan_daring)))
                                        <p class="mb-0 ms-2">--</p>
                                    @else
                                        <ol style="padding-left: 1.2rem; margin-bottom: 0;">
                                            @foreach(array_filter((array) $activity->kegiatan_daring) as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ol>
                                    @endif
                                </td>
                                
                                {{-- Action --}}
                                @if ($isDosen)
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center" style="gap: 5px;">
                                            <button type="button" class="btn btn-warning btn-sm p-2" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editActivityModal{{ $activity->id }}" 
                                                    title="Edit">
                                                <i class="ti-pencil"></i>
                                            </button>
                                            <form action="{{ route($currentPrefix . 'activity-delete', $activity->id) }}" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?')">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="btn btn-danger btn-sm p-2" title="Hapus">
                                                    <i class="ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isDosen ? '9' : '8' }}" class="text-center py-4">
                                    <p class="mb-2">Belum ada Aktivitas untuk RPS ini.</p>
                                    @if ($isDosen)
                                        <small>Silakan klik tombol "Tambah Aktivitas" untuk memulai.</small>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div> 
    </div>
</div>

{{-- MODAL UNTUK TAMBAH & EDIT AKTIVITAS --}}
@if ($isDosen)
    {{-- Modal Tambah Aktivitas --}}
    <div class="modal fade" id="addActivityModal" tabindex="-1" aria-labelledby="addActivityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addActivityModalLabel">Tambah Aktivitas Mingguan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('dosen.Activities.partials.form_add_modal')
                </div>
            </div>
        </div>
    </div>

    {{-- Loop untuk membuat Modal Edit untuk setiap Aktivitas --}}
    @foreach($rps->activities as $activity)
    <div class="modal fade" id="editActivityModal{{ $activity->id }}" tabindex="-1" aria-labelledby="editActivityModalLabel{{ $activity->id }}" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editActivityModalLabel{{ $activity->id }}">Edit Aktivitas Minggu ke-{{ $activity->minggu }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('dosen.Activities.partials.form_edit_modal', ['activity' => $activity])
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endif
@endsection