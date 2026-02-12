@extends('layouts.app')

@section('title', 'Editar unidad')
@section('header', 'Catálogos')

@section('page_title', 'Editar unidad')
@section('page_subtitle', 'Actualiza la información de la unidad')

@section('page_actions')
    <a href="{{ route('unidades.index') }}"
       class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50">
        Volver
    </a>
@endsection

@section('content')
<div class="max-w-3xl">
    <div class="bg-white border rounded-2xl p-6">
        <form method="POST" action="{{ route('unidades.update', $unidad) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                <input
                    name="nombre"
                    value="{{ old('nombre', $unidad->nombre) }}"
                    required
                    class="mt-1 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                >
                @error('nombre')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Clave (ej. PZA)</label>
                <input
                    name="clave"
                    value="{{ old('clave', $unidad->clave) }}"
                    required
                    class="mt-1 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900 uppercase"
                >
                @error('clave')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
                <div class="text-xs text-gray-500 mt-1">Se guardará en mayúsculas automáticamente.</div>
            </div>

            <div class="flex items-center gap-2">
                <input
                    type="checkbox"
                    name="activa"
                    value="1"
                    class="rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                    {{ old('activa', $unidad->activa) ? 'checked' : '' }}
                >
                <span class="text-sm text-gray-700">Activa</span>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 pt-2">
                <button class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:opacity-90">
                    Guardar cambios
                </button>

                <a href="{{ route('unidades.index') }}"
                   class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50 text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
