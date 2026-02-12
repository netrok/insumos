<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Entrada;
use App\Models\Existencia;
use App\Models\Insumo;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

        // Para filtros en la vista (combos)
        $almacenes = Almacen::orderBy('nombre')->get(['id', 'nombre']);
        $proveedores = Proveedor::orderBy('nombre')->get(['id', 'nombre']);

        return view('entradas.index', compact('entradas', 'almacenes', 'proveedores'));
    }

    public function create()
    {
        $almacenes = Almacen::orderBy('nombre')->get(['id', 'nombre']);
        $proveedores = Proveedor::orderBy('nombre')->get(['id', 'nombre']);
        $insumos = Insumo::orderBy('nombre')->get(['id', 'nombre']);

        return view('entradas.create', compact('almacenes', 'proveedores', 'insumos'));
    }

    public function store(Request $request)
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

        $entrada = DB::transaction(function () use ($data) {
            $folio = 'ENT-' . now()->format('Ymd-His') . '-' . Str::upper(Str::random(4));

            $entrada = Entrada::create([
                'folio' => $folio,
                'fecha' => $data['fecha'],
                'almacen_id' => $data['almacen_id'],
                'proveedor_id' => $data['proveedor_id'] ?? null,
                'tipo' => $data['tipo'],
                'observaciones' => $data['observaciones'] ?? null,
                'created_by' => auth()->id(),
                'total' => 0,
            ]);

            $total = 0;

            foreach ($data['detalles'] as $d) {
                $cantidad = (float) $d['cantidad'];
                $costo = (float) ($d['costo_unitario'] ?? 0);
                $subtotal = round($cantidad * $costo, 2);

                $entrada->detalles()->create([
                    'insumo_id' => $d['insumo_id'],
                    'cantidad' => $cantidad,
                    'costo_unitario' => $costo,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;

                $existencia = Existencia::query()
                    ->where('almacen_id', $entrada->almacen_id)
                    ->where('insumo_id', $d['insumo_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$existencia) {
                    $existencia = Existencia::create([
                        'almacen_id' => $entrada->almacen_id,
                        'insumo_id' => $d['insumo_id'],
                        'stock' => 0,
                    ]);
                }

                $existencia->increment('stock', $cantidad);
            }

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
        abort(403);
    }

    public function update(Request $request, Entrada $entrada)
    {
        abort(403);
    }

    public function destroy(Entrada $entrada)
    {
        abort(403);
    }
}
