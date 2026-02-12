<?php

namespace App\Http\Controllers;

use App\Models\Unidad;
use Illuminate\Http\Request;

class UnidadController extends Controller
{
    public function index()
    {
        $items = Unidad::query()
            ->orderBy('nombre')
            ->paginate(10);

        return view('unidades.index', compact('items'));
    }

    public function create()
    {
        return view('unidades.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:120', 'unique:unidades,nombre'],
            'clave'  => ['required', 'string', 'max:20',  'unique:unidades,clave'],
            'activa' => ['nullable', 'boolean'],
        ]);

        $data['clave'] = strtoupper(trim($data['clave']));
        $data['activa'] = $request->boolean('activa');

        Unidad::create($data);

        return redirect()
            ->route('unidades.index')
            ->with('ok', 'Unidad creada.');
    }

    public function show(Unidad $unidad)
    {
        $item = $unidad;

        return view('unidades.show', compact('item'));
    }

    public function edit(Unidad $unidad)
    {
        $item = $unidad;

        return view('unidades.edit', compact('item'));
    }

    public function update(Request $request, Unidad $unidad)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:120', 'unique:unidades,nombre,' . $unidad->id],
            'clave'  => ['required', 'string', 'max:20',  'unique:unidades,clave,' . $unidad->id],
            'activa' => ['nullable', 'boolean'],
        ]);

        $data['clave'] = strtoupper(trim($data['clave']));
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
