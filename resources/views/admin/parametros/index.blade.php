@extends('layouts.app')

@section('title', 'Parámetros')
@section('page_title', 'Parámetros')
@section('page_subtitle')
  Reglas del sistema: inventario, operación y alertas. Ajusta aquí antes de que el negocio “te ajuste” a ti.
@endsection

@section('page_actions')
  <a href="{{ route('admin.index') }}"
     class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2
            text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:border-slate-300">
    Volver
  </a>
@endsection

@section('content')
  <x-card class="p-6">
    <form method="POST" action="{{ route('admin.parametros.update') }}" class="space-y-8">
      @csrf
      @method('PUT')

      {{-- INVENTARIO --}}
      <div>
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-base font-extrabold text-slate-900">Inventario</h3>
            <p class="mt-1 text-sm text-slate-600">Stock mínimo, alertas y cómo se muestran cantidades.</p>
          </div>
          <span class="hidden sm:inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700">
            Stock
          </span>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
          <div>
            <label class="text-xs font-semibold tracking-wider uppercase text-slate-600">Decimales (cantidad)</label>
            <input type="number" min="0" max="6" name="decimales_cantidad"
                   value="{{ old('decimales_cantidad', $params['decimales_cantidad']) }}"
                   class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm
                          placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300">
            @error('decimales_cantidad') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            <p class="mt-2 text-xs text-slate-500">Recomendado: 2 para piezas/unidades; 3+ si manejas fracciones.</p>
          </div>

          <div>
            <label class="text-xs font-semibold tracking-wider uppercase text-slate-600">Stock mínimo default</label>
            <input type="number" min="0" step="0.01" name="stock_minimo_default"
                   value="{{ old('stock_minimo_default', $params['stock_minimo_default']) }}"
                   class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm
                          placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300">
            @error('stock_minimo_default') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            <p class="mt-2 text-xs text-slate-500">Sugerencia al crear insumos nuevos (puedes ajustarlo por insumo).</p>
          </div>

          <div class="lg:col-span-2">
            <label class="text-xs font-semibold tracking-wider uppercase text-slate-600">Alerta bajo stock (umbral)</label>
            <input type="number" min="0" step="0.01" name="alerta_bajo_stock"
                   value="{{ old('alerta_bajo_stock', $params['alerta_bajo_stock']) }}"
                   class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm
                          placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300">
            @error('alerta_bajo_stock') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            <p class="mt-2 text-xs text-slate-500">Ej: 1 o 2. Cuando el stock sea menor o igual, se marca como bajo.</p>
          </div>
        </div>
      </div>

      <hr class="border-slate-200">

      {{-- OPERACIÓN --}}
      <div>
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-base font-extrabold text-slate-900">Operación</h3>
            <p class="mt-1 text-sm text-slate-600">Controles para evitar salidas sin control.</p>
          </div>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
          <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <input type="checkbox" name="salidas_requieren_aprob" value="1"
                   @checked(old('salidas_requieren_aprob', $params['salidas_requieren_aprob']))
                   class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-200">
            <div>
              <div class="text-sm font-extrabold text-slate-900">Requerir aprobación en salidas</div>
              <div class="mt-1 text-sm text-slate-600">Antes de descontar inventario, alguien autorizado valida.</div>
            </div>
          </label>

          <div>
            <label class="text-xs font-semibold tracking-wider uppercase text-slate-600">Almacén default</label>
            <select name="almacen_default_id"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm
                           focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300">
              <option value="">— Sin default —</option>
              @foreach($almacenes as $a)
                <option value="{{ $a->id }}"
                  @selected((string) old('almacen_default_id', $params['almacen_default_id']) === (string) $a->id)
                >
                  {{ $a->nombre }}
                </option>
              @endforeach
            </select>
            @error('almacen_default_id') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            <p class="mt-2 text-xs text-slate-500">Se preselecciona en entradas/salidas para ir más rápido.</p>
          </div>
        </div>
      </div>

      <hr class="border-slate-200">

      {{-- KARDEX --}}
      <div>
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-base font-extrabold text-slate-900">Kardex</h3>
            <p class="mt-1 text-sm text-slate-600">Parámetros para reportes y apertura.</p>
          </div>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
          <div>
            <label class="text-xs font-semibold tracking-wider uppercase text-slate-600">Saldo inicial (apertura)</label>
            <input type="number" step="0.01" name="kardex_saldo_inicial"
                   value="{{ old('kardex_saldo_inicial', $params['kardex_saldo_inicial']) }}"
                   class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm
                          placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300"
                   placeholder="0.00">
            @error('kardex_saldo_inicial') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            <p class="mt-2 text-xs text-slate-500">Útil si migras inventario y quieres una apertura controlada.</p>
          </div>
        </div>
      </div>

      {{-- ACCIONES --}}
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
