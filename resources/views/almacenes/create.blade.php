<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo Almacén</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('almacenes.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium">Nombre</label>
                        <input name="nombre" value="{{ old('nombre') }}"
                               class="mt-1 w-full rounded border-gray-300" required>
                        @error('nombre')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Código (ej. ALM-CEN)</label>
                        <input name="codigo" value="{{ old('codigo') }}"
                               class="mt-1 w-full rounded border-gray-300 uppercase" required>
                        @error('codigo')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Ubicación (opcional)</label>
                        <input name="ubicacion" value="{{ old('ubicacion') }}"
                               class="mt-1 w-full rounded border-gray-300">
                        @error('ubicacion')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="activo" value="1"
                               class="rounded border-gray-300" {{ old('activo', true) ? 'checked' : '' }}>
                        <span>Activo</span>
                    </div>

                    <div class="flex gap-2">
                        <button class="px-4 py-2 bg-gray-900 text-white rounded hover:bg-gray-800">
                            Guardar
                        </button>
                        <a href="{{ route('almacenes.index') }}" class="px-4 py-2 rounded border">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
