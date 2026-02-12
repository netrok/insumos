<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Existencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'insumo_id',
        'almacen_id',
        'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2', // cámbialo a integer si tu columna es integer
    ];

    // Relaciones
    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }

    // Scopes útiles
    public function scopeDeAlmacen($q, int $almacenId)
    {
        return $q->where('almacen_id', $almacenId);
    }

    public function scopeDeInsumo($q, int $insumoId)
    {
        return $q->where('insumo_id', $insumoId);
    }
}
