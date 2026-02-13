@extends('layouts.app')

@section('title', 'Salida ' . $salida->folio)
@section('page_title', 'Salida ' . $salida->folio)

@section('page_subtitle')
  Detalle de la salida y sus renglones.
@endsection

@section('page_actions')
  <div class="flex gap-2">
    <x-btn variant="secondary" href="{{ route('salidas.index') }}">
      <x-icon name="arrow-left" class="h-4 w-4" />
      Volver
    </x-btn>

    <x-btn variant="outline" href="{{ route('salidas.edit', $salida) }}">
      <x-icon name="edit" class="h-4 w-4" />
      Editar
    </x-btn>

    <form method="POST" action="{{ route('salidas.destroy', $salida) }}"
          onsubmit="return confirm('¿Eliminar salida {{ $salida->folio }}? Esto regresará existencias.');">
      @csrf
      @method('DELETE')
      <x-btn variant="danger" type="submit">
        <x-icon name="trash" class="h-4 w-4" />
        Eliminar
      </x-btn>
    </form>
  </div>
@endsection

@section('content')
  <x-card>

    {{-- Resumen --}}
    <div class="p-6 border-b bg-white">
      <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
        <div class="sm:col-span-3">
          <div class="text-xs text-gray-500">Folio</div>
          <div class="font-semibold">{{ $salida->folio }}</div>
        </div>

        <div class="sm:col-span-3">
          <div class="text-xs text-gray-500">Fecha</div>
          <div class="font-semibold">{{ \Illuminate\Support\Carbon::parse($salida->fecha)->format('d/m/Y') }}</div>
        </div>

        <div class="sm:col-span-3">
          <div class="text-xs text-gray-500">Almacén</div>
          <div class="font-semibold">{{ $salida->almacen->nombre ?? '—' }}</div>
        </div>

        <div class="sm:col-span-3">
          <div class="text-xs text-gray-500">Tipo</div>
          <div class="mt-1">
            <x-badge variant="gold">{{ strtoupper($salida->tipo) }}</x-badge>
          </div>
        </div>

        <div class="sm:col-span-12">
          <div class="text-xs text-gray-500">Observaciones</div>
          <div class="font-medium text-gray-800">{{ $salida->observaciones ?: '—' }}</div>
        </div>
      </div>
    </div>

    {{-- Detalles --}}
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="text-left font-medium px-4 py-3">Insumo</th>
            <th class="text-right font-medium px-4 py-3 w-40">Cantidad</th>
            <th class="text-right font-medium px-4 py-3 w-44">Costo unit.</th>
            <th class="text-right font-medium px-4 py-3 w-44">Subtotal</th>
          </tr>
        </thead>

        <tbody class="divide-y">
          @foreach($salida->detalles as $d)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3">
                <div class="font-medium">{{ $d->insumo->sku ?? '—' }} — {{ $d->insumo->nombre ?? '—' }}</div>
              </td>
              <td class="px-4 py-3 text-right">{{ number_format((float) $d->cantidad, 3) }}</td>
              <td class="px-4 py-3 text-right">${{ number_format((float) $d->costo_unitario, 2) }}</td>
              <td class="px-4 py-3 text-right font-semibold">${{ number_format((float) $d->subtotal, 2) }}</td>
            </tr>
          @endforeach
        </tbody>

        <tfoot class="bg-gray-50 border-t">
          <tr>
            <td colspan="3" class="px-4 py-3 text-right font-semibold">Total</td>
            <td class="px-4 py-3 text-right font-bold">${{ number_format((float) $salida->total, 2) }}</td>
          </tr>
        </tfoot>
      </table>
    </div>

  </x-card>
@endsection
