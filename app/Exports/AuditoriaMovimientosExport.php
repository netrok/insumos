<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class AuditoriaMovimientosExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStrictNullComparison
{
    public function __construct(private array $filters)
    {
    }

    public function headings(): array
    {
        return ['Fecha', 'Tipo', 'Folio', 'Usuario', 'Insumo', 'Almacén', 'Cantidad'];
    }

    public function collection(): Collection
    {
        $f = $this->filters;

        $q    = trim((string) ($f['q'] ?? ''));
        $tipo = strtoupper(trim((string) ($f['tipo'] ?? ''))); // ENT|SAL|''
        $from = (string) ($f['from'] ?? now()->subDays(7)->toDateString());
        $to   = (string) ($f['to'] ?? now()->toDateString());

        $rows = DB::query()->fromSub(function ($sub) {

            // ENTRADAS
            $sub->from('entradas as e')
                ->join('entrada_detalles as d', 'd.entrada_id', '=', 'e.id')
                ->leftJoin('users as u', 'u.id', '=', 'e.created_by')
                ->leftJoin('insumos as i', 'i.id', '=', 'd.insumo_id')
                ->leftJoin('almacenes as a', 'a.id', '=', 'e.almacen_id')
                ->selectRaw("
                    e.fecha::date as fecha,
                    'ENT' as tipo,
                    COALESCE(e.folio, e.id::text) as folio,
                    COALESCE(u.name, '—') as usuario,
                    COALESCE(i.nombre, '—') as insumo,
                    COALESCE(a.nombre, '—') as almacen,
                    d.cantidad::numeric(14,2) as cantidad
                ")
                ->unionAll(
                    // SALIDAS (cantidad negativa)
                    DB::table('salidas as s')
                        ->join('salida_detalles as sd', 'sd.salida_id', '=', 's.id')
                        ->leftJoin('users as u2', 'u2.id', '=', 's.created_by')
                        ->leftJoin('insumos as i2', 'i2.id', '=', 'sd.insumo_id')
                        ->leftJoin('almacenes as a2', 'a2.id', '=', 's.almacen_id')
                        ->selectRaw("
                            s.fecha::date as fecha,
                            'SAL' as tipo,
                            COALESCE(s.folio, s.id::text) as folio,
                            COALESCE(u2.name, '—') as usuario,
                            COALESCE(i2.nombre, '—') as insumo,
                            COALESCE(a2.nombre, '—') as almacen,
                            (sd.cantidad * -1)::numeric(14,2) as cantidad
                        ")
                );

        }, 'm');

        // Filtros (idénticos al controller de auditoría)
        if (in_array($tipo, ['ENT', 'SAL'], true)) {
            $rows->where('m.tipo', $tipo);
        }

        if (!empty($from)) $rows->whereDate('m.fecha', '>=', $from);
        if (!empty($to))   $rows->whereDate('m.fecha', '<=', $to);

        if ($q !== '') {
            $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $q) . '%';

            $rows->where(function ($w) use ($like) {
                $w->where('m.folio', 'ilike', $like)
                  ->orWhere('m.usuario', 'ilike', $like)
                  ->orWhere('m.insumo', 'ilike', $like)
                  ->orWhere('m.almacen', 'ilike', $like)
                  ->orWhere('m.tipo', 'ilike', $like);
            });
        }

        $rows = $rows
            ->orderByDesc('m.fecha')
            ->orderByDesc('m.folio')
            ->get();

        // Excel feliz: Collection de arrays
        return $rows->map(static fn ($r) => [
            (string) $r->fecha,
            (string) $r->tipo,
            (string) $r->folio,
            (string) $r->usuario,
            (string) $r->insumo,
            (string) $r->almacen,
            (float)  $r->cantidad, // si lo quieres SIEMPRE positivo en XLSX: abs((float)$r->cantidad)
        ]);
    }
}
