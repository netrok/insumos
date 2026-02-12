<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $q = Proveedor::query()->orderBy('nombre');

        if ($request->filled('q')) {
            $term = trim($request->string('q'));
            $q->where(function ($qq) use ($term) {
                $qq->where('nombre', 'ilike', "%{$term}%")
                   ->orWhere('rfc', 'ilike', "%{$term}%")
                   ->orWhere('telefono', 'ilike', "%{$term}%")
                   ->orWhere('email', 'ilike', "%{$term}%");
            });
        }

        if ($request->filled('activo')) {
            $q->where('activo', $request->boolean('activo'));
        }

        $proveedores = $q->paginate(15)->withQueryString();

        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required','string','max:150'],
            'rfc' => ['nullable','string','max:20'],
            'telefono' => ['nullable','string','max:50'],
            'email' => ['nullable','email','max:150'],
            'direccion' => ['nullable','string'],
            'activo' => ['nullable'],
        ]);

        $data['activo'] = $request->boolean('activo');

        Proveedor::create($data);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado.');
    }

    public function show(Proveedor $proveedor)
    {
        return view('proveedores.show', compact('proveedor'));
    }

    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $data = $request->validate([
            'nombre' => ['required','string','max:150'],
            'rfc' => ['nullable','string','max:20'],
            'telefono' => ['nullable','string','max:50'],
            'email' => ['nullable','email','max:150'],
            'direccion' => ['nullable','string'],
            'activo' => ['nullable'],
        ]);

        $data['activo'] = $request->boolean('activo');

        $proveedor->update($data);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado.');
    }

    public function destroy(Proveedor $proveedor)
    {
        abort(403);
    }
}
