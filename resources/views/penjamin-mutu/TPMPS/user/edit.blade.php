@extends(auth()->user()->otoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
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

@section('content')
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit User</h4>
                <p class="card-description">
                    ({{ $user->name }})
                </p>
                {{-- Name --}}
                <form method="POST" action="{{ encrypt($user->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <div class="form-group">
                        <label>Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="Name"
                            value="{{ $user->name }}" autofocus autocomplete="off">
                        @error('name')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    {{-- Email address --}}
                    <div class="form-group">
                        <label>Email address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" placeholder="Email"
                            value="{{ $user->email }}" required autocomplete="off">
                        @error('email')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    {{-- Otoritas --}}
                    <div class="form-group">
                        <label>Otoritas <span class="text-danger">*</span></label>
                        <select class="js-example-basic-single w-100" name="otoritas" id="otoritas">
                            <option value="" disabled>Select...</option>
                            @if (auth()->user()->otoritas === 'TPMPS')
                                <option value="Dosen" {{ $user->otoritas == 'Dosen' ? 'selected' : '' }}>Dosen</option>
                                <option value="TPMPS" {{ $user->otoritas == 'TPMPS' ? 'selected' : '' }}>TPMPS</option>
                            @elseif(auth()->user()->otoritas === 'TPMF')
                                <option value="Dosen" {{ $user->otoritas == 'Dosen' ? 'selected' : '' }}>Dosen</option>
                                <option value="TPMPS" {{ $user->otoritas == 'TPMPS' ? 'selected' : '' }}>TPMPS</option>
                                <option value="TPMF" {{ $user->otoritas == 'TPMF' ? 'selected' : '' }}>TPMF</option>
                            @elseif(auth()->user()->otoritas === 'LP3M')
                                <option value="Dosen" {{ $user->otoritas == 'Dosen' ? 'selected' : '' }}>Dosen</option>
                                <option value="TPMPS" {{ $user->otoritas == 'TPMPS' ? 'selected' : '' }}>TPMPS</option>
                                <option value="TPMF" {{ $user->otoritas == 'TPMF' ? 'selected' : '' }}>TPMF</option>
                                <option value="LP3M" {{ $user->otoritas == 'LP3M' ? 'selected' : '' }}>LP3M</option>
                            @endif
                        </select>
                        @error('otoritas')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    {{-- Nama Otoritas --}}
                    <div class="form-group">
                        <label>Nama Otoritas</span></label>
                        <input type="text" class="form-control" name="nama_otoritas" placeholder="Nama Otoritas" value="{{ $user->nama_otoritas }}" autofocus autocomplete="off">
                    </div>
                    {{-- Jabatan --}}
                    <div class="form-group">
                        <label>Jabatan <span class="text-danger">*</span></label>
                        <select class="js-example-basic-single w-100" name="jabatan" id="jabatan">
                            <option selected="true" value="" disabled selected>Select...</option>
                            <option value="" {{ $user->jabatan == '' ? 'selected' : '' }}>Tidak Ada Jabatan</option>
                            <option value="Kaprodi" {{ $user->jabatan == 'Kaprodi' ? 'selected' : '' }}>Kaprodi</option>
                        </select>
                        @error('jabatan')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    {{-- Prodi --}}
                    <div class="form-group" id="prodi-form">
                        <label>Prodi<span class="text-danger">*</span></label>
                        <select class="js-example-basic-single w-100" name="prodi">
                            <option selected="true" value="" disabled>Select...</option>
                            @foreach ($allProdi as $prodi)
                                <option value="{{ $prodi->id }}"
                                    {{ $selectedProdi && $selectedProdi->id == $prodi->id ? 'selected' : '' }}>
                                    {{ $prodi->nama }}</option>
                            @endforeach
                        </select>
                        @error('prodi')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    {{-- Fakultas --}}
                    <div class="form-group" id="fakultas-form">
                        <label>Fakultas<span class="text-danger">*</span></label>
                        <select class="js-example-basic-single w-100" name="fakultas" required>
                            <option selected="true" value="" disabled>Select...</option>
                            @foreach ($allFakultas as $fakultas)
                                <option value="{{ $fakultas->id }}"
                                    {{ $selectedFakultas && $selectedFakultas->id == $fakultas->id ? 'selected' : '' }}>
                                    {{ $fakultas->nama }}</option>
                            @endforeach
                        </select>
                        @error('fakultas')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    {{-- Universitas --}}
                    <div class="form-group" id="universitas-form">
                        <label>Universitas<span class="text-danger">*</span></label>
                        <select class="js-example-basic-single w-100" name="universitas">
                            <option selected="true" value="" disabled>Select...</option>
                            @foreach ($allUniversitas as $universitas)
                                <option value="{{ $universitas->id }}"
                                    {{ $selectedUniversitas && $selectedUniversitas->id == $universitas->id ? 'selected' : '' }}>
                                    {{ $universitas->nama }}</option>
                            @endforeach
                        </select>
                        @error('universitas')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Profile Picture</label>
                        <p>{{ $user->img }}</p>
                        <input type="file" accept="image/png, image/jpeg" name="img" class="form-control"
                            style="padding-bottom: +27px">
                        @error('img')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary me-2">Edit</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            const userRole = "{{ auth()->user()->otoritas }}";

            if (userRole === 'TPMPS') {
                $('select[name="fakultas"]').prop('disabled', true).trigger('change');
                $('select[name="universitas"]').prop('disabled', true).trigger('change');
            } else if (userRole === 'TPMF') {
                $('select[name="universitas"]').prop('disabled', true).trigger('change');
            }
        });
    </script>
@endsection
