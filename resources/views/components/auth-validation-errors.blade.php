@props(['errors'])

<style>
    .text-red-600 {
        color: #e3342f;
    }
    .font-medium {
        font-weight: 500;
    }
    .list-disc {
        list-style-type: none;
    }
    .list-inside {
        padding-left: 0;
    }
    .mt-3 {
        margin-top: 1rem;
    }
</style>

@if ($errors->any())
    <div {{ $attributes }}>
        <ul class="mt-3 list-disc alert alert-danger font-medium">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
