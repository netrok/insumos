<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class KardexExport implements FromQuery, WithHeadings, WithMapping, WithColumnFormatting, ShouldAutoSize
{
    public function __construct(
        private array $filters,
        private array $almacenMap = [] // [id => nombre]
    ) {}

    public function query()
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
                d.subtotal::numeric(14,2) as subtotal
            ");

        // SALIDAS (cantidad negativa para saldo)
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
                UPPER(s.tipo) as tercero,
                (d.cantidad::numeric(14,3) * -1) as cantidad,
                d.costo_unitario::numeric(14,2) as costo_unitario,
                (d.subtotal::numeric(14,2) * -1) as subtotal
            ");

        $union = $entradas->unionAll($salidas);
        $q = DB::query()->fromSub($union, 'k');

        $f = $this->filters;

        if (!empty($f['almacen_id'])) $q->where('almacen_id', (int) $f['almacen_id']);
        if (!empty($f['insumo_id']))  $q->where('insumo_id', (int) $f['insumo_id']);
        if (!empty($f['tipo']))       $q->where('tipo', $f['tipo']);

        $q->whereDate('fecha', '>=', $f['desde'])
          ->whereDate('fecha', '<=', $f['hasta']);

        if (!empty($f['q'])) {
            $term = mb_strtolower(trim($f['q']));
            $q->where(function ($w) use ($term) {
                $w->whereRaw('LOWER(sku) LIKE ?', ["%{$term}%"])
                  ->orWhereRaw('LOWER(insumo_nombre) LIKE ?', ["%{$term}%"])
                  ->orWhereRaw('LOWER(folio) LIKE ?', ["%{$term}%"])
                  ->orWhereRaw('LOWER(tercero) LIKE ?', ["%{$term}%"]);
            });
        }

        // Orden estable para export
        return $q->orderBy('fecha')->orderBy('tipo')->orderBy('folio')->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Tipo',
            'Folio',
            'Almacén',
            'SKU',
            'Insumo',
            'Tercero',
            'Cantidad',
            'Costo unit.',
            'Subtotal',
            'Saldo',
        ];
    }

    public function map($row): array
    {
        static $saldo = null;

        if ($saldo === null) {
            // saldo arranca en 0 para export (si quieres saldoInicial real, lo pasamos también)
            $saldo = 0.0;
        }

        $qty = (float) $row->cantidad;
        $sub = (float) $row->subtotal;

        $saldo += $qty;

        return [
            (string) $row->fecha,
            (string) $row->tipo,
            (string) $row->folio,
            $this->almacenMap[(int)$row->almacen_id] ?? '—',
            (string) $row->sku,
            (string) $row->insumo_nombre,
            (string) ($row->tercero ?? '—'),
            $qty,          // signed
            (float) $row->costo_unitario,
            $sub,          // signed
            $saldo,        // signed saldo
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_YYYYMMDD2,
            'H' => NumberFormat::FORMAT_NUMBER_00,  // qty (si quieres 3 decimales: '0.000')
            'I' => NumberFormat::FORMAT_NUMBER_00,
            'J' => NumberFormat::FORMAT_NUMBER_00,
            'K' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }
}
