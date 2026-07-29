{{-- @php
    $routePrefix = [
        'admin' => ['prefix' => 'admin'],
        'Admin Universitas' => ['prefix' => 'admin-universitas.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'admin.';
@endphp --}}
@extends('admin.template')
@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Fakultas</h4>
                @if ($userOtoritas != 'Admin Universitas')
                    <form method="GET" action="{{ route(Request::route()->getName()) }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="universitas_id">Universitas</label>
                                    <select name="universitas_id" id="universitas_id" class="form-control">
                                        <option value="">Pilih Universitas</option>
                                        @foreach ($universities as $univ)
                                            <option value="{{ $univ->id }}"
                                                {{ request('universitas_id') == $univ->id ? 'selected' : '' }}>
                                                {{ $univ->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route(Request::route()->getName()) }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>
                @endif
                <div class="table-responsive">
                    <table class="table table-hover dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Fakultas</th>
                                @if ($userOtoritas != 'Admin Universitas')
                                    <th>Universitas</th>
                                @endif
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fakultass as $fakultas)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $fakultas->nama }}</td>
                                    @if ($userOtoritas != 'Admin Universitas')
                                        <td>{{ $fakultas->universitas->nama }}</td>
                                    @endif
                                    <td class="d-flex">
                                        <form
                                            action="{{ route($currentPrefix . 'delete-fakultas', ['id' => $fakultas->id]) }}"
                                            method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-danger btn-icon-text p-2 me-2"
                                                onclick="return confirm('Are you sure to delete ?')">
                                                Delete
                                                <i class="ti-trash btn-icon-append"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const universitasSelect = $('#universitas_id');

            // Initialize Select2
            universitasSelect.select2({
                placeholder: 'Pilih Universitas',
                allowClear: true
            });
        });
    </script>
@endsection
