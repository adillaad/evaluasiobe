@props([
    'value' => false,
])

@include('components.aptikom-styles')

@if ($value)
    <span {{ $attributes->merge(['class' => 'badge-aptikom badge-aptikom--yes']) }}>Aptikom</span>
@else
    <span {{ $attributes->merge(['class' => 'badge-aptikom badge-aptikom--no']) }}>Non Aptikom</span>
@endif
