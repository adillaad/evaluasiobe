@extends('dosen.template')
@section('content')

<style>
    li.select2-selection__choice {
        color: #646464;
        font-weight: bolder;
    }
</style>

<div class="container-fluid  mb-4">
    <div class="card">
        <div class="card-header">
            <div class="fw-bold">
                <h3>Tambah Universitas</h3>
            </div>
        </div>
        <div class="card-body">
            <form action="" method="post">
                @csrf
                <label> nama <span class="text-danger"> *</span></label>
                <input type="text" class="form-control" id="nama" name="nama" required>
                <br>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>

@endsection
