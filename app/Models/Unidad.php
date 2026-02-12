<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    protected $fillable = ['nombre', 'clave', 'activa'];

    protected static function booted(): void
    {
        static::setRouteKeyName('id');
    }
}
