{{-- resources/views/rpsprint.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>RPS Print</title>
    <style>
        @media print {
            .blue { background-color: rgb(221, 235, 247) !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .grey { background-color: rgb(234, 234, 234) !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .green { background-color: rgb(146, 208, 80) !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            @page { margin: 12mm; }
        }
    
        table, th, td { 
            border: 1px solid black; 
            border-collapse: collapse; 
        }
    
        thead { display: table-header-group; }
        tbody { display: table-row-group; }
    
        .title { text-align: center; font-family: Cambria; background-color: rgb(221, 235, 247); }
        .subtitle { background-color: rgb(234, 234, 234); }
        .contain { font-family: "Times New Roman", Times, serif; font-size: 11pt; text-align: left; }
        .sub-contain { padding: 4px 8px; vertical-align: top; }
        .center-text { text-align: center; vertical-align: middle; }
    
        /* ================= PENILAIAN FIX BORDER ================= */
    
        /* pembungkus tabel penilaian */
        .pen-wrap {
            padding: 0 !important;
        }
    
        .pen-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border: 0; /* hilangkan border luar */
        }
    
        .pen-table th, 
        .pen-table td {
            border: 1px solid black;
            box-sizing: border-box;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
    
        /* MATIKAN BORDER YANG NEMPEL KE LUAR (biar gak dobel) */
        .pen-table tr:first-child > * { border-top: 0; }
        .pen-table tr:last-child  > * { border-bottom: 0; }
        .pen-table tr > *:first-child { border-left: 0; }
        .pen-table tr > *:last-child  { border-right: 0; }
    
        .pen-head th, .pen-head td { font-weight: bold; }
    
        .pen-cpmk  { width: 2.8cm; }
        .pen-total { width: 2.8cm; }
    
        .pen-ins {
            font-size: 9pt;
            line-height: 1.1;
            padding: 3px 4px;
            white-space: normal;
        }
    
        @media print {
            .pen-ins { font-size: 9pt; }
        }
    </style>
</head>

<body>

<table>
    {{-- HEADER LOGO & JUDUL --}}
    <tr class="title" style="font-size:14pt; text-align:center;">
        <th colspan="2" style="width:15%; vertical-align: middle;">
            <img style="width:2cm;" src="{{ asset('assets/img/logo_unila.png') }}" alt="Logo Universitas">
        </th>
        <th colspan="8" style="width:85%; font-size:12pt;">
            <div style="font-weight: normal;">
                <div><strong>RENCANA PEMBELAJARAN SEMESTER</strong></div>
                <div>PROGRAM STUDI {{ strtoupper(optional($rps->prodi)->nama ?? '-') }}</div>
                <div>{{ strtoupper(optional(optional($rps->prodi)->fakultas)->nama ?? '-') }}</div>
                <div><strong>{{ strtoupper(optional(optional(optional($rps->prodi)->fakultas)->universitas)->nama ?? '-') }}</strong></div>
            </div>
        </th>
    </tr>

    {{-- IDENTITAS MK --}}
    <tr class="contain">
        <th class="sub-contain" rowspan="3" colspan="2" style="text-align: left; vertical-align: top;">Identitas Mata Kuliah</th>
    </tr>
    <tr class="contain">
        <th class="sub-contain subtitle grey center-text">NAMA MK</th>
        <th class="sub-contain subtitle grey center-text">KODE MK</th>
        <th class="sub-contain subtitle grey center-text" style="width:18%;">RUMPUN MATAKULIAH</th>
        <th class="sub-contain subtitle grey center-text" colspan="2">BOBOT (SKS)</th>
        <th class="sub-contain subtitle grey center-text">SEMESTER</th>
        <th class="sub-contain subtitle grey center-text" colspan="2">Direvisi</th>
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

    <tr class="contain">
        <td class="sub-contain center-text">{{ $nama }}</td>
        <td class="sub-contain center-text">{{ $rps->kode_mk }}</td>
        <td class="sub-contain center-text">{{ $rumpun_mk }}</td>
        <td class="sub-contain center-text">{{ $bobot_t }}</td>
        <td class="sub-contain center-text">{{ $bobot_p }}</td>
        <td class="sub-contain center-text">{{ $rps->semester }}/8</td>
        <td class="sub-contain center-text" colspan="2">{{ $rps->updated_at ? date('d/m/Y', strtotime($rps->updated_at)) : '-' }}</td>
    </tr>

    {{-- OTORITAS --}}
    <tr class="contain">
        <th class="sub-contain" rowspan="3" colspan="2" style="text-align: left; vertical-align: top;">Otoritas</th>
    </tr>
    <tr class="contain">
        <th class="subtitle grey center-text" colspan="2">Pengembang RPS</th>
        <th class="subtitle grey center-text" colspan="3">Ketua Kelompok Keahlian</th>
        <th class="subtitle grey center-text" colspan="3">Ka PRODI</th>
    </tr>
    <tr>
        <td class="sub-contain center-text" colspan="2" style="height: 90px; vertical-align:bottom;">
            {{ $rps->pengembang }}
        </td>
        <td class="sub-contain center-text" colspan="3" style="height: 90px; vertical-align:bottom;">
            {{ $rps->koordinator ?? '-' }}
        </td>
        <td class="sub-contain center-text" colspan="3" style="height: 90px; vertical-align:bottom;">
            {{ $rps->kaprodi }}
        </td>
    </tr>

    {{-- DESKRIPSI MK --}}
    <tr class="contain">
        <th class="sub-contain" colspan="2" style="vertical-align: top;">Deskripsi Mata Kuliah </th>
        <td class="sub-contain" colspan="8" style="text-align: justify;">{{ $deskripsi }}</td>
    </tr>

    {{-- CPL & CPMK (FIX ROWSPAN) --}}
    @php
        $jumlahCpl = $cpl_prodi->count() > 0 ? $cpl_prodi->count() : 1;
        $jumlahCpmk = $cpmks->count() > 0 ? $cpmks->count() : 1;
        $rowspanTotal = 1 + $jumlahCpl + 1 + $jumlahCpmk;
    @endphp

    <tr class="contain">
        <th class="sub-contain" rowspan="{{ $rowspanTotal }}" colspan="2" style="vertical-align: top;">
            Capaian Pembelajaran Lulusan<br><br>&amp; Capaian Pembelajaran Mata Kuliah
        </th>
        <th class="sub-contain subtitle grey" colspan="8" style="text-align: left;">
            Capaian Pembelajaran Lulusan (CPL) PRODI
        </th>
    </tr>

    @forelse ($cpl_prodi as $cpl)
        <tr class="contain">
            <td class="sub-contain">{{ $cpl->kode }}</td>
            <td class="sub-contain" colspan="7">{{ $cpl->judul }}</td>
        </tr>
    @empty
        <tr class="contain">
            <td class="sub-contain" colspan="8">- Belum ada data CPL -</td>
        </tr>
    @endforelse

    <tr class="contain">
        <th class="sub-contain subtitle grey" colspan="6" style="border-top:none">Capaian Pembelajaran Mata Kuliah (CPMK)</th>
        <th class="sub-contain subtitle grey" colspan="2" style="border-top:none">CPL yang didukung</th>
    </tr>

    @forelse ($cpmks as $cpmk)
        <tr class="contain">
            <td class="sub-contain">{{ $cpmk->kode }}</td>
            <td class="sub-contain" colspan="5">{{ $cpmk->judul }}</td>
            <td class="sub-contain" colspan="2">
                @php $cpl_pendukung = $all_cpls_for_view->firstWhere('id', $cpmk->cpl_id); @endphp
                {{ optional($cpl_pendukung)->kode ?? '-' }}
            </td>
        </tr>
    @empty
        <tr class="contain">
            <td class="sub-contain" colspan="8">- Belum ada data CPMK -</td>
        </tr>
    @endforelse

    {{-- ===================== PENILAIAN (DINAMIS) ===================== --}}
    @php
        $sumPerInstrumen = [];
        foreach ($instrumens as $ins) {
            $sumPerInstrumen[$ins->nama] = 0;
        }
        $sumTotalAll = 0;
    @endphp

    <tr class="contain ">
        <th class="sub-contain" colspan="2" style="vertical-align:top;">Penilaian</th>
        <td class="sub-contain" colspan="8" style="padding:0;">
            <table class="pen-table">
                <thead class="pen-head">
                    <tr>
                        <th class="subtitle grey center-text pen-cpmk" rowspan="2">Id CPMK</th>
                        <th class="subtitle grey center-text" colspan="{{ max(1, $instrumens->count()) }}">Bobot per Bentuk Penilaian</th>
                        <th class="subtitle grey center-text pen-total" rowspan="2">TOTAL<br>BOBOTPER<br>CPMK</th>
                    </tr>
                    <tr>
                        @forelse ($instrumens as $ins)
                            <th class="subtitle grey center-text pen-ins">{{ $ins->nama }}</th>
                        @empty
                            <th class="subtitle grey center-text pen-ins">-</th>
                        @endforelse
                    </tr>
                </thead>

                <tbody>
                    @forelse ($cpmks as $cpmk)
                        @php $rowTotal = 0; @endphp
                        <tr>
                            <td class="center-text">{{ $cpmk->kode }}</td>

                            @foreach ($instrumens as $ins)
                                @php
                                    $nilai = $penilaianMap[$cpmk->kode][$ins->nama] ?? 0;
                                    $rowTotal += $nilai;
                                    $sumPerInstrumen[$ins->nama] += $nilai;
                                @endphp
                                <td class="center-text pen-ins">{{ $nilai }}</td>
                            @endforeach

                            @if($instrumens->count() == 0)
                                <td class="center-text pen-ins">-</td>
                            @endif

                            <td class="center-text">{{ $rowTotal }}</td>
                        </tr>
                        @php $sumTotalAll += $rowTotal; @endphp
                    @empty
                        <tr>
                            <td class="center-text">-</td>
                            <td class="center-text pen-ins" colspan="{{ max(1, $instrumens->count()) }}">-</td>
                            <td class="center-text">0</td>
                        </tr>
                    @endforelse

                    <tr class="subtitle grey pen-head">
                        <th class="center-text">Total per penilaian</th>
                        @forelse ($instrumens as $ins)
                            <td class="center-text pen-ins">{{ $sumPerInstrumen[$ins->nama] }}</td>
                        @empty
                            <td class="center-text pen-ins">-</td>
                        @endforelse
                        <td class="center-text">{{ $sumTotalAll }}</td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>

    {{-- PUSTAKA (SUDAH FIX DI LANGKAH SEBELUMNYA) --}}
    @php
        $pustakaUtama = $rps->pustakaUtama;
        $pustakaPendukung = $rps->pustakaPendukung;
        $countUtama = $pustakaUtama->count() > 0 ? $pustakaUtama->count() : 1;
        $countPendukung = $pustakaPendukung->count() > 0 ? $pustakaPendukung->count() : 1;
        $totalRowspan = 1 + $countUtama + 1 + $countPendukung;
    @endphp

    <tr class="contain">
        <th class="sub-contain" colspan="2" rowspan="{{ $totalRowspan }}" style="vertical-align:top;">Pustaka</th>
        <th class="sub-contain subtitle grey" colspan="8">Utama:</th>
    </tr>

    @forelse ($pustakaUtama as $pustaka)
        <tr class="contain">
            <td class="sub-contain" colspan="8">
                {{ $pustaka->deskripsi_lengkap ?? $pustaka->kode_pustaka . ' ' . $pustaka->penulis . '. (' . $pustaka->tahun . '). ' . $pustaka->judul . '. ' . $pustaka->penerbit }}
            </td>
        </tr>
    @empty
        <tr class="contain"><td class="sub-contain" colspan="8">-</td></tr>
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
        <tr class="contain"><td class="sub-contain" colspan="8">-</td></tr>
    @endforelse

    {{-- MEDIA & DOSEN --}}
    <tr class="contain">
        <th class="sub-contain" colspan="2" rowspan="2" style="vertical-align:top;">Media Pembelajaran</th>
        <th class="sub-contain subtitle grey" colspan="6">Software:</th>
        <th class="sub-contain subtitle grey" colspan="2">Hardware:</th>
    </tr>
    <tr class="contain">
        <td class="sub-contain" colspan="6">{{ $rps->media_software ?? '-'}}</td>
        <td class="sub-contain" colspan="2">{{ $rps->media_hardware ?? '-' }}</td>
    </tr>

    <tr class="contain">
        <th class="sub-contain" colspan="2" style="vertical-align:top;">Team Teaching</th>
        <td class="sub-contain" colspan="8">
            1. {{ $rps->dosen }} <br>
            2. {{ $rps->dosen_anggota1 }} <br>
            @if($rps->dosen_anggota2) 3. {{ $rps->dosen_anggota2 }} @endif
        </td>
    </tr>

    <tr class="contain">
        <th class="sub-contain" colspan="2" style="vertical-align:top;">Matakuliah Syarat</th>
        <td class="sub-contain" colspan="8">{{ $prasyarat }}</td>
    </tr>

    <tr class="contain">
        <th class="sub-contain" colspan="2" style="vertical-align:top;">Ambang Batas Kelulusan Mahasiswa</th>
        <td class="sub-contain" colspan="8">{{ $rps->batas_kelulusan_mhs }}</td>
    </tr>

    <tr class="contain">
        <th class="sub-contain" colspan="2" style="vertical-align:top;">Ambang Batas Kelulusan MK</th>
        <td class="sub-contain" colspan="8">{{ $rps->batas_kelulusan_mk }}%</td>
    </tr>
</table>

{{-- TABLE KEGIATAN (HEADER REPEAT) --}}
<table style="width:100%; border-collapse:collapse; margin-top:-1px;">
    <thead>
        <tr class="contain">
            <th class="sub-contain green center-text" style="width: 5%;">MINGGU KE-</th>
            <th class="sub-contain green center-text" style="width: 10%;">ID CPMK</th>
            <th class="sub-contain green center-text" style="width: 15%;">DESKRIPSI SUB CPMK</th>
            <th class="sub-contain green center-text" style="width: 15%;">INDIKATOR KETERCAPAIAN CPMK</th>
            <th class="sub-contain green center-text" style="width: 10%;">BENTUK ASSESSMEN</th>
            <th class="sub-contain green center-text" style="width: 15%;">MATERI</th>
            <th class="sub-contain green center-text" style="width: 15%;">METODE</th>
            <th class="sub-contain green center-text" style="width: 7%;">LUAR JARINGAN (TATAP MUKA)</th>
            <th class="sub-contain green center-text" style="width: 8%;">DALAM JARINGAN (DARING)</th>
        </tr>
    </thead>

    <tbody>
        @php
            $rpsActivities = $activities->where('id_rps', $rps->id);
        @endphp

        @forelse ($rpsActivities as $activity)
            <tr class="contain" @if(in_array((int)$activity->minggu, [8,16])) style="background-color: #ffff00;" @endif>
                <td class="sub-contain center-text" style="vertical-align: middle;">{{ $activity->minggu }}</td>

                <td class="sub-contain center-text">
                    {{ $activity->cpmk_details->pluck('kode')->implode(', ') ?: '-' }}
                </td>

                <td class="sub-contain" style="vertical-align: middle;">
                    @php
                        $subArr = $activity->sub_cpmk;
                        if(!is_array($subArr)){
                            $subArr = json_decode($subArr ?? '[]', true);
                            $subArr = is_array($subArr) ? $subArr : [];
                        }

                        $idCpmkArr = $activity->id_cpmk;
                        if(!is_array($idCpmkArr)){
                            $idCpmkArr = json_decode($idCpmkArr ?? '[]', true);
                            $idCpmkArr = is_array($idCpmkArr) ? $idCpmkArr : [];
                        }

                        $subTextList = [];

                        if(in_array(0, array_map('intval', $subArr), true)) {
                            foreach ($idCpmkArr as $cid) {
                                $judul = optional($cpmks->firstWhere('id', (int)$cid))->judul;
                                if($judul) $subTextList[] = $judul;
                            }
                        } else {
                            foreach(array_filter($subArr) as $sid) {
                                $desc = optional($sub_cpmks->firstWhere('id', (int)$sid))->uraian;
                                if($desc) $subTextList[] = $desc;
                            }
                        }
                    @endphp

                    @if(count($subTextList) > 0)
                        @foreach($subTextList as $idx => $val)
                            {{ chr(97+$idx) }}) {{ $val }}<br>
                        @endforeach
                    @else
                        <div class="center-text">-</div>
                    @endif
                </td>

                <td class="sub-contain" style="vertical-align: middle;">
                    @php $indList = !empty($activity->indikator) && is_array($activity->indikator) ? array_values(array_filter($activity->indikator)) : []; @endphp
                    @if(count($indList) > 0)
                        @foreach($indList as $idx => $val)
                            {{ chr(97+$idx) }}) {{ trim($val) }}<br>
                        @endforeach
                    @else
                        <div class="center-text">-</div>
                    @endif
                </td>

                <td class="sub-contain" style="vertical-align: middle;">
                    {{ $activity->bentuk_asesmen ?: '-' }}
                </td>

                <td class="sub-contain" style="vertical-align: middle;">
                    @php $matList = !empty($activity->materi) && is_array($activity->materi) ? array_values(array_filter($activity->materi)) : []; @endphp
                    @if(count($matList) > 0)
                        @foreach($matList as $idx => $val)
                            {{ count($matList) > 1 ? chr(97+$idx).') ' : '' }}{{ trim($val) }}<br>
                        @endforeach
                    @else
                        <div class="center-text">-</div>
                    @endif
                </td>

                <td class="sub-contain" style="vertical-align: middle;">
                    @if(!empty($activity->metode) && is_array($activity->metode))
                        @if(!empty($activity->metode['detail_metode']))
                            @foreach ($activity->metode['detail_metode'] as $metode)
                                {{ $metode['deskripsi'] ?? '' }}
                                @if (!empty($metode['kategori']) || !empty($metode['waktu']))
                                    <br><strong>[{{ $metode['kategori'] ?? '' }}: {{ $metode['waktu'] ?? '' }}]</strong>
                                    <br>
                                @endif
                                <br>
                            @endforeach
                        @endif
                        @if(!empty($activity->metode['pustaka']))
                            <div style="margin-top: 5px;">
                                <strong>Pustaka :</strong>
                                {{ implode(', ', $activity->metode['pustaka']) }}
                            </div>
                        @endif
                    @else
                        <div class="center-text">-</div>
                    @endif
                </td>

                <td class="sub-contain" style="vertical-align: middle;">
                    @php 
                        $lurList = !empty($activity->kegiatan_luring) && is_array($activity->kegiatan_luring) 
                            ? array_values(array_filter($activity->kegiatan_luring)) 
                            : []; 
                    @endphp
                    @if(count($lurList) > 0)
                        @foreach($lurList as $val)
                            {{ $loop->iteration }}) {{ $val }}<br>
                        @endforeach
                    @else
                        -
                    @endif
                </td>
                
                <td class="sub-contain" style="vertical-align: middle;">
                    @php 
                        $darList = !empty($activity->kegiatan_daring) && is_array($activity->kegiatan_daring) 
                            ? array_values(array_filter($activity->kegiatan_daring)) 
                            : []; 
                    @endphp
                    @if(count($darList) > 0)
                        @foreach($darList as $val)
                            {{ $loop->iteration }}) {{ $val }}<br>
                        @endforeach
                    @else
                        -
                    @endif
                </td>

            </tr>
        @empty
            <tr class="contain">
                <td class="sub-contain center-text" colspan="9" style="padding: 20px;">
                    <em>Belum ada Activity untuk Rencana Kegiatan Pembelajaran yang ditambahkan.</em>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<script>window.print();</script>
</body>
</html>
