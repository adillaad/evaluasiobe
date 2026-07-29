document.addEventListener('DOMContentLoaded', function() {
    const universitasSelect = $('#universitas_id');
    const fakultasSelect = $('#fakultas_id');
    const prodiSelect = $('#prodi_id');
    const kurikulumSelect = $('#kurikulum_id');

    const userRole = document.body.dataset.userRole || '';

    // 1. DEFINISIKAN GRUP PERAN
    const universityLevelRoles = ['Admin Universitas', 'Penjamin Mutu Universitas', 'Wakil Rektor'];
    const facultyLevelRoles = ['Wakil Dekan', 'Penjamin Mutu Fakultas'];
    const prodiLevelRoles = ['Kepala Program Studi', 'Penjamin Mutu Program Studi', 'Dosen'];

    // 2. INISIALISASI SELECT2 (Tidak berubah)
    if (universitasSelect.length) {
        universitasSelect.select2({ placeholder: 'Pilih Universitas', allowClear: true });
    }
    if (fakultasSelect.length) {
        fakultasSelect.select2({ placeholder: 'Pilih Fakultas', allowClear: true });
    }
    if (prodiSelect.length) {
        prodiSelect.select2({ placeholder: 'Pilih Program Studi', allowClear: true });
    }
    if (kurikulumSelect.length) {
        kurikulumSelect.select2({ placeholder: 'Pilih Kurikulum', allowClear: true });
    }

    // 3. LOGIKA UNTUK MENAMPILKAN/MENYEMBUNYIKAN FILTER
    function initializeFilterState() {
        // Cek apakah peran user ada di dalam grup yang sudah didefinisikan
        if (universityLevelRoles.includes(userRole)) {
            universitasSelect.closest('.col-md-4').hide();
            fakultasSelect.prop('disabled', false);
            prodiSelect.prop('disabled', !fakultasSelect.val());

        } else if (facultyLevelRoles.includes(userRole)) {
            universitasSelect.closest('.col-md-4').hide();
            fakultasSelect.closest('.col-md-4').hide();
            prodiSelect.prop('disabled', false);

        } else if (prodiLevelRoles.includes(userRole)) {
            universitasSelect.closest('.col-md-4').hide();
            fakultasSelect.closest('.col-md-4').hide();
            prodiSelect.closest('.col-md-4').hide();

        } else { // Untuk Super Admin ('Admin')
            fakultasSelect.prop('disabled', !universitasSelect.val());
            prodiSelect.prop('disabled', !fakultasSelect.val());
        }
    }

    initializeFilterState();

    // 4. EVENT LISTENER
    universitasSelect.on('change', function() {
        const universitasId = $(this).val();
        fakultasSelect.val(null).empty().append('<option value="">Pilih Fakultas</option>').trigger('change');
        prodiSelect.val(null).empty().append('<option value="">Pilih Program Studi</option>').trigger('change');
        if (universitasId) {
            fakultasSelect.prop('disabled', false);
            $.ajax({
                url: `/get-faculties/${universitasId}`,
                method: 'GET',
                dataType: 'json',
                success: function(faculties) {
                    faculties.forEach(function(faculty) {
                        const newOption = new Option(faculty.nama, faculty.id, false, false);
                        fakultasSelect.append(newOption);
                    });
                    fakultasSelect.trigger('change.select2');
                }
            });
        } else {
            fakultasSelect.prop('disabled', true);
            prodiSelect.prop('disabled', true);
        }
    });

    fakultasSelect.on('change', function() {
        const fakultasId = $(this).val();
        prodiSelect.val(null).empty().append('<option value="">Pilih Program Studi</option>').trigger('change');
        if (fakultasId) {
            prodiSelect.prop('disabled', false);
            $.ajax({
                url: `/get-programs/${fakultasId}`,
                method: 'GET',
                dataType: 'json',
                success: function(programs) {
                    programs.forEach(function(program) {
                        const newOption = new Option(program.nama, program.id, false, false);
                        prodiSelect.append(newOption);
                    });
                    prodiSelect.trigger('change.select2');
                }
            });
        } else {
            prodiSelect.prop('disabled', true);
        }
    });
});