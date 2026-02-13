@extends('layouts.app')

@section('title', 'Nueva entrada')

@section('page_title', 'Nueva entrada')
@section('page_subtitle', 'Captura insumos, cantidades y costo. Al guardar, se suman existencias.')

@section('page_actions')
  <x-btn variant="secondary" href="{{ route('entradas.index') }}">
    <x-icon name="arrow-left" class="h-4 w-4" />
    Volver
  </x-btn>
@endsection

@section('content')
  @php
    /** @var \Illuminate\Support\Collection|\App\Models\Almacen[] $almacenes */
    /** @var \Illuminate\Support\Collection|\App\Models\Proveedor[] $proveedores */
    /** @var \Illuminate\Support\Collection|\App\Models\Insumo[] $insumos */
    $almacenes = $almacenes ?? collect();
    $proveedores = $proveedores ?? collect();
    $insumos = $insumos ?? collect();

    // Para el JS (siempre array simple, sin "void")
    $insumosJs = $insumos->map(fn ($i) => [
      'id' => $i->id,
      'sku' => $i->sku ?? null,
      'nombre' => $i->nombre,
    ])->values();
  @endphp

  <div class="max-w-6xl">
    <x-card>
      <form method="POST" action="{{ route('entradas.store') }}" class="p-6 space-y-6">
        @csrf

        {{-- Encabezado --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          {{-- Fecha --}}
          <div>
            <label class="text-xs font-semibold text-gray-600">Fecha</label>
            <input
              type="date"
              name="fecha"
              value="{{ old('fecha', now()->format('Y-m-d')) }}"
              class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
            />
            @error('fecha')
              <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
          </div>

          {{-- Almacén --}}
          <div>
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
            @error('almacen_id')
              <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
          </div>

          {{-- Proveedor --}}
          <div>
            <label class="text-xs font-semibold text-gray-600">Proveedor (opcional)</label>
            <select
              name="proveedor_id"
              class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
            >
              <option value="">Sin proveedor</option>
              @foreach($proveedores as $p)
                <option value="{{ $p->id }}" @selected(old('proveedor_id') == $p->id)>{{ $p->nombre }}</option>
              @endforeach
            </select>
            @error('proveedor_id')
              <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
          </div>

          {{-- Tipo --}}
          <div>
            <label class="text-xs font-semibold text-gray-600">Tipo</label>
            @php $tipo = old('tipo', 'compra'); @endphp
            <select
              name="tipo"
              class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
            >
              <option value="compra" @selected($tipo==='compra')>Compra</option>
              <option value="ajuste" @selected($tipo==='ajuste')>Ajuste</option>
              <option value="devolucion" @selected($tipo==='devolucion')>Devolución</option>
              <option value="traspaso_entrada" @selected($tipo==='traspaso_entrada')>Traspaso (entrada)</option>
            </select>
            @error('tipo')
              <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
          </div>

          {{-- Observaciones --}}
          <div class="lg:col-span-4">
            <label class="text-xs font-semibold text-gray-600">Observaciones (opcional)</label>
            <textarea
              name="observaciones"
              rows="2"
              placeholder="Notas, folio, referencia, etc."
              class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
            >{{ old('observaciones') }}</textarea>
            @error('observaciones')
              <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
          </div>
        </div>

        {{-- Detalles --}}
        <div class="rounded-2xl border border-gray-200 overflow-hidden">
          <div class="p-4 bg-gray-50 border-b flex items-center justify-between">
            <div>
              <div class="text-sm font-semibold text-gray-900">Detalles</div>
              <div class="text-xs text-gray-500">Agrega insumos, cantidades y costo unitario.</div>
            </div>

            <x-btn variant="soft" type="button" id="btnAddRow">
              <x-icon name="plus" class="h-4 w-4" />
              Agregar línea
            </x-btn>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full text-sm" id="tablaDetalles">
              <thead class="bg-white text-gray-600">
                <tr class="border-b">
                  <th class="text-left font-medium px-4 py-3">Insumo</th>
                  <th class="text-left font-medium px-4 py-3 w-40">Cantidad</th>
                  <th class="text-left font-medium px-4 py-3 w-44">Costo unitario</th>
                  <th class="text-left font-medium px-4 py-3 w-44">Subtotal</th>
                  <th class="text-right font-medium px-4 py-3 w-24"></th>
                </tr>
              </thead>
              <tbody id="tbodyDetalles" class="divide-y"></tbody>
            </table>
          </div>

          <div class="p-4 bg-white border-t flex items-center justify-end">
            <div class="text-right">
              <div class="text-xs text-gray-500">Total</div>
              <div class="text-2xl font-semibold text-gray-900" id="totalTxt">$ 0.00</div>
            </div>
          </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-2 justify-end">
          <x-btn variant="secondary" href="{{ route('entradas.index') }}">
            Cancelar
          </x-btn>

          <x-btn type="submit">
            <x-icon name="save" class="h-4 w-4" />
            Guardar entrada
          </x-btn>
        </div>
      </form>
    </x-card>
  </div>

  <script>
    const insumos = @json($insumosJs);
    const tbody = document.getElementById('tbodyDetalles');
    const totalTxt = document.getElementById('totalTxt');

    function money(n) { return (Number(n || 0)).toFixed(2); }

    function recalc() {
      let total = 0;
      tbody.querySelectorAll('tr').forEach(tr => {
        const qty = Number(tr.querySelector('[data-qty]').value || 0);
        const cost = Number(tr.querySelector('[data-cost]').value || 0);
        const subtotal = qty * cost;
        tr.querySelector('[data-subtotal]').textContent = '$ ' + money(subtotal);
        total += subtotal;
      });
      totalTxt.textContent = '$ ' + money(total);
    }

    function makeSelect(idx) {
      const sel = document.createElement('select');
      sel.name = `detalles[${idx}][insumo_id]`;
      sel.className = 'w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black';

      const opt0 = document.createElement('option');
      opt0.value = '';
      opt0.textContent = 'Selecciona…';
      sel.appendChild(opt0);

      insumos.forEach(i => {
        const opt = document.createElement('option');
        opt.value = i.id;
        opt.textContent = i.sku ? `${i.sku} — ${i.nombre}` : i.nombre;
        sel.appendChild(opt);
      });

      return sel;
    }

    function reindex() {
      const rows = Array.from(tbody.querySelectorAll('tr'));
      rows.forEach((tr, idx) => {
        tr.querySelector('select').name = `detalles[${idx}][insumo_id]`;
        tr.querySelector('[data-qty]').name = `detalles[${idx}][cantidad]`;
        tr.querySelector('[data-cost]').name = `detalles[${idx}][costo_unitario]`;
      });
    }

    function addRow() {
      const idx = tbody.querySelectorAll('tr').length;

      const tr = document.createElement('tr');
      tr.className = 'hover:bg-gray-50';

      const tdInsumo = document.createElement('td');
      tdInsumo.className = 'px-4 py-3 min-w-[320px]';
      const sel = makeSelect(idx);
      tdInsumo.appendChild(sel);

      const tdQty = document.createElement('td');
      tdQty.className = 'px-4 py-3';
      tdQty.innerHTML = `<input data-qty type="number" step="0.001" min="0"
        class="w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black" value="1">`;

      const tdCost = document.createElement('td');
      tdCost.className = 'px-4 py-3';
      tdCost.innerHTML = `<input data-cost type="number" step="0.01" min="0"
        class="w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black" value="0">`;

      const tdSub = document.createElement('td');
      tdSub.className = 'px-4 py-3 whitespace-nowrap font-semibold';
      tdSub.innerHTML = `<span data-subtotal>$ 0.00</span>`;

      const tdDel = document.createElement('td');
      tdDel.className = 'px-4 py-3 text-right';
      tdDel.innerHTML = `
        <button type="button" class="inline-flex items-center justify-center p-2 rounded-xl hover:bg-red-50 text-red-700">
          <span class="sr-only">Quitar</span>
          ✕
        </button>`;
      tdDel.querySelector('button').addEventListener('click', () => {
        tr.remove();
        reindex();
        recalc();
      });

      tr.appendChild(tdInsumo);
      tr.appendChild(tdQty);
      tr.appendChild(tdCost);
      tr.appendChild(tdSub);
      tr.appendChild(tdDel);

      tr.querySelector('[data-qty]').addEventListener('input', recalc);
      tr.querySelector('[data-cost]').addEventListener('input', recalc);
      sel.addEventListener('change', recalc);

      tbody.appendChild(tr);
      reindex();
      recalc();
    }

    document.getElementById('btnAddRow').addEventListener('click', addRow);

    addRow();
  </script>
@endsection
