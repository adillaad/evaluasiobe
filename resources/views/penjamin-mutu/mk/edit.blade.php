{{-- @php
    $routePrefix = [
        'Penjamin Mutu Program Studi' => 'penjamin-mutu.',
        'Kepala Program Studi' => 'kepala-program-studi.',
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas] ?? 'penjamin-mutu.';
@endphp --}}

@extends('penjamin-mutu.template')
@section('content')
<div class="stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Edit Mata Kuliah</h4>
            <form method="POST" action="{{ route($currentPrefix . 'mk.update', ['kode' => $mk->kode]) }}">
                @csrf
                @method('put')

                {{-- Dropdown Kurikulum dan Prasyarat --}}
                <div class="form-group">
                    <label>Tahun Kurikulum <span class="text-danger">*</span></label>
                    <select class="form-control" name="id_kurikulum">
                        @foreach($kurikulums as $kurikulum)
                            <option value="{{ $kurikulum->id }}" {{ $kurikulum->id == $mk->id_kurikulum ? 'selected' : '' }}>
                                {{ $kurikulum->tahun }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_kurikulum') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>MK Prasyarat</label>
                    <select class="form-control" name="prasyarat">
                        <option value="">Tidak ada</option>
                        @foreach($prasyarats as $prasyarat)
                            <option value="{{ $prasyarat->nama }}" {{ $prasyarat->nama == $mk->prasyarat ? 'selected' : '' }}>
                                {{ $prasyarat->nama }} ({{$prasyarat->kode}})
                            </option>
                        @endforeach
                    </select>
                    @error('prasyarat') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>

                {{-- Input Fields untuk Detail MK --}}
                <div class="form-group">
                    <label>Kode MK <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="kode" placeholder="Kode MK" value="{{ old('kode', $mk->kode) }}" maxlength="50" autocomplete="off">
                    <small class="text-muted">Huruf dan angka saja, minimal 9 karakter, maksimal 50 karakter.</small>
                    @error('kode') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Nama MK <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama" placeholder="Nama MK" value="{{ old('nama', $mk->nama) }}" autocomplete="off">
                    @error('nama') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Nama MK (English) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama_eng" placeholder="English course name" value="{{ old('nama_eng', $mk->nama_eng) }}" autocomplete="off">
                    @error('nama_eng') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Semester <span class="text-danger">*</span></label>
                    <select class="form-control" name="semester">
                        <option value="" disabled>Pilih Semester...</option>
                        @for ($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ old('semester', $mk->semester) == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                    @error('semester') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Rumpun <span class="text-danger">*</span></label>
                    <div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="rumpun" value="Wajib" {{ old('rumpun', $mk->rumpun) == 'Wajib' ? 'checked' : '' }}> Wajib</label></div>
                    <div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="rumpun" value="Peminatan" {{ old('rumpun', $mk->rumpun) == 'Peminatan' ? 'checked' : '' }}> Peminatan</label></div>
                    @error('rumpun') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                <div class="row">
                    <div class="col-6 form-group">
                        <label>Bobot teori (sks) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="bobot_teori" placeholder="Bobot teori" value="{{ old('bobot_teori', $mk->bobot_teori) }}" autocomplete="off">
                        @error('bobot_teori') <div class="alert alert-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6 form-group">
                        <label>Bobot praktikum (sks)</label>
                        <input type="number" class="form-control" name="bobot_praktikum" placeholder="Bobot praktikum" value="{{ old('bobot_praktikum', $mk->bobot_praktikum) }}" autocomplete="off">
                        @error('bobot_praktikum') <div class="alert alert-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 form-group">
                        <label>Ambang Batas Kelulusan Mahasiswa <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" max="100" class="form-control" name="batas_kelulusan_mhs" placeholder="contoh: 50.00" value="{{ old('batas_kelulusan_mhs', $mk->batas_kelulusan_mhs) }}">
                        @error('batas_kelulusan_mhs') <div class="alert alert-danger">{{ $message }}</div> @enderror
                    </div>
                
                    <div class="col-6 form-group">
                        <label>Ambang Batas Kelulusan MK (%) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" max="100" class="form-control" name="batas_kelulusan_mk" placeholder="contoh: 75.00" value="{{ old('batas_kelulusan_mk', $mk->batas_kelulusan_mk) }}">
                        @error('batas_kelulusan_mk') <div class="alert alert-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label>Deskripsi <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="deskripsi" placeholder="Deskripsi" style="height: 100px" autocomplete="off">{{ old('deskripsi', $mk->deskripsi) }}</textarea>
                    @error('deskripsi') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                
                {{-- Tombol Aksi --}}
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    <a href="{{ route($currentPrefix . 'mk.susunan-mk') }}" class="btn btn-light">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection