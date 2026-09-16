@extends('penjamin-mutu.template')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title mb-1">Daftar Asesmen per Mata Kuliah</h4>
        

                {{-- Filter Form --}}
                <form method="GET" action="{{ url()->current() }}" class="row g-3 bg-light p-3 rounded mb-4">
                    @if ($userOtoritas === 'Penjamin Mutu Universitas')
                        <div class="col-md-4">
                            <label for="fakultas_id" class="form-label fw-bold">Fakultas</label>
                            <select name="fakultas_id" id="fakultas_id" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Semua Fakultas --</option>
                                @foreach ($fakultasOptions as $f)
                                    <option value="{{ $f->id }}" {{ $selectedFakultasId == $f->id ? 'selected' : '' }}>
                                        {{ $f->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if (in_array($userOtoritas, ['Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas']))
                        <div class="col-md-4">
                            <label for="prodi_id" class="form-label fw-bold">Program Studi</label>
                            <select name="prodi_id" id="prodi_id" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Semua Prodi --</option>
                                @foreach ($prodiOptions as $p)
                                    <option value="{{ $p->id }}" {{ $selectedProdiId == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-md-4">
                        <label for="kurikulum_id" class="form-label fw-bold">Kurikulum</label>
                        <select name="kurikulum_id" id="kurikulum_id" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Semua Kurikulum --</option>
                            @foreach ($kurikulumOptions as $k)
                                <option value="{{ $k->id }}" {{ $selectedKurikulumId == $k->id ? 'selected' : '' }}>
                                    Tahun {{ $k->tahun }} - {{ $k->nama ?? 'Kurikulum' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <a href="{{ url()->current() }}" class="btn btn-outline-secondary w-100">
                            <i class="mdi mdi-refresh me-1"></i> Reset Filter
                        </a>
                    </div>
                </form>



                {{-- Tabel MK --}}
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:5%" class="text-center">No</th>
                                <th style="width:12%">Kode MK</th>
                                <th>Nama Mata Kuliah</th>
                                <th style="width:22%">Program Studi</th>
                                <th style="width:12%" class="text-center">Kurikulum</th>
                                <th style="width:18%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mks as $i => $mk)
                                <tr>
                                    <td class="text-center">{{ ($mks->currentPage() - 1) * $mks->perPage() + $i + 1 }}</td>
                                    <td><span class="badge bg-secondary">{{ $mk->kode }}</span></td>
                                    <td>
                                        <strong>{{ $mk->nama }}</strong>
                                        @if ($mk->nama_eng)
                                            <br><small class="text-muted">{{ $mk->nama_eng }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $mk->prodi->nama ?? '-' }}
                                        @if ($mk->prodi->fakultas ?? null)
                                            <br><small class="text-muted">{{ $mk->prodi->fakultas->nama }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($mk->kurikulum)
                                            <span class="badge bg-info text-dark">{{ $mk->kurikulum->tahun }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1 justify-content-center">
                                            <a href="{{ url()->current() }}/{{ $mk->kode }}" class="btn btn-sm btn-primary">
                                                <i class="mdi mdi-eye me-1"></i> Detail
                                            </a>

                                            @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                                                <form action="{{ url()->current() }}/{{ $mk->kode }}/destroy-all" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus SEMUA asesmen pada mata kuliah {{ $mk->nama }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="mdi mdi-delete me-1"></i> Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted p-4">
                                        <i class="mdi mdi-information-outline me-1"></i>
                                        Tidak ada data Mata Kuliah.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                    <small class="text-muted">
                        Menampilkan {{ $mks->firstItem() ?? 0 }} - {{ $mks->lastItem() ?? 0 }} dari {{ $mks->total() }} mata kuliah
                    </small>
                    <div>
                        {{ $mks->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
