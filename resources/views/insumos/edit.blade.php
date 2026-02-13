@extends('layouts.app')

@section('title', 'Editar insumo')
@section('header', 'Catálogos')

@section('page_title', 'Editar insumo')
@section('page_subtitle', 'Actualiza la información del insumo.')

@section('page_actions')
  <x-btn variant="secondary" href="{{ route('insumos.index') }}">
    <x-icon name="arrow-left" class="h-4 w-4" />
    Volver
  </x-btn>
@endsection

@section('content')
  @php
    /** @var \App\Models\Insumo $item */
    $cats = $categorias ?? collect();
    $unds = $unidades ?? collect();
  @endphp

  <div class="max-w-5xl">
    <x-card>
      <form method="POST" action="{{ route('insumos.update', $item) }}" class="p-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          {{-- SKU --}}
          <div>
            <label class="text-xs font-semibold text-gray-600">SKU</label>
            <input
              type="text"
              name="sku"
              value="{{ old('sku', $item->sku ?? '') }}"
              class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black uppercase"
              required
              autofocus
            />
            <div class="mt-1 text-xs text-gray-500">Se guardará en mayúsculas automáticamente.</div>
            @error('sku')
              <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
          </div>

          {{-- Nombre --}}
          <div>
            <label class="text-xs font-semibold text-gray-600">Nombre</label>
            <input
              type="text"
              name="nombre"
              value="{{ old('nombre', $item->nombre ?? '') }}"
              class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
              required
            />
            @error('nombre')
              <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
          </div>

          {{-- Categoría --}}
          <div>
            <label class="text-xs font-semibold text-gray-600">Categoría</label>
            <select
              name="categoria_id"
              class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
              required
            >
              <option value="">Selecciona…</option>
              @foreach($cats as $c)
                <option value="{{ $c->id }}" @selected(old('categoria_id', $item->categoria_id ?? null) == $c->id)>
                  {{ $c->nombre }}
                </option>
              @endforeach
            </select>
            @error('categoria_id')
              <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
          </div>

          {{-- Unidad --}}
          <div>
            <label class="text-xs font-semibold text-gray-600">Unidad</label>
            <select
              name="unidad_id"
              class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
              required
            >
              <option value="">Selecciona…</option>
              @foreach($unds as $u)
                @php($clave = $u->clave ?? '')
                <option value="{{ $u->id }}" @selected(old('unidad_id', $item->unidad_id ?? null) == $u->id)>
                  {{ $u->nombre }}@if($clave !== '') ({{ $clave }}) @endif
                </option>
              @endforeach
            </select>
            @error('unidad_id')
              <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
          </div>

          {{-- Costo promedio --}}
          <div>
            <label class="text-xs font-semibold text-gray-600">Costo promedio (opcional)</label>
            <input
              type="number"
              step="0.01"
              min="0"
              name="costo_promedio"
              value="{{ old('costo_promedio', (string)($item->costo_promedio ?? '')) }}"
              class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
            />
            @error('costo_promedio')
              <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
          </div>

          {{-- Stock mínimo --}}
          <div>
            <label class="text-xs font-semibold text-gray-600">Stock mínimo (opcional)</label>
            <input
              type="number"
              min="0"
              step="1"
              name="stock_minimo"
              value="{{ old('stock_minimo', (string)($item->stock_minimo ?? '')) }}"
              class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
            />
            @error('stock_minimo')
              <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
          </div>

          {{-- Descripción --}}
          <div class="sm:col-span-2">
            <label class="text-xs font-semibold text-gray-600">Descripción (opcional)</label>
            <textarea
              name="descripcion"
              rows="3"
              class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
            >{{ old('descripcion', $item->descripcion ?? '') }}</textarea>
            @error('descripcion')
              <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
          </div>
        </div>

        {{-- Activo --}}
        <div class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 p-4 bg-white">
          <div>
            <div class="text-sm font-semibold text-gray-900">Activo</div>
            <div class="text-xs text-gray-500">Controla si se puede usar en entradas y existencias.</div>
          </div>

          <label class="inline-flex items-center cursor-pointer select-none">
            <input type="checkbox" name="activo" value="1" class="sr-only peer"
                   @checked(old('activo', (bool)($item->activo ?? true)))>
            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-gv-gold relative transition">
              <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full transition peer-checked:translate-x-5"></div>
            </div>
          </label>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-2 justify-end">
          <x-btn variant="secondary" href="{{ route('insumos.index') }}">
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
