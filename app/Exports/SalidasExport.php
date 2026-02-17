<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalidasExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private array $filters) {}

    public function collection(): Collection
    {
        $q = DB::table('salidas as s')
            ->leftJoin('almacenes as a', 'a.id', '=', 's.almacen_id')
            ->selectRaw("
                s.id,
                s.folio,
                s.fecha::date as fecha,
                COALESCE(a.nombre,'—') as almacen,
                COALESCE(UPPER(s.tipo),'—') as tipo,
                COALESCE(s.total,0)::numeric(14,2) as total
            ");

        if (!empty($this->filters['almacen_id'])) $q->where('s.almacen_id', (int)$this->filters['almacen_id']);
        if (!empty($this->filters['tipo']))       $q->where('s.tipo', $this->filters['tipo']);

        $q->whereDate('s.fecha', '>=', $this->filters['desde'])
          ->whereDate('s.fecha', '<=', $this->filters['hasta']);

        if (!empty($this->filters['q'])) {
            $term = mb_strtolower($this->filters['q']);
            $q->whereRaw('LOWER(s.folio) LIKE ?', ["%{$term}%"]);
        }

        return $q->orderByDesc('s.fecha')->orderByDesc('s.id')->get();
    }

    public function headings(): array
    {
        return ['Folio', 'Fecha', 'Almacén', 'Tipo', 'Total'];
    }

    public function map($row): array
    {
        return [
            (string)$row->folio,
            (string)$row->fecha,
            (string)$row->almacen,
            (string)$row->tipo,
            (float)$row->total,
        ];
    }
}
