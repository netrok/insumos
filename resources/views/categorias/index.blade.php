@extends('layouts.app')

@section('title', 'Categorías')
@section('header', 'Catálogos')

@section('page_title', 'Categorías')
@section('page_subtitle', 'Administra el catálogo de categorías')

@section('page_actions')
    <a href="{{ route('categorias.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:opacity-90">
        <span>＋</span> Nueva
    </a>
@endsection

@section('content')
<div class="bg-white border rounded-2xl overflow-hidden">

    {{-- Toolbar --}}
    <div class="p-4 border-b">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
            <div class="flex-1">
                <input
                    type="text"
                    name="q"
                    value="{{ $q ?? '' }}"
                    placeholder="Buscar por nombre…"
                    class="w-full sm:max-w-md rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                />
            </div>

            <div class="flex gap-2">
                <button class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50">
                    Buscar
                </button>

                @if(!empty($q))
                    <a href="{{ route('categorias.index') }}"
                       class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="text-left font-medium px-4 py-3">Nombre</th>
                    <th class="text-left font-medium px-4 py-3">Activa</th>
                    <th class="text-right font-medium px-4 py-3 w-56">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($items as $it)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-medium">
                                <a href="{{ route('categorias.show', $it) }}" class="hover:underline">
                                    {{ $it->nombre }}
                                </a>
                            </div>
                            <div class="text-gray-500">
                                {{ $it->descripcion ?: '—' }}
                            </div>
                        </td>

                        <td class="px-4 py-3">
                            @if($it->activa)
                                <span class="inline-flex items-center px-2 py-1 rounded bg-green-100 text-green-700 text-xs">
                                    Sí
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 text-gray-700 text-xs">
                                    No
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('categorias.edit', $it) }}"
                                   class="px-3 py-1.5 rounded-lg border text-sm hover:bg-gray-50">
                                    Editar
                                </a>

                                <form method="POST" action="{{ route('categorias.destroy', $it) }}"
                                      onsubmit="return confirm('¿Eliminar esta categoría?');">
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
                        <td colspan="3" class="px-4 py-10 text-center text-gray-500">
                            No hay categorías{{ !empty($q) ? ' que coincidan con tu búsqueda' : '' }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="p-4 border-t">
        {{ $items->links() }}
    </div>
</div>
@endsection
