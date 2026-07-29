<head>
    <meta charset="UTF-8">
    <title>RPS Print</title>
    <style>
        @media print {
            .blue { background-color: rgb(221, 235, 247) !important; -webkit-print-color-adjust: exact; }
            .grey { background-color: rgb(234, 234, 234) !important; -webkit-print-color-adjust: exact; }
            .green { background-color: rgb(146, 208, 80) !important; -webkit-print-color-adjust: exact; }
        }
        table, th, td { border: 1px solid black; border-collapse: collapse; }
        thead { display: table-header-group; }
        tbody { display: table-row-group; } 
        .title { text-align: center; font-family: Cambria; background-color: rgb(221, 235, 247); }
        .subtitle { background-color: rgb(234, 234, 234); }
        .contain { font-family: "Times New Roman", Times, serif; font-size: 11pt; text-align: left; }
        .sub-contain { padding: 4px 8px; vertical-align: top; } /* Tambah padding biar rapi */
        .center-text { text-align: center; vertical-align: middle; }
    </style>
</head>

<body> <table>
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
        <th class="sub-contain subtitle grey center-text" style="width:18%;">RUMPUN MK</th>
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
        // FIX: Gunakan ternary agar minimal 1 baris, karena jika kosong kita tetap render 1 baris keterangan "Tidak ada"
        // Ini mencegah tabel "acak-acakan"
        $jumlahCpl = $cpl_prodi->count() > 0 ? $cpl_prodi->count() : 1;
        $jumlahCpmk = $cpmks->count() > 0 ? $cpmks->count() : 1;
        
        $rowspanTotal = 1 + $jumlahCpl + 1 + $jumlahCpmk;
    @endphp

    <tr class="contain">
        <th class="sub-contain" rowspan="{{ $rowspanTotal }}" colspan="2" style="vertical-align: top;">
            Capaian Pembelajaran Lulusan<br><br>& Capaian Pembelajaran Mata Kuliah
        </th>
        <th class="sub-contain subtitle grey" colspan="8" style="text-align: left;">
            Capaian Pembelajaran Lulusan (CPL) PRODI
        </th>
    </tr>

    @forelse ($cpl_prodi as $cpl)
        <tr class="contain">
            <td class="sub-contain"">{{ $cpl->kode }}</td>
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
            <td class="sub-contain"">{{$cpmk->kode}}</td>
            <td class="sub-contain" colspan="5">{{ $cpmk->judul }}</td>
            <td class="sub-contain" colspan="2">
                @php
                    $cpl_pendukung = $all_cpls_for_view->firstWhere('id', $cpmk->cpl_id);
                @endphp
                {{ optional($cpl_pendukung)->kode ?? '-' }}
            </td>
        </tr>
    @empty
        <tr class="contain">
            <td class="sub-contain" colspan="8">- Belum ada data CPMK -</td>
        </tr>
    @endforelse

    {{-- PENILAIAN (FIX LAYOUT) --}} 
    @php
        // FIX: Hitung jumlah baris CPMK untuk rowspan (minimal 1)
        $cpmkCountSafe = $cpmks->count() > 0 ? $cpmks->count() : 1;
        $rowspanPenilaian = 2 + $cpmkCountSafe + 1; // Header(2) + Content + Total(1)

        // Init sum array
        $sumPerInstrumen = [];
        foreach ($instrumens as $ins) {
            $sumPerInstrumen[$ins->nama] = 0;
        }
        $sumTotal = 0;
        $totalCols = $instrumens->count() + 2; 
        $colWidth = $totalCols > 0 ? floor(100 / $totalCols) : 100;
    @endphp

    <tr class="contain">
        <th class="sub-contain" colspan="2" rowspan="{{ $rowspanPenilaian }}" style="vertical-align:top;">Penilaian</th>
        <th class="sub-contain subtitle grey center-text" rowspan="2" style="width:{{ $colWidth }}%;">Kode CPMK</th>
        <th class="sub-contain subtitle grey center-text" colspan="{{ $instrumens->count() > 0 ? $instrumens->count() : 1 }}">Bobot per Bentuk Penilaian</th>
        <th class="sub-contain subtitle grey center-text" rowspan="2" style="width:{{ $colWidth }}%;">TOTAL</th>
    </tr>
    <tr class="contain">
        @forelse ($instrumens as $ins)
            <td class="sub-contain subtitle grey center-text">{{ $ins->nama }}</td>
        @empty
             <td class="sub-contain subtitle grey center-text">-</td>
        @endforelse
    </tr>

    {{-- Gunakan @forelse agar jika CPMK kosong, tabel tidak pecah --}}
    @forelse ($cpmks as $cpmk)
        @php $total = 0; @endphp
        <tr class="contain">
            <td class="sub-contain center-text">{{ $cpmk->kode }}</td>
            @foreach ($instrumens as $ins)
                @php
                    $nilai = $penilaianMap[$cpmk->kode][$ins->nama] ?? 0;
                    $total += $nilai;
                    $sumPerInstrumen[$ins->nama] += $nilai;
                @endphp
                <td class="sub-contain center-text">{{ $nilai }}</td>
            @endforeach
            {{-- Handle jika instrumen kosong --}}
            @if($instrumens->count() == 0)
                <td class="sub-contain center-text">-</td>
            @endif
            <td class="sub-contain center-text">{{ $total }}</td>
        </tr>
        @php $sumTotal += $total; @endphp
    @empty
        <tr class="contain">
            <td class="sub-contain center-text">-</td>
            <td class="sub-contain center-text" colspan="{{ $instrumens->count() > 0 ? $instrumens->count() : 1 }}">-</td>
            <td class="sub-contain center-text">0</td>
        </tr>
    @endforelse

    <tr class="sub contain subtitle grey" style="font-weight:bold;">
        <th class="sub-contain center-text">Total</th>
        @forelse ($instrumens as $ins)
            <td class="sub-contain center-text">{{ $sumPerInstrumen[$ins->nama] }}</td>
        @empty
             <td class="sub-contain center-text">-</td>
        @endforelse
        <td class="sub-contain center-text">{{ $sumTotal }}</td>
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

    {{-- HEADER KEGIATAN MINGGUAN --}}
    </tbody>
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
                <tr class="contain">
                    <td class="sub-contain center-text" style="vertical-align: middle;">{{ $activity->minggu }}</td>

                    <td class="sub-contain center-text">
                        {{ $activity->cpmk_details->pluck('kode')->implode(', ') ?: '-' }}
                    </td>

                    <td class="sub-contain" style="vertical-align: middle;"> 
                         
                        @php
                            // 1) Normalisasi sub_cpmk dan id_cpmk (kadang masih string JSON)
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
                    
                            // 2) Bangun list yang akan ditampilkan
                            $subTextList = [];
                    
                            // Jika ada flag 0 -> tampilkan judul CPMK
                            if(in_array(0, array_map('intval', $subArr), true)) {
                                foreach ($idCpmkArr as $cid) {
                                    $judul = optional($cpmks->firstWhere('id', (int)$cid))->judul;
                                    if($judul) $subTextList[] = $judul;
                                }
                            } else {
                                // Normal: tampilkan uraian sub-cpmk
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
                        @php $lurList = !empty($activity->kegiatan_luring) && is_array($activity->kegiatan_luring) ? array_values(array_filter($activity->kegiatan_luring)) : []; @endphp
                        @if(count($lurList) > 0)
                            @foreach($lurList as $val) {{ $val }}<br> @endforeach
                        @else
                            -
                        @endif
                    </td>

                    <td class="sub-contain" style="vertical-align: middle;">
                        @php $darList = !empty($activity->kegiatan_daring) && is_array($activity->kegiatan_daring) ? array_values(array_filter($activity->kegiatan_daring)) : []; @endphp
                        @if(count($darList) > 0)
                            @foreach($darList as $val) {{ $val }}<br> @endforeach
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
</table>
<script>window.print();</script>
</body>