@extends('dosen.template')

@section('content')
    <h3 class="fw-bold text-center mb-4">Daftar Import Nilai Konversi</h3>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="fw-bold mb-0">
                <i class="ti ti-history me-2 text-primary"></i>Riwayat Konversi Nilai
            </h6>
            <a href="{{ route('dosen.konversi-nilai.create') }}" class="btn btn-primary btn-sm fw-semibold">
                <i class="ti ti-plus me-1"></i> Buat Konversi Baru
            </a>
        </div>
        <div class="card-body p-4">
            <p class="text-muted small mb-3">
                Riwayat pemetaan dan konversi nilai historis per metode penilaian untuk periode sebelum sistem digunakan.
            </p>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%" class="text-center">No</th>
                            <th style="width: 15%">Kode MK</th>
                            <th>Nama Mata Kuliah</th>
                            <th style="width: 20%">Tahun Ajaran</th>
                            <th style="width: 15%" class="text-center">Kurikulum</th>
                            <th style="width: 22%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($konversis as $i => $konversi)
                            <tr>
                                <td class="text-center small">{{ ($konversis->currentPage() - 1) * $konversis->perPage() + $i + 1 }}</td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold">
                                        {{ $konversi->mk_kode }}
                                    </span>
                                </td>
                                <td class="small fw-semibold">{{ $konversi->mk->nama ?? '-' }}</td>
                                <td class="small">
                                    {{ $konversi->tahunAjaran->tahun ?? '-' }}
                                    @if ($konversi->tahunAjaran->jenis_semester ?? null)
                                        <span class="badge bg-light text-dark border ms-1">{{ $konversi->tahunAjaran->jenis_semester }}</span>
                                    @endif
                                </td>
                                <td class="text-center small">
                                    <span class="badge bg-info bg-opacity-10 text-info fw-bold">Tahun {{ $konversi->kurikulum->tahun ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1 justify-content-center">
                                        <a href="{{ route('dosen.konversi-nilai.detail', $konversi->id) }}" class="btn btn-sm btn-info text-white px-2 py-1" title="Lihat Nilai Konversi">
                                            <i class="ti ti-eye me-1"></i> Nilai
                                        </a>
                                        <a href="{{ route('dosen.konversi-nilai.step-metode', $konversi->id) }}" class="btn btn-sm btn-primary px-2 py-1" title="Kelola Metode & Upload Nilai">
                                            <i class="ti ti-settings me-1"></i> Kelola
                                        </a>
                                        <form action="{{ route('dosen.konversi-nilai.destroy', $konversi->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data konversi untuk MK {{ $konversi->mk_kode }} ini? Seluruh data nilai konversi mahasiswa terkait akan ikut terhapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" title="Hapus Konversi">
                                                <i class="ti ti-trash me-1"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="ti ti-folder me-2"></i>Belum ada data impor nilai konversi. Klik tombol <strong>Buat Konversi Baru</strong> untuk memulai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                <small class="text-muted">
                    Menampilkan {{ $konversis->firstItem() ?? 0 }} - {{ $konversis->lastItem() ?? 0 }} dari {{ $konversis->total() }} data
                </small>
                <div>
                    {{ $konversis->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection
