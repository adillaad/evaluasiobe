@extends('guest.template')
@section('content')
    <div class="container-fluid" style="padding-top: 100px;">
        <div class="row justify-content-center align-items-center" style="min-height: calc(100vh - 140px);">
            <div class="p-5">
                <p class="text-center fs-2 fw-bold pb-5">Informasi Terkini</p>
                <div class="row g-4 justify-content-center">
                    @forelse ($beritas as $berita)
                        <div class="col-md-4">
                            <a href="{{ route('berita.show', $berita->slug) }}" class="text-decoration-none">
                                <div class="card h-100 shadow-sm">
                                    <div style="height: 150px; overflow: hidden; position: relative;">
                                        <img src="{{ asset('storage/' . $berita->gambar) }}"
                                            class="card-img-top rounded-top" alt="{{ $berita->gambar }}"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">{{ $berita->created_at->format('F d, Y H:i') }}</p>
                                        <p class="card-title mt-2 mb-0">{{ $berita->judul }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <p class="text-center mt-5 fs-4">Tidak ada berita tersedia.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
