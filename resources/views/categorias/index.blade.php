@extends('layouts.app')

@section('title', 'Categorías')
@section('header', 'Catálogos')

@section('page_title', 'Categorías')
@section('page_subtitle', 'Administra el catálogo de categorías')

@section('page_actions')
  <x-btn href="{{ route('categorias.create') }}">
    <x-icon name="plus" class="h-4 w-4" />
    Nueva
  </x-btn>
@endsection

@section('content')
  <x-card>
    <x-toolbar
      action="{{ route('categorias.index') }}"
      qName="q"
      qValue="{{ $q ?? '' }}"
      placeholder="Buscar por nombre…"
      :showClear="!empty($q)"
      clearHref="{{ route('categorias.index') }}"
    />

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="text-left font-medium px-4 py-3">Nombre</th>
            <th class="text-left font-medium px-4 py-3">Activa</th>
            <th class="text-right font-medium px-4 py-3 w-56">Acciones</th>
          </tr>
        </thead>

        <tbody class="divide-y">
          @forelse($items as $it)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3">
                <div class="font-medium">
                  <a href="{{ route('categorias.show', $it) }}" class="hover:underline">
                    {{ $it->nombre }}
                  </a>
                </div>
                <div class="text-gray-500">
                  {{ $it->descripcion ?: '—' }}
                </div>
              </td>

              <td class="px-4 py-3">
                @if($it->activa)
                  <x-badge variant="success">Sí</x-badge>
                @else
                  <x-badge variant="muted">No</x-badge>
                @endif
              </td>

              <td class="px-4 py-3">
                <div class="flex justify-end gap-2">
                  <x-btn variant="ghost" iconOnly href="{{ route('categorias.show', $it) }}" title="Ver">
                    <x-icon name="eye" class="h-4 w-4" />
                  </x-btn>

                  <x-btn variant="outline" iconOnly href="{{ route('categorias.edit', $it) }}" title="Editar">
                    <x-icon name="edit" class="h-4 w-4" />
                  </x-btn>

                  <form method="POST" action="{{ route('categorias.destroy', $it) }}"
                        onsubmit="return confirm('¿Eliminar esta categoría?');"
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
              <td colspan="3" class="px-4 py-10 text-center text-gray-500">
                No hay categorías{{ !empty($q) ? ' que coincidan con tu búsqueda' : '' }}.
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
