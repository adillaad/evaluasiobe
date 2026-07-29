{{-- @php
    $routePrefix = [
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp --}}

@extends('penjamin-mutu.template')
@section('content')
<div class="col-12 grild-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Tambah Mata Kuliah</h4>
            <form method="POST" action="{{ route($currentPrefix . 'mk.store') }}" enctype="multipart/form-data">
                @csrf
                {{-- Form Input Mata Kuliah --}}
                <div class="form-group">
                    <label>Tahun kurikulum <span class="text-danger">*</span></label>
                    <select class="form-control" name="id_kurikulum" id="kurikulum-select">
                        <option value="" disabled selected>Pilih Tahun Kurikulum...</option>
                        @foreach($kurikulums as $k)
                            <option value="{{ $k->id }}">{{ $k->tahun }}</option>
                        @endforeach
                    </select>
                    @error('id_kurikulum') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>MK prasyarat</label>
                    <select class="form-control" name="prasyarat" id="prasyarat-select">
                        <option value="" disabled selected>Pilih MK Prasyarat...</option>
                        @foreach($mks as $mk)
                            <!-- <option value="{{ $mk->kode }}">{{ $mk->kode }} - {{ $mk->nama }}</option> -->
                             <!-- supaya di rps langsung terbaca prasyarat mata kuliahnya -->
                            <option value="{{ $mk->nama }}">{{ $mk->kode }} - {{ $mk->nama }}</option>
                        @endforeach
                    </select>
                    @error('prasyarat') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>

                {{-- Sisa Form (Kode, Nama, dll) --}}
                <div class="form-group">
                    <label>Kode MK <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="kode" placeholder="Kode MK" value="{{ old('kode') }}" autocomplete="off">
                    @error('kode') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Nama MK <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama" placeholder="Nama MK" value="{{ old('nama') }}" autocomplete="off">
                    @error('nama') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Nama MK (English) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama_eng" value="{{ old('nama_eng') }}" placeholder="English course name">
                    @error('nama_eng')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Semester <span class="text-danger">*</span></label>
                    <select class="form-control" name="semester">
                        <option value="" disabled selected>Pilih Semester...</option>
                        @for ($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                    @error('semester') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Rumpun <span class="text-danger">*</span></label>
                    <div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="rumpun" value="Wajib" {{ old('rumpun') == 'Wajib' ? 'checked' : '' }}> Wajib</label></div>
                    <div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="rumpun" value="Peminatan" {{ old('rumpun') == 'Peminatan' ? 'checked' : '' }}> Peminatan</label></div>
                    @error('rumpun') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <div class="row">
                    <div class="col-6 form-group">
                        <label>Bobot teori (sks) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="bobot_teori" placeholder="Bobot teori" value="{{ old('bobot_teori') }}" autocomplete="off">
                        @error('bobot_teori') <div class="alert alert-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6 form-group">
                        <label>Bobot praktikum (sks)</label>
                        <input type="text" class="form-control" name="bobot_praktikum" placeholder="Bobot praktikum" value="{{ old('bobot_praktikum') }}" autocomplete="off">
                        @error('bobot_praktikum') <div class="alert alert-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 form-group">
                        <label>Ambang Kelulusan Mahasiswa <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" max="100" class="form-control" name="batas_kelulusan_mhs" placeholder="Contoh : 50" value="{{ old('batas_kelulusan_mhs') }}">
                    </div>
                    <div class="col-6 form-group">
                        <label>Ambang Kelulusan MK (%) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" max="100"  class="form-control" name="batas_kelulusan_mk" placeholder="Contoh : 75" value="{{ old('batas_kelulusan_mk') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label>Deskripsi <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="deskripsi" placeholder="Deskripsi" style="height: 100px" autocomplete="off">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <input type="submit" class="btn btn-primary me-2" value="Submit">
            </form>
        </div>
    </div>
</div>
@endsection