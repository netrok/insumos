<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    public function edit()
    {
        $empresa = [
            'nombre'        => setting('empresa.nombre', config('app.name')),
            'rfc'           => setting('empresa.rfc', ''),
            'direccion'     => setting('empresa.direccion', ''),
            'telefono'      => setting('empresa.telefono', ''),
            'email'         => setting('empresa.email', ''),
            'leyenda'       => setting('empresa.leyenda', 'Documento interno generado por Insumos.'),
            'logo_path'     => setting('empresa.logo_path', null), // ej: "empresa/xxx.webp"
            'mostrar_logo'  => (bool) setting('empresa.mostrar_logo', true),
        ];

        return view('admin.empresa.edit', compact('empresa'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'nombre'       => ['required', 'string', 'max:120'],
            'rfc'          => ['nullable', 'string', 'max:30'],
            'direccion'    => ['nullable', 'string', 'max:255'],
            'telefono'     => ['nullable', 'string', 'max:40'],
            'email'        => ['nullable', 'email', 'max:120'],
            'leyenda'      => ['nullable', 'string', 'max:255'],

            // checkboxes
            'mostrar_logo' => ['nullable'], // si no viene, es false
            'remove_logo'  => ['nullable'],

            // file
            'logo'         => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ]);

        // Campos simples
        setting_set('empresa.nombre', $data['nombre']);
        setting_set('empresa.rfc', $data['rfc'] ?? '');
        setting_set('empresa.direccion', $data['direccion'] ?? '');
        setting_set('empresa.telefono', $data['telefono'] ?? '');
        setting_set('empresa.email', $data['email'] ?? '');
        setting_set('empresa.leyenda', $data['leyenda'] ?? '');

        // Toggle: si no viene el checkbox, false
        $mostrarLogo = $request->boolean('mostrar_logo');
        setting_set('empresa.mostrar_logo', $mostrarLogo);

        // Quitar logo
        if ($request->boolean('remove_logo')) {
            $old = setting('empresa.logo_path', null);

            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }

            setting_set('empresa.logo_path', null);
            setting_set('empresa.mostrar_logo', false);
        }

        // Subir logo (reemplaza)
        if ($request->hasFile('logo')) {
            $old = setting('empresa.logo_path', null);

            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }

            $path = $request->file('logo')->store('empresa', 'public');
            setting_set('empresa.logo_path', $path);
            setting_set('empresa.mostrar_logo', true);
        }

        return redirect()
            ->route('admin.empresa.edit')
            ->with('success', 'Empresa actualizada.');
    }
}
