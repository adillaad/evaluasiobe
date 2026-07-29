@extends(auth()->user()->otoritas->otoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')

@section('content')
<div class="container my-4">
    <h4 class="text-center mb-4">Tabel G - Contoh Proses Penilaian dan Evaluasi CPMK</h4>

    <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
        <table class="table table-bordered text-center align-middle" style="min-width:1000px;">

            {{-- HEADER LEVEL 1: Nama Mahasiswa + MK --}}
            <thead>
                <tr>
                    <th rowspan="4" style="vertical-align: middle; min-width:150px;">Nama Mahasiswa</th>
                    @foreach($mks as $mk)
                        @php
                            $mkCpls = $cplMk->get($mk->kode) ?? collect();
                            $cplUnik = $mkCpls->unique('cpl_kode');
                            // Hitung total CPMK di MK ini untuk colspan
                            $totalCpmkMk = 0;
                            foreach ($cplUnik as $cpl) {
                                $cpmks = collect($cpmkRelations[$mk->kode] ?? [])
                                    ->where('cpl_id', $cpl->cpl_kode)->flatten(1);
                                $totalCpmkMk += $cpmks->count();
                            }
                        @endphp
                        <th colspan="{{ $totalCpmkMk + 1 }}" style="background-color: #99ccff; min-width: 150px;">
                            {{ $mk->kode }}
                        </th>
                    @endforeach
                </tr>

                {{-- HEADER LEVEL 2: CPL di bawah MK --}}
                <tr>
                    @foreach($mks as $mk)
                        @php
                            $mkCpls = $cplMk->get($mk->kode) ?? collect();
                            $cplUnik = $mkCpls->unique('cpl_kode');
                        @endphp
                        @foreach($cplUnik as $cpl)
                            @php
                                $cpmks = collect($cpmkRelations[$mk->kode] ?? [])  
                                ->where('cpl_id', $cpl->cpl_kode);
                                $countCpmk = $cpmks->count();
                            @endphp
                            <th colspan="{{ $countCpmk }}" style="background-color: #7bed9f;">{{ $cpl->cpl_kode }}</th>
                        @endforeach
                        <th rowspan="2" style="background-color: #99ccff; vertical-align: middle;">Nilai {{ $mk->kode }}</th>
                    @endforeach
                </tr>

                {{-- HEADER LEVEL 3: CPMK di bawah CPL --}}
                <tr>
                    @foreach($mks as $mk)
                        @php
                            $mkCpls = $cplMk->get($mk->kode) ?? collect();
                            $cplUnik = $mkCpls->unique('cpl_kode');
                        @endphp
                        @foreach($cplUnik as $cpl)
                            @php
                                $cpmks = collect($cpmkRelations[$mk->kode] ?? [])
                                    ->where('cpl_id', $cpl->cpl_kode)->flatten(1);
                            @endphp
                            @foreach($cpmks as $cpmk)
                                <th style="background-color: #ff6b6b;">{{ is_object($cpmk) ? $cpmk->cpmk_kode : $cpmk }}</th>
                            @endforeach
                        @endforeach
                    @endforeach
                </tr>

                {{-- HEADER LEVEL 4: Nilai Total CPMK --}}
                <tr>
                    <th>Nilai Total</th>
                    @foreach($mks as $mk)
                        @php
                            $mkCpls = $cplMk->get($mk->kode) ?? collect();
                            $cplUnik = $mkCpls->unique('cpl_kode');
                        @endphp
                        @foreach($cplUnik as $cpl)
                            @php
                                $cpmks = collect($cpmkRelations[$mk->kode] ?? [])
                                    ->where('cpl_id', $cpl->cpl_kode)->flatten(1);
                            @endphp
                            @foreach($cpmks as $cpmk)
                                <th style="background-color: #a4b0be;">{{ 15 /* Ganti sesuai bobot nilai maksimal CPMK jika ada */ }}</th>
                            @endforeach
                        @endforeach
                        <th style="background-color: #57606f;">100</th>
                    @endforeach
                </tr>
            </thead>

            {{-- BODY: Data Mahasiswa dan nilai --}}
            <tbody>
                @foreach($mahasiswas as $mhs)
                <tr>
                    <td style="background-color:#f1f2f6; font-weight: 600;">{{ $mhs->nama_mhs }}</td>
                    @foreach($mks as $mk)
                        @php
                            $mkCpls = $cplMk->get($mk->kode) ?? collect();
                            $cplUnik = $mkCpls->unique('cpl_kode');
                        @endphp
                        @foreach($cplUnik as $cpl)
                            @php
                                $cpmks = collect($cpmkRelations[$mk->kode] ?? [])
                                    ->where('cpl_id', $cpl->cpl_kode)->flatten(1);
                            @endphp
                            @foreach($cpmks as $cpmk)
                                @php
                                    $nilai = 0;
                                    if (isset($nilaiCpmk[$mhs->NPM])) {
                                        // Cari nilai CPMK tertentu pada mahasiswa ini
                                        $record = $nilaiCpmk[$mhs->NPM]->firstWhere('cpmk', $cpmk->cpmk_kode ?? $cpmk);
                                        $nilai = $record->nilai ?? 0;
                                    }
                                @endphp
                                <td>{{ $nilai }}</td>
                            @endforeach
                        @endforeach
                        {{-- Nilai total MK --}}
                        <td style="font-weight: 700;">{{ $nilaiMkMahasiswa[$mhs->NPM][$mk->kode] ?? 0 }}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>

    <div class="mt-3 px-3 py-2 bg-warning rounded">
        <p><b>Contoh penjelasan Tabel G:</b> Mahasiswa 1 mendapat nilai 66,67 pada CPMK021 dikalikan dengan bobot CPMK021 (30%) maka mahasiswa 1 mendapatkan nilai CPMK011 = 20, untuk CPMK022 mendapat nilai 66,67 juga dikalikan dengan bobot CPMK022 (30%) sehingga nilai CPMK022 = 20, dan CPMK031 mendapat nilai 75 dikalikan bobot CPMK031 (40%) sehingga mahasiswa mendapat nilai mata kuliah MK01 sebesar 70.</p>
    </div>
</div>
@endsection