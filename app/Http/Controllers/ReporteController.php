<?php

namespace App\Http\Controllers;

use App\Exports\KardexExport;
use App\Models\Almacen;
use App\Models\Insumo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    public function kardex(Request $request)
    {
        $filters = $this->validateKardexFilters($request);

        // Paginado para web (orden desc "reciente primero")
        $movs = $this->kardexQuery($filters)
            ->orderByDesc('fecha')
            ->orderByDesc('tipo') // ENT arriba de SAL (opcional)
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $almacenes = Almacen::orderBy('nombre')->get(['id', 'nombre']);
        $insumos   = Insumo::orderBy('nombre')->get(['id', 'sku', 'nombre']);

        $totals       = $this->kardexTotals($filters);
        $saldoInicial = $this->kardexSaldoInicial($filters);

        // ✅ saldo acumulado por renglón (solo si hay insumo seleccionado)
        $showSaldo = !empty($filters['insumo_id']);

        if ($showSaldo) {
            $page    = max(1, (int) $movs->currentPage());
            $perPage = (int) $movs->perPage();
            $offset  = max(0, ($page - 1) * $perPage);

            // Para que el saldo sea correcto aunque pagines,
            // sumamos el "cantidad" de renglones ANTERIORES en orden contable (asc).
            $sumBefore = $this->kardexSumBefore($filters, $offset);

            $running = (float) $saldoInicial + (float) $sumBefore;

            foreach ($movs as $m) {
                $running += (float) $m->cantidad; // SAL viene negativo
                $m->saldo = $running;
            }
        }

        return view('reportes.kardex', [
            'movs'         => $movs,
            'almacenes'    => $almacenes,
            'insumos'      => $insumos,
            'filters'      => $filters,
            'totals'       => $totals,
            'saldoInicial' => $saldoInicial,
            'showSaldo'    => $showSaldo,
        ]);
    }

    public function kardexXlsx(Request $request)
    {
        $filters = $this->validateKardexFilters($request);

        $almacenMap = Almacen::orderBy('nombre')
            ->pluck('nombre', 'id')
            ->toArray();

        $name = 'kardex_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new KardexExport($filters, $almacenMap), $name);
    }

    public function kardexPdf(Request $request)
    {
        $filters = $this->validateKardexFilters($request);

        // Catálogos (labels + map)
        $almacenes = Almacen::orderBy('nombre')->get(['id', 'nombre']);
        $insumos   = Insumo::orderBy('nombre')->get(['id', 'sku', 'nombre']);

        $almacenLabel = 'Todos';
        if (!empty($filters['almacen_id'])) {
            $almacenLabel = $almacenes->firstWhere('id', (int) $filters['almacen_id'])->nombre ?? '—';
        }

        $insumoLabel = 'Todos';
        if (!empty($filters['insumo_id'])) {
            $ins = $insumos->firstWhere('id', (int) $filters['insumo_id']);
            $insumoLabel = $ins ? ($ins->sku . ' — ' . $ins->nombre) : '—';
        }

        $tipoLabel = $filters['tipo'] ?: 'Todos';

        $totals       = $this->kardexTotals($filters);
        $saldoInicial = $this->kardexSaldoInicial($filters);

        // PDF = sin paginar, con orden contable (asc)
        $rows = $this->kardexOrder($this->kardexQuery($filters))->get();

        // Enriquecer almacén (sin joins pesados dentro del union)
        $almMap = $almacenes->keyBy('id');
        foreach ($rows as $r) {
            $r->almacen_nombre = $almMap->get((int) $r->almacen_id)->nombre ?? '—';
        }

        // ✅ saldo acumulado por renglón solo si hay insumo
        $showSaldo = !empty($filters['insumo_id']);

        if ($showSaldo) {
            // Insertamos “APERTURA” como primer renglón (visual + auditoría)
            $apertura = (object) [
                'tipo'          => 'INI',
                'id'            => 0,
                'fecha'         => $filters['desde'],
                'folio'         => 'APERTURA',
                'almacen_id'    => $filters['almacen_id'] ?? null,
                'almacen_nombre'=> !empty($filters['almacen_id'])
                    ? ($almMap->get((int) $filters['almacen_id'])->nombre ?? '—')
                    : '—',
                'insumo_id'     => $filters['insumo_id'],
                'sku'           => '',
                'insumo_nombre' => 'Saldo inicial',
                'tercero'       => '—',
                'cantidad'      => 0,
                'costo_unitario'=> 0,
                'subtotal'      => 0,
                'saldo'         => (float) $saldoInicial,
            ];

            $rows = $rows->prepend($apertura);

            $running = (float) $saldoInicial;
            foreach ($rows as $r) {
                if (($r->tipo ?? '') !== 'INI') {
                    $running += (float) $r->cantidad; // SAL ya viene negativo
                }
                $r->saldo = $running;
            }
        }

        $labels = [
            'almacen' => $almacenLabel,
            'insumo'  => $insumoLabel,
            'tipo'    => $tipoLabel,
        ];

        $pdf = Pdf::loadView('reportes.kardex_pdf', [
            'rows'         => $rows,
            'filters'      => $filters,
            'totals'       => $totals,
            'saldoInicial' => $saldoInicial,
            'labels'       => $labels,
            'showSaldo'    => $showSaldo,
        ])->setPaper('a4', 'landscape');

        $name = 'kardex_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($name);
    }

    private function validateKardexFilters(Request $request): array
    {
        $data = $request->validate([
            'almacen_id' => ['nullable', 'integer', 'exists:almacenes,id'],
            'insumo_id'  => ['nullable', 'integer', 'exists:insumos,id'],
            'tipo'       => ['nullable', 'in:ENT,SAL'],
            'desde'      => ['nullable', 'date'],
            'hasta'      => ['nullable', 'date'],
            'q'          => ['nullable', 'string', 'max:100'],
        ]);

        // defaults conservadores: último mes
        $desde = $data['desde'] ?? now()->subDays(30)->toDateString();
        $hasta = $data['hasta'] ?? now()->toDateString();

        $q = isset($data['q']) ? trim((string) $data['q']) : null;
        if ($q === '') $q = null;

        return [
            'almacen_id' => $data['almacen_id'] ?? null,
            'insumo_id'  => $data['insumo_id'] ?? null,
            'tipo'       => $data['tipo'] ?? null,
            'desde'      => $desde,
            'hasta'      => $hasta,
            'q'          => $q,
        ];
    }

    /**
     * Builder con UNION ALL de entradas+salidas.
     * ENT: cantidad/subtotal positivos
     * SAL: cantidad/subtotal negativos (para saldo fácil)
     */
    private function kardexQuery(array $f)
    {
        // ENTRADAS
        $entradas = DB::table('entradas as e')
            ->join('entrada_detalles as d', 'd.entrada_id', '=', 'e.id')
            ->join('insumos as i', 'i.id', '=', 'd.insumo_id')
            ->leftJoin('proveedores as p', 'p.id', '=', 'e.proveedor_id')
            ->selectRaw("
                'ENT' as tipo,
                e.id as id,
                e.fecha::date as fecha,
                e.folio as folio,
                e.almacen_id as almacen_id,
                d.insumo_id as insumo_id,
                i.sku as sku,
                i.nombre as insumo_nombre,
                COALESCE(p.nombre, '—') as tercero,
                d.cantidad::numeric(14,3) as cantidad,
                d.costo_unitario::numeric(14,2) as costo_unitario,
                d.subtotal::numeric(14,2) as subtotal,
                e.created_by as user_id
            ");

        // SALIDAS (NEGATIVO)
        $salidas = DB::table('salidas as s')
            ->join('salida_detalles as d', 'd.salida_id', '=', 's.id')
            ->join('insumos as i', 'i.id', '=', 'd.insumo_id')
            ->selectRaw("
                'SAL' as tipo,
                s.id as id,
                s.fecha::date as fecha,
                s.folio as folio,
                s.almacen_id as almacen_id,
                d.insumo_id as insumo_id,
                i.sku as sku,
                i.nombre as insumo_nombre,
                COALESCE(UPPER(s.tipo), '—') as tercero,
                (d.cantidad::numeric(14,3) * -1) as cantidad,
                d.costo_unitario::numeric(14,2) as costo_unitario,
                (d.subtotal::numeric(14,2) * -1) as subtotal,
                s.created_by as user_id
            ");

        $union = $entradas->unionAll($salidas);

        $base = DB::query()->fromSub($union, 'k');

        if (!empty($f['almacen_id'])) $base->where('almacen_id', (int) $f['almacen_id']);
        if (!empty($f['insumo_id']))  $base->where('insumo_id', (int) $f['insumo_id']);
        if (!empty($f['tipo']))       $base->where('tipo', $f['tipo']);

        // fechas
        $base->whereDate('fecha', '>=', $f['desde'])
             ->whereDate('fecha', '<=', $f['hasta']);

        // búsqueda
        if (!empty($f['q'])) {
            $term = mb_strtolower($f['q']);
            $base->where(function ($w) use ($term) {
                $w->whereRaw('LOWER(sku) LIKE ?', ["%{$term}%"])
                  ->orWhereRaw('LOWER(insumo_nombre) LIKE ?', ["%{$term}%"])
                  ->orWhereRaw('LOWER(folio) LIKE ?', ["%{$term}%"])
                  ->orWhereRaw('LOWER(tercero) LIKE ?', ["%{$term}%"]);
            });
        }

        return $base;
    }

    private function kardexTotals(array $f): array
    {
        $row = $this->kardexQuery($f)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN tipo='ENT' THEN cantidad ELSE 0 END), 0) as entradas_qty,
                COALESCE(SUM(CASE WHEN tipo='SAL' THEN ABS(cantidad) ELSE 0 END), 0) as salidas_qty,
                COALESCE(SUM(CASE WHEN tipo='ENT' THEN subtotal ELSE 0 END), 0) as entradas_monto,
                COALESCE(SUM(CASE WHEN tipo='SAL' THEN ABS(subtotal) ELSE 0 END), 0) as salidas_monto,
                COALESCE(SUM(cantidad), 0) as saldo_qty,
                COALESCE(SUM(subtotal), 0) as saldo_monto
            ")
            ->first();

        return [
            'entradas_qty'   => (float) $row->entradas_qty,
            'salidas_qty'    => (float) $row->salidas_qty,
            'entradas_monto' => (float) $row->entradas_monto,
            'salidas_monto'  => (float) $row->salidas_monto,
            'saldo_qty'      => (float) $row->saldo_qty,
            'saldo_monto'    => (float) $row->saldo_monto,
        ];
    }

    private function kardexSaldoInicial(array $f): float
    {
        // mismo filtro, pero "hasta" = día anterior a "desde"
        $hasta = date('Y-m-d', strtotime($f['desde'] . ' -1 day'));
        if (!$hasta || $hasta < '1900-01-01') return 0.0;

        $f2 = $f;
        $f2['hasta'] = $hasta;

        $row = $this->kardexQuery($f2)
            ->selectRaw("COALESCE(SUM(cantidad), 0) as saldo_inicial")
            ->first();

        return (float) $row->saldo_inicial;
    }

    private function kardexOrder($q)
    {
        // Orden “contable”: fecha asc, ENT antes SAL, y luego id
        return $q->orderBy('fecha')->orderBy('tipo')->orderBy('id');
    }

    /**
     * Suma de cantidades antes de la página actual (para que el saldo paginado sea correcto).
     * IMPORTANTE: usa el mismo orden contable (asc).
     */
    private function kardexSumBefore(array $filters, int $offset): float
    {
        if ($offset <= 0) return 0.0;

        $sub = $this->kardexOrder($this->kardexQuery($filters))
            ->limit($offset);

        $sum = DB::query()
            ->fromSub($sub, 't')
            ->selectRaw("COALESCE(SUM(cantidad),0) as s")
            ->value('s');

        return (float) $sum;
    }
}
