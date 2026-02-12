@extends('layouts.app')

@section('title', 'Nueva entrada')

@section('page_title', 'Nueva entrada')
@section('page_subtitle', 'Captura insumos, cantidades y costo. Al guardar, se suman existencias.')

@section('page_actions')
    <a href="{{ route('entradas.index') }}"
       class="inline-flex items-center px-4 py-2 bg-white border rounded-lg text-sm hover:bg-gray-50">
        ← Volver
    </a>
@endsection

@section('content')
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">
            <div class="font-semibold mb-2">Corrige lo siguiente:</div>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('entradas.store') }}" class="bg-white border rounded-lg">
        @csrf

        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha</label>
                    <input type="date" name="fecha" value="{{ old('fecha', now()->format('Y-m-d')) }}"
                           class="mt-1 block w-full rounded-md border-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Almacén</label>
                    <select name="almacen_id" class="mt-1 block w-full rounded-md border-gray-300">
                        <option value="">-- Selecciona --</option>
                        @foreach($almacenes as $a)
                            <option value="{{ $a->id }}" @selected(old('almacen_id') == $a->id)>{{ $a->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Proveedor (opcional)</label>
                    <select name="proveedor_id" class="mt-1 block w-full rounded-md border-gray-300">
                        <option value="">-- Sin proveedor --</option>
                        @foreach($proveedores as $p)
                            <option value="{{ $p->id }}" @selected(old('proveedor_id') == $p->id)>{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo</label>
                    @php $tipo = old('tipo', 'compra'); @endphp
                    <select name="tipo" class="mt-1 block w-full rounded-md border-gray-300">
                        <option value="compra" @selected($tipo==='compra')>compra</option>
                        <option value="ajuste" @selected($tipo==='ajuste')>ajuste</option>
                        <option value="devolucion" @selected($tipo==='devolucion')>devolución</option>
                        <option value="traspaso_entrada" @selected($tipo==='traspaso_entrada')>traspaso_entrada</option>
                    </select>
                </div>

                <div class="md:col-span-4">
                    <label class="block text-sm font-medium text-gray-700">Observaciones</label>
                    <textarea name="observaciones" rows="2" class="mt-1 block w-full rounded-md border-gray-300">{{ old('observaciones') }}</textarea>
                </div>
            </div>

            <div class="border-t pt-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Detalles</h3>
                    <button type="button" id="btnAddRow"
                            class="inline-flex items-center px-3 py-2 bg-gray-100 rounded-lg text-sm hover:bg-gray-200">
                        + Agregar línea
                    </button>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm" id="tablaDetalles">
                        <thead class="bg-gray-50">
                            <tr class="text-left border-b">
                                <th class="py-2 px-3">Insumo</th>
                                <th class="py-2 px-3">Cantidad</th>
                                <th class="py-2 px-3">Costo unitario</th>
                                <th class="py-2 px-3">Subtotal</th>
                                <th class="py-2 px-3"></th>
                            </tr>
                        </thead>
                        <tbody id="tbodyDetalles"></tbody>
                    </table>
                </div>

                <div class="mt-4 text-right">
                    <div class="text-sm text-gray-600">Total</div>
                    <div class="text-2xl font-semibold" id="totalTxt">$ 0.00</div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('entradas.index') }}"
                   class="px-4 py-2 bg-white border rounded-lg text-sm hover:bg-gray-50">
                    Cancelar
                </a>

                <button type="submit"
                        class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm hover:opacity-90">
                    Guardar entrada
                </button>
            </div>
        </div>
    </form>

    <script>
        const insumos = @json($insumos);
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
            sel.className = 'mt-1 block w-full rounded-md border-gray-300';

            const opt0 = document.createElement('option');
            opt0.value = '';
            opt0.textContent = '-- Selecciona --';
            sel.appendChild(opt0);

            insumos.forEach(i => {
                const opt = document.createElement('option');
                opt.value = i.id;
                opt.textContent = i.nombre;
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
            tr.className = 'border-b';

            const tdInsumo = document.createElement('td');
            tdInsumo.className = 'py-2 px-3 min-w-[260px]';
            const sel = makeSelect(idx);
            tdInsumo.appendChild(sel);

            const tdQty = document.createElement('td');
            tdQty.className = 'py-2 px-3';
            tdQty.innerHTML = `<input data-qty type="number" step="0.001" min="0" class="mt-1 block w-full rounded-md border-gray-300" value="1">`;

            const tdCost = document.createElement('td');
            tdCost.className = 'py-2 px-3';
            tdCost.innerHTML = `<input data-cost type="number" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300" value="0">`;

            const tdSub = document.createElement('td');
            tdSub.className = 'py-2 px-3 whitespace-nowrap';
            tdSub.innerHTML = `<span data-subtotal>$ 0.00</span>`;

            const tdDel = document.createElement('td');
            tdDel.className = 'py-2 px-3 text-right';
            tdDel.innerHTML = `<button type="button" class="text-red-600 hover:underline">Quitar</button>`;
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

        // Arranca con 1 línea
        addRow();
    </script>
@endsection
