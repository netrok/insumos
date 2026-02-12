<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Almacenes</h2>

            <a href="{{ route('almacenes.create') }}"
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

                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="py-2">Nombre</th>
                            <th class="py-2">Código</th>
                            <th class="py-2">Activo</th>
                            <th class="py-2 w-44">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($items as $it)
                            <tr class="border-b">
                                <td class="py-2">
                                    <div class="font-medium">
                                        <a href="{{ route('almacenes.show', $it) }}"
                                           class="text-blue-700 hover:underline">
                                            {{ $it->nombre }}
                                        </a>
                                    </div>
                                    <div class="text-gray-500">{{ $it->ubicacion ?: '—' }}</div>
                                </td>

                                <td class="py-2">
                                    <span class="px-2 py-1 rounded bg-gray-100 text-gray-800">
                                        {{ $it->codigo }}
                                    </span>
                                </td>

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
                                           href="{{ route('almacenes.edit', $it) }}">
                                            Editar
                                        </a>

                                        <form method="POST" action="{{ route('almacenes.destroy', $it) }}"
                                              onsubmit="return confirm('¿Eliminar almacén?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="px-3 py-1 rounded bg-red-600 text-white hover:bg-red-500">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-gray-500">
                                    No hay almacenes todavía.
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
