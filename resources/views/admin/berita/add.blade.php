@extends('admin.template')
@section('content')
    @if (session()->has('failed'))
        <div class="alert alert-danger" role="alert" id="box">
            <div>{{ session('failed') }}</div>
        </div>
    @elseif (session()->has('success'))
        <div class="alert greenAdd" role="alert" id="box">
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <p class="card-description">
                    Add Berita
                </p>
                <form method="POST" action="{{ route('admin.berita.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="judul">Judul <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="judul" placeholder="Masukkan judul berita"
                            value="{{ old('judul') }}" autofocus autocomplete="off">
                        @error('judul')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="konten">Konten <span class="text-danger">*</span></label>
                        <textarea name="konten" id="konten" class="form-control" rows="10"
                            style="resize: vertical; min-height: 100px; max-height: 500px;" placeholder="Masukkan konten">{{ old('konten') }}</textarea>
                        </textarea>
                        @error('konten')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="gambar">Gambar <span class="text-danger">*</span></label></label>
                        <input type="file" accept="image/png, image/jpeg" name="gambar" class="form-control"
                            style="padding-bottom: +27px">
                        <small class="text-muted">Maksimal ukuran file: 2MB</small>
                        @error('gambar')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <button class="btn btn-primary me-2">{{ __('Submit') }}</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.tiny.cloud/1/kwzqw4jpycdrl77quqeorhz4zb0cugsn3aelc28261lsg2d3/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: 'textarea#konten',
            plugins: 'code table lists',
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table'
        });
    </script>
@endsection
