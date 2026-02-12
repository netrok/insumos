<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $items = Categoria::query()
            ->orderBy('nombre')
            ->paginate(10);

        return view('categorias.index', compact('items'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:120', 'unique:categorias,nombre'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'activa' => ['nullable', 'boolean'],
        ]);

        $data['activa'] = $request->boolean('activa');

        Categoria::create($data);

        return redirect()
            ->route('categorias.index')
            ->with('ok', 'Categoría creada.');
    }

    public function show(Categoria $categoria)
    {
        $item = $categoria;

        return view('categorias.show', compact('item'));
    }

    public function edit(Categoria $categoria)
    {
        $item = $categoria;

        return view('categorias.edit', compact('item'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:120', 'unique:categorias,nombre,' . $categoria->id],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'activa' => ['nullable', 'boolean'],
        ]);

        $data['activa'] = $request->boolean('activa');

        $categoria->update($data);

        return redirect()
            ->route('categorias.index')
            ->with('ok', 'Categoría actualizada.');
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();

        return redirect()
            ->route('categorias.index')
            ->with('ok', 'Categoría eliminada.');
    }
}
