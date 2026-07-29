@extends('admin.template')
@section('content')
    <style>
        /* CSS untuk menyamakan tinggi baris */
        .cpmk-rows,
        .jumlah-rows {
            display: grid;
        }

        .cpmk-row,
        .jumlah-row {
            display: flex;
            align-items: center;
            /* min-height: 60px; */
            border-bottom: 1px solid #dee2e6;
        }

        .cpmk-row:last-child,
        .jumlah-row:last-child {
            border-bottom: none;
        }
    </style>
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Summary Soal</h4>
                <x-filter-form
                    :universities="$universities"
                    :faculties="$faculties"
                    :programs="$programs"
                />
                <div class="table-responsive">
                    <table class="table table-hover dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Kode MK</th>
                                <th>CPMK</th>
                                <th>Jumlah Soal</th>
                                <th>Prodi</th>
                                <th>Fakultas</th>
                                @if (auth()->user()->otoritas->otoritas != 'Admin Universitas')
                                    <th>Universitas</th>
                                @endif
                            </tr>
                        </thead>
                        {{-- @dd($cpmks) --}}
                        <tbody>
                            {{-- Loop 1: Untuk setiap grup mata kuliah --}}
                            @foreach ($cpmks as $nama_mk => $list_cpmk_per_mk)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    
                                    {{-- Ambil data dari item PERTAMA dalam grup, karena semuanya sama --}}
                                    <td>{{ $list_cpmk_per_mk->first()->mk_kode }}</td>

                                    {{-- Kolom CPMK: Perlu loop internal --}}
                                    <td class="p-0">
                                        <div class="cpmk-rows">
                                            {{-- Loop 2: Untuk setiap item CPMK di dalam grup ini --}}
                                            @foreach ($list_cpmk_per_mk as $cp)
                                                <div class="cpmk-row">
                                                    <div class="text-wrap lh-base p-2">{{ $cp->judul }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>

                                    {{-- Kolom Jumlah Soal: Perlu loop internal --}}
                                    <td class="p-0">
                                        <div class="jumlah-rows">
                                            @foreach ($list_cpmk_per_mk as $cp)
                                                <div class="jumlah-row">
                                                    <div class="p-2 mx-auto">
                                                        {{-- Akses langsung hasil COUNT dari controller --}}
                                                        {{ $cp->soal_count }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>

                                    {{-- Ambil data dari item PERTAMA lagi --}}
                                    <td>{{ $list_cpmk_per_mk->first()->nama_prodi }}</td>
                                    <td>{{ $list_cpmk_per_mk->first()->nama_fakultas }}</td>
                                    @if (auth()->user()->otoritas->otoritas != 'Admin Universitas')
                                        <td>{{ $list_cpmk_per_mk->first()->nama_universitas }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('assets/js/dynamic-filters.js') }}"></script>
    <script>
        function syncRowHeights() {
            document.querySelectorAll('.cpmk-rows').forEach((cpmkContainer, index) => {
                const jumlahContainer = document.querySelectorAll('.jumlah-rows')[index];
                const cpmkRows = cpmkContainer.querySelectorAll('.cpmk-row');
                const jumlahRows = jumlahContainer.querySelectorAll('.jumlah-row');

                // Reset heights first
                jumlahRows.forEach(row => {
                    row.style.height = 'auto';
                });

                // Set new heights
                cpmkRows.forEach((row, i) => {
                    const targetRow = jumlahRows[i];
                    if (targetRow) {
                        const height = row.offsetHeight;
                        targetRow.style.height = `${height}px`;
                    }
                });
            });
        }

        // Initialize ResizeObserver
        const resizeObserver = new ResizeObserver(() => {
            syncRowHeights();
        });

        // Initialize row height synchronization
        window.addEventListener('load', function() {
            setTimeout(() => {
                syncRowHeights();
                // Observe each cpmk-rows container for changes
                document.querySelectorAll('.cpmk-rows').forEach(container => {
                    resizeObserver.observe(container);
                });
            }, 100);
        });

        // Handle DataTable initialization
        $('.dataTable').on('draw.dt', function() {
            setTimeout(syncRowHeights, 100);
        });
    </script>
@endsection
