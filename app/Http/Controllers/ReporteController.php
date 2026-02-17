<?php

namespace App\Http\Controllers;

use App\Exports\EntradasExport;
use App\Exports\KardexExport;
use App\Exports\SalidasExport;
use App\Models\Almacen;
use App\Models\Insumo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    /* =========================
       KÁRDEX
       ========================= */

    public function kardex(Request $request)
    {
        $filters = $this->validateKardexFilters($request);

        $movs = $this->kardexQuery($filters)
            ->orderByDesc('fecha')
            ->orderByDesc('tipo')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $almacenes = Almacen::orderBy('nombre')->get(['id', 'nombre']);
        $insumos   = Insumo::orderBy('nombre')->get(['id', 'sku', 'nombre']);

        $totals       = $this->kardexTotals($filters);
        $saldoInicial = $this->kardexSaldoInicial($filters);

        $showSaldo = !empty($filters['insumo_id']);

        if ($showSaldo) {
            $page    = max(1, (int) $movs->currentPage());
            $perPage = (int) $movs->perPage();
            $offset  = max(0, ($page - 1) * $perPage);

            $sumBefore = $this->kardexSumBefore($filters, $offset);
            $running   = (float) $saldoInicial + (float) $sumBefore;

            foreach ($movs as $m) {
                $running += (float) $m->cantidad;
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

        $almacenMap = Almacen::orderBy('nombre')->pluck('nombre', 'id')->toArray();

        return Excel::download(
            new KardexExport($filters, $almacenMap),
            'kardex_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function kardexPdf(Request $request)
    {
        $filters = $this->validateKardexFilters($request);

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

        $rows = $this->kardexOrder($this->kardexQuery($filters))->get();

        $almMap = $almacenes->keyBy('id');
        foreach ($rows as $r) {
            $r->almacen_nombre = $almMap->get((int) $r->almacen_id)->nombre ?? '—';
        }

        $showSaldo = !empty($filters['insumo_id']);

        if ($showSaldo) {
            $apertura = (object) [
                'tipo'           => 'INI',
                'id'             => 0,
                'fecha'          => $filters['desde'],
                'folio'          => 'APERTURA',
                'almacen_id'     => $filters['almacen_id'] ?? null,
                'almacen_nombre' => !empty($filters['almacen_id'])
                    ? ($almMap->get((int) $filters['almacen_id'])->nombre ?? '—')
                    : '—',
                'insumo_id'      => $filters['insumo_id'],
                'sku'            => '',
                'insumo_nombre'  => 'Saldo inicial',
                'tercero'        => '—',
                'cantidad'       => 0,
                'costo_unitario' => 0,
                'subtotal'       => 0,
                'saldo'          => (float) $saldoInicial,
            ];

            $rows = $rows->prepend($apertura);

            $running = (float) $saldoInicial;
            foreach ($rows as $r) {
                if (($r->tipo ?? '') !== 'INI') {
                    $running += (float) $r->cantidad;
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

        return $pdf->download('kardex_' . now()->format('Ymd_His') . '.pdf');
    }

    /* =========================
       ENTRADAS (PDF/XLSX)
       ========================= */

    public function entradasPdf(Request $request)
    {
        $filters = $this->validateEntradasFilters($request);

        $rows = $this->entradasQuery($filters)->get();
        $totalMonto = $this->entradasTotal($filters);

        $labels = [
            'almacen'   => $this->almacenLabel($filters['almacen_id']),
            'proveedor' => $this->proveedorLabel($filters['proveedor_id']),
        ];

        $pdf = Pdf::loadView('reportes.entradas_pdf', [
            'rows'       => $rows,
            'filters'    => $filters,
            'labels'     => $labels,
            'totalMonto' => $totalMonto,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('entradas_' . now()->format('Ymd_His') . '.pdf');
    }

    public function entradasXlsx(Request $request)
    {
        $filters = $this->validateEntradasFilters($request);

        return Excel::download(
            new EntradasExport($filters),
            'entradas_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    /* =========================
       SALIDAS (PDF/XLSX)
       ========================= */

    public function salidasPdf(Request $request)
    {
        $filters = $this->validateSalidasFilters($request);

        $rows = $this->salidasQuery($filters)->get();
        $totalMonto = $this->salidasTotal($filters);

        $labels = [
            'almacen' => $this->almacenLabel($filters['almacen_id']),
            'tipo'    => $filters['tipo'] ?: 'Todos',
        ];

        $pdf = Pdf::loadView('reportes.salidas_pdf', [
            'rows'       => $rows,
            'filters'    => $filters,
            'labels'     => $labels,
            'totalMonto' => $totalMonto,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('salidas_' . now()->format('Ymd_His') . '.pdf');
    }

    public function salidasXlsx(Request $request)
    {
        $filters = $this->validateSalidasFilters($request);

        return Excel::download(
            new SalidasExport($filters),
            'salidas_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    /* =========================
       VALIDACIONES
       ========================= */

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

    private function validateEntradasFilters(Request $request): array
    {
        $data = $request->validate([
            'almacen_id'   => ['nullable', 'integer', 'exists:almacenes,id'],
            'proveedor_id' => ['nullable', 'integer', 'exists:proveedores,id'],
            'desde'        => ['nullable', 'date'],
            'hasta'        => ['nullable', 'date'],
            'q'            => ['nullable', 'string', 'max:100'],
        ]);

        $desde = $data['desde'] ?? now()->subDays(30)->toDateString();
        $hasta = $data['hasta'] ?? now()->toDateString();

        $q = isset($data['q']) ? trim((string) $data['q']) : null;
        if ($q === '') $q = null;

        return [
            'almacen_id'   => $data['almacen_id'] ?? null,
            'proveedor_id' => $data['proveedor_id'] ?? null,
            'desde'        => $desde,
            'hasta'        => $hasta,
            'q'            => $q,
        ];
    }

    private function validateSalidasFilters(Request $request): array
    {
        $data = $request->validate([
            'almacen_id' => ['nullable', 'integer', 'exists:almacenes,id'],
            'tipo'       => ['nullable', 'in:consumo,merma,ajuste,traspaso'],
            'desde'      => ['nullable', 'date'],
            'hasta'      => ['nullable', 'date'],
            'q'          => ['nullable', 'string', 'max:100'],
        ]);

        $desde = $data['desde'] ?? now()->subDays(30)->toDateString();
        $hasta = $data['hasta'] ?? now()->toDateString();

        $q = isset($data['q']) ? trim((string) $data['q']) : null;
        if ($q === '') $q = null;

        return [
            'almacen_id' => $data['almacen_id'] ?? null,
            'tipo'       => $data['tipo'] ?? null,
            'desde'      => $desde,
            'hasta'      => $hasta,
            'q'          => $q,
        ];
    }

    /* =========================
       QUERIES LISTADO
       ========================= */

    private function entradasQuery(array $f)
    {
        $q = DB::table('entradas as e')
            ->leftJoin('almacenes as a', 'a.id', '=', 'e.almacen_id')
            ->leftJoin('proveedores as p', 'p.id', '=', 'e.proveedor_id')
            ->selectRaw("
                e.id, e.folio, e.fecha::date as fecha,
                COALESCE(a.nombre,'—') as almacen,
                COALESCE(p.nombre,'—') as proveedor,
                COALESCE(e.tipo,'—') as tipo,
                COALESCE(e.total,0)::numeric(14,2) as total
            ");

        if (!empty($f['almacen_id']))   $q->where('e.almacen_id', (int) $f['almacen_id']);
        if (!empty($f['proveedor_id'])) $q->where('e.proveedor_id', (int) $f['proveedor_id']);

        $q->whereDate('e.fecha', '>=', $f['desde'])
          ->whereDate('e.fecha', '<=', $f['hasta']);

        if (!empty($f['q'])) {
            $term = mb_strtolower($f['q']);
            $q->whereRaw('LOWER(e.folio) LIKE ?', ["%{$term}%"]);
        }

        return $q->orderByDesc('e.fecha')->orderByDesc('e.id');
    }

    private function salidasQuery(array $f)
    {
        $q = DB::table('salidas as s')
            ->leftJoin('almacenes as a', 'a.id', '=', 's.almacen_id')
            ->selectRaw("
                s.id, s.folio, s.fecha::date as fecha,
                COALESCE(a.nombre,'—') as almacen,
                COALESCE(s.tipo,'—') as tipo,
                COALESCE(s.total,0)::numeric(14,2) as total
            ");

        if (!empty($f['almacen_id'])) $q->where('s.almacen_id', (int) $f['almacen_id']);
        if (!empty($f['tipo']))      $q->where('s.tipo', $f['tipo']);

        $q->whereDate('s.fecha', '>=', $f['desde'])
          ->whereDate('s.fecha', '<=', $f['hasta']);

        if (!empty($f['q'])) {
            $term = mb_strtolower($f['q']);
            $q->whereRaw('LOWER(s.folio) LIKE ?', ["%{$term}%"]);
        }

        return $q->orderByDesc('s.fecha')->orderByDesc('s.id');
    }

    /* =========================
       TOTALES (sin GROUP BY)
       ========================= */

    private function entradasTotal(array $f): float
    {
        $q = DB::table('entradas as e');

        if (!empty($f['almacen_id']))   $q->where('e.almacen_id', (int) $f['almacen_id']);
        if (!empty($f['proveedor_id'])) $q->where('e.proveedor_id', (int) $f['proveedor_id']);

        $q->whereDate('e.fecha', '>=', $f['desde'])
          ->whereDate('e.fecha', '<=', $f['hasta']);

        if (!empty($f['q'])) {
            $term = mb_strtolower($f['q']);
            $q->whereRaw('LOWER(e.folio) LIKE ?', ["%{$term}%"]);
        }

        return (float) $q->selectRaw("COALESCE(SUM(e.total),0) as total")->value('total');
    }

    private function salidasTotal(array $f): float
    {
        $q = DB::table('salidas as s');

        if (!empty($f['almacen_id'])) $q->where('s.almacen_id', (int) $f['almacen_id']);
        if (!empty($f['tipo']))      $q->where('s.tipo', $f['tipo']);

        $q->whereDate('s.fecha', '>=', $f['desde'])
          ->whereDate('s.fecha', '<=', $f['hasta']);

        if (!empty($f['q'])) {
            $term = mb_strtolower($f['q']);
            $q->whereRaw('LOWER(s.folio) LIKE ?', ["%{$term}%"]);
        }

        return (float) $q->selectRaw("COALESCE(SUM(s.total),0) as total")->value('total');
    }

    /* =========================
       LABELS
       ========================= */

    private function almacenLabel($almacenId): string
    {
        if (empty($almacenId)) return 'Todos';
        return Almacen::query()->whereKey((int) $almacenId)->value('nombre') ?? '—';
    }

    private function proveedorLabel($proveedorId): string
    {
        if (empty($proveedorId)) return 'Todos';
        return DB::table('proveedores')->where('id', (int) $proveedorId)->value('nombre') ?? '—';
    }

    /* =========================
       HELPERS KÁRDEX
       ========================= */

    private function kardexQuery(array $f)
    {
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
        $base  = DB::query()->fromSub($union, 'k');

        if (!empty($f['almacen_id'])) $base->where('almacen_id', (int) $f['almacen_id']);
        if (!empty($f['insumo_id']))  $base->where('insumo_id', (int) $f['insumo_id']);
        if (!empty($f['tipo']))       $base->where('tipo', $f['tipo']);

        $base->whereDate('fecha', '>=', $f['desde'])
             ->whereDate('fecha', '<=', $f['hasta']);

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
        $row = $this->kardexQuery($f)->selectRaw("
            COALESCE(SUM(CASE WHEN tipo='ENT' THEN cantidad ELSE 0 END), 0) as entradas_qty,
            COALESCE(SUM(CASE WHEN tipo='SAL' THEN ABS(cantidad) ELSE 0 END), 0) as salidas_qty,
            COALESCE(SUM(CASE WHEN tipo='ENT' THEN subtotal ELSE 0 END), 0) as entradas_monto,
            COALESCE(SUM(CASE WHEN tipo='SAL' THEN ABS(subtotal) ELSE 0 END), 0) as salidas_monto,
            COALESCE(SUM(cantidad), 0) as saldo_qty,
            COALESCE(SUM(subtotal), 0) as saldo_monto
        ")->first();

        return [
            'entradas_qty'   => (float) ($row->entradas_qty ?? 0),
            'salidas_qty'    => (float) ($row->salidas_qty ?? 0),
            'entradas_monto' => (float) ($row->entradas_monto ?? 0),
            'salidas_monto'  => (float) ($row->salidas_monto ?? 0),
            'saldo_qty'      => (float) ($row->saldo_qty ?? 0),
            'saldo_monto'    => (float) ($row->saldo_monto ?? 0),
        ];
    }

    private function kardexSaldoInicial(array $f): float
    {
        $hasta = date('Y-m-d', strtotime($f['desde'] . ' -1 day'));
        if (!$hasta || $hasta < '1900-01-01') return 0.0;

        $f2 = $f;
        $f2['hasta'] = $hasta;

        return (float) $this->kardexQuery($f2)
            ->selectRaw("COALESCE(SUM(cantidad), 0) as saldo_inicial")
            ->value('saldo_inicial');
    }

    private function kardexOrder($q)
    {
        return $q->orderBy('fecha')->orderBy('tipo')->orderBy('id');
    }

    private function kardexSumBefore(array $filters, int $offset): float
    {
        if ($offset <= 0) return 0.0;

        $sub = $this->kardexOrder($this->kardexQuery($filters))->limit($offset);

        return (float) DB::query()
            ->fromSub($sub, 't')
            ->selectRaw("COALESCE(SUM(cantidad),0) as s")
            ->value('s');
    }
}
