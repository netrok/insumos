@extends('layouts.app')

@section('title', 'Detalle insumo')
@section('header', 'Catálogos')

@section('page_title', $item->nombre)
@section('page_subtitle', 'Detalle del insumo')

@section('page_actions')
    <div class="flex gap-2">
        <a href="{{ route('insumos.edit', $item) }}"
           class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:opacity-90">
            Editar
        </a>

        <a href="{{ route('insumos.index') }}"
           class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50">
            Volver
        </a>
    </div>
@endsection

@section('content')
<div class="max-w-5xl space-y-4">

    {{-- Card: Datos generales --}}
    <div class="bg-white border rounded-2xl p-6">
        <div class="flex items-start justify-between gap-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
                <div>
                    <div class="text-sm text-gray-500">SKU</div>
                    <div class="mt-1 font-mono">
                        <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 text-gray-800 text-xs">
                            {{ $item->sku }}
                        </span>
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Estado</div>
                    <div class="mt-1">
                        @if($item->activo)
                            <span class="inline-flex items-center px-2 py-1 rounded bg-green-100 text-green-700 text-xs">Activo</span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 text-gray-700 text-xs">Inactivo</span>
                        @endif
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Categoría</div>
                    <div class="mt-1 text-gray-800">{{ $item->categoria?->nombre ?: '—' }}</div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Unidad</div>
                    <div class="mt-1 text-gray-800">
                        {{ $item->unidad?->nombre ?: '—' }}
                        @if(!empty($item->unidad?->clave))
                            <span class="text-xs text-gray-500">({{ $item->unidad->clave }})</span>
                        @endif
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Costo promedio</div>
                    <div class="mt-1 text-gray-800">
                        ${{ number_format((float) $item->costo_promedio, 2) }}
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Stock mínimo</div>
                    <div class="mt-1 text-gray-800">
                        {{ (int) $item->stock_minimo }}
                    </div>
                </div>

                <div class="md:col-span-2">
                    <div class="text-sm text-gray-500">Descripción</div>
                    <div class="mt-1 text-gray-800 whitespace-pre-line">
                        {{ $item->descripcion ?: '—' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-600">
                <div>
                    <div class="text-xs uppercase tracking-wide text-gray-500">Creado</div>
                    <div class="mt-1">{{ $item->created_at?->format('Y-m-d H:i') }}</div>
                </div>
                <div>
                    <div class="text-xs uppercase tracking-wide text-gray-500">Última actualización</div>
                    <div class="mt-1">{{ $item->updated_at?->format('Y-m-d H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card: Existencias --}}
    <div class="bg-white border rounded-2xl p-6">
        <div class="flex items-center justify-between mb-3">
            <div>
                <div class="font-medium">Existencias por almacén</div>
                <div class="text-sm text-gray-500">No se edita aquí; se mueve por entradas/salidas/traspasos.</div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left font-medium px-4 py-3">Almacén</th>
                        <th class="text-left font-medium px-4 py-3">Código</th>
                        <th class="text-left font-medium px-4 py-3">Cantidad</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($item->existencias as $ex)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $ex->almacen?->nombre ?: '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 text-gray-800 text-xs">
                                    {{ $ex->almacen?->codigo ?: '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-mono">{{ (int) $ex->cantidad }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-10 text-center text-gray-500">
                                Sin existencias registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Card: acciones peligrosas --}}
    <div class="bg-white border rounded-2xl p-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <div class="font-medium">Acciones</div>
                <div class="text-sm text-gray-500">Eliminar es permanente.</div>
            </div>

            <form method="POST" action="{{ route('insumos.destroy', $item) }}"
                  onsubmit="return confirm('¿Eliminar este insumo?');">
                @csrf
                @method('DELETE')
                <button class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm hover:opacity-90">
                    Eliminar
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
