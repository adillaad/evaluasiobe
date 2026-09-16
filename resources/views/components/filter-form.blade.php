@php
    // Definisikan peran level
    $universityLevelRoles = ['Admin Universitas', 'Penjamin Mutu Universitas', 'Wakil Rektor'];
    $facultyLevelRoles = ['Wakil Dekan', 'Penjamin Mutu Fakultas'];
    $prodiLevelRoles = ['Kepala Program Studi', 'Penjamin Mutu Program Studi', 'Dosen'];

    $userOtoritas = auth()->user()->otoritas->otoritas ?? '';

    $canShowUniv = ($showUniversitas ?? true) && !in_array($userOtoritas, $universityLevelRoles) && !in_array($userOtoritas, $facultyLevelRoles) && !in_array($userOtoritas, $prodiLevelRoles);
    $canShowFakultas = ($showFakultas ?? true) && !in_array($userOtoritas, $facultyLevelRoles) && !in_array($userOtoritas, $prodiLevelRoles);
    $canShowProdi = ($showProdi ?? true) && !in_array($userOtoritas, $prodiLevelRoles);
    $canShowKurikulum = ($showKurikulum ?? false);
    $canShowCpl = ($showCpl ?? false);

    $hasVisibleFilters = $canShowUniv || $canShowFakultas || $canShowProdi || $canShowKurikulum || $canShowCpl;
@endphp

@if ($hasVisibleFilters)
<form method="GET" action="{{ url()->current() }}" class="mb-4">
    {{-- 1. Bungkus semuanya dalam SATU .row.
        Gunakan 'g-2' untuk memberi sedikit jarak antar elemen.
        Gunakan 'align-items-end' untuk meratakan semua elemen ke bagian bawah,
        sehingga tombol sejajar dengan dropdown. --}}
    <div class="row g-2 align-items-end">
        {{-- Dropdown Universitas --}}
        @if ($canShowUniv)
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
        @if ($canShowFakultas)
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
        @if ($canShowProdi)
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
        @if ($canShowKurikulum)
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
        {{-- Dropdown CPL --}}
        @if ($canShowCpl)
            <div class="col-md">
                <div class="form-group">
                    <label for="cpl_id">CPL</label>
                    <select name="cpl_id" id="cpl_id" class="form-control">
                        <option value="">Pilih CPL</option>
                        @foreach ($cplsFilter ?? [] as $cplItem)
                            <option value="{{ $cplItem->id }}" {{ request('cpl_id') == $cplItem->id ? 'selected' : '' }}>
                                {{ $cplItem->kode }} - {{ Str::limit($cplItem->judul, 40) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
        {{-- 3. Pindahkan tombol ke dalam .row yang sama --}}
        @if (!in_array($userOtoritas, $prodiLevelRoles) || $canShowKurikulum || $canShowCpl)
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
@endif