@php
    $currentPrefix = auth()->user()->otoritas->otoritas
        ? str_replace(' ', '-', strtolower(auth()->user()->otoritas->otoritas)) . '.'
        : 'admin.';
@endphp
@extends('dosen.template')
@section('content')
    @if(session('error_list'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>RPS {{ session('rps_mk') ?? '-' }} belum bisa diajukan.</strong>
            <ul class="mb-0 mt-2">
                @foreach(session('error_list') as $msg)
                    <li>{{ $msg }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif 
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar RPS</h4>
                <!--<x-filter-form-->
                <!--    :universities="$universities"-->
                <!--    :faculties="$faculties"-->
                <!--    :programs="$programs"-->
                <!--/>-->
                @if ($userOtoritas == 'Dosen') 
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary btn-icon-text" data-bs-toggle="modal" data-bs-target="#addRpsModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            <span>Tambah RPS</span>
                        </button>
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-hover dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Mata Kuliah</th>
                                <th>Versi</th>
                                <th>Status</th>
                                <th>Tgl. Penyusunan</th> 
                                <th>Semester</th>
                                <th>Pengembang RPS</th>
                                <!-- <th>Koordinator RMK</th> -->
                                @if(in_array($userOtoritas, ['Wakil Rektor', 'Wakil Dekan']))
                                    <th>Prodi</th>
                                @endif
                                @if(in_array($userOtoritas, ['Wakil Rektor']))
                                    <th>Fakultas</th>
                                @endif
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rpss as $rps)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $rps->mk?->nama ?? $rps->kode_mk ?? '-' }}</td>
                                    <td>{{ $rps->versi }}</td>
                                    <td>
                                        @if ($rps->status == 'published')
                                            {{-- Published (Hijau) --}}
                                            <span class="badge badge-success">Published</span>
                                        @elseif ($rps->status == 'rejected')
                                            {{-- Rejected (Merah) --}}
                                            <span class="badge badge-danger" style="cursor:pointer;"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Alasan Ditolak: {{ $rps->latestValidation->catatan ?? 'Tidak ada catatan.' }}">
                                                Rejected
                                            </span>
                                        @elseif ($rps->status == 'pending')
                                            {{-- Pending (Biru Muda) --}}
                                            <span class="badge badge-info">Pending</span>
                                        @else
                                            {{-- Draft (Kuning) --}}
                                            <span class="badge badge-warning">Draft</span>
                                        @endif
                                    </td>
                                    <td>{{ date('d-m-Y', strtotime($rps->created_at)) }}</td> 
                                    <td>{{ $rps->semester }}</td>
                                    <td>{{ $rps->pengembang ?? '-' }}</td>
                                    <!-- <td>{{ $rps->koordinator ?? '-' }}</td> -->
                                    @if(in_array($userOtoritas, ['Wakil Rektor', 'Wakil Dekan']))
                                        <td>{{ $rps->prodi?->nama ?? '-' }}</td>
                                    @endif
                                    @if(in_array($userOtoritas, ['Wakil Rektor']))
                                        <td>{{ $rps->prodi?->fakultas?->nama ?? '-' }}</td>
                                    @endif
                                    <td class="py-2">
                                        <div class="d-flex justify-content-center align-items-center" style="gap: 4px;">
                                            {{-- Tombol Cetak --}}
                                            <a href="{{ route($currentPrefix . 'rps-print', encrypt($rps->id)) }}" 
                                                class="btn btn-icons btn-info" 
                                                data-bs-toggle="tooltip" data-bs-placement="top" 
                                                title="Cetak RPS">
                                                <i class="ti-printer"></i>
                                            </a>

                                            @if ($userOtoritas == 'Dosen')
                                                @if($rps->status == 'draft' || $rps->status == 'rejected')
                                                    <form action="{{ route('dosen.rps-submit-validation', $rps->id) }}" method="post" class="d-inline m-0 p-0" onsubmit="return confirm('Ajukan RPS ini untuk divalidasi?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-icons btn-primary" 
                                                            data-bs-toggle="tooltip" data-bs-placement="top" 
                                                            title="Ajukan Validasi">
                                                            <i class="ti-upload"></i>
                                                        </button>
                                                    </form>
                                                    <a href="{{ route($currentPrefix . 'rps-detail', $rps->id) }}" 
                                                        class="btn btn-icons btn-success" 
                                                        data-bs-toggle="tooltip" data-bs-placement="top" 
                                                        title="Lihat Detail RPS">
                                                        <i class="ti-eye"></i>
                                                    </a>
                                                    {{-- Tombol Edit --}}
                                                    <a href="#" role="button" class="btn btn-icons btn-warning" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editRpsModal{{ $rps->id }}" 
                                                        data-bs-placement="top" 
                                                        title="Edit RPS">
                                                        <i class="ti-pencil"></i>
                                                    </a>

                                                    {{-- Tombol Hapus --}}
                                                    <form action="/dosen/rps/delete-rps/{{ $rps->id }}" method="post" class="d-inline m-0 p-0" onsubmit="return confirm('Yakin ingin menghapus RPS no. {{ $rps->nomor }}?')">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="btn btn-icons btn-danger" 
                                                            data-bs-toggle="tooltip" data-bs-placement="top" 
                                                            title="Hapus RPS">
                                                            <i class="ti-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @if ($userOtoritas == 'Dosen')
    <div class="modal fade" id="addRpsModal" tabindex="-1" aria-labelledby="addRpsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable"> {{-- Dibuat besar dan bisa di-scroll --}}
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRpsModalLabel">Tambah RPS Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Form lengkap akan dimuat dari file terpisah --}}
                    @include('dosen.RPS.partials.form_modal')
                </div>
            </div>
        </div>
    </div>
    @endif

    @if ($userOtoritas == 'Dosen')
    @foreach($rpss as $rps)
        <div class="modal fade" id="editRpsModal{{ $rps->id }}" tabindex="-1" aria-labelledby="editRpsModalLabel{{ $rps->id }}" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editRpsModalLabel{{ $rps->id }}">Edit RPS: {{ $rps->mk?->nama ?? $rps->kode_mk ?? '-' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Form edit akan dimuat dari file terpisah --}}
                        {{-- Kita passing variabel $rps, $mks, dan $users ke dalam form --}}
                        @include('dosen.RPS.partials.form_edit_modal', [
                            'rps' => $rps,
                            'mks' => $mks,
                            'users' => $users
                        ])
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @endif

@push('scripts')
<script>
$(document).ready(function() {
    
    // --- Selector ---
    const addModalSelector = "#addRpsModal"; 
    const mkDropdownSelector = '#addRpsModal select[name="matakuliah"]'; 
    const pustakaContainerSelector = '#addRpsModal #dynamic-pustaka-container'; 
    const pustakaDropdownClass = '.pustaka-dropdown'; 
    const addPustakaBtnSelector = '#addRpsModal #add-pustaka-btn'; 
    const pustakaItemClass = '.pustaka-item';
    const removePustakaBtnClass = '.remove-pustaka-btn';
    const newPustakaFormClass = '.pustaka-baru-form';
    // --- END Selector ---

    let pustakaCounter = 1; 

    // Fungsi loadPustakaOptions (AJAX)
    function loadPustakaOptions(kode_mk, callback) { 
        const pustakaDropdowns = $(pustakaContainerSelector).find(pustakaDropdownClass);

        // 1. Reset & Disable Dropdowns
        pustakaDropdowns.each(function() {
            const currentSelect = $(this);
            currentSelect.data('prev-value', currentSelect.val()); 
            currentSelect.find('option').not('[value=""]', '[value="tambah_baru"]').remove(); 
            currentSelect.prop('disabled', true).val(''); 
            currentSelect.closest(pustakaItemClass).find(newPustakaFormClass).hide()
                       .find('input').prop('required', false);
        });

        const firstPlaceholderOption = $(pustakaContainerSelector).find(pustakaDropdownClass + ':first option[value=""]');

        if (!kode_mk) {
            firstPlaceholderOption.text('-- Pilih Mata Kuliah Dulu --'); 
            if (callback) callback(); 
            return; 
        }
        firstPlaceholderOption.text('-- Loading Pustaka... --'); 

        // 2. Panggil AJAX
        $.ajax({
            url: `{{ route('dosen.get-pustaka-by-mk', ['kode_mk' => ':kode_mk']) }}`.replace(':kode_mk', kode_mk),
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // 3. Isi kembali semua dropdown
                pustakaDropdowns.each(function() {
                    const currentSelect = $(this);
                    const tambahBaruOption = currentSelect.find('option[value="tambah_baru"]');
                    const previousValue = currentSelect.data('prev-value'); 
                    const placeholderOption = currentSelect.find('option[value=""]');

                    if (!tambahBaruOption.length) {
                        console.error("Opsi 'Tambah Baru' tidak ditemukan!"); 
                        // Coba tambahkan manual sebagai fallback
                        currentSelect.append('<option value="tambah_baru" class="text-primary fw-bold">-- Tambah Pustaka Baru --</option>');
                        tambahBaruOption = currentSelect.find('option[value="tambah_baru"]'); 
                         if(!tambahBaruOption.length) return; // Skip jika masih gagal
                    }

                    // Tambahkan opsi baru DARI AJAX SEBELUM opsi "Tambah Baru"
                    if (data && data.length > 0) {
                        $.each(data, function(index, pustaka) {
                            // --- FORMAT TEKS BARU ---
                            const optionText = `${pustaka.kode_pustaka} ${pustaka.judul}, ${pustaka.penulis || ''}. ${pustaka.penerbit || ''}. ${pustaka.tahun || ''}`;
                            const newOptionElement = $('<option>', { 
                                value: pustaka.id, 
                                text: optionText 
                            });
                            tambahBaruOption.before(newOptionElement); 
                        });
                        placeholderOption.text('-- Pilih Pustaka --'); 
                    } else {
                        placeholderOption.text('-- Belum Ada Pustaka Untuk MK Ini --'); 
                    }

                    // Pilih kembali value lama
                    if (previousValue && currentSelect.find(`option[value="${previousValue}"]`).length > 0) {
                        currentSelect.val(previousValue);
                    } else {
                        currentSelect.val(''); 
                    }
                    
                    currentSelect.prop('disabled', false); // Aktifkan
                    currentSelect.trigger('change'); // Trigger change
                });
                if (callback) callback(); 
            },
            error: function(jqXHR, textStatus, errorThrown) {
                 console.error("AJAX Error:", textStatus, errorThrown);
                 alert('Gagal mengambil data pustaka.');
                 pustakaDropdowns.prop('disabled', false).find('option[value=""]').text('-- Error Loading --');
                 if (callback) callback(); 
            }
        });
    }

    // --- EVENT LISTENERS ---

    // 1. Saat dropdown Mata Kuliah berubah
    $('body').on('change', mkDropdownSelector, function() {
        loadPustakaOptions($(this).val());
    });

    // 2. Saat modal Tambah dibuka
    $(addModalSelector).on('shown.bs.modal', function () {
         const initialMkKode = $(mkDropdownSelector).val();
         // Reset tampilan pustaka
         pustakaCounter = 1; 
         const firstPustakaItem = $(pustakaContainerSelector).find(pustakaItemClass + ':first');
         $(pustakaContainerSelector).find(pustakaItemClass).not(':first').remove(); 
         firstPustakaItem.find(pustakaDropdownClass).val(''); 
         firstPustakaItem.find(removePustakaBtnClass).hide(); 
         firstPustakaItem.find(newPustakaFormClass).hide().find('input').val('').prop('required', false); 
         
         // Load pustaka atau set placeholder
         if (initialMkKode) {
            loadPustakaOptions(initialMkKode);
         } else {
            const firstPustakaDropdown = firstPustakaItem.find(pustakaDropdownClass);
            firstPustakaDropdown.prop('disabled', true)
                .find('option').not('[value=""]', '[value="tambah_baru"]').remove();
            firstPustakaDropdown.find('option[value=""]').text('-- Pilih Mata Kuliah Dulu --');
         }
    });
    
    // 3. Tombol Tambah Baris Pustaka (+) diklik
    $(pustakaContainerSelector).on('click', '#add-pustaka-btn', function() { 
        const firstItem = $(pustakaContainerSelector).find(pustakaItemClass + ':first');
        if (!firstItem.length) return; 
        const newItem = firstItem.clone(); 
        const newDropdown = newItem.find(pustakaDropdownClass);

        newDropdown.val(''); 
        newDropdown.prop('disabled', $(mkDropdownSelector).val() ? false : true); 
        newItem.find(newPustakaFormClass).hide().find('input').val('').prop('required', false);
        
        // Update index name
        newItem.find('select, input').each(function() {
            let currentName = $(this).attr('name');
            if(currentName) {
                 $(this).attr('name', currentName.replace(/\[\d+\]/, `[${pustakaCounter}]`));
            }
        });

        newItem.find(removePustakaBtnClass).show(); 
        $(pustakaContainerSelector).find(pustakaItemClass + ':last').after(newItem); // Sisipkan setelah yg terakhir
        firstItem.find(removePustakaBtnClass).show(); 
        pustakaCounter++; 
    });

    // 4. Tombol Hapus Baris Pustaka (-) diklik
    $(pustakaContainerSelector).on('click', removePustakaBtnClass, function() {
        $(this).closest(pustakaItemClass).remove(); 
        if ($(pustakaContainerSelector).find(pustakaItemClass).length === 1) {
            $(pustakaContainerSelector).find(removePustakaBtnClass).hide();
        }
    });

    // 5. Saat pilihan dropdown Pustaka Utama berubah
    $(pustakaContainerSelector).on('change', pustakaDropdownClass, function() {
        const selectedValue = $(this).val();
        const newPustakaForm = $(this).closest(pustakaItemClass).find(newPustakaFormClass); 
        const formInputs = newPustakaForm.find('input');

        if (selectedValue === 'tambah_baru') {
            newPustakaForm.slideDown(200); 
            formInputs.prop('required', true); 
        } else {
            newPustakaForm.slideUp(200); 
            formInputs.prop('required', false).val(''); 
        }
    }); 
});
</script>
@endpush

    <script src="{{ asset('assets/js/dynamic-filters.js') }}"></script>
@endsection
