@extends('layouts.app')

@section('title', 'Kardex')
@section('page_title', 'Kardex')
@section('page_subtitle') Movimientos de entradas y salidas. @endsection

@php
  /** @var \Illuminate\Support\Collection $almacenes */
  $almMap = $almacenes->keyBy('id');
@endphp

@section('page_actions')
  <div class="flex gap-2">
    <x-btn variant="secondary" href="{{ route('reportes.kardex') }}">
      <x-icon name="x" class="h-4 w-4" /> Limpiar
    </x-btn>

    <x-btn variant="soft" href="{{ route('reportes.kardex.xlsx', request()->query()) }}">
      XLSX
    </x-btn>

    <x-btn href="{{ route('reportes.kardex.pdf', request()->query()) }}">
      PDF
    </x-btn>
  </div>
@endsection

@section('content')
  <x-card>

    {{-- Totales --}}
    <div class="p-4 border-b bg-white">
      <div class="grid grid-cols-1 sm:grid-cols-6 gap-3">
        <div class="sm:col-span-2">
          <div class="text-xs text-gray-500">Saldo inicial (antes de Desde)</div>
          <div class="text-lg font-bold">{{ number_format((float)$saldoInicial, 3) }}</div>
        </div>

        <div>
          <div class="text-xs text-gray-500">Entradas</div>
          <div class="text-lg font-bold text-green-700">{{ number_format((float)$totals['entradas_qty'], 3) }}</div>
        </div>

        <div>
          <div class="text-xs text-gray-500">Salidas</div>
          <div class="text-lg font-bold text-amber-700">{{ number_format((float)$totals['salidas_qty'], 3) }}</div>
        </div>

        <div>
          <div class="text-xs text-gray-500">Saldo rango</div>
          <div class="text-lg font-bold">{{ number_format((float)$totals['saldo_qty'], 3) }}</div>
        </div>

        <div>
          <div class="text-xs text-gray-500">Saldo final</div>
          <div class="text-lg font-bold">
            {{ number_format((float)$saldoInicial + (float)$totals['saldo_qty'], 3) }}
          </div>
        </div>
      </div>

      @if(!empty($showSaldo))
        <div class="mt-2 text-xs text-gray-500">
          * El saldo por renglón se muestra solo cuando filtras por un Insumo.
        </div>
      @endif
    </div>

    {{-- filtros --}}
    <div class="p-4 border-b bg-white">
      <form method="GET" action="{{ route('reportes.kardex') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
        <div class="sm:col-span-3">
          <label class="text-xs font-semibold text-gray-600">Almacén</label>
          <select name="almacen_id" class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black">
            <option value="">Todos</option>
            @foreach($almacenes as $a)
              <option value="{{ $a->id }}" @selected((string)request('almacen_id') === (string)$a->id)>{{ $a->nombre }}</option>
            @endforeach
          </select>
        </div>

        <div class="sm:col-span-4">
          <label class="text-xs font-semibold text-gray-600">Insumo</label>
          <select name="insumo_id" class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black">
            <option value="">Todos</option>
            @foreach($insumos as $i)
              <option value="{{ $i->id }}" @selected((string)request('insumo_id') === (string)$i->id)>{{ $i->sku }} — {{ $i->nombre }}</option>
            @endforeach
          </select>
        </div>

        <div class="sm:col-span-1">
          <label class="text-xs font-semibold text-gray-600">Tipo</label>
          <select name="tipo" class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black">
            <option value="">Todos</option>
            <option value="ENT" @selected(request('tipo') === 'ENT')>ENT</option>
            <option value="SAL" @selected(request('tipo') === 'SAL')>SAL</option>
          </select>
        </div>

        <div class="sm:col-span-2">
          <label class="text-xs font-semibold text-gray-600">Desde</label>
          <input type="date" name="desde" value="{{ request('desde', $filters['desde'] ?? '') }}"
                 class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black">
        </div>

        <div class="sm:col-span-2">
          <label class="text-xs font-semibold text-gray-600">Hasta</label>
          <input type="date" name="hasta" value="{{ request('hasta', $filters['hasta'] ?? '') }}"
                 class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black">
        </div>

        <div class="sm:col-span-4">
          <label class="text-xs font-semibold text-gray-600">Buscar</label>
          <input type="text" name="q" value="{{ request('q') }}"
                 placeholder="SKU, nombre, folio, tercero…"
                 class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black">
        </div>

        <div class="sm:col-span-2 flex gap-2">
          <x-btn variant="soft" type="submit" class="w-full">
            <x-icon name="search" class="h-4 w-4" /> Filtrar
          </x-btn>
        </div>
      </form>
    </div>

    {{-- tabla --}}
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="text-left font-medium px-4 py-3">Fecha</th>
            <th class="text-left font-medium px-4 py-3">Tipo</th>
            <th class="text-left font-medium px-4 py-3">Folio</th>
            <th class="text-left font-medium px-4 py-3">Almacén</th>
            <th class="text-left font-medium px-4 py-3">Insumo</th>
            <th class="text-left font-medium px-4 py-3">Tercero</th>
            <th class="text-right font-medium px-4 py-3">Cantidad</th>
            <th class="text-right font-medium px-4 py-3">Costo</th>
            <th class="text-right font-medium px-4 py-3">Subtotal</th>
            @if(!empty($showSaldo))
              <th class="text-right font-medium px-4 py-3">Saldo</th>
            @endif
          </tr>
        </thead>

        <tbody class="divide-y">
          @forelse($movs as $m)
            @php
              $almNombre = $almMap->get((int)$m->almacen_id)->nombre ?? '—';
              $isEnt = $m->tipo === 'ENT';

              // estos vienen con signo real (ENT +, SAL -)
              $qtySigned = (float) $m->cantidad;
              $subSigned = (float) $m->subtotal;

              // display bonito (SAL positivo)
              $qtyShow = $isEnt ? $qtySigned : abs($qtySigned);
              $subShow = $isEnt ? $subSigned : abs($subSigned);
            @endphp

            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3">{{ \Illuminate\Support\Carbon::parse($m->fecha)->format('d/m/Y') }}</td>

              <td class="px-4 py-3">
                @if($isEnt)
                  <x-badge variant="success">ENT</x-badge>
                @else
                  <x-badge variant="gold">SAL</x-badge>
                @endif
              </td>

              <td class="px-4 py-3 font-medium">{{ $m->folio }}</td>
              <td class="px-4 py-3">{{ $almNombre }}</td>
              <td class="px-4 py-3">{{ $m->sku }} — {{ $m->insumo_nombre }}</td>
              <td class="px-4 py-3">{{ $m->tercero ?? '—' }}</td>

              <td class="px-4 py-3 text-right font-semibold {{ $isEnt ? 'text-green-700' : 'text-amber-700' }}">
                {{ number_format($qtyShow, 3) }}
              </td>

              <td class="px-4 py-3 text-right">${{ number_format((float)$m->costo_unitario, 2) }}</td>

              <td class="px-4 py-3 text-right font-semibold">
                ${{ number_format($subShow, 2) }}
              </td>

              @if(!empty($showSaldo))
                <td class="px-4 py-3 text-right font-bold">
                  {{ number_format((float)($m->saldo ?? 0), 3) }}
                </td>
              @endif
            </tr>
          @empty
            <tr>
              <td colspan="{{ !empty($showSaldo) ? 10 : 9 }}" class="px-4 py-10 text-center text-gray-500">
                Sin movimientos con estos filtros.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-4 border-t">
      {{ $movs->links() }}
    </div>

  </x-card>
@endsection
