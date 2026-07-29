@extends('admin.template')
@section('content')
    <div id="news" class="py-5">
        <div class="container">
            <div class="row g-4">
                @forelse ($beritas as $berita)
                    <div class="col-md-4 d-flex justify-content-center align-items-stretch">
                        <div class="card m-2" style="width: 100%;">
                            <div style="height: 150px; overflow: hidden; position: relative;">
                                <img src="{{ asset('storage/' . $berita->gambar) }}" class="card-img-top rounded-top"
                                    alt="{{ $berita->gambar }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="card-body" style="max-height: 150px;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="mb-0">{{ $berita->created_at->format('F d, Y H:i') }}</h5>
                                    <span class="badge {{ $berita->status == 'published' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($berita->status) }}
                                    </span>
                                </div>
                                <h5 class="card-title">{{ Str::limit($berita->judul, 30, '...') }}</h5>
                                <div class="d-flex">
                                    <a type="button" href="{{ route('admin.berita.edit', ['berita' => encrypt($berita->id)]) }}"
                                        class="btn btn-inverse-dark btn-icon-text p-2 flex-fill me-2">
                                        Edit
                                        <i class="ti-pencil btn-icon-append"></i>
                                    </a>
                                    <form action="{{ route('admin.berita.destroy', $berita) }}" method="post" class="flex-fill">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-danger btn-icon-text p-2 w-100"
                                            onclick="return confirm('Are you sure to delete {{ $berita->judul }}?')">
                                            Delete
                                            <i class="ti-trash btn-icon-append"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center mt-5 fs-4">Tidak ada berita tersedia.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
