<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnvioToken extends Model
{
    protected $fillable = [
        'envio_id','tipo','token_hash','max_usos','usos',
        'expires_at','used_at','created_by','last_used_by','revocado',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'revocado' => 'boolean',
    ];

    public function envio() { return $this->belongsTo(Envio::class); }
}