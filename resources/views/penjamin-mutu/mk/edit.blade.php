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
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="card-title m-0">Edit Mata Kuliah</h4>
                @if(is_null($mk->id_prodi))
                    <span class="badge bg-primary px-3 py-2 text-white">MK Universitas</span>
                @else
                    <span class="badge bg-info px-3 py-2 text-white">MK Reguler / Prodi</span>
                @endif
            </div>

            <form method="POST" action="{{ route($currentPrefix . 'mk.update', ['kode' => $mk->kode]) }}">
                @csrf
                @method('put')

                @php
                    $isUnivLevel = in_array(auth()->user()->otoritas->otoritas, ['Admin Universitas', 'Penjamin Mutu Universitas', 'Wakil Rektor']);
                    $isUnivMk = is_null(old('id_prodi', $mk->id_prodi));
                @endphp

                @if($isUnivLevel)
                <div class="form-group mb-4 p-3 bg-light border rounded">
                    <label class="font-weight-bold d-block">Tipe Mata Kuliah:</label>
                    <div class="d-flex flex-wrap gap-4 mt-2">
                        <div class="form-check me-4 mb-0">
                            <label class="form-check-label font-weight-bold" style="cursor: pointer;">
                                <input type="radio" class="form-check-input" name="mk_type_toggle" id="type-univ" value="univ" {{ $isUnivMk ? 'checked' : '' }}>
                                MK Universitas (Berlaku Semua Prodi / Tanpa Keterikatan Kurikulum Spesifik)
                            </label>
                        </div>
                        <div class="form-check mb-0">
                            <label class="form-check-label font-weight-bold" style="cursor: pointer;">
                                <input type="radio" class="form-check-input" name="mk_type_toggle" id="type-prodi" value="prodi" {{ !$isUnivMk ? 'checked' : '' }}>
                                MK Reguler / Prodi (Spesifik ke Prodi & Kurikulum)
                            </label>
                        </div>
                    </div>
                </div>

                <div id="prodi-kurikulum-section" style="{{ $isUnivMk ? 'display: none;' : '' }}">
                    <div class="form-group">
                        <label>Program Studi <span class="text-danger">*</span></label>
                        <select class="form-control" name="id_prodi" id="prodi-select">
                            <option value="">-- Pilih Program Studi --</option>
                            @foreach(\App\Models\Prodi::whereHas('fakultas', function($q){ $q->where('id_universitas', auth()->user()->id_universitasUser); })->get() as $p)
                                <option value="{{ $p->id }}" {{ old('id_prodi', $mk->id_prodi) == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tahun Kurikulum <span class="text-danger">*</span></label>
                        <select class="form-control" name="id_kurikulum" id="kurikulum-select">
                            <option value="">-- Pilih Kurikulum --</option>
                            @foreach($kurikulums as $kurikulum)
                                <option value="{{ $kurikulum->id }}" {{ $kurikulum->id == $mk->id_kurikulum ? 'selected' : '' }}>
                                    {{ $kurikulum->tahun }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_kurikulum') <div class="alert alert-danger">{{ $message }}</div> @enderror
                    </div>
                    <hr>
                </div>
                @else
                {{-- Untuk Level Prodi, SELALU tampilkan pilihan Tahun Kurikulum --}}
                <div class="form-group">
                    <label>Tahun Kurikulum <span class="text-danger">*</span></label>
                    <select class="form-control" name="id_kurikulum" required>
                        <option value="">-- Pilih Kurikulum --</option>
                        @foreach($kurikulums as $kurikulum)
                            <option value="{{ $kurikulum->id }}" {{ $kurikulum->id == $mk->id_kurikulum ? 'selected' : '' }}>
                                {{ $kurikulum->tahun }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_kurikulum') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>
                @if($isUnivMk)
                <div class="alert alert-info py-2 mb-3">
                    <small><strong>Mata Kuliah Universitas</strong>: Pengaturan kurikulum ini disimpan khusus untuk Kurikulum Prodi Anda.</small>
                </div>
                @endif
                @endif

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
                    <input type="text" class="form-control" name="kode" placeholder="Kode MK" value="{{ old('kode', $mk->kode) }}" autocomplete="off">
                    <small class="text-muted">Huruf dan angka saja, minimal 9 karakter.</small>
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
                    <div class="d-flex flex-wrap gap-3 mt-1 p-3 border rounded bg-light">
                        @php
                            $rawOldSem = old('semester', $mk->semester);
                            $selectedSemesters = is_array($rawOldSem) ? array_map('strval', $rawOldSem) : array_map('trim', explode(',', (string)$rawOldSem));
                        @endphp
                        @for ($i = 1; $i <= 8; $i++)
                            <div class="form-check me-3 mb-1">
                                <label class="form-check-label font-weight-normal" style="cursor: pointer;">
                                    <input type="checkbox" class="form-check-input" name="semester[]" value="{{ $i }}" {{ in_array((string)$i, $selectedSemesters) ? 'checked' : '' }}>
                                    Semester {{ $i }}
                                </label>
                            </div>
                        @endfor
                    </div>
                    <small class="form-text text-muted">Centang satu atau lebih semester jika mata kuliah dapat diambil di beberapa semester.</small>
                    @error('semester') <div class="alert alert-danger mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Rumpun <span class="text-danger">*</span></label>
                    <div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="rumpun" value="Wajib" {{ old('rumpun', $mk->rumpun) == 'Wajib' ? 'checked' : '' }}> Wajib</label></div>
                    <div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="rumpun" value="Peminatan" {{ old('rumpun', $mk->rumpun) == 'Peminatan' ? 'checked' : '' }}> Peminatan</label></div>
                    <div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="rumpun" value="MKWK" {{ old('rumpun', $mk->rumpun) == 'MKWK' ? 'checked' : '' }}> MKWK</label></div>
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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    const sectionProdiKurikulum = $('#prodi-kurikulum-section');
    const prodiSelect = $('#prodi-select');
    const kurikulumSelect = $('#kurikulum-select');

    $('input[name="mk_type_toggle"]').on('change', function() {
        if ($(this).val() === 'univ') {
            sectionProdiKurikulum.slideUp(200);
            prodiSelect.val('').prop('required', false);
            kurikulumSelect.val('').prop('required', false);
        } else {
            sectionProdiKurikulum.slideDown(200);
            prodiSelect.prop('required', true);
            kurikulumSelect.prop('required', true);
        }
    });
});
</script>
@endsection