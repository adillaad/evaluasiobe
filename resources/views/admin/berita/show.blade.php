@extends('guest.template')
@section('content')
<style>
    .custom-font-size,
    .custom-font-size * {
        font-size: 16px;
        line-height: 1.5;
    }
</style>
    {{-- content --}}
    <main style="margin-top: 125px;">
        <article class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="fw-bold text-center">{{ $berita->judul }}</h1>
                <div class="d-flex justify-content-center fs-5 m-5 gap-3">
                    <i class="bi bi-calendar3"></i>
                    <span>
                        {{ $berita->created_at->format('F d, Y H:i') }}
                    </span>
                </div>
                <div class="m-5">
                    <img src="{{ asset('storage/' . $berita->gambar) }}" class="img-fluid rounded" alt="{{ $berita->gambar }}">
                </div>
                <div class="m-5 custom-font-size">
                    {!! $berita->konten !!}
                </div>
                <div class="m-5">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ url()->previous() }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </article>
    </main>
@endsection
