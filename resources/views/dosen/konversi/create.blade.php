@extends('dosen.template')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <a href="{{ route('dosen.konversi-nilai.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                        <i class="mdi mdi-arrow-left me-1"></i> Kembali
                    </a>
                    <h4 class="card-title mb-0">Setup Konversi Nilai</h4>
                </div>
                <p class="text-muted small mb-4">Pilih Mata Kuliah, Tahun Ajaran, dan Kurikulum yang sesuai.</p>

                <form action="{{ route('dosen.konversi-nilai.store-setup') }}" method="POST">
                    @csrf

                    <!-- 1. Select Kurikulum -->
                    <div class="mb-3">
                        <label for="kurikulum_id" class="form-label fw-semibold">Kurikulum <span class="text-danger">*</span></label>
                        <select name="kurikulum_id" id="kurikulum_id" class="form-control" required>
                            @if ($kurikulums->isEmpty())
                                <option value="" disabled selected>-- Tidak ada data --</option>
                            @else
                                <option value="" disabled selected>-- Pilih Kurikulum --</option>
                                @foreach ($kurikulums as $k)
                                    <option value="{{ $k->id }}" {{ old('kurikulum_id') == $k->id ? 'selected' : '' }}>
                                        Tahun {{ $k->tahun }} - {{ $k->nama ?? 'Kurikulum' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- 2. Select Mata Kuliah -->
                    <div class="mb-3">
                        <label for="mk_kode" class="form-label fw-semibold">Mata Kuliah <span class="text-danger">*</span></label>
                        <select name="mk_kode" id="mk_kode" class="form-control" required disabled>
                            <option value="" disabled selected>-- Pilih Mata Kuliah --</option>
                        </select>
                        <small id="mk_help_text" class="text-muted d-block mt-1">Silakan pilih Kurikulum untuk menampilkan daftar mata kuliah yang tersedia.</small>
                    </div>

                    <!-- 3. Select Tahun Ajaran -->
                    <div class="mb-4">
                        <label for="tahun_ajaran_id" class="form-label fw-semibold">Tahun Ajaran <span class="text-danger">*</span></label>
                        <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="form-control" required>
                            <option value="" disabled selected>-- Pilih Tahun Ajaran  --</option>
                            @foreach ($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ old('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->tahun }}/{{ (int)$ta->tahun + 1 }} - {{ $ta->jenis_semester }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('dosen.konversi-nilai.index') }}" class="btn btn-secondary btn-sm">Batal</a>
                        <button type="submit" class="btn btn-primary btn-sm px-4">
                            Simpan <i class="mdi mdi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const $kurikulumSelect = $('#kurikulum_id');
    const $mkSelect = $('#mk_kode');
    const $mkHelpText = $('#mk_help_text');

    function loadMks(kurikulumId, selectedMkKode = null) {
        if (!kurikulumId) {
            $mkSelect.html('<option value="" disabled selected>-- Pilih Kurikulum Dulu --</option>').prop('disabled', true);
            $mkHelpText.text('Silakan pilih Kurikulum untuk menampilkan daftar mata kuliah yang tersedia.').addClass('text-muted').removeClass('text-danger');
            return;
        }

        $mkSelect.html('<option value="" disabled selected>Mengambil data mata kuliah...</option>').prop('disabled', true);
        
        let url = "{{ route('dosen.konversi-nilai.get-mks-by-kurikulum', ':id') }}".replace(':id', kurikulumId);

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response && response.length > 0) {
                    let options = '<option value="" disabled selected>-- Pilih Mata Kuliah --</option>';
                    response.forEach(function(mk) {
                        let isSelected = selectedMkKode === mk.kode ? 'selected' : '';
                        options += `<option value="${mk.kode}" ${isSelected}>${mk.kode} - ${mk.nama} (SKS: ${mk.total_sks})</option>`;
                    });
                    $mkSelect.html(options).prop('disabled', false);
                    $mkHelpText.text('Ditemukan ' + response.length + ' mata kuliah pada kurikulum ini.').removeClass('text-danger').addClass('text-muted');
                } else {
                    $mkSelect.html('<option value="" disabled selected>-- Tidak Ada Data Mata Kuliah --</option>').prop('disabled', true);
                    $mkHelpText.text('Tidak ada mata kuliah yang terdaftar pada kurikulum ini.').removeClass('text-danger').addClass('text-muted');
                }
            },
            error: function() {
                $mkSelect.html('<option value="" disabled selected>-- Tidak Ada Data Mata Kuliah --</option>').prop('disabled', true);
                $mkHelpText.text('Tidak ada mata kuliah yang terdaftar pada kurikulum ini.').removeClass('text-danger').addClass('text-muted');
            }
        });
    }

    $kurikulumSelect.on('change', function() {
        loadMks($(this).val());
    });

    // Handle old values if validation returns
    let initialKurikulum = $kurikulumSelect.val();
    let oldMkKode = "{{ old('mk_kode') }}";
    if (initialKurikulum) {
        loadMks(initialKurikulum, oldMkKode);
    }
});
</script>
@endsection
