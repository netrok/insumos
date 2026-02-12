@props([
  'name',
  'class' => 'h-4 w-4',
])

@php
  $attrs = $attributes->merge(['class' => $class])->except('name');
@endphp

@if($name === 'plus')
  <svg {{ $attrs }} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M12 5v14M5 12h14"/>
  </svg>
@elseif($name === 'search')
  <svg {{ $attrs }} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M21 21l-4.3-4.3"/><circle cx="11" cy="11" r="7"/>
  </svg>
@elseif($name === 'x')
  <svg {{ $attrs }} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M18 6L6 18M6 6l12 12"/>
  </svg>
@elseif($name === 'eye')
  <svg {{ $attrs }} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/>
  </svg>
@elseif($name === 'edit')
  <svg {{ $attrs }} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
  </svg>
@elseif($name === 'trash')
  <svg {{ $attrs }} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>
  </svg>
@elseif($name === 'filter')
  <svg {{ $attrs }} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M4 5h16l-6 7v6l-4 2v-8z"/>
  </svg>
@elseif($name === 'download')
  <svg {{ $attrs }} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M12 3v12"/><path d="M7 10l5 5 5-5"/><path d="M5 21h14"/>
  </svg>
@elseif($name === 'check')
  <svg {{ $attrs }} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M20 6L9 17l-5-5"/>
  </svg>
@elseif($name === 'chevron-left')
  <svg {{ $attrs }} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M15 18l-6-6 6-6"/>
  </svg>
@endif
