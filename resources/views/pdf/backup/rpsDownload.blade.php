<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>RPS Print</title>
    <style> 
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 10pt; /* Font sedikit lebih kecil untuk PDF agar muat */
            line-height: 1.3;
        }

        /* Warna (Untuk PDF terkadang perlu !important agar background ter-render) */
        .blue { background-color: #ddebf7 !important; }
        .grey { background-color: #eaeaea !important; }
        .green { background-color: #92d050 !important; }

        /* Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            /*margin-bottom: 20px;*/
        } 
        th, td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top; /* Default top, nanti ditimpa class helper */
        }
        
        thead {
            display: table-header-group;
        }
        tbody {
            display: table-row-group;
        }
        tr {
            page-break-inside: avoid;
        }

        /* Helper Classes */
        .title {
            text-align: center;
            font-family: Cambria, serif;
            background-color: #ddebf7;
        }
        .subtitle {
            background-color: #eaeaea;
            font-weight: bold;
        }
        .center-text { text-align: center; }
        .middle-align { vertical-align: middle; }
        
        /* Utilitas untuk list item di PDF yang sering berantakan */
        ul, ol { margin: 0; padding-left: 15px; }
        li { margin-bottom: 2px; }
    </style>
</head>
<body>

<table>
    {{-- HEADER LOGO & JUDUL --}}
    <tr class="title">
        <th colspan="2" style="width:15%; vertical-align: middle; padding: 10px;">
            {{-- Gunakan public_path untuk PDF --}}
            <img style="width:2cm;" src="file://{{ public_path('assets/img/logo_unila.png') }}" alt="Logo">

        </th>
        <th colspan="8" style="width:85%; font-size:12pt; vertical-align: middle;">
            <div style="font-weight: normal;">
                <div><strong>RENCANA PEMBELAJARAN SEMESTER</strong></div>
                <div>PROGRAM STUDI {{ strtoupper(optional($rps->prodi)->nama ?? '-') }}</div>
                <div>{{ strtoupper(optional(optional($rps->prodi)->fakultas)->nama ?? '-') }}</div>
                <div><strong>{{ strtoupper(optional(optional(optional($rps->prodi)->fakultas)->universitas)->nama ?? '-') }}</strong></div>
            </div>
        </th>
    </tr>

    {{-- IDENTITAS MK --}}
    <tr>
        <th rowspan="3" colspan="2" style="text-align: left;">Identitas Mata Kuliah</th>
    </tr>
    <tr>
        <th class="subtitle grey center-text" style="vertical-align: middle;">NAMA MK</th>
        <th class="subtitle grey center-text" style="vertical-align: middle;">KODE MK</th>
        <th class="subtitle grey center-text" style="vertical-align: middle; width:18%;">RUMPUN MK</th>
        <th class="subtitle grey center-text" style="vertical-align: middle;" colspan="2">BOBOT (SKS)</th>
        <th class="subtitle grey center-text" style="vertical-align: middle;">SEMESTER</th>
        <th class="subtitle grey center-text" style="vertical-align: middle;" colspan="2">Direvisi</th>
    </tr>

    @php 
        $mkFound = $mks->firstWhere('kode', $rps->kode_mk);
        $nama = optional($mkFound)->nama ?? '-';
        $rumpun_mk = optional($mkFound)->rumpun ?? '-';
        $bobot_t = optional($mkFound)->bobot_teori ?? 0;
        $bobot_p = optional($mkFound)->bobot_praktikum ?? 0;
        $prasyarat = optional($mkFound)->prasyarat ?? '-';
        $deskripsi = optional($mkFound)->deskripsi ?? '-';
    @endphp

    <tr>
        <td class="center-text middle-align" style="height: 30px;">{{ $nama }}</td>
        <td class="center-text middle-align">{{ $rps->kode_mk }}</td>
        <td class="center-text middle-align">{{ $rumpun_mk }}</td>
        <td class="center-text middle-align">{{ $bobot_t }}</td>
        <td class="center-text middle-align">{{ $bobot_p }}</td>
        <td class="center-text middle-align">{{ $rps->semester }}/8</td>
        <td class="center-text middle-align" colspan="2">{{ $rps->updated_at ? date('d/m/Y', strtotime($rps->updated_at)) : '-' }}</td>
    </tr>

    {{-- OTORITAS --}}
    <tr>
        <th rowspan="3" colspan="2" style="text-align: left;">Otoritas</th>
    </tr>
    <tr>
        <th class="subtitle grey center-text" colspan="2">Pengembang RPS</th>
        <th class="subtitle grey center-text" colspan="3">Koordinator RMK</th>
        <th class="subtitle grey center-text" colspan="3">Ka PRODI</th>
    </tr>
    <tr>
        <td class="center-text" colspan="2" style="height:70px; vertical-align:bottom;">
            {{ $rps->pengembang }}
        </td>
        <td class="center-text" colspan="3" style="height: 70px; vertical-align:bottom;">
            {{ $rps->koordinator ?? '-' }}
        </td>
        <td class="center-text" colspan="3" style="height: 70px; vertical-align:bottom;">
            {{ $rps->kaprodi }}
        </td>
    </tr>
        
    {{-- DESKRIPSI MK --}}
    <tr>
        <th colspan="2" style="text-align: left;">Deskripsi Mata Kuliah </th>
        <td colspan="8" style="text-align: justify;">{{ $deskripsi }}</td>
    </tr>

    {{-- CPL & CPMK --}}
    @php
        $jumlahCpl = $cpl_prodi->count() > 0 ? $cpl_prodi->count() : 1;
        $jumlahCpmk = $cpmks->count() > 0 ? $cpmks->count() : 1;
        $rowspanTotal = 1 + $jumlahCpl + 1 + $jumlahCpmk;
    @endphp

    <tr>
        <th rowspan="{{ $rowspanTotal }}" colspan="2" style="text-align: left;">
            Capaian Pembelajaran Lulusan <br><br>& Capaian Pembelajaran Mata Kuliah
        </th>
        <th class="subtitle grey" colspan="8" style="text-align: left;">
            Capaian Pembelajaran Lulusan (CPL) PRODI
        </th>
    </tr>

    @forelse ($cpl_prodi as $cpl)
        <tr>
            <td style="width: 10%; text-align:left;">{{ $cpl->kode }}</td>
            <td colspan="7">{{ $cpl->judul }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="8">- Belum ada data CPL -</td>
        </tr>
    @endforelse

    <tr>
        <th class="subtitle grey" colspan="6" style="vertical-align: middle; border-top:none; text-align: left;">Capaian Pembelajaran Mata Kuliah (CPMK)</th>
        <th class="subtitle grey" colspan="2" style="border-top:none; text-align: left;">CPL yang didukung</th>
    </tr>

    @forelse ($cpmks as $cpmk)
        <tr>
            <td style="text-align:left;">{{$cpmk->kode}}</td>
            <td colspan="5">{{ $cpmk->judul }}</td>
            <td colspan="2">
                @php $cpl_pendukung = $all_cpls_for_view->firstWhere('id', $cpmk->cpl_id); @endphp
                {{ optional($cpl_pendukung)->kode ?? '-' }}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8">- Belum ada data CPMK -</td>
        </tr>
    @endforelse

    {{-- PENILAIAN --}}
    @php
        $cpmkCountSafe = $cpmks->count() > 0 ? $cpmks->count() : 1;
        $rowspanPenilaian = 2 + $cpmkCountSafe + 1;
        $sumPerInstrumen = [];
        foreach ($instrumens as $ins) $sumPerInstrumen[$ins->nama_kriteria] = 0;
        $sumTotal = 0;
        $totalCols = $instrumens->count() + 2; 
        $colWidth = $totalCols > 0 ? floor(100 / $totalCols) : 100;
    @endphp

    <tr>
        <th colspan="2" rowspan="{{ $rowspanPenilaian }}" style="text-align: left;">Penilaian</th>
        <th class="subtitle grey center-text" rowspan="2" style="vertical-align: middle; width:{{ $colWidth }}%;">ID CPMK</th>
        <th class="subtitle grey center-text" colspan="{{ $instrumens->count() > 0 ? $instrumens->count() : 1 }}">Bobot per Bentuk Penilaian</th>
        <th class="subtitle grey center-text" rowspan="2" style="vertical-align: middle; width:{{ $colWidth }}%;">TOTAL BOBOT<br> PER CPMK</th>
    </tr>
    <tr>
        @forelse ($instrumens as $ins)
            <td class="subtitle grey center-text">{{ $ins->nama_kriteria }}</td>
        @empty
             <td class="subtitle grey center-text">-</td>
        @endforelse
    </tr>

    @forelse ($cpmks as $cpmk)
        @php $total = 0; @endphp
        <tr>
            <td class="center-text">{{ $cpmk->kode }}</td>
            @foreach ($instrumens as $ins)
                @php
                    $nilai = $penilaianMap[$cpmk->kode][$ins->nama_kriteria] ?? 0;
                    $total += $nilai;
                    $sumPerInstrumen[$ins->nama_kriteria] += $nilai;
                @endphp
                <td class="center-text">{{ $nilai }}</td>
            @endforeach
            @if($instrumens->count() == 0)
                <td class="center-text">-</td>
            @endif
            <td class="center-text">{{ $total }}</td>
        </tr>
        @php $sumTotal += $total; @endphp
    @empty
        <tr>
            <td class="center-text">-</td>
            <td class="center-text" colspan="{{ $instrumens->count() > 0 ? $instrumens->count() : 1 }}">-</td>
            <td class="center-text">0</td>
        </tr>
    @endforelse

    <tr style="font-weight:bold;">
        <td class="center-text">Total</td>
        @forelse ($instrumens as $ins)
            <td class="center-text">{{ $sumPerInstrumen[$ins->nama_kriteria] }}</td>
        @empty
             <td class="center-text">-</td>
        @endforelse
        <td class="center-text">{{ $sumTotal }}</td>
    </tr>

    {{-- PUSTAKA --}}
    @php
        $pustakaUtama = $rps->pustakaUtama;
        $pustakaPendukung = $rps->pustakaPendukung;
        $countUtama = $pustakaUtama->count() > 0 ? $pustakaUtama->count() : 1;
        $countPendukung = $pustakaPendukung->count() > 0 ? $pustakaPendukung->count() : 1;
        $totalRowspan = 1 + $countUtama + 1 + $countPendukung;
    @endphp

    <tr> 
        <th colspan="2" rowspan="{{ $totalRowspan }}" style="text-align: left;">Pustaka</th>
        <th class="subtitle grey" colspan="8" style="text-align: left;">Utama:</th>
    </tr>

    @forelse ($pustakaUtama as $pustaka)
        <tr>
            <td colspan="8">
                {{ $pustaka->deskripsi_lengkap ?? $pustaka->kode_pustaka . ' ' . $pustaka->penulis . '. (' . $pustaka->tahun . '). ' . $pustaka->judul . '. ' . $pustaka->penerbit }}
            </td>
        </tr>
    @empty
        <tr><td colspan="8">-</td></tr>
    @endforelse

    <tr>
        <th class="subtitle grey" colspan="8" style="text-align: left;">Pendukung:</th>
    </tr>

    @forelse ($pustakaPendukung as $pustaka)
        <tr>
            <td colspan="8">
                 {{ $pustaka->deskripsi_lengkap ?? $pustaka->kode_pustaka . ' ' . $pustaka->penulis . '. ' . $pustaka->tahun . '. ' . $pustaka->judul . '. ' . $pustaka->penerbit }}
            </td>
        </tr>
    @empty
        <tr><td colspan="8">-</td></tr>
    @endforelse

    {{-- MEDIA, TEAM, SYARAT --}}
    <tr>
        <th colspan="2" rowspan="2" style="text-align: left;">Media Pembelajaran</th>
        <th class="subtitle grey" colspan="6" style="text-align: left;">Software:</th>
        <th class="subtitle grey" colspan="2" style="text-align: left;">Hardware:</th>
    </tr>
    <tr>
        <td colspan="6">{{ $rps->media_software ?? '-'}}</td>
        <td colspan="2">{{ $rps->media_hardware ?? '-' }}</td>
    </tr>

    <tr>
        <th colspan="2" style="text-align: left;">Team Teaching</th>
        <td colspan="8">
             1. {{ $rps->dosen }} <br>
             2. {{ $rps->dosen_anggota1 }} <br>
             @if($rps->dosen_anggota2) 3. {{ $rps->dosen_anggota2 }} @endif
        </td>
    </tr>
    <tr>
        <th colspan="2" style="text-align: left;">Matakuliah Syarat</th>
        <td colspan="8">{{ $prasyarat }}</td>
    </tr>
    <tr>
        <th colspan="2" style="text-align: left;">Ambang Batas Kelulusan Mahasiswa</th>
        <td colspan="8" style="vertical-align: middle;">{{ $rps->batas_kelulusan_mhs }}</td>
    </tr>
    <tr>
        <th colspan="2" style="text-align: left;">Ambang Batas Kelulusan MK</th>
        <td colspan="8" style="vertical-align: middle;">{{ $rps->batas_kelulusan_mk }}%</td>
    </tr> 

    {{-- KEGIATAN --}} 
    <table>
    <thead>
        <tr class="green center-text">
        <th class="sub-contain green center-text" style="vertical-align: middle; width: 5%;">MINGGU KE-</th>
        <th class="sub-contain green center-text" style="vertical-align: middle; width: 10%;">ID CPMK</th>
        <th class="sub-contain green center-text" style="vertical-align: middle; width: 15%;">DESKRIPSI SUB CPMK</th>
        <th class="sub-contain green center-text" style="vertical-align: middle; width: 15%;">INDIKATOR KETERCAPAIAN CPMK</th>
        <th class="sub-contain green center-text" style="vertical-align: middle; width: 10%;">BENTUK ASSESSMEN</th>
        <th class="sub-contain green center-text" style="vertical-align: middle; width: 15%;">MATERI</th>
        <th class="sub-contain green center-text" style="vertical-align: middle; width: 15%;">METODE</th>
        <th class="sub-contain green center-text" style="vertical-align: middle; width: 7%;">LUAR JARINGAN (TATAP MUKA)</th>
        <th class="sub-contain green center-text" style="vertical-align: middle; width: 8%;">DALAM JARINGAN (DARING)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $rpsActivities = $activities->where('id_rps', $rps->id);
        @endphp

        @forelse ($rpsActivities as $activity)
            <tr>
                <td class="center-text middle-align">{{ $activity->minggu }}</td>

                <td class="center-text middle-align">
                    {{ $activity->cpmk_details->pluck('kode')->implode(', ') ?: '-' }}
                </td>

                <td>
                    @php
                        $subCpmkList = [];
                        if (!empty($activity->sub_cpmk) && is_array($activity->sub_cpmk)) {
                            foreach (array_filter($activity->sub_cpmk) as $sid) {
                                $desc = optional($sub_cpmks->firstWhere('id', $sid))->uraian;
                                if ($desc) $subCpmkList[] = $desc;
                            }
                        }
                    @endphp

                    @if(count($subCpmkList))
                        @foreach($subCpmkList as $i => $val)
                            {{ chr(97+$i) }}) {{ $val }}<br>
                        @endforeach
                    @else
                        <div class="center-text">-</div>
                    @endif
                </td>

                <td>
                    @php
                        $indikator = is_array($activity->indikator) ? array_filter($activity->indikator) : [];
                    @endphp

                    @if(count($indikator))
                        @foreach($indikator as $i => $val)
                            {{ chr(97+$i) }}) {{ trim($val) }}<br>
                        @endforeach
                    @else
                        <div class="center-text">-</div>
                    @endif
                </td>

                <td class="middle-align">
                    {{ $activity->bentuk_asesmen ?: '-' }}
                </td>

                <td>
                    @php
                        $materi = is_array($activity->materi) ? array_filter($activity->materi) : [];
                    @endphp

                    @if(count($materi))
                        @foreach($materi as $i => $val)
                            {{ chr(97+$i) }}) {{ trim($val) }}<br>
                        @endforeach
                    @else
                        <div class="center-text">-</div>
                    @endif
                </td>

                <td>
                    @if(is_array($activity->metode) && !empty($activity->metode['detail_metode']))
                        @foreach ($activity->metode['detail_metode'] as $metode)
                            {{ $metode['deskripsi'] ?? '' }}
                            @if (!empty($metode['kategori']) || !empty($metode['waktu']))
                                <br><strong>[{{ $metode['kategori'] ?? '' }}: {{ $metode['waktu'] ?? '' }}]</strong>
                            @endif
                            <br>
                        @endforeach
                    @else
                        <div class="center-text">-</div>
                    @endif
                </td>

                <td>
                    @php
                        $luring = is_array($activity->kegiatan_luring) ? array_filter($activity->kegiatan_luring) : [];
                    @endphp

                    @if(count($luring))
                        @foreach($luring as $val) {{ $val }}<br> @endforeach
                    @else
                        -
                    @endif
                </td>

                <td>
                    @php
                        $daring = is_array($activity->kegiatan_daring) ? array_filter($activity->kegiatan_daring) : [];
                    @endphp

                    @if(count($daring))
                        @foreach($daring as $val) {{ $val }}<br> @endforeach
                    @else
                        -
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="center-text" style="padding:20px;">
                    <em>Belum ada Activity untuk Rencana Kegiatan Pembelajaran.</em>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>