@extends('guest.template')

@section('content')
{{-- 1. TAMBAHKAN DIV PEMBUNGKUS DENGAN STYLE MIN-HEIGHT --}}
<div style="display: flex; flex-direction: column; min-height: calc(100vh - 100px);"> 
    
    {{-- 2. BUAT KONTEN UTAMA MENGISI SISA RUANG --}}
    <div class="container" style="padding-top: 120px; padding-bottom: 40px; flex-grow: 1;">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <div class="text-center mb-5">
                    <h1 class="display-5 fw-bold">Pencarian Rencana Pembelajaran Semester (RPS)</h1>
                    <p class="fs-5 text-muted">Temukan RPS mata kuliah berdasarkan nama atau kode mata kuliah.</p>
                </div>

                <div class="card shadow-sm mb-5">
                    <div class="card-body p-4">
                        <form action="{{ route('rps.index') }}" method="GET">
                            <div class="input-group">
                                <input type="text" class="form-control form-control-lg" id="search" name="search" placeholder="Contoh: Kecerdasan Buatan atau COM620202" value="{{ request('search') }}" aria-label="Cari Mata Kuliah">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="mdi mdi-magnify"></i> <span class="d-none d-sm-inline">Cari</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                @if(request()->filled('search') && $rpss->count() > 0)
                    <div class="alert alert-info" role="alert">
                        Menampilkan <strong>{{ $rpss->total() }}</strong> hasil untuk pencarian "<strong>{{ request('search') }}</strong>".
                    </div>
                @endif
                
                <div class="list-group">
                @forelse ($rpss as $rps)
                    <div class="list-group-item p-0 mb-3 border-0">
                        <div class="card shadow-sm rounded-4">
                            <div class="card-body p-4">
                                <div class="d-flex gap-3 align-items-start">
                
                                    {{-- KIRI: ICON --}}
                                    <div class="flex-shrink-0">
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                             style="width:50px;height:50px;">
                                            <i class="mdi mdi-book-open-page-variant text-secondary fs-4"></i>
                                        </div>
                                    </div>
                
                                    {{-- TENGAH: KONTEN --}}
                                    <div class="flex-grow-1">
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <h5 class="mb-0 fw-bold text-primary">
                                                {{ $rps->mk->nama ?? 'Nama MK Tidak Ditemukan' }}
                                            </h5>
                
                                            {{-- badge kecil kode mk --}}
                                            <span class="badge bg-light text-primary border fw-semibold">
                                                {{ $rps->mk->kode ?? 'KODE' }}
                                            </span>
                                        </div>
                
                                        {{-- META UNIV/FAK/PRODI (chips) --}}
                                        <div class="mt-2 d-flex flex-wrap gap-2">
                                            <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle">
                                                <i class="mdi mdi-domain me-1"></i>
                                                {{ $rps->mk->prodi->fakultas->universitas->nama ?? '-' }}
                                            </span>
                                            <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle">
                                                <i class="mdi mdi-domain me-1"></i>
                                                {{ $rps->mk->prodi->fakultas->nama ?? '-' }}
                                            </span>
                                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">
                                                <i class="mdi mdi-account-group-outline me-1"></i>
                                                {{ $rps->mk->prodi->nama ?? '-' }}
                                            </span>
                                        </div>
                
                                        {{-- INFO CEPAT --}}
                                        <div class="mt-2 text-muted small d-flex flex-wrap gap-3">
                                            <span><i class="mdi mdi-calendar-month-outline me-1"></i> Semester {{ $rps->semester }}</span>
                                            <span><i class="mdi mdi-book-education-outline me-1"></i>
                                                {{ ($rps->mk->bobot_teori ?? 0) + ($rps->mk->bobot_praktikum ?? 0) }} SKS
                                            </span> 
                                        </div>
                
                                        {{-- DESKRIPSI --}}
                                        <p class="mt-2 mb-0 text-muted">
                                            {{ Str::limit($rps->mk->deskripsi ?? 'Tidak ada deskripsi untuk mata kuliah ini.', 150) }}
                                        </p>
                                    </div>
                
                                    {{-- KANAN: STATUS + CTA --}}
                                    <div class="flex-shrink-0 text-end">
                                        <span class="badge bg-success rounded-pill mb-2">
                                            <i class="mdi mdi-check-circle-outline me-1"></i> Published
                                        </span>
                
                                        <div>
                                            <a href="{{ route('rps.download', encrypt($rps->id)) }}"
                                               class="btn btn-sm btn-primary d-inline-flex align-items-center">
                                                <i class="mdi mdi-download me-2"></i> Unduh
                                            </a>
                                        </div>
                                    </div>
                
                                </div>
                            </div>
                        </div>
                    </div> 
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="mdi mdi-file-search-outline display-1 text-muted"></i>
                                <h4 class="mt-3">RPS Tidak Ditemukan</h4>
                                <p class="text-muted">
                                    @if(request('search'))
                                        Tidak ada RPS yang cocok dengan kata kunci "{{ request('search') }}".
                                    @else
                                        Belum ada RPS yang dipublikasikan.
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforelse
                </div>

                @if($rpss->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    {{ $rpss->appends(request()->query())->links() }}
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection