@extends('dosen.template')

@section('content')
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="card-header py-3">
                <h4 class="mb-0 fw-bold">Daftar CPMK & Sub-CPMK</h4>
            </div>

            <x-filter-form
                :universities="$universities"
                :faculties="$faculties"
                :programs="$programs"
                :kurikulums="$kurikulums"
                :showKurikulum="true"
            />

            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 mt-3"></div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered dataTable align-top">
                    <thead class="bg-light">
                        <tr class="text-center align-middle">
                            <th width="5%">No</th>
                            <th width="8%">CPL</th>
                            <th width="10%">Kode CPMK</th>
                            <th width="20%">Rincian CPMK</th>
                            <th width="12%">Kode Sub</th>
                            <th width="22%">Uraian Sub-CPMK</th>

                            @if (in_array($userOtoritas, ['Wakil Rektor', 'Wakil Dekan']))
                                <th width="10%">Prodi</th>
                            @endif
                            @if (in_array($userOtoritas, ['Wakil Rektor']))
                                <th width="10%">Fakultas</th>
                            @endif
                            @if ($userOtoritas == 'Dosen')
                                <th width="10%">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = $cpmks->firstItem(); @endphp

                        @forelse ($cpmks->groupBy('cpl_id') as $cplId => $cpmkGroup)
                            @php
                                $cplRowspan = 0;
                                foreach ($cpmkGroup as $c) {
                                    $subCount = $c->subCpmks->count();
                                    $cplRowspan += ($subCount > 0 ? $subCount : 1);
                                }

                                $firstCpl = $cpmkGroup->first()->cpl;
                                $isFirstCpmkInGroup = true;
                            @endphp

                            @foreach ($cpmkGroup as $cpmk)
                                @php
                                    $subs = $cpmk->subCpmks->sortBy('kode')->values();
                                    $subCount = $subs->count();
                                    $cpmkRowspan = $subCount > 0 ? $subCount : 1;
                                @endphp

                                <tr>
                                    @if ($isFirstCpmkInGroup)
                                        <td rowspan="{{ $cplRowspan }}" class="text-center align-middle">
                                            {{ $no++ }}
                                        </td>
                                        <td rowspan="{{ $cplRowspan }}" class="text-center align-middle fw-bold">
                                            {{ $firstCpl->kode ?? '-' }}
                                        </td>
                                        @php $isFirstCpmkInGroup = false; @endphp
                                    @endif

                                    <td rowspan="{{ $cpmkRowspan }}" class="align-middle fw-bold text-center">
                                        {{ $cpmk->kode }}
                                    </td>

                                    <td rowspan="{{ $cpmkRowspan }}" class="align-middle text-wrap" style="white-space: normal;">
                                        {!! nl2br(e(wordwrap($cpmk->judul ?? '-', 45, "\n", true))) !!}
                                    </td>

                                    @if ($subCount > 0)
                                        <td class="align-middle text-center fw-bold">
                                            {{ $subs[0]->kode }}
                                        </td>
                                        <td class="align-middle text-wrap" style="white-space: normal;">
                                            {!! nl2br(e(wordwrap($subs[0]->uraian ?? '-', 45, "\n", true))) !!}
                                        </td>
                                    @else
                                        <td colspan="2" class="text-center text-muted fst-italic bg-light align-middle">
                                            <small>- Tidak ada Sub -</small>
                                        </td>
                                    @endif

                                    @if (in_array($userOtoritas, ['Wakil Rektor', 'Wakil Dekan']))
                                        <td rowspan="{{ $cpmkRowspan }}" class="align-middle">
                                            {!! nl2br(e(wordwrap($cpmk->prodi->nama ?? '-', 25, "\n", true))) !!}
                                        </td>
                                    @endif

                                    @if (in_array($userOtoritas, ['Wakil Rektor']))
                                        <td rowspan="{{ $cpmkRowspan }}" class="align-middle">
                                            {!! nl2br(e(wordwrap($cpmk->prodi->fakultas->nama ?? '-', 25, "\n", true))) !!}
                                        </td>
                                    @endif

                                    @if ($userOtoritas == 'Dosen')
                                        <td rowspan="{{ $cpmkRowspan }}" class="text-center align-middle">
                                            <form action="{{ route('dosen.cpmk-delete', $cpmk->id) }}" method="post" class="d-inline m-0 p-0">
                                                @csrf
                                                @method('delete')
                                                <button type="submit"
                                                    class="btn btn-danger btn-icons"
                                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                                                    onclick="return confirm('PERHATIAN: Menghapus CPMK {{ $cpmk->kode }} akan menghapus SEMUA Sub-CPMK di dalamnya. Lanjutkan?')">
                                                    <i class="ti-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>

                                @if ($subCount > 1)
                                    @foreach ($subs as $index => $sub)
                                        @if ($index > 0)
                                            <tr>
                                                <td class="align-middle text-center fw-bold">
                                                    {{ $sub->kode }}
                                                </td>
                                                <td class="align-middle text-wrap" style="white-space: normal;">
                                                    {!! nl2br(e(wordwrap($sub->uraian ?? '-', 45, "\n", true))) !!}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="{{ 6 + (in_array($userOtoritas, ['Wakil Rektor', 'Wakil Dekan']) ? 1 : 0) + (in_array($userOtoritas, ['Wakil Rektor']) ? 1 : 0) + ($userOtoritas == 'Dosen' ? 1 : 0) }}"
                                    class="text-center text-muted py-4">
                                    Data CPMK tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($cpmks->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
                    <div class="mb-2">
                        <small class="text-muted">
                            Page {{ $cpmks->currentPage() }} of {{ $cpmks->lastPage() }}
                        </small>
                    </div>

                    <div class="mb-2">
                        <ul class="pagination mb-0">
                            <li class="page-item {{ $cpmks->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $cpmks->previousPageUrl() ?? '#' }}">
                                    Previous
                                </a>
                            </li>

                            <li class="page-item disabled">
                                <span class="page-link">
                                    {{ $cpmks->currentPage() }}
                                </span>
                            </li>

                            <li class="page-item {{ $cpmks->hasMorePages() ? '' : 'disabled' }}">
                                <a class="page-link" href="{{ $cpmks->nextPageUrl() ?? '#' }}">
                                    Next
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/dynamic-filters.js') }}"></script>
@endsection