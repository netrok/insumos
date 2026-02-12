<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use Illuminate\Http\Request;

class AlmacenController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');

        $items = Almacen::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('nombre', 'ILIKE', "%{$q}%")
                      ->orWhere('codigo', 'ILIKE', "%{$q}%")
                      ->orWhere('ubicacion', 'ILIKE', "%{$q}%");
                });
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('almacenes.index', compact('items', 'q'));
    }

    public function create()
    {
        return view('almacenes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'    => ['required', 'string', 'max:120', 'unique:almacenes,nombre'],
            'codigo'    => ['required', 'string', 'max:30',  'unique:almacenes,codigo'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'activo'    => ['nullable', 'boolean'],
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
        return view('almacenes.show', compact('almacen'));
    }

    public function edit(Almacen $almacen)
    {
        return view('almacenes.edit', compact('almacen'));
    }

    public function update(Request $request, Almacen $almacen)
    {
        $data = $request->validate([
            'nombre'    => ['required', 'string', 'max:120', 'unique:almacenes,nombre,' . $almacen->id],
            'codigo'    => ['required', 'string', 'max:30',  'unique:almacenes,codigo,' . $almacen->id],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'activo'    => ['nullable', 'boolean'],
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
