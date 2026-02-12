<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Insumos</h2>
            <a href="{{ route('insumos.create') }}"
               class="px-4 py-2 bg-gray-900 text-white rounded hover:bg-gray-800">
                + Nuevo
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(session('ok'))
                    <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                        {{ session('ok') }}
                    </div>
                @endif

                {{-- Filtros --}}
                <form method="GET" class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Buscar</label>
                        <input name="q" value="{{ $q }}"
                               placeholder="SKU o nombre..."
                               class="w-full rounded border-gray-300 focus:border-gray-800 focus:ring-gray-800" />
                    </div>

                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Categoría</label>
                        <select name="categoria_id"
                                class="w-full rounded border-gray-300 focus:border-gray-800 focus:ring-gray-800">
                            <option value="">Todas</option>
                            @foreach($categorias as $c)
                                <option value="{{ $c->id }}" @selected((string)$categoriaId === (string)$c->id)>
                                    {{ $c->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Activo</label>
                        <select name="activo"
                                class="w-full rounded border-gray-300 focus:border-gray-800 focus:ring-gray-800">
                            <option value="">Todos</option>
                            <option value="1" @selected($activo === '1')>Sí</option>
                            <option value="0" @selected($activo === '0')>No</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button class="px-4 py-2 bg-gray-900 text-white rounded hover:bg-gray-800">
                            Filtrar
                        </button>
                        <a href="{{ route('insumos.index') }}"
                           class="px-4 py-2 bg-gray-100 text-gray-800 rounded hover:bg-gray-200">
                            Limpiar
                        </a>
                    </div>
                </form>

                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="py-2">Insumo</th>
                            <th class="py-2">Categoría</th>
                            <th class="py-2">Unidad</th>
                            <th class="py-2">Activo</th>
                            <th class="py-2 w-48">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $it)
                            <tr class="border-b">
                                <td class="py-2">
                                    <div class="font-medium">
                                        <a href="{{ route('insumos.show', $it) }}"
                                           class="text-blue-700 hover:underline">
                                            {{ $it->nombre }}
                                        </a>
                                    </div>
                                    <div class="text-gray-500">
                                        <span class="font-mono text-xs">{{ $it->sku }}</span>
                                        @if($it->descripcion)
                                            <span class="mx-1">•</span>{{ \Illuminate\Support\Str::limit($it->descripcion, 60) }}
                                        @endif
                                    </div>
                                </td>
                                <td class="py-2">{{ $it->categoria?->nombre ?? '—' }}</td>
                                <td class="py-2">{{ $it->unidad?->nombre ?? '—' }}</td>
                                <td class="py-2">
                                    @if($it->activo)
                                        <span class="px-2 py-1 rounded bg-green-100 text-green-700">Sí</span>
                                    @else
                                        <span class="px-2 py-1 rounded bg-gray-100 text-gray-700">No</span>
                                    @endif
                                </td>
                                <td class="py-2">
                                    <div class="flex gap-2">
                                        <a class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-500"
                                           href="{{ route('insumos.edit', $it) }}">Editar</a>

                                        <form method="POST" action="{{ route('insumos.destroy', $it) }}"
                                              onsubmit="return confirm('¿Eliminar insumo?');">
                                            @csrf @method('DELETE')
                                            <button class="px-3 py-1 rounded bg-red-600 text-white hover:bg-red-500">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500">
                                    No hay insumos todavía.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
