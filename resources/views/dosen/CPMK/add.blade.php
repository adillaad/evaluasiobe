@extends('dosen.template')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-header py-3">
            <h4 class="mb-0 fw-bold">Tambah CPMK & Sub-CPMK</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('dosen.cpmk-store') }}" method="post"> 
            @csrf
            
            {{-- BAGIAN 1: INFORMASI CPMK --}}
            <h5 class="border-bottom pb-2 mb-3"><i class="ti-files me-2"></i>Informasi CPMK</h5>
            
            <div class="row">
                {{-- 1. PILIH CPL --}} 
                <div class="col-md-12 mb-3">
                    <label for="cpl" class="form-label">
                        Pilih CPL Prodi <span class="text-danger">*</span>
                    </label>

                    <select id="cpl" name="cpl" class="form-select form-select-sm" required>
                        <option selected disabled value="">Pilih CPL...</option>

                        @foreach ($cpls as $cpl)
                            <option value="{{ $cpl->id }}" {{ old('cpl') == $cpl->id ? 'selected' : '' }}>
                                {{ $cpl->tahun_kurikulum }} – {{ $cpl->kode }} – {{ $cpl->judul }}
                            </option>
                        @endforeach
                    </select> 
                </div>

                {{-- 2. PILIH CPMK (Dropdown) --}}
                <div class="col-md-12 mb-3">
                    <label class="form-label">Pilih CPMK / Buat Baru <span class="text-danger">*</span></label>
                    <select id="cpmk_select" name="cpmk_selection" class="form-select form-select-sm" required disabled>
                        <option selected disabled value="">-- Pilih CPL Terlebih Dahulu --</option>
                    </select>

                    {{-- 3. INPUT JUDUL BARU (Hanya muncul jika pilih 'Tambah Baru') --}}
                    {{-- Label dihilangkan, langsung Textarea saja --}}
                    <div id="container-judul-baru" style="display: none;">
                        <textarea name="judul_cpmk_baru" id="judul_cpmk_baru" 
                            class="form-control form-control-sm mt-2 border-primary" 
                            rows="2" 
                            placeholder="Contoh: Mampu merancang database..."></textarea>
                    </div>
                </div>
            </div>

            {{-- BAGIAN 2: RINCIAN SUB-CPMK --}}
            <div class="d-flex justify-content-between align-items-center mt-4 border-bottom pb-2 mb-3">
                <h5 class="mb-0"><i class="ti-list me-2"></i>Rincian Sub-CPMK</h5>
                {{-- Tombol Toggle Panduan (UX: Memberi bantuan tanpa mengganggu layout) --}}
                <button class="btn btn-sm btn-info text-white" type="button" data-bs-toggle="collapse" data-bs-target="#guideKKO" aria-expanded="false">
                    <i class="bi bi-lightbulb-fill me-1"></i> Panduan & KKO Bloom
                </button>
            </div>

            {{-- AREA PANDUAN (Hidden by default) --}}
            <div class="collapse mb-4" id="guideKKO">
                <div class="card card-body bg-light border-info shadow-sm">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <h6 class="fw-bold text-info"><i class="bi bi-book-half"></i> Referensi KKO Taksonomi Bloom (Lengkap)</h6>
                            <p class="small text-muted mb-0">
                                Klik pada kata kerja di bawah untuk menyalinnya.
                                Pastikan memilih level kognitif, afektif, dan psikomotor yang sesuai dengan capaian pembelajaran.
                                <br>
                                <a href="https://drive.google.com/file/d/1DNSqzOFUeP-sU8JxLmmDRswK4Kl-0cqO/view?usp=sharing"
                                target="_blank"
                                class="text-decoration-underline fw-semibold">
                                    Lihat Panduan Blooms-for-Computing
                                </a>
                            </p>
                        </div>
                        
                        {{-- MAIN TABS (DOMAIN) --}}
                        <div class="col-md-12">
                            <ul class="nav nav-pills nav-fill small mb-3" id="domainTab" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active fw-bold" id="tab-cognitive" data-bs-toggle="pill" data-bs-target="#content-cognitive" type="button">
                                        <i class="bi bi-brain me-1"></i> COGNITIVE (Kognitif)
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link fw-bold" id="tab-affective" data-bs-toggle="pill" data-bs-target="#content-affective" type="button">
                                        <i class="bi bi-heart me-1"></i> AFFECTIVE (Afektif)
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link fw-bold" id="tab-psychomotor" data-bs-toggle="pill" data-bs-target="#content-psychomotor" type="button">
                                        <i class="bi bi-hand-index-thumb me-1"></i> PSYCHOMOTOR (Psikomotor)
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="domainTabContent">
                                
                                {{-- CONTENT: COGNITIVE --}}
                                <div class="tab-pane fade show active" id="content-cognitive">
                                    <ul class="nav nav-tabs nav-fill small" id="cognitiveTab" role="tablist">
                                        @foreach([
                                            'C1' => ['label' => 'Mengingat', 'color' => 'success'],
                                            'C2' => ['label' => 'Memahami', 'color' => 'warning'],
                                            'C3' => ['label' => 'Menerapkan', 'color' => 'primary'],
                                            'C4' => ['label' => 'Menganalisis', 'color' => 'secondary'],
                                            'C5' => ['label' => 'Mengevaluasi', 'color' => 'danger'],
                                            'C6' => ['label' => 'Menciptakan', 'color' => 'dark']
                                        ] as $code => $attr)
                                            <li class="nav-item">
                                                <button class="nav-link {{ $code == 'C1' ? 'active' : '' }} fw-bold text-{{ $attr['color'] }}" 
                                                    data-bs-toggle="tab" data-bs-target="#{{ strtolower($code) }}">
                                                    {{ $code }} - {{ $attr['label'] }}
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="tab-content p-3 border border-top-0 bg-white" style="max-height: 300px; overflow-y: auto;">
                                        @php
                                            $cognitiveData = [
                                                'C1' => ['Menemukenali (identifikasi)', 'Mengingat kembali', 'Membaca', 'Menyebutkan', 'Melafalkan', 'Menuliskan', 'Menghafal', 'Menyusun daftar', 'Menggarisbawahi', 'Menjodohkan', 'Memilih', 'Memberi definisi', 'Menyatakan'],
                                                'C2' => ['Menjelaskan', 'Mengartikan', 'Menginterpretasikan', 'Menceritakan', 'Menampilkan', 'Memberi contoh', 'Merangkum', 'Menyimpulkan', 'Membandingkan', 'Mengklasifikasikan', 'Menunjukkan', 'Menguraikan', 'Membedakan', 'Meramalkan', 'Memperkirakan', 'Menerangkan', 'Menggantikan', 'Menarik kesimpulan', 'Meringkas'],
                                                'C3' => ['Melaksanakan', 'Mengimplementasikan', 'Menggunakan', 'Mengonsepkan', 'Menentukan', 'Memproseskan', 'Mendemonstrasikan', 'Menghitung', 'Menghubungkan', 'Melakukan', 'Membuktikan', 'Menghasilkan', 'Memperagakan', 'Melengkapi', 'Menyesuaikan', 'Menemukan'],
                                                'C4' => ['Mendiferensiasikan', 'Mengorganisasikan', 'Mengatribusikan', 'Mendiagnosis', 'Memerinci', 'Menelaah', 'Mendeteksi', 'Mengaitkan', 'Memecahkan', 'Menguraikan', 'Memisahkan', 'Menyeleksi', 'Memilih', 'Membandingkan', 'Mempertentangkan', 'Menguraikan', 'Membagi', 'Membuat diagram'],
                                                'C5' => ['Mengecek', 'Mengkritik', 'Membuktikan', 'Mempertahankan', 'Memvalidasi', 'Mendukung', 'Memproyeksikan', 'Memperbandingkan', 'Menyimpulkan', 'Mengkritik', 'Menilai', 'Mengevaluasi', 'Memberi saran', 'Memberi argumen', 'Menafsirkan', 'Merekomendasi', 'Memutuskan'],
                                                'C6' => ['Membangun', 'Merencanakan', 'Memproduksi', 'Mengkombinasikan', 'Merancang', 'Merekonstruksi', 'Membuat', 'Menciptakan', 'Mengabstraksi', 'Mengkategorikan', 'Mengkombinasikan', 'Mengarang', 'Merancang', 'Menciptakan', 'Mendesain', 'Menyusun kembali', 'Merangkaikan', 'Membuat pola']
                                            ];
                                        @endphp
                                        @foreach($cognitiveData as $code => $items)
                                            <div class="tab-pane fade {{ $code == 'C1' ? 'show active' : '' }}" id="{{ strtolower($code) }}">
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach($items as $kko)
                                                        <span class="badge bg-light text-dark border border-secondary copy-badge" 
                                                            style="cursor:pointer;" 
                                                            data-code="{{ $code }}" 
                                                            title="Salin {{ $code }}">{{ $kko }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- CONTENT: AFFECTIVE --}}
                                <div class="tab-pane fade" id="content-affective">
                                    <ul class="nav nav-tabs nav-fill small" id="affectiveTab" role="tablist">
                                        @foreach([
                                            'A1' => 'Menerima', 'A2' => 'Merespon', 'A3' => 'Menghargai', 
                                            'A4' => 'Mengorganisasikan', 'A5' => 'Karakterisasi'
                                        ] as $code => $label)
                                            <li class="nav-item">
                                                <button class="nav-link {{ $code == 'A1' ? 'active' : '' }} fw-bold text-info" 
                                                    data-bs-toggle="tab" data-bs-target="#{{ strtolower($code) }}">
                                                    {{ $code }} - {{ $label }}
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="tab-content p-3 border border-top-0 bg-white" style="max-height: 300px; overflow-y: auto;">
                                        @php
                                            $affectiveData = [
                                                'A1' => ['Menanyakan', 'Memilih', 'Mengikuti', 'Menjawab', 'Melanjutkan', 'Memberi', 'Menyatakan', 'Menempatkan'],
                                                'A2' => ['Melaksanakan', 'Membantu', 'Menawarkan diri', 'Menyambut', 'Menolong', 'Mendatangi', 'Melaporkan', 'Menyumbangkan', 'Menyesuaikan diri', 'Berlatih', 'Menampilkan', 'Membawakan', 'Mendiskusikan', 'Menyatakan setuju', 'Mempraktekkan'],
                                                'A3' => ['Menunjukkan', 'Melaksanakan', 'Menyatakan pendapat', 'Mengambil prakarsa', 'Mengikuti', 'Memilih', 'Ikut serta', 'Menggabungkan diri', 'Mengundang', 'Mengusulkan', 'Membedakan', 'Membimbing', 'Membenarkan', 'Menolak', 'Mengajak'],
                                                'A4' => ['Merumuskan', 'Berpegang pada', 'Mengintegrasikan', 'Menghubungkan', 'Mengaitkan', 'Menyusun', 'Mengubah', 'Melengkapi', 'Menyempurnakan', 'Menyesuaikan', 'Menyamakan', 'Mengatur', 'Memperbandingkan', 'Mempertahankan', 'Memodifikasi', 'Mengorganisasi', 'Mengkoordinir', 'Merangkai'],
                                                'A5' => ['Bertindak', 'Menyatakan', 'Memperhatikan', 'Melayani', 'Membuktikan', 'Menunjukkan', 'Bertahan', 'Mempertimbangkan', 'Mempersoalkan']
                                            ];
                                        @endphp
                                        @foreach($affectiveData as $code => $items)
                                            <div class="tab-pane fade {{ $code == 'A1' ? 'show active' : '' }}" id="{{ strtolower($code) }}">
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach($items as $kko)
                                                        <span class="badge bg-light text-dark border border-info copy-badge" 
                                                            style="cursor:pointer;" 
                                                            data-code="{{ $code }}" 
                                                            title="Salin {{ $code }}">{{ $kko }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- CONTENT: PSYCHOMOTOR --}}
                                <div class="tab-pane fade" id="content-psychomotor">
                                    <ul class="nav nav-tabs nav-fill small" id="psychomotorTab" role="tablist">
                                        @foreach([
                                            'P1' => 'Meniru', 'P2' => 'Manipulasi', 'P3' => 'Presisi', 
                                            'P4' => 'Artikulasi', 'P5' => 'Naturalisasi'
                                        ] as $code => $label)
                                            <li class="nav-item">
                                                <button class="nav-link {{ $code == 'P1' ? 'active' : '' }} fw-bold text-primary" 
                                                    data-bs-toggle="tab" data-bs-target="#{{ strtolower($code) }}">
                                                    {{ $code }} - {{ $label }}
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="tab-content p-3 border border-top-0 bg-white" style="max-height: 300px; overflow-y: auto;">
                                        @php
                                            $psychomotorData = [
                                                'P1' => ['Menyalin', 'Mengikuti', 'Mereplikasi', 'Mengulangi', 'Mematuhi', 'Membedakan', 'Mempersiapkan', 'Menirukan', 'Menunjukkan'],
                                                'P2' => ['Membuat kembali', 'Membangun', 'Melakukan', 'Melaksanakan', 'Menerapkan', 'Mengawali', 'Bereaksi', 'Mempersiapkan', 'Memprakarsai', 'Menanggapi', 'Mempertunjukkan', 'Menggunakan', 'Menerapkan'],
                                                'P3' => ['Menunjukkan', 'Melengkapi', 'Menunjukkan', 'Menyempurnakan', 'Mengkalibrasi', 'Mengendalikan', 'Mempraktekkan', 'Memainkan', 'Mengerjakan', 'Membuat', 'Mencoba', 'Memposisikan'],
                                                'P4' => ['Membangun', 'Mengatasi', 'Menggabungkan', 'Koordinat', 'Mengintegrasikan', 'Beradaptasi', 'Mengembangkan', 'Merumuskan', 'Memodifikasi', 'Memasang', 'Membongkar', 'Merangkaikan', 'Menggabungkan', 'Mempolakan'],
                                                'P5' => ['Mendesain', 'Menentukan', 'Mengelola', 'Menciptakan', 'Membangun', 'Membuat', 'Mencipta', 'Mengoperasikan', 'Melakukan', 'Melaksananakan', 'Mengerjakan', 'Menggunakan', 'Memainkan', 'Mengatasi', 'Menyelesaikan']
                                            ];
                                        @endphp
                                        @foreach($psychomotorData as $code => $items)
                                            <div class="tab-pane fade {{ $code == 'P1' ? 'show active' : '' }}" id="{{ strtolower($code) }}">
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach($items as $kko)
                                                        <span class="badge bg-light text-dark border border-primary copy-badge" 
                                                            style="cursor:pointer;" 
                                                            data-code="{{ $code }}" 
                                                            title="Salin {{ $code }}">{{ $kko }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- INPUT SUB-CPMK DINAMIS --}}
            <div id="dynamic-subcpmk-container">
                {{-- Baris Pertama (Default) --}}
                <div class="subcpmk-item mb-2">
                    <div class="input-group">
                        <span class="input-group-text py-0 px-2 bg-light text-muted" style="font-size: 0.8rem;">Sub</span>
                        {{-- Tambahkan ID untuk target copy paste jika diperlukan nanti --}}
                        <input type="text" name="uraian_sub[]" class="form-control form-control-sm subcpmk-input" placeholder="Contoh: Mampu merancang (C6) database relasional..." required>
                        <button class="btn btn-sm btn-outline-danger remove-subcpmk-btn" type="button">Hapus</button>
                    </div>
                </div>
            </div>

            <button type="button" id="add-subcpmk-btn" class="btn btn-outline-primary btn-sm mt-2">
                <i class="bi bi-plus-circle"></i> + Tambah Sub-CPMK
            </button>

            {{-- Tombol Action --}}
            <div class="mt-4 border-top pt-3">
                <button type="reset" class="btn btn-light me-2">Batal</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>

            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Data dari Controller
    const allCpmks = {!! json_encode($existingCpmks) !!};

    // LOGIKA FILTER DROPDOWN
    // Gunakan document.on agar event tetap jalan meski elemen dirender ulang/plugin
    $(document).on('change', '#cpl', function() {
        const selectedCplId = $(this).val(); 
        const cpmkSelect = $('#cpmk_select');

        // Reset Dropdown
        cpmkSelect.empty().prop('disabled', false);
        cpmkSelect.append('<option selected disabled value="">-- Pilih CPMK --</option>');

        // Filter Data (cpl_id == selectedCplId)
        const filteredCpmks = allCpmks.filter(function(item) {
            return item.cpl_id == selectedCplId;
        });

        // Masukkan data ke dropdown
        if (filteredCpmks.length > 0) {
            $.each(filteredCpmks, function(key, cpmk) {
                let judulPendek = cpmk.judul.length > 100 ? cpmk.judul.substring(0, 100) + '...' : cpmk.judul;
                cpmkSelect.append(`<option value="${cpmk.id}">[${cpmk.kode}] ${judulPendek}</option>`);
            });
        } 
        cpmkSelect.append('<option value="new" class="fw-bold text-primary">+ Tambah CPMK Baru</option>');
        
        // Trigger perubahan untuk reset input judul
        cpmkSelect.trigger('change');
    });

    // LOGIKA MUNCULKAN TEXTAREA JUDUL BARU
    $(document).on('change', '#cpmk_select', function() {
        if ($(this).val() === 'new') {
            $('#container-judul-baru').slideDown();
            $('#judul_cpmk_baru').prop('required', true).focus();
        } else {
            $('#container-judul-baru').slideUp();
            $('#judul_cpmk_baru').prop('required', false).val('');
        }
    });

    // LOGIKA TAMBAH/HAPUS SUB-CPMK
    const containerId = '#dynamic-subcpmk-container';
    const itemClass = '.subcpmk-item';
    const removeBtnClass = '.remove-subcpmk-btn';

    $('#add-subcpmk-btn').on('click', function() {
        const firstItem = $(containerId).find(itemClass + ':first');
        if (!firstItem.length) return;
        const newItem = firstItem.clone();
        newItem.find('input').val(''); 
        $(containerId).append(newItem);
        checkRemoveButton();
    });

    $(document).on('click', removeBtnClass, function() {
        $(this).closest(itemClass).remove();
        checkRemoveButton();
    });

    // ==========================================
    // LOGIKA COPY KKO (Dengan Kode)
    // ==========================================
    $(document).on('click', '.copy-badge', function() {
        let text = $(this).text().trim();
        let code = $(this).data('code'); // Ambil kode (C1, A1, P1, dst)
        
        // Format hasil salin: "Kata Kerja (Kode)"
        let fullText = `${text} (${code})`;
        
        // Copy ke clipboard
        navigator.clipboard.writeText(fullText).then(function() {
            console.log('Copied:', fullText);
        }, function(err) {
            console.error('Could not copy text: ', err);
        });
        
        // Efek Visual (Ubah jadi "Disalin!")
        let originalText = $(this).text();
        let $badge = $(this);
        
        $badge.text('Disalin!').removeClass('bg-light text-dark border-secondary border-info border-primary')
              .addClass('bg-success text-white border-success');
        
        setTimeout(() => {
            $badge.text(originalText)
                  .addClass('bg-light text-dark')
                  .removeClass('bg-success text-white border-success');
            
            // Kembalikan border warna asli sesuai tab
            if(code.startsWith('C')) $badge.addClass('border-secondary');
            if(code.startsWith('A')) $badge.addClass('border-info');
            if(code.startsWith('P')) $badge.addClass('border-primary');
            
        }, 1000);
    });

    function checkRemoveButton() {
        const totalItems = $(containerId).find(itemClass).length;
        if (totalItems === 1) {
            $(containerId).find(removeBtnClass).hide();
        } else {
            $(containerId).find(removeBtnClass).show();
        }
    }
    checkRemoveButton();
});
</script>
@endpush
@endsection