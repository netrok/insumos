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
  @php
    $k_mov = (int) data_get($kpis, 'movimientos', 0);
    $k_ent = (float) data_get($kpis, 'entradas', 0);
    $k_sal = (float) data_get($kpis, 'salidas', 0);
    $k_net = (float) data_get($kpis, 'neto', 0);
  @endphp

  <div class="space-y-4">

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
      <x-card class="p-5">
        <div class="text-xs font-semibold tracking-wider uppercase text-slate-500">Movimientos</div>
        <div class="mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($k_mov) }}</div>
        <div class="mt-1 text-xs text-slate-500">En el rango seleccionado</div>
      </x-card>

      <x-card class="p-5">
        <div class="text-xs font-semibold tracking-wider uppercase text-slate-500">Entradas</div>
        <div class="mt-2 text-2xl font-extrabold text-emerald-700">{{ number_format($k_ent, 2) }}</div>
        <div class="mt-1 text-xs text-slate-500">Suma positiva</div>
      </x-card>

      <x-card class="p-5">
        <div class="text-xs font-semibold tracking-wider uppercase text-slate-500">Salidas</div>
        <div class="mt-2 text-2xl font-extrabold text-rose-700">{{ number_format($k_sal, 2) }}</div>
        <div class="mt-1 text-xs text-slate-500">Suma negativa</div>
      </x-card>

      <x-card class="p-5">
        <div class="text-xs font-semibold tracking-wider uppercase text-slate-500">Neto</div>
        <div class="mt-2 text-2xl font-extrabold {{ $k_net < 0 ? 'text-rose-700' : 'text-slate-900' }}">
          {{ number_format($k_net, 2) }}
        </div>
        <div class="mt-1 text-xs text-slate-500">Entradas + Salidas</div>
      </x-card>
    </div>

    {{-- Filtros --}}
    <x-card class="p-5">
      <form method="GET" action="{{ route('admin.auditoria.movimientos') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">

        <div class="md:col-span-2">
          <label class="block text-xs font-semibold tracking-wider uppercase text-slate-600 mb-1">Búsqueda</label>
          <input
            type="text"
            name="q"
            value="{{ $q ?? request('q') }}"
            placeholder="Folio, usuario, insumo, almacén…"
            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm
                   placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold tracking-wider uppercase text-slate-600 mb-1">Tipo</label>
          <select
            name="tipo"
            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm
                   focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300"
          >
            <option value=""  {{ ($tipo ?? request('tipo',''))==='' ? 'selected' : '' }}>Todos</option>
            <option value="ENT" {{ ($tipo ?? request('tipo'))==='ENT' ? 'selected' : '' }}>ENT</option>
            <option value="SAL" {{ ($tipo ?? request('tipo'))==='SAL' ? 'selected' : '' }}>SAL</option>
          </select>
        </div>

        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-xs font-semibold tracking-wider uppercase text-slate-600 mb-1">Desde</label>
            <input
              type="date"
              name="from"
              value="{{ $from ?? request('from') }}"
              class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm
                     focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300"
            />
          </div>
          <div>
            <label class="block text-xs font-semibold tracking-wider uppercase text-slate-600 mb-1">Hasta</label>
            <input
              type="date"
              name="to"
              value="{{ $to ?? request('to') }}"
              class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm
                     focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300"
            />
          </div>
        </div>

        <div class="md:col-span-4 flex flex-wrap gap-2 pt-1">
          <x-btn type="submit">Aplicar</x-btn>

          @if(filled($q ?? request('q')) || filled($tipo ?? request('tipo')) || filled($from ?? request('from')) || filled($to ?? request('to')))
            <x-btn variant="outline" href="{{ route('admin.auditoria.movimientos') }}">Limpiar</x-btn>
          @endif
        </div>
      </form>
    </x-card>

    {{-- Tabla --}}
    <x-card class="overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
        <div>
          <div class="text-sm font-extrabold text-slate-900">Movimientos</div>
          <div class="text-xs text-slate-600">{{ $items->total() }} registros</div>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50">
            <tr class="text-left border-b border-slate-200 text-slate-700">
              <th class="py-3 px-4 font-semibold">Fecha</th>
              <th class="py-3 px-4 font-semibold">Tipo</th>
              <th class="py-3 px-4 font-semibold">Folio</th>
              <th class="py-3 px-4 font-semibold">Usuario</th>
              <th class="py-3 px-4 font-semibold">Insumo</th>
              <th class="py-3 px-4 font-semibold">Almacén</th>
              <th class="py-3 px-4 font-semibold text-right">Cantidad</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            @forelse($items as $m)
              @php
                $t = (string) data_get($m, 'tipo', '');
                $fechaRaw = data_get($m, 'fecha');
                $fecha = $fechaRaw ? \Carbon\Carbon::parse($fechaRaw)->format('Y-m-d') : '—';
                $cantidad = (float) data_get($m, 'cantidad', 0);
              @endphp

              <tr class="hover:bg-slate-50/60">
                <td class="py-3 px-4 text-slate-700">{{ $fecha }}</td>

                <td class="py-3 px-4">
                  @if($t === 'ENT')
                    <span class="inline-flex rounded-full bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700 border border-emerald-200">ENT</span>
                  @elseif($t === 'SAL')
                    <span class="inline-flex rounded-full bg-rose-50 px-2 py-1 text-xs font-bold text-rose-700 border border-rose-200">SAL</span>
                  @else
                    <span class="inline-flex rounded-full bg-slate-100 px-2 py-1 text-xs font-bold text-slate-700 border border-slate-200">—</span>
                  @endif
                </td>

                <td class="py-3 px-4 font-semibold text-slate-900">{{ data_get($m, 'folio', '—') }}</td>

                <td class="py-3 px-4 text-slate-700">
                  {{ data_get($m, 'usuario') ?: '—' }}
                </td>

                <td class="py-3 px-4 text-slate-700">{{ data_get($m, 'insumo', '—') }}</td>
                <td class="py-3 px-4 text-slate-700">{{ data_get($m, 'almacen', '—') }}</td>

                <td class="py-3 px-4 text-right font-extrabold {{ $cantidad < 0 ? 'text-rose-700' : 'text-slate-900' }}">
                  {{ number_format($cantidad, 2) }}
                </td>
              </tr>
            @empty
              <tr>
                <td class="py-10 px-4 text-center text-slate-600" colspan="7">
                  Sin movimientos.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="px-5 py-4 border-t border-slate-200">
        {{ $items->links() }}
      </div>
    </x-card>

  </div>
@endsection
