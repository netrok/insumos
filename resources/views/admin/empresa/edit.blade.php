@php
  use Illuminate\Support\Facades\Storage;

  $logoUrl = !empty($empresa['logo_path'] ?? null)
    ? Storage::url($empresa['logo_path'])
    : null;
@endphp

@extends('layouts.app')

@section('title', 'Empresa')
@section('page_title', 'Empresa')
@section('page_subtitle')
  Datos corporativos para reportes y PDFs.
@endsection

@section('page_actions')
  <a href="{{ route('admin.index') }}"
     class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2
            text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:border-slate-300">
    <span>Volver</span>
  </a>
@endsection

@section('content')
  <div class="space-y-6">

    <x-card class="p-6">
      <form method="POST" action="{{ route('admin.empresa.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Datos generales --}}
        <div>
          <div class="flex items-center justify-between gap-4">
            <div>
              <h3 class="text-base font-extrabold text-slate-900">Datos generales</h3>
              <p class="mt-1 text-sm text-slate-600">Se usan en encabezados, pie de página y reportes.</p>
            </div>
            <div class="hidden sm:flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700">
              <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
              Configuración
            </div>
          </div>

          <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
            {{-- Nombre --}}
            <div>
              <label class="text-xs font-semibold tracking-wider uppercase text-slate-600">Nombre</label>
              <input name="nombre" value="{{ old('nombre', $empresa['nombre'] ?? '') }}"
                     class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm
                            placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300"
                     placeholder="Razón social / nombre comercial">
              @error('nombre') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            {{-- RFC --}}
            <div>
              <label class="text-xs font-semibold tracking-wider uppercase text-slate-600">RFC</label>
              <input name="rfc" value="{{ old('rfc', $empresa['rfc'] ?? '') }}"
                     class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm
                            placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300"
                     placeholder="ABC123456789">
              @error('rfc') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            {{-- Dirección --}}
            <div class="lg:col-span-2">
              <label class="text-xs font-semibold tracking-wider uppercase text-slate-600">Dirección</label>
              <input name="direccion" value="{{ old('direccion', $empresa['direccion'] ?? '') }}"
                     class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm
                            placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300"
                     placeholder="Calle, número, colonia, municipio, estado">
              @error('direccion') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            {{-- Teléfono --}}
            <div>
              <label class="text-xs font-semibold tracking-wider uppercase text-slate-600">Teléfono</label>
              <input name="telefono" value="{{ old('telefono', $empresa['telefono'] ?? '') }}"
                     class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm
                            placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300"
                     placeholder="33 0000 0000">
              @error('telefono') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div>
              <label class="text-xs font-semibold tracking-wider uppercase text-slate-600">Email</label>
              <input name="email" value="{{ old('email', $empresa['email'] ?? '') }}"
                     class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm
                            placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300"
                     placeholder="contacto@empresa.com">
              @error('email') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            {{-- Leyenda --}}
            <div class="lg:col-span-2">
              <label class="text-xs font-semibold tracking-wider uppercase text-slate-600">Leyenda (pie de página)</label>
              <input name="leyenda" value="{{ old('leyenda', $empresa['leyenda'] ?? '') }}"
                     class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm
                            placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300"
                     placeholder="Documento interno generado por Insumos.">
              @error('leyenda') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
          </div>
        </div>

        <hr class="border-slate-200">

        {{-- Logo --}}
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
          <div class="lg:col-span-2">
            <h3 class="text-base font-extrabold text-slate-900">Logo</h3>
            <p class="mt-1 text-sm text-slate-600">PNG/JPG/WEBP (máx 2MB). Se usa en PDFs.</p>

            <div class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-5">
              <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                  <div class="rounded-xl border border-slate-200 bg-white p-3">
                    <x-icon name="image" class="h-5 w-5 text-slate-700" />
                  </div>
                  <div>
                    <p class="text-sm font-semibold text-slate-900">Subir logo</p>
                    <p class="text-xs text-slate-600">Elige un archivo para actualizar la imagen.</p>
                  </div>
                </div>

                <label class="inline-flex cursor-pointer items-center justify-center rounded-xl bg-slate-900 px-4 py-2
                              text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                  Seleccionar archivo
                  <input type="file" name="logo" class="hidden" accept=".png,.jpg,.jpeg,.webp">
                </label>
              </div>

              <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-600">
                  {{ $logoUrl ? 'Logo cargado.' : 'Sin logo cargado.' }}
                </p>

                <div class="flex flex-wrap items-center gap-4">
                  <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                    <input type="checkbox" name="mostrar_logo" value="1"
                           @checked(old('mostrar_logo', $empresa['mostrar_logo'] ?? true))
                           class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-200">
                    Mostrar logo en reportes
                  </label>

                  @if($logoUrl)
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-rose-700">
                      <input type="checkbox" name="remove_logo" value="1"
                             class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-200">
                      Quitar logo
                    </label>
                  @endif
                </div>
              </div>
            </div>

            @error('logo') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
          </div>

          {{-- Preview --}}
          <div class="lg:col-span-1">
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
              <div class="flex items-center justify-between">
                <p class="text-sm font-extrabold text-slate-900">Vista previa</p>
                <span class="text-xs font-semibold text-slate-600">PDF</span>
              </div>

              <div class="mt-4 flex h-36 items-center justify-center rounded-xl bg-slate-50 border border-slate-200 overflow-hidden">
                @if($logoUrl)
                  <img src="{{ $logoUrl }}" class="max-h-28 object-contain" alt="Logo">
                @else
                  <div class="text-center">
                    <x-icon name="image" class="mx-auto h-6 w-6 text-slate-400" />
                    <p class="mt-2 text-xs font-semibold text-slate-500">Sin logo</p>
                  </div>
                @endif
              </div>

              <p class="mt-3 text-xs text-slate-600">
                Recomendado: fondo transparente y buen contraste.
              </p>
            </div>
          </div>
        </div>

        {{-- Acciones --}}
        <div class="flex items-center justify-end gap-3 pt-2">
          <a href="{{ route('admin.index') }}"
             class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700
                    shadow-sm transition hover:bg-slate-50 hover:border-slate-300">
            Cancelar
          </a>

          <button type="submit"
                  class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm
                         transition hover:bg-slate-800">
            Guardar
          </button>
        </div>
      </form>
    </x-card>

  </div>
@endsection
