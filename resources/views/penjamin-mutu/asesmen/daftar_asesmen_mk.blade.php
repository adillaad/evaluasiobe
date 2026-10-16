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

    /* ── Modern Assessment Table (Compact Sizing matching original) ── */
    #asesmenTable.modern-asesmen-table {
        border-collapse: separate !important;
        border-spacing: 0 !important;
        width: 100% !important;
        border: none !important;
    }

    #asesmenTable.modern-asesmen-table thead tr {
        background-color: #f8fafc !important;
    }

    #asesmenTable.modern-asesmen-table thead th {
        background-color: #f8fafc !important;
        color: #1e293b !important;
        font-weight: 700 !important;
        font-size: 12.5px !important;
        padding: 10px 12px !important;
        border-top: none !important;
        border-left: none !important;
        border-right: none !important;
        border-bottom: 1.5px solid #e2e8f0 !important;
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    #asesmenTable.modern-asesmen-table thead th:first-child {
        border-top-left-radius: 6px !important;
        border-bottom-left-radius: 6px !important;
    }

    #asesmenTable.modern-asesmen-table thead th:last-child {
        border-top-right-radius: 6px !important;
        border-bottom-right-radius: 6px !important;
    }

    #asesmenTable.modern-asesmen-table tbody td {
        padding: 9px 12px !important;
        border-top: none !important;
        border-left: none !important;
        border-right: none !important;
        border-bottom: 1px solid #e2e8f0 !important;
        vertical-align: middle !important;
        background-color: #ffffff !important;
        font-size: 12.5px !important;
        line-height: 1.35 !important;
    }

    #asesmenTable.modern-asesmen-table tbody tr:hover td {
        background-color: #f8fafc !important;
    }

    #asesmenTable.dataTable.no-footer {
        border-bottom: 1px solid #e2e8f0 !important;
    }

    /* Kode MK Pill Badge (matching validasi soal) */
    @if ($isAptikom)
        .badge-kode-mk {
            background-color: #006199 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            padding: 4px 12px !important;
            border-radius: 50rem !important;
            font-size: 11.5px !important;
            display: inline-block !important;
            letter-spacing: 0.02em !important;
        }
    @else
        .badge-kode-mk {
            background-color: #2664F5 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            padding: 4px 12px !important;
            border-radius: 50rem !important;
            font-size: 11.5px !important;
            display: inline-block !important;
            letter-spacing: 0.02em !important;
        }
    @endif

    /* Action Buttons (Aksi) matching reference */
    .btn-action-custom,
    a.btn-action-custom,
    button.btn-action-custom,
    a.btn-action-custom.btn-action-view,
    button.btn-action-custom.btn-action-delete {
        width: 28px !important;
        height: 28px !important;
        min-width: 28px !important;
        min-height: 28px !important;
        max-width: 28px !important;
        max-height: 28px !important;
        box-sizing: border-box !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 6px !important;
        transition: all 0.2s ease-in-out !important;
        background-color: #ffffff !important;
        text-decoration: none !important;
        padding: 0 !important;
        margin: 0 !important;
        border-width: 1.5px !important;
        border-style: solid !important;
        line-height: 1 !important;
        vertical-align: middle !important;
        overflow: hidden !important;
        cursor: pointer !important;
    }

    .btn-action-custom i {
        font-size: 14px !important;
        line-height: 1 !important;
        margin: 0 !important;
        padding: 0 !important;
        display: inline-block !important;
        vertical-align: middle !important;
    }

    @if ($isAptikom)
        .btn-action-view {
            color: #006199 !important;
            border-color: #006199 !important;
        }
        .btn-action-view:hover, .btn-action-view:focus {
            background-color: #006199 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(0, 97, 153, 0.2) !important;
        }
    @else
        .btn-action-view {
            color: #2664F5 !important;
            border-color: #2664F5 !important;
        }
        .btn-action-view:hover, .btn-action-view:focus {
            background-color: #2664F5 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(38, 100, 245, 0.2) !important;
        }
    @endif

    .btn-action-delete {
        color: #dc3545 !important;
        border-color: #dc3545 !important;
    }

    .btn-action-delete:hover, .btn-action-delete:focus {
        background-color: #dc3545 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(220, 53, 69, 0.2) !important;
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
                    <table class="table table-hover dataTable align-middle modern-asesmen-table" id="asesmenTable">
                        <thead>
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
                                    <td class="text-center fw-medium text-dark">{{ ($mks->currentPage() - 1) * $mks->perPage() + $i + 1 }}</td>
                                    <td><span class="badge-kode-mk">{{ $mk->kode }}</span></td>
                                    <td>
                                        <strong>{{ $mk->nama }}</strong>
                                        @if ($mk->nama_eng)
                                            <br><small class="text-muted" style="font-size: 11px;">{{ $mk->nama_eng }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span>{{ $mk->prodi->nama ?? '-' }}</span>
                                        @if ($mk->prodi->fakultas ?? null)
                                            <br><small class="text-muted" style="font-size: 11px;">{{ $mk->prodi->fakultas->nama }}</small>
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
                                        <div class="d-inline-flex gap-1 justify-content-center align-items-center">
                                            <a href="{{ url()->current() }}/{{ $mk->kode }}" class="btn-action-custom btn-action-view" title="Detail Asesmen">
                                                <i class="mdi mdi-eye"></i>
                                            </a>

                                            @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Koordinator Program Studi', 'Kepala Program Studi']))
                                                <form action="{{ url()->current() }}/{{ $mk->kode }}/destroy-all" method="POST" class="d-inline m-0 p-0" style="display: inline-flex !important; vertical-align: middle !important;"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus SEMUA asesmen pada mata kuliah {{ $mk->nama }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action-custom btn-action-delete" title="Hapus Semua Asesmen">
                                                        <i class="mdi mdi-delete"></i>
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
