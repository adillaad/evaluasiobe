@extends('dosen.template')
@section('content')
    <style>
        .ck.ck-button.ck-btn-equation {
            cursor: pointer;
            background: #f8f9fa;
            border: 1px solid #dee2e6 !important;
            display: inline-flex;
            align-items: center;
            border-radius: 4px;
            padding: 3px 8px !important;
            margin: 2px 4px;
            transition: all 0.15s ease-in-out;
        }

        .ck.ck-button.ck-btn-equation:hover {
            background: #e7f1ff !important;
            border-color: #0d6efd !important;
            color: #0d6efd !important;
        }

        .ck.ck-button.ck-btn-equation .ck-button__label {
            display: inline-block !important;
            font-size: 12px;
            font-weight: 600;
            color: #0d6efd;
            margin-left: 4px;
        }

        .ck.ck-button.ck-btn-equation .ck-button__icon {
            color: #0d6efd;
            vertical-align: middle;
        }

        /* Word-Style Equation Editor Styles */
        .word-ribbon-bar {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 12px;
        }

        .word-struct-btn {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 5px 8px;
            min-width: 66px;
            height: 60px;
            color: #1e293b;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            user-select: none;
        }

        .word-struct-btn:hover {
            background: #e0f2fe;
            border-color: #0284c7;
            color: #0369a1;
            box-shadow: 0 2px 5px rgba(2, 132, 199, 0.15);
            transform: translateY(-1px);
        }

        .word-struct-btn .preview-math {
            font-size: 16px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 2px;
            color: #0284c7;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .word-struct-btn .icon-label {
            font-size: 10px;
            font-weight: 600;
            color: #475569;
            text-align: center;
            white-space: nowrap;
        }

        .word-sym-btn {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 15px;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
            transition: all 0.12s ease-in-out;
            min-width: 32px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            user-select: none;
        }

        .word-sym-btn:hover {
            background: #0d6efd;
            border-color: #0d6efd;
            color: #ffffff;
            transform: scale(1.08);
        }

        math-field {
            font-size: 1.65rem;
            min-height: 95px;
            padding: 16px 20px;
            border-radius: 8px;
            border: 2px solid #3b82f6;
            background: #ffffff;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
            width: 100%;
            outline: none;
            display: block;
        }

        math-field:focus-within {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
        }

        #modalEditorRumus {
            z-index: 10600 !important;
        }

        .modal-backdrop {
            z-index: 10550 !important;
        }

        .ck-content img.math-tex,
        .ck-editor__editable img.math-tex,
        .ck-content img[src*="latex.codecogs.com"],
        .ck-editor__editable img[src*="latex.codecogs.com"] {
            cursor: pointer !important;
            border-radius: 4px;
            padding: 2px 4px;
            transition: all 0.15s ease-in-out;
        }

        .ck-content img.math-tex:hover,
        .ck-editor__editable img.math-tex:hover,
        .ck-content img[src*="latex.codecogs.com"]:hover,
        .ck-editor__editable img[src*="latex.codecogs.com"]:hover {
            outline: 2px solid #0d6efd !important;
            outline-offset: 1px;
            background-color: rgba(13, 110, 253, 0.08);
        }
    </style>
    <div class="container-fluid">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h5 class="fw-bold mb-0">Ubah Soal Mata Kuliah</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ url('dosen/soal/edit-soal/' . $soal->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">

                        {{-- Kurikulum --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kurikulum <span class="text-danger">*</span></label>
                            <select name="kurikulum" class="form-select" required>
                                <option value="">-- Pilih Kurikulum --</option>
                                @foreach ($kurikulum as $k)
                                    <option value="{{ $k->id }}"
                                        {{ $soal->kurikulumId == $k->id ? 'selected' : '' }}>
                                        {{ $k->tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Mata Kuliah --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mata Kuliah <span class="text-danger">*</span></label>
                            <select name="kode_mk" class="form-select" required>
                                <option value="" disabled>-- Pilih Mata Kuliah --</option>
                                @foreach ($rpss as $rps)
                                    <option value="{{ $rps->kode_mk }}"
                                        {{ $soal->kode_mk == $rps->kode_mk ? 'selected' : '' }}>
                                        {{ $rps->nama_mk ?? $rps->kode_mk }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Minggu --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Minggu <span class="text-danger">*</span></label>
                            <input type="number" name="minggu" min="1" max="16" value="{{ $soal->minggu }}"
                                class="form-control" required>
                        </div>

                        {{-- Jenis / Metode Penilaian --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jenis / Metode Penilaian <span
                                    class="text-danger">*</span></label>
                            <select name="jenis" class="form-select" required>
                                <option value="" disabled>-- Pilih Jenis --</option>
                                @foreach ($metodes as $metode)
                                    <option value="{{ $metode->id }}" {{ $soal->jenis == $metode->id ? 'selected' : '' }}>
                                        {{ $metode->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pertanyaan --}}
                        <div class="col-12">
                            <label for="pertanyaan" class="form-label fw-semibold">Pertanyaan <span class="text-danger">*</span></label>
                            <textarea name="pertanyaan" id="pertanyaan" class="form-control" rows="5" required>{{ $soal->pertanyaan }}</textarea>
                            <div class="form-text text-muted">
                                <i class="ti-info-alt me-1"></i>
                                Bobot soal akan dihitung otomatis saat download template penilaian.
                            </div>
                        </div>

                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary fw-semibold px-4">
                            <i class="ti-save me-1"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('dosen.soal-list') }}" class="btn btn-secondary fw-semibold px-4">
                            <i class="ti-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Editor Rumus Matematika (Word Equation Style) --}}
    <div class="modal fade" id="modalEditorRumus" tabindex="-1" aria-labelledby="modalEditorRumusLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-primary text-white py-3 px-4">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2 text-white" id="modalEditorRumusLabel">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-calculator" viewBox="0 0 16 16">
                            <path d="M12 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                            <path d="M4 2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5zm0 4a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"/>
                        </svg>
                        <span id="modalEditorRumusTitleText">Editor Rumus Matematika (Equation)</span>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 1.8rem; line-height: 1; opacity: 0.9; cursor: pointer; color: white;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    {{-- Nav Tabs Kategori Struktur Rumus (Word Style Ribbon) --}}
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted mb-1 text-uppercase" style="letter-spacing: 0.5px;">Pilih Bentuk Struktur Rumus:</label>
                        <ul class="nav nav-pills nav-fill mb-2 bg-light p-1 rounded-3" id="wordEqTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active py-1 px-2 small fw-semibold" id="tab-struktur-utama-tab" data-toggle="pill" data-bs-toggle="pill" data-target="#tab-struktur-utama" data-bs-target="#tab-struktur-utama" type="button" role="tab">Pecahan, Pangkat & Akar</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-1 px-2 small fw-semibold" id="tab-kalkulus-matriks-tab" data-toggle="pill" data-bs-toggle="pill" data-target="#tab-kalkulus-matriks" data-bs-target="#tab-kalkulus-matriks" type="button" role="tab">Integral, Sigma & Matriks</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-1 px-2 small fw-semibold" id="tab-kurung-fungsi-tab" data-toggle="pill" data-bs-toggle="pill" data-target="#tab-kurung-fungsi" data-bs-target="#tab-kurung-fungsi" type="button" role="tab">Kurung & Fungsi</button>
                            </li>
                        </ul>
                    </div>

                    {{-- Tab Contents: Tombol Struktur Visual Word --}}
                    <div class="tab-content border rounded-3 p-3 mb-3 bg-white" id="wordEqTabsContent" style="min-height: 80px;">
                        {{-- Pecahan, Pangkat & Akar --}}
                        <div class="tab-pane fade show active" id="tab-struktur-utama" role="tabpanel">
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <button type="button" class="word-struct-btn" data-template="\frac{#?}{#?}">
                                    <span class="preview-math"><span style="display:inline-flex;flex-direction:column;align-items:center;line-height:1;font-size:12px;"><span style="border-bottom:1.5px solid #0284c7;padding:0 3px;">□</span><span>□</span></span></span>
                                    <span class="icon-label">Pecahan</span>
                                </button>
                                <button type="button" class="word-struct-btn" data-template="\frac{d#?}{d#?}">
                                    <span class="preview-math" style="font-size:11px;"><sup>d□</sup>/<sub>d□</sub></span>
                                    <span class="icon-label">Turunan</span>
                                </button>
                                <button type="button" class="word-struct-btn" data-template="\frac{\partial #?}{\partial #?}">
                                    <span class="preview-math" style="font-size:11px;"><sup>∂□</sup>/<sub>∂□</sub></span>
                                    <span class="icon-label">Parsial</span>
                                </button>
                                <button type="button" class="word-struct-btn" data-template="^{#?}">
                                    <span class="preview-math">x<sup style="color:#0284c7;font-size:11px;">□</sup></span>
                                    <span class="icon-label">Pangkat</span>
                                </button>
                                <button type="button" class="word-struct-btn" data-template="_{#?}">
                                    <span class="preview-math">x<sub style="color:#0284c7;font-size:11px;">□</sub></span>
                                    <span class="icon-label">Indeks</span>
                                </button>
                                <button type="button" class="word-struct-btn" data-template="_{#?}^{#?}">
                                    <span class="preview-math">x<sub style="font-size:10px;">□</sub><sup style="color:#0284c7;font-size:10px;">□</sup></span>
                                    <span class="icon-label">Sub & Pangkat</span>
                                </button>
                                <button type="button" class="word-struct-btn" data-template="\sqrt{#?}">
                                    <span class="preview-math">&radic;<span style="border-top:1.5px solid #0284c7;padding:0 2px;">□</span></span>
                                    <span class="icon-label">Akar</span>
                                </button>
                                <button type="button" class="word-struct-btn" data-template="\sqrt[#?]{#?}">
                                    <span class="preview-math" style="font-size:12px;"><sup style="font-size:9px;">n</sup>&radic;<span style="border-top:1.5px solid #0284c7;padding:0 2px;">□</span></span>
                                    <span class="icon-label">Akar ke-n</span>
                                </button>
                            </div>
                        </div>

                        {{-- Integral, Sigma & Matriks --}}
                        <div class="tab-pane fade" id="tab-kalkulus-matriks" role="tabpanel">
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <button type="button" class="word-struct-btn" data-template="\int #? \, d#?">
                                    <span class="preview-math">&int; □</span>
                                    <span class="icon-label">Integral</span>
                                </button>
                                <button type="button" class="word-struct-btn" data-template="\int_{#?}^{#?} #? \, d#?">
                                    <span class="preview-math" style="font-size:11px;">&int;<sub>□</sub><sup>□</sup></span>
                                    <span class="icon-label">Integral Tentu</span>
                                </button>
                                <button type="button" class="word-struct-btn" data-template="\sum_{#?}^{#?} #?">
                                    <span class="preview-math" style="font-size:12px;">&sum;<sub>□</sub><sup>□</sup></span>
                                    <span class="icon-label">Sigma (&sum;)</span>
                                </button>
                                <button type="button" class="word-struct-btn" data-template="\prod_{#?}^{#?} #?">
                                    <span class="preview-math" style="font-size:12px;">&prod;<sub>□</sub><sup>□</sup></span>
                                    <span class="icon-label">Produk (&prod;)</span>
                                </button>
                                <button type="button" class="word-struct-btn" data-template="\begin{pmatrix} #? & #? \\ #? & #? \end{pmatrix}">
                                    <span class="preview-math" style="font-size:10px;line-height:1.1;">(<sup>□ □</sup><sub>□ □</sub>)</span>
                                    <span class="icon-label">Matriks 2x2</span>
                                </button>
                                <button type="button" class="word-struct-btn" data-template="\begin{bmatrix} #? & #? \\ #? & #? \end{bmatrix}">
                                    <span class="preview-math" style="font-size:10px;line-height:1.1;">[<sup>□ □</sup><sub>□ □</sub>]</span>
                                    <span class="icon-label">Matriks [ ]</span>
                                </button>
                                <button type="button" class="word-struct-btn" data-template="\begin{pmatrix} #? \\ #? \end{pmatrix}">
                                    <span class="preview-math" style="font-size:10px;line-height:1.1;">(<sup>□</sup><sub>□</sub>)</span>
                                    <span class="icon-label">Vektor 2x1</span>
                                </button>
                            </div>
                        </div>

                        {{-- Kurung & Fungsi --}}
                        <div class="tab-pane fade" id="tab-kurung-fungsi" role="tabpanel">
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <button type="button" class="word-struct-btn" data-template="\left(#?\right)">
                                    <span class="preview-math">( □ )</span>
                                    <span class="icon-label">Kurung ( )</span>
                                </button>
                                <button type="button" class="word-struct-btn" data-template="\left[#?\right]">
                                    <span class="preview-math">[ □ ]</span>
                                    <span class="icon-label">Kurung [ ]</span>
                                </button>
                                <button type="button" class="word-struct-btn" data-template="\left\{#?\right\}">
                                    <span class="preview-math">{ □ }</span>
                                    <span class="icon-label">Kurung { }</span>
                                </button>
                                <button type="button" class="word-struct-btn" data-template="\left|#?\right|">
                                    <span class="preview-math">| □ |</span>
                                    <span class="icon-label">Mutlak | |</span>
                                </button>
                                <button type="button" class="word-struct-btn" data-template="\sin\left(#?\right)">
                                    <span class="preview-math" style="font-size:12px;">sin(□)</span>
                                    <span class="icon-label">Sinus</span>
                                </button>
                                <button type="button" class="word-struct-btn" data-template="\cos\left(#?\right)">
                                    <span class="preview-math" style="font-size:12px;">cos(□)</span>
                                    <span class="icon-label">Cosinus</span>
                                </button>
                                <button type="button" class="word-struct-btn" data-template="\tan\left(#?\right)">
                                    <span class="preview-math" style="font-size:12px;">tan(□)</span>
                                    <span class="icon-label">Tangen</span>
                                </button>
                                <button type="button" class="word-struct-btn" data-template="\lim_{#? \to #?} #?">
                                    <span class="preview-math" style="font-size:11px;">lim<sub>□&rarr;□</sub></span>
                                    <span class="icon-label">Limit</span>
                                </button>
                                <button type="button" class="word-struct-btn" data-template="\log_{#?}\left(#?\right)">
                                    <span class="preview-math" style="font-size:11px;">log<sub>□</sub>(□)</span>
                                    <span class="icon-label">Logaritma</span>
                                </button>
                                <button type="button" class="word-struct-btn" data-template="\ln\left(#?\right)">
                                    <span class="preview-math" style="font-size:12px;">ln(□)</span>
                                    <span class="icon-label">Log Natural</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Simbol Matematika Cepat --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1 text-uppercase" style="letter-spacing: 0.5px;">Simbol Matematika:</label>
                        <div class="d-flex flex-wrap gap-1 p-2 bg-light rounded-3 border">
                            <button type="button" class="word-sym-btn" data-template="\pm">&plusmn;</button>
                            <button type="button" class="word-sym-btn" data-template="\times">&times;</button>
                            <button type="button" class="word-sym-btn" data-template="\div">&divide;</button>
                            <button type="button" class="word-sym-btn" data-template="=">=</button>
                            <button type="button" class="word-sym-btn" data-template="\neq">&ne;</button>
                            <button type="button" class="word-sym-btn" data-template="\approx">&asymp;</button>
                            <button type="button" class="word-sym-btn" data-template="\leq">&le;</button>
                            <button type="button" class="word-sym-btn" data-template="\geq">&ge;</button>
                            <button type="button" class="word-sym-btn" data-template="\infty">&infin;</button>
                            <button type="button" class="word-sym-btn" data-template="\pi">&pi;</button>
                            <button type="button" class="word-sym-btn" data-template="\alpha">&alpha;</button>
                            <button type="button" class="word-sym-btn" data-template="\beta">&beta;</button>
                            <button type="button" class="word-sym-btn" data-template="\gamma">&gamma;</button>
                            <button type="button" class="word-sym-btn" data-template="\theta">&theta;</button>
                            <button type="button" class="word-sym-btn" data-template="\lambda">&lambda;</button>
                            <button type="button" class="word-sym-btn" data-template="\mu">&mu;</button>
                            <button type="button" class="word-sym-btn" data-template="\sigma">&sigma;</button>
                            <button type="button" class="word-sym-btn" data-template="\Delta">&Delta;</button>
                            <button type="button" class="word-sym-btn" data-template="\rightarrow">&rarr;</button>
                            <button type="button" class="word-sym-btn" data-template="^\circ">&deg;</button>
                        </div>
                    </div>

                    {{-- Kanvas Lembar Rumus Visual Seperti di Word --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold mb-0 text-dark" style="font-size: 0.95rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square text-primary me-1" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                </svg>
                                Lembar Penulisan Rumus:
                            </label>
                            <button type="button" class="btn btn-outline-danger btn-sm px-2 py-1" id="btnClearWordEq">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash me-1" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                </svg>
                                Bersihkan
                            </button>
                        </div>
                        <math-field id="mfVisual" virtual-keyboard-mode="manual"></math-field>
                        <div class="form-text small text-muted mt-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1">Petunjuk Seperti Word</span>
                            Ketik huruf/angka langsung di keyboard. Klik kotak kosong <strong>(□)</strong> atau gunakan tombol panah <strong>&larr; &rarr; &uarr; &darr;</strong> untuk berpindah antar kotak pembilang/penyebut.
                        </div>
                    </div>

                    {{-- Contoh Rumus Populer Siap Pakai --}}
                    <div>
                        <label class="form-label small fw-bold text-muted mb-1">Gunakan Contoh Rumus Lengkap (Opsional):</label>
                        <select id="selectContohRumus" class="form-select form-select-sm">
                            <option value="">-- Pilih Rumus Jadi untuk Dimodifikasi --</option>
                            <option value="x = \frac{-b \pm \sqrt{b^2 - 4ac}}{2a}">Rumus ABC / Kuadratik : x = (-b ± √(b² - 4ac)) / (2a)</option>
                            <option value="a^2 + b^2 = c^2">Teorema Pythagoras : a² + b² = c²</option>
                            <option value="\bar{x} = \frac{1}{n}\sum_{i=1}^{n} x_i">Nilai Rata-rata (Mean) : x̄ = 1/n Σ x_i</option>
                            <option value="s = \sqrt{\frac{1}{n-1}\sum_{i=1}^n (x_i - \bar{x})^2}">Standar Deviasi : s = √[1/(n-1) Σ (x_i - x̄)²]</option>
                            <option value="f'(x) = \lim_{h \to 0} \frac{f(x+h) - f(x)}{h}">Definisi Turunan Diferensial</option>
                            <option value="\int_{a}^{b} f(x) \, dx = F(b) - F(a)">Teorema Dasar Kalkulus : ∫ f(x) dx = F(b) - F(a)</option>
                            <option value="e^{i\pi} + 1 = 0">Identitas Euler : e^(iπ) + 1 = 0</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer py-2 px-4 bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-dismiss="modal" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary btn-sm px-4 fw-semibold d-inline-flex align-items-center gap-1" id="btnSisipkanRumus">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-circle me-1" viewBox="0 0 16 16">
                            <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0"/>
                            <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/>
                        </svg>
                        <span id="btnSisipkanRumusText">Sisipkan Rumus ke Soal</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- KaTeX CSS & JS for Live LaTeX Rendering -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
    <script src="https://unpkg.com/mathlive"></script>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        let soalEditor;
        let activeEditingImg = null;

        function extractLatexFromImg(imgEl) {
            if (!imgEl) return '';
            let latex = imgEl.getAttribute('alt') || '';
            if (!latex && imgEl.src) {
                try {
                    const url = new URL(imgEl.src);
                    const search = decodeURIComponent(url.search.replace(/^\?/, ''));
                    latex = search.replace(/^\\dpi\{\d+\}&space;/, '');
                } catch (e) {
                    const match = imgEl.src.match(/codecogs\.com\/[a-z]+\.image\?(?:\\dpi\{\d+\}&space;)?(.*)$/);
                    if (match && match[1]) {
                        latex = decodeURIComponent(match[1]);
                    }
                }
            }
            return (latex || '').trim();
        }

        function resetModalRumusToAddMode() {
            activeEditingImg = null;
            $('#modalEditorRumusTitleText').text('Editor Rumus Matematika (Equation)');
            $('#btnSisipkanRumus').html(`
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-circle me-1" viewBox="0 0 16 16">
                    <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0"/>
                    <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/>
                </svg>
                <span id="btnSisipkanRumusText">Sisipkan Rumus ke Soal</span>
            `);
        }

        function openModalRumusForEdit(imgEl) {
            if (!imgEl) return;
            activeEditingImg = imgEl;
            const latex = extractLatexFromImg(imgEl);

            $('#modalEditorRumusTitleText').text('Edit Rumus Matematika (Equation)');
            $('#btnSisipkanRumus').html(`
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square me-1" viewBox="0 0 16 16">
                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                </svg>
                <span id="btnSisipkanRumusText">Perbarui Rumus di Soal</span>
            `);

            openModalRumus();

            setTimeout(function() {
                const mf = document.getElementById('mfVisual');
                if (mf) {
                    if (typeof mf.setValue === 'function') {
                        mf.setValue(latex);
                    } else {
                        mf.value = latex;
                    }
                    mf.focus();
                }
            }, 200);
        }

        function openModalRumus() {
            var modalEl = document.getElementById('modalEditorRumus');
            if (!modalEl) {
                console.error('Modal #modalEditorRumus tidak ditemukan!');
                return;
            }

            // Bersihkan sisa backdrop lama jika ada
            $('.modal-backdrop, #backdropModalRumus').remove();
            document.body.classList.remove('modal-open');

            // Pindahkan modal ke <body> agar terbebas dari overflow/z-index card
            if (modalEl.parentElement !== document.body) {
                document.body.appendChild(modalEl);
            }

            // Pastikan inline display: none terhapus agar modal tidak tersembunyi di balik backdrop
            modalEl.style.removeProperty('display');
            modalEl.style.zIndex = '10600';

            // 1. Coba lewat jQuery Bootstrap (Bootstrap 4 / 5)
            if (window.jQuery && typeof window.jQuery(modalEl).modal === 'function') {
                try {
                    window.jQuery(modalEl).modal('show');
                    setTimeout(function() {
                        const mf = document.getElementById('mfVisual');
                        if (mf) mf.focus();
                    }, 200);
                    return;
                } catch (err) {
                    console.warn('jQuery modal error:', err);
                }
            }

            // 2. Coba lewat Bootstrap 5 vanilla
            if (typeof window.bootstrap !== 'undefined' && window.bootstrap.Modal) {
                try {
                    var bModal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                    bModal.show();
                    setTimeout(function() {
                        const mf = document.getElementById('mfVisual');
                        if (mf) mf.focus();
                    }, 200);
                    return;
                } catch (err) {
                    console.warn('Bootstrap 5 modal error:', err);
                }
            }

            // 3. Fallback langsung CSS display
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
            modalEl.removeAttribute('aria-hidden');
            modalEl.setAttribute('aria-modal', 'true');
            var backdrop = document.createElement('div');
            backdrop.id = 'backdropModalRumus';
            backdrop.className = 'modal-backdrop fade show';
            backdrop.style.zIndex = '10550';
            document.body.appendChild(backdrop);
            document.body.classList.add('modal-open');
            setTimeout(function() {
                const mf = document.getElementById('mfVisual');
                if (mf) mf.focus();
            }, 200);
        }

        function closeModalRumus() {
            var modalEl = document.getElementById('modalEditorRumus');
            if (!modalEl) return;

            if (window.jQuery && typeof window.jQuery(modalEl).modal === 'function') {
                try {
                    window.jQuery(modalEl).modal('hide');
                } catch (e) {}
            }

            if (typeof window.bootstrap !== 'undefined' && window.bootstrap.Modal) {
                try {
                    var bModal = window.bootstrap.Modal.getInstance(modalEl);
                    if (bModal) bModal.hide();
                } catch (e) {}
            }

            modalEl.classList.remove('show');
            modalEl.style.display = 'none';
            modalEl.setAttribute('aria-hidden', 'true');
            modalEl.removeAttribute('aria-modal');

            $('.modal-backdrop, #backdropModalRumus').remove();
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');

            resetModalRumusToAddMode();
        }

        function injectEquationButton(editor) {
            if (!editor || !editor.ui || !editor.ui.view || !editor.ui.view.toolbar) return;
            const toolbarEl = editor.ui.view.toolbar.element;
            if (!toolbarEl) return;
            const toolbarItems = toolbarEl.querySelector('.ck-toolbar__items');
            if (!toolbarItems) return;

            if (toolbarItems.querySelector('.ck-btn-equation')) return;

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'ck ck-button ck-off ck-btn-equation';
            btn.setAttribute('tabindex', '-1');
            btn.setAttribute('data-cke-tooltip-text', 'Sisipkan / Edit Rumus Matematika (Equation)');
            btn.setAttribute('title', 'Sisipkan / Edit Rumus Matematika (Equation)');
            btn.setAttribute('aria-label', 'Sisipkan / Edit Rumus Matematika (Equation)');
            btn.innerHTML = `
                <svg class="ck ck-icon ck-button__icon" viewBox="0 0 20 20" style="width: 17px; height: 17px; overflow: visible;">
                    <path fill="currentColor" d="M2.5 10.5h1.8l2.2 6.5 3.5-13h7v1.4h-5.8l-3.3 12.1h-.9l-2.7-8H2.5v-1.4z"/>
                    <path fill="currentColor" d="M12.2 8.5h1.5l1.3 1.9 1.3-1.9h1.5l-2.1 2.9 2.2 3.1h-1.5l-1.4-2.1-1.4 2.1h-1.5l2.2-3.1-2.1-2.9z"/>
                </svg>
                <span class="ck ck-button__label">Equation</span>
            `;

            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Cek apakah ada rumus yang sedang dipilih di editor
                let targetImg = null;
                if (soalEditor) {
                    try {
                        const selEl = soalEditor.model.document.selection.getSelectedElement();
                        if (selEl) {
                            const viewEl = soalEditor.editing.mapper.toViewElement(selEl);
                            if (viewEl) {
                                const domEl = soalEditor.editing.view.domConverter.viewToDom(viewEl);
                                if (domEl) {
                                    if (domEl.tagName === 'IMG' && (domEl.classList.contains('math-tex') || (domEl.src && domEl.src.includes('latex.codecogs.com')))) {
                                        targetImg = domEl;
                                    } else {
                                        targetImg = domEl.querySelector('img.math-tex, img[src*="latex.codecogs.com"]');
                                    }
                                }
                            }
                        }
                    } catch (err) {
                        console.warn(err);
                    }
                }

                if (targetImg) {
                    openModalRumusForEdit(targetImg);
                } else {
                    resetModalRumusToAddMode();
                    const mf = document.getElementById('mfVisual');
                    if (mf) {
                        if (typeof mf.setValue === 'function') mf.setValue('');
                        else mf.value = '';
                    }
                    $('#selectContohRumus').val('');
                    openModalRumus();
                }
            });

            // Cari target penempatan: di area Media Embed / Table, sebelum Bulleted List
            let targetSibling = null;
            const items = Array.from(toolbarItems.children);
            for (let el of items) {
                const text = (el.getAttribute('data-cke-tooltip-text') || el.getAttribute('aria-label') || el.getAttribute('title') || '').toLowerCase();
                if (text.includes('bullet') || text.includes('daftar') || text.includes('nomor') || text.includes('numbered')) {
                    if (el.previousElementSibling && el.previousElementSibling.classList.contains('ck-toolbar__separator')) {
                        targetSibling = el.previousElementSibling;
                    } else {
                        targetSibling = el;
                    }
                    break;
                }
            }

            if (targetSibling) {
                toolbarItems.insertBefore(btn, targetSibling);
            } else {
                toolbarItems.appendChild(btn);
            }
        }

        ClassicEditor
            .create(document.querySelector('#pertanyaan'), {
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|',
                    'insertTable', 'undo', 'redo'
                ]
            })
            .then(editor => {
                soalEditor = editor;
                injectEquationButton(editor);
            })
            .catch(error => {
                console.error(error);
            });

        // Tab category switcher
        $(document).on('click', '#wordEqTabs button', function(e) {
            e.preventDefault();
            $('#wordEqTabs button').removeClass('active');
            $(this).addClass('active');
            const target = $(this).attr('data-target') || $(this).attr('data-bs-target');
            if (target) {
                $('#wordEqTabsContent .tab-pane').removeClass('show active');
                $(target).addClass('show active');
            }
        });

        // Close modal handlers
        $(document).on('click', '#modalEditorRumus [data-dismiss="modal"], #modalEditorRumus [data-bs-dismiss="modal"], #modalEditorRumus .close', function(e) {
            e.preventDefault();
            closeModalRumus();
        });

        // Sisipkan template / simbol ke MathLive visual editor
        function insertEquationTemplate(template) {
            const mf = document.getElementById('mfVisual');
            if (!mf) return;
            mf.focus();
            if (typeof mf.executeCommand === 'function') {
                mf.executeCommand(['insert', template]);
            } else {
                mf.value = (mf.value || '') + template;
            }
        }

        $(document).on('click', '.word-struct-btn, .word-sym-btn', function(e) {
            e.preventDefault();
            const template = $(this).attr('data-template');
            if (template) {
                insertEquationTemplate(template);
            }
        });

        // Contoh rumus siap pakai
        $('#selectContohRumus').on('change', function() {
            const val = $(this).val();
            const mf = document.getElementById('mfVisual');
            if (mf && val) {
                if (typeof mf.setValue === 'function') {
                    mf.setValue(val);
                } else {
                    mf.value = val;
                }
                mf.focus();
            }
        });

        // Bersihkan kanvas rumus
        $('#btnClearWordEq').on('click', function() {
            const mf = document.getElementById('mfVisual');
            if (mf) {
                if (typeof mf.setValue === 'function') {
                    mf.setValue('');
                } else {
                    mf.value = '';
                }
                mf.focus();
            }
            $('#selectContohRumus').val('');
        });

        $('#modalEditorRumus').on('shown.bs.modal', function() {
            const mf = document.getElementById('mfVisual');
            if (mf) mf.focus();
        });

        $('#modalEditorRumus').on('hidden.bs.modal', function() {
            var modalEl = document.getElementById('modalEditorRumus');
            if (modalEl) {
                modalEl.classList.remove('show');
                modalEl.style.display = 'none';
            }
            $('.modal-backdrop, #backdropModalRumus').remove();
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
            resetModalRumusToAddMode();
        });

        $(document).on('click', '#modalEditorRumus', function(e) {
            if (e.target === this) {
                closeModalRumus();
            }
        });

        // Double-click pada rumus di dalam editor untuk langsung mengedit rumus
        $(document).on('dblclick', '.ck-editor__editable img, .ck-content img, img.math-tex', function(e) {
            if (this.classList.contains('math-tex') || (this.src && this.src.includes('latex.codecogs.com'))) {
                e.preventDefault();
                e.stopPropagation();
                openModalRumusForEdit(this);
            }
        });

        // Berikan title hint saat kursor diarahkan ke rumus
        $(document).on('mouseenter', '.ck-editor__editable img, .ck-content img, img.math-tex', function() {
            if (this.classList.contains('math-tex') || (this.src && this.src.includes('latex.codecogs.com'))) {
                if (!this.getAttribute('title')) {
                    this.setAttribute('title', 'Klik ganda untuk mengedit rumus');
                }
            }
        });

        // Sisipkan atau Perbarui Rumus di CKEditor Soal
        $('#btnSisipkanRumus').on('click', function() {
            const mf = document.getElementById('mfVisual');
            const latex = (mf ? mf.value : '').trim();
            if (!latex) {
                alert('Silakan buat atau pilih struktur rumus terlebih dahulu!');
                return;
            }

            const encodedLatex = encodeURIComponent(latex);
            const imgUrl = `https://latex.codecogs.com/svg.image?\\dpi{130}&space;${encodedLatex}`;
            const safeAlt = latex.replace(/"/g, '&quot;');
            const imgHtml = `<img src="${imgUrl}" alt="${safeAlt}" class="math-tex" title="Klik ganda untuk mengedit rumus" style="vertical-align: middle; display: inline-block; margin: 0 4px; max-height: 2.8em;" />`;

            if (activeEditingImg) {
                // UPDATE RUMUS YANG SUDAH ADA
                let updated = false;

                if (soalEditor) {
                    try {
                        let modelElement = null;
                        const sel = soalEditor.model.document.selection.getSelectedElement();
                        if (sel) {
                            modelElement = sel;
                        } else if (soalEditor.editing && soalEditor.editing.view && soalEditor.editing.view.domConverter) {
                            const viewElement = soalEditor.editing.view.domConverter.domToView(activeEditingImg);
                            if (viewElement) {
                                modelElement = soalEditor.editing.mapper.toModelElement(viewElement);
                                if (!modelElement && viewElement.parent) {
                                    modelElement = soalEditor.editing.mapper.toModelElement(viewElement.parent);
                                }
                            }
                        }

                        if (modelElement) {
                            soalEditor.model.change(writer => {
                                writer.setSelection(modelElement, 'on');
                                const viewFragment = soalEditor.data.processor.toView(imgHtml);
                                const modelFragment = soalEditor.data.toModel(viewFragment);
                                soalEditor.model.insertContent(modelFragment);
                            });
                            updated = true;
                        }
                    } catch (err) {
                        console.warn('Gagal replace via model:', err);
                    }

                    // Fallback replace via data HTML
                    if (!updated) {
                        try {
                            const currentHtml = soalEditor.getData();
                            const oldSrc = activeEditingImg.getAttribute('src');
                            if (oldSrc && currentHtml.includes(oldSrc)) {
                                const escapedSrc = oldSrc.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                                const re = new RegExp('<img[^>]*src=["\']' + escapedSrc + '["\'][^>]*>', 'i');
                                if (re.test(currentHtml)) {
                                    soalEditor.setData(currentHtml.replace(re, imgHtml));
                                    updated = true;
                                }
                            }
                        } catch (errHtml) {
                            console.warn('Gagal replace via HTML:', errHtml);
                        }
                    }
                }

                if (!updated && activeEditingImg) {
                    activeEditingImg.src = imgUrl;
                    activeEditingImg.alt = safeAlt;
                    activeEditingImg.title = 'Klik ganda untuk mengedit rumus';
                    if (soalEditor) {
                        try { soalEditor.setData(soalEditor.getData()); } catch (e) {}
                    }
                }

                activeEditingImg = null;
            } else {
                // TAMBAH RUMUS BARU
                if (soalEditor) {
                    const viewFragment = soalEditor.data.processor.toView(imgHtml);
                    const modelFragment = soalEditor.data.toModel(viewFragment);
                    soalEditor.model.insertContent(modelFragment);
                } else {
                    const currentVal = $('#pertanyaan').val();
                    $('#pertanyaan').val(currentVal + ' ' + imgHtml);
                }
            }

            if (mf) {
                if (typeof mf.setValue === 'function') mf.setValue('');
                else mf.value = '';
            }
            $('#selectContohRumus').val('');

            closeModalRumus();
        });

        $(document).ready(function() {
            const modalEl = document.getElementById('modalEditorRumus');
            if (modalEl && modalEl.parentElement !== document.body) {
                document.body.appendChild(modalEl);
            }
        });
    </script>
@endsection
