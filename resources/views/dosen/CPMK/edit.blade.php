@extends('dosen.template')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="fw-bold">
                <h3>Edit CPMK</h3>
            </div>
        </div>
        <div class="card-body">
            <form action="{{$cpmk->id}}" method="post">
                @csrf
                @method('put')
                <div class="form-floating mb-3">
                    <select id="cpl" name="cpl" class="form-select form-control-lg" aria-label="select CPL">
                        <option selected value="{{$cpmk->cpl->id}}">{{$cpmk->cpl->judul}}</option>
                    </select>
                    <label for="mataKuliah">CPL</label>
                </div>
                <div class="form-floating mb-3">
                    <textarea class="form-control" name="judul" placeholder="Leave a comment here" id="floatingTextarea2" style="height: 100px"> {{$cpmk->judul}} </textarea>
                    <label for="floatingTextarea2">Rincian CPMK <span class="text-danger">*</span></label>
                </div>
                @error('judul')
                <div class="alert alert-danger">
                    {{ $message }}
                </div>
                @enderror
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>

@endsection
