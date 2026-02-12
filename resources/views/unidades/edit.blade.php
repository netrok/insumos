@extends('layouts.app')

@section('title', 'Editar unidad')
@section('header', 'Catálogos')

@section('page_title', 'Editar unidad')
@section('page_subtitle', 'Actualiza la información de la unidad')

@section('page_actions')
  <x-btn variant="secondary" href="{{ route('unidades.index') }}">
    <x-icon name="arrow-left" class="h-4 w-4" />
    Volver
  </x-btn>
@endsection

@section('content')
  <div class="max-w-3xl">
    <x-card>
      <form method="POST" action="{{ route('unidades.update', $unidad) }}" class="p-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          {{-- Nombre --}}
          <div>
            <label class="text-xs font-semibold text-gray-600">Nombre</label>
            <input
              type="text"
              name="nombre"
              value="{{ old('nombre', $unidad->nombre) }}"
              required
              class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
            />
            @error('nombre')
              <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
          </div>

          {{-- Clave --}}
          <div>
            <label class="text-xs font-semibold text-gray-600">Clave (ej. PZA)</label>
            <input
              type="text"
              name="clave"
              value="{{ old('clave', $unidad->clave) }}"
              required
              class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black uppercase"
            />
            <div class="mt-1 text-xs text-gray-500">Se guardará en mayúsculas automáticamente.</div>
            @error('clave')
              <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
          </div>
        </div>

        {{-- Activa (toggle GV) --}}
        <div class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 p-4 bg-white">
          <div>
            <div class="text-sm font-semibold text-gray-900">Activa</div>
            <div class="text-xs text-gray-500">Controla si se puede seleccionar en el sistema.</div>
          </div>

          <label class="inline-flex items-center cursor-pointer select-none">
            <input type="checkbox" name="activa" value="1" class="sr-only peer" @checked(old('activa', $unidad->activa))>
            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-gv-gold relative transition">
              <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full transition peer-checked:translate-x-5"></div>
            </div>
          </label>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-2 justify-end">
          <x-btn variant="secondary" href="{{ route('unidades.index') }}">
            Cancelar
          </x-btn>

          <x-btn type="submit">
            <x-icon name="save" class="h-4 w-4" />
            Guardar cambios
          </x-btn>
        </div>
      </form>
    </x-card>
  </div>
@endsection
