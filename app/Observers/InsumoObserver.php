<?php

namespace App\Observers;

use App\Models\Insumo;
use App\Models\Existencia;
use App\Models\Almacen;

class InsumoObserver
{
    public function created(Insumo $insumo): void
    {
        $almacenes = Almacen::query()->select('id')->get();

        $rows = $almacenes->map(fn ($a) => [
            'insumo_id'   => $insumo->id,
            'almacen_id'  => $a->id,
            'cantidad'    => 0,
            'created_at'  => now(),
            'updated_at'  => now(),
        ])->all();

        // Insert masivo (rápido)
        if (!empty($rows)) {
            Existencia::insert($rows);
        }
    }
}
