@extends('layouts.app')

@section('title', 'Almacenes')
@section('header', 'Catálogos')

@section('page_title', 'Almacenes')
@section('page_subtitle', 'Administra el catálogo de almacenes')

@section('page_actions')
  <x-btn href="{{ route('almacenes.create') }}">
    <x-icon name="plus" class="h-4 w-4" />
    Nuevo almacén
  </x-btn>
@endsection

@section('content')
  <x-card>

    <x-toolbar
      action="{{ route('almacenes.index') }}"
      qName="q"
      qValue="{{ $q ?? '' }}"
      placeholder="Buscar por nombre, código, ubicación…"
      :showClear="!empty($q)"
      clearHref="{{ route('almacenes.index') }}"
    />

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="text-left font-medium px-4 py-3">Nombre</th>
            <th class="text-left font-medium px-4 py-3">Código</th>
            <th class="text-left font-medium px-4 py-3">Ubicación</th>
            <th class="text-left font-medium px-4 py-3">Activo</th>
            <th class="text-right font-medium px-4 py-3 w-56">Acciones</th>
          </tr>
        </thead>

        <tbody class="divide-y">
          @forelse($items as $a)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3 font-medium">
                <a href="{{ route('almacenes.show', $a) }}" class="hover:underline">
                  {{ $a->nombre ?? '—' }}
                </a>
              </td>

              <td class="px-4 py-3">
                {{ $a->codigo ?? '—' }}
              </td>

              <td class="px-4 py-3">
                {{ $a->ubicacion ?? '—' }}
              </td>

              <td class="px-4 py-3">
                @if($a->activo)
                  <x-badge variant="success">Sí</x-badge>
                @else
                  <x-badge variant="muted">No</x-badge>
                @endif
              </td>

              <td class="px-4 py-3">
                <div class="flex justify-end gap-2">
                  <x-btn variant="ghost" iconOnly href="{{ route('almacenes.show', $a) }}" title="Ver">
                    <x-icon name="eye" class="h-4 w-4" />
                  </x-btn>

                  <x-btn variant="outline" iconOnly href="{{ route('almacenes.edit', $a) }}" title="Editar">
                    <x-icon name="edit" class="h-4 w-4" />
                  </x-btn>

                  <form method="POST" action="{{ route('almacenes.destroy', $a) }}"
                        onsubmit="return confirm('¿Eliminar almacén? Esta acción no se puede deshacer.');"
                        class="inline">
                    @csrf
                    @method('DELETE')

                    <x-btn variant="danger" iconOnly type="submit" title="Eliminar">
                      <x-icon name="trash" class="h-4 w-4" />
                    </x-btn>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                No hay almacenes{{ !empty($q) ? ' que coincidan con tu búsqueda' : '' }}.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-4 border-t">
      {{ $items->links() }}
    </div>

  </x-card>
@endsection
