<?php

namespace App\Observers;

use App\Models\Insumo;
use App\Models\Existencia;
use App\Models\Almacen;

class InsumoObserver
{
    public function created(Insumo $insumo): void
    {
        $now = now();

        $almacenIds = Almacen::query()->pluck('id');

        $rows = $almacenIds->map(fn ($almacenId) => [
            'insumo_id'   => $insumo->id,
            'almacen_id'  => $almacenId,
            'stock'       => 0,
            'created_at'  => $now,
            'updated_at'  => $now,
        ])->all();

        if (!empty($rows)) {
            // Si tienes unique(['almacen_id','insumo_id']) en existencias, esto evita duplicados sin romper.
            Existencia::query()->insertOrIgnore($rows);

            // Si NO tienes unique, usa insert normal:
            // Existencia::query()->insert($rows);
        }
    }
}
