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

    {{-- Loop untuk menampilkan CPL PRODI --}}
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
        $pustakaItems = $rps->pustaka_utama ?? []; // langsung array, tanpa explode
        $pustakaCount = count($pustakaItems) > 0 ? count($pustakaItems) : 1;
        $totalRowspan = $pustakaCount + 3;
    @endphp

    <tr class="contain"> 
        <th class="sub-contain" colspan="2" rowspan="{{ $totalRowspan }}" style="vertical-align:top;">Pustaka</th>
        <th class="sub-contain subtitle grey" colspan="8">Utama:</th>
    </tr>

    @forelse ($pustakaItems as $pustaka)
        <tr class="contain">
            <td class="sub-contain" colspan="8">{{ trim($pustaka) }}</td>
        </tr>
    @empty
        <tr class="contain">
            <td class="sub-contain" colspan="8">-</td>
        </tr>
    @endforelse

    <tr class="contain">
        <th class="sub-contain subtitle grey" colspan="8">Pendukung:</th>
    </tr>
    <tr class="contain">
        <td class="sub-contain" colspan="8">{{ $rps->pustaka_pendukung ?? '-' }}</td>
    </tr>

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

    <!-- TABEL CPMK (versi sesuai gambar) -->
<tr class="contain">
    <th class="sub-contain" style="text-align:center;">MINGGU KE-</th>
    <th class="sub-contain" style="text-align:center;">ID CPMK</th>
    <th class="sub-contain" style="text-align:center;">DESKRIPSI SUB CPMK</th>
    <th class="sub-contain" style="text-align:center;">INDIKATOR KETERCAPAIAN CPMK</th>
    <th class="sub-contain" style="text-align:center;">BENTUK ASSESSMEN</th>
    <th class="sub-contain" style="text-align:center;">MATERI</th>
    <th class="sub-contain" style="text-align:center;">METODE</th>
    <th class="sub-contain" style="text-align:center;">LUAR JARINGAN <br>(TATAP MUKA)</th>
    <th class="sub-contain" style="text-align:center;">DALAM JARINGAN <br>(DARING)</th>
</tr>
@foreach ($activities as $activity)
    @if ($activity->id_rps == $rps->id)
        <tr class="contain">
            <!-- Minggu -->
            <td class="sub-contain" style="text-align:center;">{{ $activity->minggu }}</td>

            <!-- ID CPMK (fk ambil kode di tabel cpmks) -->
            <td class="sub-contain">{{ $activity->cpmk->kode ?? '---' }}</td>

            <!-- Sub-CPMK -->
            <td class="sub-contain">
                @if(!empty($activity->sub_cpmk))
                    {{-- Langsung loop $activity->sub_cpmk karena sudah array --}}
                    @foreach ($activity->sub_cpmk as $index => $sub)
                        {{ chr(97 + $index) }}) {{ trim($sub) }} <br>
                    @endforeach
                @else
                    ---
                @endif
            </td>

            <!-- Indikator -->
            <td class="text-wrap">
                @if(!empty($activity->indikator))
                    {{-- Langsung loop $activity->indikator karena sudah array --}}
                    @foreach ($activity->indikator as $index => $item)
                        {{ chr(97 + $index) }}) {{ trim($item) }} <br>
                    @endforeach
                @else
                    -
                @endif
            </td>

            <!-- Bentuk Assessment -->
            <td class="sub-contain">{{ $activity->bentuk_asesmen }}</td>
            
            <!-- Materi -->
            <td class="sub-contain">
                @if(!empty($activity->materi))
                    @php $materis = explode(',', $activity->materi); @endphp
                    @foreach ($materis as $index => $materi)
                        {{ chr(97 + $index) }}) {{ trim($materi) }} <br>
                    @endforeach
                @else
                    ---
                @endif
            </td>

            <!-- Metode -->
            <td class="sub-contain">{{ $activity->metode ?? 'Ceramah & Diskusi' }}</td>

            <td class="text-wrap">
                {{-- Tambahkan pengecekan '&& trim($activity->kegiatan_luring) != "-"' --}}
                @if(!empty($activity->kegiatan_luring) && trim($activity->kegiatan_luring) != '-')
                    @php 
                        $items = explode(',', $activity->kegiatan_luring); 
                    @endphp
                    @foreach ($items as $index => $item)
                        {{ $index + 1 }}) {{ trim($item) }} <br>
                    @endforeach
                @else
                    -
                @endif
            </td>

            <td class="text-wrap">
                {{-- Terapkan logika yang sama untuk kegiatan daring --}}
                @if(!empty($activity->kegiatan_daring) && trim($activity->kegiatan_daring) != '-')
                    @php 
                        $items = explode(',', $activity->kegiatan_daring); 
                    @endphp
                    @foreach ($items as $index => $item)
                        {{ $index + 1 }}) {{ trim($item) }} <br>
                    @endforeach
                @else
                    -
                @endif
            </td>
        </tr>
    @endif
@endforeach




    <!-- ACTIVITES START -->
    <!-- <tr class="title-cpmk grey">
        <th colspan="1" rowspan="2" style="width: 3%;">Mg Ke-</th>
        <th colspan="2" rowspan="2" style="width: 10%;">
            <div class="row">
                <div class="col">Sub-CPMK</div>
                <div class="col">(Kemampuan akhir tiap</div>
                <div class="col">tahapan belajar)</div>
            </div>
        </th>
        <th colspan="2">Penilaian</th>
        <th colspan="3" style="width:15%">
            <div class="row">
                <div class="col">Bantuk Pembelajaran,</div>
                <div class="col">Metode Pembelajaran, </div>
                <div class="col">Penugasan Mahasiswa,</div>
                <div class="col" style="color:blue">[ Estimasi Waktu]</div>
            </div>
        </th>
        <th colspan="1" rowspan="2" style="width:10%">
            <div class="row">
                <div class="col">Materi</div>
                <div class="col">Pembelajaran</div>
                <div class="col" style="color:blue">[ Pustaka ]</div>
            </div>
        </th>
        <th colspan="1" rowspan="2" style="width:1%">
            <div class="row">
                <div class="col">Bobot</div>
                <div class="col">Penilaian</div>
                <div class="col">(%)</div>
            </div>
        </th>
    </tr>
    <tr class="title-cpmk grey">
        <th colspan="1" style="width:10%">Indikator</th>
        <th colspan="1">Kriteria & Bentuk</th>
        <th colspan="1">Luring (<em>offline</em>)</th>
        <th colspan="2">Daring (<em>online</em>)</th>
    </tr>
    <tr class="title-cpmk grey">

        <th colspan="1">(1)</th>
        <th colspan="2">(2)</th>
        <th colspan="1">(3)</th>
        <th colspan="1">(4)</th>
        <th colspan="1">(5)</th>
        <th colspan="2">(6)</th>
        <th colspan="1">(7)</th>
        <th colspan="1">(8)</th>
    </tr>
    @foreach ($activities as $activity)
        @if ($activity->id_rps == $rps->id)
            @php
                $minggu = explode('-', $activity->minggu);
            @endphp
            @if ((int) $minggu[0] < 8)
                <tr class="contain">
                    <td class="title-cpmk cpmk-contain" colspan="1">{{ $activity->minggu }}</td>
                    <td class="cpmk-contain sub-contain" colspan="2">{{ $activity->sub_cpmk }}</td>
                    <td class="cpmk-contain" colspan="1"><?= $activity->indikator ?></td>
                    <td class="cpmk-contain sub-contain" colspan="1">{{ $rps->tipe }}</td>
                    <td class="cpmk-contain sub-contain" colspan="1">{{ $rps->media }}</td>
                    <td class="cpmk-contain" colspan="2"></td>
                    <td class="cpmk-contain sub-contain" colspan="1"><?= $activity->materi ?></td>
                    <td class="cpmk-contain sub-contain" colspan="1">{{ $activity->bobot }}</td>
                </tr>
            @endif
        @endif
    @endforeach
    <tr class="contain">
        <td class="title-cpmk cpmk-contain" colspan="1">8</td>
        <th class="title-cpmk cpmk-contain" colspan="9">Ujian Tengah Semester</th>
    </tr>
    @foreach ($activities as $activity)
        @if ($activity->id_rps == $rps->id)
            @php
                $minggu = explode('-', $activity->minggu);
            @endphp
            @if ((int) $minggu[0] > 8)
                <tr class="contain">
                    <td class="title-cpmk cpmk-contain" colspan="1">{{ $activity->minggu }}</td>
                    <td class="cpmk-contain sub-contain" colspan="2">{{ $activity->sub_cpmk }}</td>
                    <td class="cpmk-contain" colspan="1"><?= $activity->indikator ?></td>
                    <td class="cpmk-contain sub-contain" colspan="1">{{ $rps->tipe }}</td>
                    <td class="cpmk-contain sub-contain" colspan="1">{{ $rps->media }}</td>
                    <td class="cpmk-contain sub-contain" colspan="2"></td>
                    <td class="cpmk-contain sub-contain" colspan="1"><?= $activity->materi ?></td>
                    <td class="cpmk-contain sub-contain" colspan="1">{{ $activity->bobot }}</td>
                </tr>
            @endif
        @endif
    @endforeach
    <tr class="contain">
        <td class="title-cpmk cpmk-contain" colspan="1">16</td>
        <th class="title-cpmk cpmk-contain" colspan="9">Ujian Akhir Semester</th>
    </tr>
</table>
<div class="note">
    <table style="font-size:11pt">
        <tr>
            <th colspan="4" class="tambahan">Language</td>
            <th colspan="1" class="tambahan">:</td>
            <td colspan="7" class="tambahan">{{ $bahasa }}</td>
        </tr>
        <tr>
            <th colspan="4" class="tambahan">Requirements according to the Examination regulations:</td>
            <th colspan="1" class="tambahan">:</td>
            <td colspan="7" class="tambahan">{{ $rps->syarat_ujian }}</td>
        </tr>
        <tr>
            <th colspan="4" class="tambahan">Study and examination requirements</td>
            <th colspan="1" class="tambahan">:</td>
            <td colspan="7" class="tambahan">{{ $rps->syarat_studi }}</td>
        </tr>
        <tr>
            <th colspan="4" class="tambahan">Assessment and evaluation</td>
            <th colspan="1" class="tambahan">:</td>
            <td colspan="7" class="tambahan">{{ $rps->kontrak }}</td>
        </tr>
    </table>
    <div style="font-weight:700; text-decoration:underline; margin-top: 20px">Catatan :</div>
    <ol>
        <li class="note-list">
            <div class="note-contain">
                <span class="list-title">Capaian Pembelajaran Lulusan PRODI (CPL-PRODI) </span>
                <span class="list-contain">adalah kemampuan yang dimiliki oleh setiap lulusan PRODI yang merupakan
                    internalisasi dari sikap, penguasaan pengetahuan dan ketrampilan sesuai dengan jenjang prodinya yang
                    diperoleh melalui proses pembelajaran.</span>
            </div>
        </li>
        <li class="note-list">
            <div class="note-contain">
                <span class="list-title">CPL yang dibebankan pada mata kuliah </span>
                <span class="list-contain">adalah beberapa capaian pembelajaran lulusan program studi (CPL-PRODI) yang
                    digunakan untuk pembentukan/pengembangan sebuah mata kuliah yang terdiri dari aspek sikap,
                    ketrampulan umum, ketrampilan khusus dan pengetahuan.</span>
            </div>
        </li>
        <li class="note-list">
            <div class="note-contain">
                <span class="list-title">CP Mata kuliah (CPMK) </span>
                <span class="list-contain">adalah kemampuan yang dijabarkan secara spesifik dari CPL yang dibebankan
                    pada mata kuliah, dan bersifat spesifik terhadap bahan kajian atau materi pembelajaran mata kuliah
                    tersebut.</span>
            </div>
        </li>
        <li class="note-list">
            <div class="note-contain">
                <span class="list-title">Sub-CP Mata kuliah (Sub-CPMK) </span>
                <span class="list-contain">adalah kemampuan yang dijabarkan secara spesifik dari CPMK yang dapat diukur
                    atau diamati dan merupakan kemampuan akhir yang direncanakan pada tiap tahap pembelajaran, dan
                    bersifat spesifik terhadap materi pembelajaran mata kuliah tersebut.</span>
            </div>
        </li>
        <li class="note-list">
            <div class="note-contain">
                <span class="list-title">Indikator penilaian </span>
                <span class="list-contain">kemampuan dalam proses maupun hasil belajar mahasiswa adalah pernyataan
                    spesifik dan terukur yang mengidentifikasi kemampuan atau kinerja hasil belajar mahasiswa yang
                    disertai bukti-bukti.</span>
            </div>
        </li>
        <li class="note-list">
            <div class="note-contain">
                <span class="list-title">Kreteria Penilaian </span>
                <span class="list-contain">adalah patokan yang digunakan sebagai ukuran atau tolok ukur ketercapaian
                    pembelajaran dalam penilaian berdasarkan indikator-indikator yang telah ditetapkan. Kreteria
                    penilaian merupakan pedoman bagi penilai agar penilaian konsisten dan tidak bias. Kreteria dapat
                    berupa kuantitatif ataupun kualitatif.</span>
            </div>
        </li>
        <li class="note-list">
            <div class="note-contain">
                <span class="list-title">Bentuk penilaian: </span>
                <span class="list-contain">tes dan non-tes.</span>
            </div>
        </li>
        <li class="note-list">
            <div class="note-contain">
                <span class="list-title">Bentuk pembelajaran: </span>
                <span class="list-contain">Kuliah, Responsi, Tutorial, Seminar atau yang setara, Praktikum, Praktik
                    Studio, Praktik Bengkel, Praktik Lapangan, Penelitian, Pengabdian Kepada Masyarakat dan/atau bentuk
                    pembelajaran lain yang setara.</span>
            </div>
        </li>
        <li class="note-list">
            <div class="note-contain">
                <span class="list-title">Metode Pembelajaran: </span>
                <span class="list-contain">Small Group Discussion, Role-Play & Simulation, Discovery Learning,
                    Self-Directed Learning, Cooperative Learning, Collaborative Learning, Contextual Learning, Project
                    Based Learning, dan metode lainnya yg setara.</span>
            </div>
        </li>
        <li class="note-list">
            <div class="note-contain">
                <span class="list-title">Materi Pembelajaran </span>
                <span class="list-contain">adalah rincian atau uraian dari bahan kajian yg dapat disajikan dalam bentuk
                    beberapa pokok dan sub-pokok bahasan.</span>
            </div>
        </li>
        <li class="note-list">
            <div class="note-contain">
                <span class="list-title">Bobot penilaian </span>
                <span class="list-contain">adalah prosentasi penilaian terhadap setiap pencapaian sub-CPMK yang
                    besarnya proposional dengan tingkat kesulitan pencapaian sub-CPMK tsb., dan totalnya 100%.</span>
            </div>
        </li>
        <li class="note-list">
            <div class="note-contain">
                <span class="list-contain">TM=Tatap Muka, PT=Penugasan terstruktur, BM=Belajar mandiri.</span>
            </div>
        </li>
    </ol>
</div> -->
<script>
    window.print();
</script>
