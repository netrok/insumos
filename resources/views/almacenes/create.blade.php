@extends('layouts.app')

@section('title', 'Nuevo almacén')
@section('header', 'Catálogos')

@section('page_title', 'Nuevo almacén')
@section('page_subtitle', 'Crea un almacén para controlar existencias')

@section('page_actions')
    <a href="{{ route('almacenes.index') }}"
       class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50">
        Volver
    </a>
@endsection

@section('content')
<div class="max-w-3xl">
    <div class="bg-white border rounded-2xl p-6">
        <form method="POST" action="{{ route('almacenes.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                <input name="nombre" value="{{ old('nombre') }}" required
                       class="mt-1 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                @error('nombre') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Código</label>
                <input name="codigo" value="{{ old('codigo') }}" required
                       class="mt-1 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900 uppercase"
                       placeholder="Ej. ALM-CEN, SUC-01">
                @error('codigo') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Ubicación (opcional)</label>
                <input name="ubicacion" value="{{ old('ubicacion') }}"
                       class="mt-1 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                       placeholder="Ej. Bodega planta baja, Andares, etc.">
                @error('ubicacion') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="activo" value="1"
                       class="rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                       {{ old('activo', true) ? 'checked' : '' }}>
                <span class="text-sm text-gray-700">Activo</span>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 pt-2">
                <button class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:opacity-90">
                    Guardar
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
