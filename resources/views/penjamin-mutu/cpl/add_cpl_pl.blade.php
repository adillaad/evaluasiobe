@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')
@if (session()->has('failed'))
    <div class="alert alert-danger" role="alert" id="box">
        <div>{{ session('failed') }}</div>
    </div>
@elseif (session()->has('success'))
    <div class="alert alert-success" role="alert" id="box">
        <div>{{ session('success') }}</div>
    </div>
@endif

<h3 class="px-4 pb-4 fw-bold text-center">Halaman Tambah Pemetaan CPL-PL</h3>

<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Tambah Pemetaan Profil Lulusan ke CPL</h4>

            <form action="{{ route($currentPrefix . 'cpl.cpl-pl-store') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label for="profil_lulusan_id">Pilih Profil Lulusan:</label>
                    <select class="form-control" id="profil_lulusan_id" name="profil_lulusan_id" required>
                        <option value="" selected disabled>Pilih Profil Lulusan</option>
                        @foreach ($profilLulusans as $profilLulusan)
                            <option value="{{ $profilLulusan->id }}" {{ old('profil_lulusan_id') == $profilLulusan->id ? 'selected' : '' }}>
                                {{ $profilLulusan->kode }} - {{ $profilLulusan->namaProfil }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="cpl_ids">Pilih CPL yang Terkait:</label>
                    <div class="border ps-5" style="max-height: 200px; overflow-y: auto;">
                        @foreach ($cpls as $cpl)
                            <div class="form-check d-flex align-items-center mb-2">
                                <input class="form-check-input cplId-checkbox" type="checkbox" name="cpl_ids[]"
                                    value="{{ $cpl->id }}" id="cpl_{{ $cpl->id }}"
                                    {{ in_array($cpl->id, old('cpl_ids', [])) ? 'checked' : '' }}>

                                <label class="form-check-label" style="width: 600px;" for="cpl_{{ $cpl->id }}">
                                    {{ $cpl->kode }} - {{ $cpl->judul }}
                                </label>
                                <input class="form-check-input bobot ms-4" type="number" name="bobot[{{ $cpl->id }}]"
                                    placeholder="Bobot" step="0.01" min="0"
                                    value="{{ old('bobot.' . $cpl->id) }}"
                                    {{ in_array($cpl->id, old('cpl_ids', [])) ? '' : 'disabled' }}
                                    style="width: 100px; height:40px">
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route($currentPrefix . 'cpl.cpl-pl') }}" class="btn btn-light">Kembali</a>
            </form>
        </div>
    </div>
</div>

<script>
    $('.cplId-checkbox').change(function() {
        const isChecked = $(this).is(':checked');
        const bobotInput = $(this).closest('.form-check').find('.bobot');
        bobotInput.prop('disabled', !isChecked);
        if (!isChecked) {
            bobotInput.val('');
        }
    });
</script>

@endsection
