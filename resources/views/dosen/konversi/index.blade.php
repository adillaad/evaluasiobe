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
                                        <a href="{{ route('dosen.konversi-nilai.step-metode', $konversi->id) }}" class="btn btn-sm btn-info text-white px-2 py-1" title="Lihat Detail MK">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-primary px-2 py-1" data-bs-toggle="modal" data-bs-target="#editSetupModal{{ $konversi->id }}" title="Edit Setup MK Kurikulum">
                                            <i class="ti ti-settings"></i>
                                        </button>
                                        <form action="{{ route('dosen.konversi-nilai.destroy', $konversi->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data konversi untuk MK {{ $konversi->mk_kode }} ini? Seluruh data nilai konversi mahasiswa terkait akan ikut terhapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" title="Hapus Konversi">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Modal Edit Setup MK Kurikulum (Hanya Tahun Ajaran yang bisa diubah) --}}
                                    <div class="modal fade" id="editSetupModal{{ $konversi->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-sm rounded-3">
                                                <form action="{{ route('dosen.konversi-nilai.update-setup', $konversi->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header py-3 px-4 bg-light border-bottom text-start">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-2">
                                                                <i class="ti ti-pencil fs-5"></i>
                                                            </div>
                                                            <div>
                                                                <h5 class="modal-title fs-6 fw-bold mb-0 text-dark">Edit Setup MK Kurikulum</h5>
                                                                <span class="text-muted small">Perbarui Tahun Ajaran Konversi</span>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4 text-start">
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold text-dark mb-1">Mata Kuliah</label>
                                                            <input type="text" class="form-control form-control-sm bg-light text-dark fw-semibold" value="{{ $konversi->mk_kode }} &mdash; {{ $konversi->mk->nama ?? '' }}" disabled readonly>
                                                            <small class="text-muted" style="font-size: 0.75rem;"><i class="ti ti-info-circle me-1"></i> Mata Kuliah tidak dapat diubah.</small>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold text-dark mb-1">Kurikulum</label>
                                                            <input type="text" class="form-control form-control-sm bg-light text-dark fw-semibold" value="Kurikulum {{ $konversi->kurikulum->tahun ?? '-' }}" disabled readonly>
                                                            <small class="text-muted" style="font-size: 0.75rem;"><i class="ti ti-info-circle me-1"></i> Kurikulum tidak dapat diubah.</small>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="tahun_ajaran_id_{{ $konversi->id }}" class="form-label small fw-bold text-dark mb-1">Tahun Ajaran <span class="text-danger">*</span></label>
                                                            <select name="tahun_ajaran_id" id="tahun_ajaran_id_{{ $konversi->id }}" class="form-select form-select-sm" required>
                                                                @foreach ($tahunAjarans as $ta)
                                                                    <option value="{{ $ta->id }}" {{ $konversi->tahun_ajaran_id == $ta->id ? 'selected' : '' }}>
                                                                        {{ $ta->tahun }} {{ $ta->jenis_semester ? '(' . $ta->jenis_semester . ')' : '' }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;"><i class="ti ti-check me-1"></i> Silakan pilih Tahun Ajaran yang ingin diperbarui.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer py-2 px-4 bg-light border-top justify-content-between">
                                                        <button type="button" class="btn btn-light btn-sm text-secondary px-3" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm">
                                                            <i class="ti ti-device-floppy me-1"></i> Simpan Perubahan
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
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
