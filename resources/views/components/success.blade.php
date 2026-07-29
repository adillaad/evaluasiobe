@if (session('success'))
    <div class="alert alert-success" role="alert">
        <div class="d-flex justify-content-between">
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close t" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@elseif(session('error'))
    <div class="alert alert-danger" role="alert">
        <div class="d-flex justify-content-between">
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close t" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif
