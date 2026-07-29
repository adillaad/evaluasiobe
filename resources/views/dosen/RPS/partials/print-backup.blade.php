<head>
    <meta charset="UTF-8">
    <title>RPS Print</title>
    <style>
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
            font-family: Calibri;
            font-size: 11pt;
            text-align: left;
        }

        .cpl-contain {
            font-family: Calibri;
            font-size: 8pt;
            text-align: left;
        }

        .title-cpmk {
            font-family: Calibri;
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
            font-family: Calibri;
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
            font-family: Calibri;
            font-size: 11pt;
            padding: 5px 0;
            text-align: center;
        }

        .d-flex {
            display: -webkit-box;
            display: flex;
            width: 100%
        }
    </style>
</head>
<table>
    <tr class="title blue">
        <th colspan="2" rowspan="1" style="width:12%" class="title-sub"><img style="width:1.91cm" src="{{public_path('/assets/img/logo_unila.png')}}" alt=""></td>
        <th colspan="7" rowspan="1" class="title-sub" style="width:76%;font-size:16pt;">
            <div class="row">
                <div class="col">UNIVERSITAS LAMPUNG</div>
                <div class="col">FAKULTAS MATEMATIKA DAN ILMU PENGETAHUAN ALAM</div>
                <div class="col">JURUSAN ILMU KOMPUTER</div>
                <div class="col">PRODI <?= strtoupper($rps->prodi->nama) ?></div>
            </div>
        </th>
        <th colspan="1" rowspan="1" class="title-sub" style="width:12%;font-size: 12pt; vertical-align: text-top;">
            <div class="row">
                <div class="col">RPS</div>
                <div class="col">{{$rps->nomor}}</div>
            </div>
        </th>
    </tr>
    <tr class="title blue" style="font-size:14pt">
        <th colspan="10">RENCANA PEMBELAJARAN SEMESTER</th>
    </tr>
    <tr class="contain">
        <th colspan="3" rowspan="1" class="sub-contain subtitle grey" style="width:30%">MATA KULIAH (MK)</th>
        <th colspan="2" rowspan="1" class="sub-contain subtitle grey" style="width:20%">KODE</th>
        <th colspan="1" rowspan="1" class="sub-contain subtitle grey" style="width:13%">Rumpun MK</th>
        <th colspan="2" rowspan="1" class="sub-contain subtitle grey" style="width:15%">Bobot (sks)</th>
        <th colspan="1" rowspan="1" class="sub-contain subtitle grey" style="width:13%">SEMESTER</th>
        <th colspan="1" rowspan="1" class="sub-contain subtitle grey" style="width:10%">Tgl Penyusunan</th>
    </tr>
    @php
    foreach($mks as $mk):
    if($rps->id_mk == $mk->id) {
    $nama = $mk->nama;
    $kode_mk = $mk->kode;
    $rumpun_mk = $mk->rumpun;
    $bobot_t = $mk->bobot_teori;
    $bobot_p = $mk->bobot_praktikum;
    $tanggal = $mk->created_at;
    $prasyarat = $mk->prasyarat;
    }
    endforeach
    @endphp
    <tr class="contain">
        <th colspan="3" rowspan="1" class="sub-contain" style="width: 30%">{{$nama}}</th>
        <td colspan="2" rowspan="1" class="sub-contain" style="width:20%">{{$kode_mk}}</td>
        <td colspan="1" rowspan="1" class="sub-contain" style="width:13%">MK {{$rumpun_mk}}</td>
        <th colspan="1" rowspan="1" style="width:7.5%; text-align:center">T={{$bobot_t}}</th>
        <th colspan="1" rowspan="1" style="width:7.5%; text-align:center">P={{$bobot_p}}</th>
        <td colspan="1" rowspan="1" style="width:13%; text-align:center"">{{$rps->semester}}/8</td>
        <td colspan=" 1" rowspan="1" class="sub-contain" style="width:10%">{{date("d-m-Y",strtotime($tanggal))}}</td>
    </tr>
    <tr class="contain">
        <th colspan="3" rowspan="2" class="sub-contain" style="vertical-align:top">OTORISASI</th>
        <th colspan="2" class="subtitle grey" style="text-align:center">Pengembang RPS</th>
        <th colspan="3" class="subtitle grey" style="text-align:center">Koordinator RMK</th>
        <th colspan="2" class="subtitle grey" style="text-align:center">Ketua PRODI</th>
    </tr>
    <tr class="contain">
        <th colspan="2" style="height: 60px;text-align:center;vertical-align:bottom;">{{$rps->pengembang}}</th>
        <th colspan="3" style="height: 60px;text-align:center;vertical-align:bottom;">{{$rps->koordinator}}</th>
        <th colspan="2" style="height: 60px;text-align:center;vertical-align:bottom;">{{$rps->kaprodi}}</th>
    </tr>
    @php
    $no = 0;
    $noS = 0;
    $noU = 0;
    $noP = 0;
    $noK = 0;

    foreach($sikaps as $sik):
    ++$noS;
    endforeach;
    foreach($umums as $um):
    ++$noU;
    endforeach;
    foreach($pengetahuans as $peng):
    ++$noP;
    endforeach;
    foreach($keterampilans as $ket):
    ++$noK;
    endforeach;
    foreach($cpmks as $cpmk):
    if($cpmk->id_mk == $rps->id_mk) {
    ++$no;
    }
    endforeach;
    foreach($activities as $activity):
    if($activity->id_rps == $rps->id) {
    ++$no;
    }
    endforeach;
    @endphp
    <tr class="contain">
        <th class="sub-contain" colspan="2" rowspan="{{$no+9}}" style="vertical-align:top">
            <div class="row">
                <div class="col">Capaian</div>
                <div class="col">Pembelajaran (CP)</div>
            </div>
        </th>
        <th class="subtitle grey" style="text-align:center" colspan="8">CPL-PRODI yang dibebankan pada MK</th>
    </tr>
    <tr>
        @if($noS > 0)
        <td class="title-cpl" colspan="4" style="border-bottom:none">SIKAP</td>
        @else
        <td class="title-cpl" colspan="4">SIKAP</td>
        @endif
        @if($noU > 0)
        <td class="title-cpl" colspan="4" style="border-bottom:none">UMUM</td>
        @else
        <td class="title-cpl" colspan="4">UMUM</td>
        @endif
    </tr>
    <tr class="cpl-contain">
        <td style="padding:0; border-top:none; border-left:none; border-bottom:none" colspan='4'>
            <table style="border:none; width: 100%">
                @foreach($sikaps as $sikap)
                <tr>
                    <td class="cpl-contain" style="padding: 0; border-left:none; border-right:none">
                        <div class="d-flex"">
                            <div style=" width:10%; height:50px;border-right:1px black solid">
                            <div class="sub-contain" style="padding-right:8px;">{{$sikap->kode}}</div>
                        </div>
                        <div class="sub-contain" style="width:90%;height:50px;">
                            {{$sikap->judul}}
                        </div>
                        </div>
                    </td>
                </tr>
                @endforeach
                @if($noS < $noU) <?php for ($i = 0; $i < $noU - $noS; $i++) { ?> <tr style="border: none;">
                    <td class="cpl-contain" style="padding: 0; border-right:none;border-left:none">
                        <div class="d-flex"">
                            <div style=" width:10%; height:50px;border-right:1px black solid">
                            <div class="sub-contain" style="padding-right:8px;"></div>
                        </div>
                        <div class="sub-contain" style="width:90%;height:50px;">

                        </div>
                    </td>
    </tr>
<?php } ?>
@endif
</table>
</td>
<td style="padding:0; border:none" colspan='4'>
    <table style="border:none; width:100%">
        @foreach($umums as $umum)
        <tr style="border: none;">
            <td class="cpl-contain" style="padding: 0; border-right:none;border-left:none">
                <div class="d-flex"">
                    <div style=" width:10%; height:50px;border-right:1px black solid">
                    <div class="sub-contain" style="padding-right:8px;">{{$umum->kode}}</div>
                </div>
                <div class="sub-contain" style="width:90%;height:50px;">
                    {{$umum->judul}}
                </div>
            </td>
        </tr>
        @endforeach
        @if($noU < $noS) <?php for ($i = 0; $i < $noS - $noU; $i++) { ?> <tr style="border: none;">
            <td class="cpl-contain" style="padding: 0; border-right:none;border-left:none">
                <div class="d-flex"">
                            <div style=" width:10%; height:50px;border-right:1px black solid">
                    <div class="sub-contain" style="padding-right:8px;"></div>
                </div>
                <div class="sub-contain" style="width:90%;height:50px;">

                </div>
            </td>
            </tr>
        <?php } ?>
        @endif
    </table>
</td>
</tr>
<tr>
    @if($noP > 0 && $noK > 0)
    <td class="title-cpl" colspan="4" style="border-bottom:none; border-top:none">PENGETAHUAN</td>
    <td class="title-cpl" colspan="4" style="border-bottom:none; border-top:none">KETERAMPILAN KHUSUS</td>
    @else
    <td class="title-cpl" colspan="4">PENGETAHUAN</td>
    <td class="title-cpl" colspan="4">KETERAMPILAN KHUSUS</td>
    @endif
</tr>
<tr class="cpl-contain break">
    <td style="padding:0; border-top:none; border-left:none; border-bottom:none" colspan='4'>
        <table style="border:none; width:100%">
            @foreach($pengetahuans as $pengetahuan)
            <tr>
                <td class="cpl-contain" style="padding: 0; border-right:none; border-left:none;">
                    <div class="d-flex"">
                            <div style=" width:10%; height:50px;border-right:1px black solid">
                        <div class="sub-contain" style="padding-right:8px;">{{$pengetahuan->kode}}</div>
                    </div>
                    <div class="sub-contain" style="width:90%;height:50px;">
                        {{$pengetahuan->judul}}
                    </div>
                    </div>
                </td>
            </tr>
            @endforeach
            @if($noP < $noK) <?php for ($i = 0; $i < $noK - $noP; $i++) { ?> <tr style="border: none;">
                <td class="cpl-contain" style="padding: 0; border-right:none;border-left:none;">
                    <div class="d-flex"">
                            <div style=" width:10%; height:50px;border-right:1px black solid">
                        <div class="sub-contain " style="padding-right:8px;"></div>
                    </div>
                    <div class="sub-contain " style="width:90%;height:50px;">
                </td>
</tr>
<?php } ?>
@endif
</table>
</td>
<td style="padding:0; border:none" colspan='4'>
    <table style="border:none; width:100%">
        @foreach($keterampilans as $keterampilan)
        <tr style="border: none;">
            <td class="cpl-contain" style="padding: 0; border-right:none; border-left:none">
                <div class="d-flex"">
                            <div style=" width:10%; height:50px;border-right:1px black solid">
                    <div class="sub-contain" style="padding-right:8px;">{{$keterampilan->kode}}</div>
                </div>
                <div class="sub-contain" style="width:90%;height:50px;">
                    {{$keterampilan->judul}}
                </div>
            </td>
        </tr>
        @endforeach
        @if($noK < $noP) <?php for ($i = 0; $i < $noP - $noK; $i++) { ?> <tr style="border: none;">
            <td class="cpl-contain" style="padding: 0; border-right:none; border-left:none">
                <div class="d-flex"">
                            <div style=" width:10%; height:50px;border-right:1px black solid">
                    <div class="sub-contain" style="padding-right:8px;"></div>
                </div>
                <div class="sub-contain" style="width:90%;height:50px;">
            </td>
            </tr>
        <?php } ?>
        @endif
    </table>
</td>
</tr>
<tr class="contain">
    <th class="sub-contain subtitle grey" colspan="3" style="border-top:none">Capaian Pembelajaran Mata Kuliah (CPMK)</th>
    <td class="sub-contain" style="border-top:none" colspan=" 5"></td>
</tr>
<tr class="contain">
<tr class="contain">
    <td style="padding:0" colspan='8'>
        <div class="d-flex">
            <div style="width:8%;border-right:1px black solid">
                <div class="sub-contain" style="padding-right:8px">CPMK</div>
            </div>
            <div class="sub-contain" style="width:94%;">
                CPMK merupakan turunan/uraian spesifik dari CPL-PRODI yg berkaiatan dengan mata kuliah ini
            </div>
        </div>

    </td>
</tr>
@foreach($cpmks as $no=>$cpmk)
@if($cpmk->id_mk == $rps->id_mk)
<tr class="contain">
    <td style="padding:0" colspan='8'>
        <div class="d-flex">
            <div style="width:8%;border-right:1px black solid">
                <div class="sub-contain" style="padding-right:8px">CPMK-{{$no+1}}</div>
            </div>
            <div class="sub-contain" style="width:94%;">
                {{$cpmk->judul}}
            </div>
        </div>

    </td>
</tr>
@endif
@endforeach
<tr class="contain">
    <td class="sub-contain subtitle grey" colspan="3">CPMK => Sub-CPMK</td>
    <td class="sub-contain" colspan="5"></td>
</tr>
@foreach($activities as $no=>$activity)
@if($activity->id_rps == $rps->id)
<tr class="contain">
    <td style="padding:0" colspan='8'>
        <div class="d-flex">
            <div style="width:12%;border-right:1px black solid">
                <div class="sub-contain" style="padding-right:8px">Sub-CPMK-{{$no+1}}</div>
            </div>
            <div class="sub-contain" style="width:94%;">
                {{$activity->sub_cpmk}}
            </div>
        </div>

    </td>
</tr>
@endif
@endforeach
<tr class="contain">
    <th class="sub-contain" colspan="2" rowspan="1" style="vertical-align:top">
        <div class="row">
            <div class="col">Deskripsi Singkat</div>
            <div class="col">MK</div>
        </div>
    </th>
    <td class="sub-contain" colspan="8" style="vertical-align:top"><?= $rps->deskripsi_mk ?></td>
</tr>
<tr class="contain">
    <th class="sub-contain" colspan="2" rowspan="1" style="vertical-align:top">
        <div class="row">
            <div class="col">Materi Kajian / </div>
            <div class="col">Materi</div>
            <div class="col">Pembelajaran</div>
        </div>
    </th>
    <td class="sub-contain" colspan="8" style="vertical-align:top"><?= $rps->materi_mk ?></td>
</tr>
<tr class="contain">
    <th class="sub-contain" colspan="2" rowspan="4" style="vertical-align:top;">Pustaka</th>
    <th class="sub-contain subtitle grey" colspan="2">Utama :</th>
    <td class="sub-contain" colspan="6" style="vertical-align:top"></td>
</tr>
<tr class="contain">
    <td class="sub-contain" colspan="8"><?= $rps->pustaka_utama ?></td>
</tr>
<tr class="contain">
    <th class="sub-contain subtitle grey" colspan="2">Pendukung :</th>
    <td class="sub-contain" colspan="6" style="vertical-align:top;"></td>
</tr>
<tr class="contain">
    <td class="sub-contain" colspan="8"><?= $rps->pustaka_pendukung ?></td>
</tr>
<tr class="contain">
    <th class="sub-contain" colspan="2" style="vertical-align:top;">Dosen Pengampu</th>
    <td class="sub-contain" colspan="8" style="vertical-align:top">{{$rps->dosen}}</td>
</tr>
<tr class="contain">
    <th class="sub-contain" colspan="2" style="vertical-align:top;">Matakuliah syarat</th>
    <td class="sub-contain" colspan="8" style="vertical-align:top">{{$prasyarat}}</td>
</tr>
<tr class="title-cpmk grey">
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
@foreach($activities as $activity)
@if($activity->id_rps == $rps->id)
@if((int)$activity->minggu < 8) <tr class="contain">
    <td class="title-cpmk cpmk-contain" colspan="1">{{ $activity->minggu }}</td>
    <td class="cpmk-contain sub-contain" colspan="2">{{ implode(', ', (array) $activity->sub_cpmk) }}</td>
    <td class="cpmk-contain sub-contain" colspan="1">{{ implode(', ', (array) $activity->indikator) }}</td>
    <td class="cpmk-contain sub-contain" colspan="1">{{ $activity->kriteria }}</td>
    {{-- PERHATIAN: Saya ganti 'metode_luring' menjadi 'kegiatan_luring' sesuai perbaikan kita sebelumnya --}}
    <td class="cpmk-contain sub-contain" colspan="1">{{ implode(', ', (array) $activity->kegiatan_luring) }}</td>
    <td class="cpmk-contain sub-contain" colspan="2">{{ implode(', ', (array) $activity->kegiatan_daring) }}</td>
    <td class="cpmk-contain sub-contain" colspan="1">{{ implode(', ', (array) $activity->materi) }}</td>
    <td class="cpmk-contain sub-contain" colspan="1">{{ $activity->bobot }}</td>
    </tr>
@endif
    @endif
    @endforeach
    <tr class="contain">
        <td class="title-cpmk cpmk-contain" colspan="1">8</td>
        <th class="title-cpmk cpmk-contain" colspan="8">Ujian Tengah Semester</th>
        <td class="cpmk-contain sub-contain" colspan="1"></td>
    </tr>
    @foreach($activities as $activity)
    @if($activity->id_rps == $rps->id)
    @if((int)$activity->minggu > 8) <tr class="contain">
    <td class="title-cpmk cpmk-contain" colspan="1">{{ $activity->minggu }}</td>
    <td class="cpmk-contain sub-contain" colspan="2">{{ implode(', ', (array) $activity->sub_cpmk) }}</td>
    <td class="cpmk-contain sub-contain" colspan="1">{{ implode(', ', (array) $activity->indikator) }}</td>
    <td class="cpmk-contain sub-contain" colspan="1">{{ $activity->kriteria }}</td>
    {{-- PERHATIAN: Saya ganti 'metode_luring' menjadi 'kegiatan_luring' sesuai perbaikan kita sebelumnya --}}
    <td class="cpmk-contain sub-contain" colspan="1">{{ implode(', ', (array) $activity->kegiatan_luring) }}</td>
    <td class="cpmk-contain sub-contain" colspan="2">{{ implode(', ', (array) $activity->kegiatan_daring) }}</td>
    <td class="cpmk-contain sub-contain" colspan="1">{{ implode(', ', (array) $activity->materi) }}</td>
    <td class="cpmk-contain sub-contain" colspan="1">{{ $activity->bobot }}</td>
    </tr>
    @endif
    @endif
    @endforeach
    <tr class="contain">
        <td class="title-cpmk cpmk-contain" colspan="1">16</td>
        <th class="title-cpmk cpmk-contain" colspan="8">Ujian Akhir Semester</th>
        <td class="cpmk-contain sub-contain" colspan="1"></td>
    </tr>
    </table>
    <div class="note">
        <div style="font-weight:700; text-decoration:underline">Catatan :</div>
        <ol>
            <li class="note-list">
                <div class="note-contain">
                    <span class="list-title">Capaian Pembelajaran Lulusan PRODI (CPL-PRODI) </span>
                    <span class="list-contain">adalah kemampuan yang dimiliki oleh setiap lulusan PRODI yang merupakan internalisasi dari sikap, penguasaan pengetahuan dan ketrampilan sesuai dengan jenjang prodinya yang diperoleh melalui proses pembelajaran.</span>
                </div>
            </li>
            <li class="note-list">
                <div class="note-contain">
                    <span class="list-title">CPL yang dibebankan pada mata kuliah </span>
                    <span class="list-contain">adalah beberapa capaian pembelajaran lulusan program studi (CPL-PRODI) yang digunakan untuk pembentukan/pengembangan sebuah mata kuliah yang terdiri dari aspek sikap, ketrampulan umum, ketrampilan khusus dan pengetahuan.</span>
                </div>
            </li>
            <li class="note-list">
                <div class="note-contain">
                    <span class="list-title">CP Mata kuliah (CPMK) </span>
                    <span class="list-contain">adalah kemampuan yang dijabarkan secara spesifik dari CPL yang dibebankan pada mata kuliah, dan bersifat spesifik terhadap bahan kajian atau materi pembelajaran mata kuliah tersebut.</span>
                </div>
            </li>
            <li class="note-list">
                <div class="note-contain">
                    <span class="list-title">Sub-CP Mata kuliah (Sub-CPMK) </span>
                    <span class="list-contain">adalah kemampuan yang dijabarkan secara spesifik dari CPMK yang dapat diukur atau diamati dan merupakan kemampuan akhir yang direncanakan pada tiap tahap pembelajaran, dan bersifat spesifik terhadap materi pembelajaran mata kuliah tersebut.</span>
                </div>
            </li>
            <li class="note-list">
                <div class="note-contain">
                    <span class="list-title">Indikator penilaian </span>
                    <span class="list-contain">kemampuan dalam proses maupun hasil belajar mahasiswa adalah pernyataan spesifik dan terukur yang mengidentifikasi kemampuan atau kinerja hasil belajar mahasiswa yang disertai bukti-bukti.</span>
                </div>
            </li>
            <li class="note-list">
                <div class="note-contain">
                    <span class="list-title">Kreteria Penilaian </span>
                    <span class="list-contain">adalah patokan yang digunakan sebagai ukuran atau tolok ukur ketercapaian pembelajaran dalam penilaian berdasarkan indikator-indikator yang telah ditetapkan. Kreteria penilaian merupakan pedoman bagi penilai agar penilaian konsisten dan tidak bias. Kreteria dapat berupa kuantitatif ataupun kualitatif.</span>
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
                    <span class="list-contain">Kuliah, Responsi, Tutorial, Seminar atau yang setara, Praktikum, Praktik Studio, Praktik Bengkel, Praktik Lapangan, Penelitian, Pengabdian Kepada Masyarakat dan/atau bentuk pembelajaran lain yang setara.</span>
                </div>
            </li>
            <li class="note-list">
                <div class="note-contain">
                    <span class="list-title">Metode Pembelajaran: </span>
                    <span class="list-contain">Small Group Discussion, Role-Play & Simulation, Discovery Learning, Self-Directed Learning, Cooperative Learning, Collaborative Learning, Contextual Learning, Project Based Learning, dan metode lainnya yg setara.</span>
                </div>
            </li>
            <li class="note-list">
                <div class="note-contain">
                    <span class="list-title">Materi Pembelajaran </span>
                    <span class="list-contain">adalah rincian atau uraian dari bahan kajian yg dapat disajikan dalam bentuk beberapa pokok dan sub-pokok bahasan.</span>
                </div>
            </li>
            <li class="note-list">
                <div class="note-contain">
                    <span class="list-title">Bobot penilaian </span>
                    <span class="list-contain">adalah prosentasi penilaian terhadap setiap pencapaian sub-CPMK yang besarnya proposional dengan tingkat kesulitan pencapaian sub-CPMK tsb., dan totalnya 100%.</span>
                </div>
            </li>
            <li class="note-list">
                <div class="note-contain">
                    <span class="list-contain">TM=Tatap Muka, PT=Penugasan terstruktur, BM=Belajar mandiri.</span>
                </div>
            </li>
        </ol>
    </div>
