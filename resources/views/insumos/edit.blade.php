@extends('layouts.app')

@section('title', 'Editar insumo')
@section('header', 'Catálogos')

@section('page_title', 'Editar insumo')
@section('page_subtitle', 'Actualiza la información del insumo')

@section('page_actions')
    <a href="{{ route('insumos.index') }}"
       class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50">
        Volver
    </a>
@endsection

@section('content')
<div class="max-w-3xl">
    <div class="bg-white border rounded-2xl p-6">
        <form method="POST" action="{{ route('insumos.update', $item) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">SKU</label>
                    <input name="sku" value="{{ old('sku', $item->sku) }}" required
                           class="mt-1 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900 uppercase">
                    @error('sku') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input name="nombre" value="{{ old('nombre', $item->nombre) }}" required
                           class="mt-1 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                    @error('nombre') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea name="descripcion" rows="3"
                          class="mt-1 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">{{ old('descripcion', $item->descripcion) }}</textarea>
                @error('descripcion') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Categoría</label>
                    <select name="categoria_id" required
                            class="mt-1 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                        <option value="">Selecciona…</option>
                        @foreach($categorias as $c)
                            <option value="{{ $c->id }}"
                                @selected(old('categoria_id', $item->categoria_id) == $c->id)>
                                {{ $c->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('categoria_id') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Unidad</label>
                    <select name="unidad_id" required
                            class="mt-1 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                        <option value="">Selecciona…</option>
                        @foreach($unidades as $u)
                            <option value="{{ $u->id }}"
                                @selected(old('unidad_id', $item->unidad_id) == $u->id)>
                                {{ $u->nombre }}@if(!empty($u->clave)) ({{ $u->clave }}) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('unidad_id') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Costo promedio</label>
                    <input name="costo_promedio"
                           value="{{ old('costo_promedio', $item->costo_promedio) }}"
                           type="number" step="0.01" min="0"
                           class="mt-1 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                    @error('costo_promedio') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Stock mínimo</label>
                    <input name="stock_minimo"
                           value="{{ old('stock_minimo', $item->stock_minimo) }}"
                           type="number" step="1" min="0"
                           class="mt-1 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                    @error('stock_minimo') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input id="activo" type="checkbox" name="activo" value="1"
                       class="rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                       @checked(old('activo', $item->activo))>
                <label for="activo" class="text-sm text-gray-700">Activo</label>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 pt-2">
                <button class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:opacity-90">
                    Guardar cambios
                </button>
                <a href="{{ route('insumos.index') }}"
                   class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50 text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
