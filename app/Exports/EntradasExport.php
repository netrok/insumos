<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EntradasExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private array $filters) {}

    public function collection(): Collection
    {
        $q = DB::table('entradas as e')
            ->leftJoin('almacenes as a', 'a.id', '=', 'e.almacen_id')
            ->leftJoin('proveedores as p', 'p.id', '=', 'e.proveedor_id')
            ->selectRaw("
                e.id,
                e.folio,
                e.fecha::date as fecha,
                COALESCE(a.nombre,'—') as almacen,
                COALESCE(p.nombre,'—') as proveedor,
                COALESCE(UPPER(e.tipo),'—') as tipo,
                COALESCE(e.total,0)::numeric(14,2) as total
            ");

        if (!empty($this->filters['almacen_id']))   $q->where('e.almacen_id', (int)$this->filters['almacen_id']);
        if (!empty($this->filters['proveedor_id'])) $q->where('e.proveedor_id', (int)$this->filters['proveedor_id']);

        $q->whereDate('e.fecha', '>=', $this->filters['desde'])
          ->whereDate('e.fecha', '<=', $this->filters['hasta']);

        if (!empty($this->filters['q'])) {
            $term = mb_strtolower($this->filters['q']);
            $q->whereRaw('LOWER(e.folio) LIKE ?', ["%{$term}%"]);
        }

        return $q->orderByDesc('e.fecha')->orderByDesc('e.id')->get();
    }

    public function headings(): array
    {
        return ['Folio', 'Fecha', 'Almacén', 'Proveedor', 'Tipo', 'Total'];
    }

    public function map($row): array
    {
        return [
            (string)$row->folio,
            (string)$row->fecha,
            (string)$row->almacen,
            (string)$row->proveedor,
            (string)$row->tipo,
            (float)$row->total,
        ];
    }
}
