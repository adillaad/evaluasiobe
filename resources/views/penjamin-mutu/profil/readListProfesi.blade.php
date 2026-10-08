@if ($profesi->count() > 0)
    <style>
        .btn-outline-primary {
            color: #0284c7 !important;
            border-color: #0284c7 !important;
            background-color: #ffffff !important;
        }
        .btn-outline-primary i {
            color: #0284c7 !important;
            transition: color 0.2s ease;
        }
        .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary:active {
            background-color: #0284c7 !important;
            border-color: #0284c7 !important;
            color: #ffffff !important;
        }
        .btn-outline-primary:hover i, .btn-outline-primary:focus i, .btn-outline-primary:active i {
            color: #ffffff !important;
        }

        .btn-outline-danger {
            color: #dc3545 !important;
            border-color: #dc3545 !important;
            background-color: #ffffff !important;
        }
        .btn-outline-danger i {
            color: #dc3545 !important;
            transition: color 0.2s ease;
        }
        .btn-outline-danger:hover, .btn-outline-danger:focus, .btn-outline-danger:active {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: #ffffff !important;
        }
        .btn-outline-danger:hover i, .btn-outline-danger:focus i, .btn-outline-danger:active i {
            color: #ffffff !important;
        }
    </style>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Nama Profesi</th>
                    <th>Kurikulum</th>
                    @if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Koordinator Program Studi', 'Kepala Program Studi']))
                        <th style="width: 160px;">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($profesi as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->nama }}</td>
                        <td>{{ $item->kurikulum->tahun ?? 'N/A' }}</td>
                        @if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Koordinator Program Studi', 'Kepala Program Studi']))
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <button class="btn btn-outline-primary btn-icons" onclick="showProfesi({{ $item->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                        <i class="ti-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-icons" onclick="deleteProfesi({{ $item->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
                                        <i class="ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <p class="text-center text-muted mt-3">Tidak ada data profesi ditemukan.</p>
@endif
