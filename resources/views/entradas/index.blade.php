@extends('layouts.app')

@section('title', 'Entradas')
@section('page_title', 'Entradas')

@section('page_subtitle')
  Movimientos de entrada al almacén.
@endsection

@section('page_actions')
  <x-btn href="{{ route('entradas.create') }}">
    <x-icon name="plus" class="h-4 w-4" />
    Nueva entrada
  </x-btn>
@endsection

@section('content')
  <x-card>

    {{-- Toolbar / filtros --}}
    <div class="p-4 border-b bg-white">
      <form method="GET" action="{{ route('entradas.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
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
          <label class="text-xs font-semibold text-gray-600">Proveedor</label>
          <select name="proveedor_id" class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black">
            <option value="">Todos</option>
            @foreach($proveedores as $p)
              <option value="{{ $p->id }}" @selected(request('proveedor_id') == $p->id)>{{ $p->nombre }}</option>
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

          @if(request()->filled('almacen_id') || request()->filled('proveedor_id') || request()->filled('desde') || request()->filled('hasta'))
            <x-btn variant="secondary" href="{{ route('entradas.index') }}" class="w-full">
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
            <th class="text-left font-medium px-4 py-3">Proveedor</th>
            <th class="text-left font-medium px-4 py-3">Tipo</th>
            <th class="text-right font-medium px-4 py-3">Total</th>
            <th class="text-right font-medium px-4 py-3 w-56">Acciones</th>
          </tr>
        </thead>

        <tbody class="divide-y">
          @forelse($entradas as $e)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3 font-medium">
                <a href="{{ route('entradas.show', $e) }}" class="hover:underline">
                  {{ $e->folio }}
                </a>
              </td>

              <td class="px-4 py-3">
                {{ \Illuminate\Support\Carbon::parse($e->fecha)->format('d/m/Y') }}
              </td>

              <td class="px-4 py-3">
                {{ $e->almacen->nombre ?? '—' }}
              </td>

              <td class="px-4 py-3">
                {{ $e->proveedor->nombre ?? '—' }}
              </td>

              <td class="px-4 py-3">
                <x-badge variant="gold">{{ strtoupper($e->tipo) }}</x-badge>
              </td>

              <td class="px-4 py-3 text-right font-semibold">
                ${{ number_format((float) $e->total, 2) }}
              </td>

              <td class="px-4 py-3">
                <div class="flex justify-end gap-2">
                  <x-btn variant="ghost" iconOnly href="{{ route('entradas.show', $e) }}" title="Ver">
                    <x-icon name="eye" class="h-4 w-4" />
                  </x-btn>

                  {{-- Edit/Eliminar están bloqueados por controller (403), así que ni los mostramos --}}
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                No hay entradas con los filtros actuales.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-4 border-t">
      {{ $entradas->links() }}
    </div>

  </x-card>
@endsection
