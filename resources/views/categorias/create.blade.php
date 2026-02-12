<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva Categoría</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('categorias.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium">Nombre</label>
                        <input name="nombre" value="{{ old('nombre') }}"
                               class="mt-1 w-full rounded border-gray-300" required>
                        @error('nombre')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Descripción</label>
                        <input name="descripcion" value="{{ old('descripcion') }}"
                               class="mt-1 w-full rounded border-gray-300">
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="activa" value="1"
                               class="rounded border-gray-300" {{ old('activa', true) ? 'checked' : '' }}>
                        <span>Activa</span>
                    </div>

                    <div class="flex gap-2">
                        <button class="px-4 py-2 bg-gray-900 text-white rounded hover:bg-gray-800">
                            Guardar
                        </button>
                        <a href="{{ route('categorias.index') }}" class="px-4 py-2 rounded border">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
