<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $hoy = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();

        $insumos = DB::table('insumos')->count();

        $entradasMes = DB::table('entradas')
            ->whereBetween('fecha', [$inicioMes->toDateString(), $finMes->toDateString()])
            ->count();

        $salidasMes = DB::table('salidas')
            ->whereBetween('fecha', [$inicioMes->toDateString(), $finMes->toDateString()])
            ->count();

        /**
         * Detectar nombre real de la columna de existencia en tabla existencias
         * (porque en tu BD NO existe "cantidad").
         */
        $cols = DB::select("
            select column_name
            from information_schema.columns
            where table_schema = 'public'
              and table_name = 'existencias'
        ");
        $colNames = array_map(fn ($r) => $r->column_name, $cols);

        // Lista conservadora de nombres típicos
        $existenciaCol = null;
        foreach (['cantidad', 'existencia', 'stock', 'qty', 'saldo', 'total'] as $c) {
            if (in_array($c, $colNames, true)) {
                $existenciaCol = $c;
                break;
            }
        }

        // Existencia total (sumatoria)
        $existenciaTotal = $existenciaCol
            ? (float) DB::table('existencias')->sum($existenciaCol)
            : 0.0;

        // Movimientos de hoy (conteo de entradas + salidas)
        $movHoy = (int) DB::table('entradas')->whereDate('fecha', $hoy)->count()
            + (int) DB::table('salidas')->whereDate('fecha', $hoy)->count();

        // Últimos movimientos (ENT/SAL) con UNION
        $ultimosMovs = DB::query()
            ->fromSub(function ($q) {
                $q->selectRaw("
                        e.fecha as fecha,
                        'ENT' as tipo,
                        e.folio as ref,
                        d.insumo_id as insumo_id,
                        e.almacen_id as almacen_id,
                        d.cantidad as cantidad
                    ")
                    ->from('entradas as e')
                    ->join('entrada_detalles as d', 'd.entrada_id', '=', 'e.id')

                    ->unionAll(
                        DB::table('salidas as s')
                            ->join('salida_detalles as sd', 'sd.salida_id', '=', 's.id')
                            ->selectRaw("
                                s.fecha as fecha,
                                'SAL' as tipo,
                                s.folio as ref,
                                sd.insumo_id as insumo_id,
                                s.almacen_id as almacen_id,
                                (sd.cantidad * -1) as cantidad
                            ")
                    );
            }, 'm')
            ->leftJoin('insumos as i', 'i.id', '=', 'm.insumo_id')
            ->leftJoin('almacenes as a', 'a.id', '=', 'm.almacen_id')
            ->orderByDesc('m.fecha')
            ->orderByDesc('m.tipo')
            ->limit(12)
            ->get([
                'm.fecha',
                'm.tipo',
                'm.ref',
                'i.nombre as insumo',
                'a.nombre as almacen',
                'm.cantidad',
            ]);

        /**
         * Bajo stock: si no existe columna de existencias detectada, no revienta.
         * También detecta "minimo" en existencias y/o "stock_minimo" en insumos de forma segura.
         */
        $minColsEx = DB::select("
            select column_name
            from information_schema.columns
            where table_schema = 'public'
              and table_name = 'existencias'
              and column_name in ('minimo','stock_minimo','min_stock','min_qty')
        ");
        $exMinCol = $minColsEx ? $minColsEx[0]->column_name : null;

        $minColsIn = DB::select("
            select column_name
            from information_schema.columns
            where table_schema = 'public'
              and table_name = 'insumos'
              and column_name in ('stock_minimo','minimo','min_stock','min_qty')
        ");
        $inMinCol = $minColsIn ? $minColsIn[0]->column_name : null;

        $bajoStock = collect();

        if ($existenciaCol) {
            // Construimos SQL seguro usando las columnas detectadas
            $exMinExpr = $exMinCol ? "ex.\"$exMinCol\"" : "NULL";
            $inMinExpr = $inMinCol ? "i.\"$inMinCol\"" : "NULL";

            $bajoStock = DB::table('existencias as ex')
                ->join('insumos as i', 'i.id', '=', 'ex.insumo_id')
                ->join('almacenes as a', 'a.id', '=', 'ex.almacen_id')
                ->selectRaw("
                    i.nombre as insumo,
                    a.nombre as almacen,
                    ex.\"$existenciaCol\" as cantidad,
                    COALESCE($exMinExpr, $inMinExpr, 0) as minimo
                ")
                ->whereRaw("ex.\"$existenciaCol\" <= COALESCE($exMinExpr, $inMinExpr, 0)")
                ->orderByRaw("ex.\"$existenciaCol\" asc")
                ->limit(10)
                ->get();
        }

        return view('dashboard', compact(
            'insumos',
            'entradasMes',
            'salidasMes',
            'existenciaTotal',
            'movHoy',
            'ultimosMovs',
            'bajoStock'
        ));
    }
}
