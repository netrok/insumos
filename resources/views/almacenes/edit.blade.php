@extends('layouts.app')

@section('title', 'Editar almacén')
@section('header', 'Catálogos')

@section('page_title', 'Editar almacén')
@section('page_subtitle', 'Actualiza la información del almacén')

@section('page_actions')
    <a href="{{ route('almacenes.index') }}"
       class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50">
        Volver
    </a>
@endsection

@section('content')
<div class="max-w-3xl">
    <div class="bg-white border rounded-2xl p-6">
        <form method="POST" action="{{ route('almacenes.update', $almacen) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                <input
                    name="nombre"
                    value="{{ old('nombre', $almacen->nombre) }}"
                    required
                    class="mt-1 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                >
                @error('nombre')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Código</label>
                <input
                    name="codigo"
                    value="{{ old('codigo', $almacen->codigo) }}"
                    required
                    class="mt-1 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900 uppercase"
                >
                @error('codigo')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Ubicación (opcional)</label>
                <input
                    name="ubicacion"
                    value="{{ old('ubicacion', $almacen->ubicacion) }}"
                    class="mt-1 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                >
                @error('ubicacion')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex items-center gap-2">
                <input
                    type="checkbox"
                    name="activo"
                    value="1"
                    class="rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                    {{ old('activo', $almacen->activo) ? 'checked' : '' }}
                >
                <span class="text-sm text-gray-700">Activo</span>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 pt-2">
                <button class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:opacity-90">
                    Guardar cambios
                </button>

                <a href="{{ route('almacenes.index') }}"
                   class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50 text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
