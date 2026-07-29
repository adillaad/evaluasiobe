@extends('admin.template')
@section('content')
    <style>
        li.select2-selection__choice {
            color: #646464;
            font-weight: bolder;
        }
    </style>

    <div class="container-fluid mb-4">
        <div class="card">
            <div class="card-header">
                <div class="fw-bold">
                    <h3>Tambah Fakultas</h3>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    {{-- UNIVERSITAS --}}
                    <div class="form-group" id="id_universitas-form">
                        <label>Universitas<span class="text-danger">*</span></label><br>
                        @if (in_array(auth()->user()->otoritas->otoritas, ['Admin Universitas']))
                            <input type="hidden" name="id_universitas" value="{{ auth()->user()->id_universitasUser }}">
                        @endif
                        <select class="js-example-basic-single w-100"
                            name="{{ in_array(auth()->user()->otoritas->otoritas, ['Admin Universitas']) ? '' : 'id_universitas' }}"
                            {{ in_array(auth()->user()->otoritas->otoritas, ['Admin Universitas']) ? 'disabled' : '' }}>
                            <option selected="true" value="" disabled
                                {{ !old('id_universitas') && !in_array(auth()->user()->otoritas->otoritas, ['Admin Universitas']) ? 'selected' : '' }}>
                                Select...</option>
                            @foreach ($universitas as $u)
                                <option value="{{ $u->id }}"
                                    {{ old('id_universitas') == $u->id ||
                                    (in_array(auth()->user()->otoritas->otoritas, ['Admin', 'Admin Universitas']) && auth()->user()->id_universitasUser == $u->id)
                                        ? 'selected'
                                        : '' }}>
                                    {{ $u->nama }}</option>
                            @endforeach
                        </select>
                        @error('id_universitas')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label>Nama Fakultas<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="fakultas" name="fakultas" 
                            placeholder="Nama Fakultas" value="{{ old('fakultas') }}" 
                            autofocus autocomplete="off">
                        @error('fakultas')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('fakultas').addEventListener('input', function() {
            var input = this;
            var words = input.value.split(' ');
            for (var i = 0; i < words.length; i++) {
                words[i] = words[i].charAt(0).toUpperCase() + words[i].slice(1);
            }
            input.value = words.join(' ');
        });
    </script>
@endsection