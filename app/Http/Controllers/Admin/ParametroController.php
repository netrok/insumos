<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Almacen;
use Illuminate\Http\Request;

class ParametroController extends Controller
{
    public function index()
    {
        $params = [
            // Inventario / Reglas
            'stock_minimo_default'    => (float) setting('param.stock_minimo_default', 0),
            'alerta_bajo_stock'       => (float) setting('param.alerta_bajo_stock', 0),
            'decimales_cantidad'      => (int)   setting('param.decimales_cantidad', 2),

            // Operación
            'salidas_requieren_aprob' => (bool)  setting('param.salidas_requieren_aprob', false),
            'almacen_default_id'      => setting('param.almacen_default_id', null),

            // Kardex / Apertura (opcional)
            'kardex_saldo_inicial'    => (float) setting('param.kardex_saldo_inicial', 0),
        ];

        $almacenes = Almacen::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return view('admin.parametros.index', compact('params', 'almacenes'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'stock_minimo_default' => ['required', 'numeric', 'min:0'],
            'alerta_bajo_stock'    => ['required', 'numeric', 'min:0'],

            // 0–6 decimales (2 es típico)
            'decimales_cantidad'   => ['required', 'integer', 'min:0', 'max:6'],

            // checkboxes (si no vienen, false)
            'salidas_requieren_aprob' => ['nullable'],

            // nullable: puede ser null si no quieres default
            'almacen_default_id'   => ['nullable', 'integer', 'min:1', 'exists:almacenes,id'],

            // apertura configurable
            'kardex_saldo_inicial' => ['required', 'numeric'],
        ]);

        // Inventario
        setting_set('param.stock_minimo_default', (float) $data['stock_minimo_default']);
        setting_set('param.alerta_bajo_stock', (float) $data['alerta_bajo_stock']);
        setting_set('param.decimales_cantidad', (int) $data['decimales_cantidad']);

        // Operación
        setting_set('param.salidas_requieren_aprob', $request->boolean('salidas_requieren_aprob'));
        setting_set('param.almacen_default_id', $data['almacen_default_id'] ?? null);

        // Kardex
        setting_set('param.kardex_saldo_inicial', (float) $data['kardex_saldo_inicial']);

        return back()->with('success', 'Parámetros actualizados.');
    }
}
