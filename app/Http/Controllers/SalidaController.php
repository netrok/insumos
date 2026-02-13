<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Existencia;
use App\Models\Insumo;
use App\Models\Salida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalidaController extends Controller
{
    public function index(Request $request)
    {
        $query = Salida::query()
            ->with(['almacen'])
            ->orderByDesc('fecha')
            ->orderByDesc('id');

        if ($request->filled('almacen_id')) {
            $query->where('almacen_id', $request->integer('almacen_id'));
        }

        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->date('desde'));
        }

        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->date('hasta'));
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', (string) $request->input('tipo')); // ✅ string real
        }

        $salidas = $query->paginate(15)->withQueryString();
        $almacenes = Almacen::orderBy('nombre')->get(['id', 'nombre']);

        return view('salidas.index', compact('salidas', 'almacenes'));
    }

    public function create()
    {
        $almacenes = Almacen::orderBy('nombre')->get(['id', 'nombre']);
        $insumos = Insumo::orderBy('nombre')->get(['id', 'sku', 'nombre', 'costo_promedio']);

        $tipos = ['consumo', 'merma', 'ajuste', 'traspaso']; // traspaso fase 2

        return view('salidas.create', compact('almacenes', 'insumos', 'tipos'));
    }

    public function store(Request $request)
    {
        $data = $this->validateSalida($request);

        try {
            $salida = DB::transaction(function () use ($data) {

                // Consecutivo blindado
                $last = Salida::query()
                    ->select('consecutivo')
                    ->whereNotNull('consecutivo')
                    ->orderByDesc('consecutivo')
                    ->lockForUpdate()
                    ->first();

                $next = ((int) ($last?->consecutivo ?? 0)) + 1;
                $folio = 'SAL-' . str_pad((string) $next, 8, '0', STR_PAD_LEFT);

                $salida = Salida::create([
                    'consecutivo'   => $next,
                    'folio'         => $folio,
                    'fecha'         => $data['fecha'],
                    'almacen_id'    => $data['almacen_id'],
                    'tipo'          => $data['tipo'],
                    'observaciones' => $data['observaciones'] ?? null,
                    'created_by'    => auth()->id(),
                    'total'         => 0,
                ]);

                $total = $this->persistDetallesAndStock(
                    salida: $salida,
                    almacenId: (int) $salida->almacen_id,
                    detalles: $data['detalles'],
                    mode: 'decrement'
                );

                $salida->update(['total' => $total]);

                return $salida;
            });
        } catch (\RuntimeException $e) {
            return back()
                ->withInput()
                ->withErrors(['detalles' => $e->getMessage()]);
        }

        return redirect()
            ->route('salidas.show', $salida)
            ->with('success', "Salida registrada: {$salida->folio}");
    }

    public function show(Salida $salida)
    {
        $salida->load(['almacen', 'detalles.insumo', 'createdBy']);
        return view('salidas.show', compact('salida'));
    }

    public function edit(Salida $salida)
    {
        $salida->load(['detalles']);

        $almacenes = Almacen::orderBy('nombre')->get(['id', 'nombre']);
        $insumos = Insumo::orderBy('nombre')->get(['id', 'sku', 'nombre', 'costo_promedio']);
        $tipos = ['consumo', 'merma', 'ajuste', 'traspaso'];

        $detalles = $salida->detalles->map(fn ($d) => [
            'insumo_id' => (int) $d->insumo_id,
            'cantidad' => (string) $d->cantidad,
            'costo_unitario' => (string) ($d->costo_unitario ?? 0),
        ])->values();

        return view('salidas.edit', compact('salida', 'almacenes', 'insumos', 'tipos', 'detalles'));
    }

    public function update(Request $request, Salida $salida)
    {
        $data = $this->validateSalida($request);

        try {
            $salida = DB::transaction(function () use ($salida, $data) {
                $salida->load(['detalles']);

                // Revertir stock del almacén anterior (sumar de regreso)
                $this->applyStockDelta(
                    almacenId: (int) $salida->almacen_id,
                    detalles: $salida->detalles->map(fn ($d) => [
                        'insumo_id' => (int) $d->insumo_id,
                        'cantidad' => (string) $d->cantidad,
                    ])->all(),
                    mode: 'increment'
                );

                // Actualizar encabezado (NO tocamos consecutivo/folio)
                $salida->update([
                    'fecha' => $data['fecha'],
                    'almacen_id' => $data['almacen_id'],
                    'tipo' => $data['tipo'],
                    'observaciones' => $data['observaciones'] ?? null,
                    'total' => 0,
                ]);

                // Reemplazar detalles
                $salida->detalles()->delete();

                // Aplicar nuevo descuento de stock
                $total = $this->persistDetallesAndStock(
                    salida: $salida,
                    almacenId: (int) $salida->almacen_id,
                    detalles: $data['detalles'],
                    mode: 'decrement'
                );

                $salida->update(['total' => $total]);

                return $salida;
            });
        } catch (\RuntimeException $e) {
            return back()
                ->withInput()
                ->withErrors(['detalles' => $e->getMessage()]);
        }

        return redirect()
            ->route('salidas.show', $salida)
            ->with('success', "Salida actualizada: {$salida->folio}");
    }

    public function destroy(Salida $salida)
    {
        DB::transaction(function () use ($salida) {
            $salida->load(['detalles']);

            // Al eliminar, regresamos stock
            $this->applyStockDelta(
                almacenId: (int) $salida->almacen_id,
                detalles: $salida->detalles->map(fn ($d) => [
                    'insumo_id' => (int) $d->insumo_id,
                    'cantidad' => (string) $d->cantidad,
                ])->all(),
                mode: 'increment'
            );

            $salida->detalles()->delete();
            $salida->delete();
        });

        return redirect()
            ->route('salidas.index')
            ->with('success', 'Salida eliminada.');
    }

    private function validateSalida(Request $request): array
    {
        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'almacen_id' => ['required', 'exists:almacenes,id'],
            'tipo' => ['required', 'string', 'max:30'],
            'observaciones' => ['nullable', 'string'],

            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.insumo_id' => ['required', 'exists:insumos,id'],
            'detalles.*.cantidad' => ['required', 'numeric', 'gt:0'],
            'detalles.*.costo_unitario' => ['nullable', 'numeric', 'min:0'],
        ]);

        // Consolidar por insumo_id (evita duplicados en misma salida)
        $detalles = collect($data['detalles'])
            ->map(fn ($d) => [
                'insumo_id' => (int) $d['insumo_id'],
                'cantidad' => (string) $d['cantidad'],
                'costo_unitario' => (string) ($d['costo_unitario'] ?? 0),
            ])
            ->groupBy('insumo_id')
            ->map(function ($rows) {
                $cantidad = (float) $rows->sum(fn ($r) => (float) $r['cantidad']);
                $subtotal = (float) $rows->sum(fn ($r) => (float) $r['cantidad'] * (float) $r['costo_unitario']);
                $costoUnit = $cantidad > 0 ? ($subtotal / $cantidad) : 0;

                return [
                    'insumo_id' => (int) $rows->first()['insumo_id'],
                    'cantidad' => number_format($cantidad, 3, '.', ''),
                    'costo_unitario' => number_format($costoUnit, 2, '.', ''),
                ];
            })
            ->values()
            ->all();

        $data['detalles'] = $detalles;

        return $data;
    }

    private function persistDetallesAndStock(Salida $salida, int $almacenId, array $detalles, string $mode): float
    {
        $total = 0.0;

        // ✅ 1 query para costos promedio (evita N+1)
        $ids = collect($detalles)->pluck('insumo_id')->map(fn($v) => (int) $v)->unique()->values();
        $costos = Insumo::query()
            ->whereIn('id', $ids)
            ->pluck('costo_promedio', 'id'); // [id => costo_promedio]

        foreach ($detalles as $d) {
            $insumoId = (int) $d['insumo_id'];
            $cantidad = (float) $d['cantidad'];

            $costoUnit = (float) ($d['costo_unitario'] ?? 0);
            if ($costoUnit <= 0) {
                $costoUnit = (float) ($costos[$insumoId] ?? 0);
            }

            $subtotal = round($cantidad * $costoUnit, 2);

            $salida->detalles()->create([
                'insumo_id' => $insumoId,
                'cantidad' => number_format($cantidad, 3, '.', ''),
                'costo_unitario' => number_format($costoUnit, 2, '.', ''),
                'subtotal' => $subtotal,
            ]);

            $total += $subtotal;
        }

        $this->applyStockDelta($almacenId, $detalles, $mode);

        return (float) $total;
    }

    /**
     * Stock seguro con lockForUpdate + validación de existencia.
     * Usa columna `stock` en existencias.
     */
    private function applyStockDelta(int $almacenId, array $detalles, string $mode): void
    {
        foreach ($detalles as $d) {
            $insumoId = (int) $d['insumo_id'];
            $delta = (float) $d['cantidad'];

            $existencia = Existencia::query()
                ->where('almacen_id', $almacenId)
                ->where('insumo_id', $insumoId)
                ->lockForUpdate()
                ->first();

            if (!$existencia) {
                $existencia = Existencia::query()->create([
                    'almacen_id' => $almacenId,
                    'insumo_id'  => $insumoId,
                    'stock'      => 0,
                ]);
            }

            if ($mode === 'decrement') {
                $actual = (float) $existencia->stock;

                if ($actual < $delta) {
                    $insumo = Insumo::query()->select(['id', 'sku', 'nombre'])->find($insumoId);
                    $label = $insumo ? "{$insumo->sku} - {$insumo->nombre}" : "Insumo #{$insumoId}";
                    throw new \RuntimeException(
                        "Stock insuficiente en almacén {$almacenId} para {$label}. Disponible: {$actual}, requerido: {$delta}."
                    );
                }

                $existencia->decrement('stock', $delta);
            } else {
                $existencia->increment('stock', $delta);
            }
        }
    }
}
