@extends('layouts.app')

@section('title', 'Nueva categoría')
@section('header', 'Catálogos')

@section('page_title', 'Nueva categoría')
@section('page_subtitle', 'Crea una nueva categoría para clasificar insumos')

@section('page_actions')
    <a href="{{ route('categorias.index') }}"
       class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50">
        Volver
    </a>
@endsection

@section('content')
<div class="max-w-3xl">
    <div class="bg-white border rounded-2xl p-6">
        <form method="POST" action="{{ route('categorias.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                <input
                    name="nombre"
                    value="{{ old('nombre') }}"
                    required
                    class="mt-1 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                    placeholder="Ej. Limpieza"
                >
                @error('nombre')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                <input
                    name="descripcion"
                    value="{{ old('descripcion') }}"
                    class="mt-1 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                    placeholder="Opcional"
                >
                @error('descripcion')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex items-center gap-2">
                <input
                    type="checkbox"
                    name="activa"
                    value="1"
                    class="rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                    {{ old('activa', true) ? 'checked' : '' }}
                >
                <span class="text-sm text-gray-700">Activa</span>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 pt-2">
                <button class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:opacity-90">
                    Guardar
                </button>

                <a href="{{ route('categorias.index') }}"
                   class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50 text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
