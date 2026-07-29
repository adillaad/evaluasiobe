{{-- @php
    $routePrefix = [
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp --}}
@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')
    <h3 class="px-4 pb-4 fw-bold text-center">Halaman Bahan Kajian</h3>
    @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
        <div class="container-fluid mb-3">
            <div class="card">
                <div class="card-body">
                    {{-- <div id="BK"></div> --}}
                    <form action="{{ route($currentPrefix . 'bk.bk-store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="nama">Bahan Kajian :</label>
                            <input type="text" value="{{ old('nama') }}" name="nama" id="nama" class="form-control"
                                placeholder="Nama Bahan Kajian">
                            @error('nama')
                                <div class="alert alert-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="kurikulum_id">Kurikulum :</label>
                            <select name="kurikulum_id" id="kurikulum_id" class="form-control">
                                <option value="">-- Pilih Kurikulum --</option>
                                @foreach ($kurikulums as $kurikulum)
                                    <option value="{{ $kurikulum->id }}"
                                        {{ old('kurikulum_id') == $kurikulum->id ? 'selected' : '' }}>{{ $kurikulum->tahun }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kurikulum_id')
                                <div class="alert alert-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="rumpun">Rumpun BK :</label>
                            <textarea name="rumpun" id="rumpun" class="form-control" style="height: 100px" placeholder="Rumpun Bahan Kajian">{{ old('rumpun') }}</textarea>
                            @error('rumpun')
                                <div class="alert alert-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Tambah Bahan Kajian</button>
                    </form>
                </div>
            </div>
        </div>    
    @endif
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                {{-- <div id="BK"></div> --}}
                <h4 class="card-title">List Bahan Kajian</h4>
                <form method="GET" action="{{ route($currentPrefix . 'bk.index') }}">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <select name="kurikulum_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Pilih Kurikulum --</option>
                                @foreach ($kurikulums as $kurikulum)
                                    <option value="{{ $kurikulum->id }}"
                                        {{ request('kurikulum_id') == $kurikulum->id ? 'selected' : '' }}>
                                        {{ $kurikulum->tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
                <div class="table-responsive mt-4">
                    <table class="table table-hover dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Kode BK</th>
                                <th>Nama BK</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $section=0 @endphp
                            @foreach ($bks as $rumpun => $items)
                                @php $section++ @endphp
                                <tr>
                                    <td colspan="3" style=" text-align:left;">{{ chr(64 + $section) }}.
                                        {{ $rumpun }}</td>
                                </tr>
                                @foreach ($items as $index => $bk)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $bk->kode }}</td>
                                        <td>{{ $bk->nama }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
