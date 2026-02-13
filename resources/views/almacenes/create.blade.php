@extends('layouts.app')

@section('title', 'Nuevo almacén')
@section('header', 'Catálogos')

@section('page_title', 'Nuevo almacén')
@section('page_subtitle', 'Crea un almacén para controlar existencias.')

@section('page_actions')
  <x-btn variant="secondary" href="{{ route('almacenes.index') }}">
    <x-icon name="arrow-left" class="h-4 w-4" />
    Volver
  </x-btn>
@endsection

@section('content')
  <div class="max-w-4xl">
    <x-card>
      <form method="POST" action="{{ route('almacenes.store') }}" class="p-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          {{-- Nombre --}}
          <div class="sm:col-span-2">
            <label class="text-xs font-semibold text-gray-600">Nombre</label>
            <input
              type="text"
              name="nombre"
              value="{{ old('nombre') }}"
              placeholder="Ej. Almacén Central"
              class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
              required
              autofocus
            />
            @error('nombre')
              <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
          </div>

          {{-- Código --}}
          <div>
            <label class="text-xs font-semibold text-gray-600">Código</label>
            <input
              type="text"
              name="codigo"
              value="{{ old('codigo') }}"
              placeholder="Ej. ALM-CEN, SUC-01"
              class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black uppercase"
              required
            />
            <div class="mt-1 text-xs text-gray-500">Se convertirá a mayúsculas automáticamente.</div>
            @error('codigo')
              <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
          </div>

          {{-- Ubicación --}}
          <div>
            <label class="text-xs font-semibold text-gray-600">Ubicación (opcional)</label>
            <input
              type="text"
              name="ubicacion"
              value="{{ old('ubicacion') }}"
              placeholder="Ej. Bodega planta baja, Andares, etc."
              class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
            />
            @error('ubicacion')
              <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
          </div>
        </div>

        {{-- Activo (toggle GV) --}}
        <div class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 p-4 bg-white">
          <div>
            <div class="text-sm font-semibold text-gray-900">Activo</div>
            <div class="text-xs text-gray-500">Si está inactivo, no podrá usarse en entradas ni existencias.</div>
          </div>

          <label class="inline-flex items-center cursor-pointer select-none">
            <input type="checkbox" name="activo" value="1" class="sr-only peer" @checked(old('activo', true))>
            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-gv-gold relative transition">
              <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full transition peer-checked:translate-x-5"></div>
            </div>
          </label>
        </div>

        {{-- Hint --}}
        <div class="text-xs text-gray-500">
          Tip: usa códigos cortos y consistentes (ej. <span class="font-semibold">SUC-01</span>, <span class="font-semibold">ALM-CEN</span>).
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-2 justify-end">
          <x-btn variant="secondary" href="{{ route('almacenes.index') }}">
            Cancelar
          </x-btn>

          <x-btn type="submit">
            <x-icon name="save" class="h-4 w-4" />
            Guardar
          </x-btn>
        </div>
      </form>
    </x-card>
  </div>
@endsection
