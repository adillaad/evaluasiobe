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

    <h3 class="px-4 pb-4 fw-bold text-center">Halaman MK-CPMK-Sub CPMK</h3>
    @if (session()->has('failed'))
        <div class="alert alert-danger" role="alert" id="box">
            <div>{{ session('failed') }}</div>
        </div>
    @elseif (session()->has('success'))
        <div class="alert greenAdd" role="alert" id="box">
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
        <div class="container-fluid mb-3">
        <div class="card">
            <div class="card-body">
                {{-- <div id="BK"></div> --}}
                <form action="{{ route($currentPrefix. 'cpl-cpmk.subCpmk-store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="kurikulum_id">Kurikulum :</label>
                        <select name="kurikulum_id" id="kurikulum_id" class="form-control">
                            <option value="">-- Pilih Kurikulum --</option>
                            @foreach ($kurikulums as $kurikulum)
                                <option value="{{ $kurikulum->id }}" {{ old('kurikulum_id') == $kurikulum->id ? 'selected' : '' }}>{{ $kurikulum->tahun }}</option>    
                            @endforeach
                        </select>
                        @error('kurikulum_id')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="cpmk_id">CPMK :</label>
                        <input type="hidden" name="cpmk_kode" id="cpmk_kode">
                        <select name="cpmk_id" id="cpmk_id" class="form-control" required>
                            <option value="">-- Pilih CPMK --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="uraian">Uraian Sub CPMK :</label>
                        <textarea name="uraian" id="uraian" class="form-control" style="height: 100px" placeholder="Uraian Sub CPMK">{{ old('uraian') }}</textarea>
                        @error('uraian')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
            
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
        </div>
    @endif
    
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">List Pemetaan MK-CPMK-Sub CPMK</h4>
                @if(in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                <a href="{{ route($currentPrefix. 'cpl-cpmk.cpmk-mk-subcpmk-add') }}"
                class="btn btn-primary">Tambah MK-Sub Cpmk</a>
                @endif
                <div class="table-responsive mt-4" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-hover ">
                        <thead style="position: sticky; top: 0; z-index: 10;" class="bg-light">
                            <tr>
                                <th>MK</th>
                                <th>CPMK</th>
                                <th>Deskripsi CPMK</th>
                                <th>Sub CPMK</th>
                                <th>Uraian Sub CPMK</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mks as $mk)
                                @php
                                    // Total SubCPMK valid
                                    $totalSubCpmk = $mk->cpmks->sum(function ($cpmk) use ($mk) {
                                        return $cpmk->subCpmks
                                            ->filter(function ($subCpmk) use ($mk) {
                                                return $subCpmk->mks->contains('kode', $mk->kode);
                                            })
                                            ->count();
                                    });

                                    // CPMK tanpa SubCPMK valid
                                    $cpmkWithoutSubCpmk = $mk->cpmks
                                        ->filter(function ($cpmk) use ($mk) {
                                            // Pastikan CPMK memiliki cpl_id yang sesuai dengan hubungan MK-CPL
                                            $isValidCPL = $mk->cpl->contains('id', $cpmk->cpl_id);

                                            if (!$isValidCPL) {
                                                return false;
                                            }

                                            // Periksa apakah CPMK tidak memiliki SubCPMK yang valid
                                            return $cpmk->subCpmks
                                                ->filter(function ($subCpmk) use ($mk) {
                                                    return $subCpmk->mks->contains('kode', $mk->kode); // Cek hubungan MK-SubCPMK
                                                })
                                                ->isEmpty();
                                        })
                                        ->count();
                                    $rowspanMK = $totalSubCpmk + $cpmkWithoutSubCpmk ?: 1;
                                @endphp
                                <tr>
                                    <td rowspan="{{ $rowspanMK }}">{{ $mk->kode }} </td>
                                    @php
                                        // Filter CPMK yang valid berdasarkan CPL
                                        $validCpmks = $mk->cpmks
                                            ->filter(function ($cpmk) use ($mk) {
                                                return $mk->cpl->contains('id', $cpmk->cpl_id);
                                            })
                                            ->values();

                                    @endphp

                                    @foreach ($validCpmks as $validIndex => $cpmk)
                                        @if ($validIndex > 0)
                                <tr>
                            @endif
                            @php
                                // Hitung rowspan untuk SubCPMK yang valid
                                $rowspanCPMK =
                                    $cpmk->subCpmks
                                        ->filter(function ($subCpmk) use ($mk) {
                                            return $subCpmk->mks->contains('kode', $mk->kode);
                                        })
                                        ->count() ?:
                                    1;
                            @endphp

                            <td rowspan="{{ $rowspanCPMK }}">{{ $cpmk->kode }} </td>
                            <td rowspan="{{ $rowspanCPMK }}" style="word-wrap:break-word; white-space:normal;">
                                {{ $cpmk->judul }}</td>

                            @foreach ($cpmk->subCpmks->filter(function ($subCpmk) use ($mk) {
            return $subCpmk->mks->contains('kode', $mk->kode);
        }) as $indexSubCpmk => $subCpmk)
                                @if ($indexSubCpmk > 0)
                                    <tr>
                                @endif
                                <td>{{ $subCpmk->kode }}</td>
                                <td style="word-wrap:break-word; white-space:normal;">{{ $subCpmk->uraian }}</td>
                                @if ($indexSubCpmk < $cpmk->subCpmks->count() - 1)
                                    </tr>
                                @endif
                            @endforeach

                            @if ($cpmk->subCpmks->isEmpty())
                                <td colspan="2">Tidak ada Sub-CPMK terkait</td>
                            @endif

                            @if ($validIndex < $validCpmks->count() - 1)
                                </tr>
                            @endif
                            @endforeach
                            @if ($mk->cpmks->isEmpty())
                                <td colspan="4">Tidak ada CPMK terkait</td>
                            @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function () {
            const kurikulumElement = document.getElementById("kurikulum_id");
            const cpmkSelect = document.getElementById("cpmk_id");
            const hiddenCpmkKode = document.getElementById("cpmk_kode");
            const otoritas = "{{ $userOtoritas }}";
        
            function getSelectedValue() {
                const kurikulumId = kurikulumElement.value;
                console.log("Kurikulum ID:", kurikulumId);
        
                if (!kurikulumId) {
                    cpmkSelect.innerHTML = '<option value="" disabled>Kurikulum ID tidak valid.</option>';
                    hiddenCpmkKode.value = '';
                    return;
                }
        
                let urlget = "";
                if (otoritas === "Kepala Program Studi") {
                    urlget = `/kepala-program-studi/cpl-cpmk/get-cpmk-by-kurikulum/${kurikulumId}`;
                } else if (otoritas === "Penjamin Mutu Program Studi") {
                    urlget = `/penjamin-mutu/program-studi/cpl-cpmk/get-cpmk-by-kurikulum/${kurikulumId}`;
                }
        
                cpmkSelect.innerHTML = '<option>Loading...</option>';
                hiddenCpmkKode.value = '';
        
                $.ajax({
                    url: urlget,
                    method: 'GET',
                    success: function (data) {
                        console.log("Data CPMK:", data);
                        cpmkSelect.innerHTML = '<option value="">-- Pilih CPMK --</option>';
        
                        if (data.cpmks.length > 0) {
                            data.cpmks.forEach(cpmk => {
                                cpmkSelect.innerHTML += `<option value="${cpmk.id}">${cpmk.kode} - ${cpmk.judul}</option>`;
                            });
                        } else {
                            cpmkSelect.innerHTML = '<option value="" disabled>Tidak ada data CPMK untuk kurikulum yang dipilih.</option>';
                        }
                    },
                    error: function (xhr, status, error) {
                        let errorMessage = 'Gagal memuat data CPMK.';
                        if (xhr.responseJSON?.message) {
                            errorMessage += `\nDetail: ${xhr.responseJSON.message}`;
                        } else if (xhr.responseText) {
                            errorMessage += `\nDetail: ${xhr.responseText}`;
                        } else {
                            errorMessage += `\nStatus: ${status}, Error: ${error}`;
                        }
                        alert(errorMessage);
                        cpmkSelect.innerHTML = '<option value="" disabled>Gagal memuat data.</option>';
                        hiddenCpmkKode.value = '';
                    }
                });
            }
        
            // Event listener saat CPMK dipilih — hanya pasang satu kali
            cpmkSelect.addEventListener('change', function () {
                const selectedId = this.value;
                const selectedText = this.options[this.selectedIndex]?.textContent;
                const kode = selectedText?.split(" - ")[0] ?? '';
                hiddenCpmkKode.value = kode;
            });
        
            // Pasang listener kurikulum
            if (kurikulumElement) {
                kurikulumElement.addEventListener("change", getSelectedValue);
            }
        });
        </script>
@endsection
