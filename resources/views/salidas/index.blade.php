@extends('layouts.app')

@section('title', 'Salidas')
@section('page_title', 'Salidas')

@section('page_subtitle')
  Movimientos de salida del almacén.
@endsection

@section('page_actions')
  <x-btn href="{{ route('salidas.create') }}">
    <x-icon name="plus" class="h-4 w-4" />
    Nueva salida
  </x-btn>
@endsection

@section('content')
  <x-card>

    {{-- Toolbar / filtros --}}
    <div class="p-4 border-b bg-white">
      <form method="GET" action="{{ route('salidas.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">

        <div class="sm:col-span-3">
          <label class="text-xs font-semibold text-gray-600">Almacén</label>
          <select name="almacen_id" class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black">
            <option value="">Todos</option>
            @foreach($almacenes as $a)
              <option value="{{ $a->id }}" @selected(request('almacen_id') == $a->id)>{{ $a->nombre }}</option>
            @endforeach
          </select>
        </div>

        <div class="sm:col-span-3">
          <label class="text-xs font-semibold text-gray-600">Tipo</label>
          <select name="tipo" class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black">
            <option value="">Todos</option>
            @foreach(['consumo','merma','ajuste','traspaso'] as $t)
              <option value="{{ $t }}" @selected(request('tipo') === $t)>{{ strtoupper($t) }}</option>
            @endforeach
          </select>
        </div>

        <div class="sm:col-span-2">
          <label class="text-xs font-semibold text-gray-600">Desde</label>
          <input type="date" name="desde" value="{{ request('desde') }}"
                 class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black">
        </div>

        <div class="sm:col-span-2">
          <label class="text-xs font-semibold text-gray-600">Hasta</label>
          <input type="date" name="hasta" value="{{ request('hasta') }}"
                 class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black">
        </div>

        <div class="sm:col-span-2 flex gap-2">
          <x-btn variant="soft" type="submit" class="w-full">
            <x-icon name="search" class="h-4 w-4" />
            Filtrar
          </x-btn>

          @if(request()->filled('almacen_id') || request()->filled('tipo') || request()->filled('desde') || request()->filled('hasta'))
            <x-btn variant="secondary" href="{{ route('salidas.index') }}" class="w-full">
              <x-icon name="x" class="h-4 w-4" />
              Limpiar
            </x-btn>
          @endif
        </div>

      </form>
    </div>

    {{-- Tabla --}}
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="text-left font-medium px-4 py-3">Folio</th>
            <th class="text-left font-medium px-4 py-3">Fecha</th>
            <th class="text-left font-medium px-4 py-3">Almacén</th>
            <th class="text-left font-medium px-4 py-3">Tipo</th>
            <th class="text-right font-medium px-4 py-3">Total</th>
            <th class="text-right font-medium px-4 py-3 w-56">Acciones</th>
          </tr>
        </thead>

        <tbody class="divide-y">
          @forelse($salidas as $s)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3 font-medium">
                <a href="{{ route('salidas.show', $s) }}" class="hover:underline">
                  {{ $s->folio }}
                </a>
              </td>

              <td class="px-4 py-3">
                {{ \Illuminate\Support\Carbon::parse($s->fecha)->format('d/m/Y') }}
              </td>

              <td class="px-4 py-3">
                {{ $s->almacen->nombre ?? '—' }}
              </td>

              <td class="px-4 py-3">
                <x-badge variant="gold">{{ strtoupper($s->tipo) }}</x-badge>
              </td>

              <td class="px-4 py-3 text-right font-semibold">
                ${{ number_format((float) $s->total, 2) }}
              </td>

              <td class="px-4 py-3">
                <div class="flex justify-end gap-2">
                  <x-btn variant="ghost" iconOnly href="{{ route('salidas.show', $s) }}" title="Ver">
                    <x-icon name="eye" class="h-4 w-4" />
                  </x-btn>

                  <x-btn variant="outline" iconOnly href="{{ route('salidas.edit', $s) }}" title="Editar">
                    <x-icon name="edit" class="h-4 w-4" />
                  </x-btn>

                  <form method="POST" action="{{ route('salidas.destroy', $s) }}"
                        onsubmit="return confirm('¿Eliminar salida {{ $s->folio }}? Esto regresará existencias.');"
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
                No hay salidas con los filtros actuales.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-4 border-t">
      {{ $salidas->links() }}
    </div>

  </x-card>
@endsection
