@extends('layouts.app')

@section('title', 'Administración')
@section('page_title', 'Administración')
@section('page_subtitle')
  Configuración, folios, parámetros y auditoría. Aquí se mueve el volante del sistema.
@endsection

@section('content')
  <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

    {{-- Empresa --}}
    <a href="{{ route('admin.empresa.edit') }}" class="group block">
      <x-card
        class="p-6 rounded-2xl border border-slate-200 bg-white shadow-sm transition
               hover:-translate-y-0.5 hover:shadow-md hover:border-slate-300"
      >
        <div class="flex items-start justify-between gap-4">
          <div>
            <div class="text-xs font-semibold tracking-wider uppercase text-slate-500">
              Configuración
            </div>
            <div class="mt-1 text-lg font-extrabold text-slate-900">
              Empresa
            </div>
          </div>

          <div
            class="shrink-0 rounded-xl border border-slate-200 bg-slate-50 p-3 transition
                   group-hover:bg-slate-900 group-hover:border-slate-900"
          >
            <x-icon name="settings" class="h-5 w-5 text-slate-700 transition group-hover:text-white" />
          </div>
        </div>

        <div class="mt-3 text-sm text-slate-600">
          Nombre, RFC, logo para PDFs y datos generales.
        </div>

        <div class="mt-5 flex items-center gap-2 text-sm font-semibold text-slate-700">
          <span class="transition group-hover:text-slate-900">Abrir</span>
          <span class="translate-x-0 transition group-hover:translate-x-1">→</span>
        </div>
      </x-card>
    </a>

    {{-- Folios --}}
    <a href="{{ route('admin.folios.index') }}" class="group block">
      <x-card
        class="p-6 rounded-2xl border border-slate-200 bg-white shadow-sm transition
               hover:-translate-y-0.5 hover:shadow-md hover:border-slate-300"
      >
        <div class="flex items-start justify-between gap-4">
          <div>
            <div class="text-xs font-semibold tracking-wider uppercase text-slate-500">
              Control
            </div>
            <div class="mt-1 text-lg font-extrabold text-slate-900">
              Folios
            </div>
          </div>

          <div
            class="shrink-0 rounded-xl border border-slate-200 bg-slate-50 p-3 transition
                   group-hover:bg-slate-900 group-hover:border-slate-900"
          >
            <x-icon name="hash" class="h-5 w-5 text-slate-700 transition group-hover:text-white" />
          </div>
        </div>

        <div class="mt-3 text-sm text-slate-600">
          Prefijos y consecutivos por módulo.
        </div>

        <div class="mt-5 flex items-center gap-2 text-sm font-semibold text-slate-700">
          <span class="transition group-hover:text-slate-900">Abrir</span>
          <span class="translate-x-0 transition group-hover:translate-x-1">→</span>
        </div>
      </x-card>
    </a>

    {{-- Parámetros --}}
    <a href="{{ route('admin.parametros.index') }}" class="group block">
      <x-card
        class="p-6 rounded-2xl border border-slate-200 bg-white shadow-sm transition
               hover:-translate-y-0.5 hover:shadow-md hover:border-slate-300"
      >
        <div class="flex items-start justify-between gap-4">
          <div>
            <div class="text-xs font-semibold tracking-wider uppercase text-slate-500">
              Reglas
            </div>
            <div class="mt-1 text-lg font-extrabold text-slate-900">
              Parámetros
            </div>
          </div>

          <div
            class="shrink-0 rounded-xl border border-slate-200 bg-slate-50 p-3 transition
                   group-hover:bg-slate-900 group-hover:border-slate-900"
          >
            <x-icon name="sliders" class="h-5 w-5 text-slate-700 transition group-hover:text-white" />
          </div>
        </div>

        <div class="mt-3 text-sm text-slate-600">
          Stock mínimo, alertas, decimales, aprobaciones.
        </div>

        <div class="mt-5 flex items-center gap-2 text-sm font-semibold text-slate-700">
          <span class="transition group-hover:text-slate-900">Abrir</span>
          <span class="translate-x-0 transition group-hover:translate-x-1">→</span>
        </div>
      </x-card>
    </a>

    {{-- Movimientos --}}
    <a href="{{ route('admin.auditoria.movimientos') }}" class="group block">
      <x-card
        class="p-6 rounded-2xl border border-slate-200 bg-white shadow-sm transition
               hover:-translate-y-0.5 hover:shadow-md hover:border-slate-300"
      >
        <div class="flex items-start justify-between gap-4">
          <div>
            <div class="text-xs font-semibold tracking-wider uppercase text-slate-500">
              Auditoría
            </div>
            <div class="mt-1 text-lg font-extrabold text-slate-900">
              Movimientos
            </div>
          </div>

          <div
            class="shrink-0 rounded-xl border border-slate-200 bg-slate-50 p-3 transition
                   group-hover:bg-slate-900 group-hover:border-slate-900"
          >
            <x-icon name="list" class="h-5 w-5 text-slate-700 transition group-hover:text-white" />
          </div>
        </div>

        <div class="mt-3 text-sm text-slate-600">
          Entradas/Salidas con filtros por fecha, almacén e insumo.
        </div>

        <div class="mt-5 flex items-center gap-2 text-sm font-semibold text-slate-700">
          <span class="transition group-hover:text-slate-900">Abrir</span>
          <span class="translate-x-0 transition group-hover:translate-x-1">→</span>
        </div>
      </x-card>
    </a>

  </div>
@endsection
