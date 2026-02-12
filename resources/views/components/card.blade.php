@props([
  'class' => '',
])

<div {{ $attributes->merge(['class' => "bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm $class"]) }}>
  {{ $slot }}
</div>
