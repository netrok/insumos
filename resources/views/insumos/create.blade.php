<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo insumo</h2>
            <a href="{{ route('insumos.index') }}"
               class="px-4 py-2 bg-gray-100 text-gray-800 rounded hover:bg-gray-200">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('insumos.store') }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">SKU</label>
                            <input name="sku" value="{{ old('sku') }}"
                                   class="mt-1 w-full rounded border-gray-300 focus:border-gray-800 focus:ring-gray-800"
                                   placeholder="INS-0001">
                            @error('sku') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input name="nombre" value="{{ old('nombre') }}"
                                   class="mt-1 w-full rounded border-gray-300 focus:border-gray-800 focus:ring-gray-800"
                                   placeholder="Tóner HP 85A">
                            @error('nombre') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Descripción</label>
                        <textarea name="descripcion" rows="3"
                                  class="mt-1 w-full rounded border-gray-300 focus:border-gray-800 focus:ring-gray-800"
                                  placeholder="Opcional...">{{ old('descripcion') }}</textarea>
                        @error('descripcion') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Categoría</label>
                            <select name="categoria_id"
                                    class="mt-1 w-full rounded border-gray-300 focus:border-gray-800 focus:ring-gray-800">
                                <option value="">Selecciona</option>
                                @foreach($categorias as $c)
                                    <option value="{{ $c->id }}" @selected(old('categoria_id') == $c->id)>{{ $c->nombre }}</option>
                                @endforeach
                            </select>
                            @error('categoria_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Unidad</label>
                            <select name="unidad_id"
                                    class="mt-1 w-full rounded border-gray-300 focus:border-gray-800 focus:ring-gray-800">
                                <option value="">Selecciona</option>
                                @foreach($unidades as $u)
                                    <option value="{{ $u->id }}" @selected(old('unidad_id') == $u->id)>{{ $u->nombre }}</option>
                                @endforeach
                            </select>
                            @error('unidad_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Costo promedio</label>
                            <input name="costo_promedio" value="{{ old('costo_promedio', 0) }}" type="number" step="0.01" min="0"
                                   class="mt-1 w-full rounded border-gray-300 focus:border-gray-800 focus:ring-gray-800">
                            @error('costo_promedio') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stock mínimo</label>
                            <input name="stock_minimo" value="{{ old('stock_minimo', 0) }}" type="number" min="0"
                                   class="mt-1 w-full rounded border-gray-300 focus:border-gray-800 focus:ring-gray-800">
                            @error('stock_minimo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input id="activo" name="activo" type="checkbox" value="1"
                               class="rounded border-gray-300 text-gray-900 focus:ring-gray-800"
                               @checked(old('activo', true))>
                        <label for="activo" class="text-sm text-gray-700">Activo</label>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button class="px-4 py-2 bg-gray-900 text-white rounded hover:bg-gray-800">
                            Guardar
                        </button>
                        <a href="{{ route('insumos.index') }}"
                           class="px-4 py-2 bg-gray-100 text-gray-800 rounded hover:bg-gray-200">
                            Cancelar
                        </a>
                    </div>
                </form>

                <p class="mt-6 text-xs text-gray-500">
                    Nota: al crear el insumo, se inicializan existencias en todos los almacenes activos.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
