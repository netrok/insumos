<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FolioController extends Controller
{
    public function index()
    {
        $folios = [
            'entradas' => [
                'prefijo'      => setting('folios.entradas.prefijo', 'ENT'),
                'consecutivo'  => (int) setting('folios.entradas.consecutivo', 1),
                'padding'      => (int) setting('folios.entradas.padding', 6),
                'separador'    => setting('folios.entradas.separador', '-'),
            ],
            'salidas' => [
                'prefijo'      => setting('folios.salidas.prefijo', 'SAL'),
                'consecutivo'  => (int) setting('folios.salidas.consecutivo', 1),
                'padding'      => (int) setting('folios.salidas.padding', 6),
                'separador'    => setting('folios.salidas.separador', '-'),
            ],

            // Listos para futuro
            'ajustes' => [
                'prefijo'      => setting('folios.ajustes.prefijo', 'AJU'),
                'consecutivo'  => (int) setting('folios.ajustes.consecutivo', 1),
                'padding'      => (int) setting('folios.ajustes.padding', 6),
                'separador'    => setting('folios.ajustes.separador', '-'),
            ],
            'traspasos' => [
                'prefijo'      => setting('folios.traspasos.prefijo', 'TRS'),
                'consecutivo'  => (int) setting('folios.traspasos.consecutivo', 1),
                'padding'      => (int) setting('folios.traspasos.padding', 6),
                'separador'    => setting('folios.traspasos.separador', '-'),
            ],
        ];

        return view('admin.folios.index', compact('folios'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            // Entradas
            'entradas_prefijo'     => ['required', 'string', 'max:10'],
            'entradas_consecutivo' => ['required', 'integer', 'min:1'],
            'entradas_padding'     => ['required', 'integer', 'min:3', 'max:10'],
            'entradas_separador'   => ['nullable', 'string', 'max:2'],

            // Salidas
            'salidas_prefijo'      => ['required', 'string', 'max:10'],
            'salidas_consecutivo'  => ['required', 'integer', 'min:1'],
            'salidas_padding'      => ['required', 'integer', 'min:3', 'max:10'],
            'salidas_separador'    => ['nullable', 'string', 'max:2'],

            // Ajustes (futuro)
            'ajustes_prefijo'      => ['required', 'string', 'max:10'],
            'ajustes_consecutivo'  => ['required', 'integer', 'min:1'],
            'ajustes_padding'      => ['required', 'integer', 'min:3', 'max:10'],
            'ajustes_separador'    => ['nullable', 'string', 'max:2'],

            // Traspasos (futuro)
            'traspasos_prefijo'     => ['required', 'string', 'max:10'],
            'traspasos_consecutivo' => ['required', 'integer', 'min:1'],
            'traspasos_padding'     => ['required', 'integer', 'min:3', 'max:10'],
            'traspasos_separador'   => ['nullable', 'string', 'max:2'],
        ]);

        // Normalizador: prefijos en MAYÚSCULA, separador default '-'
        $normPref = fn ($v) => strtoupper(trim((string) $v));
        $normSep  = fn ($v) => (trim((string) $v) === '') ? '-' : trim((string) $v);

        // Entradas
        setting_set('folios.entradas.prefijo', $normPref($data['entradas_prefijo']));
        setting_set('folios.entradas.consecutivo', (int) $data['entradas_consecutivo']);
        setting_set('folios.entradas.padding', (int) $data['entradas_padding']);
        setting_set('folios.entradas.separador', $normSep($data['entradas_separador'] ?? '-'));

        // Salidas
        setting_set('folios.salidas.prefijo', $normPref($data['salidas_prefijo']));
        setting_set('folios.salidas.consecutivo', (int) $data['salidas_consecutivo']);
        setting_set('folios.salidas.padding', (int) $data['salidas_padding']);
        setting_set('folios.salidas.separador', $normSep($data['salidas_separador'] ?? '-'));

        // Ajustes
        setting_set('folios.ajustes.prefijo', $normPref($data['ajustes_prefijo']));
        setting_set('folios.ajustes.consecutivo', (int) $data['ajustes_consecutivo']);
        setting_set('folios.ajustes.padding', (int) $data['ajustes_padding']);
        setting_set('folios.ajustes.separador', $normSep($data['ajustes_separador'] ?? '-'));

        // Traspasos
        setting_set('folios.traspasos.prefijo', $normPref($data['traspasos_prefijo']));
        setting_set('folios.traspasos.consecutivo', (int) $data['traspasos_consecutivo']);
        setting_set('folios.traspasos.padding', (int) $data['traspasos_padding']);
        setting_set('folios.traspasos.separador', $normSep($data['traspasos_separador'] ?? '-'));

        return redirect()
            ->route('admin.folios.index')
            ->with('success', 'Folios actualizados.');
    }
}
