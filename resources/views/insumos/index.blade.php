@extends('layouts.app')

@section('title', 'Insumos')
@section('header', 'Catálogos')

@section('page_title', 'Insumos')
@section('page_subtitle', 'Administra el catálogo de insumos')

@section('page_actions')
    <a href="{{ route('insumos.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:opacity-90">
        <span>＋</span> Nuevo
    </a>
@endsection

@section('content')
<div class="bg-white border rounded-2xl overflow-hidden">

    {{-- filtros --}}
    <div class="p-4 border-b">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-600 mb-1">Buscar</label>
                <input
                    type="text"
                    name="q"
                    value="{{ $q ?? '' }}"
                    placeholder="SKU o nombre…"
                    class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                />
            </div>

            <div>
                <label class="block text-xs text-gray-600 mb-1">Categoría</label>
                <select
                    name="categoria_id"
                    class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                >
                    <option value="">Todas</option>
                    @foreach($categorias as $c)
                        <option value="{{ $c->id }}" @selected((string)($categoriaId ?? '') === (string)$c->id)>
                            {{ $c->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs text-gray-600 mb-1">Activo</label>
                <select
                    name="activo"
                    class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                >
                    <option value="">Todos</option>
                    <option value="1" @selected(($activo ?? '') === '1')>Sí</option>
                    <option value="0" @selected(($activo ?? '') === '0')>No</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:opacity-90">
                    Filtrar
                </button>

                <a href="{{ route('insumos.index') }}"
                   class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    {{-- tabla --}}
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="text-left font-medium px-4 py-3">Insumo</th>
                    <th class="text-left font-medium px-4 py-3">Categoría</th>
                    <th class="text-left font-medium px-4 py-3">Unidad</th>
                    <th class="text-left font-medium px-4 py-3">Activo</th>
                    <th class="text-right font-medium px-4 py-3 w-56">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($items as $it)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-medium">
                                <a href="{{ route('insumos.show', $it) }}" class="hover:underline">
                                    {{ $it->nombre }}
                                </a>
                            </div>

                            <div class="text-gray-500 text-xs">
                                <span class="font-mono">{{ $it->sku }}</span>
                                @if($it->descripcion)
                                    <span class="mx-1">•</span>{{ \Illuminate\Support\Str::limit($it->descripcion, 60) }}
                                @endif
                            </div>
                        </td>

                        <td class="px-4 py-3 text-gray-700">
                            {{ $it->categoria?->nombre ?? '—' }}
                        </td>

                        <td class="px-4 py-3 text-gray-700">
                            {{ $it->unidad?->nombre ?? '—' }}
                            @if($it->unidad?->clave)
                                <span class="text-xs text-gray-500">({{ $it->unidad->clave }})</span>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            @if($it->activo)
                                <span class="inline-flex items-center px-2 py-1 rounded bg-green-100 text-green-700 text-xs">Sí</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 text-gray-700 text-xs">No</span>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('insumos.edit', $it) }}"
                                   class="px-3 py-1.5 rounded-lg border text-sm hover:bg-gray-50">
                                    Editar
                                </a>

                                <form method="POST" action="{{ route('insumos.destroy', $it) }}"
                                      onsubmit="return confirm('¿Eliminar este insumo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1.5 rounded-lg bg-red-600 text-white text-sm hover:opacity-90">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                            No hay insumos{{ !empty($q) ? ' que coincidan con tu búsqueda' : '' }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t">
        {{ $items->links() }}
    </div>
</div>
@endsection
