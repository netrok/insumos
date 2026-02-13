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
            'nombre'    => setting('empresa.nombre', config('app.name')),
            'rfc'       => setting('empresa.rfc', ''),
            'direccion' => setting('empresa.direccion', ''),
            'telefono'  => setting('empresa.telefono', ''),
            'email'     => setting('empresa.email', ''),
            'leyenda'   => setting('empresa.leyenda', 'Documento interno generado por Insumos.'),
            'logo_path' => setting('empresa.logo_path', null),     // storage/public/empresa/...
            'logo_on'   => (bool) setting('empresa.logo_on', true),
        ];

        return view('admin.empresa.edit', compact('empresa'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:120'],
            'rfc'         => ['nullable', 'string', 'max:30'],
            'direccion'   => ['nullable', 'string', 'max:255'],
            'telefono'    => ['nullable', 'string', 'max:40'],
            'email'       => ['nullable', 'email', 'max:120'],
            'leyenda'     => ['nullable', 'string', 'max:255'],

            // checkbox/toggles
            'logo_on'     => ['nullable'],   // checkbox
            'remove_logo' => ['nullable'],   // checkbox

            // file
            'logo'        => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ]);

        // Guardar campos simples
        setting_set('empresa.nombre', $data['nombre']);
        setting_set('empresa.rfc', $data['rfc'] ?? '');
        setting_set('empresa.direccion', $data['direccion'] ?? '');
        setting_set('empresa.telefono', $data['telefono'] ?? '');
        setting_set('empresa.email', $data['email'] ?? '');
        setting_set('empresa.leyenda', $data['leyenda'] ?? '');

        // Toggle de logo (si no viene el checkbox, es false)
        $logoOn = $request->boolean('logo_on');
        setting_set('empresa.logo_on', $logoOn);

        // Quitar logo (borra archivo + limpia setting)
        if ($request->boolean('remove_logo')) {
            $old = setting('empresa.logo_path', null);

            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }

            setting_set('empresa.logo_path', null);
            // si lo quitaste, no tiene sentido dejar "on"
            setting_set('empresa.logo_on', false);
        }

        // Subir logo (reemplaza el anterior)
        if ($request->hasFile('logo')) {
            $old = setting('empresa.logo_path', null);

            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }

            $path = $request->file('logo')->store('empresa', 'public');
            setting_set('empresa.logo_path', $path);
            setting_set('empresa.logo_on', true); // si subes logo, queda activo
        }

        return redirect()
            ->route('admin.empresa.edit')
            ->with('success', 'Empresa actualizada.');
    }
}
