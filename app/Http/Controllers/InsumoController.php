<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Insumo;
use App\Models\Unidad;
use Illuminate\Http\Request;

class InsumoController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $categoriaId = $request->get('categoria_id');
        $activo = $request->get('activo'); // '1' | '0' | null

        $items = Insumo::query()
            ->with(['categoria', 'unidad'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('sku', 'ilike', "%{$q}%")
                       ->orWhere('nombre', 'ilike', "%{$q}%");
                });
            })
            ->when($categoriaId, fn ($query) => $query->where('categoria_id', $categoriaId))
            ->when($activo !== null && $activo !== '', fn ($query) => $query->where('activo', $activo === '1'))
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        $categorias = Categoria::query()
            ->where('activa', true)
            ->orderBy('nombre')
            ->get();

        return view('insumos.index', compact('items', 'categorias', 'q', 'categoriaId', 'activo'));
    }

    public function create()
    {
        $categorias = Categoria::query()->where('activa', true)->orderBy('nombre')->get();
        $unidades   = Unidad::query()->where('activa', true)->orderBy('nombre')->get();

        return view('insumos.create', compact('categorias', 'unidades'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sku' => ['required', 'string', 'max:60', 'unique:insumos,sku'],
            'nombre' => ['required', 'string', 'max:180'],
            'descripcion' => ['nullable', 'string'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'unidad_id' => ['required', 'exists:unidades,id'],
            'costo_promedio' => ['nullable', 'numeric', 'min:0'],
            'stock_minimo' => ['nullable', 'integer', 'min:0'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $data['sku'] = strtoupper(trim($data['sku']));
        $data['costo_promedio'] = (float) ($data['costo_promedio'] ?? 0);
        $data['stock_minimo'] = (int) ($data['stock_minimo'] ?? 0);
        $data['activo'] = $request->boolean('activo');

        // Existencias se inicializan en InsumoObserver::created()
        Insumo::create($data);

        return redirect()
            ->route('insumos.index')
            ->with('ok', 'Insumo creado (existencias inicializadas).');
    }

    public function show(Insumo $insumo)
    {
        $item = $insumo->load([
            'categoria',
            'unidad',
            'existencias.almacen',
        ]);

        return view('insumos.show', compact('item'));
    }

    public function edit(Insumo $insumo)
    {
        $item = $insumo;

        $categorias = Categoria::query()->where('activa', true)->orderBy('nombre')->get();
        $unidades   = Unidad::query()->where('activa', true)->orderBy('nombre')->get();

        return view('insumos.edit', compact('item', 'categorias', 'unidades'));
    }

    public function update(Request $request, Insumo $insumo)
    {
        $data = $request->validate([
            'sku' => ['required', 'string', 'max:60', 'unique:insumos,sku,' . $insumo->id],
            'nombre' => ['required', 'string', 'max:180'],
            'descripcion' => ['nullable', 'string'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'unidad_id' => ['required', 'exists:unidades,id'],
            'costo_promedio' => ['nullable', 'numeric', 'min:0'],
            'stock_minimo' => ['nullable', 'integer', 'min:0'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $data['sku'] = strtoupper(trim($data['sku']));
        $data['costo_promedio'] = (float) ($data['costo_promedio'] ?? 0);
        $data['stock_minimo'] = (int) ($data['stock_minimo'] ?? 0);
        $data['activo'] = $request->boolean('activo');

        $insumo->update($data);

        return redirect()
            ->route('insumos.index')
            ->with('ok', 'Insumo actualizado.');
    }

    public function destroy(Insumo $insumo)
    {
        $insumo->delete();

        return redirect()
            ->route('insumos.index')
            ->with('ok', 'Insumo eliminado.');
    }
}
