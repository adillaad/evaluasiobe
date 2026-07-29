@extends('admin.template')
@section('content')
    <style>
        li.select2-selection__choice {
            color: #646464;
            font-weight: bolder;
        }
    </style>

    <div class="container-fluid  mb-4">
        <div class="card">
            <div class="card-header">
                <div class="fw-bold">
                    <h3>Tambah Prodi</h3>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    {{-- UNIVERSITAS --}}
                    <div class="form-group" id="id_universitas-form">
                        <label>Universitas<span class="text-danger">*</span></label>
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
                    {{-- FAKULTAS --}}
                    <div class="form-group" id="fakultas-form">
                        <label for="id_fakultas">Nama Fakultas<span class="text-danger">*</span></label>
                        <select class="js-example-basic-single w-100" name="id_fakultas" disabled>
                            <option value="" disabled selected>Select an option</option>
                        </select>
                        @error('id_fakultas')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    {{-- Is Aptikom? --}}
                    <x-aptikom-picker name="is_aptikom" id="is_aptikom" :value="old('is_aptikom')" label="Apakah Aptikom?" />
                    @error('is_aptikom')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                    {{-- PRODI --}}
                    <div class="form-group mb-3">
                        <label>Nama Prodi<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="prodi" name="prodi" placeholder="Nama Prodi"
                            value="{{ old('prodi') }}" autofocus autocomplete="off">
                        @error('prodi')
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
        document.getElementById('nama').addEventListener('input', function() {
            var input = this;
            var words = input.value.split(' ');
            for (var i = 0; i < words.length; i++) {
                words[i] = words[i].charAt(0).toUpperCase() + words[i].slice(1);
            }
            input.value = words.join(' ');
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            const userOtoritas = "{{ auth()->user()->otoritas->otoritas }}";

            // Initialize select2
            $('.js-example-basic-single').select2({
                width: '100%',
                placeholder: 'Select an option'
            });

            // Initialize fakultas based on initial universitas value
            function loadInitialFakultas() {
                // Get universitas ID from either select or hidden input
                const universitasId = $('select[name="id_universitas"]').val() || $('input[name="id_universitas"]').val();

                if (universitasId) {
                    var fakultasSelect = $('select[name="id_fakultas"]');

                    $.ajax({
                        url: `/${baseUrl}/get-fakultas/${universitasId}`,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            fakultasSelect.html(
                                '<option value="" disabled selected>Select an option</option>');
                            $.each(data, function(key, value) {
                                fakultasSelect.append('<option value="' + value.id + '">' +
                                    value.nama + '</option>');
                            });
                            fakultasSelect.prop('disabled', false);

                            // If there's an old fakultas value, select it
                            const oldFakultas = "{{ old('id_fakultas') }}";
                            if (oldFakultas) {
                                fakultasSelect.val(oldFakultas).trigger('change');
                            }
                        }
                    });
                }
            }

            // Event handler for universitas change
            $('select[name="id_universitas"]').on('change', function() {
                var universitasId = $(this).val();
                var fakultasSelect = $('select[name="id_fakultas"]');

                if (universitasId) {
                    $.ajax({
                        url: `/${baseUrl}/get-fakultas/${universitasId}`,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            fakultasSelect.html(
                                '<option value="" disabled selected>Select an option</option>'
                                );
                            $.each(data, function(key, value) {
                                fakultasSelect.append('<option value="' + value.id +
                                    '">' + value.nama + '</option>');
                            });
                            fakultasSelect.prop('disabled', false);
                        }
                    });
                } else {
                    fakultasSelect.html('<option value="" disabled selected>Select an option</option>');
                    fakultasSelect.prop('disabled', true);
                }
            });

            // Load initial fakultas options on page load
            loadInitialFakultas();

            // Reinitialize select2 on disabled fields
            $('select[disabled]').select2({
                width: '100%',
                disabled: true
            });
        });
    </script>
@endsection
