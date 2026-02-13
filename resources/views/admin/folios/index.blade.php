@extends('layouts.app')

@section('title', 'Folios')
@section('page_title', 'Folios')
@section('page_subtitle', 'Prefijos y consecutivos para Entradas y Salidas.')

@section('page_actions')
  <x-btn variant="outline" href="{{ route('admin.index') }}">Volver</x-btn>
@endsection

@section('content')
  <x-card>
    <form method="POST" action="{{ route('admin.folios.update') }}" class="space-y-6">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="rounded-2xl border p-4">
          <div class="font-bold">Entradas</div>
          <div class="mt-3 grid grid-cols-3 gap-3">
            <div>
              <label class="text-sm font-semibold">Prefijo</label>
              <input name="entradas_prefijo" value="{{ old('entradas_prefijo', $folios['entradas']['prefijo']) }}"
                     class="mt-1 w-full rounded-xl border px-3 py-2" required>
            </div>
            <div>
              <label class="text-sm font-semibold">Consecutivo</label>
              <input type="number" name="entradas_consecutivo" min="1"
                     value="{{ old('entradas_consecutivo', $folios['entradas']['consecutivo']) }}"
                     class="mt-1 w-full rounded-xl border px-3 py-2" required>
            </div>
            <div>
              <label class="text-sm font-semibold">Dígitos</label>
              <input type="number" name="entradas_padding" min="3" max="10"
                     value="{{ old('entradas_padding', $folios['entradas']['padding']) }}"
                     class="mt-1 w-full rounded-xl border px-3 py-2" required>
            </div>
          </div>
          <div class="mt-3 text-sm text-gray-600">
            Ejemplo: <span class="font-semibold">
              {{ $folios['entradas']['prefijo'] }}-{{ str_pad($folios['entradas']['consecutivo'], $folios['entradas']['padding'], '0', STR_PAD_LEFT) }}
            </span>
          </div>
        </div>

        <div class="rounded-2xl border p-4">
          <div class="font-bold">Salidas</div>
          <div class="mt-3 grid grid-cols-3 gap-3">
            <div>
              <label class="text-sm font-semibold">Prefijo</label>
              <input name="salidas_prefijo" value="{{ old('salidas_prefijo', $folios['salidas']['prefijo']) }}"
                     class="mt-1 w-full rounded-xl border px-3 py-2" required>
            </div>
            <div>
              <label class="text-sm font-semibold">Consecutivo</label>
              <input type="number" name="salidas_consecutivo" min="1"
                     value="{{ old('salidas_consecutivo', $folios['salidas']['consecutivo']) }}"
                     class="mt-1 w-full rounded-xl border px-3 py-2" required>
            </div>
            <div>
              <label class="text-sm font-semibold">Dígitos</label>
              <input type="number" name="salidas_padding" min="3" max="10"
                     value="{{ old('salidas_padding', $folios['salidas']['padding']) }}"
                     class="mt-1 w-full rounded-xl border px-3 py-2" required>
            </div>
          </div>
          <div class="mt-3 text-sm text-gray-600">
            Ejemplo: <span class="font-semibold">
              {{ $folios['salidas']['prefijo'] }}-{{ str_pad($folios['salidas']['consecutivo'], $folios['salidas']['padding'], '0', STR_PAD_LEFT) }}
            </span>
          </div>
        </div>
      </div>

      <div class="flex justify-end">
        <x-btn type="submit">Guardar</x-btn>
      </div>
    </form>
  </x-card>
@endsection
