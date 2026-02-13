<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Salida extends Model
{
    use HasFactory;

    protected $table = 'salidas';

    protected $fillable = [
        'consecutivo',
        'folio',
        'fecha',
        'almacen_id',
        'tipo',
        'observaciones',
        'created_by',
        'total',
    ];

    protected $casts = [
        'fecha' => 'date',
        'total' => 'decimal:2',
    ];

    public function almacen(): BelongsTo
    {
        return $this->belongsTo(Almacen::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(SalidaDetalle::class);
    }
}
