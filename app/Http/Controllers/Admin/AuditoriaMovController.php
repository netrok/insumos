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
        $from = (string) $request->get('from', now()->subDays(7)->toDateString());
        $to   = (string) $request->get('to', now()->toDateString());

        // Subquery UNION (ENT + SAL)
        $base = DB::query()->fromSub(function ($sub) {

            // ENTRADAS
            $sub->from('entradas as e')
                ->join('entrada_detalles as d', 'd.entrada_id', '=', 'e.id')
                ->leftJoin('users as u', 'u.id', '=', 'e.created_by')
                ->leftJoin('insumos as i', 'i.id', '=', 'd.insumo_id')
                ->leftJoin('almacenes as a', 'a.id', '=', 'e.almacen_id')
                ->selectRaw("
                    e.id as doc_id,
                    'entradas' as doc_tipo,
                    e.fecha as fecha,
                    e.created_at as created_at,
                    'ENT' as tipo,
                    COALESCE(e.folio, e.id::text) as folio,
                    COALESCE(u.name, '—') as usuario,
                    i.nombre as insumo,
                    a.nombre as almacen,
                    d.cantidad as cantidad
                ")
                ->unionAll(
                    // SALIDAS (cantidad negativa)
                    DB::table('salidas as s')
                        ->join('salida_detalles as sd', 'sd.salida_id', '=', 's.id')
                        ->leftJoin('users as u2', 'u2.id', '=', 's.created_by')
                        ->leftJoin('insumos as i2', 'i2.id', '=', 'sd.insumo_id')
                        ->leftJoin('almacenes as a2', 'a2.id', '=', 's.almacen_id')
                        ->selectRaw("
                            s.id as doc_id,
                            'salidas' as doc_tipo,
                            s.fecha as fecha,
                            s.created_at as created_at,
                            'SAL' as tipo,
                            COALESCE(s.folio, s.id::text) as folio,
                            COALESCE(u2.name, '—') as usuario,
                            i2.nombre as insumo,
                            a2.nombre as almacen,
                            (sd.cantidad * -1) as cantidad
                        ")
                );

        }, 'm');

        // Tipo (normaliza)
        if (!in_array($tipo, ['ENT', 'SAL'], true)) {
            $tipo = '';
        } else {
            $base->where('m.tipo', $tipo);
        }

        // Fechas (siempre acotamos)
        if ($from !== '') {
            $base->whereDate('m.fecha', '>=', $from);
        }
        if ($to !== '') {
            $base->whereDate('m.fecha', '<=', $to);
        }

        // Búsqueda (ILIKE) + escape % _
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

        // KPIs (clonar antes de ordenar/paginar)
        $kpiQ = clone $base;

        $kpiRow = $kpiQ->selectRaw("
                COUNT(*)::int as acciones,
                COALESCE(SUM(CASE WHEN m.tipo='ENT' THEN m.cantidad ELSE 0 END), 0)::numeric as entradas,
                COALESCE(SUM(CASE WHEN m.tipo='SAL' THEN m.cantidad ELSE 0 END), 0)::numeric as salidas,
                COALESCE(SUM(m.cantidad), 0)::numeric as neto
            ")->first();

        // Asegura array estable para la vista (aunque first() sea null)
        $kpis = [
            'acciones' => (int) data_get($kpiRow, 'acciones', 0),
            'entradas' => (float) data_get($kpiRow, 'entradas', 0),
            'salidas'  => (float) data_get($kpiRow, 'salidas', 0),
            'neto'     => (float) data_get($kpiRow, 'neto', 0),
        ];

        // ✅ Alias claro para la vista (Cantidad Acciones)
        $totalAcciones = $kpis['acciones'];

        // Orden + paginado
        $items = $base
            ->orderByDesc('m.fecha')
            ->orderByDesc('m.created_at')
            ->orderByDesc('m.folio')
            ->paginate(25)
            ->withQueryString();

        return view('admin.auditoria.movimientos', compact(
            'items',
            'q',
            'tipo',
            'from',
            'to',
            'kpis',
            'totalAcciones'
        ));
    }
}
