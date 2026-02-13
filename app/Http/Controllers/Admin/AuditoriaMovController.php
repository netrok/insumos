<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditoriaMovController extends Controller
{
    public function index(Request $request)
    {
        $q    = trim((string) $request->get('q', ''));
        $tipo = strtoupper(trim((string) $request->get('tipo', ''))); // ENT | SAL | ''
        $from = $request->get('from'); // YYYY-MM-DD
        $to   = $request->get('to');   // YYYY-MM-DD

        // Subquery UNION (ENT + SAL) ya con joins para poder filtrar por texto
        $base = DB::query()->fromSub(function ($sub) {

            $sub->selectRaw("
                e.fecha as fecha,
                'ENT' as tipo,
                COALESCE(e.folio, e.id::text) as folio,
                u.name as usuario,
                i.nombre as insumo,
                a.nombre as almacen,
                d.cantidad as cantidad
            ")
            ->from('entradas as e')
            ->join('entrada_detalles as d', 'd.entrada_id', '=', 'e.id')
            ->leftJoin('users as u', 'u.id', '=', 'e.user_id')
            ->leftJoin('insumos as i', 'i.id', '=', 'd.insumo_id')
            ->leftJoin('almacenes as a', 'a.id', '=', 'e.almacen_id')

            ->unionAll(
                DB::table('salidas as s')
                    ->join('salida_detalles as sd', 'sd.salida_id', '=', 's.id')
                    ->leftJoin('users as u2', 'u2.id', '=', 's.user_id')
                    ->leftJoin('insumos as i2', 'i2.id', '=', 'sd.insumo_id')
                    ->leftJoin('almacenes as a2', 'a2.id', '=', 's.almacen_id')
                    ->selectRaw("
                        s.fecha as fecha,
                        'SAL' as tipo,
                        COALESCE(s.folio, s.id::text) as folio,
                        u2.name as usuario,
                        i2.nombre as insumo,
                        a2.nombre as almacen,
                        (sd.cantidad * -1) as cantidad
                    ")
            );

        }, 'm');

        // Filtro tipo
        if (in_array($tipo, ['ENT', 'SAL'], true)) {
            $base->where('m.tipo', $tipo);
        } else {
            $tipo = ''; // normaliza para la vista
        }

        // Fechas
        if (!empty($from)) {
            $base->whereDate('m.fecha', '>=', $from);
        }
        if (!empty($to)) {
            $base->whereDate('m.fecha', '<=', $to);
        }

        // Búsqueda (PostgreSQL ILIKE) + escape de % _
        if ($q !== '') {
            $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $q) . '%';

            $base->where(function ($w) use ($like) {
                $w->where('m.folio', 'ilike', $like)
                  ->orWhere('m.usuario', 'ilike', $like)
                  ->orWhere('m.insumo', 'ilike', $like)
                  ->orWhere('m.almacen', 'ilike', $like)
                  ->orWhere('m.tipo', 'ilike', $like);
            });
        }

        // Orden + paginado
        $items = $base
            ->orderByDesc('m.fecha')
            ->orderByDesc('m.tipo')
            ->orderByDesc('m.folio')
            ->paginate(25)
            ->withQueryString();

        return view('admin.auditoria.movimientos', compact('items', 'q', 'tipo', 'from', 'to'));
    }
}
