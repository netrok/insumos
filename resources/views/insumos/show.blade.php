<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalle de insumo</h2>
            <div class="flex gap-2">
                <a href="{{ route('insumos.edit', $item) }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500">
                    Editar
                </a>
                <a href="{{ route('insumos.index') }}"
                   class="px-4 py-2 bg-gray-100 text-gray-800 rounded hover:bg-gray-200">
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="text-xs text-gray-500">SKU</div>
                        <div class="font-mono">{{ $item->sku }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Estado</div>
                        @if($item->activo)
                            <span class="inline-block px-2 py-1 rounded bg-green-100 text-green-700">Activo</span>
                        @else
                            <span class="inline-block px-2 py-1 rounded bg-gray-100 text-gray-700">Inactivo</span>
                        @endif
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Nombre</div>
                        <div class="font-medium">{{ $item->nombre }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Categoría / Unidad</div>
                        <div>{{ $item->categoria?->nombre ?? '—' }} / {{ $item->unidad?->nombre ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Costo promedio</div>
                        <div>${{ number_format((float)$item->costo_promedio, 2) }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Stock mínimo</div>
                        <div>{{ (int)$item->stock_minimo }}</div>
                    </div>
                </div>

                @if($item->descripcion)
                    <div>
                        <div class="text-xs text-gray-500">Descripción</div>
                        <div class="text-gray-800">{{ $item->descripcion }}</div>
                    </div>
                @endif

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-gray-800">Existencias por almacén</h3>
                        <span class="text-xs text-gray-500">No se edita aquí; se mueve por movimientos.</span>
                    </div>

                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-2">Almacén</th>
                                <th class="py-2">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($item->existencias as $ex)
                                <tr class="border-b">
                                    <td class="py-2">{{ $ex->almacen?->nombre ?? '—' }}</td>
                                    <td class="py-2 font-mono">{{ (int)$ex->cantidad }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-6 text-center text-gray-500">
                                        Sin existencias registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
