@extends(auth()->user()->otoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
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

<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">List User</h4>
            <div class="table-responsive">
                <table class="table table-hover dataTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Otoritas</th>
                            <th>Password</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $no=>$user)
                        <tr>
                            <td class="py-4">{{$no+1}}</td>
                            <td>{{$user->name}}</td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->otoritas}}</td>
                            <td>
                                <form action="{{ route('TPMPS.reset-user', encrypt($user->id)) }}" method="post" class="d-inline m-0 p-0">
                                    @csrf
                                    @method('put')
                                    <button type="submit" class="btn btn-info btn-icons"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Reset Password"
                                        onclick="return confirm('Are you sure to reset password {{$user->name}}?')">
                                        <i class="ti-reload"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <a href="{{ route('TPMPS.edit-user', encrypt($user->id)) }}"
                                        class="btn btn-warning btn-icons" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                        <i class="ti-pencil"></i>
                                    </a>
                                    <form action="delete-user/{{encrypt($user->id)}}" method="post" class="d-inline m-0 p-0">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-danger btn-icons"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                                            onclick="return confirm('Are you sure to delete {{$user->name}}?')">
                                            <i class="ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
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