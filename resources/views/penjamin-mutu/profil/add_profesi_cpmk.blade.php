{{-- @php
    $routePrefix = [
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp --}}

@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')
    @if (session()->has('failed'))
        <div class="alert alert-danger" role="alert" id="box">
            <div>{{ session('failed') }}</div>
        </div>
    @elseif (session()->has('success'))
        <div class="alert greenAdd" role="alert" id="box">

        </div>
    @endif

    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Pemetaan Profesi ke CPMK</h4>

                <form action="{{ route($currentPrefix . 'profesi-cpmk-store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Pilih CPMK</label>
                        <select class="form-control" name="cpmk_id" onchange="window.location.href='?cpmk_id=' + this.value"
                            required>
                            <option disabled selected>Pilih CPMK</option>
                            @foreach ($cpmks as $cpmk)
                                <option value="{{ $cpmk->id }}" {{ request('cpmk_id') == $cpmk->id ? 'selected' : '' }}>
                                    {{ $cpmk->kode }} - {{ $cpmk->judul }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mt-3">

                        {{-- Sudah dipetakan --}}
                        @if (!empty($selected))
                            <div class="mb-3">
                                <label class="text-success">Sudah dipetakan</label>
                                <div class="border p-3 bg-light">
                                    @foreach ($profesis->whereIn('id', $selected) as $profesi)
                                        <div class="form-check">
                                            <input type="checkbox" checked disabled>
                                            <label class="text-muted">{{ $profesi->nama }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Tambah baru --}}
                        <label>Tambah Profesi Baru</label>
                        <div class="border p-5" style="max-height: 200px; overflow-y: auto;">
                            @foreach ($profesis->whereNotIn('id', $selected ?? []) as $profesi)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="profesis[]"
                                        value="{{ $profesi->id }}">
                                    <label>{{ $profesi->nama }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>



                    <div class="mt-3 d-flex gap-2">
                        <a href="{{ route($currentPrefix . 'indexPemetaanCPMKProf') }}" class="btn btn-secondary">
                            Kembali
                        </a>

                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $('.cplId-checkbox').change(function() {
            const isChecked = $(this).is(':checked');
            $(this).closest('.form-check').find('.bobot').prop('disabled', !isChecked).val('');
        });
    </script>
@endsection