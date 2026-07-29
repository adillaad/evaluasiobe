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
                    Edit Berita
                </p>
                @if ($berita->gambar)
                    <div class="mb-3">
                        <div style="position: relative; width: 100%; padding-bottom: 56.25%;">
                            <img src="{{ asset('storage/' . $berita->gambar) }}" alt="Current Image"
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    </div>
                @endif
                <form method="POST" action="{{ route('admin.berita.update', ['berita' => encrypt($berita->id)]) }}" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <div class="form-group">
                        <label for="judul">Judul <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="judul" placeholder="Masukkan judul berita"
                            value="{{ old('judul', $berita->judul) }}" autofocus autocomplete="off">
                        @error('judul')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="konten">Konten <span class="text-danger">*</span></label>
                        <textarea name="konten" id="konten" class="form-control" rows="10" placeholder="Masukkan konten">{{ old('konten', $berita->konten) }}</textarea>
                        @error('konten')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control">
                            <option value="draft" {{ old('status', $berita->status) == 'draft' ? 'selected' : '' }}>Draft
                            </option>
                            <option value="published" {{ old('status', $berita->status) == 'published' ? 'selected' : '' }}>
                                Published</option>
                        </select>
                        @error('status')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="gambar">Gambar </label>
                        <input type="file" accept="image/png, image/jpeg" name="gambar" class="form-control"
                            style="padding-bottom: +27px">
                        <small class="text-muted">Maksimal ukuran file: 2MB. Biarkan kosong jika tidak ingin mengubah
                            gambar.</small>
                    </div>
                    <button type="submit" class="btn btn-primary me-2">Update</button>
                    <a href="{{ route('admin.berita.index') }}" class="btn btn-light">Batal</a>
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
