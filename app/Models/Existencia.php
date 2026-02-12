<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'stock' => 'decimal:3', // si tu columna es integer, cambia a 'integer'
    ];

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
