@php
    // Definisikan peran level
    $universityLevelRoles = ['Admin Universitas', 'Penjamin Mutu Universitas', 'Wakil Rektor'];
    $facultyLevelRoles = ['Wakil Dekan', 'Penjamin Mutu Fakultas'];
    $prodiLevelRoles = ['Kepala Program Studi', 'Penjamin Mutu Program Studi', 'Dosen'];
@endphp
<form method="GET" action="{{ route(Request::route()->getName()) }}" class="mb-4">
    {{-- 1. Bungkus semuanya dalam SATU .row.
        Gunakan 'g-2' untuk memberi sedikit jarak antar elemen.
        Gunakan 'align-items-end' untuk meratakan semua elemen ke bagian bawah,
        sehingga tombol sejajar dengan dropdown. --}}
    <div class="row g-2 align-items-end">
        {{-- Dropdown Universitas --}}
        @if ($showUniversitas && !in_array(auth()->user()->otoritas->otoritas, $universityLevelRoles) && !in_array(auth()->user()->otoritas->otoritas, $facultyLevelRoles) && !in_array(auth()->user()->otoritas->otoritas, $prodiLevelRoles))
            {{-- 2. Gunakan 'col-md' agar lebar kolom fleksibel dan membagi rata ruang yang ada --}}
            <div class="col-md">
                <div class="form-group">
                    <label for="universitas_id">Universitas</label>
                    <select name="universitas_id" id="universitas_id" class="form-control">
                        <option value="">Pilih Universitas</option>
                        @foreach ($universities as $univ)
                            <option value="{{ $univ->id }}" {{ request('universitas_id') == $univ->id ? 'selected' : '' }}>
                                {{ $univ->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
        {{-- Dropdown Fakultas --}}
        @if ($showFakultas && !in_array(auth()->user()->otoritas->otoritas, $facultyLevelRoles) && !in_array(auth()->user()->otoritas->otoritas, $prodiLevelRoles))
            <div class="col-md">
                <div class="form-group">
                    <label for="fakultas_id">Fakultas</label>
                    <select name="fakultas_id" id="fakultas_id" class="form-control" {{ !request('universitas_id') && empty(auth()->user()->id_universitasUser) ? 'disabled' : '' }}>
                        <option value="">Pilih Fakultas</option>
                        @foreach ($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ request('fakultas_id') == $faculty->id ? 'selected' : '' }}>
                                {{ $faculty->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
        {{-- Dropdown Prodi --}}
        @if ($showProdi && !in_array(auth()->user()->otoritas->otoritas, $prodiLevelRoles))
            <div class="col-md">
                <div class="form-group">
                    <label for="prodi_id">Program Studi</label>
                    <select name="prodi_id" id="prodi_id" class="form-control" {{ !request('fakultas_id') && empty(auth()->user()->id_fakultasUser) ? 'disabled' : '' }}>
                        <option value="">Pilih Program Studi</option>
                        @foreach ($programs as $program)
                            <option value="{{ $program->id }}" {{ request('prodi_id') == $program->id ? 'selected' : '' }}>
                                {{ $program->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
        {{-- Dropdown Kurikulum --}}
        @if ($showKurikulum)
            <div class="col-md">
                <div class="form-group">
                    <label for="kurikulum_id">Kurikulum</label>
                    <select name="kurikulum_id" id="kurikulum_id" class="form-control">
                        <option value="">Pilih Kurikulum</option>
                        @foreach ($kurikulums as $kurikulum)
                            <option value="{{ $kurikulum->id }}" {{ request('kurikulum_id') == $kurikulum->id ? 'selected' : '' }}>
                                {{ $kurikulum->tahun }} - ({{ $kurikulum->nama_prodi }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
        {{-- 3. Pindahkan tombol ke dalam .row yang sama --}}
        @if (!in_array(auth()->user()->otoritas->otoritas, $prodiLevelRoles) || $showKurikulum)
            {{-- 4. Gunakan 'col-md-auto' agar lebar kolom tombol pas dengan isinya --}}
            <div class="col-md-auto">
                <div class="form-group">
                    {{-- Tambahkan class 'd-flex' untuk membuat tombol bersebelahan --}}
                    <div class="d-flex">
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="{{ route(Request::route()->getName()) }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</form>