@extends('layouts.app')

@section('title', 'Folios')
@section('page_title', 'Folios')
@section('page_subtitle')
  Prefijos y consecutivos para Entradas y Salidas.
@endsection

@section('page_actions')
  <a href="{{ route('admin.index') }}"
     class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2
            text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:border-slate-300">
    Volver
  </a>
@endsection

@section('content')
  <x-card class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <form method="POST" action="{{ route('admin.folios.update') }}" class="space-y-6">
      @csrf
      @method('PUT')

      <div class="flex items-center justify-between gap-4">
        <div>
          <h3 class="text-base font-extrabold text-slate-900">Configuración de folios</h3>
          <p class="mt-1 text-sm text-slate-600">
            Define prefijo, consecutivo y dígitos para generar folios consistentes.
          </p>
        </div>

        <div class="hidden sm:flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700">
          <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
          Control
        </div>
      </div>

      <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

        {{-- ENTRADAS --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
          <div class="flex items-start justify-between gap-4">
            <div>
              <p class="text-xs font-semibold tracking-wider uppercase text-slate-500">Entradas</p>
              <p class="mt-1 text-sm text-slate-600">Folios para movimientos de entrada.</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
              <span class="text-xs font-extrabold text-slate-700">ENT</span>
            </div>
          </div>

          <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
              <label class="text-xs font-semibold tracking-wider uppercase text-slate-600">Prefijo</label>
              <input name="entradas_prefijo" value="{{ old('entradas_prefijo', $folios->entradas_prefijo ?? 'ENT') }}"
                     class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-900 shadow-sm
                            focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300"
                     placeholder="ENT">
              @error('entradas_prefijo') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="text-xs font-semibold tracking-wider uppercase text-slate-600">Consecutivo</label>
              <input type="number" min="1" name="entradas_consecutivo" value="{{ old('entradas_consecutivo', $folios->entradas_consecutivo ?? 1) }}"
                     class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-900 shadow-sm
                            focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300"
                     placeholder="1">
              @error('entradas_consecutivo') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="text-xs font-semibold tracking-wider uppercase text-slate-600">Dígitos</label>
              <input type="number" min="1" max="12" name="entradas_digitos" value="{{ old('entradas_digitos', $folios->entradas_digitos ?? 6) }}"
                     class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-900 shadow-sm
                            focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300"
                     placeholder="6">
              @error('entradas_digitos') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
          </div>

          <div class="mt-4 flex items-center justify-between gap-3">
            <p class="text-sm text-slate-600">Ejemplo</p>
            <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-sm font-semibold text-slate-700">
              {{ ($folios->entradas_prefijo ?? 'ENT') . '-' . str_pad(1, (int)($folios->entradas_digitos ?? 6), '0', STR_PAD_LEFT) }}
            </span>
          </div>
        </div>

        {{-- SALIDAS --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
          <div class="flex items-start justify-between gap-4">
            <div>
              <p class="text-xs font-semibold tracking-wider uppercase text-slate-500">Salidas</p>
              <p class="mt-1 text-sm text-slate-600">Folios para movimientos de salida.</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
              <span class="text-xs font-extrabold text-slate-700">SAL</span>
            </div>
          </div>

          <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
              <label class="text-xs font-semibold tracking-wider uppercase text-slate-600">Prefijo</label>
              <input name="salidas_prefijo" value="{{ old('salidas_prefijo', $folios->salidas_prefijo ?? 'SAL') }}"
                     class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-900 shadow-sm
                            focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300"
                     placeholder="SAL">
              @error('salidas_prefijo') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="text-xs font-semibold tracking-wider uppercase text-slate-600">Consecutivo</label>
              <input type="number" min="1" name="salidas_consecutivo" value="{{ old('salidas_consecutivo', $folios->salidas_consecutivo ?? 1) }}"
                     class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-900 shadow-sm
                            focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300"
                     placeholder="1">
              @error('salidas_consecutivo') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="text-xs font-semibold tracking-wider uppercase text-slate-600">Dígitos</label>
              <input type="number" min="1" max="12" name="salidas_digitos" value="{{ old('salidas_digitos', $folios->salidas_digitos ?? 6) }}"
                     class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-900 shadow-sm
                            focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300"
                     placeholder="6">
              @error('salidas_digitos') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
          </div>

          <div class="mt-4 flex items-center justify-between gap-3">
            <p class="text-sm text-slate-600">Ejemplo</p>
            <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-sm font-semibold text-slate-700">
              {{ ($folios->salidas_prefijo ?? 'SAL') . '-' . str_pad(1, (int)($folios->salidas_digitos ?? 6), '0', STR_PAD_LEFT) }}
            </span>
          </div>
        </div>

      </div>

      {{-- acciones --}}
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
@endsection
