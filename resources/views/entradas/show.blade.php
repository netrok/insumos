@extends('layouts.app')

@section('title', 'Detalle de entrada')

@section('page_title', 'Detalle de entrada')
@section('page_subtitle', "Folio: {$entrada->folio}")

@section('page_actions')
    <a href="{{ route('entradas.index') }}"
       class="inline-flex items-center px-4 py-2 bg-white border rounded-lg text-sm hover:bg-gray-50">
        ← Volver
    </a>
@endsection

@section('content')
    <div class="bg-white border rounded-lg">
        <div class="p-6 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <div class="text-gray-500">Fecha</div>
                    <div class="font-medium">{{ $entrada->fecha?->format('Y-m-d') }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Almacén</div>
                    <div class="font-medium">{{ $entrada->almacen?->nombre ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Proveedor</div>
                    <div class="font-medium">{{ $entrada->proveedor?->nombre ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Tipo</div>
                    <div class="font-medium">{{ $entrada->tipo }}</div>
                </div>

                <div class="md:col-span-4">
                    <div class="text-gray-500">Observaciones</div>
                    <div class="font-medium">{{ $entrada->observaciones ?: '—' }}</div>
                </div>
            </div>

            <div class="border-t pt-4">
                <h3 class="font-semibold text-gray-800 mb-2">Detalles</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr class="text-left border-b">
                                <th class="py-2 px-3">Insumo</th>
                                <th class="py-2 px-3">Cantidad</th>
                                <th class="py-2 px-3">Costo</th>
                                <th class="py-2 px-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entrada->detalles as $d)
                                <tr class="border-b">
                                    <td class="py-2 px-3">{{ $d->insumo?->nombre ?? ('ID '.$d->insumo_id) }}</td>
                                    <td class="py-2 px-3">{{ $d->cantidad }}</td>
                                    <td class="py-2 px-3">$ {{ number_format((float)$d->costo_unitario, 2) }}</td>
                                    <td class="py-2 px-3">$ {{ number_format((float)$d->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-right">
                    <div class="text-gray-600 text-sm">Total</div>
                    <div class="text-2xl font-semibold">$ {{ number_format((float)$entrada->total, 2) }}</div>
                </div>
            </div>

        </div>
    </div>
@endsection
