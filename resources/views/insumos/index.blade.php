@extends('layouts.app')

@section('title', 'Insumos')
@section('page_title', 'Insumos')

@section('page_subtitle')
  Catálogo de insumos para entradas y existencias.
@endsection

@section('page_actions')
  <x-btn href="{{ route('insumos.create') }}">
    <x-icon name="plus" class="h-4 w-4" />
    Nuevo insumo
  </x-btn>
@endsection

@section('content')
  <x-card>

    {{-- Toolbar --}}
    <div class="p-4 border-b bg-white">
      <form method="GET" action="{{ route('insumos.index') }}"
            class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">

        <div class="sm:col-span-5">
          <label class="text-xs font-semibold text-gray-600">Búsqueda</label>
          <div class="relative mt-1">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
              <x-icon name="search" class="h-4 w-4" />
            </span>
            <input
              type="text"
              name="q"
              value="{{ $q ?? '' }}"
              placeholder="Buscar por SKU o nombre…"
              class="w-full pl-9 rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
            />
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="text-xs font-semibold text-gray-600">Categoría</label>
          <select name="categoria_id"
                  class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black">
            <option value="">Todas</option>

            @foreach(($categorias ?? []) as $c)
              <option value="{{ $c->id }}" @selected(($categoriaId ?? '') == $c->id)>
                {{ $c->nombre }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="sm:col-span-2">
          <label class="text-xs font-semibold text-gray-600">Estatus</label>
          <select name="activo"
                  class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black">
            <option value="">Todos</option>
            <option value="1" @selected(($activo ?? '') === '1')>Activos</option>
            <option value="0" @selected(($activo ?? '') === '0')>Inactivos</option>
          </select>
        </div>

        <div class="sm:col-span-2 flex gap-2">
          <x-btn variant="soft" type="submit" class="w-full">
            <x-icon name="filter" class="h-4 w-4" />
            Filtrar
          </x-btn>

          @php
            $hasFilters =
              filled($q ?? null) ||
              filled($categoriaId ?? null) ||
              (($activo ?? '') !== '');
          @endphp

          @if($hasFilters)
            <x-btn variant="secondary" href="{{ route('insumos.index') }}" class="w-full">
              <x-icon name="x" class="h-4 w-4" />
              Limpiar
            </x-btn>
          @endif
        </div>

      </form>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="text-left font-medium px-4 py-3">Nombre</th>
            <th class="text-left font-medium px-4 py-3">SKU</th>
            <th class="text-left font-medium px-4 py-3">Categoría</th>
            <th class="text-left font-medium px-4 py-3">Unidad</th>
            <th class="text-left font-medium px-4 py-3">Activo</th>
            <th class="text-right font-medium px-4 py-3 w-56">Acciones</th>
          </tr>
        </thead>

        <tbody class="divide-y">
          @forelse($items as $it)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3 font-medium">
                <a href="{{ route('insumos.show', $it) }}" class="hover:underline">
                  {{ $it->nombre }}
                </a>
                @if(!empty($it->descripcion))
                  <div class="text-xs text-gray-500 line-clamp-1">{{ $it->descripcion }}</div>
                @endif
              </td>

              <td class="px-4 py-3">
                {{ $it->sku ?? '—' }}
              </td>

              <td class="px-4 py-3">
                {{ $it->categoria->nombre ?? '—' }}
              </td>

              <td class="px-4 py-3">
                <div class="font-medium">{{ $it->unidad->nombre ?? '—' }}</div>
                <div class="text-gray-500 text-xs">{{ $it->unidad->clave ?? '' }}</div>
              </td>

              <td class="px-4 py-3">
                @if($it->activo)
                  <x-badge variant="success">Sí</x-badge>
                @else
                  <x-badge variant="muted">No</x-badge>
                @endif
              </td>

              <td class="px-4 py-3">
                <div class="flex justify-end gap-2">
                  <x-btn variant="ghost" iconOnly href="{{ route('insumos.show', $it) }}" title="Ver">
                    <x-icon name="eye" class="h-4 w-4" />
                  </x-btn>

                  <x-btn variant="outline" iconOnly href="{{ route('insumos.edit', $it) }}" title="Editar">
                    <x-icon name="edit" class="h-4 w-4" />
                  </x-btn>

                  <form method="POST" action="{{ route('insumos.destroy', $it) }}"
                        onsubmit="return confirm('¿Eliminar / desactivar este insumo?');"
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
              <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                No hay insumos{{ filled($q ?? '') ? ' que coincidan con tu búsqueda' : '' }}.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="p-4 border-t">
      {{ $items->links() }}
    </div>

  </x-card>
@endsection
