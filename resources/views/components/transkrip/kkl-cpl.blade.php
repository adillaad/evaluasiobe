{{--
    Komponen: Summary CPL (Ketercapaian Kompetensi Lulusan)
    Perubahan: background putih/light agar warna wong palette (#0072B2, #E69F00, #D55E00)
               terbaca dengan jelas. Sebelumnya background ungu gelap membuat warna
               wong palette tidak kontras.
--}}

<div id="summarySection" style="display:none; margin-top: 1.5rem;">

    {{-- Box Utama CPL --}}
    <div class="summary-box">
        <h4>Capaian Pembelajaran Lulusan (CPL)</h4>

        <div class="progress-container">
            <div class="progress-bar" id="progressBar" style="width: 0%;"></div>
        </div>

        {{-- Nilai persentase — warna di-set dinamis via JS (wong palette) --}}
        <div class="value" id="avgCpl">0.00%</div>

        {{-- Status teks tanpa background badge --}}
        <div id="cplStatus"></div>

        <p class="mb-0" style="font-size: 0.95rem; margin-top: 0.5rem;">
            Berdasarkan capaian pada seluruh mata kuliah yang telah diambil
        </p>
    </div>
    <div style="display:none;">
        <table>
            <tbody id="cplTableBody"></tbody>
        </table>
    </div>
</div>
