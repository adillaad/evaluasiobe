@extends($template)
@section('content')
    <script>window.location.href = "{{ route(($currentPrefix ?? '') . 'mahasiswa.index') }}";</script>
@endsection
