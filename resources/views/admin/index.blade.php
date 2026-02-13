@extends('layouts.app')

@section('title', 'Administración')
@section('page_title', 'Administración')
@section('page_subtitle')
  Configuración, folios, parámetros y auditoría. Aquí se mueve el volante del sistema.
@endsection

@section('content')
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

    <a href="{{ route('admin.empresa.edit') }}" class="block">
      <x-card class="p-5 hover:bg-gray-50">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-sm text-gray-500">Configuración</div>
            <div class="text-lg font-bold">Empresa</div>
          </div>
          <x-icon name="settings" class="h-6 w-6 text-gray-400" />
        </div>
        <div class="mt-3 text-sm text-gray-600">Nombre, RFC, logo para PDFs y datos generales.</div>
      </x-card>
    </a>

    <a href="{{ route('admin.folios.index') }}" class="block">
      <x-card class="p-5 hover:bg-gray-50">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-sm text-gray-500">Control</div>
            <div class="text-lg font-bold">Folios</div>
          </div>
          <x-icon name="hash" class="h-6 w-6 text-gray-400" />
        </div>
        <div class="mt-3 text-sm text-gray-600">Prefijos y consecutivos por módulo.</div>
      </x-card>
    </a>

    <a href="{{ route('admin.parametros.index') }}" class="block">
      <x-card class="p-5 hover:bg-gray-50">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-sm text-gray-500">Reglas</div>
            <div class="text-lg font-bold">Parámetros</div>
          </div>
          <x-icon name="sliders" class="h-6 w-6 text-gray-400" />
        </div>
        <div class="mt-3 text-sm text-gray-600">Stock mínimo, alertas, decimales, aprobaciones.</div>
      </x-card>
    </a>

    <a href="{{ route('admin.auditoria.movimientos') }}" class="block">
      <x-card class="p-5 hover:bg-gray-50">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-sm text-gray-500">Auditoría</div>
            <div class="text-lg font-bold">Movimientos</div>
          </div>
          <x-icon name="list" class="h-6 w-6 text-gray-400" />
        </div>
        <div class="mt-3 text-sm text-gray-600">Entradas/Salidas con filtros por fecha, almacén e insumo.</div>
      </x-card>
    </a>

  </div>
@endsection
