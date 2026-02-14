<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entrada extends Model
{
    use HasFactory;

    protected $table = 'entradas';

    protected $fillable = [
        'consecutivo',
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
        'consecutivo' => 'integer',
        'fecha' => 'date',
        'total' => 'decimal:2',
    ];

    // Si quieres que SIEMPRE venga el usuario creador:
    protected $with = ['createdBy'];

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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
        // o si prefieres full-qualify:
        // return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
