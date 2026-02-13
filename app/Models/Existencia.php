<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Existencia extends Model
{
    use HasFactory;

    protected $table = 'existencias';

    protected $fillable = [
        'almacen_id',
        'insumo_id',
        'stock',
    ];

    protected $casts = [
        // Postgres numeric(14,3) => Laravel decimal:3 (evita float)
        'stock' => 'decimal:3',
    ];

    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class);
    }

    public function almacen(): BelongsTo
    {
        return $this->belongsTo(Almacen::class);
    }

    // Scopes útiles
    public function scopeDeAlmacen(Builder $q, int $almacenId): Builder
    {
        return $q->where('almacen_id', $almacenId);
    }

    public function scopeDeInsumo(Builder $q, int $insumoId): Builder
    {
        return $q->where('insumo_id', $insumoId);
    }
}
