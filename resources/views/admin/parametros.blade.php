@extends('layouts.app')

@section('title', 'Parámetros')
@section('page_title', 'Parámetros')
@section('page_subtitle')
  Reglas del sistema: mínimos, alertas, decimales y aprobaciones.
@endsection

@section('content')
  <x-card class="p-6">
    @if(session('status'))
      <div class="mb-4 rounded-xl border bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">
        {{ session('status') }}
      </div>
    @endif

    <form method="POST" action="{{ route('admin.parametros.update') }}" class="space-y-5">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm font-semibold">Stock mínimo default</label>
          <input type="number" step="0.01" min="0" class="mt-1 w-full rounded-xl border px-3 py-2"
                 name="stock_minimo_default" value="{{ old('stock_minimo_default', $params['stock_minimo_default']) }}">
        </div>

        <div>
          <label class="text-sm font-semibold">Alerta bajo stock</label>
          <input type="number" step="0.01" min="0" class="mt-1 w-full rounded-xl border px-3 py-2"
                 name="alerta_bajo_stock" value="{{ old('alerta_bajo_stock', $params['alerta_bajo_stock']) }}">
          <div class="text-xs text-gray-500 mt-1">Si la existencia ≤ este valor, se considera “bajo stock”.</div>
        </div>

        <div>
          <label class="text-sm font-semibold">Decimales para cantidad</label>
          <input type="number" min="0" max="4" class="mt-1 w-full rounded-xl border px-3 py-2"
                 name="decimales_cantidad" value="{{ old('decimales_cantidad', $params['decimales_cantidad']) }}">
        </div>

        <div class="rounded-2xl border p-4">
          <div class="font-bold">Aprobación de salidas</div>
          <label class="mt-3 inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="salidas_requieren_aprob" value="1"
                   @checked(old('salidas_requieren_aprob', $params['salidas_requieren_aprob']))>
            Requerir aprobación para registrar salidas
          </label>
          <div class="text-xs text-gray-500 mt-1">
            (Fase 2: flujo Supervisor → Admin/RRHH si quieres)
          </div>
        </div>
      </div>

      <div class="flex gap-2">
        <x-btn type="submit">Guardar</x-btn>
        <x-btn variant="outline" href="{{ route('admin.index') }}">Volver</x-btn>
      </div>
    </form>
  </x-card>
@endsection
