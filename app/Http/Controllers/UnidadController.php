<?php

namespace App\Http\Controllers;

use App\Models\Unidad;
use Illuminate\Http\Request;

class UnidadController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $activa = $request->get('activa'); // '1' | '0' | null

        $items = Unidad::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('nombre', 'ILIKE', "%{$q}%")
                      ->orWhere('clave', 'ILIKE', "%{$q}%");
                });
            })
            ->when($activa !== null && $activa !== '', fn ($query) => $query->where('activa', $activa === '1'))
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('unidades.index', compact('items', 'q', 'activa'));
    }

    public function create()
    {
        return view('unidades.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:120', 'unique:unidades,nombre'],
            'clave'  => ['required', 'string', 'max:20', 'unique:unidades,clave'],
            'activa' => ['nullable', 'boolean'],
        ]);

        $data['nombre'] = strtoupper(trim($data['nombre']));
        $data['clave']  = strtoupper(trim($data['clave']));
        $data['activa'] = $request->boolean('activa');

        Unidad::create($data);

        return redirect()
            ->route('unidades.index')
            ->with('ok', 'Unidad creada.');
    }

    public function show(Unidad $unidad)
    {
        return view('unidades.show', compact('unidad'));
    }

    public function edit(Unidad $unidad)
    {
        return view('unidades.edit', compact('unidad'));
    }

    public function update(Request $request, Unidad $unidad)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:120', 'unique:unidades,nombre,' . $unidad->id],
            'clave'  => ['required', 'string', 'max:20', 'unique:unidades,clave,' . $unidad->id],
            'activa' => ['nullable', 'boolean'],
        ]);

        $data['nombre'] = strtoupper(trim($data['nombre']));
        $data['clave']  = strtoupper(trim($data['clave']));
        $data['activa'] = $request->boolean('activa');

        $unidad->update($data);

        return redirect()
            ->route('unidades.index')
            ->with('ok', 'Unidad actualizada.');
    }

    public function destroy(Unidad $unidad)
    {
        $unidad->delete();

        return redirect()
            ->route('unidades.index')
            ->with('ok', 'Unidad eliminada.');
    }
}
