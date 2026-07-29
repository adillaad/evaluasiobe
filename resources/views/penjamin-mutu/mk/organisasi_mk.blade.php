@extends(auth()->user()->otoritas->otoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')
    @if (session()->has('failed'))
        <div class="alert alert-danger" role="alert" id="box">
            <div>{{ session('failed') }}</div>
        </div>
    @elseif (session()->has('success'))
        <div class="alert greenAdd" role="alert" id="box">
            <div>{{ session('success') }}</div>
        </div>
    @endif
    <h3 class="px-4 pb-4 fw-bold text-center">Halaman Organisasi Mata Kuliah</h3>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                {{-- <div id="Organisasi MK"></div> --}}
                <h4 class="card-title">Organisasi Mata Kuliah</h4>
                <div class="table-responsive">
                    <table class="table table-hover dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th>SMT</th>
                                <th>SKS</th>
                                <th>JML MK</th>
                                <th>MK Kompetensi Utama Prodi</th>
                                <th>MK Pilihan</th>
                                <th>MK Wajib Kurikulum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($semesters->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data</td>
                                </tr>
                            @else
                                @foreach ($semesters as $semester)
                                    <tr>
                                        <td>{{ $semester->semester }}</td>
                                        <td>{{ $semester->total_sks }}</td>
                                        <td>{{ $semester->jumlah_mk }}</td>
                                        <td style="color:darkgreen">
                                            {{ !empty($semester->kode_wajib) ? str_replace(',', ', ', $semester->kode_wajib) : '-' }}
                                        </td>
                                        <td style="color:goldenrod;">
                                            {{ !empty($semester->kode_peminatan) ? str_replace(',', ', ', $semester->kode_peminatan) : '-' }}
                                        </td>
                                        <td style="color: deepskyblue">
                                            {{ !empty($semester->kode_wajib_kurikulum) ? str_replace(',', ', ', $semester->kode_wajib_kurikulum) : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            <tr>
                                <td>Total:</td>
                                <td>{{ $totals->total_sks ?? 0 }}</td>
                                <td>{{ $totals->jumlah_mk ?? 0 }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h4 class="card-title">Deskripsi</h4>
                <div class="scrollable-descriptions" style="max-height: 400px; overflow-y: auto;">
                    <h5>MK Kompetensi Utama Prodi:</h5>
                    <div class="scrollable-descriptions" style="max-height: 200px; overflow-y:auto;">
                        @foreach ($mks->where('rumpun', 'Wajib') as $mk)
                            <p>
                                <span style="color: darkgreen">{{ $mk->kode }}</span> - {{ $mk->nama }}
                            </p>
                        @endforeach
                    </div>
                    <hr>
                    <h5>MK Pilihan:</h5>
                    <div class="scrollable-descriptions" style="max-height: 200px; overflow-y:auto;">
                        @foreach ($mks->where('rumpun', 'Peminatan') as $mk)
                            <p><span style="color: goldenrod">{{ $mk->kode }}</span> - {{ $mk->nama }}</p>
                        @endforeach
                    </div>
                    <hr>
                    <h5>MK Wajib Kurikulum:</h5>
                    <div class="scrollable-descriptions" style="max-height: 200px; overflow-y:auto;">
                        @foreach ($mks->where('rumpun', 'wajib_kurikulum') as $mk)
                            <p><span style="color:deepskyblue">{{ $mk->kode }}</span> - {{ $mk->nama }}</p>
                        @endforeach
                    </div>
                    <hr>
                </div>
            </div>
        </div>
    </div>
@endsection
