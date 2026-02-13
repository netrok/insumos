@extends('layouts.app')

@section('title', 'Proveedores')
@section('page_title', 'Proveedores')

@section('page_subtitle')
  Catálogo de proveedores para compras y entradas.
@endsection

@section('page_actions')
  <div class="flex flex-wrap gap-2">
    <x-btn href="{{ route('proveedores.create') }}">
      <x-icon name="plus" class="h-4 w-4" />
      Nuevo proveedor
    </x-btn>

    <x-btn variant="outline" href="{{ route('reportes.proveedores.xlsx', ['q' => request('q')]) }}">
      <x-icon name="download" class="h-4 w-4" />
      XLSX
    </x-btn>

    <x-btn variant="outline" href="{{ route('reportes.proveedores.pdf', ['q' => request('q')]) }}">
      <x-icon name="file" class="h-4 w-4" />
      PDF
    </x-btn>
  </div>
@endsection

@section('content')
  <x-card>

    {{-- Toolbar (si tu index aún no maneja búsqueda, igual funciona) --}}
    <x-toolbar
      action="{{ route('proveedores.index') }}"
      qName="q"
      qValue="{{ request('q') }}"
      placeholder="Buscar por nombre, RFC, correo, teléfono…"
      :showClear="filled(request('q'))"
      clearHref="{{ route('proveedores.index') }}"
    />

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr class="text-left border-b">
            <th class="py-3 px-4">Nombre</th>
            <th class="py-3 px-4">RFC</th>
            <th class="py-3 px-4">Teléfono</th>
            <th class="py-3 px-4">Email</th>
            <th class="py-3 px-4">Activo</th>
            <th class="py-3 px-4 text-right">Acciones</th>
          </tr>
        </thead>

        <tbody class="divide-y">
          @forelse($proveedores as $p)
            <tr class="hover:bg-gray-50">
              <td class="py-3 px-4 font-medium">{{ $p->nombre }}</td>
              <td class="py-3 px-4">{{ $p->rfc ?? '—' }}</td>
              <td class="py-3 px-4">{{ $p->telefono ?? '—' }}</td>
              <td class="py-3 px-4">{{ $p->email ?? '—' }}</td>

              <td class="py-3 px-4">
                @if($p->activo)
                  <x-badge variant="success">Sí</x-badge>
                @else
                  <x-badge variant="muted">No</x-badge>
                @endif
              </td>

              <td class="py-3 px-4 text-right whitespace-nowrap">
                <div class="inline-flex items-center gap-2">
                  <x-btn variant="ghost" iconOnly href="{{ route('proveedores.show', $p) }}" title="Ver">
                    <x-icon name="eye" class="h-4 w-4" />
                  </x-btn>

                  <x-btn variant="outline" iconOnly href="{{ route('proveedores.edit', $p) }}" title="Editar">
                    <x-icon name="edit" class="h-4 w-4" />
                  </x-btn>

                  <form method="POST" action="{{ route('proveedores.destroy', $p) }}"
                        class="inline"
                        onsubmit="return confirm('¿Eliminar proveedor? Esta acción no se puede deshacer.')">
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
              <td class="py-10 px-4 text-center text-gray-600" colspan="6">
                No hay proveedores aún.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-4 border-t">
      {{ $proveedores->links() }}
    </div>

  </x-card>
@endsection
