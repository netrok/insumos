<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Entrada;
use App\Models\Existencia;
use App\Models\Insumo;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntradaController extends Controller
{
    public function index(Request $request)
    {
        $query = Entrada::query()
            ->with(['almacen', 'proveedor'])
            ->orderByDesc('fecha')
            ->orderByDesc('id');

        if ($request->filled('almacen_id')) {
            $query->where('almacen_id', $request->integer('almacen_id'));
        }

        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->integer('proveedor_id'));
        }

        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->date('desde'));
        }

        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->date('hasta'));
        }

        $entradas = $query->paginate(15)->withQueryString();

        $almacenes = Almacen::orderBy('nombre')->get(['id', 'nombre']);
        $proveedores = Proveedor::orderBy('nombre')->get(['id', 'nombre']);

        return view('entradas.index', compact('entradas', 'almacenes', 'proveedores'));
    }

    public function create()
    {
        $almacenes = Almacen::orderBy('nombre')->get(['id', 'nombre']);
        $proveedores = Proveedor::orderBy('nombre')->get(['id', 'nombre']);
        $insumos = Insumo::orderBy('nombre')->get(['id', 'sku', 'nombre']);

        return view('entradas.create', compact('almacenes', 'proveedores', 'insumos'));
    }

    public function store(Request $request)
    {
        $data = $this->validateEntrada($request);

        $entrada = DB::transaction(function () use ($data) {

            // ✅ Consecutivo blindado (evita duplicados en concurrencia)
            $last = Entrada::query()
                ->select('consecutivo')
                ->whereNotNull('consecutivo')
                ->orderByDesc('consecutivo')
                ->lockForUpdate()
                ->first();

            $next = ((int) ($last?->consecutivo ?? 0)) + 1;

            // Folio corto: ENT-00000001
            $folio = 'ENT-' . str_pad((string) $next, 8, '0', STR_PAD_LEFT);

            $entrada = Entrada::create([
                'consecutivo' => $next,
                'folio' => $folio,
                'fecha' => $data['fecha'],
                'almacen_id' => $data['almacen_id'],
                'proveedor_id' => $data['proveedor_id'] ?? null,
                'tipo' => $data['tipo'],
                'observaciones' => $data['observaciones'] ?? null,
                'created_by' => auth()->id(),
                'total' => 0,
            ]);

            $total = $this->persistDetallesAndStock(
                entrada: $entrada,
                almacenId: (int) $entrada->almacen_id,
                detalles: $data['detalles'],
                mode: 'increment'
            );

            $entrada->update(['total' => $total]);

            return $entrada;
        });

        return redirect()
            ->route('entradas.show', $entrada)
            ->with('success', "Entrada registrada: {$entrada->folio}");
    }

    public function show(Entrada $entrada)
    {
        $entrada->load(['almacen', 'proveedor', 'detalles.insumo', 'createdBy']);
        return view('entradas.show', compact('entrada'));
    }

    public function edit(Entrada $entrada)
    {
        $entrada->load(['detalles']);

        $almacenes = Almacen::orderBy('nombre')->get(['id', 'nombre']);
        $proveedores = Proveedor::orderBy('nombre')->get(['id', 'nombre']);
        $insumos = Insumo::orderBy('nombre')->get(['id', 'sku', 'nombre']);

        $detalles = $entrada->detalles->map(fn ($d) => [
            'insumo_id' => (int) $d->insumo_id,
            'cantidad' => (string) $d->cantidad,
            'costo_unitario' => (string) ($d->costo_unitario ?? 0),
        ])->values();

        return view('entradas.edit', compact('entrada', 'almacenes', 'proveedores', 'insumos', 'detalles'));
    }

    public function update(Request $request, Entrada $entrada)
    {
        $data = $this->validateEntrada($request);

        $entrada = DB::transaction(function () use ($entrada, $data) {
            $entrada->load(['detalles']);

            // Revertir existencias del almacén anterior
            $this->applyStockDelta(
                almacenId: (int) $entrada->almacen_id,
                detalles: $entrada->detalles->map(fn ($d) => [
                    'insumo_id' => (int) $d->insumo_id,
                    'cantidad' => (string) $d->cantidad,
                ])->all(),
                mode: 'decrement'
            );

            // Actualizar encabezado (NO tocamos consecutivo/folio)
            $entrada->update([
                'fecha' => $data['fecha'],
                'almacen_id' => $data['almacen_id'],
                'proveedor_id' => $data['proveedor_id'] ?? null,
                'tipo' => $data['tipo'],
                'observaciones' => $data['observaciones'] ?? null,
                'total' => 0,
            ]);

            // Reemplazar detalles
            $entrada->detalles()->delete();

            // Aplicar existencias nuevas
            $total = $this->persistDetallesAndStock(
                entrada: $entrada,
                almacenId: (int) $entrada->almacen_id,
                detalles: $data['detalles'],
                mode: 'increment'
            );

            $entrada->update(['total' => $total]);

            return $entrada;
        });

        return redirect()
            ->route('entradas.show', $entrada)
            ->with('success', "Entrada actualizada: {$entrada->folio}");
    }

    public function destroy(Entrada $entrada)
    {
        DB::transaction(function () use ($entrada) {
            $entrada->load(['detalles']);

            $this->applyStockDelta(
                almacenId: (int) $entrada->almacen_id,
                detalles: $entrada->detalles->map(fn ($d) => [
                    'insumo_id' => (int) $d->insumo_id,
                    'cantidad' => (string) $d->cantidad,
                ])->all(),
                mode: 'decrement'
            );

            $entrada->detalles()->delete();
            $entrada->delete();
        });

        return redirect()
            ->route('entradas.index')
            ->with('success', 'Entrada eliminada.');
    }

    private function validateEntrada(Request $request): array
    {
        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'almacen_id' => ['required', 'exists:almacenes,id'],
            'proveedor_id' => ['nullable', 'exists:proveedores,id'],
            'tipo' => ['required', 'string', 'max:30'],
            'observaciones' => ['nullable', 'string'],

            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.insumo_id' => ['required', 'exists:insumos,id'],
            'detalles.*.cantidad' => ['required', 'numeric', 'gt:0'],
            'detalles.*.costo_unitario' => ['nullable', 'numeric', 'min:0'],
        ]);

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

    private function persistDetallesAndStock(Entrada $entrada, int $almacenId, array $detalles, string $mode): float
    {
        $total = 0.0;

        foreach ($detalles as $d) {
            $cantidad = (float) $d['cantidad'];
            $costo = (float) ($d['costo_unitario'] ?? 0);
            $subtotal = round($cantidad * $costo, 2);

            $entrada->detalles()->create([
                'insumo_id' => (int) $d['insumo_id'],
                'cantidad' => $d['cantidad'],
                'costo_unitario' => $d['costo_unitario'],
                'subtotal' => $subtotal,
            ]);

            $total += $subtotal;
        }

        $this->applyStockDelta($almacenId, $detalles, $mode);

        return (float) $total;
    }

    /**
     * PRO: Manejo de existencias con columna `stock` (no `cantidad`)
     * Recomendado si existe unique(['almacen_id','insumo_id']) en la tabla existencias.
     */
    private function applyStockDelta(int $almacenId, array $detalles, string $mode): void
    {
        foreach ($detalles as $d) {
            $insumoId = (int) $d['insumo_id'];
            $delta = (float) $d['cantidad'];

            // Bloqueo para consistencia en concurrencia
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
                $nuevo = (float) $existencia->stock - $delta;
                $existencia->update(['stock' => max(0, $nuevo)]);
            } else {
                $existencia->increment('stock', $delta);
            }
        }
    }
}
