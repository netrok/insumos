<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar insumo</h2>
            <a href="{{ route('insumos.index') }}"
               class="px-4 py-2 bg-gray-100 text-gray-800 rounded hover:bg-gray-200">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('insumos.update', $item) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">SKU</label>
                            <input name="sku" value="{{ old('sku', $item->sku) }}"
                                   class="mt-1 w-full rounded border-gray-300 focus:border-gray-800 focus:ring-gray-800">
                            @error('sku') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input name="nombre" value="{{ old('nombre', $item->nombre) }}"
                                   class="mt-1 w-full rounded border-gray-300 focus:border-gray-800 focus:ring-gray-800">
                            @error('nombre') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Descripción</label>
                        <textarea name="descripcion" rows="3"
                                  class="mt-1 w-full rounded border-gray-300 focus:border-gray-800 focus:ring-gray-800">{{ old('descripcion', $item->descripcion) }}</textarea>
                        @error('descripcion') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Categoría</label>
                            <select name="categoria_id"
                                    class="mt-1 w-full rounded border-gray-300 focus:border-gray-800 focus:ring-gray-800">
                                @foreach($categorias as $c)
                                    <option value="{{ $c->id }}"
                                        @selected(old('categoria_id', $item->categoria_id) == $c->id)>
                                        {{ $c->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categoria_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Unidad</label>
                            <select name="unidad_id"
                                    class="mt-1 w-full rounded border-gray-300 focus:border-gray-800 focus:ring-gray-800">
                                @foreach($unidades as $u)
                                    <option value="{{ $u->id }}"
                                        @selected(old('unidad_id', $item->unidad_id) == $u->id)>
                                        {{ $u->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unidad_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Costo promedio</label>
                            <input name="costo_promedio" value="{{ old('costo_promedio', $item->costo_promedio) }}" type="number" step="0.01" min="0"
                                   class="mt-1 w-full rounded border-gray-300 focus:border-gray-800 focus:ring-gray-800">
                            @error('costo_promedio') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stock mínimo</label>
                            <input name="stock_minimo" value="{{ old('stock_minimo', $item->stock_minimo) }}" type="number" min="0"
                                   class="mt-1 w-full rounded border-gray-300 focus:border-gray-800 focus:ring-gray-800">
                            @error('stock_minimo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input id="activo" name="activo" type="checkbox" value="1"
                               class="rounded border-gray-300 text-gray-900 focus:ring-gray-800"
                               @checked(old('activo', $item->activo))>
                        <label for="activo" class="text-sm text-gray-700">Activo</label>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button class="px-4 py-2 bg-gray-900 text-white rounded hover:bg-gray-800">
                            Guardar cambios
                        </button>
                        <a href="{{ route('insumos.index') }}"
                           class="px-4 py-2 bg-gray-100 text-gray-800 rounded hover:bg-gray-200">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
