<head>
    <meta charset="UTF-8">
    <title>RPS Print</title>
    <style>
        @media print {
            .blue {
                background-color: rgb(221, 235, 247) !important;
                -webkit-print-color-adjust: exact;
            }

            .grey {
                background-color: rgb(234, 234, 234) !important;
                -webkit-print-color-adjust: exact;
            }
            .green {
                background-color: rgb(146, 208, 80) !important; /* Warna hijau dari gambar */
                -webkit-print-color-adjust: exact;
            }
        }

        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .title {
            text-align: center;
            font-family: Cambria;
            background-color: rgb(221, 235, 247);
        }

        .title-sub {
            padding: 0 30px;
        }

        .subtitle {
            background-color: rgb(234, 234, 234);
        }

        .contain {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            text-align: left;
        }

        .cpl-contain {
            font-family: "Times New Roman", Times, serif;
            font-size: 8pt;
            text-align: left;
        }

        .title-cpmk {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            text-align: center;
        }

        .sub-contain {
            padding-left: 8px;
            padding-right: 8px;
        }

        .cpmk-contain {
            vertical-align: text-top;
        }

        .note {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            text-align: left;
            padding: 40px 50px 0 20px;
        }

        .note-list {
            margin-left: -5px;
        }

        .note-contain {
            margin-left: 15px;
        }

        .list-title {
            font-weight: 700;
        }

        .title-cpl {
            font-weight: 700;
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            padding: 5px 0;
            text-align: center;
        }

        .d-flex {
            display: -webkit-box;
            display: flex;
            width: 100%
        }

        .tambahan {
            border: solid 1px white;
            vertical-align: top;
            text-align: left;
        }

        ol.start {
            counter-reset: mycounter;
        }

        ol.start li,
        ol.continue li {
            list-style: none;
        }

        ol.start li:before,
        ol.continue li:before {
            content: counter(mycounter) ". ";
            counter-increment: mycounter;
        }
    </style>
</head>
<table>
    <tr class="title" style="font-size:14pt; text-align:center;">
        <th colspan="2" style="width:15%; vertical-align: middle;">
            <img style="width:2cm;" src="{{ asset('assets/img/logo_unila.png') }}" alt="Logo Universitas">
        </th>

        <th colspan="8" style="width:85%; font-size:12pt;">
            <div style="font-weight: normal;">
                <div><strong>RENCANA PEMBELAJARAN SEMESTER</strong></div>
                <div>PROGRAM STUDI {{ strtoupper($rps->prodi->nama) }}</div>
                <div>{{ strtoupper($rps->prodi->fakultas->nama) }}</div>
                <div><strong>{{ strtoupper($rps->prodi->fakultas->universitas->nama) }}</strong></div>
            </div>
        </th>
    </tr>

    <tr class="contain">
        <th class="sub-contain" rowspan="3" colspan="2" style="text-align: left; vertical-align: top;">Identitas Mata Kuliah</th>
    </tr>
    <tr class="contain">
        <th class="sub-contain subtitle grey" style="text-align: center;">NAMA MK</th>
        <th class="sub-contain subtitle grey" style="text-align: center;">KODE MK</th>
        <th class="sub-contain subtitle grey" style="text-align: center; width:18%;">RUMPUN MATA KULIAH</th>
        <th class="sub-contain subtitle grey" colspan="2" style="text-align: center;">BOBOT (SKS)</th>
        <th class="sub-contain subtitle grey" style="text-align: center;">SEMESTER</th>
        <th class="sub-contain subtitle grey" colspan="2" style="text-align: center;">Direvisi</th>
    </tr>
    @php
        foreach ($mks as $mk):
            if ($rps->kode_mk == $mk->kode) {
                $nama = $mk->nama;
                $kode_mk = $mk->kode;
                $rumpun_mk = $mk->rumpun;
                $bobot_t = $mk->bobot_teori;
                $bobot_p = $mk->bobot_praktikum;
                $prasyarat = $mk->prasyarat;
                $bahasa = $mk->bahasa;
                $deskripsi = $mk->deskripsi;
            }
        endforeach;
    @endphp

    <!-- $tanggal = $mk->updated_at; -->
 
    <tr class="contain">
        <td class="sub-contain" style="height: 60px; text-align: center;width:25%;">{{ $nama }}</td>
        <td class="sub-contain" style="height: 60px; text-align: center;">{{ $kode_mk }}</td>
        <td class="sub-contain" style="height: 60px; text-align: center;">{{ $rumpun_mk }}</td>
        <td class="sub-contain" style="height: 60px; text-align: center;">{{ $bobot_t }}</td>
        <td class="sub-contain" style="height: 60px; text-align: center;">{{ $bobot_p }}</td>
        <td class="sub-contain" style="height: 60px; text-align: center;">{{ $rps->semester }}/8</td>
        <td class="sub-contain" colspan="2" style="height: 60px; text-align: center;">{{ date('d/m/Y', strtotime($rps->updated_at)) }}</td>
    </tr>
    <tr class="contain">
        <th class="sub-contain" rowspan="3" colspan="2" style="text-align: left; vertical-align: top;">Otoritas</th>
    </tr>
    <tr class="contain">
        <th class="subtitle grey" style="text-align:center" colspan="3">Pengembang RPS</th>
        <th class="subtitle grey" style="text-align:center" colspan="2">Ketua Kelompok Keahlian</th>
        <th class="subtitle grey" style="text-align:center" colspan="3">Ka PRODI</th>
    </tr>

    <tr>
        <td class="sub-contain" colspan="3" style="text-align: center;">{{ $rps->pengembang }}</td>
        <td class="sub-contain" colspan="2" style="text-align: center;">{{ $rps->dosen_koordinator }}</td>
        <td class="sub-contain" colspan="3" style="text-align: center;">{{ $rps->kaprodi }}</td>
    </tr>
        
    <tr class="contain">
        <th class="sub-contain" colspan="2" style="text-align: left; vertical-align: top;">Deskripsi Mata Kuliah </th>
        <th class="sub-contain" colspan="8" style="height: 180px;text-align: left; vertical-align: top;font-weight:normal;">{{ $deskripsi}}</th>
    </tr>
    @php
        // Perhitungan rowspan menjadi jauh lebih simpel
        $jumlahCpl = $cpl_prodi->count();
        $jumlahCpmk = $cpmks->count();
        
        $rowspanTotal = 1 + $jumlahCpl + 1 + $jumlahCpmk;
    @endphp

    <tr class="contain">
        <th class="sub-contain" rowspan="{{ $rowspanTotal }}" colspan="2" style="vertical-align: top; white-space: normal;">
            Capaian Pembelajaran Lulusan<br>& Capaian Pembelajaran Mata Kuliah
        </th>
        <th class="sub-contain subtitle grey" colspan="8" style="text-align: left;">
            Capaian Pembelajaran Lulusan (CPL) PRODI
        </th>
    </tr>
 
    @forelse ($cpl_prodi as $cpl)
        <tr class="contain">
            <td class="sub-contain" style="width: 20%;">{{ $cpl->kode }}</td>
            <td class="sub-contain" colspan="7" style="width: 80%;">{{ $cpl->judul }}</td>
        </tr>
    @empty
        <tr class="contain">
            <td class="sub-contain" colspan="8">Tidak ada CPL yang dibebankan pada mata kuliah ini.</td>
        </tr>
    @endforelse

    <tr class="contain">
        <th class="sub-contain subtitle grey" colspan="6" style="border-top:none">Capaian Pembelajaran Mata Kuliah (CPMK)</th>
        <th class="sub-contain subtitle grey" colspan="2" style="border-top:none">CPL yang didukung</th>
    </tr>

    {{-- Loop untuk menampilkan CPMK --}}
    @forelse ($cpmks as $cpmk)
        <tr class="contain">
            <td class="sub-contain">{{$cpmk->kode}}</td>
            <td class="sub-contain" colspan="5">{{ $cpmk->judul }}</td>
            <td class="sub-contain" colspan="2">
                @php
                    // Cari data CPL berdasarkan cpl_id dari CPMK
                    // Menggunakan collection $all_cpls_for_view yang sudah kita siapkan
                    $cpl_pendukung = $all_cpls_for_view->firstWhere('id', $cpmk->cpl_id);
                @endphp
                
                {{-- Tampilkan kode CPL jika ditemukan --}}
                {{ optional($cpl_pendukung)->kode }}
            </td>
        </tr>
    @empty
        <tr class="contain">
            <td class="sub-contain" colspan="8">Tidak ada CPMK untuk mata kuliah ini.</td>
        </tr>
    @endforelse

    @php
        $rowspanPenilaian = 2 + $cpmks->count() + 1; // header + data + total

        // inisialisasi total per kolom
        $sumPerInstrumen = [];
        foreach ($instrumens as $ins) {
            $sumPerInstrumen[$ins->nama_kriteria] = 0;
        }
        $sumTotal = 0;

        // hitung width kolom (semua kolom sama rata)
        $totalCols = $instrumens->count() + 2; // 1 CPMK + instrumen + 1 Total
        $colWidth = $totalCols > 0 ? floor(100 / $totalCols) : 100;
    @endphp

    <tr class="contain">
        <th class="sub-contain" colspan="2" rowspan="{{ $rowspanPenilaian }}" style="vertical-align:top;">Penilaian</th>
        <th class="sub-contain subtitle grey" rowspan="2" style="text-align:center; width:{{ $colWidth }}%;">Id CPMK</th>
        <th class="sub-contain subtitle grey" colspan="{{ $instrumens->count() }}" style="text-align:center;">Bobot per Bentuk Penilaian</th>
        <th class="sub-contain subtitle grey" rowspan="2" style="text-align:center; width:{{ $colWidth }}%;">TOTAL<br>BOBOT PER CPMK</th>
    </tr>
    <tr class="contain">
        @foreach ($instrumens as $ins)
            <td class="sub-contain subtitle grey" style="text-align:center; width:{{ $colWidth }}%;">{{ $ins->nama_kriteria }}</td>
        @endforeach
    </tr>

    @foreach ($cpmks as $cpmk)
        @php $total = 0; @endphp
        <tr class="contain">
            <td class="sub-contain" style="width:{{ $colWidth }}%;">{{ $cpmk->kode }}</td>
            @foreach ($instrumens as $ins)
                @php
                    $nilai = $penilaianMap[$cpmk->kode][$ins->nama_kriteria] ?? 0;
                    $total += $nilai;
                    $sumPerInstrumen[$ins->nama_kriteria] += $nilai;
                @endphp
                <td class="sub-contain" style="text-align:center; width:{{ $colWidth }}%;">{{ $nilai }}</td>
            @endforeach
            <td class="sub-contain" style="text-align:center; width:{{ $colWidth }}%;">{{ $total }}</td>
        </tr>
        @php $sumTotal += $total; @endphp
    @endforeach

    <tr class="sub contain subtitle grey" style="font-weight:bold;">
        <th class="sub-contain" style="width:{{ $colWidth }}%;">Total per penilaian</th>
        @foreach ($instrumens as $ins)
            <td class="sub-contain" style="text-align:center; width:{{ $colWidth }}%;">{{ $sumPerInstrumen[$ins->nama_kriteria] }}</td>
        @endforeach
        <td class="sub-contain" style="text-align:center; width:{{ $colWidth }}%;">{{ $sumTotal }}</td>
    </tr>

    @php
        // 1. Ambil data dari RELASI (Bukan kolom tabel rpss)
        $pustakaUtama = $rps->pustakaUtama;       // Collection Object
        $pustakaPendukung = $rps->pustakaPendukung; // Collection Object

        // 2. Hitung Rowspan Dinamis
        // Jika data kosong, tetap dihitung 1 baris untuk menampilkan tanda strip (-)
        $countUtama = $pustakaUtama->count() > 0 ? $pustakaUtama->count() : 1;
        $countPendukung = $pustakaPendukung->count() > 0 ? $pustakaPendukung->count() : 1;

        // Total Rowspan = (Header Utama) + (Isi Utama) + (Header Pendukung) + (Isi Pendukung)
        $totalRowspan = 1 + $countUtama + 1 + $countPendukung;
    @endphp

    {{-- HEADER KIRI (PUSTAKA) DENGAN ROWSPAN DINAMIS --}}
    <tr class="contain"> 
        <th class="sub-contain" colspan="2" rowspan="{{ $totalRowspan }}" style="vertical-align:top;">Pustaka</th>
        {{-- Header Kanan: Utama --}}
        <th class="sub-contain subtitle grey" colspan="8">Utama:</th>
    </tr>

    {{-- LOOP ISI PUSTAKA UTAMA --}}
    @forelse ($pustakaUtama as $pustaka)
        <tr class="contain">
            <td class="sub-contain" colspan="8">
                {{-- Prioritaskan deskripsi_lengkap, jika null fallback ke format manual --}}
                {{ $pustaka->deskripsi_lengkap ?? $pustaka->kode_pustaka . ' ' . $pustaka->penulis . '. (' . $pustaka->tahun . '). ' . $pustaka->judul . '. ' . $pustaka->penerbit }}
            </td>
        </tr>
    @empty
        <tr class="contain">
            <td class="sub-contain" colspan="8">-</td>
        </tr>
    @endforelse
 
    <tr class="contain">
        <th class="sub-contain subtitle grey" colspan="8">Pendukung:</th>
    </tr>
 
    @forelse ($pustakaPendukung as $pustaka)
        <tr class="contain">
            <td class="sub-contain" colspan="8">
                {{ $pustaka->deskripsi_lengkap ?? $pustaka->kode_pustaka . ' ' . $pustaka->penulis . '. ' . $pustaka->tahun . '. ' . $pustaka->judul . '. ' . $pustaka->penerbit }}
            </td>
        </tr>
    @empty
        <tr class="contain">
            <td class="sub-contain" colspan="8">{{-- Jika tidak ada pendukung --}} - </td>
        </tr>
    @endforelse

    <tr class="contain">
        <th class="sub-contain" colspan="2" rowspan="2" style="vertical-align:top;">Media Pembelajaran</th>
        <th class="sub-contain subtitle grey" colspan="6">Software:</th>
        <th class="sub-contain subtitle grey" colspan="2">Hardware:</th>
    </tr>
    <tr class="contain">
        <td class="sub-contain" colspan="6">{{ $rps->media_software}}</td>
        <td class="sub-contain" colspan="2">{{ $rps->media_hardware }}</td>
    </tr>

    <tr class="contain">
        <th class="sub-contain" colspan="2" style="vertical-align:top;">Team Teaching</th>
        <!-- <td class="sub-contain" colspan="8" style="vertical-align:top">{{ $rps->dosen_anggota1 }}</td> -->
         <td class="sub-contain" colspan="8" style="vertical-align:top"> {{ $rps->dosen }} <br>
            {{ $rps->dosen_anggota1 }} <br>
            {{ $rps->dosen_anggota2 }}
        </td>
    </tr>
    <tr class="contain">
        <th class="sub-contain" colspan="2" style="vertical-align:top;">Matakuliah syarat</th>
        <td class="sub-contain" colspan="8" style="vertical-align:top">{{ $prasyarat }}</td>
    </tr>
    <tr class="contain">
        <th class="sub-contain" colspan="2" style="vertical-align:top;">Ambang Batas Kelulusan Mahasiswa</th>
        <td class="sub-contain" colspan="8">{{ $rps->batas_kelulusan_mhs }}</td>
    </tr>
    <tr class="contain">
        <th class="sub-contain" colspan="2" style="vertical-align:top;">Ambang Batas Kelulusan MK</th>
        <td class="sub-contain" colspan="8">{{ $rps->batas_kelulusan_mk }}%</td>
    </tr> 
    <tr class="contain">
    <th class="sub-contain green" style="text-align:center;">MINGGU KE-</th>
    <th class="sub-contain green" style="text-align:center;">ID CPMK</th>
    <th class="sub-contain green" style="text-align:center;">DESKRIPSI SUB CPMK</th>
    <th class="sub-contain green" style="text-align:center;">INDIKATOR KETERCAPAIAN CPMK</th>
    <th class="sub-contain green" style="text-align:center;">BENTUK ASSESSMEN</th>
    <th class="sub-contain green" style="text-align:center;">MATERI</th>
    <th class="sub-contain green" style="text-align:center;">METODE</th>
    <th class="sub-contain green" style="text-align:center;">LUAR JARINGAN <br>(TATAP MUKA)</th>
    <th class="sub-contain green" style="text-align:center;">DALAM JARINGAN <br>(DARING)</th>
</tr>

{{-- Loop activities. Filter @if masih dipakai karena controller mengambil Activity::all() --}}
@foreach ($activities as $activity)
    @if ($activity->id_rps == $rps->id)
        <tr class="contain">
            <td class="sub-contain" style="text-align:center;">{{ $activity->minggu }}</td>

            {{-- Kolom ID CPMK: Ini sudah benar --}}
            <td class="sub-contain">
                @php
                    $cpmk_codes = $activity->cpmk_details->pluck('kode')->implode(', ');
                @endphp
                {{ !empty($cpmk_codes) ? $cpmk_codes : '---' }}
            </td>

            {{-- FIX: DESKRIPSI SUB CPMK (Menggunakan $sub_cpmks dari Controller) --}}
            <td class="sub-contain">
                @if(!empty($activity->sub_cpmk) && is_array($activity->sub_cpmk))
                    @php
                        // 1. Filter ID yang kosong/null
                        $sub_ids = array_filter($activity->sub_cpmk);
                        
                        // 2. Map ID menjadi deskripsi (menggunakan variabel $sub_cpmks)
                        $descriptions = [];
                        foreach ($sub_ids as $sub_id) {
                            // Gunakan logic dari file Anda yang lain
                            // Ganti '->uraian' jika nama kolomnya beda (misal: 'deskripsi')
                            $description = optional($sub_cpmks->firstWhere('id', $sub_id))->uraian; 
                            
                            if ($description) {
                                $descriptions[] = $description;
                            } else {
                                $descriptions[] = 'ID ' . $sub_id . ' tdk ditemukan'; // Fallback
                            }
                        }
                    @endphp

                    {{-- 3. Terapkan logic "count" pada deskripsi yang sudah jadi --}}
                    @if (count($descriptions) == 1)
                        {{ $descriptions[0] }} {{-- Tampil 1 item tanpa huruf --}}
                    
                    @elseif (count($descriptions) > 1)
                        @foreach ($descriptions as $index => $desc)
                            {{ chr(97 + $index) }}) {{ $desc }} <br> {{-- Tampil >1 item dengan huruf --}}
                        @endforeach
                    
                    @else
                        --- {{-- Kosong --}}
                    @endif
                @else
                    --- {{-- Bukan array atau null --}}
                @endif
            </td>

            {{-- FIX: INDIKATOR (Cek jumlah item) --}}
            <td class="sub-contain">
                @if(!empty($activity->indikator) && is_array($activity->indikator))
                     @php $items = array_values(array_filter($activity->indikator)); @endphp
                    @if (count($items) == 1)
                        {{ trim($items[0]) }}
                    @elseif (count($items) > 1)
                        @foreach ($items as $index => $item)
                            {{ chr(97 + $index) }}) {{ trim($item) }} <br>
                        @endforeach
                    @else
                        -
                    @endif
                @else
                    -
                @endif
            </td>

            {{-- BENTUK ASSESSMEN (Ini string, tidak berubah) --}}
            <td class="sub-contain">
                {{ !empty($activity->bentuk_asesmen) ? $activity->bentuk_asesmen : '-' }}
            </td>
            
            {{-- FIX: MATERI (Cek jumlah item) --}}
            <td class="sub-contain">
                @if(!empty($activity->materi) && is_array($activity->materi))
                    @php $items = array_values(array_filter($activity->materi)); @endphp
                    @if (count($items) == 1)
                        {{ trim($items[0]) }}
                    @elseif (count($items) > 1)
                        @foreach ($items as $index => $materi)
                            {{ chr(97 + $index) }}) {{ trim($materi) }} <br>
                        @endforeach
                    @else
                        ---
                    @endif
                @else
                    ---
                @endif
            </td>
 
            <td class="sub-contain">
                @if(!empty($activity->metode) && is_array($activity->metode))
                    @if(!empty($activity->metode['detail_metode']))
                        @foreach ($activity->metode['detail_metode'] as $metode)
                            {{ $metode['deskripsi'] ?? '' }}
                            @if (!empty($metode['kategori']) || !empty($metode['waktu']))
                                [{{ $metode['kategori'] ?? '' }}: {{ $metode['waktu'] ?? '' }}]
                            @endif
                            <br>    
                        @endforeach
                    @endif
                    @if(!empty($activity->metode['pustaka']))
                    <br>
                        <div style="margin-top: 5px; font-weight:bold;">Pustaka:</div>
                        {{ implode(', ', $activity->metode['pustaka']) }}
                    @endif
                @else
                    -
                @endif
            </td>

            {{-- FIX: KEGIATAN LURING (Cek jumlah item) --}}
            <td class="sub-contain">
                @if(!empty($activity->kegiatan_luring) && is_array($activity->kegiatan_luring))
                    @php $items = array_values(array_filter($activity->kegiatan_luring)); @endphp
                    @if (count($items) == 1)
                        {{ trim($items[0]) }} {{-- Tampil 1 item tanpa angka --}}
                    @elseif (count($items) > 1)
                        @foreach ($items as $index => $item)
                            {{ $index + 1 }}) {{ trim($item) }} <br> {{-- Tampil >1 item dengan angka --}}
                        @endforeach
                    @else
                        -
                    @endif
                @else
                    -
                @endif
            </td>

            {{-- FIX: KEGIATAN DARING (Cek jumlah item) --}}
            <td class="sub-contain">
                @if(!empty($activity->kegiatan_daring) && is_array($activity->kegiatan_daring))
                    @php $items = array_values(array_filter($activity->kegiatan_daring)); @endphp
                    @if (count($items) == 1)
                        {{ trim($items[0]) }} {{-- Tampil 1 item tanpa angka --}}
                    @elseif (count($items) > 1)
                        @foreach ($items as $index => $item)
                             {{ $index + 1 }}) {{ trim($item) }} <br> {{-- Tampil >1 item dengan angka --}}
                        @endforeach
                    @else
                        -
                    @endif
                @else
                    -
                @endif
            </td>
        </tr>
    @endif {{-- Penutup @if ($activity->id_rps == $rps->id) --}}
@endforeach

<script>
    window.print();
</script>