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

    $routePrefix = [
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
        'Penjamin Mutu Universitas' => ['prefix' => 'penjamin-mutu.universitas.'],
        'Penjamin Mutu Fakultas' => ['prefix' => 'penjamin-mutu.fakultas.'],
        'Dosen' => ['prefix' => 'dosen.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp

@extends(auth()->user()->otoritas->otoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')

<style>
    @if ($isAptikom)
        .btn-primary {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
            border-color: #0284c7 !important;
            color: #ffffff !important;
        }
        .btn-primary:hover, .btn-primary:focus {
            background: #0369a1 !important;
            border-color: #0369a1 !important;
        }
    @else
        .btn-primary {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            border-color: #d97706 !important;
            color: #ffffff !important;
        }
        .btn-primary:hover, .btn-primary:focus {
            background: #b45309 !important;
            border-color: #b45309 !important;
        }
    @endif
</style>

<div class="container my-4">
    <div class="card shadow-sm rounded-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="card-title fw-bold mb-0">Tambah Pemetaan Mata Kuliah ke Sub CPMK</h4>
                <a href="{{ route($currentPrefix . 'cpl-cpmk.mk-cpmk-subcpmk') }}" class="btn btn-outline-secondary rounded-3">
                    <i class="ti-arrow-left me-1"></i> Kembali ke List Pemetaan
                </a>
            </div>

            <form action="{{ route($currentPrefix . 'cpl-cpmk.cpmk-mk-subcpmk-store') }}" method="POST">
                @csrf
                <div class="form-group mb-4">
                    <label for="mk_kode" class="fw-bold mb-2">Pilih Mata Kuliah:</label>
                    <select class="form-select form-control" id="mk_kode" name="mk_kode" required >
                        <option value="" selected disabled>-- Pilih Mata Kuliah --</option>
                        @foreach ($mks as $mk)
                            <option value="{{ $mk->kode }}">{{ $mk->kode }} - {{ $mk->nama }}</option>  
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label for="sub_cpmk_id" class="fw-bold mb-2">Pilih Sub CPMK:</label>
                    <div id="sub_cpmk_id" class="border rounded-3 p-3 bg-light" style="max-height: 250px; overflow-y: auto;">
                        <span class="text-muted small italic">Silakan pilih Mata Kuliah terlebih dahulu.</span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-primary px-4 rounded-3"><i class="ti-save me-1"></i> Simpan Pemetaan</button>
                    <a href="{{ route($currentPrefix . 'cpl-cpmk.mk-cpmk-subcpmk') }}" class="btn btn-light rounded-3 px-3">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        function getSelectedValue() {
            var mkKode = document.getElementById("mk_kode").value;
            if (mkKode) {
                var baseUrl = "{{ route($currentPrefix . 'cpl-cpmk.sub-cpmk-by-mk', ['mk' => ':mkKode']) }}";
                var urlget = baseUrl.replace(':mkKode', encodeURIComponent(mkKode));

                $('#sub_cpmk_id').html('<span class="text-muted small">Memuat data Sub CPMK...</span>');

                $.ajax({
                    url: urlget,
                    method: 'GET',
                    success: function(data) {
                        let subCpmkOptions = '';
                        if (data.subcpmks && data.subcpmks.length > 0) {
                            data.subcpmks.forEach(subcpmk => {
                                subCpmkOptions += `<div class="form-check mb-2 p-2 border rounded bg-white">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="sub_cpmk_ids[]" value="${subcpmk.id}" id="sub_cpmk_${subcpmk.id}">
                                    <label class="form-check-label small cursor-pointer" for="sub_cpmk_${subcpmk.id}">
                                        <strong>${subcpmk.kode}</strong> - ${subcpmk.uraian}
                                    </label>
                                </div>`;
                            });
                        } else {
                            subCpmkOptions = `<div class="text-muted small p-2">Tidak ada data Sub CPMK untuk Mata Kuliah ini.</div>`;
                        }
                        $('#sub_cpmk_id').html(subCpmkOptions);
                    },
                    error: function(xhr, status, error) {
                        $('#sub_cpmk_id').html('<div class="text-danger small p-2">Gagal memuat data Sub CPMK.</div>');
                    }
                });
            } else {
                $('#sub_cpmk_id').html('<span class="text-muted small italic">Silakan pilih Mata Kuliah terlebih dahulu.</span>');
            }
        }

        var mkElement = document.getElementById("mk_kode");
        if (mkElement) {
            mkElement.addEventListener("change", getSelectedValue);
        }
    });
</script>

@endsection