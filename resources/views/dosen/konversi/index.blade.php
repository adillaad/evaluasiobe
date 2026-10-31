@php
    $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
    $currentPrefix = $userOtoritas
        ? str_replace(' ', '-', strtolower($userOtoritas)) . '.'
        : 'dosen.';
@endphp
@extends('dosen.template')

@section('content')
    <h3 class="fw-bold text-center mb-4">Daftar Mata Kuliah Nilai Konversi</h3>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="fw-bold mb-0">
                <i class="ti ti-history me-2 text-primary"></i>Riwayat Konversi Nilai
            </h6>
            <a href="{{ route($currentPrefix . 'konversi-nilai.create') }}" class="btn btn-primary btn-sm fw-semibold">
                <i class="ti ti-plus me-1"></i> Buat Konversi Baru
            </a>
        </div>
        <div class="card-body p-4">
            <p class="text-muted small mb-3">
                Riwayat pemetaan dan konversi nilai per metode penilaian untuk periode sebelum sistem digunakan atau periode semester yang telah berlalu.
            </p>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%" class="text-center">No</th>
                            <th style="width: 15%">Kode MK</th>
                            <th>Nama Mata Kuliah</th>
                            <th style="width: 20%">Tahun Akademik</th>
                            <th style="width: 15%" class="text-center">Kurikulum</th>
                            <th style="width: 22%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($konversis as $i => $konversi)
                            <tr>
                                <td class="text-center small">{{ ($konversis->currentPage() - 1) * $konversis->perPage() + $i + 1 }}</td>
                                <td>
                                    <span class="badge badge-kode-mk">
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
                                        <a href="{{ route($currentPrefix . 'konversi-nilai.step-metode', $konversi->id) }}"
                                           class="btn btn-action-table btn-action-outline-blue"
                                           data-bs-toggle="tooltip"
                                           title="Lihat Detail MK">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-action-table btn-action-solid-blue"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editSetupModal{{ $konversi->id }}"
                                                title="Edit Setup MK Kurikulum">
                                            <i class="ti ti-settings"></i>
                                        </button>
                                        <form action="{{ route($currentPrefix . 'konversi-nilai.destroy', $konversi->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus data konversi untuk MK {{ $konversi->mk_kode }} ini? Seluruh data nilai konversi mahasiswa terkait akan ikut terhapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-action-table btn-action-outline-red"
                                                    data-bs-toggle="tooltip"
                                                    title="Hapus Konversi">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Modal Edit Setup MK Kurikulum (Hanya Tahun Akademik yang bisa diubah) --}}
                                    <div class="modal fade" id="editSetupModal{{ $konversi->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-sm rounded-3">
                                                <form action="{{ route($currentPrefix . 'konversi-nilai.update-setup', $konversi->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header py-3 px-4 bg-light border-bottom text-start">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-2">
                                                                <i class="ti ti-pencil fs-5"></i>
                                                            </div>
                                                            <div>
                                                                <h5 class="modal-title fs-6 fw-bold mb-0 text-dark">Edit Setup MK Kurikulum</h5>
                                                                <span class="text-muted small">Perbarui Tahun Akademik Konversi</span>
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
                                                            <label for="tahun_ajaran_id_{{ $konversi->id }}" class="form-label small fw-bold text-dark mb-1">Tahun Akademik <span class="text-danger">*</span></label>
                                                            <select name="tahun_ajaran_id" id="tahun_ajaran_id_{{ $konversi->id }}" class="form-select form-select-sm" required>
                                                                @foreach ($tahunAjarans as $ta)
                                                                    <option value="{{ $ta->id }}" {{ $konversi->tahun_ajaran_id == $ta->id ? 'selected' : '' }}>
                                                                        {{ $ta->tahun }} {{ $ta->jenis_semester ? '(' . $ta->jenis_semester . ')' : '' }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;"><i class="ti ti-check me-1"></i> Silakan pilih Tahun Akademik yang ingin diperbarui.</small>
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

    <style>
        @php
            $themeColorKonversi = \App\Support\AptikomTheme::resolve(auth()->user());
        @endphp
        /* Badge Kode MK (matching validasi soal) */
        .badge-kode-mk {
            background-color: {{ $themeColorKonversi }} !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            padding: 4px 12px !important;
            border-radius: 50rem !important;
            font-size: 11.5px !important;
            display: inline-block !important;
            letter-spacing: 0.02em !important;
        }

        /* Action Buttons Sesuai Model Gambar (#006199 & #dc3545) */
        .btn-action-table {
            width: 32px;
            height: 32px;
            min-width: 32px;
            padding: 0 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            font-size: 13.5px;
            line-height: 1;
            border: 1.5px solid transparent;
            transition: all 0.2s ease-in-out;
            text-decoration: none;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .btn-action-table:hover:not(:disabled) {
            transform: translateY(-2px);
        }

        .btn-action-table:active:not(:disabled) {
            transform: translateY(0);
        }

        /* 1. Outline Biru (Detail) - Background putih, border biru #006199 */
        .btn-action-outline-blue {
            background-color: #ffffff !important;
            border-color: #006199 !important;
            color: #006199 !important;
        }
        .btn-action-outline-blue:hover:not(:disabled),
        .btn-action-outline-blue:focus:not(:disabled) {
            background-color: #006199 !important;
            border-color: #006199 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(0, 97, 153, 0.28) !important;
        }

        /* 2. Solid Biru (Edit Setup) - Background biru #006199, icon putih */
        .btn-action-solid-blue {
            background-color: #006199 !important;
            border-color: #006199 !important;
            color: #ffffff !important;
        }
        .btn-action-solid-blue:hover:not(:disabled),
        .btn-action-solid-blue:focus:not(:disabled) {
            background-color: #004c78 !important;
            border-color: #004c78 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(0, 76, 120, 0.32) !important;
        }

        /* 3. Outline Merah (Hapus) - Background putih, border merah #dc3545 */
        .btn-action-outline-red {
            background-color: #ffffff !important;
            border-color: #dc3545 !important;
            color: #dc3545 !important;
        }
        .btn-action-outline-red:hover:not(:disabled),
        .btn-action-outline-red:focus:not(:disabled) {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.28) !important;
        }

        .btn-action-table:disabled {
            opacity: 0.45;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.bootstrap && window.bootstrap.Tooltip) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new window.bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        });
    </script>
@endsection
