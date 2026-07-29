@extends('admin.template')
@section('content')
<style>

</style>
<div class="row flex-grow">
    <div class="col-md-5 col-lg-5 grid-margin">
        <div class="card card-rounded">
            <div class="card-body card-rounded">
                <h4 class="card-title">Add Kurikulum</h4>
                <div class="form-group">
                    <label>Tahun Kurikulum <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" disabled="disabled" placeholder="Tahun Kurikulum" autofocus autocomplete="off">
                </div>
                <button type="submit" disabled="disabled" class="btn btn-primary me-2">Submit</button>
            </div>
        </div>
    </div>
    <div class="col-md-7 col-lg-7 grid-margin stretch-card">
        <div class="card card-rounded">
            <div class="card-body card-rounded">
                <h4 class="card-title">Edit Kurikulum</h4>
                <div class="table-responsive mt-3">
                    <table class="table table-hover dataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tahun</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>
                                    <form method="POST" action="/admin/edit-kurikulum/{{encrypt($kurikulum->tahun)}}">
                                        @csrf
                                        @method('put')
                                        <input id="name" type="text" class="form-control" name="tahun" style="width:50%" value="{{$kurikulum->tahun}}" autofocus autocomplete="off">
                                        @error('tahun')
                                        <div class="alert alert-danger">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                </td>
                                <td>
                                    <button type="submit" class="btn btn-primary btn-icon-text p-2">
                                        Submit
                                    </button>
                                    </form>
                                </td>
                            </tr>
                            @foreach($kurikulums as $kur)
                            @if($kur->tahun != $kurikulum->tahun)
                            <tr>
                                <td class="py-4">2</td>
                                <td>{{$kur->tahun}}</td>
                                <td></td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection