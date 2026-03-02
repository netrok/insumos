<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnvioDetalle extends Model
{
    protected $fillable = ['envio_id', 'insumo_id', 'cantidad', 'unidad'];

    public function envio() { return $this->belongsTo(Envio::class); }
    public function insumo() { return $this->belongsTo(Insumo::class); }
}