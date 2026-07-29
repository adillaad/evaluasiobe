@props([
    'name' => 'is_aptikom',
    'id' => 'is_aptikom',
    'value' => null,
    'label' => 'Apakah Prodi Aptikom?',
    'required' => true,
])

@include('components.aptikom-styles')

@php
    $selected = old($name, $value);
@endphp

<div {{ $attributes->merge(['class' => 'form-group']) }}>
    <label class="form-label d-block">
        {{ $label }}
        @if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>

    <div class="aptikom-picker" role="radiogroup" aria-label="{{ $label }}">
        <label class="aptikom-picker__option aptikom-picker__option--yes {{ (string) $selected === '1' ? 'is-selected' : '' }}">
            <input type="radio" name="{{ $name }}" id="{{ $id }}_yes" value="1"
                {{ (string) $selected === '1' ? 'checked' : '' }} {{ $required ? 'required' : '' }}>
            <span class="badge-aptikom badge-aptikom--yes">Aptikom</span>
            <small>Program studi dengan standar kurikulum Aptikom</small>
        </label>

        <label class="aptikom-picker__option aptikom-picker__option--no {{ (string) $selected === '0' ? 'is-selected' : '' }}">
            <input type="radio" name="{{ $name }}" id="{{ $id }}_no" value="0"
                {{ (string) $selected === '0' ? 'checked' : '' }}>
            <span class="badge-aptikom badge-aptikom--no">Non Aptikom</span>
            <small>Program studi di luar standar kurikulum Aptikom</small>
        </label>
    </div>
</div>

@once
    <script>
        document.addEventListener('change', function(event) {
            const input = event.target;
            if (input.type !== 'radio' || !input.closest('.aptikom-picker')) {
                return;
            }

            const picker = input.closest('.aptikom-picker');
            picker.querySelectorAll('.aptikom-picker__option').forEach(function(option) {
                option.classList.remove('is-selected');
            });
            input.closest('.aptikom-picker__option')?.classList.add('is-selected');
        });
    </script>
@endonce
