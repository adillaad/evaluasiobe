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
                    <h3>Edit Prodi</h3>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route($currentPrefix . 'update-prodi', ['id' => $prodi->id]) }}">
                    @csrf
                    @method('PUT')
                    {{-- UNIVERSITAS --}}
                    @php
                        $isAdminUniv = in_array(auth()->user()->otoritas->otoritas, ['Admin Universitas']);
                    @endphp
                    <div class="form-group" id="id_universitas-form">
                        <label>Universitas<span class="text-danger">*</span></label>
                        @if ($isAdminUniv)
                            <input type="hidden" name="id_universitas" value="{{ auth()->user()->id_universitasUser }}">
                        @endif
                        <select class="js-example-basic-single w-100"
                            name="{{ $isAdminUniv ? '' : 'id_universitas' }}"
                            {{ $isAdminUniv ? 'disabled' : '' }}>
                            <option value="" disabled>Select...</option>
                            @foreach ($universitas as $u)
                                <option value="{{ $u->id }}"
                                    {{ (old('id_universitas', $selectedUniversitasId) == $u->id) ? 'selected' : '' }}>
                                    {{ $u->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_universitas')
                            <div class="alert alert-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- FAKULTAS --}}
                    <div class="form-group" id="fakultas-form">
                        <label for="id_fakultas">Nama Fakultas<span class="text-danger">*</span></label>
                        <select class="js-example-basic-single w-100" name="id_fakultas" id="id_fakultas" required>
                            <option value="" disabled>Select an option</option>
                            @foreach ($fakultas as $f)
                                <option value="{{ $f->id }}"
                                    {{ old('id_fakultas', $prodi->id_fakultas) == $f->id ? 'selected' : '' }}>
                                    {{ $f->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_fakultas')
                            <div class="alert alert-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Is Aptikom? --}}
                    <x-aptikom-picker name="is_aptikom" id="is_aptikom" :value="old('is_aptikom', (int) $prodi->is_aptikom)" label="Apakah Aptikom?" />
                    @error('is_aptikom')
                        <div class="alert alert-danger mt-1">
                            {{ $message }}
                        </div>
                    @enderror

                    {{-- PRODI --}}
                    <div class="form-group mb-3">
                        <label>Nama Prodi<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="prodi" name="prodi" placeholder="Nama Prodi"
                            value="{{ old('prodi', $prodi->nama) }}" required autocomplete="off">
                        @error('prodi')
                            <div class="alert alert-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- JENJANG --}}
                    <div class="form-group mb-3">
                        <label>Jenjang Pendidikan<span class="text-danger">*</span></label>
                        <select class="form-select form-control" name="jenjang" id="jenjang" required>
                            @php
                                $currentJenjang = old('jenjang', $prodi->jenjang ?? 'S1');
                            @endphp
                            <option value="S1" {{ $currentJenjang == 'S1' ? 'selected' : '' }}>S1 - Sarjana</option>
                            <option value="S2" {{ $currentJenjang == 'S2' ? 'selected' : '' }}>S2 - Magister</option>
                            <option value="S3" {{ $currentJenjang == 'S3' ? 'selected' : '' }}>S3 - Doktor</option>
                            <option value="D3" {{ $currentJenjang == 'D3' ? 'selected' : '' }}>D3 - Diploma Tiga</option>
                            <option value="D4" {{ $currentJenjang == 'D4' ? 'selected' : '' }}>D4 - Sarjana Terapan</option>
                        </select>
                        @error('jenjang')
                            <div class="alert alert-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary me-2">Simpan Perubahan</button>
                    <a href="{{ route($currentPrefix . 'list-prodi') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function getBaseUrl() {
            const otoritas = "{{ auth()->user()->otoritas->otoritas }}";
            switch (otoritas) {
                case 'Admin':
                    return 'admin';
                case 'Admin Universitas':
                    return 'admin-universitas';
                default:
                    return '';
            }
        }

        $(document).ready(function() {
            const baseUrl = getBaseUrl();

            $('.js-example-basic-single').select2({
                width: '100%',
                placeholder: 'Select an option'
            });

            // Event handler for universitas change (only active for Admin)
            $('select[name="id_universitas"]').on('change', function() {
                var universitasId = $(this).val();
                var fakultasSelect = $('select[name="id_fakultas"]');

                if (universitasId) {
                    $.ajax({
                        url: `/${baseUrl}/get-fakultas/${universitasId}`,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            fakultasSelect.html('<option value="" disabled selected>Select an option</option>');
                            $.each(data, function(key, value) {
                                fakultasSelect.append('<option value="' + value.id + '">' + value.nama + '</option>');
                            });
                            fakultasSelect.prop('disabled', false).trigger('change');
                        }
                    });
                }
            });

            document.getElementById('prodi')?.addEventListener('input', function() {
                var input = this;
                var words = input.value.split(' ');
                for (var i = 0; i < words.length; i++) {
                    words[i] = words[i].charAt(0).toUpperCase() + words[i].slice(1);
                }
                input.value = words.join(' ');
            });
        });
    </script>
@endsection
