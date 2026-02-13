<?php

namespace App\Exports;

use App\Models\Proveedor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProveedoresExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private string $q = '')
    {
    }

    public function collection()
    {
        $q = trim($this->q);

        return Proveedor::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('nombre', 'ilike', "%{$q}%")
                       ->orWhere('rfc', 'ilike', "%{$q}%")
                       ->orWhere('email', 'ilike', "%{$q}%")
                       ->orWhere('telefono', 'ilike', "%{$q}%");
                });
            })
            ->orderBy('nombre')
            ->get();
    }

    public function headings(): array
    {
        return ['ID', 'Nombre', 'RFC', 'Teléfono', 'Email', 'Activo', 'Creado'];
    }

    public function map($p): array
    {
        return [
            $p->id,
            $p->nombre,
            $p->rfc ?? '',
            $p->telefono ?? '',
            $p->email ?? '',
            $p->activo ? 'Sí' : 'No',
            optional($p->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
