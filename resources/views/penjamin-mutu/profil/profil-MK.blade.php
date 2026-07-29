@php
    $userOtoritas = auth()->user()->otoritas->otoritas ?? '';

    switch ($userOtoritas) {
        case 'Kepala Program Studi':
            $currentPrefix = 'kepala-program-studi.';
            $baseTemplate = 'penjamin-mutu.template';
            break;
        case 'Penjamin Mutu Universitas':
            $currentPrefix = 'penjamin-mutu.universitas.';
            $baseTemplate = 'penjamin-mutu.template';
            break;
        case 'Penjamin Mutu Fakultas':
            $currentPrefix = 'penjamin-mutu.fakultas.';
            $baseTemplate = 'penjamin-mutu.template';
            break;
        case 'Penjamin Mutu Program Studi':
            $currentPrefix = 'penjamin-mutu.program-studi.';
            $baseTemplate = 'penjamin-mutu.template';
            break;
        default:
            $currentPrefix = 'admin.';
            $baseTemplate = 'penjamin-mutu.template';
            break;
    }
@endphp

@extends($baseTemplate)

@section('content')

    {{-- ── Flash messages ─────────────────────────────────────────── --}}
    @if (session()->has('failed'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('failed') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ── Page heading ─────────────────────────────────────────────── --}}
    <h2 class="fw-bold text-center text-dark mb-4 fs-4">
        Halaman Pemetaan Profil Lulusan - MK
    </h2>

    {{-- ── Main card ────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">

        {{-- Card header --}}
        <div class="card-header bg-white border-bottom py-3 px-4">
            <div class="row gy-2 align-items-center">

                {{-- Title --}}
                <div class="col-12 col-md-6">
                    <h5 class="fw-bold text-dark mb-0 lh-base">
                        Data Pemetaan Profil Lulusan - Mata Kuliah
                    </h5>
                </div>

                {{-- Action buttons --}}
                <div class="col-12 col-md-6">
                    <div class="d-flex flex-wrap gap-2 justify-content-md-end justify-content-start">

                        {{-- Unduh PDF --}}
                        <a href="{{ route($currentPrefix . 'generatePDFProfilMK', request()->query()) }}"
                            class="btn btn-danger btn-sm fw-semibold px-3">
                            <i class="mdi mdi-file-pdf-box me-1"></i> Unduh PDF
                        </a>

                        {{-- Cetak --}}
                        <a href="{{ route($currentPrefix . 'printProfilMK', request()->query()) }}"
                            class="btn btn-primary btn-sm fw-semibold px-3" target="_blank">
                            <i class="mdi mdi-printer me-1"></i> Cetak
                        </a>

                    </div>
                </div>
            </div>
        </div>

        {{-- Card body --}}
        <div class="card-body p-4">

            {{-- Filter section --}}
            <div class="mb-4">
                <x-filter-form :universities="$universities" :faculties="$faculties" :programs="$programs" :kurikulums="$kurikulums" :showKurikulum="true" />
            </div>

            {{-- Responsive table wrapper --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th class="text-center fw-bold" style="width:52px;">No</th>
                            <th class="text-center fw-bold" style="width:110px;">Kode Profil</th>
                            <th class="fw-bold">Deskripsi</th>

                            @if ($userOtoritas === 'Penjamin Mutu Universitas')
                                <th class="fw-bold" style="width:160px;">Fakultas</th>
                                <th class="fw-bold" style="width:160px;">Jurusan</th>
                            @elseif ($userOtoritas === 'Penjamin Mutu Fakultas')
                                <th class="fw-bold" style="width:160px;">Jurusan</th>
                            @endif

                            <th class="text-center fw-bold" style="width:100px;">Kode MK</th>
                            <th class="fw-bold">Nama Mata Kuliah</th>
                            <th class="text-center fw-bold" style="width:90px;">Kurikulum</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $no = ($groupedPaginated->currentPage() - 1) * $groupedPaginated->perPage() + 1;
                        @endphp

                        @forelse ($groupedPaginated as $items)
                            @php $rowspan = max($items->count(), 1); @endphp

                            @foreach ($items as $index => $item)
                                <tr>
                                    {{-- Cells shared across all rows of the group --}}
                                    @if ($index === 0)
                                        <td rowspan="{{ $rowspan }}" class="text-center fw-semibold align-middle">
                                            {{ $no++ }}
                                        </td>

                                        <td rowspan="{{ $rowspan }}" class="text-center align-middle">
                                            {{ $item->profil_kode ?? '-' }}
                                        </td>

                                        <td rowspan="{{ $rowspan }}" class="align-top"
                                            style="white-space:normal;word-break:break-word;">
                                            {{ $item->profil_nama ?? '-' }}
                                        </td>

                                        @if ($userOtoritas === 'Penjamin Mutu Universitas')
                                            <td rowspan="{{ $rowspan }}" class="align-top"
                                                style="white-space:normal;word-break:break-word;">
                                                {{ $item->fakultas_nama ?? '-' }}
                                            </td>
                                            <td rowspan="{{ $rowspan }}" class="align-top"
                                                style="white-space:normal;word-break:break-word;">
                                                {{ $item->prodi_nama ?? '-' }}
                                            </td>
                                        @elseif ($userOtoritas === 'Penjamin Mutu Fakultas')
                                            <td rowspan="{{ $rowspan }}" class="align-top"
                                                style="white-space:normal;word-break:break-word;">
                                                {{ $item->prodi_nama ?? '-' }}
                                            </td>
                                        @endif
                                    @endif

                                    {{-- Per-row MK cells --}}
                                    <td class="text-center">{{ $item->mk_kode ?? '-' }}</td>

                                    <td style="white-space:normal;word-break:break-word;">
                                        {{ $item->mk_nama ?? '-' }}
                                    </td>

                                    <td class="text-center">{{ $item->kurikulum_tahun ?? '-' }}</td>
                                </tr>
                            @endforeach

                        @empty
                            <tr>
                                <td colspan="
                                    @if ($userOtoritas === 'Penjamin Mutu Universitas') 8
                                    @elseif ($userOtoritas === 'Penjamin Mutu Fakultas') 7
                                    @else 6 @endif
                                "
                                    class="text-center text-muted py-5">
                                    <strong class="d-block mb-1">Data belum tersedia</strong>
                                    Belum terdapat data pemetaan profil lulusan dengan mata kuliah.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>{{-- /table-responsive --}}

            {{-- ── Pagination ──────────────────────────────────────────── --}}
            @if ($groupedPaginated->hasPages())
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">

                    <p class="text-muted small mb-0">
                        Menampilkan
                        <strong>{{ $groupedPaginated->firstItem() }}</strong>
                        sampai
                        <strong>{{ $groupedPaginated->lastItem() }}</strong>
                        dari
                        <strong>{{ $groupedPaginated->total() }}</strong>
                        profil lulusan
                    </p>

                    <div>
                        {{ $groupedPaginated->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>

                </div>
            @endif

        </div>{{-- /card-body --}}
    </div>{{-- /card --}}

    <script src="{{ asset('assets/js/dynamic-filters.js') }}"></script>

    <script>
        $(document).ready(function() {

            $('#filter-form').on('submit', function() {
                $(this).attr('method', 'GET');
                $(this).attr('action', '{{ url()->current() }}');
            });

            $('#reset-filter').on('click', function(e) {
                e.preventDefault();
                window.location.href = '{{ route(Request::route()->getName()) }}';
            });

        });
    </script>

@endsection
