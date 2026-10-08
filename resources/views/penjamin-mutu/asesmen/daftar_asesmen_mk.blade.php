@php
    $selectedProdiId = request('prodi_id');
    if (!$selectedProdiId && request('kurikulum_id')) {
        $selectedProdiId = \Illuminate\Support\Facades\DB::table('kurikulums')->where('id', request('kurikulum_id'))->value('id_prodi');
    }
    if (!$selectedProdiId && auth()->check()) {
        $selectedProdiId = auth()->user()->id_prodiUser ?? (auth()->user()->prodi ? auth()->user()->prodi->id : null);
    }
    if ($selectedProdiId) {
        $isAptikom = (bool) \Illuminate\Support\Facades\DB::table('prodi')->where('id', $selectedProdiId)->value('is_aptikom');
    } else {
        $isAptikom = auth()->check() && auth()->user()->prodi ? (bool) auth()->user()->prodi->is_aptikom : true;
    }
@endphp

@extends('penjamin-mutu.template')

@section('content')
<style>
    @if ($isAptikom)
        /* ── APTIKOM Theme (Ocean Blue #006199 Accent) ── */
        .badge-system {
            background: linear-gradient(135deg, #006199 0%, #004c78 100%) !important;
            color: #ffffff !important;
            font-weight: 500;
        }
        .badge-system-outline {
            background-color: #f0f9ff !important;
            color: #006199 !important;
            border: 1px solid #bae6fd !important;
            font-weight: 700 !important;
            padding: 4px 12px !important;
            border-radius: 8px !important;
            font-size: 13px !important;
            display: inline-block !important;
        }
        .btn-outline-primary {
            color: #006199 !important;
            background-color: #ffffff !important;
            border-color: #006199 !important;
            transition: all 0.2s ease-in-out !important;
        }
        .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary:active {
            color: #ffffff !important;
            background: linear-gradient(135deg, #006199 0%, #004c78 100%) !important;
            border-color: #006199 !important;
            box-shadow: 0 4px 12px rgba(0, 97, 153, 0.28) !important;
        }
    @else
        /* ── NON-APTIKOM Theme (Royal Blue #2664F5 Accent) ── */
        .badge-system {
            background: linear-gradient(135deg, #2664F5 0%, #1d52cc 100%) !important;
            color: #ffffff !important;
            font-weight: 500;
        }
        .badge-system-outline {
            background-color: #f0f9ff !important;
            color: #2664F5 !important;
            border: 1px solid #bae6fd !important;
            font-weight: 700 !important;
            padding: 4px 12px !important;
            border-radius: 8px !important;
            font-size: 13px !important;
            display: inline-block !important;
        }
        .btn-outline-primary {
            color: #2664F5 !important;
            background-color: #ffffff !important;
            border-color: #2664F5 !important;
            transition: all 0.2s ease-in-out !important;
        }
        .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary:active {
            color: #ffffff !important;
            background: linear-gradient(135deg, #2664F5 0%, #1d52cc 100%) !important;
            border-color: #2664F5 !important;
            box-shadow: 0 4px 12px rgba(38, 100, 245, 0.28) !important;
        }
    @endif
    .btn-outline-danger {
        color: #dc3545 !important;
        background-color: #ffffff !important;
        border-color: #dc3545 !important;
        transition: all 0.2s ease-in-out !important;
    }
    .btn-outline-danger:hover, .btn-outline-danger:focus, .btn-outline-danger:active {
        color: #ffffff !important;
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title mb-3">Daftar Asesmen per Mata Kuliah</h4>

                {{-- Filter Form --}}
                <x-filter-form
                    :universities="$universities"
                    :faculties="$faculties"
                    :programs="$programs"
                    :kurikulums="$kurikulums"
                    :showKurikulum="true"
                />

                {{-- Tabel MK --}}
                <div class="table-responsive mt-3">
                    <table class="table table-hover table-bordered dataTable align-middle" id="asesmenTable">
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
                                    <td><span class="badge-system-outline">{{ $mk->kode }}</span></td>
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
                                            <span class="text-dark">{{ $mk->kurikulum->tahun }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1 justify-content-center">
                                            <a href="{{ url()->current() }}/{{ $mk->kode }}" class="btn btn-sm btn-outline-primary p-1 px-2" title="Detail Asesmen">
                                                <i class="mdi mdi-eye me-1"></i>
                                            </a>

                                            @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Koordinator Program Studi', 'Kepala Program Studi']))
                                                <form action="{{ url()->current() }}/{{ $mk->kode }}/destroy-all" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus SEMUA asesmen pada mata kuliah {{ $mk->nama }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger p-1 px-2" title="Hapus Semua Asesmen">
                                                        <i class="mdi mdi-delete me-1"></i>
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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function() {
        if (typeof $.fn.DataTable !== 'undefined') {
            if ($.fn.DataTable.isDataTable('#asesmenTable')) {
                $('#asesmenTable').DataTable().destroy();
            }
            $('#asesmenTable').DataTable({
                "aaSorting": [],
                "retrieve": true,
                "paging": false,
                "info": false,
                "language": {
                    "search": "Search:"
                }
            });
        }
    });
</script>
@endsection
