@props([
  'qName' => 'q',
  'qValue' => '',
  'placeholder' => 'Buscar…',
  'action' => '',
  'showClear' => false,
  'clearHref' => '#',
])

<div class="p-4 border-b bg-white">
  <form method="GET" action="{{ $action }}" class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
    <div class="flex-1">
      <div class="relative w-full sm:max-w-md">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
          <x-icon name="search" class="h-4 w-4" />
        </span>
        <input
          type="text"
          name="{{ $qName }}"
          value="{{ $qValue }}"
          placeholder="{{ $placeholder }}"
          class="w-full pl-9 rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
        />
      </div>
    </div>

    <div class="flex gap-2">
      <x-btn variant="soft" type="submit">
        <x-icon name="search" class="h-4 w-4" />
        Buscar
      </x-btn>

      @if($showClear)
        <x-btn variant="secondary" href="{{ $clearHref }}">
          <x-icon name="x" class="h-4 w-4" />
          Limpiar
        </x-btn>
      @endif

      {{ $slot }}
    </div>
  </form>
</div>
