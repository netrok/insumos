@extends('layouts.app')

@section('title', 'Auditoría de movimientos')
@section('page_title', 'Auditoría de movimientos')
@section('page_subtitle')
  Entradas y salidas con filtros. Aquí se ve la verdad.
@endsection

@section('content')
  <x-card>
    <form method="GET" action="{{ route('admin.auditoria.movimientos') }}" class="p-4 border-b">
      <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
        <div>
          <label class="text-xs text-gray-500">Desde</label>
          <input type="date" name="desde" value="{{ request('desde') }}" class="w-full rounded-xl border px-3 py-2">
        </div>
        <div>
          <label class="text-xs text-gray-500">Hasta</label>
          <input type="date" name="hasta" value="{{ request('hasta') }}" class="w-full rounded-xl border px-3 py-2">
        </div>
        <div>
          <label class="text-xs text-gray-500">Tipo</label>
          <select name="tipo" class="w-full rounded-xl border px-3 py-2">
            <option value="">Todos</option>
            <option value="ENT" @selected(request('tipo')==='ENT')>ENT</option>
            <option value="SAL" @selected(request('tipo')==='SAL')>SAL</option>
          </select>
        </div>
        <div>
          <label class="text-xs text-gray-500">Almacén</label>
          <select name="almacen_id" class="w-full rounded-xl border px-3 py-2">
            <option value="">Todos</option>
            @foreach($almacenes as $a)
              <option value="{{ $a->id }}" @selected((string)$a->id === (string)request('almacen_id'))>
                {{ $a->nombre }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="md:col-span-2">
          <label class="text-xs text-gray-500">Buscar (folio o insumo)</label>
          <input name="q" value="{{ request('q') }}" class="w-full rounded-xl border px-3 py-2" placeholder="ENT-0001, guantes, cloro...">
        </div>
      </div>

      <div class="mt-3 flex gap-2">
        <x-btn type="submit">Filtrar</x-btn>
        <x-btn variant="outline" href="{{ route('admin.auditoria.movimientos') }}">Limpiar</x-btn>
        <x-btn variant="outline" href="{{ route('admin.index') }}">Volver</x-btn>
      </div>
    </form>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr class="text-left border-b">
            <th class="py-3 px-4">Fecha</th>
            <th class="py-3 px-4">Tipo</th>
            <th class="py-3 px-4">Folio</th>
            <th class="py-3 px-4">Insumo</th>
            <th class="py-3 px-4">Almacén</th>
            <th class="py-3 px-4 text-right">Cantidad</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse($movs as $m)
            <tr class="hover:bg-gray-50">
              <td class="py-3 px-4">{{ \Carbon\Carbon::parse($m->fecha)->format('Y-m-d') }}</td>
              <td class="py-3 px-4">
                @if($m->tipo === 'ENT')
                  <span class="inline-flex rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700">ENT</span>
                @else
                  <span class="inline-flex rounded-full bg-rose-50 px-2 py-1 text-xs font-semibold text-rose-700">SAL</span>
                @endif
              </td>
              <td class="py-3 px-4">{{ $m->folio }}</td>
              <td class="py-3 px-4 font-medium">{{ $m->insumo ?? '—' }}</td>
              <td class="py-3 px-4">{{ $m->almacen ?? '—' }}</td>
              <td class="py-3 px-4 text-right font-semibold">{{ number_format($m->cantidad, 2) }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="py-10 px-4 text-center text-gray-600">
                Sin movimientos con esos filtros.
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
