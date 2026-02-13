@props([
  'name',
  'label' => null,
  'hint' => null,
  'type' => 'text',
])

<div>
  @if($label)
    <label for="{{ $name }}" class="text-xs font-semibold text-gray-600">{{ $label }}</label>
  @endif

  <div class="mt-1">
    @if($type === 'textarea')
      <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $attributes->merge(['class' => 'w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black']) }}
      >{{ old($name, $slot) }}</textarea>
    @else
      <input
        id="{{ $name }}"
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ old($name, $attributes->get('value')) }}"
        {{ $attributes->merge(['class' => 'w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black']) }}
      />
    @endif
  </div>

  @if($hint)
    <div class="mt-1 text-xs text-gray-500">{{ $hint }}</div>
  @endif

  @error($name)
    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
  @enderror
</div>
