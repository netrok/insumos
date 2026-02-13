@extends('layouts.app')

@section('title', 'Nueva salida')
@section('page_title', 'Nueva salida')

@section('page_subtitle')
  Registra una salida de insumos y descuenta existencias del almacén.
@endsection

@section('page_actions')
  <x-btn variant="secondary" href="{{ route('salidas.index') }}">
    <x-icon name="arrow-left" class="h-4 w-4" />
    Volver
  </x-btn>
@endsection

@section('content')
  <x-card>
    {{-- ✅ Error global (stock insuficiente o error de detalles) --}}
    @error('detalles')
      <div class="mx-6 mt-6 rounded-xl border border-red-200 bg-red-50 text-red-700 px-4 py-3">
        <div class="font-semibold">No se pudo registrar la salida</div>
        <div class="text-sm mt-1">{{ $message }}</div>
      </div>
    @enderror

    <form method="POST" action="{{ route('salidas.store') }}" class="p-6 space-y-6">
      @csrf

      {{-- Encabezado --}}
      <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
        <div class="sm:col-span-3">
          <label class="text-xs font-semibold text-gray-600">Fecha</label>
          <input
            type="date"
            name="fecha"
            value="{{ old('fecha', now()->format('Y-m-d')) }}"
            class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
            required
          >
          @error('fecha') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-4">
          <label class="text-xs font-semibold text-gray-600">Almacén</label>
          <select
            name="almacen_id"
            class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
            required
          >
            <option value="">Selecciona…</option>
            @foreach($almacenes as $a)
              <option value="{{ $a->id }}" @selected(old('almacen_id') == $a->id)>{{ $a->nombre }}</option>
            @endforeach
          </select>
          @error('almacen_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-3">
          <label class="text-xs font-semibold text-gray-600">Tipo</label>
          <select
            name="tipo"
            class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
            required
          >
            @foreach($tipos as $t)
              <option value="{{ $t }}" @selected(old('tipo', 'consumo') === $t)>{{ strtoupper($t) }}</option>
            @endforeach
          </select>
          @error('tipo') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-12">
          <label class="text-xs font-semibold text-gray-600">Observaciones</label>
          <textarea
            name="observaciones"
            rows="2"
            class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
            placeholder="Opcional"
          >{{ old('observaciones') }}</textarea>
          @error('observaciones') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>

      {{-- Detalles --}}
      <div class="border rounded-2xl overflow-hidden">
        <div class="px-4 py-3 bg-gray-50 flex items-center justify-between">
          <div class="font-semibold text-gray-800">Detalles</div>

          <x-btn variant="soft" type="button" onclick="addRow()">
            <x-icon name="plus" class="h-4 w-4" />
            Agregar renglón
          </x-btn>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-white text-gray-600 border-b">
              <tr>
                <th class="text-left font-medium px-4 py-3">Insumo</th>
                <th class="text-right font-medium px-4 py-3 w-40">Cantidad</th>
                <th class="text-right font-medium px-4 py-3 w-44">Costo unit.</th>
                <th class="text-right font-medium px-4 py-3 w-44">Subtotal</th>
                <th class="text-right font-medium px-4 py-3 w-20"></th>
              </tr>
            </thead>

            @php
              $oldDetalles = old('detalles');
              if (!is_array($oldDetalles) || count($oldDetalles) === 0) {
                $oldDetalles = [
                  ['insumo_id' => '', 'cantidad' => '1', 'costo_unitario' => '0'],
                ];
              }
            @endphp

            <tbody id="detallesBody" class="divide-y bg-white">
              @foreach($oldDetalles as $idx => $row)
                <tr class="detalle-row">
                  <td class="px-4 py-3">
                    <select
                      name="detalles[{{ $idx }}][insumo_id]"
                      class="w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black insumo-select"
                      required
                    >
                      <option value="">Selecciona…</option>
                      @foreach($insumos as $i)
                        <option
                          value="{{ $i->id }}"
                          data-costo="{{ (float) ($i->costo_promedio ?? 0) }}"
                          @selected((string)($row['insumo_id'] ?? '') === (string)$i->id)
                        >
                          {{ $i->sku }} — {{ $i->nombre }}
                        </option>
                      @endforeach
                    </select>
                    @error("detalles.$idx.insumo_id") <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                  </td>

                  <td class="px-4 py-3">
                    <input
                      type="number"
                      step="0.001"
                      min="0.001"
                      name="detalles[{{ $idx }}][cantidad]"
                      value="{{ $row['cantidad'] ?? '1' }}"
                      class="w-full text-right rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
                      oninput="recalcRow(this)"
                      required
                    >
                    @error("detalles.$idx.cantidad") <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                  </td>

                  <td class="px-4 py-3">
                    <input
                      type="number"
                      step="0.01"
                      min="0"
                      name="detalles[{{ $idx }}][costo_unitario]"
                      value="{{ $row['costo_unitario'] ?? '0' }}"
                      class="w-full text-right rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
                      oninput="recalcRow(this)"
                    >
                    <p class="text-[11px] text-gray-500 mt-1">0 = usa costo promedio</p>
                    @error("detalles.$idx.costo_unitario") <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                  </td>

                  <td class="px-4 py-3 text-right font-semibold subtotal-cell">$0.00</td>

                  <td class="px-4 py-3 text-right">
                    <button
                      type="button"
                      onclick="removeRow(this)"
                      class="inline-flex items-center justify-center rounded-xl px-3 py-2 border border-red-200 text-red-700 hover:bg-red-50"
                      title="Quitar"
                    >
                      <span class="sr-only">Quitar</span>
                      🗑️
                    </button>
                  </td>
                </tr>
              @endforeach
            </tbody>

            <tfoot class="bg-gray-50 border-t">
              <tr>
                <td colspan="3" class="px-4 py-3 text-right font-semibold">Total</td>
                <td class="px-4 py-3 text-right font-bold" id="totalCell">$0.00</td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <div class="flex justify-end gap-2">
        <x-btn variant="secondary" href="{{ route('salidas.index') }}">
          Cancelar
        </x-btn>

        <x-btn type="submit" onclick="return confirm('¿Registrar esta salida? Esto descontará existencias.');">
          <x-icon name="check" class="h-4 w-4" />
          Guardar salida
        </x-btn>
      </div>
    </form>
  </x-card>

  <script>
    let rowIndex = document.querySelectorAll('#detallesBody tr.detalle-row').length;

    function money(n) {
      const v = Number.isFinite(n) ? n : 0;
      return '$' + v.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function getRow(el) {
      return el.closest('tr.detalle-row');
    }

    function recalcRow(el) {
      const tr = getRow(el);
      const qty = parseFloat(tr.querySelector('input[name*="[cantidad]"]').value || '0') || 0;

      const sel = tr.querySelector('select[name*="[insumo_id]"]');
      const costoProm = parseFloat(sel?.selectedOptions?.[0]?.dataset?.costo || '0') || 0;

      const costInput = tr.querySelector('input[name*="[costo_unitario]"]');
      let cost = parseFloat(costInput.value || '0') || 0;

      if (cost <= 0) cost = costoProm;

      const sub = qty * cost;
      tr.querySelector('.subtotal-cell').textContent = money(sub);

      recalcTotal();
    }

    function recalcTotal() {
      let sum = 0;
      document.querySelectorAll('#detallesBody tr.detalle-row').forEach(tr => {
        const qty = parseFloat(tr.querySelector('input[name*="[cantidad]"]').value || '0') || 0;

        const sel = tr.querySelector('select[name*="[insumo_id]"]');
        const costoProm = parseFloat(sel?.selectedOptions?.[0]?.dataset?.costo || '0') || 0;

        const costInput = tr.querySelector('input[name*="[costo_unitario]"]');
        let cost = parseFloat(costInput.value || '0') || 0;
        if (cost <= 0) cost = costoProm;

        sum += (qty * cost);
      });

      document.getElementById('totalCell').textContent = money(sum);
    }

    function addRow() {
      const tbody = document.getElementById('detallesBody');

      const tr = document.createElement('tr');
      tr.className = 'detalle-row';

      tr.innerHTML = `
        <td class="px-4 py-3">
          <select name="detalles[${rowIndex}][insumo_id]" class="w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black insumo-select" required>
            <option value="">Selecciona…</option>
            @foreach($insumos as $i)
              <option value="{{ $i->id }}" data-costo="{{ (float) ($i->costo_promedio ?? 0) }}">
                {{ $i->sku }} — {{ $i->nombre }}
              </option>
            @endforeach
          </select>
        </td>

        <td class="px-4 py-3">
          <input type="number" step="0.001" min="0.001" value="1"
                 name="detalles[${rowIndex}][cantidad]"
                 class="w-full text-right rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
                 oninput="recalcRow(this)" required>
        </td>

        <td class="px-4 py-3">
          <input type="number" step="0.01" min="0" value="0"
                 name="detalles[${rowIndex}][costo_unitario]"
                 class="w-full text-right rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
                 oninput="recalcRow(this)">
          <p class="text-[11px] text-gray-500 mt-1">0 = usa costo promedio</p>
        </td>

        <td class="px-4 py-3 text-right font-semibold subtotal-cell">$0.00</td>

        <td class="px-4 py-3 text-right">
          <button type="button" onclick="removeRow(this)"
                  class="inline-flex items-center justify-center rounded-xl px-3 py-2 border border-red-200 text-red-700 hover:bg-red-50"
                  title="Quitar">
            <span class="sr-only">Quitar</span>
            🗑️
          </button>
        </td>
      `;

      tbody.appendChild(tr);
      rowIndex++;

      tr.querySelector('select').addEventListener('change', (e) => recalcRow(e.target));
      recalcTotal();
    }

    function removeRow(btn) {
      const tbody = document.getElementById('detallesBody');
      const rows = tbody.querySelectorAll('tr.detalle-row');
      if (rows.length <= 1) {
        alert('Debe existir al menos un renglón.');
        return;
      }
      getRow(btn).remove();
      recalcTotal();
    }

    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('#detallesBody .insumo-select').forEach(sel => {
        sel.addEventListener('change', (e) => recalcRow(e.target));
      });

      // recalcula todo al cargar (por si vienes de errores y ya hay datos)
      document.querySelectorAll('#detallesBody tr.detalle-row').forEach(tr => {
        const any = tr.querySelector('input[name*="[cantidad]"]') || tr.querySelector('select[name*="[insumo_id]"]');
        if (any) recalcRow(any);
      });
      recalcTotal();
    });
  </script>
@endsection
