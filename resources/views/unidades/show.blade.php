<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Unidad: {{ $item->nombre }}
                </h2>
                <p class="text-sm text-gray-500">Detalle</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('unidades.index') }}"
                   class="px-4 py-2 rounded border bg-white hover:bg-gray-50">
                    ← Regresar
                </a>

                <a href="{{ route('unidades.edit', $item) }}"
                   class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-500">
                    Editar
                </a>

                <form method="POST" action="{{ route('unidades.destroy', $item) }}"
                      onsubmit="return confirm('¿Eliminar esta unidad?');">
                    @csrf
                    @method('DELETE')
                    <button class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-500">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500">Nombre</div>
                            <div class="mt-1 text-base font-medium text-gray-900">{{ $item->nombre }}</div>
                        </div>

                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500">Clave</div>
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 text-gray-800 text-sm">
                                    {{ $item->clave }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500">Estado</div>
                            <div class="mt-1">
                                @if($item->activa)
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-green-100 text-green-700 text-sm">
                                        Activa
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 text-gray-700 text-sm">
                                        Inactiva
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t">
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
            </div>
        </div>
    </div>
</x-app-layout>
