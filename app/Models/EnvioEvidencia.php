<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnvioEvidencia extends Model
{
    protected $fillable = ['envio_id', 'user_id', 'tipo', 'archivo', 'nota', 'estado_envio'];

    public function envio() { return $this->belongsTo(Envio::class); }
    public function user() { return $this->belongsTo(User::class); }
}