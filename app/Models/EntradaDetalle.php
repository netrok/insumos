<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EntradaDetalle extends Model
{
    use HasFactory;

    protected $table = 'entrada_detalles';

    protected $fillable = [
        'entrada_id',
        'insumo_id',
        'cantidad',
        'costo_unitario',
        'subtotal',
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'costo_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function entrada()
    {
        return $this->belongsTo(Entrada::class);
    }

    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }
}
