<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'nombre',
        'descripcion',
        'categoria_id',
        'unidad_id',
        'costo_promedio',
        'stock_minimo',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'costo_promedio' => 'decimal:2',
        'stock_minimo' => 'decimal:2', // cámbialo a int si tu columna es integer
    ];

    // Relaciones
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    public function existencias()
    {
        return $this->hasMany(Existencia::class);
    }

    // Scopes útiles
    public function scopeActivos($q)
    {
        return $q->where('activo', true);
    }

    public function scopeBuscar($q, ?string $term)
    {
        $term = trim((string) $term);
        if ($term === '') return $q;

        return $q->where(function ($qq) use ($term) {
            $qq->where('nombre', 'ilike', "%{$term}%")
               ->orWhere('sku', 'ilike', "%{$term}%");
        });
    }
}
