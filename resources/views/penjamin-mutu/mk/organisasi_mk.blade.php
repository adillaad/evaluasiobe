@extends(auth()->user()->otoritas->otoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')
    @if (session()->has('failed'))
        <div class="alert alert-danger" role="alert" id="box">
            <div>{{ session('failed') }}</div>
        </div>
    @elseif (session()->has('success'))
        <div class="alert greenAdd" role="alert" id="box">
            <div>{{ session('success') }}</div>
        </div>
    @endif    <style>
        .table-organisasi-mk {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        .table-organisasi-mk th {
            background-color: #f8fafc !important;
            color: #334155 !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            padding: 12px 14px !important;
            vertical-align: middle !important;
            border-bottom: 2px solid #cbd5e1 !important;
            text-transform: uppercase;
        }
        .table-organisasi-mk td {
            vertical-align: middle !important;
            padding: 12px 14px !important;
            font-size: 13.5px !important;
            color: #1e293b !important;
            line-height: 1.6 !important;
        }
        .code-badge-wajib {
            background-color: #dcfce7;
            color: #15803d;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 6px;
            font-size: 12px;
            display: inline-block;
            margin: 2px 1px;
        }
        .code-badge-peminatan {
            background-color: #fef9c3;
            color: #a16207;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 6px;
            font-size: 12px;
            display: inline-block;
            margin: 2px 1px;
        }
        .code-badge-kurikulum {
            background-color: #e0f2fe;
            color: #0369a1;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 6px;
            font-size: 12px;
            display: inline-block;
            margin: 2px 1px;
        }
    </style>

    <div class="container-fluid">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h4 class="card-title mb-1">Organisasi Mata Kuliah</h4>
                        <p class="text-muted small mb-0">Distribusi Mata Kuliah berdasarkan Semester dan Rumpun Kompetensi.</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-organisasi-mk align-middle">
                        <thead>
                            <tr>
                                <th style="width: 7%; text-align: center;">SMT</th>
                                <th style="width: 8%; text-align: center;">SKS</th>
                                <th style="width: 10%; text-align: center;">JML MK</th>
                                <th style="width: 45%;">MK Kompetensi Utama Prodi</th>
                                <th style="width: 30%;">MK Pilihan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($semesters->isEmpty())
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Tidak ada data Organisasi Mata Kuliah.</td>
                                </tr>
                            @else
                                @foreach ($semesters as $semester)
                                    <tr>
                                        <td class="text-center fw-bold text-primary">{{ $semester->semester }}</td>
                                        <td class="text-center fw-semibold">{{ $semester->total_sks }}</td>
                                        <td class="text-center fw-semibold">{{ $semester->jumlah_mk }}</td>
                                        <td>
                                            @if(!empty($semester->kode_wajib))
                                                @foreach(explode(',', $semester->kode_wajib) as $kode)
                                                    <span class="code-badge-wajib">{{ trim($kode) }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($semester->kode_peminatan))
                                                @foreach(explode(',', $semester->kode_peminatan) as $kode)
                                                    <span class="code-badge-peminatan">{{ trim($kode) }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                        <tfoot class="bg-light fw-bold">
                            <tr>
                                <td class="text-center">Total:</td>
                                <td class="text-center text-primary">{{ $totals->total_sks ?? 0 }}</td>
                                <td class="text-center text-primary">{{ $totals->jumlah_mk ?? 0 }}</td>
                                <td colspan="2" class="text-muted small font-weight-normal align-middle">Total SKS dan Jumlah Mata Kuliah per Kurikulum</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-3"><i class="mdi mdi-information-outline me-1 text-primary"></i> Deskripsi Mata Kuliah</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 border rounded bg-white h-100 shadow-sm">
                            <h6 class="fw-bold text-success mb-2"><i class="mdi mdi-bookmark-check me-1"></i> MK Kompetensi Utama Prodi (Wajib)</h6>
                            <div style="max-height: 250px; overflow-y: auto;">
                                @forelse ($mks->where('rumpun', 'Wajib') as $mk)
                                    <div class="d-flex align-items-center py-1 border-bottom">
                                        <span class="code-badge-wajib me-2">{{ $mk->kode }}</span>
                                        <span class="small fw-semibold text-dark">{{ $mk->nama }}</span>
                                    </div>
                                @empty
                                    <span class="text-muted small fst-italic">Tidak ada MK Kompetensi Utama</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded bg-white h-100 shadow-sm">
                            <h6 class="fw-bold text-warning mb-2" style="color: #a16207 !important;"><i class="mdi mdi-bookmark-outline me-1"></i> MK Pilihan (Peminatan)</h6>
                            <div style="max-height: 250px; overflow-y: auto;">
                                @forelse ($mks->where('rumpun', 'Peminatan') as $mk)
                                    <div class="d-flex align-items-center py-1 border-bottom">
                                        <span class="code-badge-peminatan me-2">{{ $mk->kode }}</span>
                                        <span class="small fw-semibold text-dark">{{ $mk->nama }}</span>
                                    </div>
                                @empty
                                    <span class="text-muted small fst-italic">Tidak ada MK Pilihan</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
