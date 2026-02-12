@extends('layouts.app')

@section('title', 'Detalle almacén')
@section('header', 'Catálogos')

@section('page_title', $almacen->nombre)
@section('page_subtitle', 'Detalle del almacén')

@section('page_actions')
    <div class="flex gap-2">
        <a href="{{ route('almacenes.edit', $almacen) }}"
           class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:opacity-90">
            Editar
        </a>

        <a href="{{ route('almacenes.index') }}"
           class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50">
            Volver
        </a>
    </div>
@endsection

@section('content')
<div class="max-w-4xl space-y-4">

    <div class="bg-white border rounded-2xl p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="text-sm text-gray-500">Nombre</div>
                <div class="text-lg font-semibold">{{ $almacen->nombre }}</div>

                <div class="mt-4 text-sm text-gray-500">Código</div>
                <div class="text-gray-800">
                    <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 text-gray-800 text-xs">
                        {{ $almacen->codigo }}
                    </span>
                </div>

                <div class="mt-4 text-sm text-gray-500">Ubicación</div>
                <div class="text-gray-800">{{ $almacen->ubicacion ?: '—' }}</div>
            </div>

            <div class="shrink-0">
                @if($almacen->activo)
                    <span class="inline-flex items-center px-2 py-1 rounded bg-green-100 text-green-700 text-xs">
                        Activo
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 text-gray-700 text-xs">
                        Inactivo
                    </span>
                @endif
            </div>
        </div>

        <div class="mt-6 pt-4 border-t">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-600">
                <div>
                    <div class="text-xs uppercase tracking-wide text-gray-500">Creado</div>
                    <div class="mt-1">{{ $almacen->created_at?->format('Y-m-d H:i') }}</div>
                </div>
                <div>
                    <div class="text-xs uppercase tracking-wide text-gray-500">Última actualización</div>
                    <div class="mt-1">{{ $almacen->updated_at?->format('Y-m-d H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white border rounded-2xl p-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <div class="font-medium">Acciones</div>
                <div class="text-sm text-gray-500">Eliminar es permanente.</div>
            </div>

            <form method="POST" action="{{ route('almacenes.destroy', $almacen) }}"
                  onsubmit="return confirm('¿Eliminar este almacén?');">
                @csrf
                @method('DELETE')
                <button class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm hover:opacity-90">
                    Eliminar
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
