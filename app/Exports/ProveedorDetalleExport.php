<?php

namespace App\Exports;

use App\Models\Proveedor;
use Maatwebsite\Excel\Concerns\FromArray;

class ProveedorDetalleExport implements FromArray
{
    public function __construct(private Proveedor $p)
    {
    }

    public function array(): array
    {
        $p = $this->p;

        return [
            ['Campo', 'Valor'],
            ['ID', $p->id],
            ['Nombre', $p->nombre],
            ['RFC', $p->rfc ?? ''],
            ['Teléfono', $p->telefono ?? ''],
            ['Email', $p->email ?? ''],
            ['Activo', $p->activo ? 'Sí' : 'No'],
            ['Creado', optional($p->created_at)->format('Y-m-d H:i:s')],
            ['Actualizado', optional($p->updated_at)->format('Y-m-d H:i:s')],
        ];
    }
}
