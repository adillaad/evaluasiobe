@extends('admin.template')
@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">List Universitas</h4>
                <div class="table-responsive">
                    <table class="table table-hover dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Logo</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($universitas as $universitas)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $universitas->nama }}</td>
                                    <td>
                                        <img src="{{ asset($universitas->img) }}" class="img img-responsive" />
                                    </td>
                                    <td>
                                        <form
                                            action="{{ route('admin.delete-universitas', ['id' => $universitas->id]) }}"
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
@endsection
