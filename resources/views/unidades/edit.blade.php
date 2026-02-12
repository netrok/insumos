<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Unidad</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('unidades.update', $item) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium">Nombre</label>
                        <input name="nombre" value="{{ old('nombre', $item->nombre) }}"
                               class="mt-1 w-full rounded border-gray-300" required>
                        @error('nombre')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Clave</label>
                        <input name="clave" value="{{ old('clave', $item->clave) }}"
                               class="mt-1 w-full rounded border-gray-300 uppercase" required>
                        @error('clave')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="activa" value="1"
                               class="rounded border-gray-300" {{ old('activa', $item->activa) ? 'checked' : '' }}>
                        <span>Activa</span>
                    </div>

                    <div class="flex gap-2">
                        <button class="px-4 py-2 bg-gray-900 text-white rounded hover:bg-gray-800">
                            Guardar cambios
                        </button>
                        <a href="{{ route('unidades.index') }}" class="px-4 py-2 rounded border">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
