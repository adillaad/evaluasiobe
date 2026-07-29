@extends('admin.template')
@section('content')
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Kustomisasi Tampilan</h4>
                <form action="{{ route('admin-universitas.theme.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <h5>Pengaturan Warna Tema</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="theme_color">Warna Tema</label>
                                    <input type="color" class="form-control @error('theme_color') is-invalid @enderror"
                                        id="theme_color" name="theme_color"
                                        value="{{ old('theme_color', $theme->theme_color ?? '#FFFFFF') }}">
                                    @error('theme_color')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>Logo Universitas</h5>
                        <div class="row">
                            <div class="col-md-6">
                                @if (empty($universitas->img))
                                    <img src="{{ asset('assets/img/logo_unila.png') }}" alt="Logo Universitas"
                                        class="img-thumbnail mb-2" style="max-height: 100px">
                                @else
                                    <img src="{{ asset($universitas->img) }}" alt="Logo Universitas"
                                        class="img-thumbnail mb-2" style="max-height: 100px">
                                @endif
                                <div class="form-group">
                                    <label for="logo">Upload Logo Baru</label>
                                    <input type="file" style="padding-bottom: +27px"
                                        class="form-control @error('logo') is-invalid @enderror" id="logo"
                                        name="logo" accept="image/*">
                                    <small class="text-muted">Format yang didukung: JPG, PNG, GIF. Maksimal 2MB</small>
                                    @error('logo')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>Preview</h5>
                        <div class="border p-3" id="preview-box"
                            style="background-color: {{ $theme->theme_color ?? '#FFFFFF' }}">
                            <span id="preview-text">Preview Tema</span>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
        <script>
            // Mengambil elemen input warna dan preview
            const colorInput = document.getElementById('theme_color');
            const previewBox = document.getElementById('preview-box');
            const previewText = document.getElementById('preview-text');

            // Event listener untuk perubahan warna
            colorInput.addEventListener('input', function(e) {
                const selectedColor = e.target.value;
                previewBox.style.backgroundColor = selectedColor;

                // Update preview
                if (themeUtils.isColorDark(selectedColor)) {
                    previewText.style.color = '#FFFFFF';
                } else {
                    previewText.style.color = '#000000';
                }

                // Update themed elements
                themeUtils.updateThemedElements();
            });

            // Initialize preview
            document.addEventListener('DOMContentLoaded', function() {
                const initialColor = colorInput.value;
                if (themeUtils.isColorDark(initialColor)) {
                    previewText.style.color = '#FFFFFF';
                } else {
                    previewText.style.color = '#000000';
                }
            });
        </script>
    
@endsection
