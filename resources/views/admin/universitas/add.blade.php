@extends('admin.template')
@section('content')
    <style>
        li.select2-selection__choice {
            color: #646464;
            font-weight: bolder;
        }
        
        #logoPreview {
            max-width: 100%;
            max-height: 200px;
            object-fit: contain;
            border-radius: 4px;
            padding: 5px;
        }

        .preview-container {
            min-height: 212px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    <div class="container-fluid mb-4">
        <div class="card">
            <div class="card-header">
                <div class="fw-bold">
                    <h3>Tambah Universitas</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Form di sebelah kiri -->
                    <div class="col-md-8">
                        <form method="POST" action="{{ route('admin.store-universitas') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group mb-3">
                                <label>Nama Universitas<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama" placeholder="Nama Universitas"
                                    value="{{ old('nama') }}" autofocus autocomplete="off">
                                @error('nama')
                                    <div class="alert alert-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label>Logo Universitas <span class="text-danger">*</span></label>
                                <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo"
                                    name="logo" accept="image/*" onchange="previewLogo(this);" style="padding-bottom: +27px">
                                <small class="text-muted">Format yang didukung: JPEG, JPG, PNG. Maksimal 2MB</small>
                                @error('logo')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary me-2">Submit</button>
                        </form>
                    </div>
                    
                    <!-- Preview di sebelah kanan -->
                    <div class="col-md-4">
                        <div class="preview-container">
                            <img id="logoPreview" src="#" alt="Preview Logo" style="display: none;"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('nama').addEventListener('input', function() {
            var input = this;
            var words = input.value.split(' ');
            for (var i = 0; i < words.length; i++) {
                words[i] = words[i].charAt(0).toUpperCase() + words[i].slice(1);
            }
            input.value = words.join(' ');
        });

        function previewLogo(input) {
            var preview = document.getElementById('logoPreview');
            
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '#';
                preview.style.display = 'none';
            }
        }
    </script>
@endsection