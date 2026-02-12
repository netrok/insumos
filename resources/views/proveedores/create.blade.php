@extends('layouts.app')

@section('title', 'Nuevo proveedor')

@section('page_title', 'Nuevo proveedor')
@section('page_subtitle', 'Captura la información básica del proveedor.')

@section('page_actions')
    <a href="{{ route('proveedores.index') }}"
       class="inline-flex items-center px-4 py-2 bg-white border rounded-lg text-sm hover:bg-gray-50">
        ← Volver
    </a>
@endsection

@section('content')
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">
            <div class="font-semibold mb-2">Corrige lo siguiente:</div>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('proveedores.store') }}" class="bg-white border rounded-lg">
        @csrf
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                <input name="nombre" value="{{ old('nombre') }}" required
                       class="mt-1 block w-full rounded-md border-gray-300">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">RFC</label>
                <input name="rfc" value="{{ old('rfc') }}"
                       class="mt-1 block w-full rounded-md border-gray-300">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                <input name="telefono" value="{{ old('telefono') }}"
                       class="mt-1 block w-full rounded-md border-gray-300">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input name="email" value="{{ old('email') }}"
                       class="mt-1 block w-full rounded-md border-gray-300">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Dirección</label>
                <textarea name="direccion" rows="2"
                          class="mt-1 block w-full rounded-md border-gray-300">{{ old('direccion') }}</textarea>
            </div>

            <div class="md:col-span-2 flex items-center gap-2">
                <input id="activo" type="checkbox" name="activo" value="1"
                       class="rounded border-gray-300" {{ old('activo', '1') ? 'checked' : '' }}>
                <label for="activo" class="text-sm text-gray-700">Activo</label>
            </div>

            <div class="md:col-span-2 flex justify-end gap-3 pt-2">
                <a href="{{ route('proveedores.index') }}"
                   class="px-4 py-2 bg-white border rounded-lg text-sm hover:bg-gray-50">
                    Cancelar
                </a>
                <button class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm hover:opacity-90">
                    Guardar
                </button>
            </div>
        </div>
    </form>
@endsection
