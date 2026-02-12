<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    protected $table = 'unidades';

    protected $fillable = ['nombre', 'clave', 'activa'];

    protected $casts = [
        'activa' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'id';
    }
}
