<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use Illuminate\Http\Request;

class AlmacenController extends Controller
{
    public function index()
    {
        $items = Almacen::query()
            ->orderBy('nombre')
            ->paginate(10);

        return view('almacenes.index', compact('items'));
    }

    public function create()
    {
        return view('almacenes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:120', 'unique:almacenes,nombre'],
            'codigo' => ['required', 'string', 'max:30',  'unique:almacenes,codigo'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $data['codigo'] = strtoupper(trim($data['codigo']));
        $data['activo'] = $request->boolean('activo');

        Almacen::create($data);

        return redirect()
            ->route('almacenes.index')
            ->with('ok', 'Almacén creado.');
    }

    public function show(Almacen $almacen)
    {
        $item = $almacen;

        return view('almacenes.show', compact('item'));
    }

    public function edit(Almacen $almacen)
    {
        $item = $almacen;

        return view('almacenes.edit', compact('item'));
    }

    public function update(Request $request, Almacen $almacen)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:120', 'unique:almacenes,nombre,' . $almacen->id],
            'codigo' => ['required', 'string', 'max:30',  'unique:almacenes,codigo,' . $almacen->id],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $data['codigo'] = strtoupper(trim($data['codigo']));
        $data['activo'] = $request->boolean('activo');

        $almacen->update($data);

        return redirect()
            ->route('almacenes.index')
            ->with('ok', 'Almacén actualizado.');
    }

    public function destroy(Almacen $almacen)
    {
        $almacen->delete();

        return redirect()
            ->route('almacenes.index')
            ->with('ok', 'Almacén eliminado.');
    }
}
