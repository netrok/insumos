<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Envio extends Model
{
    protected $fillable = [
        'folio',
        'almacen_origen_id',
        'almacen_destino_id',
        'solicitado_por_user_id',
        'aprobado_por_user_id',
        'surtido_por_user_id',
        'chofer_user_id',
        'estado',
        'aprobado_at',
        'surtido_at',
        'salio_at',
        'recibido_at',
        'cerrado_at',
        'notas',
    ];

    protected $casts = [
        'aprobado_at' => 'datetime',
        'surtido_at' => 'datetime',
        'salio_at' => 'datetime',
        'recibido_at' => 'datetime',
        'cerrado_at' => 'datetime',
    ];

    // Relaciones
    public function origen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_origen_id');
    }

    public function destino()
    {
        return $this->belongsTo(Almacen::class, 'almacen_destino_id');
    }

    public function solicitadoPor()
    {
        return $this->belongsTo(User::class, 'solicitado_por_user_id');
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por_user_id');
    }

    public function surtidoPor()
    {
        return $this->belongsTo(User::class, 'surtido_por_user_id');
    }

    public function chofer()
    {
        return $this->belongsTo(User::class, 'chofer_user_id');
    }

    public function detalles()
    {
        return $this->hasMany(EnvioDetalle::class);
    }

    public function evidencias()
    {
        return $this->hasMany(EnvioEvidencia::class);
    }

    public function tokens()
    {
        return $this->hasMany(EnvioToken::class);
    }
}