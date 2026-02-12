@props([
  'type' => 'button',
])

@php($isLink = $attributes->has('href'))

@if($isLink)
  <a {{ $attributes->merge(['class' => $classes()]) }}>
    {{ $slot }}
  </a>
@else
  <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes()]) }}>
    {{ $slot }}
  </button>
@endif
