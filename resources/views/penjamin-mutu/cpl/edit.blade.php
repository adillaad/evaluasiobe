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
            <h4 class="card-title">Edit CPL: {{ $cpl->kode }}</h4>
            <p class="card-description">
                Program Studi: {{ $cpl->prodi->nama }}
            </p>
            <form method="POST" action="{{ route($currentPrefix . 'cpl.update', encrypt($cpl->id)) }}">
                @csrf
                @method('PUT')
                <hr>
                <div class="border rounded p-3 mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tahun Kurikulum <span class="text-danger">*</span></label>
                                <select class="form-control" name="id_kurikulum">
                                    <option value="" disabled>Pilih Kurikulum...</option>
                                    @foreach($kurikulums as $k)
                                        {{-- Menandai kurikulum yang sedang digunakan oleh CPL ini --}}
                                        <option value="{{ $k->id }}" {{ $cpl->id_kurikulum == $k->id ? 'selected' : '' }}>
                                            {{ $k->tahun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Aspek <span class="text-danger">*</span></label>
                                <select class="form-control" name="aspek">
                                    <option value="" disabled>Pilih Aspek...</option>
                                    {{-- Menandai aspek yang sedang digunakan oleh CPL ini --}}
                                    <option value="Sikap" {{ $cpl->aspek == 'Sikap' ? 'selected' : '' }}>Sikap</option>
                                    <option value="Keterampilan Umum" {{ $cpl->aspek == 'Keterampilan Umum' ? 'selected' : '' }}>Keterampilan Umum</option>
                                    <option value="Keterampilan Khusus" {{ $cpl->aspek == 'Keterampilan Khusus' ? 'selected' : '' }}>Keterampilan Khusus</option>
                                    <option value="Pengetahuan" {{ $cpl->aspek == 'Pengetahuan' ? 'selected' : '' }}>Pengetahuan</option>
                                    <option value="Pengetahuan & Keterampilan" {{ $cpl->aspek == 'Pengetahuan & Keterampilan' ? 'selected' : '' }}>Pengetahuan & Keterampilan</option>
                                    <option value="Pengetahuan Interdisipliner" {{ $cpl->aspek == 'Pengetahuan Interdisipliner' ? 'selected' : '' }}>Pengetahuan Interdisipliner</option>
                                    <option value="Keterampilan Umum & Khusus" {{ $cpl->aspek == 'Keterampilan Umum & Khusus' ? 'selected' : '' }}>Keterampilan Umum & Khusus</option>
                                    <option value="Lainnya" {{ $cpl->aspek == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Kode <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">CPL</span></div>
                                    {{-- Mengisi input dengan data 'nomor' dari CPL --}}
                                    <input type="text" class="form-control" name="nomor" value="{{ $cpl->nomor }}" placeholder="Nomor" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label>Judul <span class="text-danger">*</span></label>
                        {{-- Mengisi input dengan data 'judul' dari CPL --}}
                        <input type="text" class="form-control" name="judul" value="{{ $cpl->judul }}" placeholder="Masukkan judul CPL" autocomplete="off">
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    <a href="{{ route($currentPrefix . 'cpl.index') }}" class="btn btn-light">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection