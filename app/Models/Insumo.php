<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    use HasFactory;

    protected $table = 'insumos';

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
        'activo'        => 'boolean',
        'costo_promedio'=> 'decimal:2',
        // Si en BD es integer, cámbialo a 'integer'
        'stock_minimo'  => 'decimal:2',
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

    // Scopes
    public function scopeActivos($q)
    {
        return $q->where('activo', true);
    }

    public function scopeBuscar($q, ?string $term)
    {
        $term = trim((string) $term);
        if ($term === '') return $q;

        return $q->where(function ($qq) use ($term) {
            $qq->where('nombre', 'ILIKE', "%{$term}%")
               ->orWhere('sku', 'ILIKE', "%{$term}%");
        });
    }
}
