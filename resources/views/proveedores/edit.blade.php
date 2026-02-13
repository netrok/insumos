@extends('layouts.app')

@section('title', 'Editar proveedor')
@section('page_title', 'Editar proveedor')

@section('page_subtitle')
  {{ $proveedor->nombre }}
@endsection

@section('page_actions')
  <x-btn variant="secondary" href="{{ route('proveedores.index') }}">
    <x-icon name="arrow-left" class="h-4 w-4" />
    Volver
  </x-btn>
@endsection

@section('content')
  <div class="max-w-5xl">
    <x-card>
      <form method="POST" action="{{ route('proveedores.update', $proveedor) }}" class="p-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          {{-- Nombre --}}
          <div class="sm:col-span-2">
            <label class="text-xs font-semibold text-gray-600">Nombre</label>
            <input
              type="text"
              name="nombre"
              value="{{ old('nombre', $proveedor->nombre) }}"
              placeholder="Ej. Papelería López SA de CV"
              class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
              required
              autofocus
            />
            @error('nombre')
              <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
          </div>

          {{-- RFC --}}
          <div>
            <label class="text-xs font-semibold text-gray-600">RFC (opcional)</label>
            <input
              type="text"
              name="rfc"
              value="{{ old('rfc', $proveedor->rfc) }}"
              placeholder="Ej. XAXX010101000"
              class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black uppercase"
              autocomplete="off"
            />
            <div class="mt-1 text-xs text-gray-500">Se convertirá a mayúsculas automáticamente.</div>
            @error('rfc')
              <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
          </div>

          {{-- Teléfono --}}
          <div>
            <label class="text-xs font-semibold text-gray-600">Teléfono (opcional)</label>
            <input
              type="text"
              name="telefono"
              value="{{ old('telefono', $proveedor->telefono) }}"
              placeholder="Ej. 33 1234 5678"
              class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
              inputmode="tel"
              autocomplete="tel"
            />
            @error('telefono')
              <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
          </div>

          {{-- Email --}}
          <div class="sm:col-span-2">
            <label class="text-xs font-semibold text-gray-600">Email (opcional)</label>
            <input
              type="email"
              name="email"
              value="{{ old('email', $proveedor->email) }}"
              placeholder="Ej. compras@proveedor.com"
              class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
              autocomplete="email"
            />
            @error('email')
              <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
          </div>

          {{-- Dirección --}}
          <div class="sm:col-span-2">
            <label class="text-xs font-semibold text-gray-600">Dirección (opcional)</label>
            <textarea
              name="direccion"
              rows="3"
              placeholder="Calle, número, colonia, municipio, estado, CP…"
              class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
            >{{ old('direccion', $proveedor->direccion) }}</textarea>
            @error('direccion')
              <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
          </div>
        </div>

        {{-- Activo --}}
        <div class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 p-4 bg-white">
          <div>
            <div class="text-sm font-semibold text-gray-900">Activo</div>
            <div class="text-xs text-gray-500">Si está inactivo, no aparecerá en formularios.</div>
          </div>

          <label class="inline-flex items-center cursor-pointer select-none">
            <input
              type="checkbox"
              name="activo"
              value="1"
              class="sr-only peer"
              @checked(old('activo', $proveedor->activo))
            >
            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-gv-gold relative transition">
              <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full transition peer-checked:translate-x-5"></div>
            </div>
          </label>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-2 justify-end">
          <x-btn variant="secondary" href="{{ route('proveedores.index') }}">
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
