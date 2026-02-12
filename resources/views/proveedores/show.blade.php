@extends('layouts.app')

@section('title', 'Proveedor')

@section('page_title', 'Proveedor')
@section('page_subtitle', $proveedor->nombre)

@section('page_actions')
    <div class="flex gap-2">
        <a href="{{ route('proveedores.edit', $proveedor) }}"
           class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-lg text-sm hover:opacity-90">
            Editar
        </a>
        <a href="{{ route('proveedores.index') }}"
           class="inline-flex items-center px-4 py-2 bg-white border rounded-lg text-sm hover:bg-gray-50">
            ← Volver
        </a>
    </div>
@endsection

@section('content')
    <div class="bg-white border rounded-lg">
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <div class="text-gray-500">RFC</div>
                <div class="font-medium">{{ $proveedor->rfc ?? '—' }}</div>
            </div>
            <div>
                <div class="text-gray-500">Teléfono</div>
                <div class="font-medium">{{ $proveedor->telefono ?? '—' }}</div>
            </div>
            <div class="md:col-span-2">
                <div class="text-gray-500">Email</div>
                <div class="font-medium">{{ $proveedor->email ?? '—' }}</div>
            </div>
            <div class="md:col-span-2">
                <div class="text-gray-500">Dirección</div>
                <div class="font-medium">{{ $proveedor->direccion ?: '—' }}</div>
            </div>
            <div class="md:col-span-2">
                <div class="text-gray-500">Activo</div>
                <div class="font-medium">{{ $proveedor->activo ? 'Sí' : 'No' }}</div>
            </div>
        </div>
    </div>
@endsection
