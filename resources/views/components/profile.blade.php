@extends($layout)
@section('content')
    <style>
        #lbname {
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            -o-user-select: none;
            user-select: none;
            color: white;
        }

        .profile-pic {
            position: relative;
            width: 200px;
            cursor: pointer;
        }

        .image {
            display: block;
            width: 100%;
            height: auto;
        }

        .middle {
            border-radius: 50%;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100%;
            width: 100%;
            opacity: 0;
            transition: .5s ease;
            background-color: hsla(0, 0%, 0%, 0.3);
            cursor: pointer;
        }

        .profile-pic:hover .middle {
            opacity: 1;
        }

        .icon {
            color: white;
            position: absolute;
            top: 50%;
            left: 50%;
            -webkit-transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
            text-align: center;
        }
    </style>
    <div class="row flex-grow">
        <div class="col-md-6 col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body text-center">
                    <center>
                        <form id="form" action="/edit-pp/{{ $user->id }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            <input style="display:none" accept="image/png, image/jpeg" type="file" name="img"
                                id="img">
                        </form>
                        <div class="profile-pic" onclick="document.getElementById('img').click()">
                            <img class="image p-3 mt-3"
                                style="width:200px; height:200px; object-fit:cover; border-radius:50%; border: 1px solid grey"
                                src="{{ asset('/assets/img/pp/' . $user->img) }}" alt="">
                            <div class="middle"><i class="icon mdi mdi-camera icon-lg"></i></div>
                        </div>
                    </center>
                    <div class="table-responsive">
                        <table class="table" style="text-align:start">
                            <tr>
                                <td>Nama</td>
                                <td>:</td>
                                <td id="input-name" hidden="hidden">
                                    <form id="form-name" action="/edit-name/{{ $user->id }}" method="post">
                                        @csrf
                                        @method('put')
                                        <input id="name" type="text" class="form-control" name="name"
                                            value="{{ $user->name }}" autofocus autocomplete="off">
                                        @error('name')
                                            <div class="alert alert-danger">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                </td>
                                <td id="submit-name" hidden="hidden">
                                    <button type="submit" class="btn-sm btn-primary me-2">Ubah</button>
                                    </form>
                                </td>
                                <td id="profile-name">{{ $user->name }} <i id="pen-edit"
                                        style="opacity:0.5; cursor:pointer" class="icon-sm mdi mdi-border-color"></i></td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>:</td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td>Otoritas</td>
                                <td>:</td>
                                <td>{{ $user->otoritas->otoritas }}</td>
                            </tr>
                            <tr>
                                <td>Universitas</td>
                                <td>:</td>
                                <td>{{ $user->universitas->nama }}</td>
                            </tr>
                            <tr>
                                <td>Fakultas</td>
                                <td>:</td>
                                <td>{{ $user->fakultas->nama }}</td>
                            </tr>
                            <tr>
                                <td>Prodi</td>
                                <td>:</td>
                                <td>
                                    {{ $user->prodis->pluck('nama')->implode(', ') ?: ($user->prodi->nama ?? '-') }}
                                    @if ($user->prodi)
                                        <span class="badge bg-primary text-white ms-1" style="font-size: 10px;">Aktif: {{ $user->prodi->nama }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Ubah Kata Sandi</h4>
                    <form method="POST" action="/edit-password/{{ $user->id }}" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="form-group" style="position: relative;">
                            <label for="old_password">Kata Sandi Lama <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="old_password" name="old_password" placeholder="Kata Sandi Lama..." autofocus autocomplete="off">
                            <span data-target="#old_password" class="mdi mdi-eye-outline toggle-password-icon" style="position: absolute; right: 15px; top: 38px; cursor: pointer;"></span>
                            @error('old_password')
                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group" style="position: relative;">
                            <label for="password">Kata Sandi Baru <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Kata Sandi Baru..." autocomplete="new-password">
                            <span data-target="#password" class="mdi mdi-eye-outline toggle-password-icon" style="position: absolute; right: 15px; top: 38px; cursor: pointer;"></span>
                            @error('password')
                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group" style="position: relative;">
                            <label for="confirm_password">Konfirmasi Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password..." autocomplete="new-password">
                            <span data-target="#confirm_password" class="mdi mdi-eye-outline toggle-password-icon" style="position: absolute; right: 15px; top: 38px; cursor: pointer;"></span>
                            @error('confirm_password')
                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary me-2">Ubah Kata Sandi</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tanda Tangan</h4>

                    <p class="card-description">
                        Tanda tangan ini akan digunakan untuk pengesahan pada dokumen tertentu sesuai otoritas Anda.
                    </p> 
                    @if($user->activeTtd)
                    <img src="{{ asset($user->activeTtd->file_ttd) }}" style="max-height:100px;">
                    @endif

                    {{-- FORM UPLOAD --}}
                    <form action="{{ route('profile.upload-ttd') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <input type="file"
                            class="form-control"
                            id="upload-ttd"
                            name="file_ttd"
                            accept=".png,.jpg,.jpeg">

                        <p class="text-muted mt-1">
                            Format file: PNG, JPG, atau JPEG (maks. 2 MB). Disarankan PNG dengan latar transparan.
                        </p>

                        <button type="submit" class="btn btn-primary mt-2">
                            Upload
                        </button>
                    </div>
                </form> 
                </div>
            </div>
        </div>
        
        @if (config('services.google.login_enabled'))
         <div class="col-md-6 col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Akun Terhubung</h4>
                    @if (session('success_google'))
                        <div class="alert alert-success">{{ session('success_google') }}</div>
                    @endif
                    @if (session('error_google'))
                        <div class="alert alert-danger">{{ session('error_google') }}</div>
                    @endif
                    {{-- Cek apakah google_id milik user ada isinya atau tidak --}}
                    @if (auth()->user()->google_id)
                        {{-- JIKA SUDAH TERHUBUNG --}}
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-google icon-lg text-success"></i>
                            <div class="ms-3">
                                <p class="mb-0 fw-bold">Terhubung dengan Google</p>
                                <p class="text-muted mb-0">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                        <form action="{{ route('google.disconnect') }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">Putuskan Hubungan</button>
                        </form>
                    @else
                        {{-- JIKA BELUM TERHUBUNG --}}
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-google icon-lg text-muted"></i>
                            <div class="ms-3">
                                <p class="mb-0 fw-bold">Hubungkan dengan Google</p>
                                <p class="text-muted mb-0">Hubungkan akun Anda untuk login lebih cepat.</p>
                            </div>
                        </div>
                        <a href="{{ route('google.connect') }}" class="btn btn-primary mt-3">Hubungkan Akun Google</a>
                    @endif
                </div>
            </div>
        </div>
        @endif
        
        @php
            $isSecondaryProdi = isset($isSecondaryProdi) ? $isSecondaryProdi : ($user->primary_prodi_id && (int)$user->id_prodiUser !== (int)$user->primary_prodi_id);
        @endphp
        @if (!$isSecondaryProdi && $profiles->count() > 1)
            <div class="col-md-6 col-lg-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Pilih Otoritas</h4>
                        <form method="POST" action="{{ route('switch-otoritas') }}">
                            @csrf
                            <div class="mb-3">
                                <select class="form-select" name="otoritas_id">
                                    @foreach ($profiles as $profile)
                                        <option value="{{ $profile->id }}" {{ $profile->active ? 'selected' : '' }}>
                                            {{ ($profile->nama_otoritas && trim($profile->nama_otoritas) !== '' && trim($profile->nama_otoritas) !== trim($profile->otoritas)) ? "$profile->nama_otoritas ($profile->otoritas)" : $profile->otoritas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan Otoritas</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <script>
        document.getElementById("img").onchange = function() {
            document.getElementById("form").submit();
        };

        document.getElementById("pen-edit").onclick = function() {
            document.getElementById("profile-name").setAttribute('hidden', 'hidden');
            document.getElementById("input-name").removeAttribute('hidden', 'hidden');
            document.getElementById("submit-name").removeAttribute('hidden', 'hidden');
        }

        document.getElementById("name").onchange = function() {
            document.getElementById("form-name").submit();
            document.getElementById("profile-name").removeAttribute('hidden', 'hidden');
            document.getElementById("input-name").setAttribute('hidden', 'hidden');
            document.getElementById("submit-name").setAttribute('hidden', 'hidden');
        };

        document.querySelectorAll('.toggle-password-icon').forEach(icon => {
            icon.addEventListener('click', function() {
                const target = document.querySelector(this.dataset.target);

                if (target.type === 'password') {
                    target.type = 'text';
                    this.classList.remove('mdi-eye-outline');
                    this.classList.add('mdi-eye-off-outline');
                } else {
                    target.type = 'password';
                    this.classList.remove('mdi-eye-off-outline');
                    this.classList.add('mdi-eye-outline');
                }
            });
        });
    </script>
@endsection