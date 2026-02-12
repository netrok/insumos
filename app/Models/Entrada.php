<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Entrada extends Model
{
    use HasFactory;

    protected $table = 'entradas';

    protected $fillable = [
        'folio',
        'fecha',
        'almacen_id',
        'proveedor_id',
        'tipo',
        'observaciones',
        'total',
        'created_by',
    ];

    protected $casts = [
        'fecha' => 'date',
        'total' => 'decimal:2',
    ];

    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function detalles()
    {
        return $this->hasMany(EntradaDetalle::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
