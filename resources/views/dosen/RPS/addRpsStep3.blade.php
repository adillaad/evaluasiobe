@extends('dosen.template')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="fw-bold">
                    <h3>Tambah RPS Baru</h3>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="add-rpsStep3" enctype="multipart/form-data">
                    @csrf
                    {{-- <p>Nilai 'step1_dataRps.nomor': {{ session('step1_dataRps.nomor') }}</p> --}}

                    <!-- <div class="form-floating mb-3">
                        <button type="button" class="btn btn-secondary btn-sm mb-3" data-bs-toggle="tooltip"
                            data-bs-placement="left"
                            title="Introduction to Linear Algebra and Systems of Linear Equations Determinants and Inverses
                                Vector Spaces and Subspaces
                                Linear Transformations
                                Eigenvalues and Eigenvectors
                                Orthogonality and Inner Products
                                Determinants continued
                                Review and Midterm
                                Linear Differential Equations
                                Eigenvalues and Eigenvectors continued
                                Numerical Linear Algebra
                                Review and Final Exam"
                            style="float:right;"> Contoh Pengisian
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                                <path
                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                            </svg>
                        </button>
                        <label for="materi_mk">Materi MK <span class="text-danger">*</span></label>
                        <textarea name="materi_mk" class="form-control" placeholder="Materi MK" style="height: 100px">{{ old('materi_mk', session('step3_dataRps.materi_mk')) }}</textarea>

                        @error('materi_mk')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-floating mb-3">
                        <button type="button" class="btn btn-secondary btn-sm mb-3" data-bs-toggle="tooltip"
                            data-bs-placement="left"
                            title="Activity and presence (5%), Quiz (10%), Individual Task (10%), Mid Exam (20%), Group Project (30%), Final Exam (25%)"
                            style="float:right;"> Contoh Pengisian
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                                <path
                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                            </svg>
                        </button>
                        <label for="kontrak">Komponen Penilaian<span class="text-danger">*</span></label>
                        <textarea name="kontrak" class="form-control" placeholder="Komponen Penilaian" style="height: 100px">{{ old('kontrak', session('step3_dataRps.kontrak')) }}</textarea>
                        @error('kontrak')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div> -->

                    <div class="form-floating mb-3">
                        <button type="button" class="btn btn-secondary btn-sm mb-3" data-bs-toggle="tooltip"
                            data-bs-placement="left"
                            title="Virtual Class, Classroom, NetworkX, python"
                            style="float:right;"> Contoh Pengisian
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                                <path
                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                            </svg>
                        </button>
                        <label for="media_software">Media Pembelajaran (Software)<span class="text-danger">*</span></label>
                        <textarea name="media_software" class="form-control" placeholder="Media Pembelajaran Software" style="height: 100px">{{ old('media_software', session('step3_dataRps.media_software')) }}</textarea>

                        @error('media_software')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-floating mb-3">
                        <button type="button" class="btn btn-secondary btn-sm mb-3" data-bs-toggle="tooltip"
                            data-bs-placement="left"
                            title="Komputer/Laptop, Projector"
                            style="float:right;"> Contoh Pengisian
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                                <path
                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                            </svg>
                        </button>
                        <label for="media_hardware">Media Pembelajaran (Hardware)<span class="text-danger">*</span></label>
                        <textarea name="media_hardware" class="form-control" placeholder="Media Pembelajaran Hardware" style="height: 100px">{{ old('media_hardware', session('step3_dataRps.media_hardware')) }}</textarea>
                        @error('media_hardware')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button type="button" class="btn btn-primary"
                        onclick="location.href='{{ route('dosen.addRpsStep2') }}'">Previous</button>
                    <button type="submit" class="btn btn-primary">Next</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

        });
    </script>
@endsection
