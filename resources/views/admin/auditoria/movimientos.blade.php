@extends('layouts.app')

@section('title', 'Auditoría')
@section('page_title', 'Auditoría de movimientos')
@section('page_subtitle', 'Últimos movimientos de entradas y salidas.')

@section('page_actions')
  <div class="flex flex-wrap gap-2">
    <x-btn variant="outline" href="{{ route('admin.index') }}">Volver</x-btn>
  </div>
@endsection

@section('content')
  <div class="space-y-4">

    {{-- Filtros --}}
    <x-card>
      <form method="GET" action="{{ route('admin.auditoria.movimientos') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">

        <div class="md:col-span-2">
          <label class="block text-xs text-gray-500 mb-1">Búsqueda</label>
          <input
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="Folio, usuario, insumo o almacén…"
            class="w-full rounded-xl border-gray-200 text-sm"
          />
        </div>

        <div>
          <label class="block text-xs text-gray-500 mb-1">Tipo</label>
          <select name="tipo" class="w-full rounded-xl border-gray-200 text-sm">
            <option value=""  {{ request('tipo')==='' ? 'selected' : '' }}>Todos</option>
            <option value="ENT" {{ request('tipo')==='ENT' ? 'selected' : '' }}>ENT</option>
            <option value="SAL" {{ request('tipo')==='SAL' ? 'selected' : '' }}>SAL</option>
          </select>
        </div>

        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-xs text-gray-500 mb-1">Desde</label>
            <input
              type="date"
              name="from"
              value="{{ request('from') }}"
              class="w-full rounded-xl border-gray-200 text-sm"
            />
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1">Hasta</label>
            <input
              type="date"
              name="to"
              value="{{ request('to') }}"
              class="w-full rounded-xl border-gray-200 text-sm"
            />
          </div>
        </div>

        <div class="md:col-span-4 flex flex-wrap gap-2 pt-1">
          <x-btn type="submit">Aplicar</x-btn>

          @if(filled(request('q')) || filled(request('tipo')) || filled(request('from')) || filled(request('to')))
            <x-btn variant="outline" href="{{ route('admin.auditoria.movimientos') }}">Limpiar</x-btn>
          @endif
        </div>
      </form>
    </x-card>

    {{-- Tabla --}}
    <x-card>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50">
            <tr class="text-left border-b">
              <th class="py-3 px-4">Fecha</th>
              <th class="py-3 px-4">Tipo</th>
              <th class="py-3 px-4">Folio</th>
              <th class="py-3 px-4">Usuario</th>
              <th class="py-3 px-4">Insumo</th>
              <th class="py-3 px-4">Almacén</th>
              <th class="py-3 px-4 text-right">Cantidad</th>
            </tr>
          </thead>

          <tbody class="divide-y">
            @forelse($items as $m)
              @php
                $tipo = (string) data_get($m, 'tipo', '');
                $fechaRaw = data_get($m, 'fecha');
                $fecha = $fechaRaw ? \Carbon\Carbon::parse($fechaRaw)->format('Y-m-d') : '—';
                $cantidad = (float) data_get($m, 'cantidad', 0);
              @endphp

              <tr class="hover:bg-gray-50">
                <td class="py-3 px-4">{{ $fecha }}</td>

                <td class="py-3 px-4">
                  @if($tipo === 'ENT')
                    <span class="inline-flex rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700">ENT</span>
                  @elseif($tipo === 'SAL')
                    <span class="inline-flex rounded-full bg-rose-50 px-2 py-1 text-xs font-semibold text-rose-700">SAL</span>
                  @else
                    <span class="inline-flex rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700">—</span>
                  @endif
                </td>

                <td class="py-3 px-4 font-medium">{{ data_get($m, 'folio', '—') }}</td>
                <td class="py-3 px-4">{{ data_get($m, 'usuario', '—') }}</td>
                <td class="py-3 px-4">{{ data_get($m, 'insumo', '—') }}</td>
                <td class="py-3 px-4">{{ data_get($m, 'almacen', '—') }}</td>

                <td class="py-3 px-4 text-right font-semibold">
                  {{ number_format($cantidad, 2) }}
                </td>
              </tr>
            @empty
              <tr>
                <td class="py-10 px-4 text-center text-gray-600" colspan="7">
                  Sin movimientos.
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

  </div>
@endsection
